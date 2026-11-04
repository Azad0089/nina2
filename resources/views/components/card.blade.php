@php
  $color = $p->blog->color ?? ($p->color ?? '#ff2bd6');
  $type = $p->type;
  $blogSlug = $p->blog->slug ?? ($p->blog ?? 'anonymous');
  $blogUrl = blog_url($p->blog ?? $blogSlug);
  $ownerUsername = $p->blog && $p->blog->user ? $p->blog->user->username : $blogSlug;
  $displaySlug = ($p->blog && !$p->blog->is_main && strtolower($p->blog->slug) !== strtolower($ownerUsername)) ? $ownerUsername . '/' . $p->blog->slug : $ownerUsername;
  $renderedBadges = '';
  if ($p->blog) {
      $renderedBadges = badge($p->blog);
  }
  if (empty($renderedBadges) && $p->blog && $p->blog->user) {
      $renderedBadges = badge($p->blog->user);
  }
  if (empty($renderedBadges)) {
      $renderedBadges = badge($p);
  }
  $isLiked = auth()->check() ? auth()->user()->likes()->where('post_id', $p->id)->exists() : false;
  $isFollowing = auth()->check() && $p->blog ? auth()->user()->follows()->where('blog_id', $p->blog->id)->exists() : false;
  $isMine = auth()->check() && $p->blog && auth()->id() === $p->blog->user_id;
  $likeCount = $p->likes()->count() ?: ($p->like_count ?? 0);
  $commentCount = method_exists($p, 'comments') ? $p->comments()->count() : 0;
  $reblogCount = method_exists($p, 'reblogs') ? $p->reblogs()->count() : ($p->reblog_count ?? 0);
  $isReblogged = auth()->check() && method_exists(auth()->user(), 'blogs') ? auth()->user()->blogs()->whereHas('posts', fn($q) => $q->where('reblog_of_post_id', $p->id))->exists() : false;
@endphp
<article class="card t-{{ $type }}" data-type="{{ $type }}" data-post-id="{{ $p->id }}" style="--c:{{ $color }}">
  @php $postSchema = post_schema($p); @endphp
  @if(!empty($postSchema))
    {!! schema_ld_tag($postSchema) !!}
  @endif
  <header class="c-head">
    <a href="{{ $blogUrl }}" style="display:flex;flex-shrink:0;text-decoration:none;">
      @if(!empty($p->blog->avatar_path))
        <img class="av" src="{{ media_url($p->blog->avatar_path) }}" alt="" width="36" height="36" loading="lazy">
      @else
        <span class="av" style="--c:{{ $color }}">{{ initial($displaySlug) }}</span>
      @endif
    </a>
    <div class="c-who">
      <div class="c-name-row">
        <a class="c-username" href="{{ $blogUrl }}">&#64;{{ $displaySlug }}</a>
        @if(!empty($renderedBadges))
          <span class="c-badges">{!! $renderedBadges !!}</span>
        @endif
      </div>
      <small class="c-type">
        @if(!empty($p->is_pinned))
          <span style="color:#ff2bd6;font-weight:800;display:inline-flex;align-items:center;gap:3px;margin-right:6px;background:rgba(255,43,214,0.15);padding:2px 8px;border-radius:9999px;border:1px solid rgba(255,43,214,0.35);">{!! icon('flame') !!} PINNED</span>
        @endif
        @if(!empty($p->is_promoted))
          <span style="color:#19e3ff;font-weight:700;display:inline-flex;align-items:center;gap:3px;margin-right:4px;">{!! icon('flame') !!} Boosted</span> &bull;
        @endif
        {{ $type }} post
      </small>
    </div>
    <div class="c-head-action" style="display:flex;align-items:center;gap:6px;">
      @if(!$isMine)
        <button class="follow{{ $isFollowing ? ' on' : '' }}" type="button" data-blog="{{ $blogSlug }}">{{ $isFollowing ? 'Following' : 'Follow' }}</button>
      @else
        <button class="btn-pin-post{{ !empty($p->is_pinned) ? ' is-pinned' : '' }}" type="button" data-post-id="{{ $p->id }}" title="{{ !empty($p->is_pinned) ? 'Unpin post from blog feed' : 'Pin post to top of blog feed (max 3)' }}" style="background:rgba(255,43,214,0.1);border:1px solid rgba(255,43,214,0.3);color:{{ !empty($p->is_pinned) ? '#ff2bd6' : '#a291b5' }};border-radius:6px;padding:3px 8px;font-size:11px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:3px;transition:all .2s;">
          {!! icon('flame') !!}
          <span>{{ !empty($p->is_pinned) ? 'Unpin' : 'Pin' }}</span>
        </button>
        <button class="btn-del-post" type="button" data-post-id="{{ $p->id }}" title="Delete your post">{!! icon('trash') !!} <span>Delete</span></button>
      @endif
    </div>
  </header>

