<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Admin &middot; {{ $title ?? 'Control Center' }} | {{ site_name() }}</title>
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
    
    /* Scrollbar */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #07030d; }
    ::-webkit-scrollbar-thumb { background: #231633; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #3c2457; }
  </style>
</head>
<body class="bg-[#07030d] text-slate-100 font-sans antialiased min-h-screen flex flex-col selection:bg-pink-500 selection:text-white">
  @include('partials.icons')

  <div class="flex-1 flex flex-col lg:flex-row min-h-screen">

    <!-- SIDEBAR NAVIGATION -->
    <aside class="w-full lg:w-72 bg-[#0c0514] border-r border-white/10 flex-shrink-0 flex flex-col justify-between p-4 lg:p-6 lg:sticky lg:top-0 lg:h-screen lg:overflow-y-auto">
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
              <span class="text-[10px] bg-pink-500/20 text-pink-400 font-sans px-1.5 py-0.5 rounded font-bold uppercase tracking-wider">Admin</span>
            </div>
            <p class="text-xs text-slate-400">Control & Moderation</p>
          </div>
        </a>

        <!-- Navigation Links -->
        <nav class="space-y-1">
          <div class="px-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">Main Control</div>
          
          <a href="{{ route('admin') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ ($section ?? '') === 'overview' ? 'bg-pink-600/15 text-pink-400 border-l-2 border-pink-500 font-bold' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
            <span class="flex items-center gap-3">
              {!! icon('shield') !!}
              <span>Overview</span>
            </span>
          </a>

          <a href="{{ route('admin.users') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ ($section ?? '') === 'users' ? 'bg-pink-600/15 text-pink-400 border-l-2 border-pink-500 font-bold' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
            <span class="flex items-center gap-3">
              {!! icon('users') !!}
              <span>Users</span>
            </span>
            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ ($section ?? '') === 'users' ? 'bg-pink-500/20 text-pink-300' : 'bg-white/10 text-slate-300' }}">{{ $allUsers->count() }}</span>
          </a>

          <a href="{{ route('admin.blogs') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ ($section ?? '') === 'blogs' ? 'bg-pink-600/15 text-pink-400 border-l-2 border-pink-500 font-bold' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
            <span class="flex items-center gap-3">
              {!! icon('compass') !!}
              <span>Unlimited Blogs</span>
            </span>
            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ ($section ?? '') === 'blogs' ? 'bg-pink-500/20 text-pink-300' : 'bg-white/10 text-slate-300' }}">{{ $allBlogs->count() }}</span>
          </a>

          <a href="{{ route('admin.posts') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ ($section ?? '') === 'posts' ? 'bg-pink-600/15 text-pink-400 border-l-2 border-pink-500 font-bold' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
            <span class="flex items-center gap-3">
              {!! icon('list') !!}
              <span>Platform Posts</span>
            </span>
            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ ($section ?? '') === 'posts' ? 'bg-pink-500/20 text-pink-300' : 'bg-white/10 text-slate-300' }}">{{ $allPosts->count() }}</span>
          </a>

          <div class="pt-4 px-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">Trust, Safety & Revenue</div>

          <a href="{{ route('admin.verifications') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ ($section ?? '') === 'verifications' ? 'bg-pink-600/15 text-pink-400 border-l-2 border-pink-500 font-bold' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
            <span class="flex items-center gap-3">
              {!! icon('verified') !!}
              <span>ID Approvals</span>
            </span>
            @php $pendingVerifs = $verifications->where('status','pending')->count(); @endphp
            @if($pendingVerifs > 0)
              <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 animate-pulse">{{ $pendingVerifs }}</span>
            @else
              <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-white/10 text-slate-300">0</span>
            @endif
          </a>

          <a href="{{ route('admin.donations') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ ($section ?? '') === 'donations' ? 'bg-pink-600/15 text-pink-400 border-l-2 border-pink-500 font-bold' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
            <span class="flex items-center gap-3">
              {!! icon('crown') !!}
              <span>Crypto Revenue</span>
            </span>
            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300">${{ number_format($donations->where('status','confirmed')->sum('amount_usd'), 0) }}</span>
          </a>

          <a href="{{ route('admin.referrals') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ ($section ?? '') === 'referrals' ? 'bg-pink-600/15 text-pink-400 border-l-2 border-pink-500 font-bold' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
            <span class="flex items-center gap-3">
              {!! icon('users') !!}
              <span>Referral Network</span>
            </span>
            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-white/10 text-slate-300">{{ $allUsers->where('referrals_count', '>', 0)->count() }}</span>
          </a>

          <a href="{{ route('admin.reports') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ ($section ?? '') === 'reports' ? 'bg-pink-600/15 text-pink-400 border-l-2 border-pink-500 font-bold' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
            <span class="flex items-center gap-3">
              {!! icon('flag') !!}
              <span>Reports</span>
            </span>
            @php $openReports = $reports->where('status','open')->count(); @endphp
            @if($openReports > 0)
              <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-500/20 text-rose-300">{{ $openReports }}</span>
            @else
              <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-white/10 text-slate-300">0</span>
            @endif
          </a>

          <a href="{{ route('admin.support') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ ($section ?? '') === 'support' ? 'bg-pink-600/15 text-pink-400 border-l-2 border-pink-500 font-bold' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
            <span class="flex items-center gap-3">
              {!! icon('mail') !!}
              <span>Support Desk</span>
            </span>
            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-white/10 text-slate-300">{{ $tickets->where('status','open')->count() }}</span>
          </a>

          <a href="{{ route('admin.logins') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ ($section ?? '') === 'logins' ? 'bg-pink-600/15 text-pink-400 border-l-2 border-pink-500 font-bold' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
            <span class="flex items-center gap-3">
              {!! icon('login') !!}
              <span>Security & IPs</span>
            </span>
          </a>

          <a href="{{ route('admin.ads') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ ($section ?? '') === 'ads' ? 'bg-pink-600/15 text-pink-400 border-l-2 border-pink-500 font-bold' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
            <span class="flex items-center gap-3">
              {!! icon('megaphone') !!}
              <span>Advertisements</span>
            </span>
            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ ($section ?? '') === 'ads' ? 'bg-pink-500/20 text-pink-300' : 'bg-white/10 text-slate-300' }}">{{ isset($allAds) ? $allAds->count() : 0 }}</span>
          </a>

          <a href="{{ route('admin.slider') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ ($section ?? '') === 'slider' ? 'bg-pink-600/15 text-pink-400 border-l-2 border-pink-500 font-bold' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
            <span class="flex items-center gap-3">
              {!! icon('photo') !!}
              <span>Hero Slider</span>
            </span>
            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ ($section ?? '') === 'slider' ? 'bg-pink-500/20 text-pink-300' : 'bg-white/10 text-slate-300' }}">{{ isset($heroSlides) ? count($heroSlides) : 0 }}</span>
          </a>

          <a href="{{ route('admin.pages') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ ($section ?? '') === 'pages' ? 'bg-pink-600/15 text-pink-400 border-l-2 border-pink-500 font-bold' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
            <span class="flex items-center gap-3">
              {!! icon('text') !!}
              <span>Static Pages</span>
            </span>
          </a>

          <a href="{{ route('admin.settings') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ ($section ?? '') === 'settings' ? 'bg-pink-600/15 text-pink-400 border-l-2 border-pink-500 font-bold' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
            <span class="flex items-center gap-3">
              {!! icon('settings') !!}
              <span>Site Settings</span>
            </span>
          </a>
        </nav>
      </div>

      <!-- Quick Outbound Links -->
      <div class="pt-6 border-t border-white/10 space-y-1">
        <a href="{{ url('/') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-semibold text-slate-400 hover:text-white rounded-lg hover:bg-white/5 transition">
          {!! icon('left') !!}
          <span>Back to Front Site</span>
        </a>
        <a href="{{ route('community') }}" target="_blank" class="flex items-center gap-3 px-3 py-2 text-xs font-semibold text-slate-400 hover:text-cyan-400 rounded-lg hover:bg-white/5 transition">
          {!! icon('users') !!}
          <span>Community Hub</span>
        </a>
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-semibold text-slate-400 hover:text-pink-400 rounded-lg hover:bg-white/5 transition">
          {!! icon('compass') !!}
          <span>User Dashboard</span>
        </a>
        <a href="{{ route('logout') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-semibold text-rose-400 hover:text-rose-300 rounded-lg hover:bg-rose-500/10 transition">
          {!! icon('login') !!}
          <span>Sign Out</span>
        </a>
      </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 min-w-0 p-4 lg:p-8 overflow-y-auto">
      
      <!-- TOP HEADER BAR -->
      <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 mb-8 border-b border-white/10">
        <div>
          <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
            <span>Admin</span>
            <span>/</span>
            <span class="text-pink-400 font-semibold capitalize">{{ $section ?? 'Overview' }}</span>
          </div>
          <h1 class="text-2xl lg:text-3xl font-display font-black tracking-tight text-white flex items-center gap-3">
            <span>Admin Control Panel</span>
            <span class="text-sm font-sans font-bold bg-pink-500/20 text-pink-400 px-2.5 py-0.5 rounded-full border border-pink-500/30">v2.0 Tailwind</span>
          </h1>
        </div>

        <div class="flex items-center gap-3 bg-[#0f0716] border border-white/10 px-4 py-2 rounded-xl">
          <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-pink-500 to-cyan-400 flex items-center justify-center font-bold text-black text-sm">
            {{ initial(auth()->user()->username ?? 'A') }}
          </div>
          <div>
            <div class="text-sm font-bold text-white flex items-center gap-1.5">
              <span>{{ auth()->user()->username ?? 'Admin' }}</span>
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            </div>
            <p class="text-[11px] text-slate-400">Master Administrator</p>
          </div>
        </div>
      </header>

      <!-- QUICK SUBNAV TABS -->
      <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-6 text-sm font-semibold custom-scrollbar">
        <a href="{{ route('admin') }}" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition {{ ($section ?? '') === 'overview' ? 'bg-pink-600 text-white shadow-lg shadow-pink-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">Overview</a>
        <a href="{{ route('admin.users') }}" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition {{ ($section ?? '') === 'users' ? 'bg-pink-600 text-white shadow-lg shadow-pink-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">Users ({{ $allUsers->count() }})</a>
        <a href="{{ route('admin.blogs') }}" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition {{ ($section ?? '') === 'blogs' ? 'bg-pink-600 text-white shadow-lg shadow-pink-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">Blogs ({{ $allBlogs->count() }})</a>
        <a href="{{ route('admin.posts') }}" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition {{ ($section ?? '') === 'posts' ? 'bg-pink-600 text-white shadow-lg shadow-pink-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">Posts ({{ $allPosts->count() }})</a>
        <a href="{{ route('admin.ads') }}" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition {{ ($section ?? '') === 'ads' ? 'bg-pink-600 text-white shadow-lg shadow-pink-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">Ads ({{ isset($allAds) ? $allAds->count() : 0 }})</a>
        <a href="{{ route('admin.slider') }}" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition {{ ($section ?? '') === 'slider' ? 'bg-pink-600 text-white shadow-lg shadow-pink-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">Hero Slider ({{ isset($heroSlides) ? count($heroSlides) : 0 }})</a>
        <a href="{{ route('admin.verifications') }}" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition {{ ($section ?? '') === 'verifications' ? 'bg-pink-600 text-white shadow-lg shadow-pink-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">ID Approvals ({{ $verifications->where('status','pending')->count() }})</a>
        <a href="{{ route('admin.donations') }}" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition {{ ($section ?? '') === 'donations' ? 'bg-pink-600 text-white shadow-lg shadow-pink-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">Donations (${{ number_format($donations->where('status','confirmed')->sum('amount_usd'), 0) }})</a>
        <a href="{{ route('admin.referrals') }}" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition {{ ($section ?? '') === 'referrals' ? 'bg-pink-600 text-white shadow-lg shadow-pink-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">Referrals ({{ $allUsers->where('referrals_count', '>', 0)->count() }})</a>
        <a href="{{ route('admin.reports') }}" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition {{ ($section ?? '') === 'reports' ? 'bg-pink-600 text-white shadow-lg shadow-pink-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">Reports ({{ $reports->where('status','open')->count() }})</a>
        <a href="{{ route('admin.support') }}" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition {{ ($section ?? '') === 'support' ? 'bg-pink-600 text-white shadow-lg shadow-pink-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">Support ({{ $tickets->where('status','open')->count() }})</a>
        <a href="{{ route('admin.logins') }}" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition {{ ($section ?? '') === 'logins' ? 'bg-pink-600 text-white shadow-lg shadow-pink-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">Security & IPs</a>
        <a href="{{ route('admin.pages') }}" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition {{ ($section ?? '') === 'pages' ? 'bg-pink-600 text-white shadow-lg shadow-pink-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">Static Pages</a>
        <a href="{{ route('admin.settings') }}" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition {{ ($section ?? '') === 'settings' ? 'bg-pink-600 text-white shadow-lg shadow-pink-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">Platform Settings</a>
      </div>

      <!-- FLASH MESSAGES -->
      @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span class="p-1 rounded-md bg-emerald-500/20">{!! icon('check') !!}</span>
            <span class="font-medium text-sm">{{ session('success') }}</span>
          </div>
        </div>
      @endif

      @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span class="p-1 rounded-md bg-rose-500/20">{!! icon('ban') !!}</span>
            <span class="font-medium text-sm">{{ session('error') }}</span>
          </div>
        </div>
      @endif

      <!-- KPI METRIC CARDS GRID -->
      <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
        <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-4 shadow-lg hover:border-pink-500/30 transition group">
          <div class="text-xs text-slate-400 font-semibold mb-1 flex items-center justify-between">
            <span>Total Posts</span>
            <span class="text-pink-400">{!! icon('list') !!}</span>
          </div>
          <div class="text-2xl font-display font-black text-white group-hover:text-pink-400 transition">{{ $allPosts->count() }}</div>
          <div class="text-[11px] text-slate-400 mt-1">Platform-wide</div>
        </div>

        <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-4 shadow-lg hover:border-cyan-500/30 transition group">
          <div class="text-xs text-slate-400 font-semibold mb-1 flex items-center justify-between">
            <span>Members</span>
            <span class="text-cyan-400">{!! icon('users') !!}</span>
          </div>
          <div class="text-2xl font-display font-black text-white group-hover:text-cyan-400 transition">{{ $allUsers->count() }}</div>
          <div class="text-[11px] text-cyan-400 mt-1 font-semibold">{{ $allUsers->where('badge_pro', 1)->count() }} Pro Members</div>
        </div>

        <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-4 shadow-lg hover:border-purple-500/30 transition group">
          <div class="text-xs text-slate-400 font-semibold mb-1 flex items-center justify-between">
            <span>Unlimited Blogs</span>
            <span class="text-purple-400">{!! icon('compass') !!}</span>
          </div>
          <div class="text-2xl font-display font-black text-white group-hover:text-purple-400 transition">{{ $allBlogs->count() }}</div>
          <div class="text-[11px] text-slate-400 mt-1">{{ $allBlogs->where('is_featured', 1)->count() }} Featured</div>
        </div>

        <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-4 shadow-lg hover:border-rose-500/30 transition group">
          <div class="text-xs text-slate-400 font-semibold mb-1 flex items-center justify-between">
            <span>Open Reports</span>
            <span class="text-rose-400">{!! icon('flag') !!}</span>
          </div>
          <div class="text-2xl font-display font-black text-white group-hover:text-rose-400 transition">{{ $reports->where('status', 'open')->count() }}</div>
          <div class="text-[11px] text-slate-400 mt-1">Requires action</div>
        </div>

        <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-4 shadow-lg hover:border-amber-500/30 transition group">
          <div class="text-xs text-slate-400 font-semibold mb-1 flex items-center justify-between">
            <span>ID Approvals</span>
            <span class="text-amber-400">{!! icon('verified') !!}</span>
          </div>
          <div class="text-2xl font-display font-black text-white group-hover:text-amber-400 transition">{{ $verifications->where('status', 'pending')->count() }}</div>
          <div class="text-[11px] text-amber-400 mt-1 font-semibold">Pending Review</div>
        </div>

        <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-4 shadow-lg hover:border-emerald-500/30 transition group">
          <div class="text-xs text-slate-400 font-semibold mb-1 flex items-center justify-between">
            <span>Crypto Raised</span>
            <span class="text-emerald-400">{!! icon('crown') !!}</span>
          </div>
          <div class="text-2xl font-display font-black text-white group-hover:text-emerald-400 transition">${{ number_format($donations->where('status', 'confirmed')->sum('amount_usd'), 0) }}</div>
          <div class="text-[11px] text-emerald-400 mt-1 font-semibold">Confirmed USD</div>
        </div>
      </div>

      <!-- ================= SECTION: OVERVIEW ================= -->
      @if(($section ?? '') === 'overview')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
          <!-- Pending ID Verification Alert Card -->
          <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-4">
                <h3 class="font-display font-bold text-lg text-white flex items-center gap-2">
                  <span class="text-amber-400">{!! icon('verified') !!}</span>
                  <span>ID Verifications</span>
                </h3>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300">{{ $verifications->where('status','pending')->count() }} Pending</span>
              </div>
              <p class="text-sm text-slate-300 mb-4 leading-relaxed">
                Creators submit legal identification to earn the Identity Verified badge and build subscriber trust.
              </p>
            </div>
            <a href="{{ route('admin.verifications') }}" class="w-full py-2.5 px-4 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 text-amber-300 font-bold text-sm text-center transition">
              Review Verification Queue &rarr;
            </a>
          </div>

          <!-- Open Moderation Reports Alert Card -->
          <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-4">
                <h3 class="font-display font-bold text-lg text-white flex items-center gap-2">
                  <span class="text-rose-400">{!! icon('flag') !!}</span>
                  <span>Content Moderation</span>
                </h3>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-500/20 text-rose-300">{{ $reports->where('status','open')->count() }} Open</span>
              </div>
              <p class="text-sm text-slate-300 mb-4 leading-relaxed">
                Community member reports on posts, blogs, copyright, or terms of service violations.
              </p>
            </div>
            <a href="{{ route('admin.reports') }}" class="w-full py-2.5 px-4 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-300 font-bold text-sm text-center transition">
              Manage Content Reports &rarr;
            </a>
          </div>

          <!-- Quick Config Card -->
          <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-4">
                <h3 class="font-display font-bold text-lg text-white flex items-center gap-2">
                  <span class="text-pink-400">{!! icon('settings') !!}</span>
                  <span>Platform Config</span>
                </h3>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-pink-500/20 text-pink-300">Active</span>
              </div>
              <p class="text-sm text-slate-300 mb-4 leading-relaxed">
                Configure NOWPayments API, BitCart, Google OAuth, Cloudflare Turnstile, and site branding.
              </p>
            </div>
            <a href="{{ route('admin.settings') }}" class="w-full py-2.5 px-4 rounded-xl bg-pink-500/10 hover:bg-pink-500/20 border border-pink-500/30 text-pink-300 font-bold text-sm text-center transition">
              Edit Platform Settings &rarr;
            </a>
          </div>
        </div>

        <!-- Recent Registered Users Table -->
        <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-6 shadow-xl mb-8">
          <div class="flex items-center justify-between mb-4">
            <h2 class="font-display font-bold text-lg text-white flex items-center gap-2">
              <span class="text-cyan-400">{!! icon('users') !!}</span>
              <span>Recent Registered Accounts</span>
            </h2>
            <a href="{{ route('admin.users') }}" class="text-xs font-bold text-pink-400 hover:text-pink-300 transition">View All Users &rarr;</a>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
              <thead class="text-xs uppercase bg-white/5 text-slate-400 border-b border-white/10">
                <tr>
                  <th class="p-3">ID</th>
                  <th class="p-3">Member</th>
                  <th class="p-3">Email</th>
                  <th class="p-3">Role</th>
                  <th class="p-3">Status</th>
                  <th class="p-3">Badges</th>
                  <th class="p-3">Joined</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/5">
                @foreach($allUsers->take(5) as $u)
                  <tr class="hover:bg-white/5 transition">
                    <td class="p-3 font-mono text-xs text-slate-400">#{{ $u->id }}</td>
                    <td class="p-3">
                      <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-pink-600/30 text-pink-300 flex items-center justify-center font-bold text-xs">
                          {{ initial($u->username) }}
                        </span>
                        <span class="font-bold text-white">@ {{ $u->username }}</span>
                      </div>
                    </td>
                    <td class="p-3 text-slate-400">{{ $u->email }}</td>
                    <td class="p-3">
                      <span class="px-2 py-0.5 rounded text-xs font-bold {{ $u->role === 'admin' ? 'bg-pink-500/20 text-pink-300' : 'bg-white/10 text-slate-300' }}">
                        {{ $u->role }}
                      </span>
                    </td>
                    <td class="p-3">
                      <span class="px-2 py-0.5 rounded text-xs font-bold {{ $u->status === 'active' ? 'bg-emerald-500/20 text-emerald-300' : ($u->status === 'suspended' ? 'bg-amber-500/20 text-amber-300' : 'bg-rose-500/20 text-rose-300') }}">
                        {{ $u->status }}
                      </span>
                    </td>
                    <td class="p-3">
                      {!! badge($u->badge_vip ? 'vip' : '') !!}
                      {!! badge($u->badge_id ? 'id' : '') !!}
                      {!! badge($u->badge_pro ? 'pro' : '') !!}
                    </td>
                    <td class="p-3 text-xs text-slate-400">{{ $u->created_at->diffForHumans() }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

      <!-- ================= SECTION: USERS ================= -->
      @elseif(($section ?? '') === 'users')
        <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-6 shadow-xl">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
              <h2 class="font-display font-bold text-xl text-white">Member Accounts, Badges & Revenue Status</h2>
              <p class="text-xs text-slate-400 mt-1">Audit Premium / Premium+ subscribers, badge holders (PRO, ID, VIP), last IP addresses, and referral performance.</p>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-xs font-bold text-slate-400 bg-white/5 px-3 py-1.5 rounded-lg border border-white/5">
                Total Accounts: <span class="text-pink-400 font-bold">{{ $allUsers->count() }}</span>
              </span>
            </div>
          </div>

          <!-- Quick Filter Buttons -->
          <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-6 border-b border-white/10 text-xs font-semibold custom-scrollbar" id="userFilterTabs">
            <button type="button" onclick="filterUsers('all')" class="user-tab-btn active px-3 py-1.5 rounded-lg bg-pink-600 text-white font-bold transition shadow-md shadow-pink-600/30" data-filter="all">
              All Accounts ({{ $allUsers->count() }})
            </button>
            <button type="button" onclick="filterUsers('premium')" class="user-tab-btn px-3 py-1.5 rounded-lg bg-white/5 text-slate-300 hover:bg-white/10 font-bold transition" data-filter="premium">
              💎 Premium ({{ $allUsers->filter(fn($u) => $u->plan === 'premium')->count() }})
            </button>
            <button type="button" onclick="filterUsers('premium_plus')" class="user-tab-btn px-3 py-1.5 rounded-lg bg-white/5 text-slate-300 hover:bg-white/10 font-bold transition" data-filter="premium_plus">
              ⚡ Premium+ ({{ $allUsers->filter(fn($u) => $u->plan === 'premium_plus')->count() }})
            </button>
            <button type="button" onclick="filterUsers('badge_pro')" class="user-tab-btn px-3 py-1.5 rounded-lg bg-white/5 text-slate-300 hover:bg-white/10 font-bold transition" data-filter="badge_pro">
              🛡️ PRO Badge ({{ $allUsers->where('badge_pro', 1)->count() }})
            </button>
            <button type="button" onclick="filterUsers('badge_id')" class="user-tab-btn px-3 py-1.5 rounded-lg bg-white/5 text-slate-300 hover:bg-white/10 font-bold transition" data-filter="badge_id">
              ✓ ID Verified ({{ $allUsers->where('badge_id', 1)->count() }})
            </button>
            <button type="button" onclick="filterUsers('badge_vip')" class="user-tab-btn px-3 py-1.5 rounded-lg bg-white/5 text-slate-300 hover:bg-white/10 font-bold transition" data-filter="badge_vip">
              👑 VIP Badge ({{ $allUsers->where('badge_vip', 1)->count() }})
            </button>
            <button type="button" onclick="filterUsers('referrers')" class="user-tab-btn px-3 py-1.5 rounded-lg bg-white/5 text-slate-300 hover:bg-white/10 font-bold transition" data-filter="referrers">
              👥 Referrers ({{ $allUsers->where('referrals_count', '>', 0)->count() }})
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300 border-collapse" id="usersTable">
              <thead class="text-xs uppercase bg-white/5 text-slate-400 border-b border-white/10">
                <tr>
                  <th class="p-3">ID</th>
                  <th class="p-3">User</th>
                  <th class="p-3">Subscription Plan</th>
                  <th class="p-3">Email &amp; Last IP</th>
                  <th class="p-3">Badges</th>
                  <th class="p-3">Status</th>
                  <th class="p-3">Points</th>
                  <th class="p-3">Referrals &amp; Earnings</th>
                  <th class="p-3">Quick Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/5">
                @foreach($allUsers as $u)
                  @php
                    $isPremium = $u->plan === 'premium';
                    $isPremiumPlus = $u->plan === 'premium_plus';
                    $hasReferrals = $u->referrals_count > 0;
                    $lastLog = $u->lastLogin;
                  @endphp
                  <tr class="user-row hover:bg-white/5 transition"
                      data-plan="{{ $u->plan }}"
                      data-badge-pro="{{ $u->badge_pro ? '1' : '0' }}"
                      data-badge-id="{{ $u->badge_id ? '1' : '0' }}"
                      data-badge-vip="{{ $u->badge_vip ? '1' : '0' }}"
                      data-has-referrals="{{ $hasReferrals ? '1' : '0' }}">
                    <td class="p-3 font-mono text-xs text-slate-400">#{{ $u->id }}</td>
                    <td class="p-3">
                      <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-gradient-to-tr from-pink-500/20 to-purple-500/20 border border-pink-500/30 text-pink-300 flex items-center justify-center font-bold text-xs flex-shrink-0">
                          {{ initial($u->username) }}
                        </span>
                        <div>
                          <div class="font-bold text-white flex items-center gap-1.5">
                            <span>@ {{ $u->username }}</span>
                          </div>
                          <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">
                            <span>{{ $u->role }}</span>
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="p-3">
                      <div class="mb-1">
                        @if($isPremiumPlus)
                          <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-black uppercase tracking-wider bg-fuchsia-500/20 text-fuchsia-300 border border-fuchsia-500/40">
                            ⚡ Premium+ ($30)
                          </span>
                        @elseif($isPremium)
                          <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-black uppercase tracking-wider bg-cyan-500/20 text-cyan-300 border border-cyan-500/40">
                            💎 Premium ($15)
                          </span>
                        @else
                          <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-white/5 text-slate-400 border border-white/10">
                            Free ($0)
                          </span>
                        @endif
                      </div>
                      <form method="POST" action="{{ route('admin.users.plan', $u->id) }}" class="flex items-center gap-1">
                        @csrf
                        <select name="plan" onchange="this.form.submit()" class="bg-[#07030d] border border-white/10 rounded px-2 py-1 text-[11px] text-white focus:outline-none focus:border-pink-500">
                          <option value="free" {{ $u->plan === 'free' ? 'selected' : '' }}>Set Free ($0)</option>
                          <option value="premium" {{ $u->plan === 'premium' ? 'selected' : '' }}>💎 Set Premium ($15 PRO)</option>
                          <option value="premium_plus" {{ $u->plan === 'premium_plus' ? 'selected' : '' }}>⚡ Set Premium+ ($30 VIP)</option>
                        </select>
                      </form>
                    </td>
                    <td class="p-3">
                      <div class="text-slate-300 font-mono text-xs flex items-center gap-1.5 flex-wrap">
                        <span>{{ $u->email }}</span>
                        @if($u->email_verified_at)
                          <span class="text-[10px] text-emerald-400 font-sans font-bold bg-emerald-500/10 px-1 rounded border border-emerald-500/30" title="Verified at {{ $u->email_verified_at }}">✓ Verified</span>
                        @else
                          <span class="text-[10px] text-amber-400 font-sans font-bold bg-amber-500/10 px-1 rounded border border-amber-500/30" title="Unverified email address">⏳ Unverified</span>
                        @endif
                      </div>
                      <div class="text-[11px] mt-0.5 flex items-center gap-1.5">
                        <span class="font-mono text-cyan-400 font-semibold">{{ $lastLog ? $lastLog->ip_address : 'No IP logged' }}</span>
                        @if($lastLog)
                          <span class="text-slate-400">&bull; {{ \Carbon\Carbon::parse($lastLog->logged_at)->diffForHumans() }}</span>
                        @endif
                      </div>
                    </td>
                    <td class="p-3 whitespace-nowrap">
                      {!! badge($u->badge_vip ? 'vip' : '') !!}
                      {!! badge($u->badge_id ? 'id' : '') !!}
                      {!! badge($u->badge_pro ? 'pro' : '') !!}
                      @if(!$u->badge_vip && !$u->badge_id && !$u->badge_pro)
                        <span class="text-xs text-slate-400">None</span>
                      @endif
                    </td>
                    <td class="p-3">
                      <span class="px-2 py-0.5 rounded text-xs font-bold {{ $u->status === 'active' ? 'bg-emerald-500/20 text-emerald-300' : ($u->status === 'suspended' ? 'bg-amber-500/20 text-amber-300' : 'bg-rose-500/20 text-rose-300') }}">
                        {{ $u->status }}
                      </span>
                    </td>
                    <td class="p-3">
                      <div class="font-bold text-white text-xs mb-1">{{ number_format($u->points ?? 0) }} pts</div>
                      <form method="POST" action="{{ route('admin.users.points', $u->id) }}" class="flex items-center gap-1">
                        @csrf
                        <input type="number" name="points" value="100" class="w-14 px-1.5 py-0.5 text-xs bg-[#07030d] border border-white/10 rounded text-slate-200">
                        <button type="submit" name="action" value="add" class="px-2 py-0.5 text-[11px] font-bold bg-emerald-600 hover:bg-emerald-500 text-white rounded transition">+Add</button>
                      </form>
                    </td>
                    <td class="p-3 text-xs">
                      <div class="font-bold text-white flex items-center gap-1">
                        <span>{{ $u->referrals_count }} invited</span>
                        @if($hasReferrals)
                          <span class="px-1.5 py-0.2 rounded bg-pink-500/20 text-pink-300 text-[10px] font-black">+{{ number_format($u->referral_earnings) }} pts</span>
                        @endif
                      </div>
                      <div class="text-[10px] text-slate-400 font-mono">Code: {{ $u->referral_code ?? 'None' }}</div>
                    </td>
                    <td class="p-3">
                      <div class="flex items-center gap-1.5 flex-wrap">
                        <!-- Password Edit -->
                        <button type="button" onclick="openAdminBladePasswordModal({{ $u->id }}, '{{ $u->username }}')" class="px-2 py-1 text-xs rounded font-bold transition bg-amber-500/20 text-amber-300 border border-amber-500/40 hover:bg-amber-500/30 flex items-center gap-1" title="Edit password for @{{ $u->username }}">
                          🔑 Password
                        </button>
                        <!-- Quick Plan Upgrades -->
                        @if($u->plan !== 'premium_plus')
                          <form method="POST" action="{{ route('admin.users.plan', $u->id) }}">
                            @csrf
                            <input type="hidden" name="plan" value="premium_plus">
                            <button type="submit" class="px-2 py-1 text-xs rounded font-bold transition bg-fuchsia-500/20 text-fuchsia-300 border border-fuchsia-500/40 hover:bg-fuchsia-500/30" title="Upgrade directly to Premium+ ($30/VIP)">
                              ⚡ +Prem+ ($30)
                            </button>
                          </form>
                        @endif
                        @if($u->plan !== 'premium')
                          <form method="POST" action="{{ route('admin.users.plan', $u->id) }}">
                            @csrf
                            <input type="hidden" name="plan" value="premium">
                            <button type="submit" class="px-2 py-1 text-xs rounded font-bold transition bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 hover:bg-cyan-500/30" title="Upgrade directly to Premium ($15/PRO)">
                              💎 +Prem ($15)
                            </button>
                          </form>
                        @endif

                        <!-- Badges toggles -->
                        <form method="POST" action="{{ route('admin.users.badge', $u->id) }}">
                          @csrf
                          <input type="hidden" name="badge" value="vip">
                          <button type="submit" class="px-2 py-1 text-xs rounded font-semibold transition {{ $u->badge_vip ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'bg-white/5 text-slate-400 hover:text-white' }}">
                            {{ $u->badge_vip ? 'VIP ✓' : '+VIP' }}
                          </button>
                        </form>

                        <form method="POST" action="{{ route('admin.users.badge', $u->id) }}">
                          @csrf
                          <input type="hidden" name="badge" value="id">
                          <button type="submit" class="px-2 py-1 text-xs rounded font-semibold transition {{ $u->badge_id ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-white/5 text-slate-400 hover:text-white' }}">
                            {{ $u->badge_id ? 'ID ✓' : '+ID' }}
                          </button>
                        </form>

                        <form method="POST" action="{{ route('admin.users.badge', $u->id) }}">
                          @csrf
                          <input type="hidden" name="badge" value="pro">
                          <button type="submit" class="px-2 py-1 text-xs rounded font-semibold transition {{ $u->badge_pro ? 'bg-blue-500/20 text-blue-300 border border-blue-500/40' : 'bg-white/5 text-slate-400 hover:text-white' }}">
                            {{ $u->badge_pro ? 'PRO ✓' : '+Pro' }}
                          </button>
                        </form>

                        <!-- Email Verification Toggle -->
                        <form method="POST" action="{{ route('admin.users.verify_email', $u->id) }}">
                          @csrf
                          <button type="submit" class="px-2 py-1 text-xs rounded font-semibold transition {{ $u->email_verified_at ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 hover:bg-cyan-500/30' }}" title="{{ $u->email_verified_at ? 'Click to mark email as unverified' : 'Click to manually mark email as verified' }}">
                            {{ $u->email_verified_at ? '✉️ Verified' : '✉️ Verify' }}
                          </button>
                        </form>

                        @if($u->id !== auth()->id())
                          <!-- Suspend -->
                          <form method="POST" action="{{ route('admin.users.toggle', $u->id) }}">
                            @csrf
                            <input type="hidden" name="status" value="{{ $u->status === 'suspended' ? 'active' : 'suspended' }}">
                            <button type="submit" class="px-2 py-1 text-xs rounded font-semibold bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 transition">
                              {{ $u->status === 'suspended' ? 'Unsuspend' : 'Suspend' }}
                            </button>
                          </form>

                          <!-- Ban -->
                          <form method="POST" action="{{ route('admin.users.toggle', $u->id) }}">
                            @csrf
                            <input type="hidden" name="status" value="{{ $u->status === 'banned' ? 'active' : 'banned' }}">
                            <button type="submit" class="px-2 py-1 text-xs rounded font-semibold bg-rose-500/20 text-rose-300 hover:bg-rose-500/30 transition">
                              {{ $u->status === 'banned' ? 'Unban' : 'Ban' }}
                            </button>
                          </form>

                          <!-- Delete -->
                          <form method="POST" action="{{ route('admin.users.delete', $u->id) }}" onsubmit="return confirm('Permanently delete this user and all associated blogs/posts?');">
                            @csrf
                            <button type="submit" class="px-2 py-1 text-xs rounded font-semibold bg-red-950 text-red-300 hover:bg-red-900 transition">
                              Delete
                            </button>
                          </form>
                        @endif
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

        <!-- Admin Edit User Password Modal -->
        <div id="adminBladePasswordModal" style="display:none;" class="fixed inset-0 bg-black/80 backdrop-blur-md z-50 items-center justify-center p-4">
          <div class="bg-[#0f0716] border border-white/10 rounded-2xl max-w-md w-full p-6 relative shadow-2xl">
            <button type="button" onclick="closeAdminBladePasswordModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white text-xl font-bold">&times;</button>
            
            <div class="flex items-center gap-3 mb-4">
              <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-300 border border-amber-500/30 flex items-center justify-center font-bold text-lg flex-shrink-0">
                🔑
              </div>
              <div>
                <h3 class="font-display font-bold text-lg text-white">Edit User Password</h3>
                <p class="text-xs text-slate-400" id="adminBladePwdModalUserLabel">Update password for account</p>
              </div>
            </div>

            <form method="POST" id="adminBladePasswordForm" action="/admin/users/0/password">
              @csrf
              <input type="hidden" name="user_id" id="adminBladePwdModalUserId" value="">

              <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">New Password</label>
                <div class="relative">
                  <input type="text" id="adminBladeNewPasswordInput" name="password" required minlength="4" placeholder="Enter new password..." class="w-full bg-[#07030d] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white pr-24 focus:outline-none focus:border-amber-400 font-mono">
                  <button type="button" onclick="generateBladeRandomPassword()" class="absolute right-2 top-1.5 px-2.5 py-1 text-[11px] font-bold bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 rounded-lg border border-amber-500/30 transition">
                    🎲 Auto
                  </button>
                </div>
                <p class="text-[11px] text-slate-400 mt-1.5">Minimum 4 characters. Click 'Auto' to generate a secure random password.</p>
              </div>

              <div class="flex items-center justify-end gap-2 mt-6">
                <button type="button" onclick="closeAdminBladePasswordModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-300 bg-white/5 hover:bg-white/10 transition">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-extrabold text-black bg-amber-400 hover:bg-amber-300 transition shadow-lg shadow-amber-500/20 flex items-center gap-1.5">
                  <span>Save New Password</span>
                </button>
              </div>
            </form>
          </div>
        </div>

        <script>
          function openAdminBladePasswordModal(userId, username) {
            const modal = document.getElementById('adminBladePasswordModal');
            const form = document.getElementById('adminBladePasswordForm');
            const userIdInput = document.getElementById('adminBladePwdModalUserId');
            const userLabel = document.getElementById('adminBladePwdModalUserLabel');
            const pwdInput = document.getElementById('adminBladeNewPasswordInput');

            if (form) form.action = '/admin/users/' + userId + '/password';
            if (userIdInput) userIdInput.value = userId;
            if (userLabel) userLabel.textContent = 'Updating password for @' + username;
            if (pwdInput) { pwdInput.value = ''; pwdInput.focus(); }
            if (modal) { modal.style.display = 'flex'; }
          }

          function closeAdminBladePasswordModal() {
            const modal = document.getElementById('adminBladePasswordModal');
            if (modal) modal.style.display = 'none';
          }

          function generateBladeRandomPassword() {
            const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$';
            let pwd = '';
            for (let i = 0; i < 10; i++) {
              pwd += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            const pwdInput = document.getElementById('adminBladeNewPasswordInput');
            if (pwdInput) pwdInput.value = pwd;
          }
        </script>

        <script>
          function filterUsers(filter) {
            const tabs = document.querySelectorAll('.user-tab-btn');
            tabs.forEach(t => {
              if (t.dataset.filter === filter) {
                t.classList.remove('bg-white/5', 'text-slate-300');
                t.classList.add('bg-pink-600', 'text-white', 'shadow-md', 'shadow-pink-600/30');
              } else {
                t.classList.remove('bg-pink-600', 'text-white', 'shadow-md', 'shadow-pink-600/30');
                t.classList.add('bg-white/5', 'text-slate-300');
              }
            });

            const rows = document.querySelectorAll('.user-row');
            rows.forEach(r => {
              if (filter === 'all') {
                r.style.display = '';
              } else if (filter === 'premium') {
                r.style.display = (r.dataset.plan === 'premium') ? '' : 'none';
              } else if (filter === 'premium_plus') {
                r.style.display = (r.dataset.plan === 'premium_plus') ? '' : 'none';
              } else if (filter === 'badge_pro') {
                r.style.display = (r.dataset.badgePro === '1') ? '' : 'none';
              } else if (filter === 'badge_id') {
                r.style.display = (r.dataset.badgeId === '1') ? '' : 'none';
              } else if (filter === 'badge_vip') {
                r.style.display = (r.dataset.badgeVip === '1') ? '' : 'none';
              } else if (filter === 'referrers') {
                r.style.display = (r.dataset.hasReferrals === '1') ? '' : 'none';
              }
            });
          }
        </script>

      <!-- ================= SECTION: REFERRAL NETWORK ================= -->
      @elseif(($section ?? '') === 'referrals')
        <div class="space-y-6">
          @php
            $referrers = $allUsers->where('referrals_count', '>', 0)->sortByDesc('referrals_count');
            $totalReferrals = $allUsers->sum('referrals_count');
            $bountyPerRef = (int)($settings['referral_reward_points'] ?? 100);
            $totalPointsDistributed = $totalReferrals * $bountyPerRef;
          @endphp

          <!-- Summary Metric Cards -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-5 rounded-2xl bg-[#0f0716] border border-white/10 shadow-xl">
              <div class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1 flex items-center justify-between">
                <span>Total Invited Members</span>
                <span class="text-pink-400">👥</span>
              </div>
              <div class="text-2xl font-display font-black text-white">{{ number_format($totalReferrals) }}</div>
              <div class="text-[11px] text-pink-400 mt-1 font-semibold">Tracked referral signups</div>
            </div>

            <div class="p-5 rounded-2xl bg-[#0f0716] border border-white/10 shadow-xl">
              <div class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1 flex items-center justify-between">
                <span>Total Points Awarded</span>
                <span class="text-emerald-400">💎</span>
              </div>
              <div class="text-2xl font-display font-black text-emerald-400">{{ number_format($totalPointsDistributed) }} pts</div>
              <div class="text-[11px] text-slate-400 mt-1">Paid to active referrers</div>
            </div>

            <div class="p-5 rounded-2xl bg-[#0f0716] border border-white/10 shadow-xl">
              <div class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1 flex items-center justify-between">
                <span>Active Referrers</span>
                <span class="text-cyan-400">⚡</span>
              </div>
              <div class="text-2xl font-display font-black text-cyan-400">{{ $referrers->count() }}</div>
              <div class="text-[11px] text-slate-400 mt-1">Users who brought new members</div>
            </div>

            <div class="p-5 rounded-2xl bg-[#0f0716] border border-white/10 shadow-xl">
              <div class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1 flex items-center justify-between">
                <span>Bounty Per Referral</span>
                <span class="text-amber-400">🎁</span>
              </div>
              <div class="text-2xl font-display font-black text-amber-400">{{ $bountyPerRef }} pts</div>
              <div class="text-[11px] text-slate-400 mt-1">Configurable in Site Settings</div>
            </div>
          </div>

          <!-- Referrals Leaderboard & Detail Table -->
          <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-6 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
              <div>
                <h2 class="font-display font-bold text-xl text-white">Referral Leaderboard &amp; Affiliate Earnings</h2>
                <p class="text-xs text-slate-400 mt-1">Audit who has referred users to the platform and exactly how many reward points they earned.</p>
              </div>
              <span class="text-xs font-bold text-slate-400 bg-white/5 px-3 py-1.5 rounded-lg border border-white/5">
                {{ $referrers->count() }} Active Affiliates
              </span>
            </div>

            <div class="overflow-x-auto">
              <table class="w-full text-left text-sm text-slate-300 border-collapse">
                <thead class="text-xs uppercase bg-white/5 text-slate-400 border-b border-white/10">
                  <tr>
                    <th class="p-3">Rank</th>
                    <th class="p-3">Referrer User</th>
                    <th class="p-3">Referral Code</th>
                    <th class="p-3">Users Referred</th>
                    <th class="p-3">Total Earnings</th>
                    <th class="p-3">Referred Accounts List</th>
                    <th class="p-3">Quick Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                  @forelse($referrers as $idx => $refUser)
                    @php
                      $earnedPts = $refUser->referrals_count * $bountyPerRef;
                      $recruits = $refUser->referrals()->take(8)->get();
                    @endphp
                    <tr class="hover:bg-white/5 transition">
                      <td class="p-3 font-mono text-xs font-bold text-pink-400">#{{ $loop->iteration }}</td>
                      <td class="p-3">
                        <div class="flex items-center gap-2">
                          <span class="w-7 h-7 rounded-full bg-gradient-to-tr from-pink-500/20 to-purple-500/20 border border-pink-500/30 text-pink-300 flex items-center justify-center font-bold text-xs flex-shrink-0">
                            {{ initial($refUser->username) }}
                          </span>
                          <div>
                            <div class="font-bold text-white flex items-center gap-1.5">
                              <span>@ {{ $refUser->username }}</span>
                              {!! badge($refUser->badge_vip ? 'vip' : ($refUser->badge_id ? 'id' : ($refUser->badge_pro ? 'pro' : ''))) !!}
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $refUser->email }}</span>
                          </div>
                        </div>
                      </td>
                      <td class="p-3 font-mono text-xs text-cyan-400 font-bold">{{ $refUser->referral_code ?? 'None' }}</td>
                      <td class="p-3">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-pink-500/20 text-pink-300 border border-pink-500/30">
                          {{ $refUser->referrals_count }} members
                        </span>
                      </td>
                      <td class="p-3">
                        <div class="font-bold text-emerald-400 text-sm">{{ number_format($earnedPts) }} pts</div>
                        <div class="text-[10px] text-slate-400 font-mono">Current bal: {{ number_format($refUser->points ?? 0) }} pts</div>
                      </td>
                      <td class="p-3 text-xs max-w-sm">
                        <div class="flex items-center gap-1 flex-wrap">
                          @foreach($recruits as $rec)
                            <span class="px-1.5 py-0.5 rounded bg-white/5 text-slate-300 font-mono text-[11px]">
                              @ {{ $rec->username }}
                            </span>
                          @endforeach
                          @if($refUser->referrals_count > 8)
                            <span class="text-[11px] text-slate-500">+{{ $refUser->referrals_count - 8 }} more</span>
                          @endif
                        </div>
                      </td>
                      <td class="p-3">
                        <form method="POST" action="{{ route('admin.users.points', $refUser->id) }}" class="flex items-center gap-1">
                          @csrf
                          <input type="number" name="points" value="500" class="w-16 px-1.5 py-0.5 text-xs bg-[#07030d] border border-white/10 rounded text-slate-200">
                          <button type="submit" name="action" value="add" class="px-2 py-0.5 text-[11px] font-bold bg-pink-600 hover:bg-pink-500 text-white rounded transition">+Bonus</button>
                        </form>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="7" class="p-8 text-center text-slate-400">
                        No referral activity recorded yet. Members can find their referral link on their user dashboard.
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

      <!-- ================= SECTION: BLOGS ================= -->
      @elseif(($section ?? '') === 'blogs')
        <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-6 shadow-xl">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="font-display font-bold text-xl text-white">Unlimited Blogs Directory & Showcase</h2>
              <p class="text-xs text-slate-400 mt-1">Feature prominent creator blogs on the homepage or moderate policy breaches.</p>
            </div>
            <span class="text-xs font-bold text-slate-400 bg-white/5 px-3 py-1.5 rounded-lg border border-white/5">
              Total Blogs: <span class="text-purple-400">{{ $allBlogs->count() }}</span>
            </span>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300 border-collapse">
              <thead class="text-xs uppercase bg-white/5 text-slate-400 border-b border-white/10">
                <tr>
                  <th class="p-3">Slug & Link</th>
                  <th class="p-3">Creator</th>
                  <th class="p-3">Title</th>
                  <th class="p-3">Status</th>
                  <th class="p-3">Featured</th>
                  <th class="p-3">Contact & Links</th>
                  <th class="p-3">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/5">
                @foreach($allBlogs as $b)
                  <tr class="hover:bg-white/5 transition">
                    <td class="p-3">
                      <a href="{{ route('blog.show', $b->slug) }}" target="_blank" class="font-bold text-pink-400 hover:text-pink-300 flex items-center gap-1.5">
                        <span>@ {{ $b->slug }}</span>
                        {!! icon('link') !!}
                      </a>
                      @if($b->is_main)
                        <span class="text-[10px] bg-white/10 text-slate-300 px-1.5 py-0.5 rounded font-bold uppercase">Primary Blog</span>
                      @endif
                    </td>
                    <td class="p-3 font-semibold text-slate-200">@ {{ $b->user?->username ?? 'N/A' }}</td>
                    <td class="p-3 text-white font-medium">{{ $b->title }}</td>
                    <td class="p-3">
                      <span class="px-2 py-0.5 rounded text-xs font-bold {{ $b->status === 'active' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300' }}">
                        {{ $b->status }}
                      </span>
                    </td>
                    <td class="p-3">
                      @if($b->is_featured)
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">⭐ Featured</span>
                      @else
                        <span class="text-xs text-slate-400">Standard</span>
                      @endif
                    </td>
                    <td class="p-3 text-xs text-slate-400">
                      <div>{{ $b->contact_email ?? 'No email' }}</div>
                      @if($b->website1)
                        <a href="{{ $b->website1 }}" target="_blank" class="text-cyan-400 hover:underline truncate max-w-[140px] block">{{ $b->website1 }}</a>
                      @endif
                    </td>
                    <td class="p-3">
                      <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('admin.blogs.feature', $b->id) }}">
                          @csrf
                          <button type="submit" class="px-2.5 py-1 text-xs rounded font-semibold transition {{ $b->is_featured ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-white/5 text-slate-300 hover:text-white' }}">
                            {{ $b->is_featured ? 'Unfeature' : '⭐ Feature' }}
                          </button>
                        </form>

                        <form method="POST" action="{{ route('admin.blogs.toggle', $b->id) }}">
                          @csrf
                          <button type="submit" class="px-2.5 py-1 text-xs rounded font-semibold transition {{ $b->status === 'active' ? 'bg-amber-500/20 text-amber-300 hover:bg-amber-500/30' : 'bg-emerald-500/20 text-emerald-300 hover:bg-emerald-500/30' }}">
                            {{ $b->status === 'active' ? 'Suspend' : 'Activate' }}
                          </button>
                        </form>

                        <form method="POST" action="{{ route('admin.blogs.delete', $b->id) }}" onsubmit="return confirm('Permanently delete blog @{{ $b->slug }} and all of its posts? This action is irreversible.');">
                          @csrf
                          <button type="submit" class="px-2.5 py-1 text-xs rounded font-semibold transition bg-rose-500/20 text-rose-300 hover:bg-rose-500 hover:text-white border border-rose-500/30">
                            🗑️ Delete
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

      <!-- ================= SECTION: VERIFICATIONS ================= -->
      @elseif(($section ?? '') === 'verifications')
        <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-6 shadow-xl">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="font-display font-bold text-xl text-white">Creator Identity Verification Requests</h2>
              <p class="text-xs text-slate-400 mt-1">Review government or photo ID documents to issue official Identity badges.</p>
            </div>
            <span class="text-xs font-bold text-amber-300 bg-amber-500/20 px-3 py-1.5 rounded-lg border border-amber-500/30">
              Pending: {{ $verifications->where('status','pending')->count() }}
            </span>
          </div>

          @if($verifications->isEmpty())
            <div class="p-12 text-center text-slate-400">
              <div class="w-12 h-12 rounded-full bg-white/5 mx-auto mb-3 flex items-center justify-center text-slate-400">{!! icon('verified') !!}</div>
              <p class="font-medium">No creator verification documents submitted yet.</p>
            </div>
          @else
            <div class="overflow-x-auto">
              <table class="w-full text-left text-sm text-slate-300 border-collapse">
                <thead class="text-xs uppercase bg-white/5 text-slate-400 border-b border-white/10">
                  <tr>
                    <th class="p-3">ID</th>
                    <th class="p-3">Creator</th>
                    <th class="p-3">Full Legal Name</th>
                    <th class="p-3">ID Document</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Date</th>
                    <th class="p-3">Review Action</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                  @foreach($verifications as $v)
                    <tr class="hover:bg-white/5 transition">
                      <td class="p-3 font-mono text-xs text-slate-400">#{{ $v->id }}</td>
                      <td class="p-3 font-bold text-white">@ {{ $v->user?->username ?? 'Unknown' }}</td>
                      <td class="p-3 text-slate-200">{{ $v->full_name }}</td>
                      <td class="p-3">
                        <a href="{{ media_url($v->document_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-pink-500/20 border border-pink-500/30 text-pink-300 font-bold text-xs hover:bg-pink-500/30 transition">
                          {!! icon('photo') !!}
                          <span>Inspect Document</span>
                        </a>
                      </td>
                      <td class="p-3">
                        <span class="px-2 py-0.5 rounded text-xs font-bold {{ $v->status === 'approved' ? 'bg-emerald-500/20 text-emerald-300' : ($v->status === 'pending' ? 'bg-amber-500/20 text-amber-300 animate-pulse' : 'bg-rose-500/20 text-rose-300') }}">
                          {{ $v->status }}
                        </span>
                      </td>
                      <td class="p-3 text-xs text-slate-400">{{ $v->created_at->format('M d, Y') }}</td>
                      <td class="p-3">
                        @if($v->status === 'pending')
                          <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('admin.verifications.review', $v->id) }}">
                              @csrf
                              <input type="hidden" name="action" value="approve">
                              <button type="submit" class="px-3 py-1 rounded font-bold text-xs bg-emerald-600 hover:bg-emerald-500 text-white transition shadow-sm">
                                Approve & Award Badge
                              </button>
                            </form>

                            <form method="POST" action="{{ route('admin.verifications.review', $v->id) }}">
                              @csrf
                              <input type="hidden" name="action" value="reject">
                              <button type="submit" class="px-3 py-1 rounded font-bold text-xs bg-rose-600 hover:bg-rose-500 text-white transition shadow-sm">
                                Reject
                              </button>
                            </form>
                          </div>
                        @else
                          <span class="text-xs text-slate-400 font-semibold">Resolved</span>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>

      <!-- ================= SECTION: DONATIONS ================= -->
      @elseif(($section ?? '') === 'donations')
        <div class="space-y-6">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-5 rounded-2xl bg-[#0f0716] border border-white/10 shadow-xl">
              <div class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1 flex items-center justify-between">
                <span>Total Crypto Raised</span>
                <span class="text-emerald-400">💎</span>
              </div>
              <div class="text-2xl font-display font-black text-white">
                ${{ number_format($donations->whereIn('status', ['confirmed', 'finished'])->sum('amount_usd'), 2) }}
              </div>
              <div class="text-[11px] text-emerald-400 mt-1 font-semibold">Settled USD Equivalent</div>
            </div>

            <div class="p-5 rounded-2xl bg-[#0f0716] border border-white/10 shadow-xl">
              <div class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1 flex items-center justify-between">
                <span>Confirmed Orders</span>
                <span class="text-cyan-400">⚡</span>
              </div>
              <div class="text-2xl font-display font-black text-cyan-400">
                {{ $donations->whereIn('status', ['confirmed', 'finished'])->count() }}
              </div>
              <div class="text-[11px] text-slate-400 mt-1">Paid &amp; verified on blockchain</div>
            </div>

            <div class="p-5 rounded-2xl bg-[#0f0716] border border-white/10 shadow-xl">
              <div class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1 flex items-center justify-between">
                <span>Pending / Waiting</span>
                <span class="text-amber-400">⏳</span>
              </div>
              <div class="text-2xl font-display font-black text-amber-400">
                {{ $donations->whereIn('status', ['pending', 'waiting', 'confirming'])->count() }}
              </div>
              <div class="text-[11px] text-slate-400 mt-1">Awaiting confirmations</div>
            </div>

            <div class="p-5 rounded-2xl bg-[#0f0716] border border-white/10 shadow-xl">
              <div class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1 flex items-center justify-between">
                <span>Active Gateway</span>
                <span class="text-pink-400">🛡️</span>
              </div>
              <div class="text-xl font-display font-black text-white">NOWPayments</div>
              <div class="text-[11px] text-emerald-400 mt-1 font-semibold flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400 inline-block animate-pulse"></span>
                <span>API Live &bull; IPN Webhook Ready</span>
              </div>
            </div>
          </div>

          <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h2 class="font-display font-bold text-xl text-white">NOWPayments Crypto Transactions Ledger</h2>
                <p class="text-xs text-slate-400 mt-1">Real-time ledger of invoices, on-chain deposits, and subscription upgrades.</p>
              </div>
              <div class="flex items-center gap-3">
                <a href="{{ route('admin.settings') }}" class="px-3 py-1.5 rounded-lg bg-pink-500/20 text-pink-300 text-xs font-bold border border-pink-500/30 hover:bg-pink-500/30 transition">
                  NOWPayments Config &rarr;
                </a>
              </div>
            </div>

            <div class="overflow-x-auto">
              <table class="w-full text-left text-sm text-slate-300 border-collapse">
                <thead class="text-xs uppercase bg-white/5 text-slate-400 border-b border-white/10">
                  <tr>
                    <th class="p-3">Order ID</th>
                    <th class="p-3">Member</th>
                    <th class="p-3">Gateway</th>
                    <th class="p-3">Plan</th>
                    <th class="p-3">USD Amount</th>
                    <th class="p-3">Crypto Currency</th>
                    <th class="p-3">Crypto Amount</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Date</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                  @forelse($donations as $d)
                    <tr class="hover:bg-white/5 transition">
                      <td class="p-3 font-mono text-xs text-pink-400 font-bold">{{ $d->order_id }}</td>
                      <td class="p-3 font-bold text-white">
                        {{ $d->user ? '@' . $d->user->username : ('User #' . $d->user_id) }}
                      </td>
                      <td class="p-3">
                        <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-white/10 text-cyan-300 uppercase">
                          {{ $d->gateway }}
                        </span>
                      </td>
                      <td class="p-3 font-semibold text-slate-200 capitalize">{{ str_replace('_', ' ', $d->plan) }}</td>
                      <td class="p-3 font-bold text-emerald-400">${{ number_format($d->amount_usd, 2) }}</td>
                      <td class="p-3 font-mono text-xs text-slate-300 font-bold">
                        {{ $d->crypto_currency ?: 'USDT' }}
                      </td>
                      <td class="p-3 font-mono text-xs text-slate-300">
                        {{ $d->crypto_amount ? $d->crypto_amount : '-' }}
                      </td>
                      <td class="p-3">
                        <span class="px-2 py-0.5 rounded text-xs font-bold uppercase {{ in_array($d->status, ['confirmed', 'finished']) ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : (in_array($d->status, ['pending', 'waiting', 'confirming']) ? 'bg-amber-500/20 text-amber-300 animate-pulse border border-amber-500/30' : 'bg-rose-500/20 text-rose-300') }}">
                          {{ $d->status }}
                        </span>
                      </td>
                      <td class="p-3 text-xs text-slate-400">{{ $d->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="9" class="p-8 text-center text-slate-400">No crypto transactions recorded yet.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

      <!-- ================= SECTION: REPORTS ================= -->
      @elseif(($section ?? '') === 'reports')
        <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-6 shadow-xl">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="font-display font-bold text-xl text-white">Content Moderation & Flagged Reports</h2>
              <p class="text-xs text-slate-400 mt-1">Review flagged adult content, copyright infringement, or harassment claims.</p>
            </div>
            <span class="text-xs font-bold text-rose-300 bg-rose-500/20 px-3 py-1.5 rounded-lg border border-rose-500/30">
              Open Reports: {{ $reports->where('status','open')->count() }}
            </span>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300 border-collapse">
              <thead class="text-xs uppercase bg-white/5 text-slate-400 border-b border-white/10">
                <tr>
                  <th class="p-3">ID</th>
                  <th class="p-3">Reporter</th>
                  <th class="p-3">Target Post / Blog</th>
                  <th class="p-3">Reason / Details</th>
                  <th class="p-3">Status</th>
                  <th class="p-3">Submitted</th>
                  <th class="p-3">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/5">
                @forelse($reports as $r)
                  <tr class="hover:bg-white/5 transition">
                    <td class="p-3 font-mono text-xs text-slate-400">#{{ $r->id }}</td>
                    <td class="p-3 font-bold text-white">@ {{ $r->reporter?->username ?? 'Anonymous' }}</td>
                    <td class="p-3">
                      @php
                        $reportedPost = $r->post ?: (($r->target_type === 'post' || is_numeric($r->target_id)) ? \App\Models\Post::with(['blog.user'])->find($r->target_id) : null);
                        $reportedBlog = $r->blog ?: ($r->target_type === 'blog' ? \App\Models\Blog::where('slug', $r->target_id)->orWhere('id', $r->target_id)->first() : null);
                      @endphp

                      @if($reportedPost)
                        <div class="space-y-1">
                          <a href="{{ route('post.show', $reportedPost->id) }}" target="_blank" class="text-pink-400 font-bold hover:underline flex items-center gap-1.5 text-xs">
                            {!! icon('eye') !!}
                            <span>Post #{{ $reportedPost->id }} ({{ strtoupper($reportedPost->type) }})</span>
                          </a>
                          <div class="text-[11px] text-slate-400">
                            Author: <span class="text-cyan-400 font-semibold">&#64;{{ $reportedPost->blog?->slug ?? $reportedPost->user?->username ?? 'creator' }}</span>
                          </div>
                          <div class="text-xs text-slate-300 max-w-sm line-clamp-2 bg-[#07030d] p-1.5 rounded border border-white/5">
                            {{ Str::limit($reportedPost->heading ?: ($reportedPost->caption ?: ($reportedPost->quote_text ?: ($reportedPost->body ?: 'Media/link post'))), 80) }}
                          </div>
                        </div>
                      @elseif($reportedBlog)
                        <div>
                          <a href="{{ route('blog.show', $reportedBlog->slug) }}" target="_blank" class="text-cyan-400 font-bold hover:underline text-xs flex items-center gap-1">
                            {!! icon('compass') !!}
                            <span>Blog: &#64;{{ $reportedBlog->slug }}</span>
                          </a>
                          <div class="text-xs text-slate-400">{{ $reportedBlog->title }}</div>
                        </div>
                      @else
                        <div class="space-y-1">
                          @if($r->target_type === 'blog')
                            <a href="{{ route('blog.show', $r->target_id) }}" target="_blank" class="text-cyan-400 font-bold hover:underline text-xs flex items-center gap-1">
                              {!! icon('compass') !!}
                              <span>View Blog &#64;{{ $r->target_id }}</span>
                            </a>
                          @else
                            <a href="{{ route('post.show', $r->target_id) }}" target="_blank" class="text-pink-400 font-bold hover:underline text-xs flex items-center gap-1">
                              {!! icon('eye') !!}
                              <span>Direct Link to Post #{{ $r->target_id }}</span>
                            </a>
                          @endif
                          <div class="text-[11px] text-slate-400 font-mono">Target: #{{ $r->target_id }} ({{ $r->target_type }})</div>
                        </div>
                      @endif
                    </td>
                    <td class="p-3 text-slate-200 max-w-xs break-words">
                      <div class="font-semibold text-rose-300 text-xs">{{ $r->reason }}</div>
                      @if($r->details)
                        <div class="text-[11px] text-slate-400 mt-0.5">{{ $r->details }}</div>
                      @endif
                    </td>
                    <td class="p-3">
                      <span class="px-2 py-0.5 rounded text-xs font-bold {{ $r->status === 'resolved' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                        {{ $r->status }}
                      </span>
                    </td>
                    <td class="p-3 text-xs text-slate-400">{{ $r->created_at->diffForHumans() }}</td>
                    <td class="p-3">
                      <div class="flex items-center gap-2 flex-wrap">
                        @if($reportedPost)
                          <form method="POST" action="{{ route('admin.posts.delete', $reportedPost->id) }}" onsubmit="return confirm('Permanently delete reported Post #{{ $reportedPost->id }}?');" style="display:inline;">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 text-xs font-bold bg-rose-600/30 text-rose-300 hover:bg-rose-600/50 border border-rose-500/40 rounded transition" title="Delete this reported post">
                              Delete Post
                            </button>
                          </form>
                        @endif

                        @if($r->status !== 'resolved')
                          <form method="POST" action="{{ route('admin.reports.resolve', $r->id) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white rounded transition shadow-sm">
                              Mark Resolved
                            </button>
                          </form>
                        @else
                          <span class="text-xs text-slate-400">Resolved</span>
                        @endif
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="p-8 text-center text-slate-400">No open moderation reports. All clear!</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

      <!-- ================= SECTION: SUPPORT TICKETS ================= -->
      @elseif(($section ?? '') === 'support')
        <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-6 shadow-xl">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="font-display font-bold text-xl text-white">Help Desk & Support Inquiries</h2>
              <p class="text-xs text-slate-400 mt-1">Communicate directly with registered creators and subscribers.</p>
            </div>
            <span class="text-xs font-bold text-cyan-300 bg-cyan-500/20 px-3 py-1.5 rounded-lg border border-cyan-500/30">
              Open Tickets: {{ $tickets->where('status','open')->count() }}
            </span>
          </div>

          <div class="space-y-4">
            @forelse($tickets as $t)
              <div class="p-5 rounded-xl bg-white/5 border border-white/10">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-white/10">
                  <div class="flex items-center gap-3">
                    <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-white/10 text-slate-300">#{{ $t->id }}</span>
                    <h3 class="font-bold text-white text-base">{{ $t->subject }}</h3>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-400">From <b>@ {{ $t->user?->username ?? 'Guest' }}</b></span>
                    <span class="px-2 py-0.5 rounded text-xs font-bold {{ $t->status === 'replied' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300' }}">
                      {{ $t->status }}
                    </span>
                  </div>
                </div>

                <p class="text-sm text-slate-300 mt-3 whitespace-pre-line">{{ $t->message }}</p>

                @if($t->admin_reply)
                  <div class="mt-4 p-3 rounded-lg bg-pink-500/10 border border-pink-500/20 text-sm">
                    <div class="text-xs font-bold text-pink-400 uppercase tracking-wider mb-1">Administrator Reply:</div>
                    <p class="text-slate-200">{{ $t->admin_reply }}</p>
                  </div>
                @endif

                <form method="POST" action="{{ route('admin.tickets.reply', $t->id) }}" class="mt-4 flex gap-2">
                  @csrf
                  <input type="text" name="reply" placeholder="Type response to creator..." required class="flex-1 px-3 py-2 text-sm bg-[#07030d] border border-white/10 rounded-lg text-white focus:outline-none focus:border-pink-500">
                  <button type="submit" class="px-4 py-2 text-xs font-bold bg-pink-600 hover:bg-pink-500 text-white rounded-lg transition shadow-sm">
                    Send Reply
                  </button>
                </form>
              </div>
            @empty
              <div class="p-8 text-center text-slate-400">No support tickets found.</div>
            @endforelse
          </div>
        </div>

      <!-- ================= SECTION: SECURITY & LOGINS ================= -->
      @elseif(($section ?? '') === 'logins')
        <div class="space-y-6">
          @php
            $uniqueIps = $logins->pluck('ip_address')->filter()->unique();
            $uniqueUsers = $logins->pluck('user_id')->filter()->unique();
          @endphp

          <!-- Login & Security Stats Cards -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-5 rounded-2xl bg-[#0f0716] border border-white/10 shadow-xl">
              <div class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1 flex items-center justify-between">
                <span>Total Recorded Logins</span>
                <span class="text-cyan-400">🛡️</span>
              </div>
              <div class="text-2xl font-display font-black text-white">{{ number_format($logins->count()) }}</div>
              <div class="text-[11px] text-cyan-400 mt-1 font-semibold">Active session logs stored</div>
            </div>

            <div class="p-5 rounded-2xl bg-[#0f0716] border border-white/10 shadow-xl">
              <div class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1 flex items-center justify-between">
                <span>Unique IP Addresses</span>
                <span class="text-pink-400">🌐</span>
              </div>
              <div class="text-2xl font-display font-black text-pink-400">{{ $uniqueIps->count() }}</div>
              <div class="text-[11px] text-slate-400 mt-1">Distinct IP locations audited</div>
            </div>

            <div class="p-5 rounded-2xl bg-[#0f0716] border border-white/10 shadow-xl">
              <div class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1 flex items-center justify-between">
                <span>Active Signed-In Members</span>
                <span class="text-emerald-400">👥</span>
              </div>
              <div class="text-2xl font-display font-black text-emerald-400">{{ $uniqueUsers->count() }}</div>
              <div class="text-[11px] text-slate-400 mt-1">Distinct member IDs logged</div>
            </div>
          </div>

          <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-6 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
              <div>
                <h2 class="font-display font-bold text-xl text-white">Member Login Security & IP Audits</h2>
                <p class="text-xs text-slate-400 mt-1">Audit recent user sign-ins, IP addresses, and user agents for fraud detection and security monitoring.</p>
              </div>
              
              <!-- Search IP or Username -->
              <div class="w-full sm:w-72">
                <input type="text" id="ipSearchInput" onkeyup="searchLogins()" placeholder="Search by IP or username..." class="w-full bg-[#170a22] border border-white/10 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 transition">
              </div>
            </div>

            <div class="overflow-x-auto">
              <table class="w-full text-left text-sm text-slate-300 border-collapse" id="loginsTable">
                <thead class="text-xs uppercase bg-white/5 text-slate-400 border-b border-white/10">
                  <tr>
                    <th class="p-3">Time</th>
                    <th class="p-3">User &amp; Plan</th>
                    <th class="p-3">IP Address</th>
                    <th class="p-3">Device &amp; User Agent</th>
                    <th class="p-3 text-right">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                  @forelse($logins as $l)
                    <tr class="login-row hover:bg-white/5 transition" data-user="{{ strtolower($l->user?->username ?? '') }}" data-ip="{{ $l->ip_address }}">
                      <td class="p-3 text-xs text-slate-400 whitespace-nowrap">
                        <div class="font-bold text-slate-200">
                          {{ $l->logged_at ? \Carbon\Carbon::parse($l->logged_at)->format('M d, Y H:i:s') : $l->created_at->format('M d, Y H:i:s') }}
                        </div>
                        <div class="text-[11px] text-slate-500">
                          {{ $l->logged_at ? \Carbon\Carbon::parse($l->logged_at)->diffForHumans() : $l->created_at->diffForHumans() }}
                        </div>
                      </td>
                      <td class="p-3">
                        <div class="flex items-center gap-2">
                          <span class="w-7 h-7 rounded-full bg-gradient-to-tr from-cyan-500/20 to-blue-500/20 border border-cyan-500/30 text-cyan-300 flex items-center justify-center font-bold text-xs flex-shrink-0">
                            {{ initial($l->user?->username ?? 'U') }}
                          </span>
                          <div>
                            <div class="font-bold text-white flex items-center gap-1.5">
                              <span>@ {{ $l->user?->username ?? 'Unknown' }}</span>
                              @if($l->user)
                                {!! badge($l->user->badge_vip ? 'vip' : ($l->user->badge_id ? 'id' : ($l->user->badge_pro ? 'pro' : ''))) !!}
                              @endif
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $l->user?->email ?? 'N/A' }}</span>
                          </div>
                        </div>
                      </td>
                      <td class="p-3">
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-cyan-500/10 border border-cyan-500/20">
                          <span class="font-mono text-xs text-cyan-300 font-bold">{{ $l->ip_address }}</span>
                        </div>
                      </td>
                      <td class="p-3 text-xs text-slate-400 max-w-sm">
                        <div class="truncate font-mono text-[11px]" title="{{ $l->user_agent }}">{{ $l->user_agent }}</div>
                      </td>
                      <td class="p-3 text-right">
                        @if($l->user && $l->user->id !== auth()->id())
                          <form method="POST" action="{{ route('admin.users.toggle', $l->user->id) }}" class="inline">
                            @csrf
                            <input type="hidden" name="status" value="{{ $l->user->status === 'banned' ? 'active' : 'banned' }}">
                            <button type="submit" class="px-2 py-1 text-xs rounded font-semibold transition {{ $l->user->status === 'banned' ? 'bg-rose-500 text-white font-bold' : 'bg-white/5 text-slate-400 hover:text-rose-300' }}">
                              {{ $l->user->status === 'banned' ? 'Banned' : 'Ban User' }}
                            </button>
                          </form>
                        @endif
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="p-8 text-center text-slate-400">No login security records captured yet.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <script>
          function searchLogins() {
            const query = (document.getElementById('ipSearchInput').value || '').toLowerCase().trim();
            const rows = document.querySelectorAll('.login-row');
            rows.forEach(r => {
              const user = r.dataset.user || '';
              const ip = r.dataset.ip || '';
              if (!query || user.includes(query) || ip.includes(query)) {
                r.style.display = '';
              } else {
                r.style.display = 'none';
              }
            });
          }
        </script>

      <!-- ================= SECTION: STATIC PAGES ================= -->
      @elseif(($section ?? '') === 'pages')
        @php $pageEditor = $staticPageEditor ?? null; @endphp
        <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-6 lg:p-8 shadow-xl">
          <div class="mb-6 pb-4 border-b border-white/10">
            <h2 class="font-display font-bold text-2xl text-white">Edit static pages</h2>
            <p class="text-sm text-slate-400 mt-1">Change the title, intro, and text on About, FAQ, Terms, Privacy, Rules, Compliance, Safety, DMCA, Support, and Contact. The contact form itself stays in place.</p>
          </div>

          @if($pageEditor)
            <div class="flex flex-wrap gap-2 mb-6">
              @foreach($pageEditor['pages'] as $pageKey => $pageMeta)
                <a href="{{ route('admin.pages', ['page' => $pageKey]) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $pageEditor['key'] === $pageKey ? 'bg-pink-600 text-white' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">{{ $pageMeta['label'] }}</a>
              @endforeach
            </div>

            <form method="POST" action="{{ route('admin.pages.save') }}" class="space-y-4">
              @csrf
              <input type="hidden" name="page_key" value="{{ $pageEditor['key'] }}">

              <div class="flex items-center justify-between gap-3">
                <h3 class="text-lg font-bold text-white">{{ $pageEditor['pages'][$pageEditor['key']]['label'] }}</h3>
                <a href="{{ url($pageEditor['pages'][$pageEditor['key']]['path']) }}" target="_blank" class="text-xs font-bold text-cyan-300 hover:underline">View page</a>
              </div>

              <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Page title</label>
                <input type="text" name="title" maxlength="180" value="{{ $pageEditor['current']['title'] }}" class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white focus:outline-none focus:border-pink-500">
              </div>

              <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Intro line</label>
                <textarea name="lead" rows="2" maxlength="500" class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white focus:outline-none focus:border-pink-500 text-sm leading-relaxed">{{ $pageEditor['current']['lead'] }}</textarea>
              </div>

              <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Page text</label>
                <textarea name="body" rows="18" class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white focus:outline-none focus:border-pink-500 text-xs leading-relaxed font-mono">{{ $pageEditor['current']['body'] }}</textarea>
                <span class="text-[10px] text-slate-400 mt-1 block">This is the page content. Basic HTML such as headings, paragraphs, and links is kept. Script tags are removed.</span>
              </div>

              <button type="submit" class="px-5 py-2.5 rounded-xl bg-pink-600 hover:bg-pink-500 text-white text-sm font-bold transition">Save page text</button>
            </form>

            @if(!empty($pageEditor['saved']))
              <form method="POST" action="{{ route('admin.pages.save') }}" class="mt-3">
                @csrf
                <input type="hidden" name="page_key" value="{{ $pageEditor['key'] }}">
                <input type="hidden" name="reset" value="1">
                <button type="submit" class="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-slate-300 text-xs font-bold transition">Restore original text</button>
              </form>
            @endif
          @endif
        </div>

      <!-- ================= SECTION: PLATFORM SETTINGS ================= -->
      @elseif(($section ?? '') === 'settings')
        <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-6 lg:p-8 shadow-xl">
          <div class="mb-6 pb-4 border-b border-white/10">
            <h2 class="font-display font-bold text-2xl text-white">Platform Branding, SEO & Integration Settings</h2>
            <p class="text-sm text-slate-400 mt-1">Configure global platform options, API keys, payment webhooks, and community bio.</p>
          </div>

          <form method="POST" action="{{ route('admin.settings.save') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
              <!-- Column 1: Branding & Media Options -->
              <div class="space-y-5">
                <div class="flex items-center gap-2 pb-2 border-b border-white/10 text-pink-400 font-bold text-base">
                  {!! icon('settings') !!}
                  <span>Branding & Media Options</span>
                </div>

                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Website Title</label>
                  <input type="text" name="site_title" value="{{ $settings['site_title'] ?? 'Nina.Sucks' }}" class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white focus:outline-none focus:border-pink-500">
                </div>

                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Meta Description (SEO)</label>
                  <textarea name="meta_description" rows="3" class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white focus:outline-none focus:border-pink-500 text-sm leading-relaxed">{{ $settings['meta_description'] ?? 'An adult microblogging community. Post photos, text, video, quotes and chats.' }}</textarea>
                </div>

                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Global Schema Markup</label>
                  <textarea name="custom_schema" rows="8" placeholder='{"@context":"https://schema.org","@type":"WebSite","name":"Nina.Sucks","url":"https://nina.sucks"}' class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white focus:outline-none focus:border-pink-500 text-xs leading-relaxed font-mono">{{ $settings['custom_schema'] ?? '' }}</textarea>
                  <span class="text-[10px] text-slate-400 mt-1 block">Paste Organization, WebSite, or any other JSON-LD. One or more schema blocks. Script tags are optional. This is added on every page.</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Logo Asset URL / Path</label>
                    <input type="text" name="site_logo" value="{{ $settings['site_logo'] ?? '' }}" placeholder="assets/logo.png" class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white focus:outline-none focus:border-pink-500">
                  </div>
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Favicon Asset URL / Path</label>
                    <input type="text" name="site_favicon" value="{{ $settings['site_favicon'] ?? '' }}" placeholder="assets/favicon-32.png" class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white focus:outline-none focus:border-pink-500">
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Primary Hex Color</label>
                    <div class="flex items-center gap-2">
                      <input type="color" value="{{ $settings['primary_color'] ?? '#ff2bd6' }}" onchange="this.nextElementSibling.value = this.value" class="w-10 h-10 rounded-lg cursor-pointer bg-transparent border-0 p-0">
                      <input type="text" name="primary_color" value="{{ $settings['primary_color'] ?? '#ff2bd6' }}" class="flex-1 px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white font-mono text-sm focus:outline-none focus:border-pink-500">
                    </div>
                  </div>

                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Secondary Hex Color</label>
                    <div class="flex items-center gap-2">
                      <input type="color" value="{{ $settings['secondary_color'] ?? '#19e3ff' }}" onchange="this.nextElementSibling.value = this.value" class="w-10 h-10 rounded-lg cursor-pointer bg-transparent border-0 p-0">
                      <input type="text" name="secondary_color" value="{{ $settings['secondary_color'] ?? '#19e3ff' }}" class="flex-1 px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white font-mono text-sm focus:outline-none focus:border-pink-500">
                    </div>
                  </div>
                </div>

                <!-- Media Upload Limits (Images & Videos) -->
                <div class="p-4 rounded-xl bg-[#07030d] border border-white/10 space-y-4">
                  <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-1.5">
                      <span class="text-pink-400">{!! icon('settings') !!}</span>
                      <span>Media Upload Limits</span>
                    </span>
                    <div class="flex items-center gap-1.5">
                      <button type="button" onclick="document.getElementById('laravelImgMb').value=2; document.getElementById('laravelVidMb').value=50;" class="px-2 py-0.5 text-[10px] font-bold rounded bg-pink-500/20 text-pink-300 hover:bg-pink-500/30">
                        Default (2MB / 50MB)
                      </button>
                      <button type="button" onclick="document.getElementById('laravelImgMb').value=10; document.getElementById('laravelVidMb').value=100;" class="px-2 py-0.5 text-[10px] font-bold rounded bg-cyan-500/20 text-cyan-300 hover:bg-cyan-500/30">
                        HD (10MB / 100MB)
                      </button>
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label class="block text-xs font-bold uppercase tracking-wider text-pink-400 mb-1.5">Max Image Size (MB)</label>
                      <div class="relative">
                        <input type="number" step="0.5" min="0.5" max="100" id="laravelImgMb" name="max_image_mb" value="{{ $settings['max_image_mb'] ?? 2 }}" class="w-full px-3.5 py-2.5 bg-[#170a22] border border-white/10 rounded-xl text-white font-mono focus:outline-none focus:border-pink-500">
                        <span class="absolute right-3.5 top-3 text-xs text-slate-400 font-bold">MB</span>
                      </div>
                      <span class="text-[10px] text-slate-400 mt-1 block">E.g. 2 MB for photo posts &amp; avatars</span>
                    </div>

                    <div>
                      <label class="block text-xs font-bold uppercase tracking-wider text-cyan-400 mb-1.5">Max Video Size (MB)</label>
                      <div class="relative">
                        <input type="number" step="1" min="1" max="1000" id="laravelVidMb" name="max_video_mb" value="{{ $settings['max_video_mb'] ?? 50 }}" class="w-full px-3.5 py-2.5 bg-[#170a22] border border-white/10 rounded-xl text-white font-mono focus:outline-none focus:border-cyan-400">
                        <span class="absolute right-3.5 top-3 text-xs text-slate-400 font-bold">MB</span>
                      </div>
                      <span class="text-[10px] text-slate-400 mt-1 block">E.g. 50 MB for MP4/WEBM video posts</span>
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-4 pt-1">
                    <div>
                      <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-300 mb-1">Allowed Image Formats</label>
                      <input type="text" name="allowed_image_ext" value="{{ $settings['allowed_image_ext'] ?? 'jpg, jpeg, png, gif, webp' }}" class="w-full px-3 py-2 bg-[#170a22] border border-white/10 rounded-xl text-white font-mono text-xs focus:outline-none focus:border-pink-500">
                    </div>
                    <div>
                      <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-300 mb-1">Allowed Video Formats</label>
                      <input type="text" name="allowed_video_ext" value="{{ $settings['allowed_video_ext'] ?? 'mp4, webm, mov' }}" class="w-full px-3 py-2 bg-[#170a22] border border-white/10 rounded-xl text-white font-mono text-xs focus:outline-none focus:border-cyan-400">
                    </div>
                  </div>

                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-fuchsia-300 mb-1.5">Max Private Message File (MB)</label>
                    <div class="relative">
                      <input type="number" step="1" min="0" max="500" name="max_message_file_mb" value="{{ $settings['max_message_file_mb'] ?? 10 }}" class="w-full px-3.5 py-2.5 bg-[#170a22] border border-white/10 rounded-xl text-white font-mono focus:outline-none focus:border-fuchsia-400">
                      <span class="absolute right-3.5 top-3 text-xs text-slate-400 font-bold">MB</span>
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 block">Largest file a member can attach to a private message. Set 0 to turn attachments off. Images, video, audio, PDF, TXT, and ZIP are allowed.</span>
                  </div>

                  <input type="hidden" name="max_media_mb" value="{{ $settings['max_video_mb'] ?? 50 }}">
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Referral Reward Points</label>
                    <input type="number" name="referral_reward_points" value="{{ $settings['referral_reward_points'] ?? 100 }}" class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white focus:outline-none focus:border-pink-500">
                  </div>

                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Max Audio Size (MB)</label>
                    <input type="number" name="max_audio_mb" value="{{ $settings['max_audio_mb'] ?? 20 }}" class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white focus:outline-none focus:border-pink-500">
                  </div>
                </div>

                <!-- Featured Blogs Display Limit on Index & Trending -->
                <div class="p-4 rounded-xl bg-[#07030d] border border-white/10 space-y-2">
                  <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-pink-400 flex items-center gap-1.5">
                      ⭐ Featured Blogs Display Limit
                    </span>
                    <span class="text-[11px] text-slate-400">Homepage &amp; Trending</span>
                  </div>
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Max Featured Blogs to Display</label>
                    <div class="relative">
                      <input type="number" min="1" max="100" name="featured_blogs_max_count" value="{{ $settings['featured_blogs_max_count'] ?? 6 }}" class="w-full px-3.5 py-2.5 bg-[#170a22] border border-white/10 rounded-xl text-white font-mono focus:outline-none focus:border-pink-500">
                      <span class="absolute right-3.5 top-3 text-xs text-slate-400 font-bold">Blogs</span>
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 block">
                      Controls maximum featured/promoted blogs shown on Homepage and Trending pages (max 6 per row, balanced rows without empty spaces). If more paid blogs than this limit, they rotate evenly (admin blog remains pinned).
                    </span>
                  </div>
                </div>

                <!-- 18+ Adult Content Gate Toggle -->
                <div class="p-4 rounded-xl bg-[#07030d] border border-white/10 space-y-2">
                  <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-pink-400 flex items-center gap-1.5">
                      🔞 Adult Content Warning (18+ Gate)
                    </span>
                    <span class="text-[11px] text-slate-400">First-Time Visitors</span>
                  </div>
                  <div>
                    <select name="enable_adult_warning" class="w-full px-3.5 py-2.5 bg-[#170a22] border border-white/10 rounded-xl text-white font-semibold focus:outline-none focus:border-pink-500">
                      <option value="0" {{ ($settings['enable_adult_warning'] ?? '0') === '0' ? 'selected' : '' }}>Disabled (Direct Access - Off by Default)</option>
                      <option value="1" {{ ($settings['enable_adult_warning'] ?? '0') === '1' ? 'selected' : '' }}>Enabled (Show 18+ Age Verification Modal on first visit)</option>
                    </select>
                    <span class="text-[10px] text-slate-400 mt-1 block">
                      Controls whether new visitors must confirm they are 18+ before browsing. Disabled by default. Note: Cookie consent banner is always active.
                    </span>
                  </div>
                </div>

                <!-- Feed Excerpts & Anti-Duplicate Post Limits -->
                <div class="p-4 rounded-xl bg-[#07030d] border border-white/10 space-y-3">
                  <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-cyan-400 flex items-center gap-1.5">
                      📝 Feed Excerpts &amp; Post Limits (Anti-Duplicate)
                    </span>
                    <span class="text-[11px] text-slate-400">Tumblr-Style Clickable Feeds</span>
                  </div>
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-300 mb-1">Feed Text Post Excerpt</label>
                      <div class="relative">
                        <input type="number" min="50" max="2000" name="feed_text_excerpt_limit" value="{{ $settings['feed_text_excerpt_limit'] ?? 280 }}" class="w-full px-3 py-2 bg-[#170a22] border border-white/10 rounded-xl text-white font-mono text-xs focus:outline-none focus:border-cyan-400">
                        <span class="absolute right-3 top-2 text-[10px] text-slate-400 font-bold">chars</span>
                      </div>
                      <span class="text-[9px] text-slate-400 mt-0.5 block">Truncates long text in feeds with "Read full story &rarr;"</span>
                    </div>
                    <div>
                      <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-300 mb-1">Feed Caption Excerpt</label>
                      <div class="relative">
                        <input type="number" min="30" max="1000" name="feed_caption_excerpt_limit" value="{{ $settings['feed_caption_excerpt_limit'] ?? 140 }}" class="w-full px-3 py-2 bg-[#170a22] border border-white/10 rounded-xl text-white font-mono text-xs focus:outline-none focus:border-cyan-400">
                        <span class="absolute right-3 top-2 text-[10px] text-slate-400 font-bold">chars</span>
                      </div>
                      <span class="text-[9px] text-slate-400 mt-0.5 block">Feed shows about one sentence. The full caption appears when the post is opened.</span>
                    </div>
                  </div>
                  <div class="grid grid-cols-2 gap-4 pt-1">
                    <div>
                      <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-300 mb-1">Max Text Post Length</label>
                      <div class="relative">
                        <input type="number" min="500" max="50000" name="post_max_text_length" value="{{ $settings['post_max_text_length'] ?? 10000 }}" class="w-full px-3 py-2 bg-[#170a22] border border-white/10 rounded-xl text-white font-mono text-xs focus:outline-none focus:border-pink-500">
                        <span class="absolute right-3 top-2 text-[10px] text-slate-400 font-bold">chars</span>
                      </div>
                      <span class="text-[9px] text-slate-400 mt-0.5 block">Max characters allowed per text post body</span>
                    </div>
                    <div>
                      <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-300 mb-1">Max Caption Length</label>
                      <div class="relative">
                        <input type="number" min="80" max="2000" name="post_max_caption_length" value="{{ $settings['post_max_caption_length'] ?? 450 }}" class="w-full px-3 py-2 bg-[#170a22] border border-white/10 rounded-xl text-white font-mono text-xs focus:outline-none focus:border-cyan-400">
                        <span class="absolute right-3 top-2 text-[10px] text-slate-400 font-bold">chars</span>
                      </div>
                      <span class="text-[9px] text-slate-400 mt-0.5 block">Max characters for photo/video text, about 2–3 sentences. Longer text cannot be published.</span>
                    </div>
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Enable Crypto Donations & Subscriptions</label>
                  <select name="donations_enabled" class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white focus:outline-none focus:border-pink-500">
                    <option value="1" {{ ($settings['donations_enabled'] ?? '1') === '1' ? 'selected' : '' }}>Enabled (Show donation & premium tiers sitewide)</option>
                    <option value="0" {{ ($settings['donations_enabled'] ?? '1') === '0' ? 'selected' : '' }}>Disabled (Hide donation & premium checkout)</option>
                  </select>
                </div>
              </div>

              <!-- Column 2: Integrations & API Keys -->
              <div class="space-y-5">
                <div class="flex items-center gap-2 pb-2 border-b border-white/10 text-cyan-400 font-bold text-base">
                  {!! icon('shield') !!}
                  <span>API Keys & External Gateways</span>
                </div>

                <div class="p-4 rounded-xl bg-[#170a22] border border-white/10 space-y-4">
                  <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-200">NOWPayments Crypto Gateway</span>
                    <button type="button" onclick="testNowPaymentsConnection()" id="btnTestApi" class="px-2.5 py-1 rounded bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition">
                      Test Connection
                    </button>
                  </div>
                  <div id="nowpaymentsStatusIndicator" class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 inline-block animate-pulse"></span>
                    <span id="nowpaymentsStatusText">API Status: Ready</span>
                  </div>

                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">NOWPayments API Key</label>
                    <input type="text" name="nowpayments_api_key" value="{{ $settings['nowpayments_api_key'] ?? '4DMYGP5-7V04ZZJ-G3JTHJH-XGNZHD5' }}" class="w-full px-3.5 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white font-mono text-sm focus:outline-none focus:border-pink-500">
                  </div>

                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">NOWPayments IPN Secret Key (HMAC-SHA512)</label>
                    <input type="text" name="nowpayments_ipn_secret" value="{{ $settings['nowpayments_ipn_secret'] ?? 'mIV4c5kQrKwYN4PEGRV4L5MM8UQUhAEz' }}" class="w-full px-3.5 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white font-mono text-sm focus:outline-none focus:border-cyan-400">
                  </div>

                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Gateway Environment</label>
                    <select name="nowpayments_sandbox" class="w-full px-3.5 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white text-sm focus:outline-none focus:border-pink-500">
                      <option value="0" {{ ($settings['nowpayments_sandbox'] ?? '0') === '0' ? 'selected' : '' }}>Production (https://api.nowpayments.io)</option>
                      <option value="1" {{ ($settings['nowpayments_sandbox'] ?? '0') === '1' ? 'selected' : '' }}>Sandbox Testnet (https://api-sandbox.nowpayments.io)</option>
                    </select>
                  </div>

                  <div>
                    <div class="flex items-center justify-between mb-1.5">
                      <label class="block text-xs font-bold uppercase tracking-wider text-slate-300">IPN Webhook URL</label>
                      <button type="button" onclick="copyIpnWebhookUrl()" class="text-xs font-bold text-cyan-400 hover:text-cyan-300">📋 Copy URL</button>
                    </div>
                    <input type="text" id="ipnWebhookUrlInput" readonly value="{{ url('/api/donations/nowpayments/ipn') }}" class="w-full px-3.5 py-2 bg-[#07030d] border border-white/10 rounded-xl text-cyan-300 font-mono text-xs focus:outline-none">
                    <p class="text-[11px] text-slate-400 mt-1">Set this IPN callback URL in your NOWPayments Store Settings.</p>
                  </div>
                </div>

                <!-- BitCart Self-Hosted Crypto Gateway -->
                <div class="p-4 rounded-xl bg-[#170a22] border border-white/10 space-y-4">
                  <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-200 flex items-center gap-1.5">
                      <span>⚡</span>
                      <span>BitCart Gateway Settings &amp; Payment Toggles</span>
                    </span>
                    <a href="https://bitcart.server5.click/admin" target="_blank" rel="noopener" class="text-xs font-bold text-cyan-400 hover:text-cyan-300">
                      Open BitCart Admin &rarr;
                    </a>
                  </div>

                  <!-- Toggles for BitCart and Direct QR Code -->
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-[#07030d] border border-white/10">
                      <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-white block">Enable BitCart Gateway</span>
                        <p class="text-[10px] text-slate-400">Show BitCart option on checkout</p>
                      </div>
                      <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="bitcart_enabled" value="0">
                        <input type="checkbox" name="bitcart_enabled" value="1" {{ (isset($settings['bitcart_enabled']) && $settings['bitcart_enabled'] == '1') ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-9 h-5 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-pink-600"></div>
                      </label>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-[#07030d] border border-white/10">
                      <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-white block">Enable Direct QR Code</span>
                        <p class="text-[10px] text-slate-400">Show 'Or pay with QR Code' link</p>
                      </div>
                      <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="qr_code_enabled" value="0">
                        <input type="checkbox" name="qr_code_enabled" value="1" {{ (isset($settings['qr_code_enabled']) && $settings['qr_code_enabled'] == '1') ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-9 h-5 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-pink-600"></div>
                      </label>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">BitCart Store URL</label>
                      <input type="text" name="bitcart_url" value="{{ $settings['bitcart_url'] ?? 'https://bitcart.server5.click/store/01M2V1SFTBTD7TCN1HKKNKS2WK' }}" placeholder="https://bitcart.server5.click/store/YOUR_STORE_ID" class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white font-mono text-sm focus:outline-none focus:border-pink-500">
                    </div>
                    <div>
                      <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">BitCart Product ID (Optional)</label>
                      <input type="text" name="bitcart_product_id" value="{{ $settings['bitcart_product_id'] ?? '01M0H6Q5G6GAQV5T0TJX27R8XD' }}" placeholder="01M0H6Q5G6GAQV5T0TJX27R8XD" class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white font-mono text-sm focus:outline-none focus:border-pink-500">
                    </div>
                  </div>

                  <div>
                    <div class="flex items-center justify-between mb-1.5">
                      <label class="block text-xs font-bold uppercase tracking-wider text-slate-300">BitCart IPN / Webhook Notification URL</label>
                      <div class="flex items-center gap-3">
                        <button type="button" onclick="testBitcartSimulation()" class="text-xs font-bold text-pink-400 hover:text-pink-300">⚡ Test Webhook</button>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ url('/api/donations/bitcart/ipn') }}'); alert('BitCart Webhook URL copied!');" class="text-xs font-bold text-cyan-400 hover:text-cyan-300">📋 Copy URL</button>
                      </div>
                    </div>
                    <input type="text" readonly value="{{ url('/api/donations/bitcart/ipn') }}" class="w-full px-3.5 py-2 bg-[#07030d] border border-white/10 rounded-xl text-cyan-300 font-mono text-xs focus:outline-none">
                    <p class="text-[11px] text-slate-400 mt-1">Set this notification URL in your BitCart store settings (<a href="https://bitcart.server5.click/admin/stores" target="_blank" class="text-cyan-400 underline">/admin/stores</a>) to instantly activate memberships and award verified PRO badges.</p>
                  </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Google OAuth Client ID</label>
                    <input type="text" name="google_client_id" value="{{ $settings['google_client_id'] ?? '421189417454-gbbjstrtcarq898bkjdemholu8te82op.apps.googleusercontent.com' }}" class="w-full px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white font-mono text-xs focus:outline-none focus:border-pink-500">
                  </div>
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Google Client Secret</label>
                    <input type="password" name="google_client_secret" value="{{ $settings['google_client_secret'] ?? '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' }}" class="w-full px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white font-mono text-xs focus:outline-none focus:border-pink-500">
                  </div>
                  <div class="sm:col-span-2">
                    <p class="text-[11px] text-slate-400">
                      Authorized redirect URI to put in Google Cloud Console: <code class="text-cyan-300 font-mono select-all bg-white/5 px-2 py-0.5 rounded">{{ url('/auth/google/callback') }}</code>
                    </p>
                  </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Cloudflare Turnstile Key</label>
                    <input type="text" name="turnstile_site_key" value="{{ $settings['turnstile_site_key'] ?? '0x4AAAAAAE8QOvWsSh5TL-MQ' }}" class="w-full px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white font-mono text-xs focus:outline-none focus:border-pink-500">
                  </div>
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Turnstile Secret Key</label>
                    <input type="password" name="turnstile_secret_key" value="{{ $settings['turnstile_secret_key'] ?? '0x4AAAAAAE8QOkJScve1toziPVGAdBajjJI' }}" class="w-full px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white font-mono text-xs focus:outline-none focus:border-pink-500">
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Sitewide Head Scripts (Google Analytics, GTM, Custom JS)</label>
                  <textarea name="head_scripts" rows="3" placeholder="<script async src='https://www.googletagmanager.com/gtag/js?id=...'></script>" class="w-full px-3.5 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white font-mono text-xs focus:outline-none focus:border-pink-500">{{ $settings['head_scripts'] ?? '' }}</textarea>
                </div>

                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Meta Verification Codes (Google Search Console, Bing, Pinterest)</label>
                  <textarea name="meta_verification" rows="2" placeholder='<meta name="google-site-verification" content="..." />' class="w-full px-3.5 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white font-mono text-xs focus:outline-none focus:border-cyan-400">{{ $settings['meta_verification'] ?? '' }}</textarea>
                </div>
              </div>
            </div>

            <!-- Community Hub Profile Configuration -->
            <div class="pt-6 border-t border-white/10">
              <div class="flex items-center gap-2 pb-4 text-purple-400 font-bold text-base">
                {!! icon('users') !!}
                <span>Community Profile Setup (Avatar, Cover, Bio, 3 URLs & Contact Email)</span>
              </div>

              <!-- Community Avatar & Cover Upload -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4 p-4 rounded-xl bg-white/5 border border-white/5">
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5 flex items-center justify-between">
                    <span>Community Avatar Image</span>
                    <span class="text-[10px] text-pink-400">Square 1:1 Recommended</span>
                  </label>
                  <div class="flex items-center gap-3">
                    @if(!empty($settings['community_avatar']))
                      <img src="{{ media_url($settings['community_avatar']) }}" alt="Avatar" class="w-12 h-12 rounded-full object-cover border border-pink-500/50">
                    @else
                      <div class="w-12 h-12 rounded-full bg-pink-500/20 text-pink-400 flex items-center justify-center font-bold text-lg border border-pink-500/30">N</div>
                    @endif
                    <div class="flex-1">
                      <input type="file" name="community_avatar_file" accept="image/*" class="w-full text-xs text-slate-300 file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-pink-500/20 file:text-pink-300 hover:file:bg-pink-500/30">
                      <input type="hidden" name="community_avatar" value="{{ $settings['community_avatar'] ?? '' }}">
                    </div>
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5 flex items-center justify-between">
                    <span>Community Cover Banner</span>
                    <span class="text-[10px] text-cyan-400">Wide Header Banner</span>
                  </label>
                  <div class="flex items-center gap-3">
                    @if(!empty($settings['community_cover']))
                      <img src="{{ media_url($settings['community_cover']) }}" alt="Cover" class="w-16 h-12 rounded object-cover border border-cyan-500/50">
                    @else
                      <div class="w-16 h-12 rounded bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-xs font-bold border border-cyan-500/30">Banner</div>
                    @endif
                    <div class="flex-1">
                      <input type="file" name="community_cover_file" accept="image/*" class="w-full text-xs text-slate-300 file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-cyan-500/20 file:text-cyan-300 hover:file:bg-cyan-500/30">
                      <input type="hidden" name="community_cover" value="{{ $settings['community_cover'] ?? '' }}">
                    </div>
                  </div>
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Community Title</label>
                  <input type="text" name="community_title" value="{{ $settings['community_title'] ?? 'Nina.Sucks Community' }}" class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white focus:outline-none focus:border-pink-500">
                </div>
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Community Contact Email</label>
                  <input type="email" name="community_email" value="{{ $settings['community_email'] ?? 'community@nina.sucks' }}" class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white focus:outline-none focus:border-pink-500">
                </div>
              </div>

              <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Community Bio Text</label>
                <textarea name="community_bio" rows="3" class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white focus:outline-none focus:border-pink-500 text-sm leading-relaxed">{{ $settings['community_bio'] ?? 'The uncensored hub for creator discovery & expression.' }}</textarea>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Website URL 1</label>
                  <input type="text" name="community_url1" value="{{ $settings['community_url1'] ?? 'https://nina.sucks' }}" class="w-full px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-pink-500">
                </div>
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Website URL 2 (Telegram)</label>
                  <input type="text" name="community_url2" value="{{ $settings['community_url2'] ?? 'https://t.me/ninasucks' }}" class="w-full px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-pink-500">
                </div>
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Website URL 3 (Twitter)</label>
                  <input type="text" name="community_url3" value="{{ $settings['community_url3'] ?? 'https://twitter.com/ninasucks' }}" class="w-full px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-pink-500">
                </div>
              </div>
            </div>

            <!-- Advertising Marketplace Packages & Subscription Rewards -->
            <div class="pt-6 border-t border-white/10">
              <div class="flex items-center gap-2 pb-4 text-amber-400 font-bold text-base">
                {!! icon('flame') !!}
                <span>Advertising Packages Pricing & Subscription Rewards (Point Settings)</span>
              </div>
              <p class="text-xs text-slate-400 mb-4">Set custom point costs that creators spend on impression packages and flat-rate spotlight placements, plus monthly subscription rewards.</p>

              <!-- CPM View Packages -->
              <div class="mb-5">
                <span class="block text-xs font-bold uppercase tracking-wider text-pink-400 mb-2">CPM Impression Packages (Views &rarr; Points Cost)</span>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                  <div>
                    <label class="block text-[11px] text-slate-300 mb-1">1,000 Views</label>
                    <div class="relative">
                      <input type="number" min="1" name="pkg_cpm_1k" value="{{ $settings['pkg_cpm_1k'] ?? 500 }}" class="w-full px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white text-xs font-mono focus:outline-none focus:border-pink-500">
                      <span class="absolute right-3 top-2 text-[10px] text-slate-500">pts</span>
                    </div>
                  </div>
                  <div>
                    <label class="block text-[11px] text-slate-300 mb-1">5,000 Views</label>
                    <div class="relative">
                      <input type="number" min="1" name="pkg_cpm_5k" value="{{ $settings['pkg_cpm_5k'] ?? 2000 }}" class="w-full px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white text-xs font-mono focus:outline-none focus:border-pink-500">
                      <span class="absolute right-3 top-2 text-[10px] text-slate-500">pts</span>
                    </div>
                  </div>
                  <div>
                    <label class="block text-[11px] text-slate-300 mb-1">25,000 Views</label>
                    <div class="relative">
                      <input type="number" min="1" name="pkg_cpm_25k" value="{{ $settings['pkg_cpm_25k'] ?? 8000 }}" class="w-full px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white text-xs font-mono focus:outline-none focus:border-pink-500">
                      <span class="absolute right-3 top-2 text-[10px] text-slate-500">pts</span>
                    </div>
                  </div>
                  <div>
                    <label class="block text-[11px] text-slate-300 mb-1">100,000 Views</label>
                    <div class="relative">
                      <input type="number" min="1" name="pkg_cpm_100k" value="{{ $settings['pkg_cpm_100k'] ?? 25000 }}" class="w-full px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white text-xs font-mono focus:outline-none focus:border-pink-500">
                      <span class="absolute right-3 top-2 text-[10px] text-slate-500">pts</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Flat-Rate Time Placements -->
              <div class="mb-5">
                <span class="block text-xs font-bold uppercase tracking-wider text-cyan-400 mb-2">Flat-Rate Placements (Duration &rarr; Points Cost)</span>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                  <div>
                    <label class="block text-[11px] text-slate-300 mb-1">24 Hours Spotlight</label>
                    <div class="relative">
                      <input type="number" min="1" name="pkg_flat_1d" value="{{ $settings['pkg_flat_1d'] ?? 350 }}" class="w-full px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white text-xs font-mono focus:outline-none focus:border-cyan-500">
                      <span class="absolute right-3 top-2 text-[10px] text-slate-500">pts</span>
                    </div>
                  </div>
                  <div>
                    <label class="block text-[11px] text-slate-300 mb-1">7 Days Spotlight</label>
                    <div class="relative">
                      <input type="number" min="1" name="pkg_flat_7d" value="{{ $settings['pkg_flat_7d'] ?? 2000 }}" class="w-full px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white text-xs font-mono focus:outline-none focus:border-cyan-500">
                      <span class="absolute right-3 top-2 text-[10px] text-slate-500">pts</span>
                    </div>
                  </div>
                  <div>
                    <label class="block text-[11px] text-slate-300 mb-1">30 Days Spotlight</label>
                    <div class="relative">
                      <input type="number" min="1" name="pkg_flat_30d" value="{{ $settings['pkg_flat_30d'] ?? 7000 }}" class="w-full px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white text-xs font-mono focus:outline-none focus:border-cyan-500">
                      <span class="absolute right-3 top-2 text-[10px] text-slate-500">pts</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Subscription Pricing & Monthly Points Rewards -->
              <div>
                <span class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-2">Subscription USD Prices &amp; Monthly Points Rewards</span>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                  <div>
                    <label class="block text-[11px] text-slate-300 mb-1">Premium Price ($ USD)</label>
                    <div class="relative">
                      <input type="number" min="1" step="1" name="premium_price_usd" value="{{ $settings['premium_price_usd'] ?? 15 }}" class="w-full px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white text-xs font-mono focus:outline-none focus:border-amber-400">
                      <span class="absolute right-3 top-2 text-[10px] text-slate-500">$</span>
                    </div>
                  </div>
                  <div>
                    <label class="block text-[11px] text-slate-300 mb-1">Premium+ Price ($ USD)</label>
                    <div class="relative">
                      <input type="number" min="1" step="1" name="premium_plus_price_usd" value="{{ $settings['premium_plus_price_usd'] ?? 30 }}" class="w-full px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white text-xs font-mono focus:outline-none focus:border-amber-400">
                      <span class="absolute right-3 top-2 text-[10px] text-slate-500">$</span>
                    </div>
                  </div>
                  <div>
                    <label class="block text-[11px] text-slate-300 mb-1">Premium Bonus Points</label>
                    <div class="relative">
                      <input type="number" min="100" name="premium_points_monthly" value="{{ $settings['premium_points_monthly'] ?? 5000 }}" class="w-full px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white text-xs font-mono focus:outline-none focus:border-amber-400">
                      <span class="absolute right-3 top-2 text-[10px] text-slate-500">pts/mo</span>
                    </div>
                  </div>
                  <div>
                    <label class="block text-[11px] text-slate-300 mb-1">Premium+ Bonus (Monthly)</label>
                    <div class="relative">
                      <input type="number" min="100" name="premium_plus_points_monthly" value="{{ $settings['premium_plus_points_monthly'] ?? 11000 }}" class="w-full px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white text-xs font-mono focus:outline-none focus:border-amber-400">
                      <span class="absolute right-3 top-2 text-[10px] text-slate-500">pts/mo</span>
                    </div>
                  </div>
                  <div>
                    <label class="block text-[11px] text-slate-300 mb-1">Referral Reward (Per Sign-up)</label>
                    <div class="relative">
                      <input type="number" min="1" name="referral_reward_points" value="{{ $settings['referral_reward_points'] ?? 100 }}" class="w-full px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white text-xs font-mono focus:outline-none focus:border-amber-400">
                      <span class="absolute right-3 top-2 text-[10px] text-slate-500">pts</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- CARD: SMTP MAIL ACCOUNT & WELCOME EMAIL CONFIGURATION -->
            <div class="p-6 rounded-2xl bg-[#0f0716] border border-white/10 shadow-xl space-y-6">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-white/10">
                <div>
                  <h2 class="text-xl font-display font-bold text-white flex items-center gap-2">
                    <span class="text-cyan-400">{!! icon('mail') !!}</span>
                    <span>SMTP Mail Server &amp; Welcome Email Setup</span>
                  </h2>
                  <p class="text-xs text-slate-400 mt-1">Configure your PHP/Node SMTP account credentials to send automated welcome emails and platform notifications.</p>
                </div>
                <div class="flex items-center gap-3">
                  <label class="inline-flex items-center cursor-pointer gap-2 bg-[#170a22] px-3.5 py-1.5 rounded-xl border border-white/10">
                    <input type="checkbox" name="smtp_enabled" value="1" {{ ($settings['smtp_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-9 h-5 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-cyan-500 relative"></div>
                    <span class="text-xs font-bold text-slate-200">Enable SMTP Mail</span>
                  </label>
                </div>
              </div>

              <!-- SMTP Account Server Settings Grid -->
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="p-3.5 rounded-xl bg-[#170a22] border border-white/10 col-span-full">
                  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                      <label class="text-xs font-bold text-slate-300 block mb-0.5">Email Delivery System</label>
                      <p class="text-[11px] text-slate-400">Choose whether to send outgoing mail via configured SMTP credentials or standard PHP mail().</p>
                    </div>
                    <select name="mail_method" class="bg-[#07030d] border border-white/10 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-medium">
                      <option value="smtp" {{ ($settings['mail_method'] ?? 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP Server Account (Custom Mail Server / Mailtrap / Gmail)</option>
                      <option value="php_mail" {{ ($settings['mail_method'] ?? 'smtp') === 'php_mail' ? 'selected' : '' }}>PHP mail() Function (Direct Server PHP Mailer)</option>
                      <option value="auto" {{ ($settings['mail_method'] ?? 'smtp') === 'auto' ? 'selected' : '' }}>Automatic (Try SMTP first, fall back to PHP mail())</option>
                    </select>
                  </div>
                </div>

                <div class="p-3.5 rounded-xl bg-[#170a22] border border-white/10">
                  <label class="text-xs font-bold text-slate-300 block mb-1">SMTP Host</label>
                  <input type="text" name="smtp_host" value="{{ $settings['smtp_host'] ?? 'smtp.mailtrap.io' }}" placeholder="smtp.gmail.com or smtp.mailtrap.io" class="w-full bg-[#07030d] border border-white/10 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>

                <div class="p-3.5 rounded-xl bg-[#170a22] border border-white/10">
                  <label class="text-xs font-bold text-slate-300 block mb-1">SMTP Port</label>
                  <input type="number" name="smtp_port" value="{{ $settings['smtp_port'] ?? 587 }}" placeholder="587, 465, 25" class="w-full bg-[#07030d] border border-white/10 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>

                <div class="p-3.5 rounded-xl bg-[#170a22] border border-white/10">
                  <label class="text-xs font-bold text-slate-300 block mb-1">Encryption Protocol</label>
                  <select name="smtp_encryption" class="w-full bg-[#07030d] border border-white/10 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                    <option value="tls" {{ ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS (Port 587 / 25)</option>
                    <option value="ssl" {{ ($settings['smtp_encryption'] ?? 'tls') === 'ssl' ? 'selected' : '' }}>SSL (Port 465)</option>
                    <option value="none" {{ ($settings['smtp_encryption'] ?? 'tls') === 'none' ? 'selected' : '' }}>None (Plain text / Local)</option>
                  </select>
                </div>

                <div class="p-3.5 rounded-xl bg-[#170a22] border border-white/10">
                  <label class="text-xs font-bold text-slate-300 block mb-1">SMTP Username / Account Email</label>
                  <input type="text" name="smtp_username" value="{{ $settings['smtp_username'] ?? '' }}" placeholder="user@example.com" class="w-full bg-[#07030d] border border-white/10 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>

                <div class="p-3.5 rounded-xl bg-[#170a22] border border-white/10">
                  <label class="text-xs font-bold text-slate-300 block mb-1">SMTP Password</label>
                  <input type="password" name="smtp_password" value="{{ $settings['smtp_password'] ?? '' }}" placeholder="••••••••••••" class="w-full bg-[#07030d] border border-white/10 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                </div>

                <div class="p-3.5 rounded-xl bg-[#170a22] border border-white/10">
                  <label class="text-xs font-bold text-slate-300 block mb-1">Sender Email Address ("From")</label>
                  <input type="email" name="smtp_from_email" value="{{ $settings['smtp_from_email'] ?? 'welcome@nina.sucks' }}" placeholder="noreply@nina.sucks" class="w-full bg-[#07030d] border border-white/10 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-3.5 rounded-xl bg-[#170a22] border border-white/10">
                  <label class="text-xs font-bold text-slate-300 block mb-1">Sender Name ("From Name")</label>
                  <input type="text" name="smtp_from_name" value="{{ $settings['smtp_from_name'] ?? 'Nina Microblogging' }}" placeholder="Nina Community" class="w-full bg-[#07030d] border border-white/10 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                </div>
              </div>

              <!-- Welcome Email Customization -->
              <div class="p-4 rounded-xl bg-[#170a22] border border-white/10 space-y-4">
                <div class="flex items-center justify-between border-b border-white/10 pb-3">
                  <div class="flex items-center gap-2">
                    <span class="text-pink-400">{!! icon('mail') !!}</span>
                    <span class="text-sm font-bold text-white">Welcome Email Customization</span>
                  </div>
                  <label class="inline-flex items-center cursor-pointer gap-2">
                    <input type="checkbox" name="welcome_email_enabled" value="1" {{ ($settings['welcome_email_enabled'] ?? '1') === '1' ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-8 h-4 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-pink-500 relative"></div>
                    <span class="text-xs font-bold text-slate-300">Send Welcome Email on Sign Up</span>
                  </label>
                </div>

                <div>
                  <label class="text-xs font-bold text-slate-300 block mb-1">Welcome Email Subject Line</label>
                  <input type="text" name="welcome_email_subject" value="{{ $settings['welcome_email_subject'] ?? 'Welcome to {site_title}, @{username}!' }}" class="w-full bg-[#07030d] border border-white/10 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-pink-500">
                  <p class="text-[11px] text-slate-400 mt-1">Placeholders allowed: <code class="text-pink-300 font-mono">{username}</code>, <code class="text-pink-300 font-mono">{site_title}</code></p>
                </div>

                <div>
                  <label class="text-xs font-bold text-slate-300 block mb-1">Welcome Email HTML Body Template</label>
                  <textarea name="welcome_email_body" rows="6" class="w-full bg-[#07030d] border border-white/10 rounded-lg p-3 text-xs text-slate-200 font-mono leading-relaxed focus:outline-none focus:border-pink-500">{{ $settings['welcome_email_body'] ?? '' }}</textarea>
                  <p class="text-[11px] text-slate-400 mt-1">Placeholders available: <code class="text-pink-300 font-mono">{username}</code>, <code class="text-pink-300 font-mono">{site_title}</code>, <code class="text-pink-300 font-mono">{blog_url}</code>, <code class="text-pink-300 font-mono">{login_url}</code>, <code class="text-pink-300 font-mono">{support_email}</code></p>
                </div>
              </div>

              <!-- Email Verification on Signup Customization -->
              <div class="p-4 rounded-xl bg-[#170a22] border border-white/10 space-y-4">
                <div class="flex items-center justify-between border-b border-white/10 pb-3">
                  <div class="flex items-center gap-2">
                    <span class="text-cyan-400">{!! icon('check') !!}</span>
                    <span class="text-sm font-bold text-white">Email Verification on User Signup</span>
                  </div>
                  <label class="inline-flex items-center cursor-pointer gap-2">
                    <input type="checkbox" name="email_verification_enabled" value="1" {{ ($settings['email_verification_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-8 h-4 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-cyan-500 relative"></div>
                    <span class="text-xs font-bold text-slate-300">Require Email Verification on Signup</span>
                  </label>
                </div>

                <p class="text-xs text-slate-400">
                  When enabled, new users must click a verification link sent to their registered email before accessing their creator dashboard. If disabled, new users are automatically verified and signed in immediately.
                </p>

                <div>
                  <label class="text-xs font-bold text-slate-300 block mb-1">Email Verification Subject Line</label>
                  <input type="text" name="email_verification_subject" value="{{ $settings['email_verification_subject'] ?? 'Verify your email address - {site_title}' }}" class="w-full bg-[#07030d] border border-white/10 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                  <p class="text-[11px] text-slate-400 mt-1">Placeholders allowed: <code class="text-cyan-300 font-mono">{username}</code>, <code class="text-cyan-300 font-mono">{site_title}</code></p>
                </div>
              </div>

              <!-- Admin Alert on New User Signup -->
              <div class="p-4 rounded-xl bg-[#170a22] border border-white/10 space-y-4">
                <div class="flex items-center justify-between border-b border-white/10 pb-3">
                  <div class="flex items-center gap-2">
                    <span class="text-pink-400">{!! icon('shield') !!}</span>
                    <span class="text-sm font-bold text-white">Admin Alert on User Signup</span>
                  </div>
                  <label class="inline-flex items-center cursor-pointer gap-2">
                    <input type="checkbox" name="admin_notify_signup" value="1" {{ ($settings['admin_notify_signup'] ?? '1') === '1' ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-8 h-4 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-pink-500 relative"></div>
                    <span class="text-xs font-bold text-slate-300">Email Admin on New Signups</span>
                  </label>
                </div>

                <p class="text-xs text-slate-400">
                  Sends an automated notification email directly to the administrator whenever a new member registers an account.
                </p>

                <div>
                  <label class="text-xs font-bold text-slate-300 block mb-1">Admin Notification Recipient Email</label>
                  <input type="email" name="admin_notification_email" value="{{ $settings['admin_notification_email'] ?? 'probloggerrohit@gmail.com' }}" placeholder="admin@example.com" class="w-full bg-[#07030d] border border-white/10 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-pink-400 font-mono">
                  <p class="text-[11px] text-slate-400 mt-1">Leave blank to use the primary admin account or community email.</p>
                </div>
              </div>

              <!-- Send Test Email Action Box -->
              <div class="pt-4 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-400">
                  <p class="font-semibold text-slate-300">Test SMTP Configuration</p>
                  <p>Send a test welcome message to verify your SMTP mail credentials.</p>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                  <input type="email" id="testEmailRecipient" placeholder="your-email@example.com" class="px-3 py-2 bg-[#07030d] border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-cyan-400 w-full sm:w-64">
                  <button type="button" onclick="sendTestSmtpEmail()" class="px-4 py-2 bg-cyan-600/30 hover:bg-cyan-600/50 text-cyan-300 border border-cyan-500/40 rounded-xl text-xs font-bold transition whitespace-nowrap flex items-center gap-1.5">
                    {!! icon('mail') !!}
                    <span>Send Test Email</span>
                  </button>
                </div>
              </div>
              <div id="testMailResult" class="hidden p-3 rounded-xl text-xs font-bold"></div>
            </div>

            <script>
              function sendTestSmtpEmail() {
                const recipient = document.getElementById('testEmailRecipient')?.value || '';
                const resultBox = document.getElementById('testMailResult');
                if (!recipient) {
                  alert('Please enter a recipient email address to send the test email.');
                  return;
                }
                if (resultBox) {
                  resultBox.className = 'p-3 rounded-xl text-xs font-bold bg-slate-800 text-slate-300';
                  resultBox.innerHTML = '⌛ Sending test email via SMTP... Please wait.';
                  resultBox.classList.remove('hidden');
                }

                const csrfMeta = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const csrfInput = document.querySelector('input[name="_token"]')?.value;
                const token = csrfMeta || csrfInput || '{{ csrf_token() }}';

                fetch('{{ route("admin.smtp.test") }}', {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                  },
                  body: JSON.stringify({
                    _token: token,
                    test_email: recipient
                  })
                })
                .then(res => res.json())
                .then(data => {
                  if (resultBox) {
                    if (data.ok || data.success) {
                      resultBox.className = 'p-3 rounded-xl text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40';
                      resultBox.innerHTML = `✅ <b>Success:</b> ${data.message || 'Test welcome email dispatched successfully!'}`;
                    } else {
                      resultBox.className = 'p-3 rounded-xl text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40';
                      resultBox.innerHTML = `❌ <b>SMTP Failure:</b> ${data.reason || data.error || 'Failed to send email. Verify host, port, and password.'}`;
                    }
                  }
                })
                .catch(err => {
                  if (resultBox) {
                    resultBox.className = 'p-3 rounded-xl text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40';
                    resultBox.innerHTML = `❌ <b>Error:</b> ${err.message || 'Failed to connect to SMTP test endpoint.'}`;
                  }
                });
              }
            </script>

            <div class="pt-4 flex items-center justify-end">
              <button type="submit" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-pink-500 to-pink-600 hover:from-pink-600 hover:to-pink-700 text-white font-display font-bold text-sm shadow-lg shadow-pink-500/25 transition transform hover:-translate-y-0.5">
                Save Platform &amp; SMTP Settings
              </button>
            </div>
          </form>
        </div>

      <!-- ================= SECTION: POSTS (DEFAULT) ================= -->
      @elseif(($section ?? '') === 'posts' || ($section ?? '') === 'overview' || empty($section))
        <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-6 shadow-xl">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="font-display font-bold text-xl text-white">All Platform Posts & Microblog Content</h2>
              <p class="text-xs text-slate-400 mt-1">Directly view and moderate posts across all 6 formats (text, photo, video, quote, link, chat).</p>
            </div>
            <span class="text-xs font-bold text-slate-400 bg-white/5 px-3 py-1.5 rounded-lg border border-white/5">
              Total Posts: <span class="text-pink-400">{{ $allPosts->count() }}</span>
            </span>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300 border-collapse">
              <thead class="text-xs uppercase bg-white/5 text-slate-400 border-b border-white/10">
                <tr>
                  <th class="p-3">ID</th>
                  <th class="p-3">Blog</th>
                  <th class="p-3">Format</th>
                  <th class="p-3">Content Snippet</th>
                  <th class="p-3">Engagement</th>
                  <th class="p-3">Status</th>
                  <th class="p-3">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/5">
                @forelse($allPosts as $p)
                  <tr class="hover:bg-white/5 transition">
                    <td class="p-3 font-mono text-xs text-slate-400">#{{ $p->id }}</td>
                    <td class="p-3 font-bold text-white">
                      <a href="{{ route('blog.show', $p->blog->slug ?? '') }}" target="_blank" class="text-pink-400 hover:underline">
                        @ {{ $p->blog->slug ?? 'unknown' }}
                      </a>
                    </td>
                    <td class="p-3">
                      <span class="px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider {{ $p->type === 'video' ? 'bg-purple-500/20 text-purple-300' : ($p->type === 'photo' ? 'bg-cyan-500/20 text-cyan-300' : ($p->type === 'quote' ? 'bg-amber-500/20 text-amber-300' : 'bg-pink-500/20 text-pink-300')) }}">
                        {{ $p->type }}
                      </span>
                    </td>
                    <td class="p-3 text-slate-200 max-w-sm truncate">
                      {{ \Illuminate\Support\Str::limit($p->heading ?? $p->caption ?? $p->quote_text ?? $p->link_title ?? 'Post #' . $p->id, 65) }}
                    </td>
                    <td class="p-3 text-xs whitespace-nowrap">
                      <span class="text-pink-400 font-bold">❤️ {{ $p->likes_count ?? $p->likes()->count() }}</span>
                      <span class="text-slate-400 ml-2">👁️ {{ $p->views_count }}</span>
                    </td>
                    <td class="p-3">
                      <span class="px-2 py-0.5 rounded text-xs font-bold {{ $p->status === 'published' ? 'bg-emerald-500/20 text-emerald-300' : ($p->status === 'queued' ? 'bg-purple-500/20 text-purple-300' : 'bg-amber-500/20 text-amber-300') }}">
                        {{ $p->status }}
                      </span>
                    </td>
                    <td class="p-3 whitespace-nowrap">
                      <div class="flex items-center gap-2">
                        <a href="{{ route('post.show', $p->id) }}" target="_blank" class="px-2.5 py-1 text-xs font-bold rounded bg-pink-600 hover:bg-pink-500 text-white transition shadow-sm">
                          View
                        </a>
                        <form method="POST" action="{{ route('admin.posts.delete', $p->id) }}" onsubmit="return confirm('Permanently remove this post?');">
                          @csrf
                          <button type="submit" class="px-2.5 py-1 text-xs font-bold rounded bg-rose-600 hover:bg-rose-500 text-white transition shadow-sm">
                            Delete
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="p-8 text-center text-slate-400">No posts published yet.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

      <!-- ================= SECTION: ADVERTISEMENTS ================= -->
      @elseif(($section ?? '') === 'ads')
        <div class="space-y-6">
          
          <!-- Top Ad Stats -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-4 rounded-xl bg-[#0f0716] border border-white/10">
              <span class="text-xs uppercase font-semibold text-slate-400">Total Ads</span>
              <div class="text-2xl font-display font-black text-white mt-1">{{ isset($allAds) ? $allAds->count() : 0 }}</div>
            </div>
            <div class="p-4 rounded-xl bg-[#0f0716] border border-white/10">
              <span class="text-xs uppercase font-semibold text-emerald-400">Active Campaigns</span>
              <div class="text-2xl font-display font-black text-emerald-400 mt-1">{{ isset($allAds) ? $allAds->where('is_active', true)->count() : 0 }}</div>
            </div>
            <div class="p-4 rounded-xl bg-[#0f0716] border border-white/10">
              <span class="text-xs uppercase font-semibold text-cyan-400">Tracked Impressions</span>
              <div class="text-2xl font-display font-black text-cyan-400 mt-1">{{ isset($allAds) ? number_format($allAds->sum('impressions_count')) : 0 }}</div>
            </div>
            <div class="p-4 rounded-xl bg-[#0f0716] border border-white/10">
              <span class="text-xs uppercase font-semibold text-pink-400">Tracked Clicks</span>
              <div class="text-2xl font-display font-black text-pink-400 mt-1">{{ isset($allAds) ? number_format($allAds->sum('clicks_count')) : 0 }}</div>
            </div>
          </div>

          <!-- Create Advertisement Form -->
          <div class="p-6 rounded-2xl bg-[#0f0716] border border-white/10">
            <h2 class="text-lg font-display font-bold text-white mb-1 flex items-center gap-2">
              {!! icon('plus') !!}
              <span>Create New Advertisement / Sponsored Banner</span>
            </h2>
            <p class="text-xs text-slate-400 mb-6">Create sitewide sponsored ads, sidebar spotlights, or feed banners with click tracking.</p>

            <form method="POST" action="{{ route('admin.ads.create') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
              @csrf

              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Ad Campaign Title</label>
                <input type="text" name="title" required placeholder="e.g. VIP Creator Spotlight" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-pink-500 transition">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Sponsor / Brand Name</label>
                <input type="text" name="sponsor_name" placeholder="e.g. Nina Studio VIP" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-pink-500 transition">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Destination Link URL</label>
                <input type="url" name="link_url" required placeholder="https://example.com/promo" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-pink-500 transition">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Image / Banner Asset URL</label>
                <input type="text" name="image_url" placeholder="hero.jpg or https://image-url" value="hero.jpg" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-pink-500 transition">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Placement Target</label>
                <select name="placement" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-pink-500 transition">
                  <option value="sidebar">Explore Sidebar Spotlight</option>
                  <option value="feed">Feed In-Stream Banner</option>
                  <option value="hero">Hero Top Banner</option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Ad Copy / Tagline</label>
                <input type="text" name="description" placeholder="Short persuasive callout" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-pink-500 transition">
              </div>

              <div class="md:col-span-2 flex items-center justify-between pt-2">
                <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-300">
                  <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-pink-600 rounded bg-[#170a22] border-white/20">
                  <span>Activate advertisement immediately</span>
                </label>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-pink-500 to-fuchsia-600 hover:from-pink-400 hover:to-fuchsia-500 text-white font-bold text-sm shadow-lg shadow-pink-500/20 transition">
                  Publish Advertisement
                </button>
              </div>
            </form>
          </div>

          <!-- All Ads Table -->
          <div class="p-6 rounded-2xl bg-[#0f0716] border border-white/10">
            <h2 class="text-lg font-display font-bold text-white mb-4">All Active & Past Advertisements</h2>

            @if(!isset($allAds) || $allAds->isEmpty())
              <div class="py-12 text-center text-slate-400 text-sm">
                No custom advertisements created yet. Use the form above to add your first campaign!
              </div>
            @else
              <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300 border-collapse">
                  <thead class="text-xs uppercase bg-white/5 text-slate-400 border-b border-white/10">
                    <tr>
                      <th class="p-3">Ad Title & Sponsor</th>
                      <th class="p-3">Placement</th>
                      <th class="p-3">Destination Link</th>
                      <th class="p-3">Performance</th>
                      <th class="p-3">Status</th>
                      <th class="p-3 text-right">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-white/5">
                    @foreach($allAds as $ad)
                      <tr class="hover:bg-white/5 transition">
                        <td class="p-3">
                          <div class="font-bold text-white">{{ $ad->title }}</div>
                          <div class="text-xs text-pink-400">{{ $ad->sponsor_name ?: 'Platform Sponsor' }}</div>
                        </td>
                        <td class="p-3">
                          <span class="px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-cyan-500/20 text-cyan-300">
                            {{ $ad->placement }}
                          </span>
                        </td>
                        <td class="p-3 font-mono text-xs max-w-xs truncate">
                          <a href="{{ $ad->link_url }}" target="_blank" class="text-slate-400 hover:text-white underline">
                            {{ $ad->link_url }}
                          </a>
                        </td>
                        <td class="p-3 text-xs">
                          <div>👁️ {{ number_format($ad->impressions_count) }} views</div>
                          <div class="text-pink-400">👆 {{ number_format($ad->clicks_count) }} clicks</div>
                        </td>
                        <td class="p-3">
                          <form method="POST" action="{{ route('admin.ads.toggle', $ad->id) }}">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 text-xs font-bold rounded {{ $ad->is_active ? 'bg-emerald-500/20 text-emerald-300 hover:bg-emerald-500/30' : 'bg-rose-500/20 text-rose-300 hover:bg-rose-500/30' }} transition">
                              {{ $ad->is_active ? 'Active' : 'Paused' }}
                            </button>
                          </form>
                        </td>
                        <td class="p-3 text-right">
                          <form method="POST" action="{{ route('admin.ads.delete', $ad->id) }}" onsubmit="return confirm('Remove this advertisement?');">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 text-xs font-bold rounded bg-rose-600 hover:bg-rose-500 text-white transition shadow-sm">
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
        </div>

      <!-- ================= SECTION: HERO SLIDER ================= -->
      @elseif(($section ?? '') === 'slider')
        <div class="space-y-6">

          <!-- Add Slide Card -->
          <div class="p-6 rounded-2xl bg-[#0f0716] border border-white/10">
            <h2 class="text-lg font-display font-bold text-white mb-1 flex items-center gap-2">
              {!! icon('plus') !!}
              <span>Add New Homepage Hero Slide</span>
            </h2>
            <p class="text-xs text-slate-400 mb-6">Add high-converting slides to the homepage carousel with custom imagery, glowing colors, and call-to-actions.</p>

            <form method="POST" action="{{ route('admin.slider.add') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
              @csrf

              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Slide Main Headline</label>
                <input type="text" name="title" required placeholder="e.g. Follow your desires." class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-pink-500 transition">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Slide Subtext / Copy</label>
                <input type="text" name="text" required placeholder="Short descriptive copy for the banner" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-pink-500 transition">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Background Image Asset</label>
                <input type="text" name="img" value="hero.jpg" placeholder="hero.jpg or image URL" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-pink-500 transition">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Accent Glow Color</label>
                <div class="flex items-center gap-3">
                  <input type="color" name="color" value="#ff2bd6" class="w-12 h-10 rounded-lg cursor-pointer bg-transparent border-0">
                  <span class="text-xs text-slate-400">Controls glowing headline shadow & button highlights</span>
                </div>
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Primary Button (Label & URL)</label>
                <div class="grid grid-cols-2 gap-2">
                  <input type="text" name="cta1_label" value="Create blog" placeholder="Label" class="bg-[#170a22] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                  <input type="text" name="cta1_url" value="/register" placeholder="/register" class="bg-[#170a22] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                </div>
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Secondary Button (Label & URL)</label>
                <div class="grid grid-cols-2 gap-2">
                  <input type="text" name="cta2_label" value="Explore feed" placeholder="Label" class="bg-[#170a22] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                  <input type="text" name="cta2_url" value="#feed" placeholder="#feed" class="bg-[#170a22] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                </div>
              </div>

              <div class="md:col-span-2 pt-2">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-pink-500 to-fuchsia-600 hover:from-pink-400 hover:to-fuchsia-500 text-white font-bold text-sm shadow-lg shadow-pink-500/20 transition">
                  Add Slide to Hero Slider
                </button>
              </div>
            </form>
          </div>

          <!-- Current Slides Preview & Delete -->
          <div class="p-6 rounded-2xl bg-[#0f0716] border border-white/10">
            <h2 class="text-lg font-display font-bold text-white mb-4">Current Active Hero Slides ({{ isset($heroSlides) ? count($heroSlides) : 0 }})</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              @if(isset($heroSlides))
                @foreach($heroSlides as $idx => $s)
                  <div class="rounded-xl border border-white/10 bg-[#170a22] overflow-hidden flex flex-col justify-between group hover:border-pink-500/40 transition">
                    <div class="p-4">
                      <div class="flex items-center justify-between mb-2">
                        <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-white/10 text-white">#{{ $idx + 1 }}</span>
                        <span class="w-4 h-4 rounded-full border border-white/20" style="background-color: {{ $s['color'] ?? '#ff2bd6' }};"></span>
                      </div>
                      <h3 class="font-display font-bold text-base text-white mb-1 line-clamp-1">{{ $s['title'] ?? 'Slide Title' }}</h3>
                      <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed mb-3">{{ $s['text'] ?? '' }}</p>
                      
                      <div class="text-[11px] text-slate-500 space-y-1 pt-2 border-t border-white/5">
                        <div><b>Image:</b> {{ $s['img'] ?? 'hero.jpg' }}</div>
                        @if(isset($s['cta']) && is_array($s['cta']))
                          <div><b>CTA:</b> {{ implode(', ', array_column($s['cta'], 'label')) }}</div>
                        @endif
                      </div>
                    </div>

                    <div class="p-3 bg-black/40 border-t border-white/10 flex items-center justify-between">
                      <span class="text-[11px] text-slate-400">Order: {{ $idx + 1 }}</span>
                      <form method="POST" action="{{ route('admin.slider.delete', $idx) }}" onsubmit="return confirm('Remove this slide from the homepage?');">
                        @csrf
                        <button type="submit" class="px-2.5 py-1 text-xs font-bold rounded bg-rose-600/80 hover:bg-rose-500 text-white transition">
                          Remove Slide
                        </button>
                      </form>
                    </div>
                  </div>
                @endforeach
              @endif
            </div>
          </div>
        </div>

      @endif

    </main>
  </div>

  <script>
    function copyIpnWebhookUrl() {
      const input = document.getElementById('ipnWebhookUrlInput');
      if (input) {
        navigator.clipboard.writeText(input.value).then(() => {
          alert('NOWPayments IPN Webhook URL copied to clipboard!\n' + input.value);
        });
      }
    }

    async function testNowPaymentsConnection() {
      const btn = document.getElementById('btnTestApi');
      const ind = document.getElementById('nowpaymentsStatusIndicator');
      const text = document.getElementById('nowpaymentsStatusText');
      if (btn) btn.textContent = 'Testing...';

      try {
        const res = await fetch('{{ route("donations.nowpayments.status") }}');
        const data = await res.json();
        if (data && data.ok) {
          ind.className = 'flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-xs font-bold';
          text.textContent = `Connected (${data.latencyMs}ms)`;
          alert(`NOWPayments API connection successful!\nStatus: ${data.message}\nLatency: ${data.latencyMs}ms\nBase URL: ${data.baseUrl}`);
        } else {
          ind.className = 'flex items-center gap-2 px-3 py-1.5 rounded-lg bg-rose-500/20 border border-rose-500/30 text-rose-300 text-xs font-bold';
          text.textContent = 'Error: ' + (data.message || 'Offline');
          alert(`NOWPayments API test failed:\n${data.message || 'Unknown error'}`);
        }
      } catch (err) {
        ind.className = 'flex items-center gap-2 px-3 py-1.5 rounded-lg bg-rose-500/20 border border-rose-500/30 text-rose-300 text-xs font-bold';
        text.textContent = 'Network Error';
        alert('Error connecting to NOWPayments status endpoint.');
      } finally {
        if (btn) btn.textContent = 'Test Connection';
      }
    }

    async function testBitcartSimulation() {
      if (!confirm('Simulate an incoming successful BitCart webhook payment for your admin account?')) return;
      try {
        const res = await fetch('{{ route("donations.bitcart.simulate") }}', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({ plan: 'premium_plus' })
        });
        const data = await res.json();
        if (data && data.ok) {
          alert('BitCart Webhook Simulation Successful!\n' + data.message + '\nOrder ID: ' + data.order_id);
          window.location.reload();
        } else {
          alert('Simulation failed: ' + (data.message || 'Unknown error'));
        }
      } catch (err) {
        alert('Network error during BitCart simulation.');
      }
    }
  </script>
</body>
</html>
