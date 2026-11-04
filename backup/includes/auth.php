<?php
declare(strict_types=1);
/** Session / auth helpers. Included by config.php on every page. */

function current_user(): ?array {
    static $user = false; // false = not looked up yet, null = looked up, none found
    if ($user !== false) return $user;
    $user = null;
    if (!empty($_SESSION['uid'])) {
        $stmt = db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$_SESSION['uid']]);
        $row = $stmt->fetch();
        if ($row && $row['status'] === 'active') {
            $user = $row;
        } else {
            // account no longer active (suspended/banned/deleted since login) -> force logout
            unset($_SESSION['uid']);
        }
    }
    return $user;
}
function is_logged_in(): bool { return current_user() !== null; }
function is_admin(): bool { $u = current_user(); return $u !== null && $u['role'] === 'admin'; }

function require_login(): array {
    $u = current_user();
    if (!$u) { header('Location: ' . url('login.php')); exit; }
    return $u;
}
function require_admin(): array {
    $u = require_login();
    if ($u['role'] !== 'admin') { http_response_code(403); exit('Admin access only.'); }
    return $u;
}

/** The signed-in user's main blog (their profile blog). */
function main_blog_of(int $userId): ?array {
    $stmt = db()->prepare('SELECT * FROM blogs WHERE user_id = ? AND is_main = 1 LIMIT 1');
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function log_in_user(array $user, bool $remember = false): void {
    session_regenerate_id(true);
    $_SESSION['uid'] = (int)$user['id'];
    if ($remember) {
        // 30-day persistent cookie carrying the session id, read back by config.php's session_start().
        $params = session_get_cookie_params();
        setcookie(session_name(), session_id(), time() + 60 * 60 * 24 * 30, $params['path'], $params['domain'], $params['secure'], true);
    }
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $ua = substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
    db()->prepare('UPDATE users SET last_login_at = NOW(), last_login_ip = ? WHERE id = ?')->execute([$ip, $user['id']]);
    db()->prepare('INSERT INTO login_logs (user_id, ip, user_agent) VALUES (?, ?, ?)')->execute([$user['id'], $ip, $ua]);
}
function log_out_user(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], true);
    }
    session_destroy();
}

/** username/email + password -> user row, or null. Also blocks suspended/banned accounts with a message. */
function attempt_login(string $login, string $password): array {
    $stmt = db()->prepare('SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1');
    $stmt->execute([$login, $login]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password_hash'])) {
        return ['ok' => false, 'message' => 'Wrong email/username or password.'];
    }
    if ($user['status'] === 'suspended') {
        return ['ok' => false, 'message' => 'This account is suspended. You can appeal from the Contact page.'];
    }
    if ($user['status'] === 'banned') {
        return ['ok' => false, 'message' => 'This account has been banned.'];
    }
    return ['ok' => true, 'user' => $user];
}

function slugify(string $s): string {
    $s = strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9\-]+/', '-', $s) ?? $s;
    $s = trim((string)preg_replace('/-+/', '-', $s), '-');
    return $s !== '' ? $s : 'blog';
}

/** Register a new user + auto-create their main (profile) blog. Returns ['ok'=>bool,'message'=>?,'user'=>?] */
function register_user(string $username, string $email, string $password, ?string $refCode): array {
    $username = trim($username);
    $email    = trim($email);

    if (!preg_match('/^[a-zA-Z0-9\-]{3,30}$/', $username)) {
        return ['ok' => false, 'message' => 'Blog name must be 3-30 letters, numbers or hyphens.'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'message' => 'Please enter a valid email address.'];
    }
    if (strlen($password) < 8) {
        return ['ok' => false, 'message' => 'Password must be at least 8 characters.'];
    }
    $reserved = ['admin','administrator','support','root','api','www','mail','static','assets','blog','dashboard'];
    if (in_array(strtolower($username), $reserved, true)) {
        return ['ok' => false, 'message' => 'That blog name is reserved. Please choose another.'];
    }

    $pdo = db();
    $chk = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
    $chk->execute([$username, $email]);
    if ($chk->fetch()) {
        return ['ok' => false, 'message' => 'That username or email is already taken.'];
    }
    $slugChk = $pdo->prepare('SELECT id FROM blogs WHERE slug = ? LIMIT 1');
    $slugChk->execute([slugify($username)]);
    if ($slugChk->fetch()) {
        return ['ok' => false, 'message' => 'That blog name is already taken.'];
    }

    $referrer = null;
    if ($refCode) {
        $r = $pdo->prepare('SELECT id FROM users WHERE referral_code = ? LIMIT 1');
        $r->execute([$refCode]);
        $referrer = $r->fetch();
    }

    $pdo->beginTransaction();
    try {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $refCodeNew = bin2hex(random_bytes(4));
        $pdo->prepare('INSERT INTO users (username, email, password_hash, referral_code, referred_by) VALUES (?, ?, ?, ?, ?)')
            ->execute([$username, $email, $hash, $refCodeNew, $referrer['id'] ?? null]);
        $userId = (int)$pdo->lastInsertId();

        $pdo->prepare('INSERT INTO blogs (user_id, slug, title, is_main, show_profile, color) VALUES (?, ?, ?, 1, 1, ?)')
            ->execute([$userId, slugify($username), $username, '#ff2bd6']);

        if ($referrer) {
            $pts = setting_int('referral_points', 100);
            $pdo->prepare('UPDATE users SET points = points + ? WHERE id = ?')->execute([$pts, $referrer['id']]);
            $pdo->prepare('INSERT INTO referrals (referrer_user_id, referred_user_id, points_awarded) VALUES (?, ?, ?)')
                ->execute([$referrer['id'], $userId, $pts]);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        return ['ok' => false, 'message' => 'Something went wrong creating your account. Please try again.'];
    }

    $u = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $u->execute([$userId]);
    return ['ok' => true, 'user' => $u->fetch()];
}
