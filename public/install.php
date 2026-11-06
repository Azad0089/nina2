<?php

/**
 * Fresh-install wizard.
 * Open /install.php on a new server. An existing site that already has members is left unchanged.
 */

declare(strict_types=1);

const NINA_TITLE = 'Microblogging Nina';
const NINA_DESCRIPTION = 'Microblogging Platform Nina';

$root = dirname(__DIR__);
$lock = $root . '/storage/framework/installed';

session_name('nina_install');
session_start();

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function env_read(string $path): array
{
    if (!is_file($path)) {
        return [];
    }
    $vars = [];
    foreach (file($path, FILE_IGNORE_NEW_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $vars[trim($key)] = trim($value, " \t\"'");
    }
    return $vars;
}

function env_quote(string $value): string
{
    return '"' . str_replace(['\\', '"', "\n", "\r"], ['\\\\', '\\"', '', ''], $value) . '"';
}

function posted_token_ok(): bool
{
    return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string) $_POST['csrf']);
}

function existing_member_count(string $root): ?int
{
    $env = env_read($root . '/.env');
    if (($env['DB_CONNECTION'] ?? '') !== 'mysql' || empty($env['DB_DATABASE'])) {
        return null;
    }
    try {
        $pdo = new PDO(
            'mysql:host=' . ($env['DB_HOST'] ?? '127.0.0.1') . ';port=' . ($env['DB_PORT'] ?? '3306') . ';dbname=' . $env['DB_DATABASE'] . ';charset=utf8mb4',
            $env['DB_USERNAME'] ?? '',
            $env['DB_PASSWORD'] ?? '',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 4]
        );
        $tables = $pdo->query("SHOW TABLES LIKE 'users'")->fetchAll();
        if (!$tables) {
            return 0;
        }
        return (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    } catch (Throwable $e) {
        return null;
    }
}

function requirements(string $root): array
{
    $checks = [];
    $checks[] = ['PHP 8.2 or newer', PHP_VERSION_ID >= 80200, 'Found ' . PHP_VERSION];
    foreach ([
        'pdo' => 'PDO',
        'pdo_mysql' => 'PDO MySQL',
        'mbstring' => 'Mbstring',
        'openssl' => 'OpenSSL',
        'tokenizer' => 'Tokenizer',
        'xml' => 'XML',
        'ctype' => 'Ctype',
        'json' => 'JSON',
        'fileinfo' => 'Fileinfo',
        'gd' => 'GD (placeholder images)',
    ] as $ext => $label) {
        $checks[] = [$label, extension_loaded($ext), extension_loaded($ext) ? 'Loaded' : 'Missing PHP extension: ' . $ext];
    }
    $checks[] = ['Composer packages', is_file($root . '/vendor/autoload.php'), is_file($root . '/vendor/autoload.php') ? 'vendor/autoload.php is present' : 'Run composer install in the project folder first'];

    $dirs = [
        $root . '/storage',
        $root . '/storage/app',
        $root . '/storage/app/public',
        $root . '/storage/framework',
        $root . '/storage/framework/cache',
        $root . '/storage/framework/cache/data',
        $root . '/storage/framework/sessions',
        $root . '/storage/framework/views',
        $root . '/storage/logs',
        $root . '/bootstrap/cache',
        $root . '/public/assets',
        $root . '/public/assets/install',
        $root . '/public/storage',
        $root . '/public/storage/avatars',
        $root . '/public/storage/covers',
        $root . '/public/uploads',
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $ok = is_dir($dir) && is_writable($dir);
        $checks[] = ['Writable ' . str_replace($root . '/', '', $dir), $ok, $ok ? 'Writable' : 'Create this folder and make it writable'];
    }
    $checks[] = ['Project folder can store .env', is_writable($root) || (is_file($root . '/.env') && is_writable($root . '/.env')), 'The installer writes the .env file here'];

    return $checks;
}

function requirements_pass(array $checks): bool
{
    foreach ($checks as $check) {
        if (!$check[1]) {
            return false;
        }
    }
    return true;
}

