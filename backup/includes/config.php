<?php
declare(strict_types=1);
/**
 * Site-wide settings and small helpers. Load this first on every page:
 *   require __DIR__ . '/includes/config.php';
 *
 * On first run against an empty database, includes/db.php creates every table it
 * needs automatically -- nothing to import by hand. Just create an empty database
 * and fill in the four DB_* constants below.
 */
// --- TEMPORARY: show real PHP errors instead of a blank 500 page. -----------
// Remove these two lines once the site is working -- never leave error display
// on for a live/production site.
ini_set('display_errors', '1');
error_reporting(E_ALL);
// -----------------------------------------------------------------------------

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!empty($_GET['ref']) && preg_match('/^[a-f0-9]{8}$/', (string)$_GET['ref'])) {
    $_SESSION['ref'] = $_GET['ref']; // referral code, picked up by the register form
}

// ---- Server / install settings (these stay in this file, they are not admin-editable) ----
const SITE_URL  = 'https://nina.sucks';        // no trailing slash. Set to your real domain in production.
const BASE_PATH = '';                          // '' when installed in the web root, '/subfolder' otherwise

const CONTACT_EMAIL = 'support@your-domain.com';
const LEGAL_NAME    = 'Your Company Name';    // shown on Terms / Privacy / DMCA pages
const LEGAL_ADDRESS = 'Your street address, city, country';

// Cloudflare Turnstile (leave the site key empty to show a placeholder box instead)
const TURNSTILE_SITE_KEY   = '';
const TURNSTILE_SECRET_KEY = '';
const GOOGLE_LOGIN_ENABLED = true;
const GOOGLE_CLIENT_ID     = '';               // fill in from Google Cloud Console to enable Google sign-in
const GOOGLE_CLIENT_SECRET = '';

// ---- Database (create an empty database; tables are created automatically by this app) ----
const DB_HOST = 'localhost';
const DB_PORT = '3306';
const DB_NAME = 'nina_sucks';
const DB_USER = 'nina_sucks';
const DB_PASS = 'vCsKqYamZx4A';

/** URL to a page: url('login.php') */
function url(string $path = ''): string {
    return BASE_PATH . '/' . ltrim($path, '/');
}
/** URL to a file in /assets: asset('logo.png') */
function asset(string $file): string {
    return url('assets/' . ltrim($file, '/'));
}
/** Escape text for HTML output */
function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
/** CSRF token stored in the session */
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_field(): string {
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}
function csrf_valid(): bool {
    return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string)$_POST['csrf']);
}
/** Cloudflare Turnstile widget (or a visible placeholder while no key is set) */
function turnstile_widget(): string {
    if (TURNSTILE_SITE_KEY !== '') {
        return '<div class="cf-turnstile" data-sitekey="' . e(TURNSTILE_SITE_KEY) . '" data-theme="dark"></div>';
    }
    return '<div class="ts-slot">Cloudflare Turnstile captcha goes here</div>';
}
/** Server-side Turnstile check. Always passes while no secret key is configured (dev mode). */
function turnstile_passed(): bool {
    if (TURNSTILE_SECRET_KEY === '') return true;
    $token = (string)($_POST['cf-turnstile-response'] ?? '');
    if ($token === '') return false;
    $resp = @file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query(['secret' => TURNSTILE_SECRET_KEY, 'response' => $token]),
            'timeout' => 8,
        ],
    ]));
    if ($resp === false) return true; // don't lock everyone out if Cloudflare is unreachable
    $data = json_decode($resp, true);
    return !empty($data['success']);
}

// Order matters: db -> settings/uploads (used by helpers) -> auth -> helpers -> data
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/settings.php';
require_once __DIR__ . '/uploads.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/social.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/data.php';