<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? ucfirst($section ?? 'Dashboard') }} &middot; Creator Studio | {{ site_name() }}</title>
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
                        <a href="{{ route('post.show', ['id' => $p->id]) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-white/5 hover:bg-white/10 text-xs font-bold text-pink-400 transition">
                          View
                        </a>
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
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Media Upload</label>
                <div class="border-2 border-dashed border-white/15 rounded-2xl p-6 text-center hover:border-pink-500/50 transition bg-white/[0.01]">
                  <input type="file" name="media_file" id="mediaFileInput" accept="image/*,video/mp4,video/webm" class="hidden">
                  <label for="mediaFileInput" class="cursor-pointer block">
                    <div class="w-12 h-12 rounded-xl bg-pink-500/10 text-pink-400 mx-auto flex items-center justify-center mb-3">
                      {!! icon('photo') !!}
                    </div>
                    <span class="text-sm font-bold text-white block">Click to select photo or MP4 video</span>
                    <span class="text-xs text-slate-400 block mt-1">Supports JPG, PNG, WEBP, and MP4 videos up to 50MB</span>
                    <span id="selectedFileName" class="text-xs text-pink-400 font-semibold mt-2 block"></span>
                  </label>
                </div>
              </div>

              <!-- Heading / Title Field -->
              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Heading / Title (Optional)</label>
                <input type="text" name="heading" placeholder="Catchy post title..." class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 transition">
              </div>

              <!-- Body Text / Caption -->
              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2" id="bodyLabel">Body Content</label>
                <textarea name="body" rows="6" placeholder="Write your post content, description or markdown here..." class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 transition"></textarea>
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

              <!-- Action Submission Buttons -->
              <div class="pt-4 border-t border-white/10 flex flex-col sm:flex-row items-center gap-3">
                <button type="submit" name="post_action" value="publish" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-gradient-to-r from-pink-500 to-fuchsia-600 hover:from-pink-400 hover:to-fuchsia-500 text-white font-bold text-sm shadow-lg shadow-pink-500/20 transition flex items-center justify-center gap-2">
                  {!! icon('plus') !!}
                  <span>Publish Immediately</span>
                </button>

                <button type="submit" name="post_action" value="queue" class="w-full sm:w-auto px-5 py-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-white font-bold text-sm transition flex items-center justify-center gap-2" title="Queue post for automatic delivery">
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

          if (fileInput) {
            fileInput.addEventListener('change', () => {
              if (fileInput.files.length > 0) {
                fileName.textContent = 'Selected: ' + fileInput.files[0].name;
              }
            });
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
              quoteBox.classList.toggle('hidden', type !== 'quote');
              linkBox.classList.toggle('hidden', type !== 'link');
              chatBox.classList.toggle('hidden', type !== 'chat');

              if (type === 'photo' || type === 'video') {
                bodyLabel.textContent = 'Caption / Text (Optional)';
              } else {
                bodyLabel.textContent = 'Body Content';
              }
            });
          });
        </script>


      <!-- ================= SECTION: QUEUE ================= -->
      @elseif(($section ?? '') === 'queue')

        <div class="p-6 lg:p-8 rounded-2xl bg-[#0f0716] border border-white/10">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-6 border-b border-white/10">
            <div>
              <h2 class="text-xl font-display font-black text-white flex items-center gap-2">
                <span>Blog Post Queue</span>
                <span class="text-xs px-2.5 py-1 rounded-full bg-pink-500/20 text-pink-400 font-bold">
                  {{ $queuedPosts->count() }} / 20 Slots
                </span>
              </h2>
              <p class="text-xs lg:text-sm text-slate-400 mt-1">
                Queue up to 20 text, photo, quote, and link posts. Queued items are published sequentially.
              </p>
            </div>

            <a href="{{ route('dashboard.post.create') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-pink-500 to-fuchsia-600 hover:from-pink-400 hover:to-fuchsia-500 text-white font-bold text-xs shadow-lg shadow-pink-500/20 transition flex items-center gap-2 self-start">
              {!! icon('plus') !!}
              <span>Add to Queue</span>
            </a>
          </div>

          @if($queuedPosts->isEmpty())
            <div class="py-16 text-center text-slate-400">
              <div class="w-12 h-12 rounded-xl bg-white/5 text-slate-500 mx-auto flex items-center justify-center mb-3">
                {!! icon('flame') !!}
              </div>
              <h3 class="font-bold text-white text-base mb-1">Queue is currently empty</h3>
              <p class="text-xs text-slate-400 max-w-sm mx-auto mb-4">Keep your audience engaged around the clock by scheduling upcoming posts in advance.</p>
              <a href="{{ route('dashboard.post.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-pink-500 text-white text-xs font-bold hover:bg-pink-400 transition">
                {!! icon('plus') !!}
                <span>Queue a Post Now</span>
              </a>
            </div>
          @else
            <div class="overflow-x-auto">
              <table class="w-full text-left text-sm">
                <thead>
                  <tr class="text-slate-400 border-b border-white/10 text-xs uppercase font-semibold">
                    <th class="py-3 px-3">Order</th>
                    <th class="py-3 px-3">Format</th>
                    <th class="py-3 px-3">Content Preview</th>
                    <th class="py-3 px-3">Queued At</th>
                    <th class="py-3 px-3 text-right">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-slate-300">
                  @foreach($queuedPosts as $idx => $qp)
                    <tr class="hover:bg-white/[0.02] transition">
                      <td class="py-3 px-3 font-mono text-xs text-slate-500 font-bold">#{{ $idx + 1 }}</td>
                      <td class="py-3 px-3">
                        <span class="text-xs uppercase px-2 py-0.5 rounded-full font-bold {{ $qp->type === 'photo' ? 'bg-pink-500/20 text-pink-400' : 'bg-white/10 text-slate-300' }}">
                          {{ $qp->type }}
                        </span>
                      </td>
                      <td class="py-3 px-3 font-semibold text-white max-w-xs truncate">
                        {{ $qp->heading ?: ($qp->caption ?: ($qp->quote_text ?: 'Queued Post #' . $qp->id)) }}
                      </td>
                      <td class="py-3 px-3 text-slate-400 text-xs">{{ $qp->created_at->diffForHumans() }}</td>
                      <td class="py-3 px-3 text-right space-x-2">
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
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-xl font-display font-black text-white">Edit Profile & Appearance</h2>
              <span class="text-xs px-2.5 py-1 rounded-full bg-pink-500/20 text-pink-400 font-bold">@ {{ $blog->slug }}</span>
            </div>
            <p class="text-xs text-slate-400 mb-6">
              Customize title, bio, header links, avatar, cover banner, and accent colors.
            </p>

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

              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">About / Bio</label>
                <textarea name="bio" rows="3" placeholder="Tell visitors about your blog..." class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-pink-500 transition">{{ $blog->bio ?? '' }}</textarea>
              </div>

              <!-- Profile Links (3 websites & contact email) -->
              <div class="pt-4 border-t border-white/10 space-y-3">
                <h3 class="text-xs font-display font-bold uppercase tracking-wider text-pink-400">Header Social & Website Links</h3>
                
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

              <!-- Avatar & Cover -->
              <div class="pt-4 border-t border-white/10 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Avatar Icon</label>
                  <input type="file" name="avatar" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-white/10 file:text-white hover:file:bg-white/20">
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Cover Banner</label>
                  <input type="file" name="cover" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-white/10 file:text-white hover:file:bg-white/20">
                </div>
              </div>

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
                You can create unlimited separate blogs under one account. Each gets its own URL slug, styling, and posts!
              </p>

              <form method="POST" action="{{ route('dashboard.blogs.store') }}" class="space-y-4">
                @csrf
                <div>
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Blog URL Slug</label>
                  <div class="flex rounded-xl bg-[#170a22] border border-white/10 overflow-hidden focus-within:border-pink-500 transition">
                    <span class="px-3 py-3 text-xs text-slate-500 bg-white/5 font-mono select-none">/blog/</span>
                    <input type="text" name="slug" required placeholder="my-alias" class="flex-1 bg-transparent px-3 py-3 text-sm text-white focus:outline-none lowercase font-mono">
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Blog Display Title</label>
                  <input type="text" name="title" required placeholder="My Creative Space" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-pink-500 transition">
                </div>

                <div>
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Tagline (Optional)</label>
                  <input type="text" name="tagline" placeholder="Drawings and unfiltered thoughts" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-pink-500 transition">
                </div>

                <div>
                  <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Theme Color</label>
                  <input type="color" name="color" value="#19e3ff" class="w-12 h-10 rounded-lg cursor-pointer bg-transparent border-0">
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-white/10 hover:bg-white/15 text-white font-bold text-sm transition">
                  Create New Blog
                </button>
              </form>
            </div>

            <!-- List of All Blogs -->
            <div class="p-6 rounded-2xl bg-[#0f0716] border border-white/10">
              <h3 class="text-base font-display font-bold text-white mb-3">All Your Blogs ({{ isset($allBlogs) ? $allBlogs->count() : 1 }})</h3>
              <div class="space-y-2">
                @if(isset($allBlogs))
                  @foreach($allBlogs as $ub)
                    <div class="p-3.5 rounded-xl bg-white/[0.02] border border-white/10 flex items-center justify-between hover:bg-white/5 transition">
                      <div>
                        <div class="font-bold text-white text-sm flex items-center gap-2">
                          <span>@ {{ $ub->slug }}</span>
                          @if($ub->is_main)
                            <span class="text-[10px] bg-pink-500/20 text-pink-400 font-sans px-1.5 py-0.5 rounded font-bold uppercase">Main</span>
                          @endif
                        </div>
                        <p class="text-xs text-slate-400">{{ $ub->title }} &middot; {{ $ub->posts()->count() }} posts</p>
                      </div>
                      <a href="{{ route('blog.show', $ub->slug) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-xs font-bold text-slate-300 hover:text-white transition">
                        Visit &rarr;
                      </a>
                    </div>
                  @endforeach
                @endif
              </div>
            </div>

          </div>
        </div>


      <!-- ================= SECTION: REFERRALS ================= -->
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
                        <a href="{{ route('post.show', ['id' => $p->id]) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-white/5 hover:bg-white/10 text-xs font-bold text-pink-400 transition">
                          View Post &rarr;
                        </a>
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
</body>
</html>