@php
  $mediaUrl = media_url($p->media_path);
  $isVideo = ($type === 'video') || 
             (!empty($p->media_path) && preg_match('/\.(mp4|webm|mov|m4v|ogg)(\?.*)?$/i', $p->media_path)) ||
             (!empty($p->media_type) && $p->media_type === 'video');
  $postUrl = route('post.show', ['id' => $p->id]);
  $isSingle = !empty($isSinglePost);
  $textLimit = (int)($siteSettings['feed_text_excerpt_limit'] ?? 280);
  $captionLimit = (int)($siteSettings['feed_caption_excerpt_limit'] ?? 140);
  $mediaHtml = trim((string)($p->body ?? ''));
  if (plain_post_text($mediaHtml) === '') {
      $fallbackCaption = trim((string)($p->caption ?? ''));
      $mediaHtml = $fallbackCaption !== '' ? '<p>' . e($fallbackCaption) . '</p>' : '';
  }
  $mediaPlain = plain_post_text($mediaHtml);
  [$displayCap, $isCapLong] = $isSingle ? [$mediaPlain, false] : feed_media_excerpt($mediaPlain, $captionLimit);
  $mediaAllowed = '<p><br><b><strong><i><em><u><s><strike><h2><h3><h4><blockquote><ul><ol><li><a><code><pre><span><mark><hr><div>';
@endphp

@if($isVideo)
  <figure class="media media-video" style="position:relative;background:#05020a;border-radius:4px;overflow:hidden;margin:0;">
    <video
      controls
      preload="metadata"
      playsinline
      webkit-playsinline
      class="player-video"
      style="width:100%;max-height:560px;display:block;margin:0 auto;background:#000;object-fit:contain;"
      src="{{ $mediaUrl }}">
      <source src="{{ $mediaUrl }}" type="video/mp4">
      Your browser does not support HTML5 video.
    </video>
  </figure>
  <div class="c-body">
    @if(!empty($p->heading))
      <h3 style="margin:0 0 8px;font-size:1.05rem;line-height:1.35;">@if(!$isSingle)<a href="{{ $postUrl }}" target="_blank" style="color:inherit;text-decoration:none;">{{ $p->heading }}</a>@else{{ $p->heading }}@endif</h3>
    @endif
    @if($mediaPlain !== '')
      @if($isSingle)
        <div class="rich-body">{!! strip_tags($mediaHtml, $mediaAllowed) !!}</div>
      @else
        <p style="margin:0;">{{ $displayCap }} @if($isCapLong)<a href="{{ $postUrl }}" target="_blank" style="color:#19e3ff;font-weight:600;font-size:12px;text-decoration:underline;">Read more &rarr;</a>@endif</p>
      @endif
    @endif

