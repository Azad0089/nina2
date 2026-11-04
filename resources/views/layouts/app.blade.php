<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script>
    (function() {
      try {
        var savedTheme = localStorage.getItem('nina_theme');
        var theme = savedTheme ? savedTheme : 'dark';
        document.documentElement.setAttribute('data-theme', theme);
        if (theme === 'light') {
          document.documentElement.classList.add('light-theme');
          document.documentElement.classList.remove('dark');
        } else {
          document.documentElement.classList.remove('light-theme');
          document.documentElement.classList.add('dark');
        }
      } catch (e) {}
    })();
    window.toggleNinaTheme = function() {
      var current = document.documentElement.getAttribute('data-theme') || 'dark';
      var next = current === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', next);
      if (next === 'light') {
        document.documentElement.classList.add('light-theme');
        document.documentElement.classList.remove('dark');
      } else {
        document.documentElement.classList.remove('light-theme');
        document.documentElement.classList.add('dark');
      }
      try {
        localStorage.setItem('nina_theme', next);
      } catch (e) {}
      var metas = document.querySelectorAll('meta[name="theme-color"]');
      metas.forEach(function(m) { m.setAttribute('content', next === 'light' ? '#f8fafc' : '#07030d'); });
      window.dispatchEvent(new CustomEvent('nina-theme-changed', { detail: { theme: next } }));
    };
  </script>
  <title>@yield('title', site_name() . ' | Adult Microblogging Community')</title>
  <meta name="max-image-mb" content="{{ $siteSettings['max_image_mb'] ?? 5 }}">
  <script>window.MAX_IMAGE_MB = {{ (float)($siteSettings['max_image_mb'] ?? 5) }};</script>
  <meta name="description" content="@yield('description', $siteSettings['meta_description'] ?? 'Adult microblogging platform: text, photos, video, quotes, links, and chats.')">
  <meta name="robots" content="index, follow, max-image-preview:large">
  <meta name="theme-color" content="#07030d">

  <!-- OpenGraph & Social Cards -->
  <meta property="og:site_name" content="{{ site_name() }}">
  <meta property="og:title" content="@yield('title', site_name())">
  <meta property="og:description" content="@yield('description', $siteSettings['meta_description'] ?? 'Adult microblogging platform')">
  <meta property="og:type" content="website">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:image" content="{{ asset('assets/hero.jpg') }}">
  <meta name="twitter:card" content="summary_large_image">

  <link rel="canonical" href="{{ url()->current() }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ !empty($siteSettings['site_favicon']) ? (str_starts_with($siteSettings['site_favicon'], 'http') ? $siteSettings['site_favicon'] : asset($siteSettings['site_favicon'])) : asset('assets/favicon-32.png') }}">
  <link rel="icon" type="image/png" sizes="192x192" href="{{ !empty($siteSettings['site_favicon']) ? (str_starts_with($siteSettings['site_favicon'], 'http') ? $siteSettings['site_favicon'] : asset($siteSettings['site_favicon'])) : asset('assets/favicon-192.png') }}">
  <link rel="apple-touch-icon" href="{{ !empty($siteSettings['site_favicon']) ? (str_starts_with($siteSettings['site_favicon'], 'http') ? $siteSettings['site_favicon'] : asset($siteSettings['site_favicon'])) : asset('assets/apple-touch-icon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@500;700;900&family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ file_exists(public_path('css/style.css')) ? filemtime(public_path('css/style.css')) : time() }}">
  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  @endif

  <!-- Meta Verification Tags (Google Search Console, Bing, Pinterest) -->
  @if(!empty($siteSettings['meta_verification']))
    {!! $siteSettings['meta_verification'] !!}
  @endif

  <!-- Dynamic Theme Colors & Critical Light Mode Styles -->
  <style id="nina-theme-critical">
    :root {
      --primary: {{ $siteSettings['primary_color'] ?? '#ff2bd6' }};
      --secondary: {{ $siteSettings['secondary_color'] ?? '#19e3ff' }};
    }
    html[data-theme="light"],
    html.light-theme,
    [data-theme="light"] {
      --black: #f8fafc;
      --ink: #ffffff;
      --ink-2: #f1f5f9;
      --line: rgba(15, 23, 42, 0.12);
      --line-hot: rgba(225, 29, 72, 0.7);
      --text: #0f172a;
      --mute: #64748b;
      --paper: #ffffff;
      --cyan: #0284c7;
      color-scheme: light;
    }

    html[data-theme="light"] body,
    html.light-theme body,
    body[data-theme="light"] {
      background: #f8fafc !important;
      color: #0f172a !important;
    }

    /* Left Sidebar (.side) */
    html[data-theme="light"] .side,
    html.light-theme .side {
      background: #ffffff !important;
      border-right: 1px solid rgba(15, 23, 42, 0.12) !important;
      box-shadow: 2px 0 20px rgba(15, 23, 42, 0.04) !important;
    }
    html[data-theme="light"] .side-top,
    html.light-theme .side-top,
    html[data-theme="light"] .side-nav,
    html.light-theme .side-nav,
    html[data-theme="light"] .side-auth,
    html.light-theme .side-auth,
    html[data-theme="light"] .side-foot,
    html.light-theme .side-foot {
      background: transparent !important;
    }
    html[data-theme="light"] .nav-link,
    html.light-theme .nav-link {
      color: #0f172a !important;
      background: transparent !important;
    }
    html[data-theme="light"] .nav-link .ic,
    html.light-theme .nav-link .ic {
      color: #475569 !important;
    }
    html[data-theme="light"] .nav-link:hover,
    html.light-theme .nav-link:hover {
      color: #e11d48 !important;
      background: #fdf2f8 !important;
      border-color: rgba(225, 29, 72, 0.3) !important;
    }
    html[data-theme="light"] .nav-link.is-active,
    html.light-theme .nav-link.is-active {
      color: #0284c7 !important;
      background: rgba(2, 132, 199, 0.08) !important;
      border-color: #0284c7 !important;
      font-weight: 700 !important;
    }
    html[data-theme="light"] .side-foot,
    html.light-theme .side-foot,
    html[data-theme="light"] .side-foot a,
    html.light-theme .side-foot a,
    html[data-theme="light"] .side-foot span,
    html.light-theme .side-foot span {
      color: #64748b !important;
    }
    html[data-theme="light"] .side-foot a:hover,
    html.light-theme .side-foot a:hover {
      color: #e11d48 !important;
    }

    /* Topbar (.top) */
    html[data-theme="light"] .top,
    html.light-theme .top {
      background: rgba(255, 255, 255, 0.96) !important;
      border-bottom: 1px solid rgba(15, 23, 42, 0.12) !important;
      box-shadow: 0 2px 14px rgba(15, 23, 42, 0.04) !important;
    }
    html[data-theme="light"] .top-logo,
    html.light-theme .top-logo,
    html[data-theme="light"] .top-logo span,
    html.light-theme .top-logo span {
      color: #0f172a !important;
    }
    html[data-theme="light"] .search,
    html.light-theme .search {
      background: #f1f5f9 !important;
      border: 1px solid rgba(15, 23, 42, 0.14) !important;
    }
    html[data-theme="light"] .search input,
    html.light-theme .search input {
      color: #0f172a !important;
    }
    html[data-theme="light"] .search .ic,
    html.light-theme .search .ic {
      color: #64748b !important;
    }

    /* Badges & Right Rail Boxes (.box, .box.badges) */
    html[data-theme="light"] .box,
    html.light-theme .box,
    html[data-theme="light"] .box.badges,
    html.light-theme .box.badges,
    html[data-theme="light"] .panel,
    html.light-theme .panel,
    html[data-theme="light"] .card,
    html.light-theme .card,
    html[data-theme="light"] .fb,
    html.light-theme .fb,
    html[data-theme="light"] .tile,
    html.light-theme .tile {
      background: #ffffff !important;
      border: 1px solid rgba(15, 23, 42, 0.12) !important;
      box-shadow: 0 2px 12px rgba(15, 23, 42, 0.04) !important;
      color: #0f172a !important;
    }
    html[data-theme="light"] .box h3,
    html.light-theme .box h3,
    html[data-theme="light"] .box.badges h3,
    html.light-theme .box.badges h3,
    html[data-theme="light"] .badges-title,
    html.light-theme .badges-title,
    html[data-theme="light"] .badge-label,
    html.light-theme .badge-label,
    html[data-theme="light"] .badge-item,
    html.light-theme .badge-item,
    html[data-theme="light"] .people b,
    html.light-theme .people b,
    html[data-theme="light"] .block-head h2,
    html.light-theme .block-head h2,
    html[data-theme="light"] .c-username,
    html.light-theme .c-username,
    html[data-theme="light"] .c-who b,
    html.light-theme .c-who b,
    html[data-theme="light"] .c-who b a,
    html.light-theme .c-who b a,
    html[data-theme="light"] .blog-head h1,
    html.light-theme .blog-head h1,
    html[data-theme="light"] .prose h2,
    html.light-theme .prose h2,
    html[data-theme="light"] .prose strong,
    html.light-theme .prose strong,
    html[data-theme="light"] .rich-body h1,
    html.light-theme .rich-body h1,
    html[data-theme="light"] .rich-body h2,
    html.light-theme .rich-body h2 {
      color: #0f172a !important;
      text-shadow: none !important;
    }
    html[data-theme="light"] .people small,
    html.light-theme .people small,
    html[data-theme="light"] .c-type,
    html.light-theme .c-type,
    html[data-theme="light"] .c-who small,
    html.light-theme .c-who small,
    html[data-theme="light"] .c-meta-row,
    html.light-theme .c-meta-row,
    html[data-theme="light"] .c-body p,
    html.light-theme .c-body p,
    html[data-theme="light"] .prose p,
    html.light-theme .prose p,
    html[data-theme="light"] .prose li,
    html.light-theme .prose li,
    html[data-theme="light"] .rich-body p,
    html.light-theme .rich-body p,
    html[data-theme="light"] .blog-profile p,
    html.light-theme .blog-profile p,
    html[data-theme="light"] .faq p,
    html.light-theme .faq p {
      color: #334155 !important;
    }
    html[data-theme="light"] .people li,
    html.light-theme .people li {
      border-top-color: rgba(15, 23, 42, 0.08) !important;
    }

    /* Promo Box */
    html[data-theme="light"] .box.promo,
    html.light-theme .box.promo {
      background: linear-gradient(140deg, rgba(255, 43, 214, 0.08), rgba(138, 61, 255, 0.05) 60%, #ffffff) !important;
      border-color: rgba(255, 43, 214, 0.4) !important;
    }
    html[data-theme="light"] .box.promo p,
    html.light-theme .box.promo p {
      color: #334155 !important;
    }

    /* Buttons & Auth User Box */
    html[data-theme="light"] .theme-toggle-btn,
    html.light-theme .theme-toggle-btn,
    html[data-theme="light"] .side-auth > div,
    html.light-theme .side-auth > div {
      background: #f1f5f9 !important;
      border: 1px solid rgba(15, 23, 42, 0.12) !important;
      color: #0f172a !important;
    }
    html[data-theme="light"] .theme-current-name,
    html.light-theme .theme-current-name {
      background: #e2e8f0 !important;
      color: #475569 !important;
    }
    html[data-theme="light"] .btn-ghost,
    html.light-theme .btn-ghost {
      background: #f1f5f9 !important;
      border: 1px solid rgba(15, 23, 42, 0.18) !important;
      color: #0f172a !important;
    }
    html[data-theme="light"] .btn-ghost:hover,
    html.light-theme .btn-ghost:hover {
      background: #0f172a !important;
      color: #ffffff !important;
    }
    html[data-theme="light"] .btn-line,
    html.light-theme .btn-line {
      border-color: #0284c7 !important;
      color: #0284c7 !important;
    }

    /* Cookie Banner & Modals */
    html[data-theme="light"] .cookie,
    html.light-theme .cookie,
    html[data-theme="light"] .m-box,
    html.light-theme .m-box,
    html[data-theme="light"] .gate-box,
    html.light-theme .gate-box {
      background: #ffffff !important;
      border-color: rgba(15, 23, 42, 0.15) !important;
      box-shadow: 0 16px 50px rgba(15, 23, 42, 0.15) !important;
      color: #0f172a !important;
    }
    html[data-theme="light"] .cookie p,
    html.light-theme .cookie p {
      color: #475569 !important;
    }
  </style>

  <!-- Cloudflare Turnstile CAPTCHA JS -->
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

  <!-- Sitewide Head Scripts & Analytics Injected via Admin Control Panel -->
  @if(!empty($siteSettings['head_scripts']))
    {!! $siteSettings['head_scripts'] !!}
  @endif

  @if(!empty($siteSettings['custom_schema']))
    {!! custom_schema_markup($siteSettings['custom_schema']) !!}
  @endif

  @stack('head')
  @stack('styles')
</head>
<body>
  @include('partials.icons')

  <div class="wrap">
    @yield('content')
  </div>

  @include('partials.gate')
  @include('partials.modals')

  <script>
  window.NINA = {
    base: '{{ url('') }}',
    csrf: '{{ csrf_token() }}',
    loggedIn: {{ auth()->check() ? 'true' : 'false' }},
    loginUrl: '{{ route('login') }}'
  };
  @if(!empty($open_modal))
    document.addEventListener('DOMContentLoaded', function() {
      const m = document.querySelector('.modal[data-name="{{ $open_modal }}"]');
      if (m) {
        m.hidden = false;
        document.body.classList.add('locked');
      }
    });
  @endif
  </script>
  <script src="{{ asset('js/main.js') }}"></script>
  @stack('scripts')
</body>
</html>
