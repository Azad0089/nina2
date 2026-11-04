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
      <label class="field"><span>Blog Subdomain</span>
        <span class="suffix"><input type="text" name="username" id="regUsername" autocomplete="username" pattern="[a-zA-Z0-9_\-]{3,30}" placeholder="my-blog" value="{{ old('username') }}" required oninput="this.value=this.value.toLowerCase().replace(/[^a-z0-9_-]/g,'');"><em>.{{ str_replace(['https://', 'http://'], '', site_name()) }}</em></span></label>
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
    <p class="m-sub">Enter your account email or username and we will send a reset link to your mail inbox.</p>
    <form method="post" action="{{ route('auth.forgot') }}">
      @csrf
      @if(session('success'))
        <p class="form-msg ok" role="alert">{!! session('success') !!}</p>
      @elseif(session('error'))
        <p class="form-msg" role="alert">{{ session('error') }}</p>
      @endif
      <label class="field"><span>Email or Username</span>
        <input type="text" name="email" autocomplete="username email" placeholder="admin or you@example.com" required></label>

      <!-- Cloudflare Turnstile CAPTCHA -->
      <div class="cf-turnstile" data-sitekey="{{ $siteSettings['turnstile_site_key'] ?? '0x4AAAAAAE8QOvWsSh5TL-MQ' }}" style="margin:10px 0;"></div>

      <button class="btn btn-fill btn-wide" type="submit">Send reset link to email</button>
    </form>
    <p class="m-alt"><a class="lnk" href="{{ route('login') }}" data-modal="login">Back to sign in</a></p>
  </div>
</div>

<!-- ============ REPORT CONTENT MODAL ============ -->
<div class="modal" data-name="report" role="dialog" aria-modal="true" aria-labelledby="m-report-h" hidden>
  <div class="m-box">
    <button class="m-close" data-close aria-label="Close"><svg class="ic"><use href="#i-x"/></svg></button>
    <h2 id="m-report-h" style="color:#f43f5e;display:flex;align-items:center;gap:8px;">
      <svg class="ic" style="stroke:#f43f5e;width:1.3rem;height:1.3rem;"><use href="#i-flag"/></svg>
      Report Content
    </h2>
    <p class="m-sub">Flag inappropriate content, copyright infringement, or safety violations for administrative review.</p>
    
    <form method="post" action="{{ route('action.report') }}" id="reportModalForm">
      @csrf
      <input type="hidden" name="target_type" id="reportTargetType" value="post">
      <input type="hidden" name="target_id" id="reportTargetId" value="">

      <label class="field">
        <span>Reason for Report</span>
        <select name="reason" id="reportReasonSelect" required style="width:100%;padding:10px;background:var(--ink);color:var(--text);border:1px solid var(--line);border-radius:4px;">
          <option value="Inappropriate / Non-consensual content">Inappropriate / Non-consensual content</option>
          <option value="Underage / CSAM concern">Underage / CSAM concern</option>
          <option value="Copyright / DMCA violation">Copyright / DMCA violation</option>
          <option value="Harassment or Hate Speech">Harassment or Hate Speech</option>
          <option value="Spam / Scams / Phishing">Spam / Scams / Phishing</option>
          <option value="Other Policy Violation">Other Policy Violation</option>
        </select>
      </label>

      <label class="field">
        <span>Additional Details / Clarification (Optional)</span>
        <textarea name="details" id="reportDetailsInput" rows="3" placeholder="Provide extra context, links, or explanation..." style="width:100%;padding:10px;background:var(--ink);color:var(--text);border:1px solid var(--line);border-radius:4px;"></textarea>
      </label>

      <button class="btn btn-fill btn-wide" type="submit" style="background:#e11d48;border-color:#e11d48;color:#fff;font-weight:700;">
        Submit Report to Moderation
      </button>
    </form>
  </div>
</div>

<!-- ============ CONTACT SUPPORT MODAL ============ -->
<div class="modal" data-name="support" role="dialog" aria-modal="true" aria-labelledby="m-support-h" hidden>
  <div class="m-box">
    <button class="m-close" data-close aria-label="Close"><svg class="ic"><use href="#i-x"/></svg></button>
    <h2 id="m-support-h" style="color:var(--cyan);display:flex;align-items:center;gap:8px;">
      <svg class="ic" style="stroke:var(--cyan);width:1.3rem;height:1.3rem;"><use href="#i-mail"/></svg>
      Contact Admin Support
    </h2>
    <p class="m-sub">Send a direct message or inquiry to our administration and support desk.</p>

    <form method="post" action="{{ route('action.contact') }}">
      @csrf
      <label class="field"><span>Your Name</span>
        <input type="text" name="name" value="{{ auth()->check() ? auth()->user()->username : '' }}" required></label>
      <label class="field"><span>Your Email</span>
        <input type="email" name="email" value="{{ auth()->check() ? auth()->user()->email : '' }}" required></label>
      <label class="field"><span>Topic</span>
        <select name="topic" style="width:100%;padding:10px;background:var(--ink);color:var(--text);border:1px solid var(--line);border-radius:4px;">
          <option value="General Support">General Support</option>
          <option value="Account / Login Issue">Account / Login Issue</option>
          <option value="Crypto Payment / Premium">Crypto Payment / Premium</option>
          <option value="ID Verification Request">ID Verification Request</option>
          <option value="Feature Suggestion">Feature Suggestion</option>
          <option value="Report Bug">Report Bug</option>
        </select>
      </label>
      <label class="field"><span>Message</span>
        <textarea name="message" rows="4" placeholder="How can our admin team help you?" required style="width:100%;padding:10px;background:var(--ink);color:var(--text);border:1px solid var(--line);border-radius:4px;"></textarea>
      </label>
      <button class="btn btn-fill btn-wide" type="submit">Submit Support Ticket</button>
    </form>
  </div>
