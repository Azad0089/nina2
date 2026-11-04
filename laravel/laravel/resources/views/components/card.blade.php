@php
  $color = $p->blog->color ?? ($p->color ?? '#ff2bd6');
  $type = $p->type;
  $blogSlug = $p->blog->slug ?? ($p->blog ?? 'anonymous');
  $blogBadge = !empty($p->blog->badge) ? $p->blog->badge : (!empty($p->badge) ? $p->badge : ($p->blog && $p->blog->user ? $p->blog->user->badge : ''));
  $isLiked = auth()->check() ? auth()->user()->likes()->where('post_id', $p->id)->exists() : false;
  $isFollowing = auth()->check() && $p->blog ? auth()->user()->follows()->where('blog_id', $p->blog->id)->exists() : false;
  $isMine = auth()->check() && $p->blog && auth()->id() === $p->blog->user_id;
  $likeCount = $p->likes()->count() ?: ($p->like_count ?? 0);
  $commentCount = method_exists($p, 'comments') ? $p->comments()->count() : 0;
  $reblogCount = method_exists($p, 'reblogs') ? $p->reblogs()->count() : ($p->reblog_count ?? 0);
  $isReblogged = auth()->check() && method_exists(auth()->user(), 'blogs') ? auth()->user()->blogs()->whereHas('posts', fn($q) => $q->where('reblog_of_post_id', $p->id))->exists() : false;
@endphp
<article class="card t-{{ $type }}" data-type="{{ $type }}" data-post-id="{{ $p->id }}" style="--c:{{ $color }}">
  <header class="c-head">
    <a href="{{ route('blog.show', ['slug' => $blogSlug]) }}" style="display:contents">
      @if(!empty($p->blog->avatar_path))
        <img class="av" src="{{ media_url($p->blog->avatar_path) }}" alt="" width="36" height="36" loading="lazy">
      @else
        <span class="av" style="--c:{{ $color }}">{{ initial($blogSlug) }}</span>
      @endif
    </a>
    <div class="c-who">
      <b><a href="{{ route('blog.show', ['slug' => $blogSlug]) }}">{{ $blogSlug }}</a> {!! badge($blogBadge) !!}</b>
      <small>{{ $type }} post</small>
    </div>
    @if(!$isMine)
      <button class="follow{{ $isFollowing ? ' on' : '' }}" type="button" data-blog="{{ $blogSlug }}">{{ $isFollowing ? 'Following' : 'Follow' }}</button>
    @endif
  </header>

@if($type === 'photo' || $type === 'video')
  <figure class="media crop" style="{{ crop_style($p->crop_meta ?? []) }}">
    <img src="{{ media_url($p->media_path ?: 'hero.jpg') }}" alt="{{ $p->caption ?? '' }}" loading="lazy" decoding="async">
    @if($type === 'video')
      <video controls preload="none" poster="" style="display:none"></video>
      <button class="play" type="button" aria-label="Play video" data-video-src="{{ media_url($p->media_path ?: 'hero.jpg') }}">{!! icon('play') !!}</button>
      <span class="dur">0:30</span>
    @endif
  </figure>
  <div class="c-body"><p>{{ $p->caption ?? '' }}</p>

@elseif($type === 'text')
  <div class="blk"><h3>{{ $p->heading ?? '' }}</h3><p>{!! nl2br(e($p->body ?? '')) !!}</p></div>
  <div class="c-body">

@elseif($type === 'quote')
  <div class="c-body">
    <blockquote><p>{{ $p->quote_text ?? ($p->quote ?? '') }}</p><cite>{{ $p->quote_source ?? ($p->source ?? '') }}</cite></blockquote>

@elseif($type === 'link')
  <a class="lk" href="{{ $p->link_url ?? '#' }}" target="_blank" rel="nofollow noopener">
    <div class="lk-t">{!! icon('link') !!}</div>
    <div><b>{{ $p->link_title ?? ($p->heading ?? 'External Link') }}</b><small>{{ $p->link_domain ?? parse_url($p->link_url ?? '', PHP_URL_HOST) }}</small></div>
  </a>
  <div class="c-body">

@elseif($type === 'chat')
  <div class="chat">
    @php
      $rawChat = is_array($p->chat_payload) ? $p->chat_payload : (is_string($p->chat_payload) ? json_decode($p->chat_payload, true) : []);
      $lines = is_array($rawChat) ? $rawChat : [];
    @endphp
    @foreach($lines as $l)
      @if(is_array($l))
        <p><b>{{ $l[0] ?? '' }}:</b> {{ $l[1] ?? '' }}</p>
      @else
        @php
          $parts = explode(':', (string)$l, 2);
        @endphp
        @if(count($parts) === 2)
          <p><b>{{ trim($parts[0]) }}:</b> {{ trim($parts[1]) }}</p>
        @else
          <p>{{ $l }}</p>
        @endif
      @endif
    @endforeach
  </div>
  <div class="c-body">
@endif

    <div class="tags">
      <a href="{{ route('trending', ['tag' => 'adult']) }}">#adult</a>
      <a href="{{ route('trending', ['tag' => 'microblog']) }}">#microblog</a>
    </div>
  </div>

  <footer class="c-foot">
    <a class="act" href="{{ route('post.show', ['id' => $p->id]) }}#comments" aria-label="View comments" title="Comments and Discussion">{!! icon('chat') !!}<span>{{ $commentCount }}</span></a>
    <button class="act reblog{{ $isReblogged ? ' on' : '' }}" type="button" aria-label="Reblog" data-post-id="{{ $p->id }}" title="Reblog to your blog">{!! icon('repeat') !!}<span>{{ $reblogCount }}</span></button>
    <button class="act like{{ $isLiked ? ' on' : '' }}" type="button" aria-label="Like" data-post-id="{{ $p->id }}">{!! icon('heart') !!}<span>{{ fmt_count($likeCount) }}</span></button>
    <span class="ago">{{ time_ago($p->published_at ?? 5) }} ago</span>
    <button class="act report" type="button" aria-label="Report" data-post-id="{{ $p->id }}" title="Report this post">{!! icon('flag') !!}</button>
  </footer>
</article>
