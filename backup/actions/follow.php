<?php
require __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');
if (!is_logged_in()) { http_response_code(401); echo json_encode(['ok' => false, 'message' => 'Please sign in first.']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valid()) { http_response_code(400); echo json_encode(['ok' => false]); exit; }
$slug = trim((string)($_POST['blog'] ?? ''));
$stmt = db()->prepare('SELECT id FROM blogs WHERE slug = ? AND status = "active"');
$stmt->execute([$slug]);
$blogId = (int)$stmt->fetchColumn();
if (!$blogId) { http_response_code(404); echo json_encode(['ok' => false, 'message' => 'Blog not found.']); exit; }
$on = toggle_follow((int)current_user()['id'], $blogId);
echo json_encode(['ok' => true, 'following' => $on]);
