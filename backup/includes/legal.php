<?php
/**
 * Renders a legal / text document.
 * Expects $doc = ['title', 'lead', 'updated', 'sections' => [['id', 'title', 'body(HTML)'], ...]]
 * Later you can load $doc from the database so the admin can edit these pages.
 */
$hero = ['title' => $doc['title'], 'lead' => $doc['lead'] ?? '', 'color' => $doc['color'] ?? '#ff2bd6'];
?>
<main class="page" id="main">
  <?php require __DIR__ . '/page-hero.php'; ?>

  <div class="doc-layout">
    <nav class="toc" aria-label="On this page">
      <h2>On this page</h2>
      <ol>
        <?php foreach ($doc['sections'] as $s): ?><li><a href="#<?= e($s['id']) ?>"><?= e($s['title']) ?></a></li><?php endforeach; ?>
      </ol>
      <p class="toc-meta">Last updated: <?= e($doc['updated']) ?></p>
    </nav>

    <article class="prose">
      <?php foreach ($doc['sections'] as $i => $s): ?>
        <section id="<?= e($s['id']) ?>">
          <h2><span class="num"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span><?= e($s['title']) ?></h2>
          <?= $s['body'] ?>
        </section>
      <?php endforeach; ?>

      <div class="doc-links">
        <span>More:</span>
        <?php foreach (footer_links() as $l): ?><a href="<?= e($l['href']) ?>"><?= e($l['label']) ?></a><?php endforeach; ?>
      </div>
    </article>
  </div>
</main>
