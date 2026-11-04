<?php
require_once __DIR__ . '/../includes/config.php';
require_admin();
require __DIR__ . '/_nav.php';
$app_title = 'Overview'; $app_active = 'overview';
$pdo = db();
$stats = [
    'Users'            => (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'Blogs'            => (int)$pdo->query('SELECT COUNT(*) FROM blogs')->fetchColumn(),
    'Published posts'  => (int)$pdo->query("SELECT COUNT(*) FROM posts WHERE status='published'")->fetchColumn(),
    'Queued posts'     => (int)$pdo->query("SELECT COUNT(*) FROM posts WHERE status='scheduled'")->fetchColumn(),
    'Open reports'     => (int)$pdo->query("SELECT COUNT(*) FROM reports WHERE status='open'")->fetchColumn(),
    'Open tickets'     => (int)$pdo->query("SELECT COUNT(*) FROM support_tickets WHERE status='open'")->fetchColumn(),
    'Premium members'  => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE premium_tier != 'none'")->fetchColumn(),
    'Suspended/banned' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status != 'active'")->fetchColumn(),
];
$recentUsers = $pdo->query('SELECT username, email, created_at FROM users ORDER BY created_at DESC LIMIT 8')->fetchAll();
$recentReports = $pdo->query("SELECT id, target_type, target_id, reason, created_at FROM reports WHERE status='open' ORDER BY created_at DESC LIMIT 6")->fetchAll();

require __DIR__ . '/../includes/app-start.php';
?>
<div class="stat-row">
  <?php foreach ($stats as $label => $n): ?><div class="stat"><b><?= $n ?></b><span><?= e($label) ?></span></div><?php endforeach; ?>
</div>

<div class="two" style="align-items:start">
  <div class="app-card">
    <h2>Newest signups</h2>
    <div class="table-wrap"><table class="data-table"><thead><tr><th>User</th><th>Email</th><th>Joined</th></tr></thead><tbody>
      <?php foreach ($recentUsers as $u): ?><tr><td><?= e($u['username']) ?></td><td><?= e($u['email']) ?></td><td><?= e(date('M j', strtotime($u['created_at']))) ?></td></tr><?php endforeach; ?>
    </tbody></table></div>
  </div>
  <div class="app-card">
    <h2>Open reports <a class="btn-xs" style="float:right" href="<?= e(url('admin/reports.php')) ?>">View all</a></h2>
    <div class="table-wrap"><table class="data-table"><thead><tr><th>Target</th><th>Reason</th><th>When</th></tr></thead><tbody>
      <?php foreach ($recentReports as $r): ?><tr><td><?= e($r['target_type']) ?> #<?= (int)$r['target_id'] ?></td><td><?= e(mb_substr($r['reason'],0,50)) ?></td><td><?= e(date('M j', strtotime($r['created_at']))) ?></td></tr><?php endforeach; ?>
      <?php if (!$recentReports): ?><tr><td colspan="3">Nothing open. 🎉</td></tr><?php endif; ?>
    </tbody></table></div>
  </div>
</div>
<?php require __DIR__ . '/../includes/app-end.php'; ?>
