@extends('layouts.app')

@section('title', 'Contact | ' . site_name())
@section('description', 'Contact ' . site_name() . ' support: questions, reports, appeals, verification, advertising and legal requests.')

@php
  $contactEmailForSchema = \App\Models\Setting::get('community_email', 'support@nina.sucks');
  $contactSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'ContactPage',
    'name' => ($title ?? 'Contact') . ' | ' . site_name(),
    'url' => url('/contact'),
    'description' => $lead ?? ('Contact ' . site_name() . ' support for questions, reports, appeals, verification, advertising, and legal requests.'),
    'mainEntity' => [
      '@type' => 'Organization',
      'name' => site_name(),
      'url' => url('/'),
      'email' => $contactEmailForSchema,
      'contactPoint' => [
        '@type' => 'ContactPoint',
        'contactType' => 'customer support',
        'email' => $contactEmailForSchema,
        'url' => url('/contact'),
        'availableLanguage' => 'English',
      ],
    ],
  ];
@endphp
@push('head')
  {!! schema_ld_tag($contactSchema) !!}
@endpush

@section('content')
  @include('partials.header')

  @php
    $topics = [
      'support'       => 'General support',
      'report'        => 'Report content or a blog',
      'appeal'        => 'Appeal a suspension or ban',
      'verification'  => 'Identity verification',
      'advertising'   => 'Advertising & promotion',
      'premium'       => 'Premium & donations',
      'legal'         => 'Legal, DMCA or privacy request',
      'other'         => 'Something else',
    ];
    $currentTopic = request('topic', 'support');
    if (!array_key_exists($currentTopic, $topics)) {
      $currentTopic = 'support';
    }
    $contactEmail = \App\Models\Setting::get('community_email', 'support@nina.sucks');
    $turnstileKey = \App\Models\Setting::get('turnstile_site_key', '0x4AAAAAAE8QOvWsSh5TL-MQ');
  @endphp

  <main class="page" id="main">
    @include('partials.page-hero', [
      'hero' => [
        'title' => $title ?? 'Contact us',
        'lead' => $lead ?? 'Questions, reports and appeals go straight to the site admin.',
        'color' => $color ?? '#19e3ff'
      ]
    ])

    @if(!empty($bodyHtml))
      <div class="panel" style="max-width:800px;margin:0 auto 20px;line-height:1.7;">
        {!! $bodyHtml !!}
      </div>
    @endif

    <div class="contact-grid">
      <section class="panel" aria-labelledby="cf-h">
        <h2 id="cf-h">Send a message</h2>

        @if(session('success'))
          <p class="form-msg ok" role="alert" style="padding:12px 16px;background:rgba(5,150,105,0.15);border:1px solid rgba(5,150,105,0.3);color:#34d399;border-radius:6px;margin-bottom:18px;font-size:14px;font-weight:600;">
            {{ session('success') }}
          </p>
        @endif

        @if($errors->any())
          <p class="form-msg error" role="alert" style="padding:12px 16px;background:rgba(225,29,72,0.15);border:1px solid rgba(225,29,72,0.3);color:#fb7185;border-radius:6px;margin-bottom:18px;font-size:14px;font-weight:600;">
            {{ $errors->first() }}
          </p>
        @endif

        <form method="post" action="{{ route('action.contact') }}">
          @csrf
          <div class="two">
            <label class="field"><span>Your name</span><input type="text" name="name" value="{{ old('name', auth()->check() ? auth()->user()->username : '') }}" autocomplete="name" required></label>
            <label class="field"><span>Your email</span><input type="email" name="email" value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}" autocomplete="email" required></label>
          </div>

          <label class="field"><span>Topic</span>
            <select name="topic">
              @foreach ($topics as $k => $label)
                <option value="{{ $k }}" {{ $k === $currentTopic ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </label>

          <label class="field"><span>Link to the post or blog <em>(for reports and appeals)</em></span>
            <input type="url" name="link" value="{{ old('link', request('link')) }}" placeholder="https://"></label>

          <label class="field"><span>Message</span>
            <textarea name="message" rows="6" required placeholder="Describe your question, request, or issue with as much detail as possible...">{{ old('message') }}</textarea></label>

          <div class="cf-turnstile" data-sitekey="{{ $turnstileKey }}" style="margin:14px 0;"></div>

          <button class="btn btn-fill" type="submit">Send message</button>
        </form>
      </section>

      <aside class="side-info" aria-label="Other ways to get help">
        <a class="tile" href="{{ url('/contact?topic=report') }}" style="--c:#ff1f4b">
          {!! icon('flag') !!}
          <div>
            <h3>Report content</h3>
            <p>Something breaks the rules? Tell us where to look.</p>
          </div>
        </a>

        <a class="tile" href="{{ url('/support') }}" style="--c:#19e3ff">
          {!! icon('help') !!}
          <div>
            <h3>Help center</h3>
            <p>Answers to common questions about blogs, premium and ads.</p>
          </div>
        </a>

        <a class="tile" href="mailto:{{ $contactEmail }}" style="--c:#ffd60a">
          {!! icon('mail') !!}
          <div>
            <h3>Email us</h3>
            <p>{{ $contactEmail }}</p>
          </div>
        </a>
      </aside>
    </div>
  </main>

  @include('partials.footer')
@endsection
