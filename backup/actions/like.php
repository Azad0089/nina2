<?php
require __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');
if (!is_logged_in()) { http_response_code(401); echo json_encode(['ok' => false, 'message' => 'Please sign in first.']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valid()) { http_response_code(400); echo json_encode(['ok' => false]); exit; }
$postId = (int)($_POST['post_id'] ?? 0);
$on = toggle_like((int)current_user()['id'], $postId);
$count = (int)db()->query('SELECT COUNT(*) FROM likes WHERE post_id = ' . $postId)->fetchColumn();
echo json_encode(['ok' => true, 'liked' => $on, 'count' => $count]);
