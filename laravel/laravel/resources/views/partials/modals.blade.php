<!-- ============ LOGIN ============ -->
<div class="modal" data-name="login" role="dialog" aria-modal="true" aria-labelledby="m-login-h" hidden>
  <div class="m-box">
    <button class="m-close" data-close aria-label="Close"><svg class="ic"><use href="#i-x"/></svg></button>
    <h2 id="m-login-h">Sign in</h2>
    <p class="m-sub">Welcome back. Your blogs are waiting.</p>

    <!-- Google OAuth Login -->
    <a href="{{ route('auth.google') }}" class="btn btn-line btn-wide" style="display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:14px;">
      <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.7l3.1-3.1C17.3 1.8 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.3l3.7 2.9C6.5 7.3 9 5 12 5z"/><path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.6h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.9z"/><path fill="#FBBC05" d="M5.6 14.8c-.2-.7-.4-1.5-.4-2.3 0-.8.2-1.6.4-2.3L1.9 7.3C.7 9.7 0 12.3 0 15.2c0 2.8.7 5.5 1.9 7.8l3.7-2.9z"/><path fill="#34A853" d="M12 23.5c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2.3-6.4-5.2L1.9 16.5C3.7 20.2 7.5 23.5 12 23.5z"/></svg>
      Continue with Google
    </a>

    <div style="font-size: 11px; color: var(--mute); text-transform: uppercase; text-align:center; margin-bottom: 12px;">&mdash; Or with credentials &mdash;</div>

    <form method="post" action="{{ route('login') }}">
      @csrf
      @if($errors->has('login'))
        <p class="form-msg" role="alert">{{ $errors->first('login') }}</p>
      @endif
      <label class="field"><span>Email or username</span>
        <input type="text" name="login" autocomplete="username" placeholder="admin or username" value="{{ old('login') }}" required></label>
      <label class="field"><span>Password</span>
        <span class="pw"><input type="password" name="password" autocomplete="current-password" placeholder="ChangeMe123!" required>
        <button type="button" class="pw-toggle" aria-label="Show password"><svg class="ic"><use href="#i-eye"/></svg></button></span></label>
      <div class="row-between">
        <label class="check"><input type="checkbox" name="remember" value="1"><span>Keep me signed in</span></label>
        <a class="lnk" href="#forgot" data-modal="forgot">Forgot password?</a>
      </div>

      <!-- Cloudflare Turnstile CAPTCHA -->
      <div class="cf-turnstile" data-sitekey="{{ $siteSettings['turnstile_site_key'] ?? '0x4AAAAAAE8QOvWsSh5TL-MQ' }}" style="margin:10px 0;"></div>

      <button class="btn btn-fill btn-wide" type="submit">Sign in</button>
    </form>
    <p class="m-alt">New here? <a class="lnk" href="{{ route('register') }}" data-modal="register">Create an account</a></p>
  </div>
</div>

<!-- ============ REGISTER ============ -->
<div class="modal" data-name="register" role="dialog" aria-modal="true" aria-labelledby="m-reg-h" hidden>
  <div class="m-box">
    <button class="m-close" data-close aria-label="Close"><svg class="ic"><use href="#i-x"/></svg></button>
    <h2 id="m-reg-h">Create your blog</h2>
    <p class="m-sub">Your first blog is also your profile with bio and website links.</p>

    <!-- Google OAuth Signup -->
    <a href="{{ route('auth.google') }}" class="btn btn-line btn-wide" style="display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:14px;">
      <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.7l3.1-3.1C17.3 1.8 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.3l3.7 2.9C6.5 7.3 9 5 12 5z"/><path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.6h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.9z"/><path fill="#FBBC05" d="M5.6 14.8c-.2-.7-.4-1.5-.4-2.3 0-.8.2-1.6.4-2.3L1.9 7.3C.7 9.7 0 12.3 0 15.2c0 2.8.7 5.5 1.9 7.8l3.7-2.9z"/><path fill="#34A853" d="M12 23.5c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2.3-6.4-5.2L1.9 16.5C3.7 20.2 7.5 23.5 12 23.5z"/></svg>
      Sign up with Google
    </a>

    <div style="font-size: 11px; color: var(--mute); text-transform: uppercase; text-align:center; margin-bottom: 12px;">&mdash; Or with email &mdash;</div>

    <form method="post" action="{{ route('register') }}">
      @csrf
      @if($errors->any())
        <p class="form-msg" role="alert">{{ $errors->first() }}</p>
      @endif
      <label class="field"><span>Blog / Profile Name</span>
        <span class="suffix"><input type="text" name="username" autocomplete="username" pattern="[a-zA-Z0-9\-]{3,30}" placeholder="my-blog" value="{{ old('username') }}" required><em>.{{ str_replace(['https://', 'http://'], '', site_name()) }}</em></span></label>
      <label class="field"><span>Email</span>
        <input type="email" name="email" autocomplete="email" placeholder="you@example.com" value="{{ old('email') }}" required></label>
      <label class="field"><span>Password</span>
        <span class="pw"><input type="password" name="password" autocomplete="new-password" minlength="6" required>
        <button type="button" class="pw-toggle" aria-label="Show password"><svg class="ic"><use href="#i-eye"/></svg></button></span></label>
      <label class="field"><span>Referral Code (Optional)</span>
        <input type="text" name="ref" placeholder="Friend referral code" value="{{ request('ref') }}"></label>
      <label class="check"><input type="checkbox" name="age18" value="1" required checked><span>I am 18 or older</span></label>
      <label class="check"><input type="checkbox" name="terms" value="1" required checked><span>I accept the <a class="lnk" href="{{ route('terms') }}" target="_blank" rel="noopener">Terms</a> and <a class="lnk" href="{{ route('privacy') }}" target="_blank" rel="noopener">Privacy Policy</a></span></label>

      <!-- Cloudflare Turnstile CAPTCHA -->
      <div class="cf-turnstile" data-sitekey="{{ $siteSettings['turnstile_site_key'] ?? '0x4AAAAAAE8QOvWsSh5TL-MQ' }}" style="margin:10px 0;"></div>

      <button class="btn btn-fill btn-wide" type="submit">Sign up &amp; Create Blog</button>
    </form>
    <p class="m-alt">Already have an account? <a class="lnk" href="{{ route('login') }}" data-modal="login">Sign in</a></p>
  </div>
</div>

<!-- ============ FORGOT PASSWORD ============ -->
<div class="modal" data-name="forgot" role="dialog" aria-modal="true" aria-labelledby="m-forgot-h" hidden>
  <div class="m-box">
    <button class="m-close" data-close aria-label="Close"><svg class="ic"><use href="#i-x"/></svg></button>
    <h2 id="m-forgot-h">Reset your password</h2>
    <p class="m-sub">Enter your account email and we will send you a reset link.</p>
    <form method="post" action="{{ route('contact') }}">
      @csrf
      <label class="field"><span>Email</span>
        <input type="email" name="email" autocomplete="email" required></label>

      <!-- Cloudflare Turnstile CAPTCHA -->
      <div class="cf-turnstile" data-sitekey="{{ $siteSettings['turnstile_site_key'] ?? '0x4AAAAAAE8QOvWsSh5TL-MQ' }}" style="margin:10px 0;"></div>

      <button class="btn btn-fill btn-wide" type="submit">Send reset link</button>
    </form>
    <p class="m-alt"><a class="lnk" href="{{ route('login') }}" data-modal="login">Back to sign in</a></p>
  </div>
</div>
