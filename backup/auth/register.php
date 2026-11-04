<?php
require __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valid()) {
    http_response_code(400);
    exit('Invalid request');
}

if (empty($_POST['age18']) || empty($_POST['terms'])) {
    $_SESSION['flash']['register'] = 'Please confirm you are 18+ and accept the Terms & Privacy Policy.';
    header('Location: ' . url('register.php'));
    exit;
}
if (!turnstile_passed()) {
    $_SESSION['flash']['register'] = 'Captcha check failed. Please try again.';
    header('Location: ' . url('register.php'));
    exit;
}

$username = (string)($_POST['username'] ?? '');
$email    = (string)($_POST['email'] ?? '');
$password = (string)($_POST['password'] ?? '');
$ref      = trim((string)($_POST['ref'] ?? '')) ?: null;

$result = register_user($username, $email, $password, $ref);
if (!$result['ok']) {
    $_SESSION['flash']['register'] = $result['message'];
    header('Location: ' . url('register.php'));
    exit;
}

unset($_SESSION['ref']);
log_in_user($result['user'], true);
header('Location: ' . url('dashboard/?welcome=1'));
exit;
