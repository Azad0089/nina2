<?php
require_once __DIR__ . '/../includes/config.php';
$me = require_login();
require __DIR__ . '/_nav.php';

$pdo = db();
$postId = (int)($_GET['id'] ?? 0);
$editing = $postId > 0;

// blogs this user can post to
$blogsStmt = $pdo->prepare('SELECT id, slug, title, is_main FROM blogs WHERE user_id = ? AND status = "active" ORDER BY is_main DESC, title');
$blogsStmt->execute([$me['id']]);
$myBlogs = $blogsStmt->fetchAll();
if (!$myBlogs) { exit('You have no active blog to post to.'); }

$post = ['type' => 'text', 'blog_id' => $myBlogs[0]['id'], 'heading' => '', 'body' => '', 'caption' => '',
         'quote_text' => '', 'quote_source' => '', 'link_title' => '', 'link_url' => '', 'chat_lines' => [['', '']],
         'tags' => '', 'status' => 'published', 'scheduled_at' => '', 'media_path' => null];

if ($editing) {
    $stmt = $pdo->prepare('SELECT p.* FROM posts p JOIN blogs b ON b.id = p.blog_id WHERE p.id = ? AND b.user_id = ?');
    $stmt->execute([$postId, $me['id']]);
    $row = $stmt->fetch();
    if (!$row) { exit('Post not found.'); }
    $post = array_merge($post, $row);
    $post['tags'] = implode(', ', post_tags_for($postId));
    $post['chat_lines'] = $row['chat_json'] ? (json_decode($row['chat_json'], true) ?: [['', '']]) : [['', '']];
    $post['scheduled_at'] = $row['scheduled_at'] ? date('Y-m-d\TH:i', strtotime($row['scheduled_at'])) : '';
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) { exit('Invalid request.'); }

    $type   = in_array($_POST['type'] ?? '', ['text','photo','video','link','quote','chat'], true) ? $_POST['type'] : 'text';
    $blogId = (int)($_POST['blog_id'] ?? 0);
    $blogOk = false;
    foreach ($myBlogs as $b) { if ((int)$b['id'] === $blogId) { $blogOk = true; break; } }
    if (!$blogOk) { $errors[] = 'Choose one of your own blogs.'; }

    $queueAction = (string)($_POST['action'] ?? 'publish'); // publish | draft | schedule
    $scheduledAt = null;
    if ($queueAction === 'schedule') {
        $raw = (string)($_POST['scheduled_at'] ?? '');
        $ts  = $raw !== '' ? strtotime($raw) : false;
        if (!$ts || $ts <= time()) { $errors[] = 'Pick a schedule date/time in the future.'; }
        else {
            $scheduledAt = date('Y-m-d H:i:s', $ts);
            if (in_array($type, ['video','chat'], true)) { $errors[] = 'Only text, photo and link posts can be scheduled.'; }
            $cntStmt = $pdo->prepare('SELECT COUNT(*) FROM posts WHERE blog_id = ? AND status = "scheduled"' . ($editing ? ' AND id != ?' : ''));
            $cntStmt->execute($editing ? [$blogId, $postId] : [$blogId]);
            if ((int)$cntStmt->fetchColumn() >= setting_int('max_queue_per_blog', 20)) {
                $errors[] = 'This blog\'s queue is full (' . setting_int('max_queue_per_blog', 20) . ' max). Publish or delete a queued post first.';
            }
        }
    }

    $data = [
        'heading' => trim((string)($_POST['heading'] ?? '')),
        'body' => trim((string)($_POST['body'] ?? '')),
        'caption' => trim((string)($_POST['caption'] ?? '')),
        'alt_text' => trim((string)($_POST['alt_text'] ?? '')),
        'quote_text' => trim((string)($_POST['quote_text'] ?? '')),
        'quote_source' => trim((string)($_POST['quote_source'] ?? '')),
        'link_title' => trim((string)($_POST['link_title'] ?? '')),
        'link_url' => trim((string)($_POST['link_url'] ?? '')),
    ];
    if ($type === 'text' && $data['heading'] === '' && $data['body'] === '') $errors[] = 'Add a heading or some text.';
    if ($type === 'quote' && $data['quote_text'] === '') $errors[] = 'Add the quote text.';
    if ($type === 'link') {
        if ($data['link_url'] === '' || !filter_var($data['link_url'], FILTER_VALIDATE_URL)) $errors[] = 'Add a valid link URL.';
        else $data['link_domain'] = (string)parse_url($data['link_url'], PHP_URL_HOST);
    }
    $chatLines = [];
    if ($type === 'chat') {
        $names = $_POST['chat_who'] ?? []; $texts = $_POST['chat_line'] ?? [];
        foreach ($names as $i => $who) {
            $who = trim((string)$who); $line = trim((string)($texts[$i] ?? ''));
            if ($who !== '' && $line !== '') $chatLines[] = [$who, $line];
        }
        if (!$chatLines) $errors[] = 'Add at least one chat line.';
    }

    $mediaPath = $editing ? $post['media_path'] : null;
    $mediaKind = $editing ? ($post['media_kind'] ?? 'none') : 'none';
    $duration  = trim((string)($_POST['duration'] ?? ''));
    if ($type === 'photo' || $type === 'video') {
        if (!empty($_FILES['media']['name'])) {
            $up = handle_upload('media', UPLOAD_DIR_MEDIA, $type === 'video' ? 'video' : 'image');
            if (!$up['ok']) { $errors[] = $up['message']; }
            else { $mediaPath = $up['path']; $mediaKind = $up['kind']; }
        } elseif (!$editing) {
            $errors[] = $type === 'video' ? 'Choose a video file to upload.' : 'Choose a photo to upload.';
        }
    }

    if (!$errors) {
        $status = $queueAction === 'draft' ? 'draft' : ($queueAction === 'schedule' ? 'scheduled' : 'published');
        $publishedAt = $status === 'published' ? date('Y-m-d H:i:s') : ($editing && $post['published_at'] ? $post['published_at'] : null);

        $cols = [
            'blog_id' => $blogId, 'type' => $type, 'heading' => $data['heading'] ?: null, 'body' => $data['body'] ?: null,
            'caption' => $data['caption'] ?: null, 'alt_text' => $data['alt_text'] ?: null,
            'media_path' => $mediaPath, 'media_kind' => $mediaKind, 'duration' => $duration ?: null,
            'quote_text' => $data['quote_text'] ?: null, 'quote_source' => $data['quote_source'] ?: null,
            'link_title' => $data['link_title'] ?: null, 'link_url' => $data['link_url'] ?: null, 'link_domain' => $data['link_domain'] ?? null,
            'chat_json' => $chatLines ? json_encode($chatLines) : null,
            'status' => $status, 'scheduled_at' => $scheduledAt, 'published_at' => $publishedAt,
        ];

        if ($editing) {
            $set = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($cols)));
            $pdo->prepare("UPDATE posts SET $set WHERE id = :id")->execute($cols + ['id' => $postId]);
            $newId = $postId;
        } else {
            $ph = implode(',', array_map(fn($k) => ":$k", array_keys($cols)));
            $pdo->prepare('INSERT INTO posts (' . implode(',', array_keys($cols)) . ") VALUES ($ph)")->execute($cols);
            $newId = (int)$pdo->lastInsertId();
        }
        set_post_tags($newId, (string)($_POST['tags'] ?? ''));

        $_SESSION['flash']['app'] = ['type' => 'ok', 'text' => $status === 'scheduled' ? 'Post queued.' : ($status === 'draft' ? 'Draft saved.' : 'Post published.')];
        header('Location: ' . url('dashboard/posts.php'));
        exit;
    }
    $post = array_merge($post, $data, ['type' => $type, 'blog_id' => $blogId, 'chat_lines' => $chatLines ?: [['', '']]]);
}

