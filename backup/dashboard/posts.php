<?php
require_once __DIR__ . '/../includes/config.php';
$me = require_login();
require __DIR__ . '/_nav.php';
$app_title = 'My posts'; $app_active = 'posts';

$pdo = db();
$stmt = $pdo->prepare("SELECT p.id, p.type, p.status, p.published_at, p.scheduled_at, p.heading, p.caption, p.quote_text, p.link_title, b.slug AS blog_slug,
    (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) AS like_count
    FROM posts p JOIN blogs b ON b.id = p.blog_id WHERE b.user_id = ? ORDER BY (p.status='scheduled') DESC, p.scheduled_at ASC, p.created_at DESC LIMIT 200");
$stmt->execute([$me['id']]);
$rows = $stmt->fetchAll();

require __DIR__ . '/../includes/app-start.php';
?>
<div class="app-card">
  <h2>All posts <a class="btn btn-fill" style="float:right;padding:8px 14px" href="<?= e(url('dashboard/post.php')) ?>"><?= icon('plus') ?> New post</a></h2>
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th>Type</th><th>Blog</th><th>Preview</th><th>Status</th><th>When</th><th>Likes</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r):
          $preview = $r['heading'] ?: $r['caption'] ?: $r['quote_text'] ?: $r['link_title'] ?: '(' . $r['type'] . ' post)';
      ?>
        <tr>
          <td><?= icon($r['type']==='photo'?'photo':$r['type']) ?> <?= e($r['type']) ?></td>
          <td><?= e($r['blog_slug']) ?></td>
          <td><?= e(mb_substr((string)$preview, 0, 60)) ?></td>
          <td><span class="pill pill-<?= $r['status']==='published'?'active':($r['status']==='scheduled'?'open':'suspended') ?>"><?= e($r['status']) ?></span></td>
          <td><?= e($r['status']==='scheduled' ? date('M j, Y H:i', strtotime($r['scheduled_at'])) : ($r['published_at'] ? date('M j, Y', strtotime($r['published_at'])) : '—')) ?></td>
          <td><?= (int)$r['like_count'] ?></td>
          <td class="row-actions">
            <a class="btn-xs" href="<?= e(url('dashboard/post.php?id=' . $r['id'])) ?>"><?= icon('edit') ?></a>
            <form method="post" action="<?= e(url('dashboard/post-delete.php')) ?>" onsubmit="return confirm('Delete this post?')">
              <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button class="btn-xs danger" type="submit"><?= icon('trash') ?></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="7">No posts yet. <a class="lnk" href="<?= e(url('dashboard/post.php')) ?>">Write your first one</a>.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/../includes/app-end.php'; ?>
