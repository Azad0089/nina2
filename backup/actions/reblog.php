<?php
require __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');
if (!is_logged_in()) { http_response_code(401); echo json_encode(['ok' => false, 'message' => 'Please sign in first.']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valid()) { http_response_code(400); echo json_encode(['ok' => false]); exit; }
$postId = (int)($_POST['post_id'] ?? 0);
$res = reblog_post((int)current_user()['id'], $postId);
echo json_encode($res);
