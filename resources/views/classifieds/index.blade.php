@extends('layouts.app')

@section('title', t('classifieds.title') . ' | ' . site_name())
@section('description', t('classifieds.lead'))

@section('content')
  @include('partials.header')
  <main class="page" id="main">
    <header class="page-hero">
      <h1>{{ t('classifieds.title') }}</h1>
      <p>{{ t('classifieds.lead') }}</p>
      @auth
        <a class="btn btn-fill" href="{{ route('dashboard.classifieds') }}">{{ t('classifieds.post') }}</a>
      @endauth
    </header>

    @if(!empty($tagCounts))
      <div style="margin-bottom:18px;">
        <h2 class="sec-title">{{ t('classifieds.tags') }}</h2>
        <a class="chip{{ $tag === '' ? ' is-on' : '' }}" href="{{ route('classifieds.index') }}">All</a>
        @foreach($tagCounts as $name => $count)
          <a class="chip{{ $tag === $name ? ' is-on' : '' }}" href="{{ route('classifieds.index', ['tag' => $name]) }}">#{{ $name }}</a>
        @endforeach
      </div>
    @endif

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px;">
      @forelse($ads as $ad)
        <a href="{{ route('classifieds.show', $ad->id) }}" style="display:flex;flex-direction:column;gap:8px;padding:12px;border:1px solid var(--line);border-radius:12px;background:var(--paper);color:inherit;text-decoration:none;">
          @if($ad->image_one)
            <img src="{{ media_url($ad->image_one) }}" alt="" style="width:100%;height:140px;object-fit:cover;border-radius:8px;">
          @endif
          <b>{{ $ad->title }}</b>
          <span style="color:var(--mute);font-size:13px;">{{ \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', $ad->description), 140) }}</span>
          <span style="font-size:12px;color:var(--pink);">
            @foreach(array_slice((array) $ad->tags, 0, 6) as $item)#{{ $item }} @endforeach
          </span>
        </a>
      @empty
        <p>{{ t('classifieds.none') }}</p>
      @endforelse
    </div>
    <div style="margin-top:20px;">{{ $ads->links() }}</div>
  </main>
  @include('partials.footer')
@endsection