@elseif($type === 'photo')
  <figure class="media">
    <img src="{{ $mediaUrl }}" alt="{{ $mediaPlain }}" loading="lazy" decoding="async">
  </figure>
  <div class="c-body">
    @if(!empty($p->heading))
      <h3 style="margin:0 0 8px;font-size:1.05rem;line-height:1.35;">@if(!$isSingle)<a href="{{ $postUrl }}" target="_blank" style="color:inherit;text-decoration:none;">{{ $p->heading }}</a>@else{{ $p->heading }}@endif</h3>
    @endif
    @if($mediaPlain !== '')
      @if($isSingle)
        <div class="rich-body">{!! strip_tags($mediaHtml, $mediaAllowed) !!}</div>
      @else
        <p style="margin:0;">{{ $displayCap }} @if($isCapLong)<a href="{{ $postUrl }}" target="_blank" style="color:#19e3ff;font-weight:600;font-size:12px;text-decoration:underline;">Read more &rarr;</a>@endif</p>
      @endif
    @endif

@elseif($type === 'text')
  @php
    $plain = trim(preg_replace('/\s+/', ' ', strip_tags($p->body ?? '')));
    $isTextLong = !$isSingle && (mb_strlen($plain) > $textLimit);
    $textExcerpt = $isTextLong ? mb_substr($plain, 0, $textLimit) . '...' : ($p->body ?? '');
  @endphp
  <div class="blk">
    @if(!empty($p->heading))
      <h3><a href="{{ $postUrl }}" target="_blank" style="color:inherit;text-decoration:none;">{{ $p->heading }}</a></h3>
    @endif
    @if($isSingle)
      <div class="rich-body">{!! strip_tags($p->body ?? '', '<p><br><b><strong><i><em><u><s><strike><h2><h3><h4><blockquote><ul><ol><li><a><code><pre><span><mark><hr><img><div>') !!}</div>
    @elseif($isTextLong)
      <div class="rich-body"><p>{{ $textExcerpt }}</p></div>
      <div style="margin-top:10px;">
        <a href="{{ $postUrl }}" target="_blank" class="btn-read-more" style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:6px;background:rgba(255,43,214,0.12);border:1px solid rgba(255,43,214,0.3);color:#ff2bd6;font-size:12px;font-weight:700;text-decoration:none;transition:all 0.2s;">
          <span>Read full story</span> &rarr;
        </a>
      </div>
    @else
      <div class="rich-body">{!! strip_tags($p->body ?? '', '<p><br><b><strong><i><em><u><s><strike><h2><h3><h4><blockquote><ul><ol><li><a><code><pre><span><mark><hr><img><div>') !!}</div>
    @endif
  </div>
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

    @php
      $postTags = $p->tags_list ?? [];
    @endphp
    @if(!empty($postTags))
      <div class="tags">
        @foreach($postTags as $t)
          <a href="{{ route('trending', ['tag' => $t]) }}">#{{ $t }}</a>
        @endforeach
      </div>
    @endif
  </div>

  <footer class="c-foot">
    <button class="act comment-btn" type="button" aria-label="View comments" title="Comments and Discussion" data-post-id="{{ $p->id }}" data-post-title="{{ e($p->heading ?? ($p->caption ?? ($p->quote_text ?? 'Post #' . $p->id))) }}">{!! icon('chat') !!}<span>{{ $commentCount }}</span></button>
    <button class="act reblog{{ $isReblogged ? ' on' : '' }}" type="button" aria-label="Reblog" data-post-id="{{ $p->id }}" title="Reblog to your blog">{!! icon('repeat') !!}<span>{{ $reblogCount }}</span></button>
    <button class="act like{{ $isLiked ? ' on' : '' }}" type="button" aria-label="Like" data-post-id="{{ $p->id }}">{!! icon('heart') !!}<span>{{ fmt_count($likeCount) }}</span></button>
    <a class="ago" href="{{ $postUrl }}" target="_blank" title="Open post in new window" style="color:inherit;text-decoration:none;">{{ time_ago($p->published_at ?? 5) }} ago</a>
    @if($isMine)
      <button class="act post-delete-btn" type="button" aria-label="Delete post" data-post-id="{{ $p->id }}" title="Delete this post">{!! icon('trash') !!}</button>
    @else
      <button class="act report" type="button" aria-label="Report" data-post-id="{{ $p->id }}" title="Report this post">{!! icon('flag') !!}</button>
    @endif
  </footer>
</article>
