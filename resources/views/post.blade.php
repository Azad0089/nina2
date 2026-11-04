@extends('layouts.app')

@section('title', $p->heading ?? 'View Post #' . $p->id . ' | ' . site_name())

@section('content')
  @include('partials.header')

  <main class="page" id="main">
    <section class="panel" style="max-width:680px;margin:30px auto;padding:0 14px">
      <nav class="crumbs" style="margin-bottom:16px" aria-label="Breadcrumb">
        <a href="{{ url('/') }}">Home</a>
        <span aria-hidden="true">/</span>
        <a href="{{ blog_url($p->blog ?? $p->blog_id) }}">&#64;{{ $p->blog && $p->blog->user ? ($p->blog->is_main ? $p->blog->user->username : $p->blog->user->username . '/' . $p->blog->slug) : ($p->blog->slug ?? 'anonymous') }}</a>
        <span aria-hidden="true">/</span>
        <span>Post #{{ $p->id }}</span>
      </nav>

      <div class="feed" data-view="list">
        @include('components.card', ['p' => $p, 'isSinglePost' => true])
      </div>

      <!-- ============ COMMENTS SECTION ============ -->
      <section class="comments-section" id="comments" style="margin-top:28px;background:var(--ink);border:1px solid var(--line);border-radius:6px;padding:24px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;border-bottom:1px solid var(--line);padding-bottom:14px;">
          <h2 style="font:700 18px var(--f-display);margin:0;display:flex;align-items:center;gap:8px;">
            {!! icon('chat') !!}
            <span>Comments</span>
            <span style="font-size:13px;padding:2px 8px;border-radius:99px;background:var(--line);color:var(--text);">{{ $p->comments->count() }}</span>
          </h2>
          <small style="color:var(--mute);">Be respectful & adhere to community rules</small>
        </div>

        @if(session('success'))
          <div style="background:rgba(16,185,129,0.2);border:1px solid #10b981;color:#10b981;padding:12px;border-radius:4px;margin-bottom:16px;font-size:14px;">
            {{ session('success') }}
          </div>
        @endif

        <!-- Add Comment Form -->
        @auth
          <form method="POST" action="{{ route('action.comment') }}" style="margin-bottom:28px;" id="commentForm">
            @csrf
            <input type="hidden" name="post_id" value="{{ $p->id }}">
            <div style="display:flex;gap:12px;align-items:flex-start;">
              <span class="av" style="--c:var(--pink);width:36px;height:36px;font-size:14px;font-weight:700;flex-shrink:0;">{{ initial(auth()->user()->username) }}</span>
              <div style="flex:1;">
                <textarea name="comment" rows="3" required placeholder="Write a comment..." style="width:100%;box-sizing:border-box;background:var(--black);border:1px solid var(--line);border-radius:4px;padding:10px 12px;color:var(--text);font-family:inherit;font-size:14px;resize:vertical;"></textarea>
                <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                  <button type="submit" class="btn btn-fill" style="padding:8px 18px;font-size:13px;">Post Comment</button>
                </div>
              </div>
            </div>
          </form>
        @else
          <div style="background:rgba(255,255,255,0.03);border:1px dashed var(--line);border-radius:6px;padding:20px;text-align:center;margin-bottom:24px;">
            <p style="margin:0 0 12px;color:var(--mute);font-size:14px;">Sign in or create a free account to join the discussion.</p>
            <div style="display:flex;gap:10px;justify-content:center;">
              <a href="{{ route('login') }}" data-modal="login" class="btn btn-fill" style="padding:8px 16px;font-size:13px;">Sign In to Comment</a>
              <a href="{{ route('register') }}" data-modal="register" class="btn btn-line" style="padding:8px 16px;font-size:13px;">Create Account</a>
            </div>
          </div>
        @endauth

        <!-- Comments List -->
        <div class="comments-list" style="display:flex;flex-direction:column;gap:16px;">
          @forelse($p->comments as $c)
            <div class="comment-item" style="display:flex;gap:12px;align-items:flex-start;padding:12px 14px;background:rgba(255,255,255,0.02);border:1px solid var(--line);border-radius:4px;">
              <span class="av" style="--c:var(--cyan);width:32px;height:32px;font-size:13px;font-weight:700;flex-shrink:0;">{{ initial($c->user->username ?? 'U') }}</span>
              <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;flex-wrap:wrap;">
                  <b style="font-size:13px;color:#fff;">{{ $c->user->username ?? 'Anonymous' }}</b>
                  {!! badge($c->user) !!}
                  <span style="font-size:11px;color:var(--mute);margin-left:auto;">{{ $c->created_at->diffForHumans() }}</span>
                </div>
                <div style="font-size:14px;line-height:1.5;color:var(--text);white-space:pre-wrap;word-break:break-word;">{{ $c->comment }}</div>
              </div>
            </div>
          @empty
            <p style="text-align:center;color:var(--mute);font-size:14px;padding:24px 0;margin:0;">No comments yet. Be the first to share your thoughts!</p>
          @endforelse
        </div>
      </section>

    </section>
  </main>
@endsection
