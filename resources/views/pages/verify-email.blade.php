@extends('layouts.app')

@section('title', 'Verify Your Email Address | ' . site_name())

@section('content')
  @include('partials.header')

  <main class="page" id="main">
    <section class="page-hero" style="--c:var(--cyan)">
      <nav class="crumbs" aria-label="Breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        <span aria-hidden="true">/</span>
        <span>Email Verification</span>
      </nav>
      <h1>Verify Your Email Address</h1>
      <p>Confirm your email to complete registration and activate your creator blogs.</p>
    </section>

    <div class="panel" style="max-width:520px;margin:24px auto;padding:32px 28px;background:var(--paper);border:1px solid var(--line);border-radius:12px;box-shadow:0 12px 35px rgba(0,0,0,0.5);">
      <div style="text-align:center;margin-bottom:24px;">
        <div style="width:64px;height:64px;border-radius:50%;background:rgba(25,227,255,0.12);border:2px solid var(--cyan);display:inline-flex;align-items:center;justify-content:center;margin-bottom:14px;color:var(--cyan);">
          <svg style="width:32px;height:32px;stroke:currentColor;fill:none;stroke-width:2;"><use href="#i-mail"/></svg>
        </div>
        <h2 style="font:800 22px var(--f-display);color:#fff;margin:0 0 8px;">Check Your Mailbox</h2>
        <p style="font-size:14px;color:var(--mute);line-height:1.6;margin:0;">
          A verification link has been sent to:
          @if(!empty($maskedEmail))
            <br><strong style="color:var(--cyan);font-size:15px;">{{ $maskedEmail }}</strong>
          @endif
        </p>
      </div>

      @if(session('success'))
        <div class="form-msg ok" style="margin-bottom:20px;padding:12px 14px;border-radius:6px;">
          {!! session('success') !!}
        </div>
      @endif

      @if(session('error'))
        <div class="form-msg" style="margin-bottom:20px;padding:12px 14px;border-radius:6px;">
          {{ session('error') }}
        </div>
      @endif

      <div style="background:rgba(255,255,255,0.02);border:1px solid var(--line);border-radius:8px;padding:16px;margin-bottom:24px;font-size:13px;line-height:1.6;color:var(--text);">
        Click the link in the email to activate your account. If you don't receive the email within a couple of minutes, please check your spam or junk folder.
      </div>

      <form method="post" action="{{ route('verification.send') }}" style="display:flex;flex-direction:column;gap:14px;">
        @csrf
        <input type="hidden" name="email" value="{{ $email ?? session('unverified_email') ?? '' }}">
        @if(empty($email) && !session('unverified_email'))
          <label class="field">
            <span>Account Email Address</span>
            <input type="email" name="email" required placeholder="you@example.com">
          </label>
        @endif
        <button type="submit" class="btn btn-fill btn-wide" style="padding:12px;font-weight:700;">
          Resend Verification Email
        </button>
      </form>

      <div style="display:flex;align-items:center;justify-content:space-between;margin-top:24px;padding-top:16px;border-top:1px solid var(--line);font-size:13px;">
        <a class="lnk" href="{{ route('login') }}" data-modal="login">Back to Sign in</a>
        <a class="lnk" href="{{ route('logout') }}" style="color:var(--mute);">Sign Out</a>
      </div>
    </div>
  </main>
@endsection
