<?php
require_once __DIR__ . '/../includes/config.php';
$me = require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valid()) { exit('Invalid request'); }
$id = (int)($_POST['id'] ?? 0);
$stmt = db()->prepare('DELETE p FROM posts p JOIN blogs b ON b.id = p.blog_id WHERE p.id = ? AND b.user_id = ?');
$stmt->execute([$id, $me['id']]);
db()->prepare('DELETE FROM post_tags WHERE post_id = ?')->execute([$id]);
db()->prepare('DELETE FROM likes WHERE post_id = ?')->execute([$id]);
$_SESSION['flash']['app'] = ['type' => 'ok', 'text' => 'Post deleted.'];
header('Location: ' . url('dashboard/posts.php'));
exit;
