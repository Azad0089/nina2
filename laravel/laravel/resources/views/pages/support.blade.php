@extends('layouts.app')

@section('title', 'Help Center & Support | Nina.Sucks')

@section('content')
  @include('partials.header')

  <main class="page" id="main">
    @include('partials.page-hero', [
      'hero' => [
        'title' => 'Help Center',
        'lead' => 'Quick answers. Cannot find yours? Ask us.',
        'color' => '#19e3ff'
      ]
    ])

    <div class="tiles tiles-3">
      <a class="tile" href="{{ route('rules') }}" style="--c:#ffd60a">{!! icon('shield') !!}<h3>Community Rules</h3><p>What is allowed and what is not.</p></a>
      <a class="tile" href="{{ route('contact', ['topic' => 'report']) }}" style="--c:#ff1f4b">{!! icon('flag') !!}<h3>Report content</h3><p>Flag a post, blog or account.</p></a>
      <a class="tile" href="{{ route('contact') }}" style="--c:#19e3ff">{!! icon('mail') !!}<h3>Contact support</h3><p>Talk to a real person.</p></a>
    </div>

    <section class="faq-group">
      <h2 class="sec-title">Getting started</h2>
      <details class="faq"><summary>How do I start a blog?</summary><p>Press Sign up, choose a blog name and confirm you are 18 or older. Your first blog is created automatically.</p></details>
      <details class="faq"><summary>What post formats can I use?</summary><p>Text, photo, video, quote, link, and chat.</p></details>
    </section>

    <section class="faq-group">
      <h2 class="sec-title">Design &amp; Customization</h2>
      <details class="faq"><summary>Can I customize my blog colors?</summary><p>Yes, visit Blog settings in your Dashboard to change theme colors, avatars, and cover photos.</p></details>
    </section>
  </main>
@endsection
