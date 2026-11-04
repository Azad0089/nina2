@extends('layouts.app')

@section('title', 'Trending on Nina.Sucks')
@section('description', 'Popular tags, trending creators, and most liked posts this week.')

@section('content')
  @include('partials.header')

  <main class="page" id="main">
    @include('partials.page-hero', [
      'hero' => [
        'title' => 'Trending now',
        'lead' => 'What everybody is tagging, following and liking this week.',
        'color' => '#ff1f4b'
      ]
    ])

    <h2 class="sec-title">Trending tags</h2>
    @if(!empty($selectedTag))
      <div style="margin-bottom:12px;display:flex;align-items:center;gap:10px;padding:8px 14px;background:rgba(25,227,255,0.08);border:1px solid rgba(25,227,255,0.3);border-radius:8px;font-size:13px;">
        <span style="color:var(--cyan);font-weight:700;">Filtered by tag: #{{ $selectedTag }}</span>
        <span style="color:var(--mute);">({{ $topPosts->count() }} {{ $topPosts->count() === 1 ? 'post' : 'posts' }})</span>
        <a href="{{ route('trending') }}" style="margin-left:auto;color:#ff2bd6;font-weight:700;text-decoration:none;font-size:12px;">✕ Clear filter</a>
      </div>
    @endif
    <div class="tag-cloud">
      @php
        $maxCount = !empty($tagCounts) ? max(array_values($tagCounts)) : 1;
      @endphp
      @foreach($tagCounts as $tag => $count)
        @php
          $lvl = 1 + (int)floor(($count / max(1, $maxCount)) * 3.99);
          $isActive = !empty($selectedTag) && $selectedTag === $tag;
        @endphp
        <a href="{{ route('trending', ['tag' => $tag]) }}" class="tc tc-{{ $lvl }}{{ $isActive ? ' tc-active' : '' }}" style="{{ $isActive ? 'background:var(--cyan);border-color:var(--cyan);color:#001318;font-weight:800;' : '' }}">#{{ $tag }} <small style="{{ $isActive ? 'color:#001318;font-weight:800;' : '' }}">{{ $count }}</small></a>
      @endforeach
    </div>

    @if($featuredBlogs->isNotEmpty())
      @php
        $featCount = count($featuredBlogs);
        $featRows = $featCount > 6 ? (int)ceil($featCount / 6) : 1;
        $featCols = $featCount > 6 ? (int)ceil($featCount / $featRows) : min(6, max(1, $featCount));
      @endphp
      <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:14px;border-bottom:1px solid var(--line);padding-bottom:10px;">
        <h2 class="sec-title" style="margin:0;">Popular &amp; Featured blogs</h2>
        <a href="{{ route('featured.blogs') }}" style="color:var(--pink);font-size:13.5px;font-weight:700;text-decoration:none;">Show all &rarr;</a>
      </div>
      <div class="featured" style="--cols: {{ $featCols }}; margin-bottom: 36px;">
        @foreach($featuredBlogs as $b)
          @php
            $coverImg = $b->cover_path ?? ($b['img'] ?? null);
            $hasCover = !empty($coverImg) && $coverImg !== 'hero.jpg';
            $blogColor = $b->color ?? ($b['color'] ?? '#ff2bd6');
          @endphp
          <a class="fb" href="{{ blog_url($b) }}" style="--c:{{ $blogColor }}">
            <span class="fb-cover{{ $hasCover ? ' crop' : ' ph' }}" style="{{ $hasCover ? crop_style() : 'background:linear-gradient(135deg, ' . $blogColor . ' 0%, #07030d 100%);' }}">
              @if($hasCover)
                <img src="{{ media_url($coverImg) }}" alt="" loading="lazy" onerror="this.style.display='none';this.parentElement.classList.add('ph');this.parentElement.style.background='linear-gradient(135deg, {{ $blogColor }} 0%, #07030d 100%)';">
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
    @endif

    <h2 class="sec-title">{{ !empty($selectedTag) ? 'Posts tagged #' . $selectedTag : 'Most liked posts' }}</h2>
    <div class="feed" id="feed" data-view="grid">
      @forelse($topPosts as $p)
        @include('components.card', ['p' => $p])
      @empty
        <p style="width:100%;text-align:center;padding:40px 20px;color:var(--mute);">No posts found tagged with #{{ $selectedTag ?? '' }}. <a href="{{ route('trending') }}" style="color:var(--pink);">View all posts</a></p>
      @endforelse
    </div>
  </main>
@endsection