function placeholder_image(string $path, int $width, int $height, string $label, array $rgb): void
{
    $image = imagecreatetruecolor($width, $height);
    $background = imagecolorallocate($image, 12, 6, 23);
    $accent = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
    $muted = imagecolorallocate($image, 168, 150, 184);
    imagefilledrectangle($image, 0, 0, $width, $height, $background);
    imagerectangle($image, 10, 10, $width - 11, $height - 11, $accent);
    imagerectangle($image, 14, 14, $width - 15, $height - 15, $accent);
    $font = 5;
    $labelWidth = imagefontwidth($font) * strlen($label);
    imagestring($image, $font, (int) max(8, ($width - $labelWidth) / 2), (int) (($height / 2) - 8), $label, $accent);
    $note = 'PLACEHOLDER';
    $noteWidth = imagefontwidth($font) * strlen($note);
    imagestring($image, $font, (int) max(8, ($width - $noteWidth) / 2), (int) (($height / 2) + 14), $note, $muted);
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    if (str_ends_with(strtolower($path), '.png')) {
        imagepng($image, $path);
    } else {
        imagejpeg($image, $path, 86);
    }
    imagedestroy($image);
}

function hero_slides_for_install(): array
{
    $guestFirst = [
        ['label' => 'Create blog', 'href' => '/register', 'class' => 'btn-fill', 'modal' => 'register'],
        ['label' => 'Explore feed', 'href' => '#feed', 'class' => 'btn-line'],
    ];
    return [
        [
            'title' => 'Follow your desires.',
            'text' => 'An adult microblogging community. Post photos, text, video, quotes and chats. Uncensored, creative & free.',
            'img' => 'install/hero-1.jpg',
            'alt' => 'Platform highlights',
            'color' => '#ff2bd6',
            'x' => 50, 'y' => 30, 'z' => 1.05, 'mx' => 50, 'my' => 25, 'mz' => 1.15,
            'cta' => $guestFirst,
        ],
        [
            'title' => 'Earn & promote with points.',
            'text' => 'Collect points via membership or referrals. Spend points in our ad marketplace to get 1,000 to 100,000 views on your posts.',
            'img' => 'install/hero-2.jpg',
            'alt' => 'Ad Marketplace',
            'color' => '#19e3ff',
            'x' => 50, 'y' => 50, 'z' => 1.1, 'mx' => 50, 'my' => 40, 'mz' => 1.2,
            'cta' => [
                ['label' => 'View Marketplace', 'href' => '/advertising', 'class' => 'btn-fill'],
                ['label' => 'Go Premium', 'href' => '/premium', 'class' => 'btn-line'],
            ],
        ],
        [
            'title' => 'Customized Creator Blogs.',
            'text' => 'Create as many blogs as you want. Each with their own colors, bios, website links, custom avatar and banner styling.',
            'img' => 'install/hero-3.jpg',
            'alt' => 'Customized creator blogs',
            'color' => '#ffd60a',
            'x' => 50, 'y' => 45, 'z' => 1.0, 'mx' => 50, 'my' => 35, 'mz' => 1.1,
            'cta' => [
                ['label' => 'Start Now', 'href' => '/register', 'class' => 'btn-fill', 'modal' => 'register'],
                ['label' => 'Trending Blogs', 'href' => '/trending', 'class' => 'btn-line'],
            ],
        ],
    ];
}

