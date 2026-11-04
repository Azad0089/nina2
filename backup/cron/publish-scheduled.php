<?php
/**
 * Publishes any queued post whose scheduled time has arrived. Call this on a schedule:
 *
 *   - cPanel / shared hosting "Cron Jobs" -> run every 5 minutes:
 *       wget -q -O /dev/null "https://yourdomain.com/cron/publish-scheduled.php?key=YOUR_CRON_KEY"
 *     (or curl -s -o /dev/null "...")
 *   - or, if your host gives you SSH/CLI cron instead of URL cron, run this every 5 minutes:
 *       php /path/to/site/cron/publish-scheduled.php --cli
 *
 * Set CRON_KEY below to something random so strangers can't trigger it over HTTP.
 */
require_once __DIR__ . '/../includes/config.php';

const CRON_KEY = 'change-this-to-something-random';

$isCli = PHP_SAPI === 'cli' || in_array('--cli', $argv ?? [], true);
if (!$isCli) {
    if (!hash_equals(CRON_KEY, (string)($_GET['key'] ?? ''))) {
        http_response_code(403);
        exit('Forbidden');
    }
    header('Content-Type: text/plain');
}

$pdo = db();
$due = $pdo->query("SELECT id FROM posts WHERE status = 'scheduled' AND scheduled_at <= NOW()")->fetchAll();
$n = 0;
foreach ($due as $row) {
    $pdo->prepare("UPDATE posts SET status = 'published', published_at = NOW() WHERE id = ?")->execute([$row['id']]);
    $n++;
}
echo "Published {$n} queued post(s) at " . date('Y-m-d H:i:s') . ".\n";
