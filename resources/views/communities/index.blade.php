@extends('layouts.app')

@section('title', t('communities.title') . ' | ' . site_name())
@section('description', t('communities.lead'))

@section('content')
  @include('partials.header')
  <main class="page" id="main">
    <header class="page-hero">
      <h1>{{ t('communities.title') }}</h1>
      <p>{{ t('communities.lead') }}</p>
      @auth
        <a class="btn btn-fill" href="{{ route('dashboard.communities') }}">{{ t('communities.create') }}</a>
      @endauth
    </header>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px;">
      @forelse($communities as $community)
        <a href="{{ route('member-community.show', $community->slug) }}" style="display:block;padding:16px;border:1px solid var(--line);border-radius:12px;background:var(--paper);color:inherit;text-decoration:none;">
          <b style="color:{{ $community->color ?: '#ff2bd6' }}">{{ $community->title }}</b>
          <small style="display:block;color:var(--mute);margin:6px 0;">{{ $community->tagline }}</small>
          <span style="font-size:12px;">{{ $community->active_members_count }} {{ t('communities.members') }}</span>
        </a>
      @empty
        <p>{{ t('search.none') }}</p>
      @endforelse
    </div>
    <div style="margin-top:20px;">{{ $communities->links() }}</div>
  </main>
  @include('partials.footer')
@endsection
