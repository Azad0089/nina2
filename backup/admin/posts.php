<?php
require_once __DIR__ . '/../includes/config.php';
require_admin();
require __DIR__ . '/_nav.php';
$app_title = 'Posts'; $app_active = 'posts';
$pdo = db();
$ok = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid()) {
    $id = (int)($_POST['id'] ?? 0); $act = (string)($_POST['act'] ?? '');
    if ($act === 'delete') {
        $pdo->prepare('DELETE FROM posts WHERE id=?')->execute([$id]);
        $pdo->prepare('DELETE FROM post_tags WHERE post_id=?')->execute([$id]);
        $pdo->prepare('DELETE FROM likes WHERE post_id=?')->execute([$id]);
        $ok = 'Post deleted.';
    } elseif ($act === 'feature') {
        $days = max(1, (int)($_POST['days'] ?? 7));
        $pdo->prepare('UPDATE posts SET is_featured=1, featured_until=DATE_ADD(NOW(), INTERVAL ? DAY) WHERE id=?')->execute([$days, $id]);
        $ok = 'Post featured.';
    } elseif ($act === 'unfeature') {
        $pdo->prepare('UPDATE posts SET is_featured=0, featured_until=NULL WHERE id=?')->execute([$id]);
        $ok = 'Post unfeatured.';
    }
}

$type = $_GET['type'] ?? 'all';
$sql = "SELECT p.id, p.type, p.status, p.heading, p.caption, p.quote_text, p.link_title, p.is_featured, p.created_at, b.slug AS blog_slug
        FROM posts p JOIN blogs b ON b.id = p.blog_id";
$params = [];
if (in_array($type, ['text','photo','video','link','quote','chat'], true)) { $sql .= ' WHERE p.type = ?'; $params[] = $type; }
$sql .= ' ORDER BY p.created_at DESC LIMIT 200';
$stmt = $pdo->prepare($sql); $stmt->execute($params); $rows = $stmt->fetchAll();

require __DIR__ . '/../includes/app-start.php';
?>
<?php if ($ok): ?><p class="app-flash"><?= e($ok) ?></p><?php endif; ?>
<div class="app-card">
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th>Type</th><th>Blog</th><th>Preview</th><th>Status</th><th>Featured</th><th>Date</th><th>Actions</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r): $preview = $r['heading'] ?: $r['caption'] ?: $r['quote_text'] ?: $r['link_title'] ?: '(' . $r['type'] . ')'; ?>
        <tr>
          <td><?= e($r['type']) ?></td>
          <td><a class="lnk" href="<?= e(url('blog.php?u=' . urlencode($r['blog_slug']))) ?>" target="_blank"><?= e($r['blog_slug']) ?></a></td>
          <td><a class="lnk" href="<?= e(url('post.php?id=' . $r['id'])) ?>" target="_blank"><?= e(mb_substr((string)$preview, 0, 50)) ?></a></td>
          <td><span class="pill pill-<?= $r['status']==='published'?'active':'open' ?>"><?= e($r['status']) ?></span></td>
          <td><?= $r['is_featured'] ? '★' : '—' ?></td>
          <td><?= e(date('M j, Y', strtotime($r['created_at']))) ?></td>
          <td class="row-actions">
            <?php if ($r['is_featured']): ?>
              <form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="unfeature"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-xs" type="submit">Unfeature</button></form>
            <?php else: ?>
              <form method="post" class="row-actions"><?= csrf_field() ?><input type="hidden" name="act" value="feature"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <select name="days" class="btn-xs"><option value="3">3d</option><option value="7">7d</option><option value="30">30d</option></select>
                <button class="btn-xs" type="submit">Feature</button></form>
            <?php endif; ?>
            <form method="post" onsubmit="return confirm('Delete this post permanently?')"><?= csrf_field() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-xs danger" type="submit"><?= icon('trash') ?></button></form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/../includes/app-end.php'; ?>
