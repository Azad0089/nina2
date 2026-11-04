<?php
require __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valid()) { http_response_code(400); echo json_encode(['ok' => false]); exit; }
$targetType = in_array($_POST['target_type'] ?? 'post', ['post','blog','user'], true) ? $_POST['target_type'] : 'post';
$targetId = (int)($_POST['target_id'] ?? $_POST['post_id'] ?? 0);
$reason = (string)($_POST['reason'] ?? '');
if ($targetId <= 0) { http_response_code(400); echo json_encode(['ok' => false, 'message' => 'Missing target.']); exit; }
file_report(is_logged_in() ? (int)current_user()['id'] : null, $targetType, $targetId, $reason);
echo json_encode(['ok' => true, 'message' => 'Thanks — our moderators will take a look.']);
