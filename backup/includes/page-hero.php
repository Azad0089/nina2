<?php
/** Title banner used on inner pages. Expects $hero = ['title' => , 'lead' => , 'color' => optional] */
$hc = $hero['color'] ?? '#ff2bd6';
?>
<section class="page-hero" style="--c:<?= e($hc) ?>">
  <nav class="crumbs" aria-label="Breadcrumb"><a href="<?= e(url()) ?>">Home</a><span aria-hidden="true">/</span><span><?= e($hero['title']) ?></span></nav>
  <h1><?= e($hero['title']) ?></h1>
  <?php if (!empty($hero['lead'])): ?><p><?= e($hero['lead']) ?></p><?php endif; ?>
</section>