$app_title = $editing ? 'Edit post' : 'New post'; $app_active = 'new';
require __DIR__ . '/../includes/app-start.php';
?>
<?php if ($errors): ?><p class="app-flash err"><?php foreach ($errors as $er) echo e($er) . '<br>'; ?></p><?php endif; ?>

<form class="app-card" method="post" enctype="multipart/form-data" novalidate>
  <?= csrf_field() ?>
  <div class="compose-types" role="tablist">
    <?php foreach (['text'=>'Text','photo'=>'Photo','video'=>'Video','link'=>'Link','quote'=>'Quote','chat'=>'Chat'] as $k=>$label): ?>
      <button type="button" class="type-btn<?= $post['type']===$k?' on':'' ?>" data-type="<?= $k ?>"><?= icon($k==='photo'?'photo':$k) ?> <?= $label ?></button>
    <?php endforeach; ?>
  </div>
  <input type="hidden" name="type" id="typeField" value="<?= e($post['type']) ?>">

  <label class="field"><span>Post to blog</span>
    <select name="blog_id">
      <?php foreach ($myBlogs as $b): ?>
        <option value="<?= (int)$b['id'] ?>" <?= (int)$post['blog_id'] === (int)$b['id'] ? 'selected' : '' ?>><?= e($b['title']) ?> (<?= e($b['slug']) ?>)<?= $b['is_main'] ? ' — main' : '' ?></option>
      <?php endforeach; ?>
    </select></label>

  <div class="pf" data-for="text">
    <label class="field"><span>Heading</span><input type="text" name="heading" value="<?= e($post['heading']) ?>"></label>
    <label class="field"><span>Text</span><textarea name="body" rows="6"><?= e($post['body']) ?></textarea></label>
  </div>

  <div class="pf" data-for="photo,video">
    <label class="field"><span><?= $post['type']==='video'?'Video file (MP4/WebM/MOV)':'Photo file' ?></span>
      <input type="file" name="media" accept="<?= $post['type']==='video'?'video/mp4,video/webm,video/quicktime':'image/*' ?>"></label>
    <?php if ($editing && $post['media_path']): ?><p class="fine">Current file: <?= e(basename($post['media_path'])) ?> (leave blank to keep it)</p><?php endif; ?>
    <label class="field"><span>Caption</span><textarea name="caption" rows="3"><?= e($post['caption']) ?></textarea></label>
    <label class="field"><span>Alt text (accessibility / SEO)</span><input type="text" name="alt_text" value="<?= e($post['alt_text'] ?? '') ?>"></label>
    <div class="pf" data-for="video">
      <label class="field"><span>Duration label (optional, e.g. 0:42)</span><input type="text" name="duration" value="<?= e($post['duration'] ?? '') ?>"></label>
      <p class="fine">Uploads as-is by default. <?= setting_bool('ffmpeg_enabled') ? 'FFmpeg transcoding is enabled site-wide.' : 'FFmpeg transcoding is currently off (Admin > Settings > Media).' ?></p>
    </div>
  </div>

  <div class="pf" data-for="quote">
    <label class="field"><span>Quote</span><textarea name="quote_text" rows="4"><?= e($post['quote_text']) ?></textarea></label>
    <label class="field"><span>Source (optional)</span><input type="text" name="quote_source" value="<?= e($post['quote_source']) ?>"></label>
  </div>

  <div class="pf" data-for="link">
    <label class="field"><span>Link title</span><input type="text" name="link_title" value="<?= e($post['link_title']) ?>"></label>
    <label class="field"><span>URL</span><input type="url" name="link_url" value="<?= e($post['link_url']) ?>" placeholder="https://"></label>
  </div>

  <div class="pf" data-for="chat">
    <span style="display:block;margin-bottom:6px;font-weight:600;font-size:13.5px">Chat lines</span>
    <div id="chatLines">
      <?php foreach ($post['chat_lines'] as $ln): [$who, $line] = $ln + ['', '']; ?>
        <div class="two" style="margin-bottom:8px"><input type="text" name="chat_who[]" placeholder="Name" value="<?= e($who) ?>"><input type="text" name="chat_line[]" placeholder="Line" value="<?= e($line) ?>"></div>
      <?php endforeach; ?>
    </div>
    <button type="button" class="btn-xs" id="addChatLine">+ Add line</button>
  </div>

  <label class="field"><span>Tags (comma separated)</span><input type="text" name="tags" value="<?= e($post['tags']) ?>" placeholder="neon, selfie, nightshift"></label>

  <label class="field"><span>Publish</span>
    <select name="action" id="actionField">
      <option value="publish" <?= $post['status']==='published'?'selected':'' ?>>Publish now</option>
      <option value="draft" <?= $post['status']==='draft'?'selected':'' ?>>Save as draft</option>
      <option value="schedule" <?= $post['status']==='scheduled'?'selected':'' ?>>Add to queue (scheduled)</option>
    </select></label>
  <label class="field" id="scheduleField" style="<?= $post['status']==='scheduled'?'':'display:none' ?>"><span>Scheduled for</span>
    <input type="datetime-local" name="scheduled_at" value="<?= e($post['scheduled_at']) ?>"></label>

  <button class="btn btn-fill" type="submit"><?= $editing ? 'Save changes' : 'Save post' ?></button>
  <?php if ($editing): ?>
    <button class="btn btn-ghost" type="submit" form="deleteForm">Delete post</button>
  <?php endif; ?>
