<?php
require_once __DIR__ . '/../includes/config.php';
$me = require_login();
require __DIR__ . '/_nav.php';
$app_title = 'My blogs'; $app_active = 'blogs';
$pdo = db();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid()) {
    $act = (string)($_POST['act'] ?? '');
    if ($act === 'create') {
        $title = trim((string)($_POST['title'] ?? ''));
        $slug = slugify($title);
        if ($title === '' || strlen($title) < 2) { $errors[] = 'Give the new blog a name.'; }
        else {
            $chk = $pdo->prepare('SELECT id FROM blogs WHERE slug = ?'); $chk->execute([$slug]);
            if ($chk->fetch()) { $errors[] = 'That blog name is taken. Try another.'; }
            else {
                $pdo->prepare('INSERT INTO blogs (user_id, slug, title, color) VALUES (?, ?, ?, ?)')
                    ->execute([$me['id'], $slug, $title, '#' . substr(md5((string)microtime(true)), 0, 6)]);
                $_SESSION['flash']['app'] = ['type' => 'ok', 'text' => 'Blog created.'];
                header('Location: ' . url('dashboard/blogs.php')); exit;
            }
        }
    } elseif ($act === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $own = $pdo->prepare('SELECT * FROM blogs WHERE id = ? AND user_id = ?'); $own->execute([$id, $me['id']]);
        $b = $own->fetch();
        if (!$b) { $errors[] = 'Blog not found.'; }
        else {
            $title = trim((string)($_POST['title'] ?? $b['title']));
            $tagline = trim((string)($_POST['tagline'] ?? ''));
            $color = preg_match('/^#[0-9a-fA-F]{6}$/', (string)($_POST['color'] ?? '')) ? $_POST['color'] : $b['color'];
            $searchable = !empty($_POST['searchable']) ? 1 : 0;
            $bio = trim((string)($_POST['bio'] ?? ''));
            $w1 = trim((string)($_POST['website1'] ?? '')); $w2 = trim((string)($_POST['website2'] ?? '')); $w3 = trim((string)($_POST['website3'] ?? ''));
            $cemail = trim((string)($_POST['contact_email'] ?? ''));
            $type = ($_POST['type'] ?? 'personal') === 'community' ? 'community' : 'personal';
            $showProfile = $b['is_main'] ? 1 : ($type === 'community' ? 1 : (!empty($_POST['show_profile']) ? 1 : 0));
            $avatar = $b['avatar_path']; $cover = $b['cover_path'];
            if (!empty($_FILES['avatar']['name'])) { $up = handle_upload('avatar', UPLOAD_DIR_AVATARS, 'image'); if ($up['ok']) $avatar = $up['path']; else $errors[] = $up['message']; }
            if (!empty($_FILES['cover']['name']))  { $up = handle_upload('cover', UPLOAD_DIR_COVERS, 'image');  if ($up['ok']) $cover  = $up['path']; else $errors[] = $up['message']; }
            if (!$errors) {
                $pdo->prepare('UPDATE blogs SET title=?, tagline=?, color=?, searchable=?, avatar_path=?, cover_path=?, bio=?, website1=?, website2=?, website3=?, contact_email=?, type=?, show_profile=? WHERE id=?')
                    ->execute([$title, $tagline ?: null, $color, $searchable, $avatar, $cover, $bio ?: null, $w1 ?: null, $w2 ?: null, $w3 ?: null, $cemail ?: null, $type, $showProfile, $id]);
                $_SESSION['flash']['app'] = ['type' => 'ok', 'text' => 'Blog updated.'];
                header('Location: ' . url('dashboard/blogs.php')); exit;
            }
        }
    } elseif ($act === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $own = $pdo->prepare('SELECT is_main FROM blogs WHERE id = ? AND user_id = ?'); $own->execute([$id, $me['id']]);
        $b = $own->fetch();
        if (!$b) { $errors[] = 'Blog not found.'; }
        elseif ((int)$b['is_main'] === 1) { $errors[] = 'Your main blog cannot be deleted.'; }
        else {
            $pdo->prepare('DELETE FROM posts WHERE blog_id = ?')->execute([$id]);
            $pdo->prepare('DELETE FROM follows WHERE blog_id = ?')->execute([$id]);
            $pdo->prepare('DELETE FROM blogs WHERE id = ?')->execute([$id]);
            $_SESSION['flash']['app'] = ['type' => 'ok', 'text' => 'Blog deleted.'];
            header('Location: ' . url('dashboard/blogs.php')); exit;
        }
    }
}

$stmt = $pdo->prepare('SELECT * FROM blogs WHERE user_id = ? ORDER BY is_main DESC, created_at ASC');
$stmt->execute([$me['id']]);
$blogs = $stmt->fetchAll();