function write_env(string $root, array $db, string $url): void
{
    $key = 'base64:' . base64_encode(random_bytes(32));
    $body = implode("\n", [
        'APP_NAME=' . env_quote(NINA_TITLE),
        'APP_ENV=production',
        'APP_KEY=' . $key,
        'APP_DEBUG=false',
        'APP_TIMEZONE=UTC',
        'APP_URL=' . env_quote(rtrim($url, '/')),
        '',
        'APP_LOCALE=en',
        'APP_FALLBACK_LOCALE=en',
        'APP_FAKER_LOCALE=en_US',
        'APP_MAINTENANCE_DRIVER=file',
        'BCRYPT_ROUNDS=12',
        '',
        'LOG_CHANNEL=stack',
        'LOG_STACK=single',
        'LOG_DEPRECATIONS_CHANNEL=null',
        'LOG_LEVEL=error',
        '',
        'DB_CONNECTION=mysql',
        'DB_HOST=' . env_quote($db['host']),
        'DB_PORT=' . env_quote($db['port']),
        'DB_DATABASE=' . env_quote($db['database']),
        'DB_USERNAME=' . env_quote($db['username']),
        'DB_PASSWORD=' . env_quote($db['password']),
        '',
        'SESSION_DRIVER=file',
        'SESSION_LIFETIME=120',
        'SESSION_ENCRYPT=false',
        'SESSION_PATH=/',
        'SESSION_DOMAIN=null',
        '',
        'BROADCAST_CONNECTION=log',
        'FILESYSTEM_DISK=local',
        'QUEUE_CONNECTION=sync',
        'CACHE_STORE=file',
        'CACHE_PREFIX=',
        '',
        'MAIL_MAILER=log',
        'MAIL_FROM_ADDRESS="hello@example.com"',
        'MAIL_FROM_NAME="${APP_NAME}"',
        '',
    ]) . "\n";
    if (file_put_contents($root . '/.env', $body) === false) {
        throw new RuntimeException('Could not write the .env file.');
    }
}