</div>

<!-- ============ IN-PAGE COMMENTS MODAL ============ -->
<div class="modal" data-name="comments" id="commentsModal" role="dialog" aria-modal="true" aria-labelledby="m-comments-h" hidden>
  <div class="m-box" style="max-width:580px;max-height:85vh;display:flex;flex-direction:column;">
    <button class="m-close" data-close aria-label="Close"><svg class="ic"><use href="#i-x"/></svg></button>
    
    <div style="padding-bottom:12px;border-bottom:1px solid var(--line);margin-bottom:12px;">
      <h2 id="m-comments-h" style="font:800 20px var(--f-display);display:flex;align-items:center;gap:10px;color:#fff;">
        <svg class="ic" style="stroke:var(--pink);width:1.3rem;height:1.3rem;"><use href="#i-chat"/></svg>
        Comments &amp; Discussion
      </h2>
      <p class="m-sub" id="commentsPostInfo" style="margin:4px 0 0;font-size:13px;color:var(--mute);"></p>
    </div>

    <!-- Comments List Container -->
    <div id="commentsList" style="flex:1;overflow-y:auto;min-height:160px;max-height:360px;display:flex;flex-direction:column;gap:12px;padding-right:6px;margin-bottom:16px;">
      <div id="commentsLoading" style="text-align:center;padding:30px;color:var(--mute);">
        <span class="loader" style="display:inline-block;margin-bottom:8px;"></span>
        <p style="font-size:13px;">Loading comments...</p>
      </div>
      <div id="commentsEmpty" style="display:none;text-align:center;padding:30px 10px;color:var(--mute);font-size:13px;background:rgba(255,255,255,0.02);border:1px dashed var(--line);border-radius:4px;">
        No comments yet. Be the first to start the discussion!
      </div>
      <div id="commentsEntries" style="display:flex;flex-direction:column;gap:10px;"></div>
    </div>

    <!-- New Comment Form -->
    <div style="border-top:1px solid var(--line);padding-top:14px;">
      @auth
        <form id="inlineCommentForm" method="post" action="{{ route('action.comment') }}" style="display:flex;flex-direction:column;gap:10px;">
          @csrf
          <input type="hidden" name="post_id" id="commentPostId" value="">
          <div style="display:flex;gap:10px;align-items:flex-start;">
            <span class="av" style="--c:var(--pink);width:34px;height:34px;font-size:13px;font-weight:700;flex-shrink:0;">{{ initial(auth()->user()->username) }}</span>
            <div style="flex:1;position:relative;">
              <textarea name="comment" id="commentInput" rows="2" placeholder="Write a thoughtful comment..." required style="width:100%;padding:10px 12px;background:var(--ink);color:var(--text);border:1px solid var(--line);border-radius:4px;font-size:13.5px;resize:vertical;min-height:56px;line-height:1.5;"></textarea>
            </div>
          </div>
          <div style="display:flex;justify-content:space-between;align-items:center;">
            <small style="color:var(--mute);font-size:12px;">Posting as <b>{{ auth()->user()->username }}</b></small>
            <button type="submit" id="commentSubmitBtn" class="btn btn-fill" style="padding:8px 18px;font-size:13px;font-weight:700;">
              Post Comment
            </button>
          </div>
        </form>
      @else
        <div style="padding:14px;background:rgba(255,255,255,0.03);border:1px solid var(--line);border-radius:4px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
          <div style="font-size:13px;color:var(--text);">
            <b>Join the discussion</b>
            <p style="color:var(--mute);font-size:12px;margin:2px 0 0;">Sign in to like, reblog, and leave comments on posts.</p>
          </div>
          <a href="{{ route('login') }}" data-modal="login" class="btn btn-fill" style="padding:8px 16px;font-size:13px;white-space:nowrap;">Sign in</a>
        </div>
      @endauth
    </div>
  </div>
</div>