require __DIR__ . '/../includes/app-start.php';
?>
<?php if ($errors): ?><p class="app-flash err"><?php foreach ($errors as $er) echo e($er) . '<br>'; ?></p><?php endif; ?>

<div class="app-card">
  <h2>Create a new blog</h2>
  <form method="post" class="two">
    <?= csrf_field() ?><input type="hidden" name="act" value="create">
    <label class="field"><span>Blog name</span><input type="text" name="title" required></label>
    <div style="align-self:end"><button class="btn btn-fill" type="submit"><?= icon('plus') ?> Create blog</button></div>
  </form>
</div>

<?php foreach ($blogs as $b): ?>
  <div class="app-card" <?= $b['is_main'] ? 'id="main"' : '' ?>>
    <h2 style="display:flex;align-items:center;gap:10px">
      <span style="--c:<?= e($b['color']) ?>;width:28px;height:28px;border-radius:50%;background:var(--c);display:inline-block"></span>
      <?= e($b['title']) ?> <small style="color:var(--mute);font-weight:400">(<?= e($b['slug']) ?>)</small>
      <?php if ($b['is_main']): ?><span class="pill pill-active">Main / Profile</span><?php endif; ?>
      <span class="pill pill-<?= $b['status'] === 'active' ? 'active' : ($b['status'] === 'suspended' ? 'suspended' : 'banned') ?>"><?= e($b['status']) ?></span>
      <a href="<?= e(url('blog.php?u=' . urlencode($b['slug']))) ?>" target="_blank" class="btn-xs" style="margin-left:auto"><?= icon('eye') ?> View</a>
    </h2>
    <form method="post" enctype="multipart/form-data">
      <?= csrf_field() ?><input type="hidden" name="act" value="update"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
      <div class="two">
        <label class="field"><span>Title</span><input type="text" name="title" value="<?= e($b['title']) ?>"></label>
        <label class="field"><span>Tagline</span><input type="text" name="tagline" value="<?= e($b['tagline'] ?? '') ?>"></label>
      </div>
      <div class="two">
        <label class="field"><span>Blog color</span><div class="color-row"><input type="color" name="color" value="<?= e($b['color']) ?>"></div></label>
        <label class="check" style="align-self:end"><input type="checkbox" name="searchable" value="1" <?= $b['searchable'] ? 'checked' : '' ?>><span>Allow search engines to crawl this blog</span></label>
      </div>
      <div class="two">
        <label class="field"><span>Avatar</span><input type="file" name="avatar" accept="image/*"></label>
        <label class="field"><span>Cover image</span><input type="file" name="cover" accept="image/*"></label>
      </div>
      <?php if (!$b['is_main']): ?>
      <div class="two">
        <label class="field"><span>Blog type</span>
          <select name="type"><option value="personal" <?= $b['type']==='personal'?'selected':'' ?>>Personal blog</option><option value="community" <?= $b['type']==='community'?'selected':'' ?>>Community (shows profile info below)</option></select></label>
        <label class="check" style="align-self:end"><input type="checkbox" name="show_profile" value="1" <?= $b['show_profile'] ? 'checked' : '' ?>><span>Show bio/links/email on this blog</span></label>
      </div>
      <?php endif; ?>
      <p class="fine" style="margin:8px 0 0"><?= $b['is_main'] ? 'This is your main blog — it always shows your profile info below.' : 'Profile info (shown only if enabled above, or if this is a Community blog):' ?></p>
      <label class="field"><span>Bio</span><textarea name="bio" rows="3"><?= e($b['bio'] ?? '') ?></textarea></label>
      <div class="two">
        <label class="field"><span>Website 1</span><input type="url" name="website1" value="<?= e($b['website1'] ?? '') ?>" placeholder="https://"></label>
        <label class="field"><span>Website 2</span><input type="url" name="website2" value="<?= e($b['website2'] ?? '') ?>" placeholder="https://"></label>
      </div>
      <div class="two">
        <label class="field"><span>Website 3</span><input type="url" name="website3" value="<?= e($b['website3'] ?? '') ?>" placeholder="https://"></label>
        <label class="field"><span>Contact email</span><input type="email" name="contact_email" value="<?= e($b['contact_email'] ?? '') ?>"></label>
      </div>
      <button class="btn btn-fill" type="submit">Save blog</button>
      <?php if (!$b['is_main']): ?>
        <button class="btn btn-ghost" type="submit" form="del-<?= (int)$b['id'] ?>">Delete blog</button>
      <?php endif; ?>
    </form>
    <?php if (!$b['is_main']): ?>
      <form id="del-<?= (int)$b['id'] ?>" method="post" onsubmit="return confirm('Delete this blog and all its posts? This cannot be undone.')">
        <?= csrf_field() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
      </form>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
<?php require __DIR__ . '/../includes/app-end.php'; ?>
