<?php
require_once __DIR__ . '/../includes/config.php';
require_admin();
require __DIR__ . '/_nav.php';
$app_title = 'Support tickets'; $app_active = 'support';
$pdo = db();
$ok = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid()) {
    $id = (int)($_POST['id'] ?? 0); $act = (string)($_POST['act'] ?? '');
    if ($act === 'close') { $pdo->prepare("UPDATE support_tickets SET status='closed' WHERE id=?")->execute([$id]); $ok = 'Ticket closed.'; }
    elseif ($act === 'reopen') { $pdo->prepare("UPDATE support_tickets SET status='open' WHERE id=?")->execute([$id]); $ok = 'Ticket reopened.'; }
}
$statusFilter = $_GET['status'] ?? 'open';
$sql = 'SELECT * FROM support_tickets';
$params = [];
if (in_array($statusFilter, ['open','closed'], true)) { $sql .= ' WHERE status = ?'; $params[] = $statusFilter; }
$sql .= ' ORDER BY created_at DESC LIMIT 200';
$stmt = $pdo->prepare($sql); $stmt->execute($params); $rows = $stmt->fetchAll();
require __DIR__ . '/../includes/app-start.php';
?>
<?php if ($ok): ?><p class="app-flash"><?= e($ok) ?></p><?php endif; ?>
<div class="app-card">
  <div class="row-actions" style="margin-bottom:14px">
    <a class="btn-xs<?= $statusFilter==='open'?' danger':'' ?>" href="?status=open">Open</a>
    <a class="btn-xs<?= $statusFilter==='closed'?' danger':'' ?>" href="?status=closed">Closed</a>
    <a class="btn-xs<?= $statusFilter==='all'?' danger':'' ?>" href="?status=all">All</a>
  </div>
  <?php foreach ($rows as $t): ?>
    <div class="app-card" style="margin-bottom:12px">
      <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div><b><?= e($t['name']) ?></b> · <?= e($t['email']) ?> · <span class="pill pill-<?= $t['topic']==='report'||str_starts_with($t['topic'],'report')?'suspended':'open' ?>"><?= e($t['topic']) ?></span></div>
        <span class="pill pill-<?= $t['status'] ?>"><?= e($t['status']) ?></span>
      </div>
      <?php if ($t['link']): ?><p><small>Link: <a class="lnk" href="<?= e($t['link']) ?>" target="_blank"><?= e($t['link']) ?></a></small></p><?php endif; ?>
      <p><?= nl2br(e($t['message'])) ?></p>
      <p class="fine"><?= e(date('M j, Y H:i', strtotime($t['created_at']))) ?></p>
      <form method="post">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
        <?php if ($t['status'] === 'open'): ?>
          <button class="btn-xs" type="submit" name="act" value="close"><?= icon('check') ?> Mark closed</button>
        <?php else: ?>
          <button class="btn-xs" type="submit" name="act" value="reopen">Reopen</button>
        <?php endif; ?>
      </form>
    </div>
  <?php endforeach; ?>
  <?php if (!$rows): ?><p>No tickets here.</p><?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/app-end.php'; ?>
