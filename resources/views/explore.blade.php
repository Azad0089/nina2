@extends('layouts.app')

@section('title', 'Nina.Sucks | Adult Microblogging Community')
@section('description', 'Adult microblogging platform: text, photos, video, quotes, links, and chats.')

@section('content')
  @include('partials.header')

  <!-- ============ HERO SLIDER ============ -->
  <section class="hero" id="hero" aria-roledescription="carousel" aria-label="Featured" tabindex="0">
    <div class="slides">
      @foreach(hero_slides() as $i => $s)
        @php $H = $i === 0 ? 'h1' : 'h2'; @endphp
        <article class="slide{{ $i === 0 ? ' active' : '' }}" style="--c:{{ $s['color'] }};--px:{{ $s['x'] }}%;--py:{{ $s['y'] }}%;--s:{{ $s['z'] }};--mpx:{{ $s['mx'] }}%;--mpy:{{ $s['my'] }}%;--ms:{{ $s['mz'] }}">
          <img class="bg" src="{{ asset('assets/' . $s['img']) }}" alt="{{ $s['alt'] }}" {{ $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"' }} decoding="async">
          <div class="copy">
            <{{ $H }}>{{ $s['title'] }}</{{ $H }}>
            <p>{{ $s['text'] }}</p>
            <div class="cta">
              @foreach($s['cta'] as $b)
                <a class="btn {{ $b['class'] }}" href="{{ str_starts_with($b['href'], '#') ? $b['href'] : url($b['href']) }}"{!! isset($b['modal']) ? ' data-modal="' . $b['modal'] . '"' : '' !!}>{{ $b['label'] }}</a>
              @endforeach
            </div>
          </div>
        </article>
      @endforeach
    </div>

    <div class="controls">
      <button class="sq" id="prev" type="button" aria-label="Previous slide">{!! icon('left') !!}</button>
      <div class="dots" role="tablist" aria-label="Choose slide">
        @foreach(hero_slides() as $i => $s)
          <button class="dot{{ $i === 0 ? ' active' : '' }}" type="button" role="tab" aria-label="Slide {{ $i + 1 }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}"><i></i></button>
        @endforeach
      </div>
      <button class="sq" id="next" type="button" aria-label="Next slide">{!! icon('right') !!}</button>
    </div>
  </section>

  <div class="layout">
    <main class="main" id="main">

      <!-- Featured blogs -->
      @if($featuredBlogs->isNotEmpty())
        <section class="block" aria-labelledby="feat-h">
          @php
            $featCount = count($featuredBlogs);
            $featRows = $featCount > 6 ? (int)ceil($featCount / 6) : 1;
            $featCols = $featCount > 6 ? (int)ceil($featCount / $featRows) : min(6, max(1, $featCount));
          @endphp
          <div class="block-head">
            <h2 id="feat-h">Featured blogs</h2>
            <a href="{{ route('featured.blogs') }}" style="color:var(--pink);font-weight:700;">Show all &rarr;</a>
          </div>
          <div class="featured" style="--cols: {{ $featCols }};">
            @foreach($featuredBlogs as $b)
              @php
                $coverImg = $b->cover_path ?? ($b['img'] ?? null);
                $hasCover = !empty($coverImg) && $coverImg !== 'hero.jpg';
                $blogColor = $b->color ?? ($b['color'] ?? '#ff2bd6');
              @endphp
              <a class="fb" href="{{ blog_url($b) }}" style="--c:{{ $blogColor }}">
                <span class="fb-cover{{ $hasCover ? ' crop' : ' ph' }}" style="{{ $hasCover ? crop_style() : 'background:linear-gradient(135deg, ' . $blogColor . ' 0%, #07030d 100%);' }}">
                  @if($hasCover)
                    <img src="{{ media_url($coverImg) }}" alt="" loading="lazy" decoding="async" onerror="this.style.display='none';this.parentElement.classList.add('ph');this.parentElement.style.background='linear-gradient(135deg, {{ $blogColor }} 0%, #07030d 100%)';">
                  @endif
                </span>
                <span class="fb-av">
                  @if(!empty($b->avatar_path ?? ($b['avatar'] ?? null)))
                    <img src="{{ media_url($b->avatar_path ?? ($b['avatar'] ?? null)) }}" alt="{{ $b->title ?? ($b['name'] ?? '') }}" loading="lazy">
                  @else
                    {{ initial($b->slug ?? ($b['name'] ?? 'B')) }}
                  @endif
                </span>
                <b>{{ $b->title ?? ($b['name'] ?? '') }} {!! badge($b->user ?? $b) !!}</b>
                <small>{{ $b->tagline ?? ('@' . ($b->slug ?? ($b['name'] ?? ''))) }}</small>
              </a>
            @endforeach
          </div>
        </section>
      @endif

      <!-- Newest posts -->
      <section class="block" aria-labelledby="feed-h">
        <div class="block-head">
          <h2 id="feed-h">Newest posts</h2>
          <div class="view-toggle" role="group" aria-label="Post layout">
            <button type="button" data-view="grid" class="{{ ($view ?? 'grid') === 'grid' ? 'is-on' : '' }}" aria-pressed="{{ ($view ?? 'grid') === 'grid' ? 'true' : 'false' }}" aria-label="Grid view" title="Grid view">{!! icon('grid') !!}<span>Grid</span></button>
            <button type="button" data-view="list" class="{{ ($view ?? 'grid') === 'list' ? 'is-on' : '' }}" aria-pressed="{{ ($view ?? 'grid') === 'list' ? 'true' : 'false' }}" aria-label="List view" title="List view">{!! icon('list') !!}<span>List</span></button>
          </div>
        </div>
        <p class="hint">Newest first. Keep scrolling for older posts.</p>

        <div class="filters" role="toolbar" aria-label="Filter posts by format">
          <a class="chip{{ ($type ?? 'all') === 'all' ? ' is-on' : '' }}" href="{{ url('/') }}" data-f="all">All</a>
          @foreach(post_types() as $key => $val)
            <a class="chip{{ ($type ?? 'all') === $key ? ' is-on' : '' }}" href="{{ url('/?type=' . $key) }}" data-f="{{ $key }}">{!! icon($val[1]) !!}{{ $val[0] }}</a>
          @endforeach
        </div>

        <div class="feed" id="feed" data-view="{{ $view ?? 'grid' }}" data-type="{{ $type ?? 'all' }}" data-next="{{ count($posts) }}" data-total="{{ $postsCount ?? count($posts) }}" aria-live="polite">
          @foreach($posts as $p)
            @include('components.card', ['p' => $p])
          @endforeach
        </div>
        <div class="sentinel" id="sentinel">
          <span class="loader" aria-hidden="true" style="display:none;"></span>
          <span class="s-text" style="display:none;">Loading older posts</span>
          <button type="button" class="btn btn-line s-btn" id="loadMoreBtn" style="padding:8px 18px;font-size:13px;">{!! icon('plus') !!}Load older posts</button>
        </div>
      </section>
    </main>

    <!-- ============ RIGHT RAIL ============ -->
    <aside class="rail" aria-label="Discover">
      <section class="box">
        <h3>Trending tags</h3>
        <div class="tags">
          @foreach($trendingTags ?? ['neon', 'cyberpunk', 'aesthetic', 'art'] as $tag)
            <a href="{{ route('trending', ['tag' => $tag]) }}">#{{ $tag }}</a>
          @endforeach
        </div>
      </section>

      <section class="box">
        <h3>Blogs to follow</h3>
        <ul class="people">
          @foreach($featuredBlogs->take(4) as $b)
            @php
              $isF = auth()->check() ? auth()->user()->follows()->where('blog_id', $b->id)->exists() : false;
            @endphp
            <li>
              <span class="av" style="--c:{{ $b->color ?? '#ff2bd6' }}">{{ initial($b->slug) }}</span>
              <div><b>{{ $b->slug }}</b><small>{{ $b->tagline ?? 'Creator blog' }}</small></div>
              <button class="follow{{ $isF ? ' on' : '' }}" type="button" data-blog="{{ $b->slug }}">{{ $isF ? 'Following' : 'Follow' }}</button>
            </li>
          @endforeach
        </ul>
      </section>

      @if(isset($activeAds) && $activeAds->count())
        <section class="box ads-section" style="border-color:rgba(255,43,214,0.35);">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            <h3 style="margin:0;font-size:13px;text-transform:uppercase;letter-spacing:0.05em;color:var(--pink);">Sponsored Ads</h3>
            <a href="{{ route('advertising') }}" style="font-size:11px;color:var(--mute);">Advertise &rarr;</a>
          </div>
          <div style="display:flex;flex-direction:column;gap:12px;">
            @foreach($activeAds as $ad)
              <div class="ad-card" style="background:rgba(255,255,255,0.02);border:1px solid var(--line);border-radius:4px;padding:10px;transition:border-color .2s;">
                @if(!empty($ad->image_url))
                  <a href="{{ $ad->link_url }}" target="_blank" rel="sponsored noopener" style="display:block;margin-bottom:8px;overflow:hidden;border-radius:2px;">
                    <img src="{{ media_url($ad->image_url) }}" alt="{{ $ad->title }}" style="width:100%;height:110px;object-fit:cover;">
                  </a>
                @endif
                <b style="display:block;font-size:13px;margin-bottom:3px;"><a href="{{ $ad->link_url }}" target="_blank" rel="sponsored noopener" style="color:var(--text);">{{ $ad->title }}</a></b>
                @if($ad->description)
                  <p style="font-size:12px;color:var(--mute);margin:0 0 6px;line-height:1.4;">{{ $ad->description }}</p>
                @endif
                <div style="display:flex;align-items:center;justify-content:space-between;font-size:11px;">
                  <span style="color:var(--yellow);font-weight:600;">Sponsored{{ $ad->sponsor_name ? ' by ' . $ad->sponsor_name : '' }}</span>
                  <a href="{{ $ad->link_url }}" target="_blank" rel="sponsored noopener" class="btn btn-line" style="padding:2px 8px;font-size:10px;">Visit &rarr;</a>
                </div>
              </div>
            @endforeach
          </div>
        </section>
      @endif

      <section class="box promo">
        <h3>Get your posts seen</h3>
        <p>Spend points on views or book a flat-rate spot on Explore.</p>
        <a class="btn btn-fill" href="{{ route('advertising') }}">Advertise</a>
      </section>

      <section class="box badges" aria-labelledby="badges-heading">
        <h3 id="badges-heading" class="badges-title">Badges</h3>
        <ul class="badges-list">
          @foreach(badge_legend() as $b)
            <li class="badge-item">
              <svg class="badge-ico badge-ico-{{ $b['badge'] }}" aria-hidden="true"><use href="#i-{{ $b['icon'] }}"/></svg>
              <span class="badge-label">{{ $b['text'] }}</span>
            </li>
          @endforeach
        </ul>
      </section>
    </aside>
  </div>
@endsection
