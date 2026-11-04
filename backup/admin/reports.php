<?php
require_once __DIR__ . '/../includes/config.php';
require_admin();
require __DIR__ . '/_nav.php';
$app_title = 'Reports'; $app_active = 'reports';
$pdo = db();
$ok = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid()) {
    $id = (int)($_POST['id'] ?? 0); $act = (string)($_POST['act'] ?? '');
    if ($act === 'resolve') { $pdo->prepare("UPDATE reports SET status='resolved' WHERE id=?")->execute([$id]); $ok = 'Marked resolved.'; }
    elseif ($act === 'dismiss') { $pdo->prepare("UPDATE reports SET status='dismissed' WHERE id=?")->execute([$id]); $ok = 'Dismissed.'; }
    elseif ($act === 'delete_target') {
        $r = $pdo->prepare('SELECT * FROM reports WHERE id=?'); $r->execute([$id]); $rep = $r->fetch();
        if ($rep) {
            if ($rep['target_type'] === 'post') {
                $pdo->prepare('DELETE FROM posts WHERE id=?')->execute([$rep['target_id']]);
                $pdo->prepare('DELETE FROM post_tags WHERE post_id=?')->execute([$rep['target_id']]);
            } elseif ($rep['target_type'] === 'blog') {
                $pdo->prepare("UPDATE blogs SET status='suspended' WHERE id=?")->execute([$rep['target_id']]);
            } elseif ($rep['target_type'] === 'user') {
                $pdo->prepare("UPDATE users SET status='suspended' WHERE id=?")->execute([$rep['target_id']]);
            }
            $pdo->prepare("UPDATE reports SET status='resolved' WHERE id=?")->execute([$id]);
            $ok = 'Content removed / suspended and report resolved.';
        }
    }
}

$statusFilter = $_GET['status'] ?? 'open';
$sql = 'SELECT r.*, u.username AS reporter_name FROM reports r LEFT JOIN users u ON u.id = r.reporter_user_id';
$params = [];
if (in_array($statusFilter, ['open','resolved','dismissed'], true)) { $sql .= ' WHERE r.status = ?'; $params[] = $statusFilter; }
$sql .= ' ORDER BY r.created_at DESC LIMIT 200';
$stmt = $pdo->prepare($sql); $stmt->execute($params); $rows = $stmt->fetchAll();

require __DIR__ . '/../includes/app-start.php';
?>
<?php if ($ok): ?><p class="app-flash"><?= e($ok) ?></p><?php endif; ?>
<div class="app-card">
  <div class="row-actions" style="margin-bottom:14px">
    <a class="btn-xs<?= $statusFilter==='open'?' danger':'' ?>" href="?status=open">Open</a>
    <a class="btn-xs<?= $statusFilter==='resolved'?' danger':'' ?>" href="?status=resolved">Resolved</a>
    <a class="btn-xs<?= $statusFilter==='dismissed'?' danger':'' ?>" href="?status=dismissed">Dismissed</a>
    <a class="btn-xs<?= $statusFilter==='all'?' danger':'' ?>" href="?status=all">All</a>
  </div>
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th>Target</th><th>Reason</th><th>Reported by</th><th>Status</th><th>When</th><th>Actions</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= e($r['target_type']) ?> #<?= (int)$r['target_id'] ?></td>
          <td style="max-width:320px"><?= e($r['reason']) ?></td>
          <td><?= e($r['reporter_name'] ?? 'anonymous') ?></td>
          <td><span class="pill pill-<?= $r['status'] ?>"><?= e($r['status']) ?></span></td>
          <td><?= e(date('M j, Y', strtotime($r['created_at']))) ?></td>
          <td class="row-actions">
            <?php if ($r['status'] === 'open'): ?>
              <form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="resolve"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-xs" type="submit"><?= icon('check') ?> Resolve</button></form>
              <form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="dismiss"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-xs" type="submit"><?= icon('x') ?> Dismiss</button></form>
              <?php if (in_array($r['target_type'], ['post','blog','user'], true) && (int)$r['target_id'] > 0): ?>
              <form method="post" onsubmit="return confirm('Remove/suspend this content now?')"><?= csrf_field() ?><input type="hidden" name="act" value="delete_target"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-xs danger" type="submit"><?= icon('ban') ?> Act now</button></form>
              <?php endif; ?>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="6">Nothing here.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/../includes/app-end.php'; ?>
