<?php
/**
 * Shared shell for /dashboard and /admin pages.
 * Set before including: $app_title, $app_active (nav key), $app_nav (array), $app_section ('dashboard'|'admin')
 */
$me = current_user();
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($app_title) ?> | <?= e(site_name()) ?></title>
<meta name="robots" content="noindex,nofollow">
<link rel="icon" type="image/png" href="<?= e(site_favicon_url('32')) ?>">
<link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@600;800&family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= url('css/style.css') ?>">
<link rel="stylesheet" href="<?= url('css/app.css') ?>">
<?= site_color_vars() ?>
</head>
<body>
<?php require __DIR__ . '/icons.php'; ?>
<div class="app">
  <aside class="app-side">
    <a class="brand" href="<?= e(url($app_section === 'admin' ? 'admin/' : 'dashboard/')) ?>">
      <img src="<?= e(site_logo_url()) ?>" alt=""><span><?= $app_section === 'admin' ? 'Admin' : 'Dashboard' ?></span>
    </a>
    <nav class="app-nav">
      <?php foreach ($app_nav as $item): ?>
        <a class="<?= ($app_active ?? '') === $item['key'] ? 'on' : '' ?>" href="<?= e($item['href']) ?>"><?= icon($item['icon']) ?><span><?= e($item['label']) ?></span></a>
      <?php endforeach; ?>
      <hr>
      <a href="<?= e(url()) ?>" target="_blank"><?= icon('compass') ?><span>View site</span></a>
      <a href="<?= e(url('logout.php')) ?>"><?= icon('x') ?><span>Log out</span></a>
    </nav>
  </aside>
  <main class="app-main">
    <div class="app-topbar">
      <h1><?= e($app_title) ?></h1>
      <?php if ($me): ?>
        <div class="app-user"><span class="av"><?= e(initial($me['username'])) ?></span> <?= e($me['username']) ?> <?= badge(top_badge($me)) ?></div>
      <?php endif; ?>
    </div>
    <?php if (!empty($_SESSION['flash']['app'])): $f = $_SESSION['flash']['app']; unset($_SESSION['flash']['app']); ?>
      <p class="app-flash <?= ($f['type'] ?? '') === 'error' ? 'err' : '' ?>"><?= e($f['text']) ?></p>
    <?php endif; ?>
