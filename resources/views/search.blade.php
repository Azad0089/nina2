@extends('layouts.app')

@section('title', t('search.title') . ' | ' . site_name())
@section('description', t('search.placeholder'))

@section('content')
  @include('partials.header')
  <main class="page" id="main">
    <header class="page-hero">
      <h1>{{ t('search.title') }}</h1>
      <p>{{ $query !== '' ? '“' . $query . '”' : t('search.empty') }}</p>
    </header>

    @if($query === '')
      <p class="fine">{{ t('search.empty') }}</p>
    @else
      <section style="margin-bottom:28px;">
        <h2 class="sec-title">{{ t('search.tags') }}</h2>
        @forelse($tags as $tag => $count)
          <a class="chip" href="{{ route('search', ['q' => $tag]) }}">#{{ $tag }} <small>{{ $count }}</small></a>
        @empty
          <p>{{ t('search.none') }}</p>
        @endforelse
      </section>

      <section style="margin-bottom:28px;">
        <h2 class="sec-title">{{ t('search.blogs') }}</h2>
        @forelse($blogs as $blog)
          <a href="{{ blog_url($blog) }}" style="display:block;padding:12px 0;border-bottom:1px solid var(--line);color:inherit;text-decoration:none;">
            <b>@{{ $blog->slug }}</b> · {{ $blog->title }}
            @if($blog->tagline)<small style="display:block;color:var(--mute);">{{ $blog->tagline }}</small>@endif
          </a>
        @empty
          <p>{{ t('search.none') }}</p>
        @endforelse
        <div style="margin-top:12px;">{{ $blogs->links() }}</div>
      </section>

      <section>
        <h2 class="sec-title">{{ t('search.posts') }}</h2>
        <div class="feed">
          @forelse($posts as $p)
            @include('components.card', ['p' => $p])
          @empty
            <p>{{ t('search.none') }}</p>
          @endforelse
        </div>
        <div style="margin-top:16px;">{{ $posts->links() }}</div>
      </section>
    @endif
  </main>
  @include('partials.footer')
@endsection
