<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page_title) ?></title>
<meta name="description" content="<?= e($page_desc) ?>">
<link rel="canonical" href="<?= e($page_canonical) ?>">
<?php if (!empty($page_noindex)): ?><meta name="robots" content="noindex,nofollow"><?php endif; ?>
<meta name="theme-color" content="#07030d">
<meta name="rta" content="RTA-5042-1996-1400-1577-RTA">

<!-- Open Graph / social -->
<meta property="og:type" content="website">
<meta property="og:title" content="<?= e($page_title) ?>">
<meta property="og:description" content="<?= e($page_desc) ?>">
<meta property="og:image" content="<?= e(SITE_URL . asset('hero.jpg')) ?>">

<!-- Favicons -->
<link rel="icon" type="image/png" sizes="32x32" href="<?= site_favicon_url('32') ?>">
<link rel="icon" type="image/png" sizes="192x192" href="<?= site_favicon_url('192') ?>">
<link rel="apple-touch-icon" href="<?= site_favicon_url('apple') ?>">

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@500;700;900&family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="<?= url('css/style.css') ?>">
<?= site_color_vars() ?>

<?php /* Sitewide <head> code saved in Admin > Settings (Google Analytics, verification meta tags, etc.) */ echo setting('head_code', ''); ?>
</head>
<body>
