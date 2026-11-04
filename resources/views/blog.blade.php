@extends('layouts.app')

@php
  $blogOwner = $user ?? ($blog->user ?? null);
  $ownerUsername = $blogOwner ? $blogOwner->username : ($blog->slug ?? '');
  $canonicalUrl = blog_url($blog);
  $fullShareUrl = str_starts_with($canonicalUrl, 'http') ? $canonicalUrl : url($canonicalUrl);
  $displayDomain = site_domain();
  $displayUrl = $displayDomain . ($blog->is_main ? '/u/' . $ownerUsername : '/u/' . $ownerUsername . '/' . $blog->slug);
@endphp

@section('title', $blog->title . ' (@' . $ownerUsername . ($blog->is_main ? '' : '/' . $blog->slug) . ') | Nina.Sucks')
@section('description', $blog->tagline ?: 'Explore posts by @' . $ownerUsername)

@section('content')
  @include('partials.header')

  <main class="page" id="main">
    <header class="blog-head" style="--c:{{ $blog->color ?? '#ff2bd6' }}">
      <div class="blog-cover{{ $blog->cover_path ? '' : ' ph' }}" {!! $blog->cover_path ? 'style="background-image:url(' . media_url($blog->cover_path) . ')"' : '' !!}></div>
      <div class="panel" style="margin-top:-46px;position:relative">
        <div style="display:flex;gap:16px;align-items:flex-end;justify-content:space-between;flex-wrap:wrap">
          <div style="display:flex;gap:16px;align-items:flex-end;flex:1;min-width:280px;">
            @if($blog->avatar_path)
              <img src="{{ media_url($blog->avatar_path) }}" alt="" width="88" height="88" style="border-radius:50%;border:4px solid var(--black);object-fit:cover;flex-shrink:0;">
            @else
              <span class="av" style="--c:{{ $blog->color ?? '#ff2bd6' }};width:88px;height:88px;font-size:34px;border:4px solid var(--black);flex-shrink:0;">{{ initial($blog->slug ?: $ownerUsername) }}</span>
            @endif
            <div style="flex:1;min-width:0;">
              <h1 style="font:800 26px var(--f-display);display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:0 0 4px 0;">
                <span style="max-width:100%;overflow:hidden;text-overflow:ellipsis;">{{ $blog->title }}</span>
                {!! badge($blog->badge ?: ($blogOwner ? $blogOwner->badge : '')) !!}
              </h1>
              <p style="color:var(--mute);margin-bottom:6px;word-break:break-word;">
                &#64;{{ $ownerUsername }}{{ !$blog->is_main ? '/' . $blog->slug : '' }}{{ $blog->tagline ? ' · ' . $blog->tagline : '' }} · {{ $posts->total() }} posts · {{ $followerCount }} followers
              </p>
              <div class="profile-link-badge" style="display:inline-flex;align-items:center;gap:6px;background:rgba(25,227,255,0.08);border:1px solid rgba(25,227,255,0.3);padding:4px 12px;border-radius:9999px;font-size:12px;color:var(--cyan);position:relative;max-width:100%;">
                {!! icon('link') !!}
                <a href="{{ $canonicalUrl }}" class="profile-link-text" title="Click to copy profile URL" style="color:var(--cyan);font-family:monospace;font-weight:700;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:220px;display:inline-block;" onclick="event.preventDefault();window.shareOrCopyProfileLink('{{ $fullShareUrl }}', this.parentElement.querySelector('.btn-copy-url'));">{{ $displayUrl }}</a>
                <button type="button" class="btn-copy-url" data-url="{{ $fullShareUrl }}" title="Share or copy profile link" style="background:none;border:none;color:var(--mute);cursor:pointer;padding:0;display:inline-flex;align-items:center;margin-left:4px;transition:color .2s;flex-shrink:0;" onclick="window.shareOrCopyProfileLink('{{ $fullShareUrl }}', this);">
                  {!! icon('share') !!}
                </button>
                <span class="copy-tooltip" style="display:none;position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);background:#19e3ff;color:#001318;font-size:11px;font-weight:800;padding:3px 8px;border-radius:4px;white-space:nowrap;box-shadow:0 4px 12px rgba(0,0,0,0.4);pointer-events:none;z-index:10;">Copied to clipboard!</span>
              </div>
            </div>
          </div>
          <div style="flex-shrink:0;align-self:flex-end;margin-bottom:6px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            @if(!$isOwner && auth()->check())
              <button class="follow{{ !empty($isFollowing) ? ' on' : '' }}" type="button" data-blog="{{ $blog->slug }}" style="padding:8px 18px;font-size:13px;border-radius:4px;white-space:nowrap;">{{ !empty($isFollowing) ? 'Following' : 'Follow' }}</button>
              <button type="button" class="btn btn-line" onclick="openPrivateMessageModal('{{ $blogOwner ? $blogOwner->id : '' }}', '{{ $ownerUsername }}')" style="padding:8px 14px;font-size:13px;border-radius:4px;white-space:nowrap;display:inline-flex;align-items:center;gap:6px;">
                {!! icon('mail') !!} <span>Message</span>
              </button>
            @elseif(!auth()->check())
              <a class="btn btn-fill" href="{{ route('login') }}" data-modal="login" style="padding:8px 18px;font-size:13px;white-space:nowrap;">Sign in to follow</a>
              <a class="btn btn-line" href="{{ route('login') }}" data-modal="login" style="padding:8px 14px;font-size:13px;white-space:nowrap;display:inline-flex;align-items:center;gap:6px;">
                {!! icon('mail') !!} <span>Message</span>
              </a>
            @elseif($isOwner)
              <a class="btn btn-ghost" href="{{ route('dashboard.blogs') }}" style="padding:8px 18px;font-size:13px;white-space:nowrap;">Edit blog</a>
            @endif
          </div>
        </div>

        @if(isset($userBlogs) && $userBlogs->count() > 1)
          <!-- Multi-Blog Network Switcher -->
          <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--line);display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <span style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;color:var(--mute);margin-right:4px;">Blogs by &#64;{{ $ownerUsername }}:</span>
            @foreach($userBlogs as $ub)
              @php
                $isCurrent = $ub->id === $blog->id || ($ub->slug === $blog->slug);
                $uUrl = blog_url($ub);
              @endphp
              <a href="{{ $uUrl }}" class="chip{{ $isCurrent ? ' is-on' : '' }}" style="padding:4px 10px;font-size:12px;border-radius:9999px;">
                @if($ub->is_main)
                  <b>Main Profile</b>
                @else
                  <span>/{{ $ub->slug }}</span>
                @endif
              </a>
            @endforeach
          </div>
        @endif

        @if(($blog->is_main || $blog->show_profile) && ($blog->bio || $blog->website1 || $blog->website2 || $blog->website3 || $blog->contact_email))
          <div class="blog-profile" style="margin-top:18px;padding-top:18px;border-top:1px solid var(--line)">
            @if($blog->bio)<p>{!! nl2br(e($blog->bio)) !!}</p>@endif
            <p class="row-actions" style="margin-top:8px">
              @foreach([$blog->website1, $blog->website2, $blog->website3] as $w)
                @if($w)
                  <a class="btn-xs" href="{{ $w }}" target="_blank" rel="nofollow noopener me">{!! icon('link') !!} {{ preg_replace('/^https?:\/\//', '', $w) }}</a>
                @endif
              @endforeach
              @if($blog->contact_email)
                <a class="btn-xs" href="mailto:{{ $blog->contact_email }}">{!! icon('mail') !!} {{ $blog->contact_email }}</a>
              @endif
            </p>
          </div>
        @endif

        <div class="filters" style="margin-top:20px;border-top:1px solid var(--line);padding-top:16px" role="toolbar" aria-label="Filter posts by format">
          <a class="chip{{ ($type ?? 'all') === 'all' ? ' is-on' : '' }}" href="{{ $canonicalUrl }}" data-f="all">All</a>
          @foreach(post_types() as $key => $val)
            <a class="chip{{ ($type ?? 'all') === $key ? ' is-on' : '' }}" href="{{ $canonicalUrl }}?type={{ $key }}" data-f="{{ $key }}">{!! icon($val[1]) !!}{{ $val[0] }}</a>
          @endforeach
        </div>
      </div>
    </header>

    <section class="panel" style="margin-top:24px">
      <div class="feed" id="feed" data-view="grid">
        @forelse($posts as $p)
          @include('components.card', ['p' => $p])
        @empty
          <p class="empty" style="padding:40px 20px;text-align:center;color:var(--mute);width:100%;">No posts found on this blog yet.</p>
        @endforelse
      </div>
      <div style="margin-top:24px;text-align:center;">
        {{ $posts->links() }}
      </div>
    </section>
  </main>

  <!-- Private Message Modal -->
  <div class="modal" id="pmModal" hidden role="dialog" aria-modal="true" aria-labelledby="pmModalTitle">
    <div class="m-box" style="border-color:var(--cyan);box-shadow:0 0 50px rgba(25,227,255,0.3);">
      <button class="m-close" type="button" aria-label="Close modal" onclick="closePrivateMessageModal()">{!! icon('x') !!}</button>
      <h2 id="pmModalTitle" style="color:var(--cyan);font-size:22px;">Send Private Message</h2>
      <p class="m-sub">Send a direct message to <strong id="pmRecipientName" style="color:#fff;"></strong>. They will also receive an email notification.</p>

      <form method="POST" action="{{ route('action.message') }}" id="pmForm">
        @csrf
        <input type="hidden" name="recipient_id" id="pmRecipientId" value="">
        <input type="hidden" name="recipient_username" id="pmRecipientUsername" value="">

        <label class="field">
          <span>Subject (Optional)</span>
          <input type="text" name="subject" placeholder="What's this regarding?">
        </label>

        <label class="field">
          <span>Message</span>
          <textarea name="body" required placeholder="Write your private message here..." style="min-height:120px;"></textarea>
        </label>

        <div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;margin-top:16px;">
          <button type="button" class="btn btn-ghost" onclick="closePrivateMessageModal()">Cancel</button>
          <button type="submit" class="btn btn-fill" style="background:var(--cyan);border-color:var(--cyan);color:#001318;">
            {!! icon('mail') !!} <span>Send Message</span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openPrivateMessageModal(userId, username) {
      const modal = document.getElementById('pmModal');
      if (!modal) return;
      document.getElementById('pmRecipientId').value = userId || '';
      document.getElementById('pmRecipientUsername').value = username || '';
      document.getElementById('pmRecipientName').textContent = '@' + username;
      modal.hidden = false;
      document.body.classList.add('locked');
    }

    function closePrivateMessageModal() {
      const modal = document.getElementById('pmModal');
      if (modal) {
        modal.hidden = true;
        document.body.classList.remove('locked');
      }
    }
  </script>
@endsection
