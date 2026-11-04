<?php
require_once __DIR__ . '/../includes/config.php';
$me = require_login();
require __DIR__ . '/_nav.php';
$app_title = 'Referrals'; $app_active = 'referral';

$pdo = db();
$stmt = $pdo->prepare('SELECT r.points_awarded, r.created_at, u.username FROM referrals r JOIN users u ON u.id = r.referred_user_id WHERE r.referrer_user_id = ? ORDER BY r.created_at DESC');
$stmt->execute([$me['id']]);
$rows = $stmt->fetchAll();
$totalEarned = array_sum(array_column($rows, 'points_awarded'));
$refLink = SITE_URL . url('/?ref=' . $me['referral_code']);

require __DIR__ . '/../includes/app-start.php';
?>
<div class="app-card">
  <h2>Your referral link</h2>
  <p>Share this link. Everyone who signs up through it earns you <b><?= setting_int('referral_points', 100) ?> points</b>, and points can be spent boosting your posts in the <a class="lnk" href="<?= e(url('advertising.php')) ?>">advertising marketplace</a>.</p>
  <div class="two">
    <input type="text" readonly value="<?= e($refLink) ?>" onclick="this.select()" style="background:var(--black);border:1px solid var(--line);color:var(--text);padding:12px 14px;width:100%">
    <button class="btn btn-line" type="button" onclick="navigator.clipboard.writeText('<?= e($refLink) ?>');this.textContent='Copied!'">Copy link</button>
  </div>
</div>
<div class="app-card">
  <h2>Referral history — <?= (int)$totalEarned ?> points earned total</h2>
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th>New member</th><th>Points earned</th><th>Date</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr><td><?= e($r['username']) ?></td><td>+<?= (int)$r['points_awarded'] ?></td><td><?= e(date('M j, Y', strtotime($r['created_at']))) ?></td></tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="3">No referrals yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/../includes/app-end.php'; ?>
