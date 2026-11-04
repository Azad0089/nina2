<?php
require __DIR__ . '/../includes/config.php';
/**
 * Google OAuth (Authorization Code flow). Fill in GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET in
 * includes/config.php from https://console.cloud.google.com/apis/credentials, and add this exact
 * redirect URI there:  SITE_URL . url('auth/google-callback.php')
 */
if (GOOGLE_CLIENT_ID === '' || GOOGLE_CLIENT_SECRET === '') {
    exit('Google login is not configured yet. Set GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET in includes/config.php.');
}
$state = bin2hex(random_bytes(16));
$_SESSION['google_oauth_state'] = $state;
$redirect = SITE_URL . url('auth/google-callback.php');
$params = http_build_query([
    'client_id'     => GOOGLE_CLIENT_ID,
    'redirect_uri'  => $redirect,
    'response_type' => 'code',
    'scope'         => 'openid email profile',
    'state'         => $state,
    'prompt'        => 'select_account',
]);
header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . $params);
exit;
