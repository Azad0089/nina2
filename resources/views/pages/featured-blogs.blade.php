@extends('layouts.app')

@section('title', 'Featured & Promoted Creator Blogs | ' . site_name())
@section('description', 'Explore all spotlighted, featured, and promoted creator blogs across the community.')

@section('content')
  @include('partials.header')

  <main class="page" id="main">
    @include('partials.page-hero', [
      'hero' => [
        'title' => 'Featured Creator Blogs',
        'lead' => 'Discover verified community creators, spotlighted channels, and curated blogs.',
        'color' => '#ff2bd6'
      ]
    ])

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
      <div>
        <h2 class="sec-title" style="margin:0 0 4px;">All Spotlighted Blogs</h2>
        <p class="sec-sub" style="margin:0;">Showing {{ $featuredBlogs->firstItem() ?? 1 }} - {{ $featuredBlogs->lastItem() ?? $featuredBlogs->count() }} of {{ $featuredBlogs->total() }} blogs</p>
      </div>

      <div style="display:flex;align-items:center;gap:10px;">
        <a href="{{ route('advertising') }}" class="btn btn-fill btn-sm" style="background:linear-gradient(135deg,#ff2bd6,#19e3ff);color:#07030d;font-weight:800;">
          ✨ Feature Your Blog
        </a>
        <a href="{{ route('home') }}" class="btn btn-line btn-sm">
          &larr; Back to Explore
        </a>
      </div>
    </div>

    @if($featuredBlogs->isEmpty())
      <div class="panel" style="text-align:center;padding:50px 20px;color:var(--mute);">
        <p style="font-size:16px;">No featured blogs found at this moment.</p>
        <a href="{{ route('advertising') }}" class="btn btn-fill btn-sm" style="margin-top:14px;">Boost your blog now</a>
      </div>
    @else
      <div class="featured" style="--cols: 6; margin-bottom: 30px;">
        @foreach($featuredBlogs as $b)
          @php
            $coverImg = $b->cover_path ?? ($b['img'] ?? null);
            $hasCover = !empty($coverImg) && $coverImg !== 'hero.jpg';
            $blogColor = $b->color ?? ($b['color'] ?? '#ff2bd6');
            $followerCount = $b->followers()->count();
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
            <b>
              <span>{{ $b->title ?? ($b['name'] ?? '') }}</span>
              {!! badge($b->user ?? $b) !!}
            </b>
            <small>{{ $b->tagline ?? ('@' . ($b->slug ?? ($b['name'] ?? ''))) }}</small>
            
            <div style="margin-top:auto;padding:8px 12px 0;display:flex;align-items:center;justify-content:space-between;border-top:1px solid rgba(255,255,255,0.06);font-size:11px;color:var(--mute);">
              <span>{{ $followerCount }} {{ $followerCount === 1 ? 'follower' : 'followers' }}</span>
              <span style="color:var(--cyan);font-weight:700;">Visit &rarr;</span>
            </div>
          </a>
        @endforeach
      </div>

      <!-- Pagination -->
      <div style="margin-top:40px;display:flex;justify-content:center;">
        {{ $featuredBlogs->links() }}
      </div>
    @endif

    <!-- Call to action footer banner -->
    <div style="margin-top:60px;background:rgba(255,43,214,0.06);border:1px solid rgba(255,43,214,0.3);border-radius:16px;padding:30px;text-align:center;box-shadow:0 0 30px rgba(255,43,214,0.1);">
      <h3 style="font-size:20px;font-weight:900;margin:0 0 8px;color:#fff;">Want your blog showcased here?</h3>
      <p style="font-size:14px;color:#cbd5e1;max-width:560px;margin:0 auto 20px;">
        Book an Explore Spotlight or 30-Day Placement in the Advertising Market using your monthly activity reward points.
      </p>
      <a href="{{ route('advertising') }}" class="btn btn-fill" style="background:var(--pink);color:#fff;font-weight:800;padding:10px 24px;">
        Visit Ads &amp; Spotlights Market &rarr;
      </a>
    </div>
  </main>
@endsection
