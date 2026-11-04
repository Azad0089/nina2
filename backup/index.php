<?php
require __DIR__ . '/includes/config.php';

$type = valid_type($_GET['type'] ?? null);          // ?type=photo (crawlable filter links)
$view = (($_GET['view'] ?? '') === 'list') ? 'list' : 'grid';
$first_batch = 8;                                   // posts printed by PHP; JS loads more from feed.php

$page_title     = site_name() . ' | Adult blogging community: text, photo, video, quote, link & chat';
$page_desc      = site_name() . ' is an 18+ blogging community. Start unlimited blogs, follow creators, reblog, boost your posts and explore the newest posts first, with no login wall.';
$page_canonical = canonical();
$active_nav     = 'explore';

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/header.php';
?>

  <!-- ============ HERO SLIDER ============ -->
  <section class="hero" id="hero" aria-roledescription="carousel" aria-label="Featured" tabindex="0">
    <div class="slides">
      <?php foreach (hero_slides() as $i => $s): $H = $i === 0 ? 'h1' : 'h2'; ?>
        <article class="slide<?= $i === 0 ? ' active' : '' ?>" style="--c:<?= e($s['color']) ?>;--px:<?= (float)$s['x'] ?>%;--py:<?= (float)$s['y'] ?>%;--s:<?= (float)$s['z'] ?>;--mpx:<?= (float)$s['mx'] ?>%;--mpy:<?= (float)$s['my'] ?>%;--ms:<?= (float)$s['mz'] ?>">
          <img class="bg" src="<?= e(asset($s['img'])) ?>" alt="<?= e($s['alt']) ?>" <?= $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?> decoding="async">
          <div class="hue"></div><div class="shade"></div>
          <div class="copy">
            <<?= $H ?>><?= e($s['title']) ?></<?= $H ?>>
            <p><?= e($s['text']) ?></p>
            <div class="cta">
              <?php foreach ($s['cta'] as $b): ?>
                <a class="btn <?= e($b['class']) ?>" href="<?= e($b['href']) ?>"<?= !empty($b['modal']) ? ' data-modal="' . e($b['modal']) . '"' : '' ?>><?= e($b['label']) ?></a>
              <?php endforeach; ?>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <div class="controls">
      <button class="sq" id="prev" type="button" aria-label="Previous slide"><?= icon('left') ?></button>
      <div class="dots" role="tablist" aria-label="Choose slide">
        <?php foreach (hero_slides() as $i => $s): ?>
          <button class="dot<?= $i === 0 ? ' active' : '' ?>" type="button" role="tab" aria-label="Slide <?= $i + 1 ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"><i></i></button>
        <?php endforeach; ?>
      </div>
      <button class="sq" id="next" type="button" aria-label="Next slide"><?= icon('right') ?></button>
    </div>
  </section>

  <div class="layout">
    <main class="main" id="main">

      <!-- Featured blogs -->
      <section class="block" aria-labelledby="feat-h">
        <div class="block-head"><h2 id="feat-h">Featured blogs</h2><a href="<?= e(url('trending.php')) ?>">See all</a></div>
        <div class="featured">
          <?php foreach (featured_blogs() as $b): ?>
            <a class="fb" href="<?= e($b['href']) ?>" style="--c:<?= e($b['color']) ?>">
              <span class="fb-cover crop" style="<?= e(crop_style($b)) ?>"><img src="<?= e(media_url($b['img'])) ?>" alt="" loading="lazy" decoding="async"></span>
              <span class="fb-av"><?= e(initial($b['name'])) ?></span>
              <b><?= e($b['name']) ?> <?= badge($b['badge']) ?></b>
              <small><?= e($b['tagline']) ?></small>
            </a>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- Newest posts -->
      <section class="block" aria-labelledby="feed-h">
        <div class="block-head">
          <h2 id="feed-h">Newest posts</h2>
          <div class="view-toggle" role="group" aria-label="Post layout">
            <button type="button" data-view="grid" class="<?= $view === 'grid' ? 'is-on' : '' ?>" aria-pressed="<?= $view === 'grid' ? 'true' : 'false' ?>" aria-label="Grid view" title="Grid view"><?= icon('grid') ?><span>Grid</span></button>
            <button type="button" data-view="list" class="<?= $view === 'list' ? 'is-on' : '' ?>" aria-pressed="<?= $view === 'list' ? 'true' : 'false' ?>" aria-label="List view" title="List view"><?= icon('list') ?><span>List</span></button>
          </div>
        </div>
        <p class="hint">Newest first. Keep scrolling for older posts.</p>

        <div class="filters" role="toolbar" aria-label="Filter posts by format">
          <a class="chip<?= $type === 'all' ? ' is-on' : '' ?>" href="<?= e(url()) ?>" data-f="all">All</a>
          <?php foreach (post_types() as $key => [$label, $ic]): ?>
            <a class="chip<?= $type === $key ? ' is-on' : '' ?>" href="<?= e(url('?type=' . $key)) ?>" data-f="<?= e($key) ?>"><?= icon($ic) ?><?= e($label) ?></a>
          <?php endforeach; ?>
        </div>

        <div class="feed" id="feed" data-view="<?= e($view) ?>" data-type="<?= e($type) ?>" data-next="<?= $first_batch ?>" aria-live="polite">
          <?php foreach (feed_posts(0, $first_batch, $type) as $p) { include __DIR__ . '/includes/card.php'; } ?>
        </div>
        <div class="sentinel" id="sentinel"><span class="loader" aria-hidden="true"></span><span class="s-text">Loading older posts</span></div>
      </section>
    </main>

    <!-- ============ RIGHT RAIL ============ -->
    <aside class="rail" aria-label="Discover">
      <section class="box">
        <h3>Trending tags</h3>
        <div class="tags">
          <?php foreach (array_slice(trending_tags(), 0, 10) as [$tag]): ?><a href="#">#<?= e($tag) ?></a><?php endforeach; ?>
        </div>
      </section>

      <section class="box">
        <h3>Blogs to follow</h3>
        <ul class="people">
          <?php foreach (suggested_blogs() as $b): ?>
            <li><span class="av" style="--c:<?= e($b['color']) ?>"><?= e(initial($b['name'])) ?></span><div><b><?= e($b['name']) ?></b><small><?= e($b['note']) ?></small></div><button class="follow<?= (is_logged_in() && blog_followed_by((int)current_user()['id'], $b['id'])) ? ' on' : '' ?>" type="button" data-blog="<?= e($b['name']) ?>"><?= (is_logged_in() && blog_followed_by((int)current_user()['id'], $b['id'])) ? 'Following' : 'Follow' ?></button></li>
          <?php endforeach; ?>
        </ul>
      </section>

      <section class="box promo">
        <h3>Get your posts seen</h3>
        <p>Spend points on views or book a flat-rate spot on Explore.</p>
        <a class="btn btn-fill" href="<?= e(url('advertising.php')) ?>">Advertise</a>
      </section>

      <section class="box badges">
        <h3>Badges</h3>
        <ul>
          <?php foreach (badge_legend() as $b): ?><li><?= badge($b['badge']) ?><?= e($b['text']) ?></li><?php endforeach; ?>
        </ul>
      </section>
    </aside>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
