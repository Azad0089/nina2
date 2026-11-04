<?php
require_once __DIR__ . '/../includes/config.php';
require_admin();
require __DIR__ . '/_nav.php';
$app_title = 'Login history'; $app_active = 'users';
$pdo = db();
$userId = (int)($_GET['user'] ?? 0);
$u = null;
if ($userId) { $s = $pdo->prepare('SELECT username, email FROM users WHERE id=?'); $s->execute([$userId]); $u = $s->fetch(); }
$sql = 'SELECT l.*, u.username FROM login_logs l JOIN users u ON u.id = l.user_id';
$params = [];
if ($userId) { $sql .= ' WHERE l.user_id = ?'; $params[] = $userId; }
$sql .= ' ORDER BY l.created_at DESC LIMIT 200';
$stmt = $pdo->prepare($sql); $stmt->execute($params); $rows = $stmt->fetchAll();
require __DIR__ . '/../includes/app-start.php';
?>
<div class="app-card">
  <h2><?= $u ? 'Logins for ' . e($u['username']) : 'All recent logins' ?> <?php if ($u): ?><a class="btn-xs" href="<?= e(url('admin/logins.php')) ?>">Clear filter</a><?php endif; ?></h2>
  <div class="table-wrap"><table class="data-table"><thead><tr><th>User</th><th>IP</th><th>User agent</th><th>When</th></tr></thead><tbody>
    <?php foreach ($rows as $r): ?>
      <tr><td><?= e($r['username']) ?></td><td><?= e($r['ip'] ?? '') ?></td><td><small><?= e(mb_substr((string)$r['user_agent'],0,60)) ?></small></td><td><?= e(date('M j, Y H:i', strtotime($r['created_at']))) ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="4">No login history yet.</td></tr><?php endif; ?>
  </tbody></table></div>
</div>
<?php require __DIR__ . '/../includes/app-end.php'; ?>
