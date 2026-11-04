<?php
require_once __DIR__ . '/../includes/config.php';
$me = require_login();
require __DIR__ . '/_nav.php';
$app_title = 'Account settings'; $app_active = 'account';
$pdo = db();
$errors = []; $ok = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid()) {
    $act = (string)($_POST['act'] ?? '');
    if ($act === 'email') {
        $email = trim((string)($_POST['email'] ?? ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Enter a valid email.'; }
        else {
            $chk = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?'); $chk->execute([$email, $me['id']]);
            if ($chk->fetch()) { $errors[] = 'That email is already in use.'; }
            else { $pdo->prepare('UPDATE users SET email = ? WHERE id = ?')->execute([$email, $me['id']]); $ok = 'Email updated.'; $me['email'] = $email; }
        }
    } elseif ($act === 'password') {
        $cur = (string)($_POST['current_password'] ?? ''); $new = (string)($_POST['new_password'] ?? '');
        if (!password_verify($cur, $me['password_hash'])) { $errors[] = 'Current password is incorrect.'; }
        elseif (strlen($new) < 8) { $errors[] = 'New password must be at least 8 characters.'; }
        else { $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([password_hash($new, PASSWORD_DEFAULT), $me['id']]); $ok = 'Password updated.'; }
    } elseif ($act === 'delete') {
        if (!password_verify((string)($_POST['confirm_password'] ?? ''), $me['password_hash'])) { $errors[] = 'Password did not match. Account not deleted.'; }
        else {
            $pdo->prepare("UPDATE users SET status='banned' WHERE id=?")->execute([$me['id']]); // soft delete: same tier as admin ban, keeps content intact for moderation
            $pdo->prepare("UPDATE blogs SET status='banned' WHERE user_id=?")->execute([$me['id']]);
            log_out_user();
            header('Location: ' . url('/')); exit;
        }
    }
}

require __DIR__ . '/../includes/app-start.php';
?>
<?php if ($errors): ?><p class="app-flash err"><?php foreach ($errors as $er) echo e($er) . '<br>'; ?></p><?php endif; ?>
<?php if ($ok): ?><p class="app-flash"><?= e($ok) ?></p><?php endif; ?>

<div class="app-card">
  <h2>Email</h2>
  <form method="post" class="two">
    <?= csrf_field() ?><input type="hidden" name="act" value="email">
    <label class="field"><span>Email</span><input type="email" name="email" value="<?= e($me['email']) ?>"></label>
    <div style="align-self:end"><button class="btn btn-fill" type="submit">Update email</button></div>
  </form>
</div>
<div class="app-card">
  <h2>Password</h2>
  <form method="post" class="two">
    <?= csrf_field() ?><input type="hidden" name="act" value="password">
    <label class="field"><span>Current password</span><input type="password" name="current_password" required></label>
    <label class="field"><span>New password</span><input type="password" name="new_password" minlength="8" required></label>
    <div><button class="btn btn-fill" type="submit">Update password</button></div>
  </form>
</div>
<div class="app-card">
  <h2>Badges &amp; plan</h2>
  <p>
    <?= badge('pro') ?> Premium <?= $me['badge_pro'] ? '— active' : '— not active' ?><br>
    <?= badge('id') ?> Identity verified <?= $me['badge_id'] ? '— active' : '— not active' ?><br>
    <?= badge('vip') ?> VIP <?= $me['badge_vip'] ? '— active' : '— not active' ?>
  </p>
  <p class="fine">Badges, premium and points are granted by the site admin. See <a class="lnk" href="<?= e(url('premium.php')) ?>">Premium</a> for how to qualify.</p>
</div>
<div class="app-card">
  <h2>Delete account</h2>
  <p class="fine">This deactivates your account and blogs immediately. Content is kept for moderation records; contact support if you want it permanently erased.</p>
  <form method="post" onsubmit="return confirm('This will deactivate your account. Continue?')">
    <?= csrf_field() ?><input type="hidden" name="act" value="delete">
    <label class="field"><span>Confirm password</span><input type="password" name="confirm_password" required style="max-width:320px"></label>
    <button class="btn btn-ghost" type="submit">Delete my account</button>
  </form>
</div>
<?php require __DIR__ . '/../includes/app-end.php'; ?>
