<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
        <a href="{{ route('admin.reports') }}" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition {{ ($section ?? '') === 'reports' ? 'bg-pink-600 text-white shadow-lg shadow-pink-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">Reports ({{ $reports->where('status','open')->count() }})</a>
        <a href="{{ route('admin.support') }}" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition {{ ($section ?? '') === 'support' ? 'bg-pink-600 text-white shadow-lg shadow-pink-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">Support ({{ $tickets->where('status','open')->count() }})</a>
        <a href="{{ route('admin.logins') }}" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition {{ ($section ?? '') === 'logins' ? 'bg-pink-600 text-white shadow-lg shadow-pink-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">Security & IPs</a>
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
              <h2 class="font-display font-bold text-xl text-white">Member Accounts & Moderation</h2>
              <p class="text-xs text-slate-400 mt-1">Manage badges, award reward points, suspend or ban bad actors.</p>
            </div>
            <div class="text-xs font-bold text-slate-400 bg-white/5 px-3 py-1.5 rounded-lg border border-white/5">
              Total Accounts: <span class="text-pink-400">{{ $allUsers->count() }}</span>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300 border-collapse">
              <thead class="text-xs uppercase bg-white/5 text-slate-400 border-b border-white/10">
                <tr>
                  <th class="p-3">ID</th>
                  <th class="p-3">User & Role</th>
                  <th class="p-3">Email</th>
                  <th class="p-3">Status</th>
                  <th class="p-3">Points</th>
                  <th class="p-3">Badges</th>
                  <th class="p-3">Referrals</th>
                  <th class="p-3">Quick Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/5">
                @foreach($allUsers as $u)
                  <tr class="hover:bg-white/5 transition">
                    <td class="p-3 font-mono text-xs text-slate-400">#{{ $u->id }}</td>
                    <td class="p-3">
                      <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-gradient-to-tr from-pink-500/20 to-purple-500/20 border border-pink-500/30 text-pink-300 flex items-center justify-center font-bold text-xs flex-shrink-0">
                          {{ initial($u->username) }}
                        </span>
                        <div>
                          <div class="font-bold text-white">@ {{ $u->username }}</div>
                          <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">{{ $u->role }}</span>
                        </div>
                      </div>
                    </td>
                    <td class="p-3 text-slate-400 font-mono text-xs">{{ $u->email }}</td>
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
                    <td class="p-3 whitespace-nowrap">
                      {!! badge($u->badge_vip ? 'vip' : '') !!}
                      {!! badge($u->badge_id ? 'id' : '') !!}
                      {!! badge($u->badge_pro ? 'pro' : '') !!}
                    </td>
                    <td class="p-3 text-xs">
                      <span class="font-bold text-white">{{ $u->referrals()->count() }} users</span>
                      <div class="text-[10px] text-slate-400 font-mono">Code: {{ $u->referral_code ?? 'None' }}</div>
                    </td>
                    <td class="p-3">
                      <div class="flex items-center gap-1.5 flex-wrap">
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
                        <a href="{{ asset('storage/' . $v->document_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-pink-500/20 border border-pink-500/30 text-pink-300 font-bold text-xs hover:bg-pink-500/30 transition">
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
        <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-6 shadow-xl">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="font-display font-bold text-xl text-white">Crypto Donations & Premium Subscriptions</h2>
              <p class="text-xs text-slate-400 mt-1">Real-time ledger of NOWPayments & BitCart transactions.</p>
            </div>
            <div class="text-sm font-bold text-emerald-400 bg-emerald-500/20 px-3 py-1.5 rounded-lg border border-emerald-500/30">
              Total Raised: ${{ number_format($donations->where('status','confirmed')->sum('amount_usd'), 2) }}
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
                  <th class="p-3">Crypto Amount</th>
                  <th class="p-3">Status</th>
                  <th class="p-3">Date</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/5">
                @forelse($donations as $d)
                  <tr class="hover:bg-white/5 transition">
                    <td class="p-3 font-mono text-xs text-pink-400">{{ $d->order_id }}</td>
                    <td class="p-3 font-bold text-white">@ {{ $d->user?->username ?? 'Anonymous' }}</td>
                    <td class="p-3">
                      <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-white/10 text-slate-200 uppercase">
                        {{ $d->gateway }}
                      </span>
                    </td>
                    <td class="p-3 font-semibold text-slate-200 capitalize">{{ str_replace('_', ' ', $d->plan) }}</td>
                    <td class="p-3 font-bold text-emerald-400">${{ number_format($d->amount_usd, 2) }}</td>
                    <td class="p-3 font-mono text-xs text-slate-300">{{ $d->crypto_amount ? ($d->crypto_amount . ' ' . $d->crypto_currency) : '-' }}</td>
                    <td class="p-3">
                      <span class="px-2 py-0.5 rounded text-xs font-bold {{ $d->status === 'confirmed' ? 'bg-emerald-500/20 text-emerald-300' : ($d->status === 'pending' ? 'bg-amber-500/20 text-amber-300 animate-pulse' : 'bg-rose-500/20 text-rose-300') }}">
                        {{ $d->status }}
                      </span>
                    </td>
                    <td class="p-3 text-xs text-slate-400">{{ $d->created_at->format('M d, Y H:i') }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="p-8 text-center text-slate-400">No transactions recorded yet.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
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
                      @if($r->post_id)
                        <a href="{{ route('post.show', $r->post_id) }}" target="_blank" class="text-pink-400 hover:underline font-semibold">Post #{{ $r->post_id }}</a>
                      @elseif($r->blog_id)
                        <span class="text-purple-400 font-semibold">Blog #{{ $r->blog_id }}</span>
                      @else
                        <span class="text-slate-400">General</span>
                      @endif
                    </td>
                    <td class="p-3 text-slate-200 max-w-xs break-words">{{ $r->reason }}</td>
                    <td class="p-3">
                      <span class="px-2 py-0.5 rounded text-xs font-bold {{ $r->status === 'resolved' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                        {{ $r->status }}
                      </span>
                    </td>
                    <td class="p-3 text-xs text-slate-400">{{ $r->created_at->diffForHumans() }}</td>
                    <td class="p-3">
                      @if($r->status !== 'resolved')
                        <form method="POST" action="{{ route('admin.reports.resolve', $r->id) }}">
                          @csrf
                          <button type="submit" class="px-3 py-1 text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white rounded transition shadow-sm">
                            Mark Resolved
                          </button>
                        </form>
                      @else
                        <span class="text-xs text-slate-400">Closed</span>
                      @endif
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
        <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-6 shadow-xl">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="font-display font-bold text-xl text-white">Member Login Security & IP Audits</h2>
              <p class="text-xs text-slate-400 mt-1">Audit recent user sign-ins, IP addresses, and user agents for fraud detection.</p>
            </div>
            <span class="text-xs font-bold text-slate-400 bg-white/5 px-3 py-1.5 rounded-lg border border-white/5">
              Showing Last 50 Sessions
            </span>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300 border-collapse">
              <thead class="text-xs uppercase bg-white/5 text-slate-400 border-b border-white/10">
                <tr>
                  <th class="p-3">Time</th>
                  <th class="p-3">User</th>
                  <th class="p-3">IP Address</th>
                  <th class="p-3">Device & Browser</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/5">
                @forelse($logins as $l)
                  <tr class="hover:bg-white/5 transition">
                    <td class="p-3 text-xs text-slate-400 whitespace-nowrap">
                      {{ $l->logged_at ? \Carbon\Carbon::parse($l->logged_at)->diffForHumans() : $l->created_at->diffForHumans() }}
                    </td>
                    <td class="p-3 font-bold text-white">@ {{ $l->user?->username ?? 'Unknown' }}</td>
                    <td class="p-3 font-mono text-xs text-cyan-400 font-semibold">{{ $l->ip_address }}</td>
                    <td class="p-3 text-xs text-slate-400 max-w-sm truncate">{{ $l->user_agent }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="p-8 text-center text-slate-400">No login security records captured yet.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

      <!-- ================= SECTION: PLATFORM SETTINGS ================= -->
      @elseif(($section ?? '') === 'settings')
        <div class="bg-[#0f0716] border border-white/10 rounded-2xl p-6 lg:p-8 shadow-xl">
          <div class="mb-6 pb-4 border-b border-white/10">
            <h2 class="font-display font-bold text-2xl text-white">Platform Branding, SEO & Integration Settings</h2>
            <p class="text-sm text-slate-400 mt-1">Configure global platform options, API keys, payment webhooks, and community bio.</p>
          </div>

          <form method="POST" action="{{ route('admin.settings.save') }}" class="space-y-8">
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
                  <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Logo Asset URL</label>
                  <input type="text" name="site_logo" value="{{ $settings['site_logo'] ?? '' }}" placeholder="assets/logo.png" class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white focus:outline-none focus:border-pink-500">
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

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Max Media Upload (MB)</label>
                    <input type="number" name="max_media_mb" value="{{ $settings['max_media_mb'] ?? 50 }}" class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white focus:outline-none focus:border-pink-500">
                  </div>

                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Referral Reward Points</label>
                    <input type="number" name="referral_reward_points" value="{{ $settings['referral_reward_points'] ?? 100 }}" class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white focus:outline-none focus:border-pink-500">
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

                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">NOWPayments API Key</label>
                  <input type="text" name="nowpayments_api_key" value="{{ $settings['nowpayments_api_key'] ?? '4DMYGP5-7V04ZZJ-G3JTHJH-XGNZHD5' }}" class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white font-mono text-sm focus:outline-none focus:border-pink-500">
                </div>

                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">NOWPayments IPN Secret</label>
                  <input type="text" name="nowpayments_ipn_secret" value="{{ $settings['nowpayments_ipn_secret'] ?? 'mIV4c5kQrKwYN4PEGRV4L5MM8UQUhAEz' }}" class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white font-mono text-sm focus:outline-none focus:border-pink-500">
                </div>

                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">BitCart Store URL</label>
                  <input type="text" name="bitcart_url" value="{{ $settings['bitcart_url'] ?? 'https://bitcart.server5.click/store/01M2V1SFTBTD7TCN1HKKNKS2WK' }}" class="w-full px-3.5 py-2.5 bg-[#07030d] border border-white/10 rounded-xl text-white font-mono text-sm focus:outline-none focus:border-pink-500">
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
                  <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Sitewide Head Scripts (Google Analytics, Pixels)</label>
                  <textarea name="head_scripts" rows="3" placeholder="<script async src='https://www.googletagmanager.com/gtag/js?id=...'></script>" class="w-full px-3.5 py-2 bg-[#07030d] border border-white/10 rounded-xl text-white font-mono text-xs focus:outline-none focus:border-pink-500">{{ $settings['head_scripts'] ?? '' }}</textarea>
                </div>
              </div>
            </div>

            <!-- Community Hub Profile Configuration -->
            <div class="pt-6 border-t border-white/10">
              <div class="flex items-center gap-2 pb-4 text-purple-400 font-bold text-base">
                {!! icon('users') !!}
                <span>Community Profile Setup (Bio, 3 URLs & Contact Email)</span>
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

            <div class="pt-4 flex items-center justify-end">
              <button type="submit" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-pink-500 to-pink-600 hover:from-pink-600 hover:to-pink-700 text-white font-display font-bold text-sm shadow-lg shadow-pink-500/25 transition transform hover:-translate-y-0.5">
                Save Platform Settings
              </button>
            </div>
          </form>
        </div>

      <!-- ================= SECTION: POSTS (DEFAULT) ================= -->
      @else
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
</body>
</html>
