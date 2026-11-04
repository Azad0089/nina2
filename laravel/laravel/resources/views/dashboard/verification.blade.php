<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Creator ID Verification &middot; Creator Studio | {{ site_name() }}</title>
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
    <aside class="w-full lg:w-72 bg-[#0c0514] border-b lg:border-b-0 lg:border-r border-white/10 flex-shrink-0 flex flex-col justify-between p-4 lg:p-6 lg:sticky lg:top-0 lg:h-screen lg:overflow-y-auto">
      <div>
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

        <nav class="space-y-1">
          <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:text-white hover:bg-white/5 transition">
            {!! icon('compass') !!}
            <span>Overview</span>
          </a>

          <a href="{{ route('dashboard.post.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:text-white hover:bg-white/5 transition">
            {!! icon('plus') !!}
            <span>Create Post</span>
          </a>

          <a href="{{ route('dashboard.posts') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:text-white hover:bg-white/5 transition">
            {!! icon('list') !!}
            <span>My Posts</span>
          </a>

          <a href="{{ route('dashboard.queue') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:text-white hover:bg-white/5 transition">
            {!! icon('flame') !!}
            <span>Schedule Queue</span>
          </a>

          <a href="{{ route('dashboard.blogs') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:text-white hover:bg-white/5 transition">
            {!! icon('edit') !!}
            <span>My Blogs & URLs</span>
          </a>

          <a href="{{ route('dashboard.referrals') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:text-white hover:bg-white/5 transition">
            {!! icon('users') !!}
            <span>Referrals & Rewards</span>
          </a>

          <a href="{{ route('dashboard.verification') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold bg-gradient-to-r from-pink-500/20 to-transparent text-pink-400 border-l-4 border-pink-500">
            {!! icon('verified') !!}
            <span>ID Verification</span>
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

          <a href="{{ url('/') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:text-white hover:bg-white/5 transition">
            {!! icon('left') !!}
            <span>Back to Explore</span>
          </a>
        </nav>
      </div>

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
            <div class="text-xs text-pink-400 font-semibold">
              <span>{{ number_format(auth()->user()->points ?? 0) }} pts</span>
            </div>
          </div>
        </div>

        <a href="{{ route('logout') }}" title="Sign out" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-white/5 rounded-lg transition">
          {!! icon('login') !!}
        </a>
      </div>
    </aside>

    <!-- MAIN VIEW -->
    <main class="flex-1 min-w-0 bg-[#07030d] p-4 lg:p-8 overflow-y-auto">

      <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-6 border-b border-white/10">
        <div>
          <h1 class="text-2xl lg:text-3xl font-display font-black text-white tracking-tight flex items-center gap-3">
            <span>Creator Identity Verification</span>
            <span class="text-xs px-2.5 py-1 bg-emerald-500/20 text-emerald-400 rounded-lg font-sans font-bold uppercase tracking-wider">Trust & Safety</span>
          </h1>
          <p class="text-xs lg:text-sm text-slate-400 mt-1">
            Unlock the official verified creator badge and priority ranking across {{ site_name() }}.
          </p>
        </div>
      </header>

      @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm flex items-center gap-3">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          <span>{{ session('success') }}</span>
        </div>
      @endif

      @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm flex items-center gap-3">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          <span>{{ session('error') }}</span>
        </div>
      @endif

      <div class="max-w-2xl mx-auto">
        <div class="p-6 lg:p-8 rounded-2xl bg-[#0f0716] border border-white/10 shadow-xl">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
              {!! icon('verified') !!}
            </div>
            <div>
              <h2 class="text-xl font-display font-black text-white">Lifetime Verified Creator Badge</h2>
              <p class="text-xs text-slate-400">Authentic proof of identity for creators</p>
            </div>
          </div>

          <p class="text-xs lg:text-sm text-slate-300 leading-relaxed mb-6">
            Creators who submit verified legal identification receive a permanent <b>Identity Verified Creator</b> badge {!! badge('id') !!} on their blogs and publications. This builds immediate trust with readers and unlocks boosted algorithm discovery.
          </p>

          @if($user->badge_id)
            <div class="p-5 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-start gap-4">
              <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0">
                {!! icon('verified') !!}
              </div>
              <div>
                <strong class="text-base text-emerald-300 font-bold block">You are Identity Verified!</strong>
                <p class="text-xs text-emerald-400/80 mt-1 leading-relaxed">
                  Your official verified creator badge is active for life across all of your blogs, posts, and comments.
                </p>
              </div>
            </div>
          @elseif($latest && $latest->status === 'pending')
            <div class="p-5 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 flex items-start gap-4">
              <div class="w-10 h-10 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center flex-shrink-0 animate-pulse">
                {!! icon('flame') !!}
              </div>
              <div>
                <strong class="text-base text-cyan-300 font-bold block">Verification In Progress</strong>
                <p class="text-xs text-cyan-400/80 mt-1 leading-relaxed">
                  Your legal identification documents have been securely submitted and are currently being reviewed by our moderation staff. Reviews typically conclude within 24 hours.
                </p>
              </div>
            </div>
          @else
            <form method="POST" action="{{ route('dashboard.verification.submit') }}" enctype="multipart/form-data" class="space-y-5">
              @csrf

              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Full Legal Name</label>
                <input type="text" name="full_name" required placeholder="Jane Doe" class="w-full bg-[#170a22] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 transition">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Government ID / Passport / Driver's License</label>
                <div class="border-2 border-dashed border-white/15 rounded-2xl p-6 text-center hover:border-pink-500/50 transition bg-white/[0.01]">
                  <input type="file" name="id_document" id="idDocInput" required accept="image/*,.pdf" class="hidden">
                  <label for="idDocInput" class="cursor-pointer block">
                    <div class="w-12 h-12 rounded-xl bg-pink-500/10 text-pink-400 mx-auto flex items-center justify-center mb-3">
                      {!! icon('verified') !!}
                    </div>
                    <span class="text-sm font-bold text-white block">Upload ID Document (Image or PDF)</span>
                    <span class="text-xs text-slate-400 block mt-1">Clear photo or scan of passport, driver's license, or national identity card</span>
                    <span id="selectedIdName" class="text-xs text-pink-400 font-semibold mt-2 block"></span>
                  </label>
                </div>
              </div>

              <div class="pt-4">
                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-pink-500 to-fuchsia-600 hover:from-pink-400 hover:to-fuchsia-500 text-white font-bold text-sm shadow-lg shadow-pink-500/20 transition flex items-center justify-center gap-2">
                  {!! icon('verified') !!}
                  <span>Submit Identity for Review</span>
                </button>
              </div>

              <p class="text-[11px] text-slate-500 text-center leading-normal">
                🔒 Privacy Guarantee: Identification documents are encrypted during transfer and purged from our servers once verification status is confirmed.
              </p>
            </form>

            <script>
              const idInput = document.getElementById('idDocInput');
              const idName = document.getElementById('selectedIdName');
              if (idInput) {
                idInput.addEventListener('change', () => {
                  if (idInput.files.length > 0) {
                    idName.textContent = 'Selected: ' + idInput.files[0].name;
                  }
                });
              }
            </script>
          @endif
        </div>
      </div>

    </main>
  </div>
</body>
</html>
