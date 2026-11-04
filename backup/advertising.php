<?php
require_once __DIR__ . '/includes/config.php';
$page_title = 'Advertising';
$page_desc  = 'Promote your blog or posts on ' . site_name() . '. Buy views or flat-rate placements with points.';
$page_path  = 'advertising.php';
$active_nav = 'advertising';
require __DIR__ . '/includes/page-start.php';

$hero = ['title' => 'Advertising marketplace', 'lead' => 'Spend your points to get your blog or posts in front of more people.', 'color' => '#ff2bd6'];

// SAMPLE VALUES. Later: loaded from the database, prices set in the admin panel.
$earn = [
  ['Premium',  '5,000 points every month', '#ff2bd6'],
  ['Premium+', '11,000 points every month', '#ffd60a'],
  ['Referrals', '100 points per invited member', '#19e3ff'],
];
$view_packs = [
  ['Starter',  '1,000 views',   '100 pts',   false],
  ['Boost',    '5,000 views',   '450 pts',   false],
  ['Popular',  '20,000 views',  '1,600 pts', true],
  ['Blast',    '100,000 views', '7,000 pts', false],
];
$flat = [
  ['Explore: featured blog',  'Top of the Explore page',     [['1 day', '800 pts'], ['7 days', '4,500 pts'], ['30 days', '15,000 pts']]],
  ['Front page: featured post', 'Front page for logged-out visitors', [['1 day', '600 pts'], ['7 days', '3,500 pts'], ['30 days', '12,000 pts']]],
  ['Sidebar: blogs to follow', 'Suggested blogs next to the feed',  [['1 day', '300 pts'], ['7 days', '1,800 pts'], ['30 days', '6,000 pts']]],
];
?>
<main class="page" id="main">
  <?php require __DIR__ . '/includes/page-hero.php'; ?>

  <div class="balance">
    <div><small>Your points</small><b>Sign in to see your balance</b></div>
    <a class="btn btn-fill" href="<?= e(url('login.php')) ?>" data-modal="login">Sign in</a>
  </div>

  <h2 class="sec-title">Earn points</h2>
  <div class="tiles tiles-3">
    <?php foreach ($earn as [$t, $d, $c]): ?>
      <div class="tile" style="--c:<?= e($c) ?>"><?= icon('star') ?><h3><?= e($t) ?></h3><p><?= e($d) ?></p></div>
    <?php endforeach; ?>
  </div>
  <p class="fine">Not a premium member yet? <a class="lnk" href="<?= e(url('premium.php')) ?>">See Premium</a>.</p>

  <h2 class="sec-title">View packages</h2>
  <p class="sec-sub">Your post or blog is shown on Explore and other placements until the views are used up.</p>
  <div class="packs">
    <?php foreach ($view_packs as [$name, $views, $price, $hot]): ?>
      <div class="pack<?= $hot ? ' pack-hot' : '' ?>">
        <?php if ($hot): ?><span class="ribbon">Popular</span><?php endif; ?>
        <h3><?= e($name) ?></h3>
        <p class="big"><?= e($views) ?></p>
        <p class="cost"><?= e($price) ?></p>
        <a class="btn <?= $hot ? 'btn-fill' : 'btn-line' ?> btn-wide" href="<?= e(url('login.php')) ?>" data-modal="login">Buy</a>
      </div>
    <?php endforeach; ?>
  </div>

  <h2 class="sec-title">Flat-rate placements</h2>
  <p class="sec-sub">Prefer a fixed period? Book a placement by the day, week or month.</p>
  <div class="flat">
    <?php foreach ($flat as [$name, $where, $rates]): ?>
      <section class="flat-row">
        <div><h3><?= e($name) ?></h3><p><?= e($where) ?></p></div>
        <div class="rates">
          <?php foreach ($rates as [$len, $cost]): ?>
            <a class="rate" href="<?= e(url('login.php')) ?>" data-modal="login"><b><?= e($len) ?></b><span><?= e($cost) ?></span></a>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>
  </div>
</main>
<?php require __DIR__ . '/includes/page-end.php'; ?>
