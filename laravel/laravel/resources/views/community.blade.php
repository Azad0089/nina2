@extends('layouts.app')

@section('title', 'Community Hub | ' . site_name())
@section('description', $community['tagline'] ?? 'Connect with creators, explore featured profiles and discover active blogs on ' . site_name())

@section('content')
  @include('partials.header')

  <main class="page" id="main">
    <header class="blog-head" style="--c:{{ $community['color'] ?? '#ff2bd6' }}">
      <div class="blog-cover ph" style="background:linear-gradient(135deg, rgba(255,43,214,0.3) 0%, rgba(25,227,255,0.2) 100%), #111;"></div>
      <div class="panel" style="margin-top:-46px;position:relative">
        <div style="display:flex;gap:16px;align-items:flex-end;flex-wrap:wrap">
          <span class="av" style="--c:{{ $community['color'] ?? '#ff2bd6' }};width:88px;height:88px;font-size:34px;border:4px solid var(--black)">
            {!! icon('users') !!}
          </span>
          <div style="flex:1">
            <h1 style="font:800 28px var(--f-display);display:flex;align-items:center;gap:10px;flex-wrap:wrap">
              {{ $community['title'] }}
              <span class="badge b-vip" title="Official Community">{!! icon('star') !!}</span>
            </h1>
            <p style="color:var(--mute)">
              {{ $community['tagline'] }} &middot; {{ $stats['total_creators'] }} creators &middot; {{ $stats['total_blogs'] }} blogs &middot; {{ $stats['total_posts'] }} posts
            </p>
          </div>
          @if(auth()->check())
            <a class="btn btn-fill" href="{{ route('dashboard.post.create') }}">{!! icon('plus') !!} New Post</a>
          @else
            <a class="btn btn-fill" href="{{ route('register') }}" data-modal="register">Join Community</a>
          @endif
        </div>

        <!-- Community Profile Box: Bio, 3 Links & Email -->
        <div class="blog-profile" style="margin-top:18px;padding-top:18px;border-top:1px solid var(--line)">
          @if(!empty($community['bio']))
            <p style="font-size:15px;line-height:1.6;color:var(--text);white-space:pre-line;">{{ $community['bio'] }}</p>
          @endif

          <p class="row-actions" style="margin-top:12px;display:flex;gap:10px;flex-wrap:wrap;">
            @if(!empty($community['website1']))
              <a class="btn-xs" href="{{ $community['website1'] }}" target="_blank" rel="nofollow noopener">{!! icon('link') !!} {{ preg_replace('/^https?:\/\//', '', $community['website1']) }}</a>
            @endif
            @if(!empty($community['website2']))
              <a class="btn-xs" href="{{ $community['website2'] }}" target="_blank" rel="nofollow noopener">{!! icon('link') !!} {{ preg_replace('/^https?:\/\//', '', $community['website2']) }}</a>
            @endif
            @if(!empty($community['website3']))
              <a class="btn-xs" href="{{ $community['website3'] }}" target="_blank" rel="nofollow noopener">{!! icon('link') !!} {{ preg_replace('/^https?:\/\//', '', $community['website3']) }}</a>
            @endif
            @if(!empty($community['email']))
              <a class="btn-xs" href="mailto:{{ $community['email'] }}">{!! icon('mail') !!} {{ $community['email'] }}</a>
            @endif
          </p>
        </div>
      </div>
    </header>

    <!-- Community Sections -->
    <div style="max-width:1120px;margin:28px auto 60px;padding:0 16px;">
      <!-- Featured Creators Showcase -->
      <section style="margin-bottom:40px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
          <h2 style="font:800 20px var(--f-display);display:flex;align-items:center;gap:8px;">
            {!! icon('star') !!} Featured Community Creators
          </h2>
          <a href="{{ route('trending') }}" style="color:var(--mute);font-size:14px;text-decoration:none;">View all &rarr;</a>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(240px, 1fr));gap:16px;">
          @foreach($featuredCreators as $cBlog)
            <div style="background:var(--paper);border:1px solid var(--line);border-radius:10px;padding:16px;display:flex;flex-direction:column;gap:10px;position:relative;">
              <div style="display:flex;align-items:center;gap:12px;">
                @if($cBlog->avatar_path)
                  <img src="{{ media_url($cBlog->avatar_path) }}" alt="" width="48" height="48" style="border-radius:50%;object-fit:cover;">
                @else
                  <span class="av" style="--c:{{ $cBlog->color ?? '#ff2bd6' }};width:48px;height:48px;font-size:20px;">{{ initial($cBlog->slug) }}</span>
                @endif
                <div style="overflow:hidden;">
                  <a href="{{ route('blog.show', $cBlog->slug) }}" style="font-weight:700;color:var(--text);text-decoration:none;display:flex;align-items:center;gap:4px;">
                    <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $cBlog->title }}</span>
                    @if($cBlog->user)
                      {!! badge($cBlog->user->badge_vip ? 'vip' : ($cBlog->user->badge_id ? 'id' : ($cBlog->user->badge_pro ? 'pro' : ''))) !!}
                    @endif
                  </a>
                  <span style="font-size:12px;color:var(--mute);">@ {{ $cBlog->slug }}</span>
                </div>
              </div>

              @if($cBlog->bio)
                <p style="font-size:13px;color:var(--mute);line-height:1.4;margin:0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                  {{ $cBlog->bio }}
                </p>
              @endif

              <div style="margin-top:auto;padding-top:8px;border-top:1px solid var(--line);display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:12px;color:var(--mute);">{{ $cBlog->posts()->count() }} posts</span>
                <a class="btn-xs" href="{{ route('blog.show', $cBlog->slug) }}" style="padding:4px 10px;background:var(--line);color:var(--text);border-radius:4px;text-decoration:none;">Visit Blog</a>
              </div>
            </div>
          @endforeach
        </div>
      </section>

      <!-- Recent Community Feed -->
      <section>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
          <h2 style="font:800 20px var(--f-display);display:flex;align-items:center;gap:8px;">
            {!! icon('flame') !!} Community Discussions & Media
          </h2>
          <a href="{{ url('/') }}" style="color:var(--mute);font-size:14px;text-decoration:none;">Explore feed &rarr;</a>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));gap:20px;">
          @foreach($communityPosts as $post)
            @include('components.card', ['p' => $post])
          @endforeach
        </div>
      </section>
    </div>
  </main>

  @include('partials.footer')
@endsection
