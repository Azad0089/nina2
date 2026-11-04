<?php
require_once __DIR__ . '/includes/config.php';
$page_title = 'Premium';
$page_desc  = 'Support ' . site_name() . ' with a crypto donation and get a premium verified badge plus monthly points for one year.';
$page_path  = 'premium.php';
$active_nav = 'premium';
require __DIR__ . '/includes/page-start.php';

$hero = ['title' => 'Go Premium', 'lead' => 'Donations keep the site running. In return you get a badge and monthly points to promote your blog.', 'color' => '#ffd60a'];

// SAMPLE VALUES. Later: read from the admin settings.
$plans = [
  ['name' => 'Premium',  'color' => '#ff2bd6', 'amount' => '15', 'points' => '5,000',  'featured' => false,
   'perks' => ['Premium verified badge for 1 year', '5,000 points every month', 'Spend points on views and placements', 'Renew with another donation when the year ends']],
  ['name' => 'Premium+', 'color' => '#ffd60a', 'amount' => '30', 'points' => '11,000', 'featured' => true,
   'perks' => ['Premium verified badge for 1 year', '11,000 points every month', 'Spend points on views and placements', 'Renew with another donation when the year ends']],
];
$badges = [
  ['pro', 'Premium verified', 'Given with Premium and Premium+. Lasts one year and can be renewed with another donation.', '#ffd60a'],
  ['id',  'Identity verified', 'For big creators who send a photo ID for review. The badge lasts for life.', '#19e3ff'],
  ['vip', 'VIP', 'Handed out by the admin to members who give a lot to the community. Lifetime.', '#ff2bd6'],
];
?>
<main class="page" id="main">
  <?php require __DIR__ . '/includes/page-hero.php'; ?>

  <div class="plans">
    <?php foreach ($plans as $pl): ?>
      <section class="plan<?= $pl['featured'] ? ' plan-hot' : '' ?>" style="--c:<?= e($pl['color']) ?>">
        <?php if ($pl['featured']): ?><span class="ribbon">Best value</span><?php endif; ?>
        <h2><?= e($pl['name']) ?></h2>
        <p class="price"><span>Donate</span> $<?= e($pl['amount']) ?><small>+ in crypto</small></p>
        <p class="pts"><?= badge('pro') ?><?= e($pl['points']) ?> points / month</p>
        <ul>
          <?php foreach ($pl['perks'] as $perk): ?><li><?= icon('verified') ?><?= e($perk) ?></li><?php endforeach; ?>
        </ul>
        <a class="btn btn-fill btn-wide" href="<?= e(url('login.php')) ?>" data-modal="login">Donate with crypto</a>
      </section>
    <?php endforeach; ?>
  </div>
  <p class="fine center">Payments are handled by NOWPayments and BitCart. Premium lasts one year from your donation. Amounts and points can change; see the <a class="lnk" href="<?= e(url('terms.php')) ?>#premium">Terms</a>.</p>

  <h2 class="sec-title">Three badges</h2>
  <div class="tiles tiles-3">
    <?php foreach ($badges as [$b, $title, $text, $col]): ?>
      <div class="tile" style="--c:<?= e($col) ?>"><?= badge($b) ?><h3><?= e($title) ?></h3><p><?= e($text) ?></p></div>
    <?php endforeach; ?>
  </div>
  <p class="fine">You can have all three badges at the same time.</p>

  <h2 class="sec-title">How points work</h2>
  <ol class="steps">
    <li><b>Donate</b><span>Pay in crypto and your account is upgraded automatically.</span></li>
    <li><b>Collect points</b><span>Premium members get their points every month. Everybody can also earn 100 points per friend they refer.</span></li>
    <li><b>Get seen</b><span>Spend points in the <a class="lnk" href="<?= e(url('advertising.php')) ?>">advertising marketplace</a> to feature your blog or posts.</span></li>
  </ol>

  <div class="tile tile-wide" style="--c:#ff2bd6"><?= icon('gift') ?><div><h3>Invite friends, earn points</h3><p>Every member has a referral link. You get 100 points for each person who signs up with it.</p></div><a class="btn btn-line" href="<?= e(url('register.php')) ?>" data-modal="register">Create an account</a></div>
</main>
<?php require __DIR__ . '/includes/page-end.php'; ?>