function finish_install(string $root, string $lock, array $db, array $admin, string $url): void
{
    try {
        $pdo = new PDO(
            'mysql:host=' . $db['host'] . ';port=' . $db['port'] . ';dbname=' . $db['database'] . ';charset=utf8mb4',
            $db['username'],
            $db['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $tables = $pdo->query("SHOW TABLES LIKE 'users'")->fetchAll();
        if ($tables && (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn() > 0) {
            throw new RuntimeException('That database already has members. The installer will not change an existing site.');
        }
    } catch (RuntimeException $e) {
        throw $e;
    } catch (Throwable $e) {
        throw new RuntimeException('Could not inspect the database before install. ' . $e->getMessage());
    }

    write_env($root, $db, $url);

    $assets = $root . '/public/assets/install';
    placeholder_image($assets . '/hero-1.jpg', 1400, 780, 'HERO 1', [255, 43, 214]);
    placeholder_image($assets . '/hero-2.jpg', 1400, 780, 'HERO 2', [25, 227, 255]);
    placeholder_image($assets . '/hero-3.jpg', 1400, 780, 'HERO 3', [255, 214, 10]);
    placeholder_image($assets . '/logo.png', 480, 120, 'LOGO', [255, 43, 214]);
    placeholder_image($assets . '/favicon-32.png', 32, 32, 'N', [255, 43, 214]);
    placeholder_image($assets . '/favicon-192.png', 192, 192, 'N', [255, 43, 214]);
    placeholder_image($root . '/public/storage/avatars/admin-placeholder.png', 320, 320, 'AVATAR', [255, 43, 214]);
    placeholder_image($root . '/public/storage/covers/admin-placeholder.jpg', 1400, 420, 'COVER', [25, 227, 255]);

    require $root . '/vendor/autoload.php';
    $app = require $root . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    $output = new Symfony\Component\Console\Output\BufferedOutput();
    $code = $kernel->call('migrate', ['--force' => true], $output);
    if ($code !== 0) {
        throw new RuntimeException("Database migration failed.\n" . $output->fetch());
    }
    if (function_exists('ensure_private_message_tables')) {
        ensure_private_message_tables();
    }
    if (function_exists('ensure_platform_tables')) {
        ensure_platform_tables();
    }

    $settings = [
        'site_title' => NINA_TITLE,
        'meta_description' => NINA_DESCRIPTION,
        'site_logo' => 'assets/install/logo.png',
        'site_favicon' => 'assets/install/favicon-192.png',
        'enable_adult_warning' => '0',
        'primary_color' => '#ff2bd6',
        'secondary_color' => '#19e3ff',
        'hero_slides' => json_encode(hero_slides_for_install(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'default_locale' => 'en',
        'donations_enabled' => '1',
        'bitcart_enabled' => '0',
        'qr_code_enabled' => '0',
        'smtp_enabled' => '0',
        'email_verification_enabled' => '0',
        'welcome_email_enabled' => '0',
        'admin_notify_signup' => '0',
        'max_image_mb' => '2',
        'max_video_mb' => '50',
        'max_audio_mb' => '20',
        'max_message_file_mb' => '10',
        'max_extra_blogs' => '0',
        'max_user_communities' => '5',
        'member_communities_per_page' => '12',
        'admin_list_per_page' => '25',
        'featured_blogs_max_count' => '6',
        'referral_reward_points' => '100',
        'feed_text_excerpt_limit' => '280',
        'feed_caption_excerpt_limit' => '140',
        'post_max_caption_length' => '450',
        'classifieds_enabled' => '1',
        'classifieds_max_tags' => '30',
        'classifieds_per_page' => '36',
        'classifieds_expire_months' => '12',
        'classifieds_title_min' => '10',
        'classifieds_title_max' => '80',
        'classifieds_body_min' => '30',
        'classifieds_body_max' => '5000',
        'classifieds_tags_min' => '2',
        'classifieds_tags_max' => '6',
        'community_title' => NINA_TITLE . ' Community',
        'community_tagline' => 'The hub for creator discovery and collaborative expression.',
        'community_bio' => "Welcome to the official community.\nJoin, publish community posts, and connect in this shared space.",
        'community_url1' => '',
        'community_url2' => '',
        'community_url3' => '',
        'community_email' => '',
        'smtp_from_name' => NINA_TITLE,
    ];
    foreach ($settings as $key => $value) {
        App\Models\Setting::set($key, $value);
    }

    $email = strtolower($admin['email']);
    $user = App\Models\User::where('email', $email)->orWhere('username', $admin['username'])->first();
    if (!$user) {
        $user = App\Models\User::create([
            'username' => $admin['username'],
            'email' => $email,
            'email_verified_at' => now(),
            'password_hash' => Illuminate\Support\Facades\Hash::make($admin['password']),
            'role' => 'admin',
            'points' => 0,
            'status' => 'active',
            'referral_code' => strtoupper(bin2hex(random_bytes(4))),
            'notify_new_follower' => true,
            'notify_private_message' => true,
        ]);
        App\Models\Blog::create([
            'user_id' => $user->id,
            'slug' => $admin['username'],
            'title' => $admin['username'],
            'tagline' => '',
            'color' => '#ff2bd6',
            'avatar_path' => 'storage/avatars/admin-placeholder.png',
            'cover_path' => 'storage/covers/admin-placeholder.jpg',
            'is_main' => 1,
            'status' => 'active',
            'searchable' => 1,
            'show_profile' => 1,
            'bio' => '',
        ]);
    }

    $dir = dirname($lock);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($lock, 'installed ' . date('c') . "\n");
}

$memberCount = is_file($lock) ? 1 : existing_member_count($root);
$blocked = is_file($lock) || ($memberCount !== null && $memberCount > 0);
$step = (string) ($_POST['step'] ?? $_GET['step'] ?? 'requirements');
$error = '';
$checks = requirements($root);
$ready = requirements_pass($checks);

if (!$blocked && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!posted_token_ok()) {
        $error = 'The form expired. Reload the page and try again.';
    } elseif ($step === 'database') {
        $db = [
            'host' => trim((string) ($_POST['db_host'] ?? '127.0.0.1')),
            'port' => trim((string) ($_POST['db_port'] ?? '3306')),
            'database' => trim((string) ($_POST['db_name'] ?? '')),
            'username' => trim((string) ($_POST['db_user'] ?? '')),
            'password' => (string) ($_POST['db_pass'] ?? ''),
        ];
        $url = trim((string) ($_POST['app_url'] ?? ''));
        $replace = isset($_POST['replace_env']);
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $db['database'])) {
            $error = 'Use a database name made of letters, numbers, and underscores.';
        } elseif (!preg_match('#^https?://#', $url)) {
            $error = 'Site URL should start with http:// or https://.';
        } elseif (is_file($root . '/.env') && existing_member_count($root) === null && !$replace) {
            $error = 'A .env file is already here and its database could not be read. Tick the confirmation box only if this is a new server.';
        } else {
            try {
                $server = new PDO(
                    'mysql:host=' . $db['host'] . ';port=' . $db['port'] . ';charset=utf8mb4',
                    $db['username'],
                    $db['password'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]
                );
                $name = str_replace('`', '', $db['database']);
                $server->exec('CREATE DATABASE IF NOT EXISTS `' . $name . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
                new PDO(
                    'mysql:host=' . $db['host'] . ';port=' . $db['port'] . ';dbname=' . $name . ';charset=utf8mb4',
                    $db['username'],
                    $db['password'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                $_SESSION['db'] = $db;
                $_SESSION['app_url'] = rtrim($url, '/');
                $step = 'admin';
            } catch (Throwable $e) {
                $error = 'Could not connect or create the database. ' . $e->getMessage();
                $step = 'database';
            }
        }
        if ($error !== '') {
            $step = 'database';
        }
    } elseif ($step === 'install') {
        $username = strtolower(trim((string) ($_POST['username'] ?? 'admin')));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['password_confirmation'] ?? '');
        if (!preg_match('/^[a-z0-9_]{3,40}$/', $username)) {
            $error = 'Admin username must be 3 to 40 characters: letters, numbers, and underscores.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Enter a valid admin email.';
        } elseif (strlen($password) < 8) {
            $error = 'Admin password must be at least 8 characters.';
        } elseif (!hash_equals($password, $confirm)) {
            $error = 'The two passwords do not match.';
        } elseif (empty($_SESSION['db']) || empty($_SESSION['app_url'])) {
            $error = 'Database details were lost. Start again from the database step.';
            $step = 'database';
        } else {
            try {
                finish_install($root, $lock, $_SESSION['db'], [
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                ], $_SESSION['app_url']);
                $_SESSION = [];
                $step = 'done';
                $blocked = false;
            } catch (Throwable $e) {
                $error = $e->getMessage();
                $step = 'admin';
            }
        }
        if ($error !== '' && $step !== 'database') {
            $step = 'admin';
        }
    }
}

if (!$ready && $step !== 'requirements') {
    $step = 'requirements';
}

$guessUrl = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$dbSaved = $_SESSION['db'] ?? ['host' => '127.0.0.1', 'port' => '3306', 'database' => '', 'username' => 'root', 'password' => ''];
$urlSaved = $_SESSION['app_url'] ?? $guessUrl;

header('X-Robots-Tag: noindex');
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex">
  <title>Install <?= h(NINA_TITLE) ?></title>
  <style>
    :root { color-scheme: dark; }
    * { box-sizing: border-box; }
    body { margin: 0; font-family: Georgia, "Times New Roman", serif; background: #07030d; color: #f4e9f8; }
    main { max-width: 760px; margin: 0 auto; padding: 32px 18px 64px; }
    h1 { font-size: 32px; margin: 0 0 8px; }
    h2 { font-size: 20px; margin: 0 0 12px; }
    p, li { line-height: 1.5; }
    .muted { color: #b7a4c4; }
    .card { background: #120818; border: 1px solid rgba(255,255,255,.12); border-radius: 16px; padding: 18px; margin-top: 16px; }
    .steps { display: flex; gap: 8px; margin: 18px 0; flex-wrap: wrap; }
    .steps span { padding: 6px 10px; border-radius: 999px; background: #22102e; color: #b7a4c4; font-size: 13px; }
    .steps .on { background: #ff2bd6; color: #1a0014; font-weight: 700; }
    label { display: block; font-size: 13px; margin: 12px 0 4px; color: #f0d4ea; }
    input[type=text], input[type=password], input[type=email], input[type=url] { width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid rgba(255,255,255,.16); background: #07030d; color: white; }
    .row { display: grid; grid-template-columns: 1fr 120px; gap: 10px; }
    button, .btn { display: inline-block; margin-top: 16px; background: #ff2bd6; color: #1a0014; border: 0; border-radius: 10px; padding: 10px 16px; font-weight: 700; text-decoration: none; cursor: pointer; }
    .err { background: rgba(255,70,90,.12); border: 1px solid rgba(255,70,90,.4); color: #ffd0d6; padding: 12px; border-radius: 10px; white-space: pre-wrap; }
    table { width: 100%; border-collapse: collapse; font-size: 14px; }
    td { padding: 8px 4px; border-bottom: 1px solid rgba(255,255,255,.08); vertical-align: top; }
    .ok { color: #7dffa8; }
    .bad { color: #ff8fa0; }
    code { font-family: Consolas, monospace; }
  </style>
</head>
<body>
<main>
  <h1><?= h(NINA_TITLE) ?></h1>
  <p class="muted">A few steps create a blank site: one admin account, placeholder images, and the same hero wording. The 18+ warning is installed turned off.</p>
  <div class="steps">
    <span class="<?= $step === 'requirements' ? 'on' : '' ?>">1. Check</span>
    <span class="<?= $step === 'database' ? 'on' : '' ?>">2. Database</span>
    <span class="<?= $step === 'admin' ? 'on' : '' ?>">3. Admin</span>
    <span class="<?= $step === 'done' ? 'on' : '' ?>">4. Done</span>
  </div>

  <?php if ($error !== ''): ?>
    <div class="err"><?= h($error) ?></div>
  <?php endif; ?>

  <?php if ($blocked): ?>
    <div class="card">
      <h2>This copy is already installed</h2>
      <p>The installer stops when a lock file exists or the database already has members. Nothing was changed.</p>
      <a class="btn" href="./">Open the site</a>
    </div>
  <?php elseif ($step === 'done'): ?>
    <div class="card">
      <h2>Installation is complete</h2>
      <p>The site title is <b><?= h(NINA_TITLE) ?></b> and the description is <b><?= h(NINA_DESCRIPTION) ?></b>. There is one admin user, an empty main blog, three placeholder hero images, and placeholder logo, favicon, avatar, and cover. Hero sentences match the current public homepage. The adult-content warning is disabled and can be turned on later in Admin, Site Settings.</p>
      <p>Delete <code>public/install.php</code> after you sign in.</p>
      <a class="btn" href="./">Open the site</a>
    </div>
  <?php elseif ($step === 'requirements'): ?>
    <div class="card">
      <h2>Server check</h2>
      <table>
        <?php foreach ($checks as $check): ?>
          <tr>
            <td><?= h($check[0]) ?></td>
            <td class="<?= $check[1] ? 'ok' : 'bad' ?>"><?= $check[1] ? 'Ready' : 'Needed' ?></td>
            <td class="muted"><?= h($check[2]) ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
      <?php if ($ready): ?>
        <form method="get">
          <input type="hidden" name="step" value="database">
          <button type="submit">Continue to database</button>
        </form>
      <?php else: ?>
        <p class="bad">Fix the needed items, then reload this page.</p>
      <?php endif; ?>
    </div>
  <?php elseif ($step === 'database'): ?>
    <div class="card">
      <h2>MySQL database</h2>
      <p class="muted">The installer creates the database when the MySQL user is allowed to. Otherwise create an empty database first, then enter it here.</p>
      <form method="post">
        <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
        <input type="hidden" name="step" value="database">
        <div class="row">
          <div>
            <label>Database host</label>
            <input type="text" name="db_host" value="<?= h($dbSaved['host']) ?>" required>
          </div>
          <div>
            <label>Port</label>
            <input type="text" name="db_port" value="<?= h($dbSaved['port']) ?>" required>
          </div>
        </div>
        <label>Database name</label>
        <input type="text" name="db_name" value="<?= h($dbSaved['database']) ?>" required>
        <label>Database user</label>
        <input type="text" name="db_user" value="<?= h($dbSaved['username']) ?>" required>
        <label>Database password</label>
        <input type="password" name="db_pass" value="">
        <label>Site URL</label>
        <input type="url" name="app_url" value="<?= h($urlSaved) ?>" required>
        <?php if (is_file($root . '/.env')): ?>
          <label><input type="checkbox" name="replace_env" value="1"> Replace the existing .env. Use this only on a new server.</label>
        <?php endif; ?>
        <button type="submit">Test database and continue</button>
      </form>
    </div>
  <?php else: ?>
    <div class="card">
      <h2>Administrator</h2>
      <p class="muted">This is the only account created. The site starts with no posts, no other members, and no filled-in content.</p>
      <form method="post">
        <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
        <input type="hidden" name="step" value="install">
        <label>Username</label>
        <input type="text" name="username" value="admin" required>
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <label>Confirm password</label>
        <input type="password" name="password_confirmation" required>
        <button type="submit">Install the blank site</button>
      </form>
    </div>
  <?php endif; ?>
</main>
</body>
</html>
