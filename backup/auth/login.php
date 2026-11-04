<?php
require __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valid()) {
    http_response_code(400);
    exit('Invalid request');
}

if (!turnstile_passed()) {
    $_SESSION['flash']['login'] = 'Captcha check failed. Please try again.';
    header('Location: ' . url('login.php'));
    exit;
}

$login    = trim((string)($_POST['login'] ?? ''));
$password = (string)($_POST['password'] ?? '');
$remember = !empty($_POST['remember']);

$result = attempt_login($login, $password);
if (!$result['ok']) {
    $_SESSION['flash']['login'] = $result['message'];
    header('Location: ' . url('login.php'));
    exit;
}

log_in_user($result['user'], $remember);
header('Location: ' . ($result['user']['role'] === 'admin' ? url('admin/') : url('dashboard/')));
exit;
