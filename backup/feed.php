<?php
/**
 * Returns a batch of post cards as an HTML fragment. Used by the infinite scroll in js/main.js:
 *   feed.php?offset=8&limit=6&type=photo
 */
require __DIR__ . '/includes/config.php';

$offset = max(0, (int)($_GET['offset'] ?? 0));
$limit  = min(24, max(1, (int)($_GET['limit'] ?? 6)));
$type   = valid_type($_GET['type'] ?? null);

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store');
foreach (feed_posts($offset, $limit, $type) as $p) {
    include __DIR__ . '/includes/card.php';
}
