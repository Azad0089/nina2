@extends('layouts.app')

@section('title', 'Help Center & Support | Nina.Sucks')

@section('content')
  @include('partials.header')

  <main class="page" id="main">
    @include('partials.page-hero', [
      'hero' => [
        'title' => $title ?? 'Help Center',
        'lead' => $lead ?? 'Quick answers. Cannot find yours? Ask us.',
        'color' => $color ?? '#19e3ff'
      ]
    ])

    <div class="tiles tiles-3">
      <a class="tile" href="{{ route('rules') }}" style="--c:#ffd60a">{!! icon('shield') !!}<h3>Community Rules</h3><p>What is allowed and what is not.</p></a>
      <a class="tile" href="{{ route('contact', ['topic' => 'report']) }}" style="--c:#ff1f4b">{!! icon('flag') !!}<h3>Report content</h3><p>Flag a post, blog or account.</p></a>
      <a class="tile" href="{{ route('contact') }}" style="--c:#19e3ff">{!! icon('mail') !!}<h3>Contact support</h3><p>Talk to a real person.</p></a>
    </div>

    {!! $bodyHtml ?? '' !!}
  </main>
@endsection
