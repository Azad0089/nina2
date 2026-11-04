<?php
require_once __DIR__ . '/../includes/config.php';
require_admin();
require __DIR__ . '/_nav.php';
$app_title = 'Blogs'; $app_active = 'blogs';
$pdo = db();
$ok = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid()) {
    $id = (int)($_POST['id'] ?? 0); $act = (string)($_POST['act'] ?? '');
    switch ($act) {
        case 'suspend': $pdo->prepare("UPDATE blogs SET status='suspended' WHERE id=?")->execute([$id]); $ok = 'Blog suspended.'; break;
        case 'unsuspend': $pdo->prepare("UPDATE blogs SET status='active' WHERE id=?")->execute([$id]); $ok = 'Blog reactivated.'; break;
        case 'ban': $pdo->prepare("UPDATE blogs SET status='banned' WHERE id=?")->execute([$id]); $ok = 'Blog banned.'; break;
        case 'delete':
            $pdo->prepare('DELETE FROM posts WHERE blog_id=?')->execute([$id]);
            $pdo->prepare('DELETE FROM follows WHERE blog_id=?')->execute([$id]);
            $pdo->prepare('DELETE FROM blogs WHERE id=?')->execute([$id]);
            $ok = 'Blog deleted.';
            break;
        case 'feature':
            $days = max(0, (int)($_POST['days'] ?? 7));
            if ($days > 0) $pdo->prepare('UPDATE blogs SET is_featured=1, featured_until=DATE_ADD(NOW(), INTERVAL ? DAY) WHERE id=?')->execute([$days, $id]);
            else $pdo->prepare('UPDATE blogs SET is_featured=1, featured_until=NULL WHERE id=?')->execute([$id]);
            $ok = 'Blog featured.';
            break;
        case 'unfeature': $pdo->prepare('UPDATE blogs SET is_featured=0, featured_until=NULL WHERE id=?')->execute([$id]); $ok = 'Removed from featured.'; break;
    }
}

$q = trim((string)($_GET['q'] ?? ''));
$sql = 'SELECT b.*, u.username FROM blogs b JOIN users u ON u.id = b.user_id';
$params = [];
if ($q !== '') { $sql .= ' WHERE b.slug LIKE ? OR b.title LIKE ? OR u.username LIKE ?'; $params = ["%$q%","%$q%","%$q%"]; }
$sql .= ' ORDER BY b.created_at DESC LIMIT 200';
$stmt = $pdo->prepare($sql); $stmt->execute($params); $blogs = $stmt->fetchAll();

require __DIR__ . '/../includes/app-start.php';
?>
<?php if ($ok): ?><p class="app-flash"><?= e($ok) ?></p><?php endif; ?>
<div class="app-card">
  <form method="get" class="two" style="margin-bottom:0">
    <label class="field" style="margin:0"><span>Search blog or owner</span><input type="text" name="q" value="<?= e($q) ?>"></label>
    <div style="align-self:end"><button class="btn btn-line" type="submit">Search</button></div>
  </form>
</div>
<div class="app-card">
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th>Blog</th><th>Owner</th><th>Type</th><th>Status</th><th>Featured</th><th>Actions</th></tr></thead>
      <tbody>
      <?php foreach ($blogs as $b): ?>
        <tr>
          <td><a class="lnk" href="<?= e(url('blog.php?u=' . urlencode($b['slug']))) ?>" target="_blank"><?= e($b['title']) ?></a><br><small style="color:var(--mute)"><?= e($b['slug']) ?><?= $b['is_main']?' · main':'' ?></small></td>
          <td><?= e($b['username']) ?></td>
          <td><?= e($b['type']) ?></td>
          <td><span class="pill pill-<?= $b['status']==='active'?'active':($b['status']==='suspended'?'suspended':'banned') ?>"><?= e($b['status']) ?></span></td>
          <td><?= $b['is_featured'] ? '★' . ($b['featured_until'] ? ' until ' . e(date('M j', strtotime($b['featured_until']))) : ' (indefinite)') : '—' ?></td>
          <td class="row-actions">
            <?php if ($b['status'] === 'active'): ?>
              <form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="suspend"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>"><button class="btn-xs" type="submit">Suspend</button></form>
            <?php else: ?>
              <form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="unsuspend"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>"><button class="btn-xs" type="submit">Reactivate</button></form>
            <?php endif; ?>
            <?php if ($b['status'] !== 'banned'): ?>
              <form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="ban"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>"><button class="btn-xs danger" type="submit">Ban</button></form>
            <?php endif; ?>
            <?php if (!$b['is_main']): ?>
            <form method="post" onsubmit="return confirm('Delete this blog and its posts permanently?')"><?= csrf_field() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>"><button class="btn-xs danger" type="submit"><?= icon('trash') ?></button></form>
            <?php endif; ?>
            <?php if ($b['is_featured']): ?>
              <form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="unfeature"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>"><button class="btn-xs" type="submit">Unfeature</button></form>
            <?php else: ?>
              <form method="post" class="row-actions"><?= csrf_field() ?><input type="hidden" name="act" value="feature"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                <select name="days" class="btn-xs"><option value="7">7d</option><option value="30">30d</option><option value="0">Indefinite</option></select>
                <button class="btn-xs" type="submit">Feature</button></form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/../includes/app-end.php'; ?>
