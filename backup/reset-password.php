<?php
require_once __DIR__ . '/includes/config.php';
$page_title = 'Reset password'; $page_desc = 'Set a new password for your account.'; $page_path = 'reset-password.php';
require __DIR__ . '/includes/page-start.php';

$uid   = (int)($_GET['uid'] ?? $_POST['uid'] ?? 0);
$token = (string)($_GET['token'] ?? $_POST['token'] ?? '');
$error = null; $done = false;

$stmt = db()->prepare('SELECT * FROM password_resets WHERE user_id = ? AND token_hash = ? AND used = 0 AND expires_at > NOW() ORDER BY id DESC LIMIT 1');
$stmt->execute([$uid, hash('sha256', $token)]);
$reset = $stmt->fetch();

if (!$reset) {
    $error = 'This reset link is invalid or has expired. Request a new one from the sign-in form.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid()) {
    $p1 = (string)($_POST['password'] ?? '');
    $p2 = (string)($_POST['password2'] ?? '');
    if (strlen($p1) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($p1 !== $p2) {
        $error = 'Passwords do not match.';
    } else {
        db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([password_hash($p1, PASSWORD_DEFAULT), $uid]);
        db()->prepare('UPDATE password_resets SET used = 1 WHERE id = ?')->execute([$reset['id']]);
        $done = true;
    }
}
$hero = ['title' => 'Reset your password', 'lead' => 'Choose a new password below.', 'color' => '#19e3ff'];
?>
<main class="page" id="main">
  <?php require __DIR__ . '/includes/page-hero.php'; ?>
  <section class="panel" style="max-width:480px;margin:0 auto">
    <?php if ($done): ?>
      <p class="form-msg ok" role="alert">Your password has been updated. <a class="lnk" href="<?= e(url('login.php')) ?>" data-modal="login">Sign in</a>.</p>
    <?php elseif ($error): ?>
      <p class="form-msg" role="alert"><?= e($error) ?></p>
    <?php else: ?>
      <form method="post" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="uid" value="<?= (int)$uid ?>">
        <input type="hidden" name="token" value="<?= e($token) ?>">
        <label class="field"><span>New password</span><input type="password" name="password" minlength="8" required></label>
        <label class="field"><span>Confirm new password</span><input type="password" name="password2" minlength="8" required></label>
        <button class="btn btn-fill btn-wide" type="submit">Update password</button>
      </form>
    <?php endif; ?>
  </section>
</main>
<?php require __DIR__ . '/includes/page-end.php'; ?>