</form>
<?php if ($editing): ?>
  <form id="deleteForm" method="post" action="<?= e(url('dashboard/post-delete.php')) ?>" onsubmit="return confirm('Delete this post permanently?')">
    <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$postId ?>">
  </form>
<?php endif; ?>

<script>
(function(){
  var typeBtns=document.querySelectorAll('.type-btn'), typeField=document.getElementById('typeField');
  function show(t){
    document.querySelectorAll('.pf').forEach(function(p){ p.style.display=p.dataset.for.split(',').includes(t)?'':'none'; });
    typeBtns.forEach(function(b){ b.classList.toggle('on', b.dataset.type===t); });
    typeField.value=t;
  }
  typeBtns.forEach(function(b){ b.addEventListener('click',function(){ show(b.dataset.type); }); });
  show(typeField.value);
  document.getElementById('actionField').addEventListener('change',function(){
    document.getElementById('scheduleField').style.display = this.value==='schedule' ? '' : 'none';
  });
  document.getElementById('addChatLine').addEventListener('click',function(){
    var row=document.createElement('div'); row.className='two'; row.style.marginBottom='8px';
    row.innerHTML='<input type="text" name="chat_who[]" placeholder="Name"><input type="text" name="chat_line[]" placeholder="Line">';
    document.getElementById('chatLines').appendChild(row);
  });
})();
</script>
<?php require __DIR__ . '/../includes/app-end.php'; ?>
