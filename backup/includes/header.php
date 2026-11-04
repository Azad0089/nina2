<?php require __DIR__ . '/icons.php'; ?>
<a class="skip" href="#main">Skip to content</a>

<!-- ============ LEFT SIDEBAR ============ -->
<aside class="side" id="side" aria-label="Site menu">
  <a class="side-logo" href="<?= e(url()) ?>" aria-label="<?= e(site_name()) ?> home">
    <img src="<?= e(site_logo_url()) ?>" alt="<?= e(site_name()) ?>" width="200" height="150">
  </a>

  <nav class="side-nav" aria-label="Main">
    <?php foreach (nav_items() as $item): $on = ($active_nav ?? 'explore') === $item['key']; ?>
      <a href="<?= e($item['href']) ?>" class="nav-link<?= $on ? ' is-active' : '' ?>"<?= $on ? ' aria-current="page"' : '' ?>><?= icon($item['icon']) ?><?= e($item['label']) ?></a>
    <?php endforeach; ?>
  </nav>

  <div class="side-auth">
    <a href="<?= e(url('register.php')) ?>" data-modal="register" class="btn btn-fill"><?= icon('userplus') ?>Sign up</a>
    <a href="<?= e(url('login.php')) ?>" data-modal="login" class="btn btn-line"><?= icon('login') ?>Sign in</a>
  </div>

  <footer class="side-foot">
    <?php foreach (footer_links() as $l): ?><a href="<?= e($l['href']) ?>"><?= e($l['label']) ?></a><?php endforeach; ?>
    <span>&copy; <?= date('Y') ?> <?= e(site_name()) ?> &middot; 18+ only</span>
  </footer>
</aside>
<div class="scrim" id="scrim"></div>

<!-- ============ MAIN ============ -->
<div class="wrap">

  <header class="top">
    <button class="icon-btn menu-btn" id="menuBtn" aria-label="Open menu" aria-controls="side" aria-expanded="false"><?= icon('menu') ?></button>
    <a class="top-logo" href="<?= e(url()) ?>" aria-label="<?= e(site_name()) ?> home"><img src="<?= e(site_favicon_url('192')) ?>" alt="" width="36" height="36"><span><?= e(site_name()) ?></span></a>
    <form class="search" role="search" action="<?= e(url('search.php')) ?>" method="get" onsubmit="return false">
      <?= icon('search') ?>
      <input type="search" name="q" placeholder="Search blogs, tags and posts" aria-label="Search blogs, tags and posts">
    </form>
    <a href="<?= e(url('register.php')) ?>" data-modal="register" class="btn btn-fill top-signup">Sign up</a>
  </header>
