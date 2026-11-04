@extends('layouts.app')

@section('title', $blog->title . ' (@' . $blog->slug . ') | Nina.Sucks')
@section('description', $blog->tagline ?: 'Explore posts by @' . $blog->slug)

@section('content')
  @include('partials.header')

  <main class="page" id="main">
    <header class="blog-head" style="--c:{{ $blog->color ?? '#ff2bd6' }}">
      <div class="blog-cover{{ $blog->cover_path ? '' : ' ph' }}" {!! $blog->cover_path ? 'style="background-image:url(' . media_url($blog->cover_path) . ')"' : '' !!}></div>
      <div class="panel" style="margin-top:-46px;position:relative">
        <div style="display:flex;gap:16px;align-items:flex-end;flex-wrap:wrap">
          @if($blog->avatar_path)
            <img src="{{ media_url($blog->avatar_path) }}" alt="" width="88" height="88" style="border-radius:50%;border:4px solid var(--black);object-fit:cover">
          @else
            <span class="av" style="--c:{{ $blog->color ?? '#ff2bd6' }};width:88px;height:88px;font-size:34px;border:4px solid var(--black)">{{ initial($blog->slug) }}</span>
          @endif
          <div style="flex:1">
            <h1 style="font:800 26px var(--f-display);display:flex;align-items:center;gap:8px;flex-wrap:wrap">
              {{ $blog->title }}
            </h1>
            <p style="color:var(--mute)">
              @{{ $blog->slug }}{{ $blog->tagline ? ' · ' . $blog->tagline : '' }} · {{ $posts->total() }} posts · {{ $followerCount }} followers
            </p>
          </div>
          @if(!$isOwner && auth()->check())
            <button class="follow{{ !empty($isFollowing) ? ' on' : '' }}" type="button" data-blog="{{ $blog->slug }}">{{ !empty($isFollowing) ? 'Following' : 'Follow' }}</button>
          @elseif(!auth()->check())
            <a class="btn btn-fill" href="{{ route('login') }}" data-modal="login">Sign in to follow</a>
          @elseif($isOwner)
            <a class="btn btn-ghost" href="{{ route('dashboard.blogs') }}">Edit blog</a>
          @endif
        </div>

        @if($blog->bio || $blog->website1 || $blog->website2 || $blog->contact_email)
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
          <a class="chip{{ ($type ?? 'all') === 'all' ? ' is-on' : '' }}" href="{{ route('blog.show', ['slug' => $blog->slug]) }}">All</a>
          @foreach(post_types() as $key => $val)
            <a class="chip{{ ($type ?? 'all') === $key ? ' is-on' : '' }}" href="{{ route('blog.show', ['slug' => $blog->slug, 'type' => $key]) }}">{!! icon($val[1]) !!}{{ $val[0] }}</a>
          @endforeach
        </div>
      </div>
    </header>

    <section class="panel" style="margin-top:24px">
      <div class="feed" data-view="grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;">
        @forelse($posts as $p)
          @include('components.card', ['p' => $p])
        @empty
          <p class="empty" style="padding:40px 20px;text-align:center;color:var(--mute);grid-column:1/-1">No posts found on this blog yet.</p>
        @endforelse
      </div>
      <div style="margin-top:24px;text-align:center;">
        {{ $posts->links() }}
      </div>
    </section>
  </main>
@endsection
