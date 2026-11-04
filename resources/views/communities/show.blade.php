@extends('layouts.app')

@section('title', $community->title . ' | ' . site_name())
@section('description', $community->tagline ?: $community->title)

@section('content')
  @include('partials.header')
  <main class="page" id="main">
    <header class="blog-head" style="--c:{{ $community->color ?: '#ff2bd6' }}">
      @if($community->cover_path)
        <div class="blog-cover" style="background-image:url('{{ media_url($community->cover_path) }}');background-size:cover;background-position:center;height:200px;"></div>
      @endif
      <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap;padding:16px 0;">
        @if($community->avatar_path)
          <img src="{{ media_url($community->avatar_path) }}" alt="" width="72" height="72" style="border-radius:16px;object-fit:cover;">
        @endif
        <div style="flex:1;min-width:200px;">
          <h1 style="margin:0;">{{ $community->title }}</h1>
          <p style="margin:4px 0;color:var(--mute);">{{ $community->tagline }}</p>
          @if($community->bio)<p>{!! nl2br(e($community->bio)) !!}</p>@endif
        </div>
        @auth
          @if(!$membership || $membership->status !== 'active')
            <form method="post" action="{{ route('member-community.join', $community->slug) }}">@csrf
              <button class="btn btn-fill" type="submit">{{ $community->join_mode === 'invite' ? t('communities.invite_only') : t('communities.join') }}</button>
            </form>
          @elseif($membership->role !== 'owner')
            <form method="post" action="{{ route('member-community.leave', $community->slug) }}">@csrf
              <button class="btn btn-line" type="submit">{{ t('communities.leave') }}</button>
            </form>
          @endif
        @endauth
      </div>
    </header>

    @if(session('success'))<p class="form-msg ok">{{ session('success') }}</p>@endif
    @if(session('error'))<p class="form-msg error">{{ session('error') }}</p>@endif

    @if(!empty($tagCounts))
      <div style="margin:12px 0 18px;">
        <h2 class="sec-title">{{ t('communities.tags') }}</h2>
        <a class="chip{{ $tag === '' ? ' is-on' : '' }}" href="{{ route('member-community.show', $community->slug) }}">All</a>
        @foreach($tagCounts as $name => $count)
          <a class="chip{{ $tag === $name ? ' is-on' : '' }}" href="{{ route('member-community.show', ['slug' => $community->slug, 'tag' => $name]) }}">#{{ $name }}</a>
        @endforeach
      </div>
    @endif

    @if($isAdmin)
      <form method="post" action="{{ route('member-community.invite', $community->slug) }}" style="display:flex;gap:8px;margin-bottom:16px;">
        @csrf
        <input name="username" placeholder="@username" style="padding:8px 12px;background:var(--ink);border:1px solid var(--line);color:#fff;border-radius:8px;">
        <button class="btn btn-line" type="submit">Invite</button>
      </form>
      @foreach($pending as $row)
        <form method="post" action="{{ route('member-community.approve', ['slug' => $community->slug, 'userId' => $row->user_id]) }}" style="margin-bottom:8px;">
          @csrf
          <button class="btn btn-xs" type="submit">Approve @{{ $row->user->username ?? $row->user_id }}</button>
        </form>
      @endforeach
    @endif

    @if($membership && $membership->status === 'active')
      <form method="post" action="{{ route('member-community.post', $community->slug) }}" enctype="multipart/form-data" style="margin-bottom:20px;padding:14px;border:1px solid var(--line);border-radius:12px;">
        @csrf
        <input type="text" name="heading" placeholder="Title (optional)" style="width:100%;margin-bottom:8px;padding:8px;background:var(--ink);border:1px solid var(--line);color:#fff;">
        <textarea name="body" rows="3" placeholder="Write in this community" style="width:100%;margin-bottom:8px;padding:8px;background:var(--ink);border:1px solid var(--line);color:#fff;"></textarea>
        <input type="text" name="tags" placeholder="community tags, comma separated" style="width:100%;margin-bottom:8px;padding:8px;background:var(--ink);border:1px solid var(--line);color:#fff;">
        <input type="file" name="media_file" accept="image/*,video/mp4,video/webm">
        <input type="url" name="video_url" placeholder="Or paste a video page URL" style="width:100%;margin:8px 0;padding:8px;background:var(--ink);border:1px solid var(--line);color:#fff;">
        <button class="btn btn-fill" type="submit">Publish</button>
      </form>
    @endif

    <div class="feed">
      @foreach($posts as $p)
        @include('components.card', ['p' => $p])
        @if($isAdmin)
          <form method="post" action="{{ route('member-community.pin', ['slug' => $community->slug, 'postId' => $p->id]) }}" style="margin:-8px 0 16px;">
            @csrf
            <button class="btn btn-xs" type="submit">{{ $p->is_pinned ? t('communities.unpin') : t('communities.pin') }}</button>
          </form>
        @endif
      @endforeach
    </div>
    {{ $posts->links() }}
  </main>
  @include('partials.footer')
@endsection
