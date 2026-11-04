<?php
require_once __DIR__ . '/includes/config.php';
$page_title = 'Trending';
$page_desc  = 'Trending tags, popular blogs and the most-liked posts on ' . site_name() . ' right now.';
$page_path  = 'trending.php';
$active_nav = 'trending';
require __DIR__ . '/includes/page-start.php';

$hero = ['title' => 'Trending now', 'lead' => 'What everybody is tagging, following and liking this week.', 'color' => '#ff1f4b'];
$tags = trending_tags();
$max  = max(1, max(array_column($tags, 1)));
?>
<main class="page" id="main">
  <?php require __DIR__ . '/includes/page-hero.php'; ?>

  <h2 class="sec-title">Trending tags</h2>
  <div class="tag-cloud">
    <?php foreach ($tags as [$tag, $count]): $lvl = 1 + (int)floor(($count / $max) * 3.99); ?>
      <a href="#" class="tc tc-<?= $lvl ?>">#<?= e($tag) ?><small><?= fmt_count($count) ?></small></a>
    <?php endforeach; ?>
  </div>

  <h2 class="sec-title">Popular blogs</h2>
  <div class="featured">
    <?php foreach (featured_blogs() as $b): ?>
      <a class="fb" href="<?= e($b['href']) ?>" style="--c:<?= e($b['color']) ?>">
        <span class="fb-cover crop" style="<?= e(crop_style($b)) ?>"><img src="<?= e(media_url($b['img'])) ?>" alt="" loading="lazy"></span>
        <span class="fb-av"><?= e(initial($b['name'])) ?></span>
        <b><?= e($b['name']) ?> <?= badge($b['badge']) ?></b>
        <small><?= e($b['tagline']) ?></small>
      </a>
    <?php endforeach; ?>
  </div>

  <h2 class="sec-title">Most liked posts</h2>
  <div class="grid-static">
    <?php foreach (top_posts(6) as $p) { include __DIR__ . '/includes/card.php'; } ?>
  </div>
</main>
<?php require __DIR__ . '/includes/page-end.php'; ?>
