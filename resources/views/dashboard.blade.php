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
      window.dispatchEvent(new CustomEvent('nina-theme-changed', { detail: { theme: next } }));
    };
  </script>
  <title>{{ $title ?? ucfirst($section ?? 'Dashboard') }} &middot; Creator Studio | {{ site_name() }}</title>
  <meta name="max-image-mb" content="{{ $settings['max_image_mb'] ?? 5 }}">
  <meta name="max-text-length" content="{{ $settings['post_max_text_length'] ?? 10000 }}">
  <meta name="max-caption-length" content="{{ $settings['post_max_caption_length'] ?? 450 }}">
  <script>window.MAX_IMAGE_MB = {{ (float)($settings['max_image_mb'] ?? 5) }};</script>
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon-32.png') }}">
  <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/favicon-192.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@500;700;900&family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">

  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  @endif

  <!-- Tailwind CSS Engine with Custom Platform Theme Tokens -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            brand: {
              pink: '{{ $settings['primary_color'] ?? '#ff2bd6' }}',
              cyan: '{{ $settings['secondary_color'] ?? '#19e3ff' }}',
              dark: '#07030d',
              surface: '#0f0716',
              surfaceAlt: '#170a22',
              border: 'rgba(255, 255, 255, 0.1)',
            }
          },
          fontFamily: {
            display: ['Unbounded', 'sans-serif'],
            sans: ['Figtree', 'system-ui', 'sans-serif'],
          }
        }
      }
    }
  </script>

  <style>
    /* Platform icons and badges styling */
    svg.ic {
      display: inline-block;
      vertical-align: middle;
      width: 1.15rem;
      height: 1.15rem;
      stroke: currentColor;
      fill: none;
      stroke-width: 2;
      flex-shrink: 0;
    }
    .badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.15rem 0.4rem;
      border-radius: 9999px;
      font-size: 0.7rem;
      font-weight: 700;
      margin-left: 0.25rem;
    }
    .badge.b-pro { background-color: #2563eb; color: #ffffff; }
    .badge.b-id { background-color: #059669; color: #ffffff; }
    .badge.b-vip { background-color: #d97706; color: #ffffff; }
    .badge svg.ic { width: 0.75rem; height: 0.75rem; }

    /* Custom scrollbars */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #07030d; }
    ::-webkit-scrollbar-thumb { background: #231633; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #3c2457; }

    /* =========================================================
       CREATOR STUDIO LIGHT THEME OVERRIDES
       ========================================================= */
    html[data-theme="light"] body,
    html.light-theme body {
      background-color: #f8fafc !important;
      color: #0f172a !important;
    }
    html[data-theme="light"] aside,
    html.light-theme aside {
      background-color: #ffffff !important;
      border-color: #e2e8f0 !important;
      box-shadow: 2px 0 20px rgba(15, 23, 42, 0.04) !important;
    }
    html[data-theme="light"] header,
    html.light-theme header {
      background-color: rgba(255, 255, 255, 0.95) !important;
      border-color: #e2e8f0 !important;
      box-shadow: 0 2px 14px rgba(15, 23, 42, 0.04) !important;
    }
    html[data-theme="light"] .bg-\[\#07030d\],
    html.light-theme .bg-\[\#07030d\] {
      background-color: #f8fafc !important;
    }
    html[data-theme="light"] .bg-\[\#0c0514\],
    html.light-theme .bg-\[\#0c0514\],
    html[data-theme="light"] .bg-\[\#0f0716\],
    html.light-theme .bg-\[\#0f0716\],
    html[data-theme="light"] .bg-\[\#170a22\],
    html.light-theme .bg-\[\#170a22\] {
      background-color: #ffffff !important;
      border-color: #e2e8f0 !important;
    }
    html[data-theme="light"] .text-white,
    html.light-theme .text-white {
      color: #0f172a !important;
    }
    html[data-theme="light"] .text-slate-100,
    html.light-theme .text-slate-100,
    html[data-theme="light"] .text-slate-200,
    html.light-theme .text-slate-200 {
      color: #1e293b !important;
    }
    html[data-theme="light"] .text-slate-300,
    html.light-theme .text-slate-300,
    html[data-theme="light"] .text-slate-400,
    html.light-theme .text-slate-400 {
      color: #64748b !important;
    }
    html[data-theme="light"] .border-white\/10,
    html.light-theme .border-white\/10,
    html[data-theme="light"] .border-white\/5,
    html.light-theme .border-white\/5 {
      border-color: #e2e8f0 !important;
    }
    html[data-theme="light"] .rich-editor,
    html.light-theme .rich-editor {
      background: #ffffff !important;
      border-color: #cbd5e1 !important;
      box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05) !important;
    }
    html[data-theme="light"] .editor-toolbar,
    html.light-theme .editor-toolbar {
      background: #f8fafc !important;
      border-bottom-color: #e2e8f0 !important;
    }
    html[data-theme="light"] .editor-select,
    html.light-theme .editor-select {
      background: #ffffff !important;
      border-color: #cbd5e1 !important;
      color: #0f172a !important;
    }
    html[data-theme="light"] .editor-select option,
    html.light-theme .editor-select option {
      background: #ffffff !important;
      color: #0f172a !important;
    }
    html[data-theme="light"] .editor-content,
    html.light-theme .editor-content {
      color: #0f172a !important;
    }
    html[data-theme="light"] input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]),
    html.light-theme input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]),
    html[data-theme="light"] select,
    html.light-theme select,
    html[data-theme="light"] textarea,
    html.light-theme textarea {
      background-color: #ffffff !important;
      border-color: #cbd5e1 !important;
      color: #0f172a !important;
    }
    html[data-theme="light"] input::placeholder,
    html.light-theme input::placeholder,
    html[data-theme="light"] textarea::placeholder,
    html.light-theme textarea::placeholder {
      color: #94a3b8 !important;
    }

    /* =========================================================
       RICH TEXT EDITOR STYLING FOR CREATOR STUDIO
       ========================================================= */
    .rich-editor {
      display: flex !important;
      flex-direction: column !important;
      border: 1px solid rgba(255, 255, 255, 0.12) !important;
      background: #0f0716 !important;
      border-radius: 0.75rem !important;
      overflow: hidden !important;
      transition: all 0.2s ease-in-out !important;
      box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.5) !important;
    }
    .rich-editor:focus-within {
      border-color: #ff2bd6 !important;
      box-shadow: 0 0 20px rgba(255, 43, 214, 0.25), 0 4px 20px -2px rgba(0, 0, 0, 0.5) !important;
    }
    .editor-toolbar {
      display: flex !important;
      flex-wrap: wrap !important;
      align-items: center !important;
      gap: 0.25rem !important;
      padding: 0.5rem 0.75rem !important;
      background: rgba(255, 255, 255, 0.03) !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    }
    .editor-toolbar .tb-group {
      display: inline-flex !important;
      align-items: center !important;
      gap: 2px !important;
    }
    .editor-toolbar .tb-sep {
      width: 1px !important;
      height: 22px !important;
      background: rgba(255, 255, 255, 0.1) !important;
      margin: 0 0.35rem !important;
    }
    .editor-btn {
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      min-width: 32px !important;
      height: 32px !important;
      padding: 0 6px !important;
      background: transparent !important;
      border: 1px solid transparent !important;
      border-radius: 6px !important;
      color: #94a3b8 !important;
      font-size: 13px !important;
      font-weight: 600 !important;
      cursor: pointer !important;
      transition: all 0.15s ease !important;
      user-select: none !important;
    }
    .editor-btn:hover {
      background: rgba(255, 255, 255, 0.08) !important;
      border-color: rgba(255, 255, 255, 0.15) !important;
      color: #ffffff !important;
    }
    .editor-btn.is-active {
      background: rgba(255, 43, 214, 0.2) !important;
      border-color: #ff2bd6 !important;
      color: #ff2bd6 !important;
    }
    .editor-btn svg {
      width: 16px !important;
      height: 16px !important;
      stroke: currentColor !important;
      fill: none !important;
      stroke-width: 2 !important;
      display: inline-block !important;
    }
    .editor-select {
      height: 32px !important;
      background: #170a22 !important;
      border: 1px solid rgba(255, 255, 255, 0.15) !important;
      color: #f1f5f9 !important;
      border-radius: 6px !important;
      padding: 0 8px !important;
      font-size: 12px !important;
      font-weight: 600 !important;
      cursor: pointer !important;
      outline: none !important;
      transition: border-color 0.15s !important;
    }
    .editor-select:focus {
      border-color: #ff2bd6 !important;
    }
    .editor-select option {
      background: #170a22 !important;
      color: #f1f5f9 !important;
    }
    .editor-content {
      min-height: 220px !important;
      max-height: 520px !important;
      overflow-y: auto !important;
      padding: 16px 18px !important;
      color: #f8fafc !important;
      font-size: 15px !important;
      line-height: 1.7 !important;
      outline: none !important;
      background: transparent !important;
      word-break: break-word !important;
    }
    .editor-content:empty::before {
      content: attr(data-placeholder) !important;
      color: #64748b !important;
      pointer-events: none !important;
      display: block !important;
    }
    .editor-footer {
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
      padding: 8px 14px !important;
      background: rgba(0, 0, 0, 0.25) !important;
      border-top: 1px solid rgba(255, 255, 255, 0.05) !important;
      font-size: 11px !important;
      color: #64748b !important;
    }
    .editor-source {
      width: 100% !important;
      min-height: 220px !important;
      padding: 16px 18px !important;
      background: #050208 !important;
      color: #19e3ff !important;
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
      font-size: 13px !important;
      line-height: 1.6 !important;
      border: none !important;
      outline: none !important;
      resize: vertical !important;
      display: none;
    }

    /* Content Typography inside editor & cards */
    .editor-content p, .rich-body p { margin-bottom: 0.85em; }
    .editor-content p:last-child, .rich-body p:last-child { margin-bottom: 0; }
    .editor-content h2, .rich-body h2 { font-size: 1.35rem; font-weight: 700; color: #fff; margin: 1.1em 0 0.4em; }
    .editor-content h3, .rich-body h3 { font-size: 1.15rem; font-weight: 700; color: #ff2bd6; margin: 0.9em 0 0.3em; }
    .editor-content blockquote, .rich-body blockquote { margin: 1em 0; padding: 0.65rem 1rem; border-left: 3px solid #ff2bd6; background: rgba(255, 43, 214, 0.08); border-radius: 0 6px 6px 0; color: #fff; font-style: italic; }
    .editor-content ul, .rich-body ul { list-style: disc inside; margin: 0.6em 0; padding-left: 0.5rem; }
    .editor-content ol, .rich-body ol { list-style: decimal inside; margin: 0.6em 0; padding-left: 0.5rem; }
    .editor-content li, .rich-body li { margin-bottom: 0.25rem; }
    .editor-content a, .rich-body a { color: #19e3ff; text-decoration: underline; text-underline-offset: 3px; }
    .editor-content code, .rich-body code { padding: 2px 6px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 4px; font-family: monospace; font-size: 0.9em; color: #ffd60a; }
    .editor-content pre, .rich-body pre { padding: 12px 14px; background: #050208; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 6px; overflow-x: auto; margin: 0.8em 0; color: #19e3ff; }
    .editor-content hr, .rich-body hr { border: none; height: 1px; background: rgba(255, 255, 255, 0.12); margin: 1.5em 0; }
    .editor-content mark, .rich-body mark { background: rgba(255, 214, 10, 0.35); color: #fff; padding: 1px 4px; border-radius: 3px; }
  </style>
</head>
<body class="bg-[#07030d] text-slate-100 font-sans antialiased min-h-screen flex flex-col selection:bg-pink-500 selection:text-white">
  @include('partials.icons')

  <div class="flex-1 flex flex-col lg:flex-row min-h-screen">

    <!-- DESKTOP / MOBILE SIDEBAR NAVIGATION -->
    <aside class="w-full lg:w-72 bg-[#0c0514] border-b lg:border-b-0 lg:border-r border-white/10 flex-shrink-0 flex flex-col justify-between p-4 lg:p-6 lg:sticky lg:top-0 lg:h-screen lg:overflow-y-auto">
      <div>
        <!-- Brand Header -->
        <a href="{{ url('/') }}" class="flex items-center gap-3 px-2 py-3 mb-6 rounded-xl hover:bg-white/5 transition group">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-pink-500 to-cyan-400 p-0.5 shadow-lg shadow-pink-500/20 group-hover:scale-105 transition-transform flex items-center justify-center">
            <div class="w-full h-full bg-[#0c0514] rounded-[10px] flex items-center justify-center text-pink-400 font-display font-black text-sm">
              N
            </div>
          </div>
          <div>
            <div class="font-display font-bold text-base tracking-tight text-white flex items-center gap-1.5">
              <span>{{ site_name() }}</span>
              <span class="text-[10px] bg-pink-500/20 text-pink-400 font-sans px-1.5 py-0.5 rounded font-bold uppercase tracking-wider">Studio</span>
            </div>
            <p class="text-xs text-slate-400">Creator Dashboard</p>
          </div>
        </a>

        <!-- Main Navigation Links -->
        <nav class="space-y-1">
          <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ ($section ?? '') === 'overview' ? 'bg-gradient-to-r from-pink-500/20 to-transparent text-pink-400 border-l-4 border-pink-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            {!! icon('compass') !!}
            <span>Overview</span>
          </a>

          <a href="{{ route('dashboard.post.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ ($section ?? '') === 'new_post' ? 'bg-gradient-to-r from-pink-500/20 to-transparent text-pink-400 border-l-4 border-pink-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            {!! icon('plus') !!}
            <span>Create Post</span>
            <span class="ml-auto text-[10px] bg-pink-500 text-white font-bold px-1.5 py-0.5 rounded">NEW</span>
          </a>

          <a href="{{ route('dashboard.posts') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ ($section ?? '') === 'posts' ? 'bg-gradient-to-r from-pink-500/20 to-transparent text-pink-400 border-l-4 border-pink-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            {!! icon('list') !!}
            <span>My Posts</span>
          </a>

          <a href="{{ route('dashboard.queue') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ ($section ?? '') === 'queue' ? 'bg-gradient-to-r from-pink-500/20 to-transparent text-pink-400 border-l-4 border-pink-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            <div class="flex items-center gap-3">
              {!! icon('flame') !!}
              <span>Schedule Queue</span>
            </div>
            @php $queueCount = $blog ? $blog->posts()->where('status', 'queued')->count() : 0; @endphp
            <span class="text-xs px-2 py-0.5 rounded-full {{ $queueCount > 0 ? 'bg-pink-500/20 text-pink-400 font-bold' : 'bg-white/5 text-slate-400' }}">
              {{ $queueCount }}/20
            </span>
          </a>

          <a href="{{ route('dashboard.blogs') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ ($section ?? '') === 'blogs' ? 'bg-gradient-to-r from-pink-500/20 to-transparent text-pink-400 border-l-4 border-pink-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            {!! icon('edit') !!}
            <span>My Blogs & URLs</span>
          </a>

          <a href="{{ route('dashboard.communities') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ ($section ?? '') === 'communities' ? 'bg-gradient-to-r from-pink-500/20 to-transparent text-pink-400 border-l-4 border-pink-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            {!! icon('users') !!}
            <span>My Communities</span>
          </a>

          @if(classifieds_enabled())
          <a href="{{ route('dashboard.classifieds') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ ($section ?? '') === 'classifieds' ? 'bg-gradient-to-r from-pink-500/20 to-transparent text-pink-400 border-l-4 border-pink-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            {!! icon('megaphone') !!}
            <span>Sex Ads</span>
          </a>
          @endif

          <a href="{{ route('dashboard.referrals') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ ($section ?? '') === 'referrals' ? 'bg-gradient-to-r from-pink-500/20 to-transparent text-pink-400 border-l-4 border-pink-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            {!! icon('users') !!}
            <span>Referrals & Rewards</span>
          </a>

          <a href="{{ route('dashboard.verification') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ ($section ?? '') === 'verification' ? 'bg-gradient-to-r from-pink-500/20 to-transparent text-pink-400 border-l-4 border-pink-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            {!! icon('verified') !!}
            <span>ID Verification</span>
            @if(auth()->user()->badge_id)
              <span class="ml-auto w-2 h-2 rounded-full bg-emerald-400" title="Verified"></span>
            @endif
          </a>

          @php $unreadCount = auth()->user()->unread_messages_count ?? 0; @endphp
          <a href="{{ route('dashboard.messages') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ ($section ?? '') === 'messages' ? 'bg-gradient-to-r from-pink-500/20 to-transparent text-pink-400 border-l-4 border-pink-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            <div class="flex items-center gap-3">
              {!! icon('mail') !!}
              <span>Messages</span>
            </div>
            @if($unreadCount > 0)
              <span class="text-xs px-2 py-0.5 rounded-full bg-cyan-400 text-slate-950 font-black shadow-md shadow-cyan-400/30">
                {{ $unreadCount }}
              </span>
            @endif
          </a>

          <a href="{{ route('dashboard.notifications') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ ($section ?? '') === 'notifications' ? 'bg-gradient-to-r from-pink-500/20 to-transparent text-pink-400 border-l-4 border-pink-500' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            {!! icon('settings') !!}
            <span>Notifications</span>
          </a>

          <div class="pt-4 pb-2">
            <div class="h-px bg-white/10 mx-2"></div>
          </div>

          <a href="{{ route('advertising') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:text-cyan-400 hover:bg-white/5 transition">
            {!! icon('megaphone') !!}
            <span>Ad Marketplace</span>
          </a>

          <a href="{{ route('premium') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-amber-400 hover:bg-amber-400/10 transition">
            {!! icon('crown') !!}
            <span>Premium Upgrade</span>
          </a>

          <a href="{{ route('community') }}" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:text-white hover:bg-white/5 transition">
            {!! icon('users') !!}
            <span>Community Hub</span>
          </a>

          <a href="{{ url('/') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:text-white hover:bg-white/5 transition">
            {!! icon('left') !!}
            <span>Back to Explore</span>
          </a>
        </nav>
      </div>

      <!-- User Profile Footnote -->
      <div class="mt-6 pt-6 border-t border-white/10 flex items-center justify-between">
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-9 h-9 rounded-full bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center font-bold text-white text-sm shadow-md flex-shrink-0">
            {{ initial(auth()->user()->username) }}
          </div>
          <div class="min-w-0">
            <div class="font-bold text-sm text-white truncate flex items-center">
              <span>{{ auth()->user()->username }}</span>
              {!! badge(auth()->user()->badge_vip ? 'vip' : '') !!}
              {!! badge(auth()->user()->badge_id ? 'id' : '') !!}
              {!! badge(auth()->user()->badge_pro ? 'pro' : '') !!}
            </div>
            <div class="text-xs text-pink-400 font-semibold flex items-center gap-1">
              <span>{{ number_format(auth()->user()->points ?? 0) }} pts</span>
            </div>
          </div>
        </div>

        <a href="{{ route('logout') }}" title="Sign out" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-white/5 rounded-lg transition">
          {!! icon('login') !!}
        </a>
      </div>
    </aside>

    <!-- MAIN CONTENT VIEWPORT -->
    <main class="flex-1 min-w-0 bg-[#07030d] p-4 lg:p-8 overflow-y-auto">

      <!-- TOPBAR HEADER -->
      <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-6 border-b border-white/10">
        <div>
          <h1 class="text-2xl lg:text-3xl font-display font-black text-white tracking-tight flex items-center gap-3">
            <span>{{ ucfirst(str_replace('_', ' ', $section ?? 'Overview')) }}</span>
            @if(($section ?? '') === 'new_post')
              <span class="text-xs px-2.5 py-1 bg-pink-500/20 text-pink-400 rounded-lg font-sans font-bold uppercase tracking-wider">Editor</span>
            @endif
          </h1>
          <p class="text-xs lg:text-sm text-slate-400 mt-1">
            Manage your adult microblogging content, queue schedule, custom domains, and rewards.
          </p>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
          @if($blog)
            <a href="{{ route('blog.show', $blog->slug) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-bold text-slate-200 transition flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-pink-400"></span>
              <span>@ {{ $blog->slug }}</span>
            </a>
          @endif

          <!-- Theme Toggle -->
          <button type="button" onclick="window.toggleNinaTheme()" class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-bold text-slate-200 transition flex items-center justify-center theme-toggle-btn" aria-label="Toggle theme" title="Toggle Light / Dark theme">
            <span class="theme-icon-dark inline-flex items-center text-amber-400">{!! icon('sun') !!}</span>
            <span class="theme-icon-light hidden items-center text-cyan-400">{!! icon('moon') !!}</span>
          </button>

          <a href="{{ route('dashboard.post.create') }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-pink-500 to-fuchsia-600 hover:from-pink-400 hover:to-fuchsia-500 text-white text-xs font-bold shadow-lg shadow-pink-500/20 transition flex items-center gap-2">
            {!! icon('plus') !!}
            <span>New Post</span>
          </a>
        </div>
      </header>

      <!-- FLASH NOTIFICATIONS -->
      @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm flex items-center justify-between">
          <div class="flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
          </div>
        </div>
      @endif

      @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm flex items-center justify-between">
          <div class="flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>{{ session('error') }}</span>
          </div>
        </div>
      @endif


      <!-- ================= SECTION: OVERVIEW ================= -->
      @if(($section ?? '') === 'overview')
        
        <!-- KPI Stat Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
          <div class="p-5 rounded-2xl bg-[#0f0716] border border-white/10 hover:border-pink-500/40 transition group">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">
              <span>Published Posts</span>
              <span class="text-pink-400 group-hover:scale-110 transition">{!! icon('list') !!}</span>
            </div>
            <div class="text-2xl lg:text-3xl font-display font-black text-white">
              {{ $myPosts->count() }}
            </div>
            <div class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
              <span>All formats</span>
            </div>
          </div>

          <div class="p-5 rounded-2xl bg-[#0f0716] border border-white/10 hover:border-cyan-500/40 transition group">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">
              <span>Followers</span>
              <span class="text-cyan-400 group-hover:scale-110 transition">{!! icon('users') !!}</span>
            </div>
            <div class="text-2xl lg:text-3xl font-display font-black text-white">
              {{ $blog ? $blog->followers()->count() : 0 }}
            </div>
            <div class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
              <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
              <span>Subscribed members</span>
            </div>
          </div>

          <div class="p-5 rounded-2xl bg-[#0f0716] border border-white/10 hover:border-purple-500/40 transition group">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">
              <span>Total Views</span>
              <span class="text-purple-400 group-hover:scale-110 transition">👁️</span>
            </div>
            <div class="text-2xl lg:text-3xl font-display font-black text-white">
              {{ number_format($myPosts->sum('views_count')) }}
            </div>
            <div class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
              <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
              <span>Public post impressions</span>
            </div>
          </div>

          <div class="p-5 rounded-2xl bg-[#0f0716] border border-white/10 hover:border-amber-500/40 transition group">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">
              <span>Reward Points</span>
              <span class="text-amber-400 group-hover:scale-110 transition">{!! icon('flame') !!}</span>
            </div>
            <div class="text-2xl lg:text-3xl font-display font-black text-white">
              {{ number_format(auth()->user()->points ?? 0) }}
            </div>
            <div class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
              <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
              <span>Spend in Ad Marketplace</span>
            </div>
          </div>
        </div>

        <!-- Two Column Hub: Quick Actions & Main Blog Card -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          
          <!-- Quick Action Buttons -->
          <div class="p-6 rounded-2xl bg-[#0f0716] border border-white/10">
            <h2 class="text-base font-display font-bold text-white mb-4 flex items-center gap-2">
              <span>Quick Studio Actions</span>
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <a href="{{ route('dashboard.post.create') }}" class="p-4 rounded-xl bg-pink-500/10 hover:bg-pink-500/20 border border-pink-500/30 transition text-left group">
                <div class="text-pink-400 font-bold text-sm mb-1 flex items-center justify-between">
                  <span>Create Post</span>
                  <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
                <p class="text-xs text-slate-400">Publish photo, video, text, quote or chat.</p>
              </a>

              <a href="{{ route('advertising') }}" class="p-4 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/30 transition text-left group">
                <div class="text-cyan-400 font-bold text-sm mb-1 flex items-center justify-between">
                  <span>Boost in Ads</span>
                  <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
                <p class="text-xs text-slate-400">Spend points to reach 10k+ adult visitors.</p>
              </a>

              <a href="{{ route('dashboard.referrals') }}" class="p-4 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/30 transition text-left group">
                <div class="text-purple-400 font-bold text-sm mb-1 flex items-center justify-between">
                  <span>Refer Friends</span>
                  <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
                <p class="text-xs text-slate-400">Earn 100 free points per referral signup.</p>
              </a>

              <a href="{{ route('premium') }}" class="p-4 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 transition text-left group">
                <div class="text-amber-400 font-bold text-sm mb-1 flex items-center justify-between">
                  <span>Upgrade VIP</span>
                  <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
                <p class="text-xs text-slate-400">Get verified PRO badges & crypto perks.</p>
              </a>
            </div>
          </div>

          <!-- Main Profile Card -->
          <div class="p-6 rounded-2xl bg-[#0f0716] border border-white/10 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-display font-bold text-white">Current Active Blog</h2>
                <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400 font-bold">Default Profile</span>
              </div>

              @if($blog)
                <div class="flex items-start gap-4 mb-4">
                  <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-pink-500 to-purple-600 flex items-center justify-center font-display font-bold text-xl text-white shadow-lg flex-shrink-0">
                    {{ initial($blog->slug) }}
                  </div>
                  <div>
                    <h3 class="font-display font-bold text-lg text-white">@ {{ $blog->slug }}</h3>
                    <p class="text-xs text-pink-400 font-semibold">{{ $blog->title }}</p>
                    <p class="text-xs text-slate-400 mt-2 line-clamp-2 leading-relaxed">
                      {{ $blog->bio ?: 'No bio configured yet. Customize your bio, colors, and website links in Blog Settings.' }}
                    </p>
                  </div>
                </div>
              @endif
            </div>

            <div class="pt-4 border-t border-white/10 flex items-center gap-3">
              @if($blog)
                <a href="{{ route('blog.show', $blog->slug) }}" target="_blank" class="flex-1 py-2 px-3 text-center text-xs font-bold rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-white transition">
                  View Public Profile
                </a>
              @endif
              <a href="{{ route('dashboard.blogs') }}" class="flex-1 py-2 px-3 text-center text-xs font-bold rounded-xl bg-pink-500/20 hover:bg-pink-500/30 border border-pink-500/30 text-pink-400 transition">
                Edit Blog Settings
              </a>
            </div>
          </div>
        </div>

        <!-- Recent Posts Quick Table -->
        <div class="p-6 rounded-2xl bg-[#0f0716] border border-white/10">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-display font-bold text-white">Recent Publications</h2>
            <a href="{{ route('dashboard.posts') }}" class="text-xs text-pink-400 hover:text-pink-300 font-bold transition">View All &rarr;</a>
          </div>

          @if($myPosts->isEmpty())
            <div class="py-12 text-center text-slate-400 text-sm">
              <p class="mb-3">No posts published yet.</p>
              <a href="{{ route('dashboard.post.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-pink-500 text-white text-xs font-bold hover:bg-pink-400 transition">
                {!! icon('plus') !!}
                <span>Create Your First Post</span>
              </a>
            </div>
          @else
            <div class="overflow-x-auto">
              <table class="w-full text-left text-sm">
                <thead>
                  <tr class="text-slate-400 border-b border-white/10 text-xs uppercase font-semibold">
                    <th class="py-3 px-3">Format</th>
                    <th class="py-3 px-3">Content / Title</th>
                    <th class="py-3 px-3">Views</th>
                    <th class="py-3 px-3">Date</th>
                    <th class="py-3 px-3 text-right">Action</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-slate-300">
                  @foreach($myPosts->take(5) as $p)
                    <tr class="hover:bg-white/[0.02] transition">
                      <td class="py-3 px-3">
                        <span class="text-xs uppercase px-2 py-0.5 rounded-full font-bold {{ $p->type === 'video' ? 'bg-cyan-500/20 text-cyan-400' : ($p->type === 'photo' ? 'bg-pink-500/20 text-pink-400' : 'bg-white/10 text-slate-300') }}">
                          {{ $p->type }}
                        </span>
                      </td>
                      <td class="py-3 px-3 font-semibold text-white max-w-xs truncate">
                        {{ $p->heading ?: ($p->caption ?: ($p->quote_text ?: 'Post #' . $p->id)) }}
                      </td>
                      <td class="py-3 px-3 text-slate-400 text-xs">👁️ {{ number_format($p->views_count) }}</td>
                      <td class="py-3 px-3 text-slate-400 text-xs">{{ time_ago($p->published_at) }}</td>
                      <td class="py-3 px-3 text-right">
                        <div class="inline-flex items-center gap-2">
                          <a href="{{ route('post.show', ['id' => $p->id]) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-white/5 hover:bg-white/10 text-xs font-bold text-pink-400 transition">
                            View
                          </a>
                          <a href="{{ route('dashboard.post.edit', ['id' => $p->id]) }}" class="px-2.5 py-1 rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 text-xs font-bold text-cyan-400 transition">
                            Edit
                          </a>
                          <form method="POST" action="{{ route('dashboard.post.delete', ['id' => $p->id]) }}" onsubmit="return confirm('Are you sure you want to delete this post?');" style="display:inline;margin:0;">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-xs font-bold text-red-400 transition" title="Delete post">
                              Delete
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>


      <!-- ================= SECTION: NEW POST ================= -->
      @elseif(($section ?? '') === 'new_post')

        <div class="max-w-3xl mx-auto">
          <div class="p-6 lg:p-8 rounded-2xl bg-[#0f0716] border border-white/10 shadow-xl">
            <h2 class="text-xl font-display font-black text-white mb-2">Publish or Schedule Content</h2>
            <p class="text-xs lg:text-sm text-slate-400 mb-6">
              Create and publish engaging adult microblogging content across your blog network.
            </p>

            <form method="POST" action="{{ route('dashboard.post.store') }}" enctype="multipart/form-data" id="postForm" class="space-y-6">
              @csrf

              <!-- Target Blog Selector (if user has > 1 blog) -->
              @if(isset($blogs) && $blogs->count() > 1)
                <div>
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Target Blog</label>
                  <select name="blog_id" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-pink-500 transition">
                    @foreach($blogs as $b)
                      <option value="{{ $b->id }}" {{ ($blog->id ?? 0) === $b->id ? 'selected' : '' }}>
                        @ {{ $b->slug }} &middot; {{ $b->title }}
                      </option>
                    @endforeach
                  </select>
                </div>
              @endif

              <!-- Format Picker Tabs -->
              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Post Format</label>
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-2" id="formatPicker">
                  <button type="button" data-type="text" class="format-btn active px-3 py-2.5 rounded-xl border border-pink-500 bg-pink-500/20 text-pink-400 text-xs font-bold transition flex flex-col items-center gap-1">
                    {!! icon('text') !!}
                    <span>Text</span>
                  </button>
                  <button type="button" data-type="photo" class="format-btn px-3 py-2.5 rounded-xl border border-white/10 bg-white/5 text-slate-300 hover:border-pink-500/50 text-xs font-bold transition flex flex-col items-center gap-1">
                    {!! icon('photo') !!}
                    <span>Photo</span>
                  </button>
                  <button type="button" data-type="video" class="format-btn px-3 py-2.5 rounded-xl border border-white/10 bg-white/5 text-slate-300 hover:border-pink-500/50 text-xs font-bold transition flex flex-col items-center gap-1">
                    {!! icon('video') !!}
                    <span>Video</span>
                  </button>
                  <button type="button" data-type="quote" class="format-btn px-3 py-2.5 rounded-xl border border-white/10 bg-white/5 text-slate-300 hover:border-pink-500/50 text-xs font-bold transition flex flex-col items-center gap-1">
                    {!! icon('quote') !!}
                    <span>Quote</span>
                  </button>
                  <button type="button" data-type="link" class="format-btn px-3 py-2.5 rounded-xl border border-white/10 bg-white/5 text-slate-300 hover:border-pink-500/50 text-xs font-bold transition flex flex-col items-center gap-1">
                    {!! icon('link') !!}
                    <span>Link</span>
                  </button>
                  <button type="button" data-type="chat" class="format-btn px-3 py-2.5 rounded-xl border border-white/10 bg-white/5 text-slate-300 hover:border-pink-500/50 text-xs font-bold transition flex flex-col items-center gap-1">
                    {!! icon('chat') !!}
                    <span>Chat</span>
                  </button>
                </div>
                <input type="hidden" name="type" id="hiddenPostType" value="text">
              </div>

              <!-- Media Upload Area (Photo / MP4 Video) -->
              <div id="mediaUploadBox" class="hidden">
                <div class="flex items-center justify-between mb-2">
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Media Upload</label>
                  <span class="text-[11px] font-bold text-pink-400">
                    Admin Limits: Images &le; {{ $settings['max_image_mb'] ?? 2 }}MB &bull; Videos &le; {{ $settings['max_video_mb'] ?? 50 }}MB
                  </span>
                </div>
                <div class="border-2 border-dashed border-white/15 rounded-2xl p-6 text-center hover:border-pink-500/50 transition bg-white/[0.01]">
                  <input type="file" name="media_file" id="mediaFileInput" accept="image/*,video/mp4,video/webm,video/quicktime" class="hidden">
                  <label for="mediaFileInput" class="cursor-pointer block">
                    <div class="w-12 h-12 rounded-xl bg-pink-500/10 text-pink-400 mx-auto flex items-center justify-center mb-3">
                      {!! icon('photo') !!}
                    </div>
                    <span class="text-sm font-bold text-white block">Click to select photo or MP4 video</span>
                    <span class="text-xs text-slate-400 block mt-1">Supports JPG, PNG, WEBP (max {{ $settings['max_image_mb'] ?? 2 }}MB) &amp; MP4/WEBM videos (max {{ $settings['max_video_mb'] ?? 50 }}MB)</span>
                    <span id="selectedFileName" class="text-xs text-pink-400 font-semibold mt-2 block"></span>
                  </label>
                </div>
                <div id="videoUrlBox" class="hidden mt-3">
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Or embed a video URL</label>
                  <input type="url" name="video_url" placeholder="https://… paste a tube, YouTube, or Vimeo page link" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 transition">
                  <p class="text-[11px] text-slate-400 mt-1">The page link is turned into an embed. A file upload is used when both are provided.</p>
                </div>
              </div>

              <!-- Heading / Title Field -->
              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Heading / Title (Optional)</label>
                <input type="text" name="heading" placeholder="Catchy post title..." class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 transition">
              </div>

              <!-- Body Text / Rich Editor -->
              <div>
                <div class="flex items-center justify-between mb-2">
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider" id="bodyLabel">Body Content</label>
                  <span class="text-[11px] font-bold text-cyan-400 flex items-center gap-1">
                    ✨ Rich Text Formatting (Headers &amp; Links) Enabled
                  </span>
                </div>
                <p id="captionLimitHint" class="hidden text-[11px] text-slate-400 mb-2">Photo and video text is limited to {{ (int)($settings['post_max_caption_length'] ?? 450) }} characters (about 2–3 sentences). Feeds show about one sentence. The full text appears when the post is opened.</p>

                <div class="rich-editor" id="dashboardRichEditor">
                  <div class="editor-toolbar" role="toolbar" aria-label="Formatting options">
                    <!-- Headings / Block Types -->
                    <select class="editor-select" title="Block Type">
                      <option value="p">Paragraph</option>
                      <option value="h1">Heading 1 (H1)</option>
                      <option value="h2">Heading 2 (H2)</option>
                      <option value="h3">Heading 3 (H3)</option>
                      <option value="h4">Heading 4 (H4)</option>
                      <option value="blockquote">Quote Block</option>
                      <option value="pre">Code Block</option>
                    </select>

                    <!-- Quick Header Buttons -->
                    <div class="tb-group">
                      <button type="button" class="editor-btn" data-action="h1" title="Heading 1"><span style="font-weight:800;font-size:12px;">H1</span></button>
                      <button type="button" class="editor-btn" data-action="h2" title="Heading 2"><span style="font-weight:800;font-size:12px;">H2</span></button>
                      <button type="button" class="editor-btn" data-action="h3" title="Heading 3"><span style="font-weight:800;font-size:12px;">H3</span></button>
                    </div>

                    <div class="tb-sep"></div>

                    <!-- Basic Styles -->
                    <div class="tb-group">
                      <button type="button" class="editor-btn" data-cmd="bold" title="Bold (Ctrl+B)"><span style="font-weight:900;font-size:14px;">B</span></button>
                      <button type="button" class="editor-btn" data-cmd="italic" title="Italic (Ctrl+I)"><span style="font-style:italic;font-family:serif;font-size:15px;font-weight:700;">I</span></button>
                      <button type="button" class="editor-btn" data-cmd="underline" title="Underline (Ctrl+U)"><span style="text-decoration:underline;font-weight:700;">U</span></button>
                      <button type="button" class="editor-btn" data-cmd="strikeThrough" title="Strikethrough"><span style="text-decoration:line-through;font-weight:700;">S</span></button>
                      <button type="button" class="editor-btn" data-cmd="subscript" title="Subscript"><span style="font-size:11px;font-weight:700;">X<sub>2</sub></span></button>
                      <button type="button" class="editor-btn" data-cmd="superscript" title="Superscript"><span style="font-size:11px;font-weight:700;">X<sup>2</sup></span></button>
                    </div>

                    <div class="tb-sep"></div>

                    <!-- Text Alignments -->
                    <div class="tb-group">
                      <button type="button" class="editor-btn" data-cmd="justifyLeft" title="Align Left">
                        <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="15" y2="12"/><line x1="3" y1="18" x2="19" y2="18"/></svg>
                      </button>
                      <button type="button" class="editor-btn" data-cmd="justifyCenter" title="Align Center">
                        <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="6" y1="12" x2="18" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
                      </button>
                      <button type="button" class="editor-btn" data-cmd="justifyRight" title="Align Right">
                        <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="9" y1="12" x2="21" y2="12"/><line x1="5" y1="18" x2="21" y2="18"/></svg>
                      </button>
                    </div>

                    <div class="tb-sep"></div>

                    <!-- Colors / Highlights -->
                    <div class="tb-group">
                      <button type="button" class="editor-btn" data-color="#ff2bd6" title="Pink Neon Accent"><span style="color:#ff2bd6;font-weight:900;text-shadow:0 0 8px rgba(255,43,214,0.6);">A</span></button>
                      <button type="button" class="editor-btn" data-color="#19e3ff" title="Cyan Neon Accent"><span style="color:#19e3ff;font-weight:900;text-shadow:0 0 8px rgba(25,227,255,0.6);">A</span></button>
                      <button type="button" class="editor-btn" data-color="#ffd60a" title="Gold Neon Accent"><span style="color:#ffd60a;font-weight:900;text-shadow:0 0 8px rgba(255,214,10,0.6);">A</span></button>
                      <button type="button" class="editor-btn" data-color="#10b981" title="Emerald Accent"><span style="color:#10b981;font-weight:900;text-shadow:0 0 8px rgba(16,185,129,0.6);">A</span></button>
                      <button type="button" class="editor-btn" data-color="#ffffff" title="White Text"><span style="color:#ffffff;font-weight:900;">A</span></button>
                      <button type="button" class="editor-btn" data-hilite="rgba(255,214,10,0.35)" title="Highlight text"><span style="background:rgba(255,214,10,0.3);color:#ffd60a;padding:1px 5px;border-radius:3px;font-size:11px;font-weight:800;">HL</span></button>
                    </div>

                    <div class="tb-sep"></div>

                    <!-- Lists & Divider -->
                    <div class="tb-group">
                      <button type="button" class="editor-btn" data-cmd="insertUnorderedList" title="Bulleted list">
                        <svg viewBox="0 0 24 24"><line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/><circle cx="4.5" cy="6" r="1.5" fill="currentColor"/><circle cx="4.5" cy="12" r="1.5" fill="currentColor"/><circle cx="4.5" cy="18" r="1.5" fill="currentColor"/></svg>
                      </button>
                      <button type="button" class="editor-btn" data-cmd="insertOrderedList" title="Numbered list">
                        <span style="font-size:12px;font-weight:800;letter-spacing:-0.5px;">1.</span>
                      </button>
                      <button type="button" class="editor-btn" data-cmd="indent" title="Indent">
                        <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="11" y1="12" x2="21" y2="12"/><line x1="11" y1="18" x2="21" y2="18"/><polyline points="7 8 11 12 7 16"/></svg>
                      </button>
                      <button type="button" class="editor-btn" data-cmd="outdent" title="Outdent">
                        <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="11" y1="12" x2="21" y2="12"/><line x1="11" y1="18" x2="21" y2="18"/><polyline points="7 16 3 12 7 8"/></svg>
                      </button>
                      <button type="button" class="editor-btn" data-action="hr" title="Horizontal Divider">
                        <span style="font-size:16px;line-height:1;font-weight:bold;">&horbar;</span>
                      </button>
                    </div>

                    <div class="tb-sep"></div>

                    <!-- Link, Image & Clear -->
                    <div class="tb-group">
                      <button type="button" class="editor-btn" data-action="link" title="Insert / Edit Link (Ctrl+K)">
                        <svg viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                      </button>
                      <button type="button" class="editor-btn" data-action="unlink" title="Remove Link">
                        <svg viewBox="0 0 24 24"><path d="m18.84 12.25 1.72-1.71a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="m5.16 11.75-1.72 1.71a5 5 0 0 0 7.07 7.07l1.71-1.71"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                      </button>
                      <button type="button" class="editor-btn" data-action="blockquote" title="Quote Block">
                        <span style="font-family:serif;font-size:16px;font-weight:900;">&ldquo;</span>
                      </button>
                      <button type="button" class="editor-btn" data-action="inline-code" title="Inline Code">
                        <span style="font-family:monospace;font-size:11px;font-weight:700;">`c`</span>
                      </button>
                      <button type="button" class="editor-btn" data-action="code" title="Code Block">
                        <span style="font-family:monospace;font-size:11px;font-weight:700;">&lt;pre&gt;</span>
                      </button>
                      <button type="button" class="editor-btn" data-action="image" title="Upload or Insert Image">
                        <svg viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                      </button>
                      <button type="button" class="editor-btn text-pink-400 hover:text-pink-300" data-action="image-browse" title="Quick Upload Image File">
                        <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                      </button>
                      <button type="button" class="editor-btn" data-action="clear" title="Clear Formatting">
                        <svg viewBox="0 0 24 24"><path d="m7 21 10-10-4-4L3 17v4h4z"/><path d="M14.5 4.5l3 3"/></svg>
                      </button>
                    </div>

                    <div class="tb-sep"></div>

                    <!-- Undo/Redo & Source Toggle -->
                    <div class="tb-group" style="margin-left:auto;">
                      <button type="button" class="editor-btn" data-cmd="undo" title="Undo (Ctrl+Z)">
                        <svg viewBox="0 0 24 24"><path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"/></svg>
                      </button>
                      <button type="button" class="editor-btn" data-cmd="redo" title="Redo (Ctrl+Y)">
                        <svg viewBox="0 0 24 24"><path d="M21 7v6h-6"/><path d="M3 17a9 9 0 0 1 9-9 9 9 0 0 1 6 2.3l3 2.7"/></svg>
                      </button>
                      <button type="button" class="editor-btn" data-action="toggle-source" title="Toggle HTML View">
                        <span style="font-family:monospace;font-size:12px;font-weight:700;">&lt;/&gt;</span>
                      </button>
                    </div>
                  </div>

                  <!-- Editable Canvas -->
                  <div class="editor-content" id="editorCanvas" contenteditable="true" data-placeholder="Write your story, thoughts, or formatted creative writing here..."></div>
                  <textarea class="editor-source" id="editorSource" style="display:none;" placeholder="Raw HTML source code..."></textarea>

                  <div class="editor-footer">
                    <span id="editorWordCount">0 words &middot; 0 characters</span>
                    <span class="text-cyan-400 font-semibold">Supports Headings, Links &amp; Rich Formatting</span>
                  </div>
                </div>

                <!-- Form submission textarea -->
                <textarea name="body" id="hiddenBodyTextarea" style="display:none;"></textarea>
              </div>

              <!-- Quote Specific Fields -->
              <div id="quoteFields" class="hidden space-y-4">
                <div>
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Quote Text</label>
                  <textarea name="quote" rows="2" placeholder="The quote excerpt..." class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 transition"></textarea>
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Quote Source / Author</label>
                  <input type="text" name="source" placeholder="Speaker or source" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 transition">
                </div>
              </div>

              <!-- Link Specific Fields -->
              <div id="linkFields" class="hidden space-y-4">
                <div>
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Link URL</label>
                  <input type="url" name="url" placeholder="https://example.com" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 transition">
                </div>
              </div>

              <!-- Chat Specific Fields -->
              <div id="chatFields" class="hidden">
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Chat Dialogue (Line-by-line)</label>
                <textarea name="chat" rows="4" placeholder="Alice: Hey there!&#10;Bob: Hello! How are you?" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 transition font-mono"></textarea>
              </div>

              <!-- Tags / Hashtags -->
              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2 flex items-center justify-between">
                  <span>Tags &amp; Categories</span>
                  <span class="text-slate-400 font-normal lowercase">comma-separated or #hashtags (e.g. art, cyberpunk, neon)</span>
                </label>
                <input type="text" name="tags" placeholder="aesthetic, cyberpunk, neon, photography" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-cyan-300 placeholder-slate-500 focus:outline-none focus:border-cyan-400 transition font-mono">
              </div>

              <!-- Automated Queue & Scheduling Options -->
              <div class="p-4 rounded-xl bg-white/[0.03] border border-white/10 space-y-3" id="queueSchedulePanel">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-pink-400">
                    {!! icon('flame') !!}
                    <span>Automated Schedule Queue (Max 20 Posts per Blog)</span>
                  </div>
                  <span class="text-[11px] font-semibold text-slate-400">
                    Allowed formats: <b class="text-white">Text</b>, <b class="text-white">Image</b>, <b class="text-white">Link</b>
                  </span>
                </div>

                <div id="queueFormatWarning" class="hidden text-xs px-3 py-2 rounded-lg bg-amber-500/10 border border-amber-500/30 text-amber-300 font-semibold flex items-center gap-2">
                  <span>⚠️</span>
                  <span>Video and Chat formats cannot be queued or scheduled. Select <b>Text</b>, <b>Photo</b>, or <b>Link</b> above to enable queuing.</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-2">
                  <div>
                    <label class="block text-[11px] text-slate-400 font-semibold mb-1">Month</label>
                    <select name="schedule_month" class="w-full bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-xs text-white">
                      @foreach([1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'] as $mNum => $mName)
                        <option value="{{ $mNum }}" {{ date('n') == $mNum ? 'selected' : '' }}>{{ $mName }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div>
                    <label class="block text-[11px] text-slate-400 font-semibold mb-1">Day</label>
                    <select name="schedule_day" class="w-full bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-xs text-white">
                      @for($d = 1; $d <= 31; $d++)
                        <option value="{{ $d }}" {{ date('j') == $d ? 'selected' : '' }}>{{ $d }}</option>
                      @endfor
                    </select>
                  </div>
                  <div>
                    <label class="block text-[11px] text-slate-400 font-semibold mb-1">Time (24h)</label>
                    <input type="time" name="schedule_time" value="{{ date('H:i', strtotime('+2 hours')) }}" class="w-full bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-xs text-white">
                  </div>
                  <div>
                    <label class="block text-[11px] text-slate-400 font-semibold mb-1">Or Exact DateTime</label>
                    <input type="datetime-local" name="scheduled_at" class="w-full bg-[#170a22] border border-white/10 rounded-lg px-2.5 py-1.5 text-xs text-white">
                  </div>
                </div>
                <p class="text-[11px] text-slate-500">
                  Leave blank to auto-schedule sequentially (+2 hours after previous queued post). Posts publish automatically when the scheduled month, day, and time arrives.
                </p>
              </div>

              <!-- Action Submission Buttons -->
              <div class="pt-4 border-t border-white/10 flex flex-col sm:flex-row items-center gap-3">
                <button type="submit" name="post_action" value="publish" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-gradient-to-r from-pink-500 to-fuchsia-600 hover:from-pink-400 hover:to-fuchsia-500 text-white font-bold text-sm shadow-lg shadow-pink-500/20 transition flex items-center justify-center gap-2">
                  {!! icon('plus') !!}
                  <span>Publish Immediately</span>
                </button>

                <button type="submit" name="post_action" value="queue" id="btnAddToQueue" class="w-full sm:w-auto px-5 py-3 rounded-xl bg-pink-500/20 hover:bg-pink-500/30 border border-pink-500/40 text-pink-300 font-bold text-sm transition flex items-center justify-center gap-2" title="Queue post for scheduled delivery">
                  {!! icon('flame') !!}
                  <span>Add to Queue (Max 20)</span>
                </button>

                <button type="submit" name="post_action" value="draft" class="w-full sm:w-auto px-5 py-3 rounded-xl text-slate-400 hover:text-white font-semibold text-sm transition">
                  Save Draft
                </button>
              </div>
            </form>
          </div>
        </div>

        <script>
          // Dynamic Format Switcher
          const formatBtns = document.querySelectorAll('.format-btn');
          const hiddenType = document.getElementById('hiddenPostType');
          const mediaBox = document.getElementById('mediaUploadBox');
          const quoteBox = document.getElementById('quoteFields');
          const linkBox = document.getElementById('linkFields');
          const chatBox = document.getElementById('chatFields');
          const bodyLabel = document.getElementById('bodyLabel');
          const fileInput = document.getElementById('mediaFileInput');
          const fileName = document.getElementById('selectedFileName');
          const btnQueue = document.getElementById('btnAddToQueue');
          const queueWarning = document.getElementById('queueFormatWarning');

          if (fileInput) {
            fileInput.addEventListener('change', () => {
              if (fileInput.files.length > 0) {
                fileName.textContent = 'Selected: ' + fileInput.files[0].name;
              }
            });
          }

          function checkQueueEligibility(type) {
            const isQueueable = (type === 'text' || type === 'photo' || type === 'link');
            if (btnQueue) {
              btnQueue.disabled = !isQueueable;
              if (!isQueueable) {
                btnQueue.classList.add('opacity-40', 'cursor-not-allowed');
                btnQueue.classList.remove('hover:bg-pink-500/30');
              } else {
                btnQueue.classList.remove('opacity-40', 'cursor-not-allowed');
                btnQueue.classList.add('hover:bg-pink-500/30');
              }
            }
            if (queueWarning) {
              queueWarning.classList.toggle('hidden', isQueueable);
            }
          }

          formatBtns.forEach(btn => {
            btn.addEventListener('click', () => {
              formatBtns.forEach(b => {
                b.classList.remove('border-pink-500', 'bg-pink-500/20', 'text-pink-400');
                b.classList.add('border-white/10', 'bg-white/5', 'text-slate-300');
              });
              btn.classList.remove('border-white/10', 'bg-white/5', 'text-slate-300');
              btn.classList.add('border-pink-500', 'bg-pink-500/20', 'text-pink-400');

              const type = btn.getAttribute('data-type');
              hiddenType.value = type;

              mediaBox.classList.toggle('hidden', type !== 'photo' && type !== 'video');
              const videoUrlBox = document.getElementById('videoUrlBox');
              if (videoUrlBox) videoUrlBox.classList.toggle('hidden', type !== 'video');
              quoteBox.classList.toggle('hidden', type !== 'quote');
              linkBox.classList.toggle('hidden', type !== 'link');
              chatBox.classList.toggle('hidden', type !== 'chat');

              if (type === 'photo' || type === 'video') {
                bodyLabel.textContent = 'Caption / Text (Optional)';
              } else {
                bodyLabel.textContent = 'Body Content';
              }
              const captionHint = document.getElementById('captionLimitHint');
              if (captionHint) captionHint.classList.toggle('hidden', type !== 'photo' && type !== 'video');
              document.dispatchEvent(new Event('nina-post-type-changed'));

              checkQueueEligibility(type);
            });
          });

          checkQueueEligibility('text');
        </script>


      <!-- ================= SECTION: EDIT POST ================= -->
      @elseif(($section ?? '') === 'edit_post')

        <div class="max-w-3xl mx-auto">
          <div class="p-6 lg:p-8 rounded-2xl bg-[#0f0716] border border-white/10 shadow-xl">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/10">
              <div>
                <h2 class="text-xl font-display font-black text-white">Edit Post #{{ $post->id }}</h2>
                <p class="text-xs lg:text-sm text-slate-400 mt-1">
                  Update your post content, media, format, or tags.
                </p>
              </div>
              <a href="{{ route('dashboard.posts') }}" class="px-3.5 py-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-bold text-slate-300 transition">
                &larr; Back to My Posts
              </a>
            </div>

            <form method="POST" action="{{ route('dashboard.post.update', $post->id) }}" enctype="multipart/form-data" id="editPostForm" class="space-y-6">
              @csrf

              <!-- Target Blog Selector -->
              @if(isset($blogs) && $blogs->count() > 1)
                <div>
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Target Blog</label>
                  <select name="blog_id" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-pink-500 transition">
                    @foreach($blogs as $b)
                      <option value="{{ $b->id }}" {{ $post->blog_id == $b->id ? 'selected' : '' }}>
                        @ {{ $b->slug }} &middot; {{ $b->title }}
                      </option>
                    @endforeach
                  </select>
                </div>
              @endif

              <!-- Format Picker Tabs -->
              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Post Format</label>
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-2" id="editFormatPicker">
                  <button type="button" data-type="text" class="edit-format-btn px-3 py-2.5 rounded-xl border text-xs font-bold transition flex flex-col items-center gap-1 {{ $post->type === 'text' ? 'border-pink-500 bg-pink-500/20 text-pink-400' : 'border-white/10 bg-white/5 text-slate-300 hover:border-pink-500/50' }}">
                    {!! icon('text') !!}
                    <span>Text</span>
                  </button>
                  <button type="button" data-type="photo" class="edit-format-btn px-3 py-2.5 rounded-xl border text-xs font-bold transition flex flex-col items-center gap-1 {{ $post->type === 'photo' ? 'border-pink-500 bg-pink-500/20 text-pink-400' : 'border-white/10 bg-white/5 text-slate-300 hover:border-pink-500/50' }}">
                    {!! icon('photo') !!}
                    <span>Photo</span>
                  </button>
                  <button type="button" data-type="video" class="edit-format-btn px-3 py-2.5 rounded-xl border text-xs font-bold transition flex flex-col items-center gap-1 {{ $post->type === 'video' ? 'border-pink-500 bg-pink-500/20 text-pink-400' : 'border-white/10 bg-white/5 text-slate-300 hover:border-pink-500/50' }}">
                    {!! icon('video') !!}
                    <span>Video</span>
                  </button>
                  <button type="button" data-type="quote" class="edit-format-btn px-3 py-2.5 rounded-xl border text-xs font-bold transition flex flex-col items-center gap-1 {{ $post->type === 'quote' ? 'border-pink-500 bg-pink-500/20 text-pink-400' : 'border-white/10 bg-white/5 text-slate-300 hover:border-pink-500/50' }}">
                    {!! icon('quote') !!}
                    <span>Quote</span>
                  </button>
                  <button type="button" data-type="link" class="edit-format-btn px-3 py-2.5 rounded-xl border text-xs font-bold transition flex flex-col items-center gap-1 {{ $post->type === 'link' ? 'border-pink-500 bg-pink-500/20 text-pink-400' : 'border-white/10 bg-white/5 text-slate-300 hover:border-pink-500/50' }}">
                    {!! icon('link') !!}
                    <span>Link</span>
                  </button>
                  <button type="button" data-type="chat" class="edit-format-btn px-3 py-2.5 rounded-xl border text-xs font-bold transition flex flex-col items-center gap-1 {{ $post->type === 'chat' ? 'border-pink-500 bg-pink-500/20 text-pink-400' : 'border-white/10 bg-white/5 text-slate-300 hover:border-pink-500/50' }}">
                    {!! icon('chat') !!}
                    <span>Chat</span>
                  </button>
                </div>
                <input type="hidden" name="type" id="editHiddenPostType" value="{{ $post->type }}">
              </div>

              <!-- Media Upload Area -->
              <div id="editMediaUploadBox" class="{{ in_array($post->type, ['photo', 'video']) ? '' : 'hidden' }}">
                <div class="flex items-center justify-between mb-2">
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Media File</label>
                  <span class="text-[11px] font-bold text-pink-400">
                    Max: Images &le; {{ $settings['max_image_mb'] ?? 2 }}MB &bull; Videos &le; {{ $settings['max_video_mb'] ?? 50 }}MB
                  </span>
                </div>

                @if($post->media_path)
                  <div class="p-4 rounded-xl bg-white/[0.03] border border-white/10 mb-3 flex items-center gap-4">
                    <div class="w-20 h-20 rounded-lg overflow-hidden bg-black/50 border border-white/10 flex-shrink-0 flex items-center justify-center">
                      @if($post->media_type === 'video' || Str::endsWith(strtolower($post->media_path), ['.mp4', '.mov', '.webm']))
                        <video src="{{ media_url($post->media_path) }}" class="w-full h-full object-cover" muted></video>
                      @else
                        <img src="{{ media_url($post->media_path) }}" alt="" class="w-full h-full object-cover">
                      @endif
                    </div>
                    <div>
                      <span class="text-xs font-bold text-white block">Current Attached Media</span>
                      <a href="{{ media_url($post->media_path) }}" target="_blank" class="text-xs text-pink-400 hover:underline font-mono">View full media &rarr;</a>
                      <p class="text-[11px] text-slate-400 mt-1">Upload a new file below only if you wish to replace this media.</p>
                    </div>
                  </div>
                @endif

                <div class="border-2 border-dashed border-white/15 rounded-2xl p-6 text-center hover:border-pink-500/50 transition bg-white/[0.01]">
                  <input type="file" name="media_file" id="editMediaFileInput" accept="image/*,video/mp4,video/webm,video/quicktime" class="hidden">
                  <label for="editMediaFileInput" class="cursor-pointer block">
                    <div class="w-12 h-12 rounded-xl bg-pink-500/10 text-pink-400 mx-auto flex items-center justify-center mb-3">
                      {!! icon('photo') !!}
                    </div>
                    <span class="text-sm font-bold text-white block">{{ $post->media_path ? 'Click to replace media file' : 'Click to select photo or MP4 video' }}</span>
                    <span class="text-xs text-slate-400 block mt-1">Supports JPG, PNG, WEBP &amp; MP4/WEBM videos</span>
                    <span id="editSelectedFileName" class="text-xs text-pink-400 font-semibold mt-2 block"></span>
                  </label>
                </div>
                <div class="mt-3 {{ $post->type === 'video' ? '' : 'hidden' }}" id="editVideoUrlBox">
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Or replace with a video URL</label>
                  <input type="url" name="video_url" value="{{ is_array($post->media_meta ?? null) ? ($post->media_meta['embed_url'] ?? '') : '' }}" placeholder="https://… tube, YouTube, or Vimeo page link" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 transition">
                </div>
              </div>

              <!-- Heading / Title Field -->
              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Heading / Title (Optional)</label>
                <input type="text" name="heading" value="{{ $post->heading }}" placeholder="Post title..." class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 transition">
              </div>

              <!-- Body Text / Rich Editor -->
              <div>
                <div class="flex items-center justify-between mb-2">
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider" id="editBodyLabel">
                    {{ in_array($post->type, ['photo', 'video']) ? 'Caption / Text (Optional)' : 'Body Content' }}
                  </label>
                  <span class="text-[11px] font-bold text-cyan-400 flex items-center gap-1">
                    ✨ Rich Text Formatting Enabled
                  </span>
                </div>
                <p id="editCaptionLimitHint" class="{{ in_array($post->type, ['photo', 'video']) ? '' : 'hidden' }} text-[11px] text-slate-400 mb-2">Limited to {{ (int)($settings['post_max_caption_length'] ?? 450) }} characters (about 2–3 sentences).</p>

                <div class="rich-editor" id="editDashboardRichEditor">
                  <div class="editor-toolbar" role="toolbar" aria-label="Formatting options">
                    <select class="editor-select" title="Block Type">
                      <option value="p">Paragraph</option>
                      <option value="h1">Heading 1 (H1)</option>
                      <option value="h2">Heading 2 (H2)</option>
                      <option value="h3">Heading 3 (H3)</option>
                      <option value="h4">Heading 4 (H4)</option>
                      <option value="blockquote">Quote Block</option>
                      <option value="pre">Code Block</option>
                    </select>

                    <div class="tb-group">
                      <button type="button" class="editor-btn" data-action="h1" title="Heading 1"><span style="font-weight:800;font-size:12px;">H1</span></button>
                      <button type="button" class="editor-btn" data-action="h2" title="Heading 2"><span style="font-weight:800;font-size:12px;">H2</span></button>
                      <button type="button" class="editor-btn" data-action="h3" title="Heading 3"><span style="font-weight:800;font-size:12px;">H3</span></button>
                    </div>

                    <div class="tb-sep"></div>

                    <div class="tb-group">
                      <button type="button" class="editor-btn" data-cmd="bold" title="Bold"><span style="font-weight:900;font-size:14px;">B</span></button>
                      <button type="button" class="editor-btn" data-cmd="italic" title="Italic"><span style="font-style:italic;font-family:serif;font-size:15px;font-weight:700;">I</span></button>
                      <button type="button" class="editor-btn" data-cmd="underline" title="Underline"><span style="text-decoration:underline;font-weight:700;">U</span></button>
                      <button type="button" class="editor-btn" data-cmd="strikeThrough" title="Strikethrough"><span style="text-decoration:line-through;font-weight:700;">S</span></button>
                    </div>

                    <div class="tb-sep"></div>

                    <div class="tb-group">
                      <button type="button" class="editor-btn" data-color="#ff2bd6" title="Pink Accent"><span style="color:#ff2bd6;font-weight:900;">A</span></button>
                      <button type="button" class="editor-btn" data-color="#19e3ff" title="Cyan Accent"><span style="color:#19e3ff;font-weight:900;">A</span></button>
                      <button type="button" class="editor-btn" data-color="#ffd60a" title="Gold Accent"><span style="color:#ffd60a;font-weight:900;">A</span></button>
                    </div>

                    <div class="tb-sep"></div>

                    <div class="tb-group">
                      <button type="button" class="editor-btn" data-action="link" title="Insert / Edit Link">
                        <svg viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                      </button>
                      <button type="button" class="editor-btn" data-action="image" title="Insert Image">
                        <svg viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                      </button>
                      <button type="button" class="editor-btn" data-action="clear" title="Clear Formatting">
                        <svg viewBox="0 0 24 24"><path d="m7 21 10-10-4-4L3 17v4h4z"/><path d="M14.5 4.5l3 3"/></svg>
                      </button>
                    </div>

                    <div class="tb-group" style="margin-left:auto;">
                      <button type="button" class="editor-btn" data-action="toggle-source" title="Toggle HTML View">
                        <span style="font-family:monospace;font-size:12px;font-weight:700;">&lt;/&gt;</span>
                      </button>
                    </div>
                  </div>

                  <div class="editor-content" id="editEditorCanvas" contenteditable="true" data-placeholder="Edit your post content...">{!! $post->body !!}</div>
                  <textarea class="editor-source" id="editEditorSource" style="display:none;" placeholder="Raw HTML source code...">{!! $post->body !!}</textarea>

                  <div class="editor-footer">
                    <span id="editEditorWordCount">0 words &middot; 0 characters</span>
                    <span class="text-cyan-400 font-semibold">Editing Post #{{ $post->id }}</span>
                  </div>
                </div>

                <textarea name="body" id="editHiddenBodyTextarea" style="display:none;">{!! $post->body !!}</textarea>
              </div>

              <!-- Quote Specific Fields -->
              <div id="editQuoteFields" class="{{ $post->type === 'quote' ? '' : 'hidden' }} space-y-4">
                <div>
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Quote Text</label>
                  <textarea name="quote" rows="2" placeholder="The quote excerpt..." class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 transition">{{ $post->quote_text }}</textarea>
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Quote Source / Author</label>
                  <input type="text" name="source" value="{{ $post->quote_source }}" placeholder="Speaker or source" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 transition">
                </div>
              </div>

              <!-- Link Specific Fields -->
              <div id="editLinkFields" class="{{ $post->type === 'link' ? '' : 'hidden' }} space-y-4">
                <div>
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Link URL</label>
                  <input type="url" name="url" value="{{ $post->link_url }}" placeholder="https://example.com" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 transition">
                </div>
              </div>

              <!-- Chat Specific Fields -->
              <div id="editChatFields" class="{{ $post->type === 'chat' ? '' : 'hidden' }}">
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Chat Dialogue (Line-by-line)</label>
                <textarea name="chat" rows="4" placeholder="Alice: Hey there!&#10;Bob: Hello! How are you?" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 transition font-mono">{{ $post->chat_body }}</textarea>
              </div>

              <!-- Tags / Hashtags -->
              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2 flex items-center justify-between">
                  <span>Tags &amp; Categories</span>
                  <span class="text-slate-400 font-normal lowercase">comma-separated or #hashtags</span>
                </label>
                <input type="text" name="tags" value="{{ !empty($post->tags_list) ? implode(', ', $post->tags_list) : '' }}" placeholder="art, photography, adult" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-cyan-300 placeholder-slate-500 focus:outline-none focus:border-cyan-400 transition font-mono">
              </div>

              <!-- Action Submission Buttons -->
              <div class="pt-4 border-t border-white/10 flex items-center justify-between gap-3">
                <a href="{{ route('dashboard.posts') }}" class="px-5 py-3 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 font-semibold text-sm transition">
                  Cancel
                </a>

                <button type="submit" class="px-7 py-3 rounded-xl bg-gradient-to-r from-pink-500 to-fuchsia-600 hover:from-pink-400 hover:to-fuchsia-500 text-white font-bold text-sm shadow-lg shadow-pink-500/20 transition flex items-center gap-2">
                  {!! icon('edit') !!}
                  <span>Save Changes</span>
                </button>
              </div>
            </form>
          </div>
        </div>

        <script>
          const editFormatBtns = document.querySelectorAll('.edit-format-btn');
          const editHiddenType = document.getElementById('editHiddenPostType');
          const editMediaBox = document.getElementById('editMediaUploadBox');
          const editQuoteBox = document.getElementById('editQuoteFields');
          const editLinkBox = document.getElementById('editLinkFields');
          const editChatBox = document.getElementById('editChatFields');
          const editBodyLabel = document.getElementById('editBodyLabel');
          const editFileInput = document.getElementById('editMediaFileInput');
          const editFileName = document.getElementById('editSelectedFileName');

          if (editFileInput) {
            editFileInput.addEventListener('change', () => {
              if (editFileInput.files.length > 0) {
                editFileName.textContent = 'Selected replacement: ' + editFileInput.files[0].name;
              }
            });
          }

          editFormatBtns.forEach(btn => {
            btn.addEventListener('click', () => {
              editFormatBtns.forEach(b => {
                b.classList.remove('border-pink-500', 'bg-pink-500/20', 'text-pink-400');
                b.classList.add('border-white/10', 'bg-white/5', 'text-slate-300');
              });
              btn.classList.remove('border-white/10', 'bg-white/5', 'text-slate-300');
              btn.classList.add('border-pink-500', 'bg-pink-500/20', 'text-pink-400');

              const type = btn.getAttribute('data-type');
              if (editHiddenType) editHiddenType.value = type;

              if (editMediaBox) editMediaBox.classList.toggle('hidden', type !== 'photo' && type !== 'video');
              const editVideoUrlBox = document.getElementById('editVideoUrlBox');
              if (editVideoUrlBox) editVideoUrlBox.classList.toggle('hidden', type !== 'video');
              if (editQuoteBox) editQuoteBox.classList.toggle('hidden', type !== 'quote');
              if (editLinkBox) editLinkBox.classList.toggle('hidden', type !== 'link');
              if (editChatBox) editChatBox.classList.toggle('hidden', type !== 'chat');

              if (editBodyLabel) {
                if (type === 'photo' || type === 'video') {
                  editBodyLabel.textContent = 'Caption / Text (Optional)';
                } else {
                  editBodyLabel.textContent = 'Body Content';
                }
              }
              const editCaptionHint = document.getElementById('editCaptionLimitHint');
              if (editCaptionHint) editCaptionHint.classList.toggle('hidden', type !== 'photo' && type !== 'video');
              document.dispatchEvent(new Event('nina-post-type-changed'));
            });
          });
        </script>


      <!-- ================= SECTION: QUEUE ================= -->
      @elseif(($section ?? '') === 'queue')

        <div class="p-6 lg:p-8 rounded-2xl bg-[#0f0716] border border-white/10">
          
          <!-- Blog Selector Tabs if User Owns Multiple Blogs -->
          @if(isset($allBlogs) && $allBlogs->count() > 1)
            <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-6 border-b border-white/10 text-xs font-semibold custom-scrollbar">
              <span class="text-slate-400 uppercase tracking-wider text-[11px] mr-2">Select Blog Queue:</span>
              @foreach($allBlogs as $b)
                @php $bQueued = $b->posts()->where('status', 'queued')->count(); @endphp
                <a href="{{ route('dashboard.queue', ['blog_id' => $b->id]) }}" class="px-3 py-1.5 rounded-lg whitespace-nowrap transition flex items-center gap-2 {{ ($blog->id ?? 0) === $b->id ? 'bg-pink-500 text-white font-bold shadow-md shadow-pink-500/20' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">
                  <span>@ {{ $b->slug }}</span>
                  <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ ($blog->id ?? 0) === $b->id ? 'bg-black/30 text-white' : 'bg-white/10 text-slate-400' }}">
                    {{ $bQueued }}/20
                  </span>
                </a>
              @endforeach
            </div>
          @endif

          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-6 border-b border-white/10">
            <div>
              <div class="flex items-center gap-3">
                <h2 class="text-xl font-display font-black text-white flex items-center gap-2">
                  <span>Queue for @ {{ $blog->slug ?? 'blog' }}</span>
                </h2>
                <span class="text-xs px-2.5 py-1 rounded-full {{ $queuedPosts->count() >= 20 ? 'bg-rose-500/20 text-rose-400' : 'bg-pink-500/20 text-pink-400' }} font-bold">
                  {{ $queuedPosts->count() }} / 20 Slots Used
                </span>
              </div>
              <p class="text-xs lg:text-sm text-slate-400 mt-1">
                Automated schedule queue (Max 20 posts per blog). Accepts <b>Text</b>, <b>Image</b>, and <b>Link</b> formats. Automatically scheduled by month, day, and time.
              </p>
            </div>

            <a href="{{ route('dashboard.post.create', ['blog_id' => $blog->id ?? null]) }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-pink-500 to-fuchsia-600 hover:from-pink-400 hover:to-fuchsia-500 text-white font-bold text-xs shadow-lg shadow-pink-500/20 transition flex items-center gap-2 self-start">
              {!! icon('plus') !!}
              <span>Schedule New Post</span>
            </a>
          </div>

          <!-- Queue Capacity Progress Bar -->
          <div class="mb-6">
            <div class="w-full bg-white/5 rounded-full h-2 overflow-hidden">
              @php $pct = min(100, round(($queuedPosts->count() / 20) * 100)); @endphp
              <div class="h-full rounded-full transition-all duration-300 {{ $pct >= 90 ? 'bg-rose-500' : ($pct >= 60 ? 'bg-amber-500' : 'bg-gradient-to-r from-pink-500 to-cyan-400') }}" style="width: {{ $pct }}%"></div>
            </div>
            <div class="flex justify-between items-center text-[11px] text-slate-400 mt-1.5">
              <span>{{ 20 - $queuedPosts->count() }} slots remaining on this blog</span>
              <span>{{ $pct }}% full</span>
            </div>
          </div>

          @if($queuedPosts->isEmpty())
            <div class="py-16 text-center text-slate-400">
              <div class="w-12 h-12 rounded-xl bg-white/5 text-slate-500 mx-auto flex items-center justify-center mb-3">
                {!! icon('flame') !!}
              </div>
              <h3 class="font-bold text-white text-base mb-1">Queue is empty for @ {{ $blog->slug ?? 'this blog' }}</h3>
              <p class="text-xs text-slate-400 max-w-sm mx-auto mb-4">
                Keep your audience engaged around the clock by scheduling upcoming text, image, and link posts in advance.
              </p>
              <a href="{{ route('dashboard.post.create', ['blog_id' => $blog->id ?? null]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-pink-500 text-white text-xs font-bold hover:bg-pink-400 transition">
                {!! icon('plus') !!}
                <span>Queue a Post Now</span>
              </a>
            </div>
          @else
            <div class="overflow-x-auto">
              <table class="w-full text-left text-sm">
                <thead>
                  <tr class="text-slate-400 border-b border-white/10 text-xs uppercase font-semibold">
                    <th class="py-3 px-3">Slot</th>
                    <th class="py-3 px-3">Format</th>
                    <th class="py-3 px-3">Content Preview</th>
                    <th class="py-3 px-3">Scheduled Delivery (Month, Day, Time)</th>
                    <th class="py-3 px-3 text-right">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-slate-300">
                  @foreach($queuedPosts as $idx => $qp)
                    <tr class="hover:bg-white/[0.02] transition">
                      <td class="py-3 px-3 font-mono text-xs text-pink-400 font-bold">#{{ $idx + 1 }}</td>
                      <td class="py-3 px-3">
                        <span class="text-xs uppercase px-2 py-0.5 rounded-full font-bold {{ $qp->type === 'photo' ? 'bg-pink-500/20 text-pink-400' : ($qp->type === 'link' ? 'bg-cyan-500/20 text-cyan-300' : 'bg-white/10 text-slate-300') }}">
                          {{ $qp->type === 'photo' ? 'Image' : $qp->type }}
                        </span>
                      </td>
                      <td class="py-3 px-3 font-semibold text-white max-w-xs truncate">
                        <div class="flex items-center gap-2">
                          @if($qp->media_path)
                            <span class="w-7 h-7 rounded bg-white/10 flex-shrink-0 overflow-hidden flex items-center justify-center">
                              <img src="{{ media_url($qp->media_path) }}" alt="" class="w-full h-full object-cover">
                            </span>
                          @endif
                          <span class="truncate">{{ $qp->heading ?: ($qp->caption ?: ($qp->body ? strip_tags(substr($qp->body, 0, 50)) : 'Queued Post #' . $qp->id)) }}</span>
                        </div>
                      </td>
                      <td class="py-3 px-3 text-xs">
                        @if($qp->published_at)
                          <div class="font-bold text-emerald-400">
                            {{ $qp->published_at->format('F d, Y \a\t h:i A') }}
                          </div>
                          <div class="text-[11px] text-slate-400">
                            {{ $qp->published_at->diffForHumans() }}
                          </div>
                        @else
                          <span class="text-slate-400">Next available sequence</span>
                        @endif
                      </td>
                      <td class="py-3 px-3 text-right space-x-2 whitespace-nowrap">
                        <form method="POST" action="{{ route('dashboard.queue.publish', $qp->id) }}" class="inline">
                          @csrf
                          <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 text-xs font-bold transition">
                            Publish Now
                          </button>
                        </form>
                        <form method="POST" action="{{ route('dashboard.queue.delete', $qp->id) }}" class="inline" onsubmit="return confirm('Delete this queued post?');">
                          @csrf
                          <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-500/20 hover:bg-rose-500/30 text-rose-400 text-xs font-bold transition">
                            Delete
                          </button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>


      <!-- ================= SECTION: BLOGS & APPEARANCE ================= -->
      @elseif(($section ?? '') === 'blogs')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          
          <!-- Edit Selected Blog -->
          <div class="p-6 lg:p-8 rounded-2xl bg-[#0f0716] border border-white/10">
            <!-- Blog Switcher Tabs -->
            <div class="mb-5 p-3.5 rounded-xl bg-white/[0.03] border border-white/10">
              <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-300 mb-2">Switch Blog to Edit Appearance &amp; Avatar:</label>
              <div class="flex flex-wrap gap-2">
                @if(isset($allBlogs))
                  @foreach($allBlogs as $bTab)
                    <a href="{{ route('dashboard.blogs', ['blog_id' => $bTab->id]) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 {{ $bTab->id === $blog->id ? 'bg-pink-500 text-white shadow-lg shadow-pink-500/30 ring-2 ring-pink-400/50' : 'bg-white/5 text-slate-300 hover:bg-white/10 hover:text-white border border-white/10' }}">
                      <span class="w-4 h-4 rounded-full overflow-hidden inline-block flex-shrink-0 bg-black/40">
                        @if(!empty($bTab->avatar_path))
                          <img src="{{ media_url($bTab->avatar_path) }}" class="w-full h-full object-cover">
                        @else
                          <span class="flex items-center justify-center w-full h-full text-[9px] text-pink-300 font-black">{{ initial($bTab->slug) }}</span>
                        @endif
                      </span>
                      <span>@if($bTab->is_main)@ {{ auth()->user()->username }} (Main Profile)@else/u/{{ auth()->user()->username }}/{{ $bTab->slug }}@endif</span>
                      @if($bTab->id === $blog->id)
                        <span class="text-[9px] bg-white/20 text-white uppercase px-1 rounded">Active</span>
                      @endif
                    </a>
                  @endforeach
                @endif
              </div>
            </div>

            <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
              <div>
                <h2 class="text-xl font-display font-black text-white">Edit Profile &amp; Appearance</h2>
                <p class="text-xs text-slate-400 mt-0.5">
                  @if($blog->is_main)
                    Customizing your <b class="text-pink-400">Primary Profile Blog</b>.
                  @else
                    Customizing Additional Blog: <b class="text-cyan-300 font-mono">/u/{{ auth()->user()->username }}/{{ $blog->slug }}</b>
                  @endif
                </p>
              </div>
              @if($blog->is_main)
                <span class="text-xs px-2.5 py-1 rounded-full bg-pink-500/20 text-pink-400 font-bold border border-pink-500/30">Main Profile Blog</span>
              @else
                <div class="flex items-center gap-2">
                  <span class="text-xs px-2.5 py-1 rounded-full bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/30">Additional Blog (@ {{ $blog->slug }})</span>
                  @if(auth()->user()->blogs()->count() > 1)
                    <form method="POST" action="{{ route('dashboard.blogs.delete', $blog->id) }}" onsubmit="return confirm('Are you sure you want to permanently delete additional blog @{{ $blog->slug }}? All posts in this blog will also be removed. This cannot be undone.');" class="inline">
                      @csrf
                      <button type="submit" class="px-2.5 py-1 rounded-full bg-rose-500/20 hover:bg-rose-500 border border-rose-500/40 text-xs font-bold text-rose-300 hover:text-white transition flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Delete Blog</span>
                      </button>
                    </form>
                  @endif
                </div>
              @endif
            </div>

            <form method="POST" action="{{ route('dashboard.blogs.update') }}" enctype="multipart/form-data" class="space-y-4">
              @csrf
              <input type="hidden" name="blog_id" value="{{ $blog->id }}">

              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Blog Title</label>
                <input type="text" name="title" value="{{ $blog->title ?? '' }}" required class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-pink-500 transition">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Tagline</label>
                <input type="text" name="tagline" value="{{ $blog->tagline ?? '' }}" placeholder="A short bio headline" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-pink-500 transition">
              </div>

              @if($blog->is_main)
                <!-- Primary Blog Profile Info (Bio, 3 URLs & Contact Email) -->
                <div class="pt-4 border-t border-white/10 space-y-3">
                  <div class="flex items-center justify-between">
                    <h3 class="text-xs font-display font-bold uppercase tracking-wider text-pink-400">Primary Blog Profile (Bio, 3 URLs & Contact Email)</h3>
                    <span class="text-[10px] bg-pink-500/20 text-pink-300 font-bold px-2 py-0.5 rounded uppercase">User Profile Blog</span>
                  </div>
                  <p class="text-[11px] text-slate-400">
                    Your primary blog created on signup acts as your user profile. Fill out your Bio, up to 3 website links, and contact email to appear under your header banner.
                  </p>

                  <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">About / Bio</label>
                    <textarea name="bio" rows="3" placeholder="Tell visitors about yourself and your microblog..." class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-pink-500 transition">{{ $blog->bio ?? '' }}</textarea>
                  </div>

                  <div>
                    <label class="block text-xs text-slate-400 mb-1">Website URL #1</label>
                    <input type="url" name="website1" value="{{ $blog->website1 ?? '' }}" placeholder="https://myfanpage.com" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-pink-500 transition">
                  </div>

                  <div>
                    <label class="block text-xs text-slate-400 mb-1">Website URL #2</label>
                    <input type="url" name="website2" value="{{ $blog->website2 ?? '' }}" placeholder="https://twitter.com/myhandle" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-pink-500 transition">
                  </div>

                  <div>
                    <label class="block text-xs text-slate-400 mb-1">Website URL #3</label>
                    <input type="url" name="website3" value="{{ $blog->website3 ?? '' }}" placeholder="https://instagram.com/myhandle" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-pink-500 transition">
                  </div>

                  <div>
                    <label class="block text-xs text-slate-400 mb-1">Contact Email</label>
                    <input type="email" name="contact_email" value="{{ $blog->contact_email ?? '' }}" placeholder="contact@creator.com" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-pink-500 transition">
                  </div>
                </div>
              @else
                <div class="pt-4 border-t border-white/10 p-3.5 rounded-xl bg-white/[0.02] border border-white/5 text-xs text-slate-400">
                  <div class="font-bold text-pink-400 flex items-center gap-1.5 mb-1">
                    <span>{!! icon('info') !!}</span>
                    <span>Additional Content Blog</span>
                  </div>
                  <p>
                    Profile bio, 3 website URLs, and contact email are anchored exclusively to your primary account blog (<a href="{{ route('dashboard.blogs', ['blog_id' => $user->mainBlog?->id]) }}" class="text-pink-400 font-semibold underline">@ {{ $user->mainBlog?->slug }}</a>). Additional blogs are dedicated purely to independent content, branding, and color customization!
                  </p>
                </div>
              @endif

              <!-- Avatar & Cover with Live Previews -->
              <div class="pt-4 border-t border-white/10 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Avatar Icon</label>
                  <div class="flex items-center gap-3 mb-2">
                    <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-pink-500/50 bg-[#170a22] flex items-center justify-center flex-shrink-0">
                      @if(!empty($blog->avatar_path))
                        <img id="avatarPreview" src="{{ media_url($blog->avatar_path) }}" alt="Avatar" class="w-full h-full object-cover">
                      @else
                        <img id="avatarPreview" src="{{ asset('assets/logo.png') }}" alt="Avatar" class="w-full h-full object-cover hidden">
                        <span id="avatarFallback" class="font-display font-black text-xl text-pink-400">{{ initial($blog->slug) }}</span>
                      @endif
                    </div>
                    <div class="flex-1">
                      <input type="file" name="avatar" id="avatarFileInput" accept="image/*" class="w-full text-xs text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-white/10 file:text-white hover:file:bg-white/20 cursor-pointer" onchange="previewAvatar(this);">
                      <input type="hidden" name="avatar_data" id="avatarDataInput">
                      <span class="text-[10px] text-slate-400">Square JPG, PNG, GIF, WebP (rec. 200x200)</span>
                    </div>
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Cover Banner</label>
                  <div class="mb-2">
                    <div id="coverPreviewContainer" class="w-full h-14 rounded-xl border border-white/10 overflow-hidden bg-cover bg-center mb-1.5" style="{{ !empty($blog->cover_path) ? "background-image:url('" . media_url($blog->cover_path) . "');" : 'background-color:rgba(255,255,255,0.03);' }}"></div>
                    <input type="file" name="cover" id="coverFileInput" accept="image/*" class="w-full text-xs text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-white/10 file:text-white hover:file:bg-white/20 cursor-pointer" onchange="previewCover(this);">
                    <input type="hidden" name="cover_data" id="coverDataInput">
                    <span class="text-[10px] text-slate-400">Landscape banner (rec. 1200x300)</span>
                  </div>
                </div>
              </div>

              <script>
                var maxImageMbBlade = {{ $settings['max_image_mb'] ?? 5 }};
                function previewAvatar(input) {
                  if (input.files && input.files[0]) {
                    var file = input.files[0];
                    if (file.size > maxImageMbBlade * 1024 * 1024) {
                      alert('Selected avatar image is too large (' + (file.size / (1024*1024)).toFixed(1) + 'MB). Maximum allowed image size configured in Admin is ' + maxImageMbBlade + 'MB.');
                      input.value = '';
                      return;
                    }
                    var reader = new FileReader();
                    reader.onload = function(e) {
                      var img = document.getElementById('avatarPreview');
                      var fb = document.getElementById('avatarFallback');
                      if (img) {
                        img.src = e.target.result;
                        img.classList.remove('hidden');
                      }
                      if (fb) fb.classList.add('hidden');
                      var dataInput = document.getElementById('avatarDataInput');
                      if (dataInput) dataInput.value = e.target.result;
                    };
                    reader.readAsDataURL(file);
                  }
                }

                function previewCover(input) {
                  if (input.files && input.files[0]) {
                    var file = input.files[0];
                    if (file.size > maxImageMbBlade * 1024 * 1024) {
                      alert('Selected cover banner is too large (' + (file.size / (1024*1024)).toFixed(1) + 'MB). Maximum allowed image size configured in Admin is ' + maxImageMbBlade + 'MB.');
                      input.value = '';
                      return;
                    }
                    var reader = new FileReader();
                    reader.onload = function(e) {
                      var container = document.getElementById('coverPreviewContainer');
                      if (container) container.style.backgroundImage = 'url(' + e.target.result + ')';
                      var dataInput = document.getElementById('coverDataInput');
                      if (dataInput) dataInput.value = e.target.result;
                    };
                    reader.readAsDataURL(file);
                  }
                }
              </script>

              <!-- Color Picker -->
              <div class="pt-4 border-t border-white/10">
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Accent Color</label>
                <div class="flex items-center gap-3">
                  <input type="color" name="color" value="{{ $blog->color ?? '#ff2bd6' }}" class="w-12 h-10 rounded-lg cursor-pointer bg-transparent border-0">
                  <span class="text-xs font-mono text-slate-400">{{ $blog->color ?? '#ff2bd6' }}</span>
                </div>
              </div>

              <div class="pt-4">
                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-pink-500 to-fuchsia-600 hover:from-pink-400 hover:to-fuchsia-500 text-white font-bold text-sm shadow-lg shadow-pink-500/20 transition">
                  Save Profile Settings
                </button>
              </div>
            </form>
          </div>

          <!-- Unlimited Blogs & Multi-Blog Network -->
          <div class="space-y-6">
            
            <!-- Create New Blog Card -->
            <div class="p-6 lg:p-8 rounded-2xl bg-[#0f0716] border border-white/10">
              <h2 class="text-xl font-display font-black text-white mb-2">Create Additional Blog</h2>
              <p class="text-xs text-slate-400 mb-6">
                You can create unlimited separate blogs under your account. Your main blog lives at <b class="text-pink-400 font-mono">/u/{{ auth()->user()->username }}/</b>, and additional blogs are reached at <b class="text-cyan-400 font-mono">/u/{{ auth()->user()->username }}/[blog-name]/</b>.
              </p>

              <form method="POST" action="{{ route('dashboard.blogs.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Blog Slug &amp; URL Path</label>
                  <div class="flex rounded-xl bg-[#170a22] border border-white/10 overflow-hidden focus-within:border-pink-500 transition items-center">
                    <span class="px-3 py-3 text-xs text-pink-400 bg-pink-500/10 font-mono font-bold select-none">/u/{{ auth()->user()->username }}/</span>
                    <input type="text" name="slug" id="newBlogSlugInput" required placeholder="art" class="flex-1 bg-transparent px-2 py-3 text-sm text-white focus:outline-none lowercase font-mono" oninput="document.getElementById('subdomainPreviewText').textContent = '{{ url('/u/' . auth()->user()->username) }}/' + (this.value.trim().toLowerCase() || 'art')">
                  </div>
                  <p class="text-[11px] text-cyan-400 font-mono mt-1.5 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 inline"><use href="#i-link"/></svg>
                    Direct Blog URL: <span id="subdomainPreviewText" class="text-white font-bold">{{ url('/u/' . auth()->user()->username) }}/art</span>
                  </p>
                </div>

                <div>
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Blog Display Title</label>
                  <input type="text" name="title" required placeholder="My Art Portfolio" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-pink-500 transition">
                </div>

                <div>
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Tagline (Optional)</label>
                  <input type="text" name="tagline" placeholder="Drawings and unfiltered thoughts" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-pink-500 transition">
                </div>

                <!-- Avatar & Cover Upload for New Blog -->
                <div class="pt-3 border-t border-white/10 grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Avatar Icon (Optional)</label>
                    <div class="flex items-center gap-2.5">
                      <div class="w-11 h-11 rounded-full overflow-hidden border-2 border-cyan-500/50 bg-[#170a22] flex items-center justify-center flex-shrink-0">
                        <img id="newBlogAvatarPreview" src="" alt="Avatar" class="w-full h-full object-cover hidden">
                        <span id="newBlogAvatarFallback" class="font-display font-black text-sm text-cyan-400">+</span>
                      </div>
                      <div class="flex-1">
                        <input type="file" name="avatar" accept="image/*" class="w-full text-[11px] text-slate-400 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-white/10 file:text-white hover:file:bg-white/20 cursor-pointer" onchange="previewNewBlogAvatar(this);">
                        <input type="hidden" name="avatar_data" id="newBlogAvatarData">
                        <span class="text-[9px] text-slate-400 block mt-0.5">Square image (200x200)</span>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Cover Banner (Optional)</label>
                    <div>
                      <div id="newBlogCoverPreview" class="w-full h-11 rounded-lg border border-white/10 overflow-hidden bg-cover bg-center mb-1 bg-white/[0.03]"></div>
                      <input type="file" name="cover" accept="image/*" class="w-full text-[11px] text-slate-400 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-white/10 file:text-white hover:file:bg-white/20 cursor-pointer" onchange="previewNewBlogCover(this);">
                      <input type="hidden" name="cover_data" id="newBlogCoverData">
                      <span class="text-[9px] text-slate-400 block mt-0.5">Landscape banner (1200x300)</span>
                    </div>
                  </div>
                </div>

                <script>
                  function previewNewBlogAvatar(input) {
                    if (input.files && input.files[0]) {
                      var file = input.files[0];
                      if (file.size > maxImageMbBlade * 1024 * 1024) {
                        alert('Selected avatar is too large (' + (file.size / (1024*1024)).toFixed(1) + 'MB). Maximum allowed image size is ' + maxImageMbBlade + 'MB.');
                        input.value = '';
                        return;
                      }
                      var reader = new FileReader();
                      reader.onload = function(e) {
                        var img = document.getElementById('newBlogAvatarPreview');
                        var fb = document.getElementById('newBlogAvatarFallback');
                        if (img) { img.src = e.target.result; img.classList.remove('hidden'); }
                        if (fb) fb.classList.add('hidden');
                        var d = document.getElementById('newBlogAvatarData');
                        if (d) d.value = e.target.result;
                      };
                      reader.readAsDataURL(file);
                    }
                  }
                  function previewNewBlogCover(input) {
                    if (input.files && input.files[0]) {
                      var file = input.files[0];
                      if (file.size > maxImageMbBlade * 1024 * 1024) {
                        alert('Selected cover banner is too large (' + (file.size / (1024*1024)).toFixed(1) + 'MB). Maximum allowed image size is ' + maxImageMbBlade + 'MB.');
                        input.value = '';
                        return;
                      }
                      var reader = new FileReader();
                      reader.onload = function(e) {
                        var c = document.getElementById('newBlogCoverPreview');
                        if (c) c.style.backgroundImage = 'url(' + e.target.result + ')';
                        var d = document.getElementById('newBlogCoverData');
                        if (d) d.value = e.target.result;
                      };
                      reader.readAsDataURL(file);
                    }
                  }
                </script>

                <div>
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Theme Color</label>
                  <input type="color" name="color" value="#19e3ff" class="w-12 h-10 rounded-lg cursor-pointer bg-transparent border-0">
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-pink-500 to-fuchsia-600 hover:from-pink-400 hover:to-fuchsia-500 text-white font-bold text-sm transition shadow-lg shadow-pink-500/20">
                  Create New Blog
                </button>
              </form>
            </div>

            <!-- List of All Blogs -->
            <div class="p-6 rounded-2xl bg-[#0f0716] border border-white/10">
              <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-display font-bold text-white">All Your Blogs ({{ isset($allBlogs) ? $allBlogs->count() : 1 }})</h3>
                <span class="text-[11px] text-slate-400">Click &ldquo;Edit Appearance&rdquo; to customize</span>
              </div>
              <div class="space-y-2.5">
                @if(isset($allBlogs))
                  @foreach($allBlogs as $ub)
                    <div class="p-3.5 rounded-xl transition flex items-center justify-between gap-3 {{ $ub->id === $blog->id ? 'bg-pink-500/[0.08] border-2 border-pink-500/60 shadow-lg shadow-pink-500/10' : 'bg-white/[0.02] border border-white/10 hover:bg-white/5' }}">
                      <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-full overflow-hidden border border-white/20 bg-black/40 flex-shrink-0 flex items-center justify-center">
                          @if(!empty($ub->avatar_path))
                            <img src="{{ media_url($ub->avatar_path) }}" alt="" class="w-full h-full object-cover">
                          @else
                            <span class="text-xs font-black text-pink-400 font-display">{{ initial($ub->slug) }}</span>
                          @endif
                        </div>
                        <div class="min-w-0">
                          <div class="font-bold text-white text-sm flex items-center gap-2 flex-wrap">
                            <span class="truncate">@if($ub->is_main)/u/{{ auth()->user()->username }} (Main)@else/u/{{ auth()->user()->username }}/{{ $ub->slug }}@endif</span>
                            @if($ub->is_main)
                              <span class="text-[10px] bg-pink-500/20 text-pink-400 font-sans px-1.5 py-0.5 rounded font-bold uppercase">Main Profile</span>
                            @else
                              <span class="text-[10px] bg-cyan-500/20 text-cyan-300 font-sans px-1.5 py-0.5 rounded font-bold uppercase">Additional Blog</span>
                            @endif
                            @if($ub->id === $blog->id)
                              <span class="text-[9px] bg-pink-500 text-white uppercase px-1.5 py-0.5 rounded font-black tracking-wider animate-pulse">Currently Editing</span>
                            @endif
                          </div>
                          <p class="text-xs text-slate-400 truncate">{{ $ub->title }} &middot; {{ $ub->posts()->count() }} posts</p>
                        </div>
                      </div>
                      <div class="flex items-center gap-1.5 flex-shrink-0">
                        <a href="{{ route('dashboard.blogs', ['blog_id' => $ub->id]) }}" title="Edit appearance, avatar, and cover" class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1 {{ $ub->id === $blog->id ? 'bg-pink-500 text-white shadow-md shadow-pink-500/30' : 'bg-white/10 hover:bg-white/20 text-slate-200 border border-white/10' }}">
                          <svg class="w-3.5 h-3.5 inline"><use href="#i-edit"/></svg>
                          <span>Edit</span>
                        </a>
                        <a href="{{ blog_url($ub) }}" target="_blank" title="Open blog page" class="px-2.5 py-1.5 rounded-lg bg-pink-500/20 hover:bg-pink-500/30 border border-pink-500/30 text-xs font-bold text-pink-300 hover:text-white transition flex items-center gap-1">
                          <span>View</span> &rarr;
                        </a>
                        @if(!$ub->is_main && auth()->user()->blogs()->count() > 1)
                          <form method="POST" action="{{ route('dashboard.blogs.delete', $ub->id) }}" onsubmit="return confirm('Are you sure you want to permanently delete additional blog @{{ $ub->slug }}? All posts under this blog will also be removed. This cannot be undone.');" class="inline">
                            @csrf
                            <button type="submit" title="Delete additional blog" class="px-2 py-1.5 rounded-lg bg-rose-500/20 hover:bg-rose-500 border border-rose-500/30 text-xs font-bold text-rose-300 hover:text-white transition flex items-center gap-1">
                              <svg class="w-3.5 h-3.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                              <span>Delete</span>
                            </button>
                          </form>
                        @endif
                      </div>
                    </div>
                  @endforeach
                @endif
              </div>
            </div>

          </div>
        </div>


      <!-- ================= SECTION: REFERRALS ================= -->
      @elseif(($section ?? '') === 'communities')
        @include('dashboard.partials.communities')
      @elseif(($section ?? '') === 'classifieds')
        @include('dashboard.partials.classifieds')
      @elseif(($section ?? '') === 'referrals')

        <div class="max-w-4xl mx-auto space-y-6">
          <div class="p-6 lg:p-8 rounded-2xl bg-[#0f0716] border border-white/10">
            <h2 class="text-2xl font-display font-black text-white mb-2">Invite Friends & Earn Points</h2>
            <p class="text-xs lg:text-sm text-slate-400 mb-6 leading-relaxed">
              Share your referral link with friends and creators. For every registered creator who joins with your link, you instantly earn <b class="text-amber-400">{{ $rewardPerRef ?? 100 }} points</b> to boost your blog views in the Ad Marketplace!
            </p>

            <!-- Referral Link Box -->
            <div class="p-4 rounded-xl bg-black/40 border border-white/10 mb-6">
              <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Your Personal Referral Link</label>
              <div class="flex flex-col sm:flex-row gap-2">
                <input type="text" readonly value="{{ url('/register?ref=' . auth()->user()->referral_code) }}" id="refLinkInput" class="flex-1 bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-pink-400 font-mono font-bold select-all focus:outline-none">
                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('refLinkInput').value);alert('Referral link copied to clipboard!');" class="px-5 py-3 rounded-xl bg-pink-500 hover:bg-pink-400 text-white font-bold text-xs uppercase tracking-wider transition flex items-center justify-center gap-2">
                  <span>Copy Link</span>
                </button>
              </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 gap-4 mb-6">
              <div class="p-4 rounded-xl bg-white/[0.02] border border-white/10">
                <span class="text-xs text-slate-400 uppercase font-semibold">Friends Invited</span>
                <div class="text-2xl font-display font-black text-white mt-1">{{ $referrals->count() }}</div>
              </div>
              <div class="p-4 rounded-xl bg-white/[0.02] border border-white/10">
                <span class="text-xs text-slate-400 uppercase font-semibold">Total Points Earned</span>
                <div class="text-2xl font-display font-black text-amber-400 mt-1">{{ number_format($totalEarned) }} pts</div>
              </div>
            </div>

            <!-- Table of Invited Members -->
            <h3 class="text-sm font-display font-bold text-white mb-3">Referred Members</h3>
            @if($referrals->isEmpty())
              <div class="py-8 text-center text-slate-400 text-xs">
                No members have registered through your link yet. Share your link on Twitter, Telegram, or Discord!
              </div>
            @else
              <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                  <thead>
                    <tr class="text-slate-400 border-b border-white/10 text-xs uppercase font-semibold">
                      <th class="py-2.5 px-3">User</th>
                      <th class="py-2.5 px-3">Registered</th>
                      <th class="py-2.5 px-3 text-right">Points Rewarded</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-white/5 text-slate-300">
                    @foreach($referrals as $refUser)
                      <tr class="hover:bg-white/[0.02] transition">
                        <td class="py-3 px-3 font-semibold text-white">@ {{ $refUser->username }}</td>
                        <td class="py-3 px-3 text-xs text-slate-400">{{ $refUser->created_at->diffForHumans() }}</td>
                        <td class="py-3 px-3 text-right text-xs font-bold text-emerald-400">+{{ $rewardPerRef ?? 100 }} pts</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          </div>
        </div>


      <!-- ================= SECTION: MESSAGES / INBOX ================= -->
      @elseif(($section ?? '') === 'messages')
        @php
          $currentTab = ($tab ?? 'inbox') === 'blocked' ? 'blocked' : 'inbox';
          $partnerUser = $partner ?? null;
          $maxMbLabel = rtrim(rtrim(number_format((float) ($maxMessageMb ?? 10), 1, '.', ''), '0'), '.');
        @endphp

        <div class="max-w-6xl mx-auto">
          <div class="mb-4 flex flex-col sm:flex-row sm:items-end justify-between gap-3">
            <div>
              <h2 class="text-xl font-display font-black text-white">Private Messages</h2>
              <p class="text-xs lg:text-sm text-slate-400 mt-1">Message any member. No follow is required. Block someone if you do not want to hear from them.</p>
            </div>
            <button type="button" onclick="document.getElementById('composeCard').classList.toggle('hidden')" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-pink-500 to-fuchsia-600 hover:from-pink-400 hover:to-fuchsia-500 text-white font-bold text-xs shadow-lg shadow-pink-500/20 transition flex items-center gap-2 self-start">
              {!! icon('plus') !!}
              <span>New Message</span>
            </button>
          </div>

          <div id="composeCard" class="hidden mb-4 p-4 rounded-xl bg-[#170a22] border border-pink-500/30">
            <form method="GET" action="{{ route('dashboard.messages') }}" class="flex flex-col sm:flex-row gap-2 sm:items-end">
              <div class="flex-1">
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Open a chat with</label>
                <div class="relative">
                  <span class="absolute left-3 top-2.5 text-xs text-pink-400 font-bold">@</span>
                  <input type="text" name="with" required placeholder="username" class="w-full bg-[#07030d] border border-white/10 rounded-xl pl-7 pr-3 py-2 text-xs text-white focus:outline-none focus:border-pink-500">
                </div>
              </div>
              <button type="submit" class="px-4 py-2 rounded-xl bg-pink-500 hover:bg-pink-400 text-white font-bold text-xs">Open chat</button>
            </form>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
            <aside class="lg:col-span-4 rounded-2xl bg-[#0f0716] border border-white/10 overflow-hidden {{ $partnerUser ? 'hidden lg:block' : '' }}">
              <div class="flex gap-2 p-3 border-b border-white/10">
                <a href="{{ route('dashboard.messages') }}" class="px-3 py-1.5 rounded-lg text-xs font-bold {{ $currentTab === 'inbox' ? 'bg-pink-500 text-white' : 'bg-white/5 text-slate-400 hover:text-white' }}">Inbox</a>
                <a href="{{ route('dashboard.messages', ['tab' => 'blocked']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold {{ $currentTab === 'blocked' ? 'bg-pink-500 text-white' : 'bg-white/5 text-slate-400 hover:text-white' }}">Blocked ({{ isset($blockedUsers) ? $blockedUsers->count() : 0 }})</a>
              </div>

              @if($currentTab === 'blocked')
                <div class="p-3 space-y-2 max-h-[640px] overflow-y-auto custom-scrollbar">
                  @forelse(($blockedUsers ?? collect()) as $blockedUser)
                    <div class="flex items-center justify-between gap-2 p-3 rounded-xl bg-[#170a22] border border-white/10">
                      <a href="{{ url('/u/' . $blockedUser->username) }}" class="text-sm font-bold text-white hover:text-pink-300">&#64;{{ $blockedUser->username }}</a>
                      <form method="POST" action="{{ route('action.block') }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $blockedUser->id }}">
                        <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white/5 hover:bg-white/10 text-cyan-300">Unblock</button>
                      </form>
                    </div>
                  @empty
                    <p class="text-xs text-slate-400 px-2 py-8 text-center">You have not blocked anyone.</p>
                  @endforelse
                </div>
              @else
                <div class="max-h-[640px] overflow-y-auto custom-scrollbar">
                  @forelse(($conversations ?? collect()) as $conversation)
                    @php
                      $other = $conversation['user'] ?? null;
                      $otherName = $other->username ?? 'Former member';
                      $otherBlog = $other->mainBlog ?? null;
                      $color = $otherBlog && $otherBlog->color ? $otherBlog->color : '#ff2bd6';
                      $isActive = $partnerUser && $other && (int) $partnerUser->id === (int) $other->id;
                      $preview = trim((string) ($conversation['last']->body ?? ''));
                      if ($preview === '' && !empty($conversation['last']->attachment_name)) {
                          $preview = 'Sent a file';
                      }
                    @endphp
                    @if($other)
                      <a href="{{ route('dashboard.messages', ['with' => $otherName]) }}" class="flex items-start gap-3 px-3 py-3 border-b border-white/5 hover:bg-white/5 {{ $isActive ? 'bg-pink-500/10' : '' }}">
                        <span class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs text-white flex-shrink-0" style="background:{{ $color }}">{{ initial($otherName) }}</span>
                        <span class="min-w-0 flex-1">
                          <span class="flex items-center justify-between gap-2">
                            <span class="text-sm font-bold text-white truncate">&#64;{{ $otherName }}</span>
                            @if(($conversation['unread'] ?? 0) > 0)
                              <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-cyan-400 text-slate-950 font-black">{{ $conversation['unread'] }}</span>
                            @endif
                          </span>
                          <span class="block text-[11px] text-slate-400 truncate">{{ \Illuminate\Support\Str::limit($preview, 70) }}</span>
                        </span>
                      </a>
                    @endif
                  @empty
                    <div class="px-4 py-12 text-center text-slate-400">
                      <p class="text-sm font-bold text-white mb-1">No conversations yet</p>
                      <p class="text-xs">Open a chat, or use Message on someone&apos;s profile.</p>
                    </div>
                  @endforelse
                </div>
              @endif
            </aside>

            <section class="lg:col-span-8 rounded-2xl bg-[#0f0716] border border-white/10 min-h-[520px] flex-col {{ $partnerUser ? 'flex' : 'hidden lg:flex' }}">
              @if($partnerUser)
                @php
                  $partnerBlog = $partnerUser->mainBlog;
                  $partnerColor = $partnerBlog && $partnerBlog->color ? $partnerBlog->color : '#ff2bd6';
                @endphp
                <div class="flex items-center justify-between gap-3 p-4 border-b border-white/10">
                  <div class="flex items-center gap-3 min-w-0">
                    <a href="{{ route('dashboard.messages') }}" class="lg:hidden text-slate-400 hover:text-white">{!! icon('left') !!}</a>
                    <span class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs text-white flex-shrink-0" style="background:{{ $partnerColor }}">{{ initial($partnerUser->username) }}</span>
                    <div class="min-w-0">
                      <a href="{{ url('/u/' . $partnerUser->username) }}" class="font-bold text-white hover:text-pink-300 truncate block">&#64;{{ $partnerUser->username }}</a>
                      <p class="text-[11px] text-slate-400">Direct conversation</p>
                    </div>
                  </div>
                  <form method="POST" action="{{ route('action.block') }}" onsubmit="return confirm({{ json_encode(!empty($isBlockedByMe) ? 'Unblock @'.$partnerUser->username.'?' : 'Block @'.$partnerUser->username.'? You will not be able to message each other until you unblock.') }});">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $partnerUser->id }}">
                    <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold border border-white/10 {{ !empty($isBlockedByMe) ? 'text-cyan-300 bg-cyan-500/10' : 'text-rose-300 bg-rose-500/10' }}">
                      {{ !empty($isBlockedByMe) ? 'Unblock' : 'Block' }}
                    </button>
                  </form>
                </div>

                <div id="pmThread" class="flex-1 overflow-y-auto max-h-[520px] p-4 space-y-3 custom-scrollbar">
                  @if(!empty($threadHasOlder))
                    <p class="text-center text-[11px] text-slate-500">Showing the latest 100 messages.</p>
                  @endif
                  @forelse(($thread ?? collect()) as $m)
                    @php $mine = (int) $m->sender_id === (int) auth()->id(); @endphp
                    <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                      <div class="max-w-[85%] rounded-2xl px-3 py-2 {{ $mine ? 'bg-pink-500/15 border border-pink-500/30' : 'bg-white/5 border border-white/10' }}">
                        @if(!empty($m->subject) && !in_array($m->subject, ['Private message', 'Attachment'], true))
                          <p class="text-[11px] font-bold text-slate-200 mb-1">{{ $m->subject }}</p>
                        @endif
                        @if(trim((string) $m->body) !== '')
                          <p class="text-sm text-slate-100 whitespace-pre-wrap leading-relaxed">{{ $m->body }}</p>
                        @endif
                        @if($m->hasAttachment())
                          <div class="mt-2">
                            @if($m->attachmentKind() === 'image')
                              <a href="{{ $m->attachmentUrl() }}" target="_blank" rel="noopener">
                                <img src="{{ $m->attachmentUrl() }}" alt="{{ $m->attachment_name }}" class="max-h-64 rounded-lg border border-white/10">
                              </a>
                            @elseif($m->attachmentKind() === 'video')
                              <video src="{{ $m->attachmentUrl() }}" controls class="max-h-64 rounded-lg bg-black"></video>
                            @elseif($m->attachmentKind() === 'audio')
                              <audio src="{{ $m->attachmentUrl() }}" controls class="w-full"></audio>
                            @endif
                            <a href="{{ $m->attachmentUrl() }}" class="mt-1 inline-flex items-center gap-1 text-[11px] font-bold text-cyan-300 hover:text-cyan-200" @if($m->attachmentKind() === 'file') download @endif>
                              {!! icon('clip') !!}
                              <span>{{ $m->attachment_name }}</span>
                              <span class="text-slate-500 font-medium">({{ $m->attachmentSizeLabel() }})</span>
                            </a>
                          </div>
                        @endif
                        <div class="mt-1 flex items-center justify-between gap-3">
                          <span class="text-[10px] text-slate-500">{{ $m->created_at ? $m->created_at->diffForHumans() : '' }}</span>
                          <form method="POST" action="{{ route('dashboard.messages.delete', $m->id) }}" onsubmit="return confirm('Delete this message for both of you?');">
                            @csrf
                            <button type="submit" class="text-slate-500 hover:text-rose-400" title="Delete message">{!! icon('trash') !!}</button>
                          </form>
                        </div>
                      </div>
                    </div>
                  @empty
                    <p class="text-center text-sm text-slate-400 py-16">No messages yet. Say hello below.</p>
                  @endforelse
                </div>

                <div class="p-4 border-t border-white/10">
                  @if(!empty($isBlockedByMe))
                    <p class="text-sm text-rose-300">You blocked &#64;{{ $partnerUser->username }}. Unblock them to send a message.</p>
                  @elseif(!empty($hasBlockedMe))
                    <p class="text-sm text-rose-300">You cannot send messages to this user.</p>
                  @else
                    <form method="POST" action="{{ route('action.message') }}" enctype="multipart/form-data" class="space-y-2">
                      @csrf
                      <input type="hidden" name="recipient_id" value="{{ $partnerUser->id }}">
                      <input type="hidden" name="recipient_username" value="{{ $partnerUser->username }}">
                      <input type="text" name="subject" maxlength="190" placeholder="Subject (optional)" value="{{ old('subject') }}" class="w-full bg-[#07030d] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-pink-500">
                      <textarea name="body" rows="3" maxlength="3000" placeholder="Write a message..." class="w-full bg-[#07030d] border border-white/10 rounded-xl p-3 text-sm text-slate-100 focus:outline-none focus:border-pink-500">{{ old('body') }}</textarea>
                      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <label class="text-[11px] text-slate-400 flex items-center gap-2">
                          @if(($maxMessageMb ?? 10) > 0)
                            {!! icon('clip') !!}
                            <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.gif,.webp,.mp4,.webm,.mov,.mp3,.wav,.ogg,.m4a,.pdf,.txt,.zip" class="text-[11px] text-slate-300 file:mr-2 file:rounded-lg file:border-0 file:bg-white/10 file:px-2 file:py-1 file:text-white">
                            <span>Max {{ $maxMbLabel }} MB</span>
                          @else
                            <span>File attachments are turned off.</span>
                          @endif
                        </label>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-pink-500 hover:bg-pink-400 text-white font-bold text-xs">{!! icon('mail') !!} <span>Send</span></button>
                      </div>
                    </form>
                  @endif
                </div>
              @else
                <div class="flex-1 flex items-center justify-center p-8 text-center">
                  <div>
                    <div class="w-12 h-12 rounded-xl bg-white/5 text-slate-500 mx-auto flex items-center justify-center mb-3">{!! icon('mail') !!}</div>
                    <h3 class="font-bold text-white mb-1">{{ $threadNotice ?: 'Choose a conversation' }}</h3>
                    <p class="text-xs text-slate-400 max-w-sm">Pick someone from the inbox, or start a new message. You can also press Message on any member&apos;s profile.</p>
                  </div>
                </div>
              @endif
            </section>
          </div>
        </div>
        <script>
          const pmThread = document.getElementById('pmThread');
          if (pmThread) pmThread.scrollTop = pmThread.scrollHeight;
        </script>


      <!-- ================= SECTION: NOTIFICATIONS PREFERENCES ================= -->
      @elseif(($section ?? '') === 'notifications')

        <div class="max-w-2xl mx-auto space-y-6">
          <div class="p-6 lg:p-8 rounded-2xl bg-[#0f0716] border border-white/10 shadow-xl">
            <div class="mb-6 pb-6 border-b border-white/10">
              <h2 class="text-xl font-display font-black text-white flex items-center gap-2">
                <span>Notification &amp; Email Preferences</span>
              </h2>
              <p class="text-xs lg:text-sm text-slate-400 mt-1">
                Customize which email updates you receive from {{ site_name() }}. You can change these preferences at any time.
              </p>
            </div>

            <form method="POST" action="{{ route('dashboard.notifications.update') }}" class="space-y-6">
              @csrf

              <!-- Follower Alerts Card -->
              <div class="p-4 rounded-xl bg-[#170a22] border border-white/10 flex items-start justify-between gap-4">
                <div class="space-y-1">
                  <div class="flex items-center gap-2">
                    <span class="text-pink-400">{!! icon('users') !!}</span>
                    <h3 class="text-sm font-bold text-white">New Follower Alerts</h3>
                  </div>
                  <p class="text-xs text-slate-400 leading-relaxed">
                    Receive an email notification whenever another member starts following one of your blogs.
                  </p>
                </div>
                <label class="inline-flex items-center cursor-pointer flex-shrink-0 mt-1">
                  <input type="checkbox" name="notify_new_follower" value="1" {{ auth()->user()->prefersFollowerNotification() ? 'checked' : '' }} class="sr-only peer">
                  <div class="w-10 h-5 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-pink-500 relative"></div>
                </label>
              </div>

              <!-- Private Message Alerts Card -->
              <div class="p-4 rounded-xl bg-[#170a22] border border-white/10 flex items-start justify-between gap-4">
                <div class="space-y-1">
                  <div class="flex items-center gap-2">
                    <span class="text-cyan-400">{!! icon('mail') !!}</span>
                    <h3 class="text-sm font-bold text-white">Private Message Alerts</h3>
                  </div>
                  <p class="text-xs text-slate-400 leading-relaxed">
                    Receive an email notification whenever another member sends you a direct private message.
                  </p>
                </div>
                <label class="inline-flex items-center cursor-pointer flex-shrink-0 mt-1">
                  <input type="checkbox" name="notify_private_message" value="1" {{ auth()->user()->prefersMessageNotification() ? 'checked' : '' }} class="sr-only peer">
                  <div class="w-10 h-5 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-cyan-500 relative"></div>
                </label>
              </div>

              <div class="pt-2 flex items-center justify-between">
                <span class="text-xs text-slate-400">
                  Sending notifications to: <strong class="text-white">{{ auth()->user()->email }}</strong>
                </span>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-pink-500 to-pink-600 hover:from-pink-600 hover:to-pink-700 text-white font-display font-bold text-xs shadow-lg shadow-pink-500/25 transition">
                  Save Preferences
                </button>
              </div>
            </form>
          </div>
        </div>


      <!-- ================= SECTION: MY POSTS ================= -->
      @else

        <div class="p-6 lg:p-8 rounded-2xl bg-[#0f0716] border border-white/10">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-6 border-b border-white/10">
            <div>
              <h2 class="text-xl font-display font-black text-white">Published Posts</h2>
              <p class="text-xs lg:text-sm text-slate-400 mt-1">
                You have published {{ $myPosts->count() }} posts on your blog network.
              </p>
            </div>

            <a href="{{ route('dashboard.post.create') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-pink-500 to-fuchsia-600 hover:from-pink-400 hover:to-fuchsia-500 text-white font-bold text-xs shadow-lg shadow-pink-500/20 transition flex items-center gap-2 self-start">
              {!! icon('plus') !!}
              <span>Create New Post</span>
            </a>
          </div>

          @if($myPosts->isEmpty())
            <div class="py-16 text-center text-slate-400">
              <div class="w-12 h-12 rounded-xl bg-white/5 text-slate-500 mx-auto flex items-center justify-center mb-3">
                {!! icon('list') !!}
              </div>
              <h3 class="font-bold text-white text-base mb-1">No posts found</h3>
              <p class="text-xs text-slate-400 max-w-sm mx-auto mb-4">You haven't published any posts yet. Start sharing text, photos, quotes, or videos today!</p>
              <a href="{{ route('dashboard.post.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-pink-500 text-white text-xs font-bold hover:bg-pink-400 transition">
                {!! icon('plus') !!}
                <span>Create Your First Post</span>
              </a>
            </div>
          @else
            <div class="overflow-x-auto">
              <table class="w-full text-left text-sm">
                <thead>
                  <tr class="text-slate-400 border-b border-white/10 text-xs uppercase font-semibold">
                    <th class="py-3 px-3">Format</th>
                    <th class="py-3 px-3">Content / Title</th>
                    <th class="py-3 px-3">Views</th>
                    <th class="py-3 px-3">Published Date</th>
                    <th class="py-3 px-3 text-right">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-slate-300">
                  @foreach($myPosts as $p)
                    <tr class="hover:bg-white/[0.02] transition">
                      <td class="py-3 px-3">
                        <span class="text-xs uppercase px-2 py-0.5 rounded-full font-bold {{ $p->type === 'video' ? 'bg-cyan-500/20 text-cyan-400' : ($p->type === 'photo' ? 'bg-pink-500/20 text-pink-400' : 'bg-white/10 text-slate-300') }}">
                          {{ $p->type }}
                        </span>
                      </td>
                      <td class="py-3 px-3 font-semibold text-white max-w-sm truncate">
                        {{ $p->heading ?: ($p->caption ?: ($p->quote_text ?: 'Post #' . $p->id)) }}
                      </td>
                      <td class="py-3 px-3 text-slate-400 text-xs">👁️ {{ number_format($p->views_count) }}</td>
                      <td class="py-3 px-3 text-slate-400 text-xs">{{ time_ago($p->published_at) }}</td>
                      <td class="py-3 px-3 text-right">
                        <div class="inline-flex items-center gap-2">
                          <a href="{{ route('post.show', ['id' => $p->id]) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-white/5 hover:bg-white/10 text-xs font-bold text-pink-400 transition">
                            View Post &rarr;
                          </a>
                          <a href="{{ route('dashboard.post.edit', ['id' => $p->id]) }}" class="px-2.5 py-1 rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 text-xs font-bold text-cyan-400 transition">
                            Edit
                          </a>
                          <form method="POST" action="{{ route('dashboard.post.delete', ['id' => $p->id]) }}" onsubmit="return confirm('Are you sure you want to permanently delete this post?');" style="display:inline;margin:0;">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-xs font-bold text-red-400 transition" title="Delete post">
                              Delete
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>

      @endif

    </main>
  </div>
  <script src="{{ asset('js/rich-editor.js') }}"></script>
  <script>
    function syncDashboardThemeUI() {
      var theme = document.documentElement.getAttribute('data-theme') || 'dark';
      var isLight = theme === 'light';
      document.querySelectorAll('.theme-icon-dark').forEach(function(el) {
        el.style.display = isLight ? 'none' : 'inline-flex';
      });
      document.querySelectorAll('.theme-icon-light').forEach(function(el) {
        el.style.display = isLight ? 'inline-flex' : 'none';
      });
    }
    document.addEventListener('DOMContentLoaded', syncDashboardThemeUI);
    window.addEventListener('nina-theme-changed', syncDashboardThemeUI);
  </script>
</body>
</html>
