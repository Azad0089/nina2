<?php
require __DIR__ . '/../includes/config.php';

if (GOOGLE_CLIENT_ID === '' || GOOGLE_CLIENT_SECRET === '') {
    exit('Google login is not configured.');
}
$state = (string)($_GET['state'] ?? '');
if (empty($_SESSION['google_oauth_state']) || !hash_equals($_SESSION['google_oauth_state'], $state)) {
    $_SESSION['flash']['login'] = 'Google sign-in failed (state mismatch). Please try again.';
    header('Location: ' . url('login.php'));
    exit;
}
unset($_SESSION['google_oauth_state']);

$code = (string)($_GET['code'] ?? '');
if ($code === '') {
    $_SESSION['flash']['login'] = 'Google sign-in was cancelled.';
    header('Location: ' . url('login.php'));
    exit;
}

$redirect = SITE_URL . url('auth/google-callback.php');
$tokenResp = @file_get_contents('https://oauth2.googleapis.com/token', false, stream_context_create(['http' => [
    'method'  => 'POST',
    'header'  => 'Content-Type: application/x-www-form-urlencoded',
    'content' => http_build_query([
        'code' => $code, 'client_id' => GOOGLE_CLIENT_ID, 'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => $redirect, 'grant_type' => 'authorization_code',
    ]),
]]));
$tokens = $tokenResp ? json_decode($tokenResp, true) : null;
if (empty($tokens['access_token'])) {
    $_SESSION['flash']['login'] = 'Google sign-in failed. Please try again or use email/password.';
    header('Location: ' . url('login.php'));
    exit;
}

$infoResp = @file_get_contents('https://www.googleapis.com/oauth2/v3/userinfo', false, stream_context_create(['http' => [
    'header' => 'Authorization: Bearer ' . $tokens['access_token'],
]]));
$info = $infoResp ? json_decode($infoResp, true) : null;
if (empty($info['sub']) || empty($info['email'])) {
    $_SESSION['flash']['login'] = 'Could not read your Google profile. Please try again.';
    header('Location: ' . url('login.php'));
    exit;
}

$pdo = db();
$stmt = $pdo->prepare('SELECT * FROM users WHERE google_id = ? OR email = ? LIMIT 1');
$stmt->execute([$info['sub'], $info['email']]);
$user = $stmt->fetch();

if ($user) {
    if (!$user['google_id']) {
        $pdo->prepare('UPDATE users SET google_id = ? WHERE id = ?')->execute([$info['sub'], $user['id']]);
    }
    if ($user['status'] !== 'active') {
        $_SESSION['flash']['login'] = 'This account is ' . $user['status'] . '.';
        header('Location: ' . url('login.php'));
        exit;
    }
} else {
    // create a new account from the Google profile; username derived from the email, made unique
    $base = slugify(explode('@', $info['email'])[0]);
    $username = $base; $i = 1;
    $chk = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    while (true) {
        $chk->execute([$username]);
        if (!$chk->fetch()) break;
        $username = $base . $i++;
    }
    $result = register_user($username, $info['email'], bin2hex(random_bytes(16)), $_SESSION['ref'] ?? null);
    if (!$result['ok']) {
        $_SESSION['flash']['login'] = $result['message'];
        header('Location: ' . url('login.php'));
        exit;
    }
    $user = $result['user'];
    $pdo->prepare('UPDATE users SET google_id = ? WHERE id = ?')->execute([$info['sub'], $user['id']]);
    unset($_SESSION['ref']);
}

log_in_user($user, true);
header('Location: ' . ($user['role'] === 'admin' ? url('admin/') : url('dashboard/')));
exit;
