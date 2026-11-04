<?php
require __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valid()) {
    http_response_code(400);
    exit('Invalid request');
}
if (!turnstile_passed()) {
    $_SESSION['flash']['forgot'] = 'Captcha check failed. Please try again.';
    header('Location: ' . url('forgot.php'));
    exit;
}

$email = trim((string)($_POST['email'] ?? ''));
$stmt = db()->prepare('SELECT id, username, email FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

// Always show the same message whether or not the email exists, so the form can't be used to
// discover which emails are registered.
if ($user) {
    $token = bin2hex(random_bytes(32));
    db()->prepare('INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))')
        ->execute([$user['id'], hash('sha256', $token)]);
    $resetUrl = SITE_URL . url('reset-password.php') . '?uid=' . $user['id'] . '&token=' . $token;

    // Best-effort email via PHP's mail(). On most shared hosting this needs the server's mail
    // setup (sendmail/Postfix) to actually deliver; swap in SMTP (e.g. PHPMailer) if mail() bounces.
    @mail($user['email'], 'Reset your ' . site_name() . ' password',
        "Hi {$user['username']},\n\nReset your password here (valid 1 hour):\n{$resetUrl}\n\nIf you didn't request this, you can ignore this email.");
}

$_SESSION['flash']['forgot'] = 'If that email is registered, a reset link is on its way.';
header('Location: ' . url('forgot.php'));
exit;
