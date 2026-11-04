<?php
require __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valid()) {
    http_response_code(400);
    exit('Invalid request');
}

$name    = trim((string)($_POST['name'] ?? ''));
$email   = trim((string)($_POST['email'] ?? ''));
$topic   = (string)($_POST['topic'] ?? 'support');
$link    = trim((string)($_POST['link'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($message) < 10) {
    $_SESSION['flash']['contact'] = ['type' => 'error', 'text' => 'Please fill in your name, a valid email and a message (at least 10 characters).'];
    header('Location: ' . url('contact.php?topic=' . urlencode($topic)));
    exit;
}

if (!turnstile_passed()) {
    $_SESSION['flash']['contact'] = ['type' => 'error', 'text' => 'Captcha check failed. Please try again.'];
    header('Location: ' . url('contact.php?topic=' . urlencode($topic)));
    exit;
}

db()->prepare('INSERT INTO support_tickets (name, email, topic, link, message) VALUES (?, ?, ?, ?, ?)')
    ->execute([$name, $email, $topic, $link !== '' ? $link : null, $message]);

if (str_starts_with($topic, 'report') && $link !== '') {
    file_report(is_logged_in() ? (int)current_user()['id'] : null, 'blog', 0, "Report via contact form for {$link}: {$message}");
}

$_SESSION['flash']['contact'] = ['type' => 'ok', 'text' => 'Thanks — your message was received. Our team will follow up by email.'];
header('Location: ' . url('contact.php'));
exit;
