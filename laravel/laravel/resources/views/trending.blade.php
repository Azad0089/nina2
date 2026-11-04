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
    <div class="tag-cloud">
      @foreach(['aesthetic' => 120, 'late-night' => 95, 'cyberpunk' => 84, 'cosplay' => 67, 'neon' => 54, 'glamour' => 45, 'synthwave' => 38, 'streetstyle' => 31] as $tag => $count)
        <a href="{{ route('trending', ['tag' => $tag]) }}" class="tc tc-2">#{{ $tag }} <small>{{ $count }}</small></a>
      @endforeach
    </div>

    <h2 class="sec-title">Popular blogs</h2>
    <div class="featured">
      @foreach($featuredBlogs as $b)
        <a class="fb" href="{{ route('blog.show', ['slug' => $b->slug]) }}" style="--c:{{ $b->color ?? '#ff2bd6' }}">
          <span class="fb-cover crop" style="{{ crop_style() }}"><img src="{{ media_url($b->cover_path ?: 'hero.jpg') }}" alt="" loading="lazy"></span>
          <span class="fb-av">{{ initial($b->slug) }}</span>
          <b>{{ $b->title }} {!! badge('pro') !!}</b>
          <small>{{ $b->tagline ?? ('@' . $b->slug) }}</small>
        </a>
      @endforeach
    </div>

    <h2 class="sec-title">Most liked posts</h2>
    <div class="grid-static" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;">
      @foreach($topPosts as $p)
        @include('components.card', ['p' => $p])
      @endforeach
    </div>
  </main>
@endsection
