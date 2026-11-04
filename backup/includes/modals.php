<?php
$host = (string)parse_url(SITE_URL, PHP_URL_HOST);
$google = GOOGLE_LOGIN_ENABLED ? '
      <div class="or"><span>or</span></div>
      <a class="btn btn-google" href="' . e(url('auth/google.php')) . '">
        <svg viewBox="0 0 48 48" width="18" height="18" aria-hidden="true"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
        Continue with Google
      </a>' : '';
$flash = $_SESSION['flash'] ?? [];   // e.g. $_SESSION['flash']['login'] = 'Wrong email or password.'
unset($_SESSION['flash']);
?>

<!-- ============ LOGIN ============ -->
<div class="modal" data-name="login" role="dialog" aria-modal="true" aria-labelledby="m-login-h" hidden>
  <div class="m-box">
    <button class="m-close" data-close aria-label="Close"><svg class="ic"><use href="#i-x"/></svg></button>
    <h2 id="m-login-h">Sign in</h2>
    <p class="m-sub">Welcome back. Your blogs are waiting.</p>
    <form method="post" action="<?= e(url('auth/login.php')) ?>" novalidate>
      <?= csrf_field() ?>
      <p class="form-msg" role="alert" <?= empty($flash['login']) ? 'hidden' : '' ?>><?= e($flash['login'] ?? '') ?></p>
      <label class="field"><span>Email or username</span>
        <input type="text" name="login" autocomplete="username" required></label>
      <label class="field"><span>Password</span>
        <span class="pw"><input type="password" name="password" autocomplete="current-password" required>
        <button type="button" class="pw-toggle" aria-label="Show password"><svg class="ic"><use href="#i-eye"/></svg></button></span></label>
      <div class="row-between">
        <label class="check"><input type="checkbox" name="remember" value="1"><span>Keep me signed in</span></label>
        <a class="lnk" href="<?= e(url('forgot.php')) ?>" data-modal="forgot">Forgot password?</a>
      </div>
      <?= turnstile_widget() ?>
      <button class="btn btn-fill btn-wide" type="submit">Sign in</button>
      <?= $google ?>
    </form>
    <p class="m-alt">New here? <a class="lnk" href="<?= e(url('register.php')) ?>" data-modal="register">Create an account</a></p>
  </div>
</div>

<!-- ============ REGISTER ============ -->
<div class="modal" data-name="register" role="dialog" aria-modal="true" aria-labelledby="m-reg-h" hidden>
  <div class="m-box">
    <button class="m-close" data-close aria-label="Close"><svg class="ic"><use href="#i-x"/></svg></button>
    <h2 id="m-reg-h">Create your blog</h2>
    <p class="m-sub">Your first blog is also your profile. Add more blogs any time.</p>
    <form method="post" action="<?= e(url('auth/register.php')) ?>" novalidate>
      <?= csrf_field() ?>
      <input type="hidden" name="ref" value="<?= e((string)($_GET['ref'] ?? ($_SESSION['ref'] ?? ''))) ?>">
      <p class="form-msg" role="alert" <?= empty($flash['register']) ? 'hidden' : '' ?>><?= e($flash['register'] ?? '') ?></p>
      <label class="field"><span>Blog name</span>
        <span class="suffix"><input type="text" name="username" autocomplete="username" pattern="[a-zA-Z0-9\-]{3,30}" required><em>.<?= e($host) ?></em></span></label>
      <label class="field"><span>Email</span>
        <input type="email" name="email" autocomplete="email" required></label>
      <label class="field"><span>Password</span>
        <span class="pw"><input type="password" name="password" autocomplete="new-password" minlength="8" required>
        <button type="button" class="pw-toggle" aria-label="Show password"><svg class="ic"><use href="#i-eye"/></svg></button></span></label>
      <label class="check"><input type="checkbox" name="age18" value="1" required><span>I am 18 or older</span></label>
      <label class="check"><input type="checkbox" name="terms" value="1" required><span>I accept the <a class="lnk" href="<?= e(url('terms.php')) ?>" target="_blank" rel="noopener">Terms</a> and <a class="lnk" href="<?= e(url('privacy.php')) ?>" target="_blank" rel="noopener">Privacy Policy</a></span></label>
      <?= turnstile_widget() ?>
      <button class="btn btn-fill btn-wide" type="submit">Sign up</button>
      <?= $google ?>
    </form>
    <p class="m-alt">Already have an account? <a class="lnk" href="<?= e(url('login.php')) ?>" data-modal="login">Sign in</a></p>
  </div>
</div>

<!-- ============ FORGOT PASSWORD ============ -->
<div class="modal" data-name="forgot" role="dialog" aria-modal="true" aria-labelledby="m-forgot-h" hidden>
  <div class="m-box">
    <button class="m-close" data-close aria-label="Close"><svg class="ic"><use href="#i-x"/></svg></button>
    <h2 id="m-forgot-h">Reset your password</h2>
    <p class="m-sub">Enter your account email and we will send you a reset link.</p>
    <form method="post" action="<?= e(url('auth/forgot.php')) ?>" novalidate>
      <?= csrf_field() ?>
      <p class="form-msg" role="alert" <?= empty($flash['forgot']) ? 'hidden' : '' ?>><?= e($flash['forgot'] ?? '') ?></p>
      <label class="field"><span>Email</span>
        <input type="email" name="email" autocomplete="email" required></label>
      <?= turnstile_widget() ?>
      <button class="btn btn-fill btn-wide" type="submit">Send reset link</button>
    </form>
    <p class="m-alt"><a class="lnk" href="<?= e(url('login.php')) ?>" data-modal="login">Back to sign in</a></p>
  </div>
</div>
