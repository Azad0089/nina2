@extends('layouts.app')

@section('title', 'Advertising Marketplace | ' . site_name())
@section('description', 'Spend your points to boost your blog or posts across high-traffic placements on ' . site_name())

@section('content')
  @include('partials.header')

  <main class="page" id="main">
    @include('partials.page-hero', [
      'hero' => [
        'title' => 'Community Advertising Marketplace',
        'lead' => 'Spend your reward points to promote your blog posts and gain thousands of real impressions.',
        'color' => '#ff2bd6'
      ]
    ])

    @if(session('success'))
      <div class="panel" style="background:#10b98122;border:1px solid #10b981;color:#10b981;margin-bottom:20px;padding:14px;border-radius:8px;">
        {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div class="panel" style="background:#ef444422;border:1px solid #ef4444;color:#ef4444;margin-bottom:20px;padding:14px;border-radius:8px;">
        {{ session('error') }}
      </div>
    @endif

    <div class="balance">
      <div>
        <small>Your Point Balance</small>
        <b>{{ auth()->check() ? number_format(auth()->user()->points ?? 0) . ' points available' : 'Sign in to see your balance' }}</b>
      </div>
      @guest
        <a class="btn btn-fill" href="{{ route('login') }}" data-modal="login">Sign In</a>
      @else
        <div style="display:flex;gap:8px;">
          <a class="btn btn-fill" href="{{ route('premium') }}">{!! icon('crown') !!} Get More Points (Premium)</a>
          <a class="btn btn-line" href="{{ route('dashboard.referrals') }}">{!! icon('users') !!} Earn via Referrals</a>
        </div>
      @endguest
    </div>

    <!-- Active Promotions -->
    @if(auth()->check() && isset($activePromotions) && $activePromotions->isNotEmpty())
      <h2 class="sec-title">Your Active Campaigns</h2>
      <div class="panel" style="margin-bottom:30px;overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;text-align:left;">
          <thead>
            <tr style="border-bottom:1px solid var(--line);">
              <th style="padding:10px;">Package</th>
              <th style="padding:10px;">Target</th>
              <th style="padding:10px;">Progress</th>
              <th style="padding:10px;">Expires / Ends</th>
              <th style="padding:10px;">Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($activePromotions as $promo)
              <tr style="border-bottom:1px solid var(--line);">
                <td style="padding:10px;"><b>{{ $promo->package_name }}</b></td>
                <td style="padding:10px;">
                  @if($promo->post)
                    <a href="{{ route('post.show', $promo->post->id) }}" target="_blank" style="color:var(--primary);">Post #{{ $promo->post->id }}</a>
                  @elseif($promo->blog)
                    <a href="{{ route('blog.show', $promo->blog->slug) }}" target="_blank" style="color:var(--primary);">@ {{ $promo->blog->slug }}</a>
                  @else
                    Platform Sitewide
                  @endif
                </td>
                <td style="padding:10px;">
                  {{ $promo->target_views > 0 ? ($promo->current_views . ' / ' . number_format($promo->target_views) . ' views') : ($promo->duration_days . ' days placement') }}
                </td>
                <td style="padding:10px;">
                  {{ $promo->expires_at ? \Carbon\Carbon::parse($promo->expires_at)->diffForHumans() : 'Until views depleted' }}
                </td>
                <td style="padding:10px;"><span style="color:#10b981;font-weight:700;">{{ strtoupper($promo->status) }}</span></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

    <!-- Purchase Promotion Form -->
    @if(auth()->check() && isset($userPosts) && $userPosts->isNotEmpty())
      <div class="panel" style="margin-bottom:36px;background:var(--paper);border:1px solid var(--line);border-radius:10px;padding:24px;">
        <h2 style="font:800 20px var(--f-display);margin-bottom:14px;">Instant Boost: Select Your Post</h2>
        <form method="POST" action="{{ route('advertising.purchase') }}">
          @csrf
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
            <div>
              <label style="display:block;font-weight:600;font-size:13px;margin-bottom:6px;color:var(--mute);">Choose Post to Promote</label>
              <select name="post_id" style="width:100%;padding:10px;background:var(--ink);color:var(--text);border:1px solid var(--line);border-radius:6px;">
                @foreach($userPosts as $up)
                  <option value="{{ $up->id }}">{{ $up->heading ?: ($up->caption ?: 'Post #' . $up->id) }} ({{ $up->type }})</option>
                @endforeach
              </select>
            </div>
            <div>
              <label style="display:block;font-weight:600;font-size:13px;margin-bottom:6px;color:var(--mute);">Choose Promotion Package</label>
              <select name="package_id" style="width:100%;padding:10px;background:var(--ink);color:var(--text);border:1px solid var(--line);border-radius:6px;">
                <optgroup label="Impression Packages (CPM Views)">
                  <option value="cpm_1k">1,000 Views — 500 Points</option>
                  <option value="cpm_5k">5,000 Views — 2,000 Points</option>
                  <option value="cpm_25k">25,000 Views — 8,000 Points</option>
                  <option value="cpm_100k">100,000 Views — 25,000 Points</option>
                </optgroup>
                <optgroup label="Spotlight Fixed Time Placements">
                  <option value="flat_1d">24 Hours Spotlight — 350 Points</option>
                  <option value="flat_7d">7 Days Spotlight — 2,000 Points</option>
                  <option value="flat_30d">30 Days Placement — 7,000 Points</option>
                </optgroup>
              </select>
            </div>
          </div>
          <button type="submit" class="btn btn-fill">{!! icon('flame') !!} Activate Boost with Points</button>
        </form>
      </div>
    @endif

    <h2 class="sec-title">How To Earn Points</h2>
    <div class="tiles tiles-3">
      <div class="tile" style="--c:#ff2bd6">{!! icon('crown') !!}<h3>Premium</h3><p>5,000 bonus points monthly ($15/yr donation)</p></div>
      <div class="tile" style="--c:#ffd60a">{!! icon('crown') !!}<h3>Premium+</h3><p>11,000 bonus points monthly ($30/yr donation)</p></div>
      <div class="tile" style="--c:#19e3ff">{!! icon('users') !!}<h3>Referral Program</h3><p>100 reward points for every invited creator</p></div>
    </div>

    <h2 class="sec-title">Impression Packages (CPM Views)</h2>
    <p class="sec-sub">Your post is distributed across Explore, Trending, and the front page until views are fulfilled.</p>
    <div class="packs">
      <div class="pack">
        <h3>Starter</h3>
        <p class="big">1,000 views</p>
        <p class="cost">500 pts</p>
        @auth
          <form method="POST" action="{{ route('advertising.purchase') }}">
            @csrf
            <input type="hidden" name="package_id" value="cpm_1k">
            <button type="submit" class="btn btn-line btn-wide">Activate</button>
          </form>
        @else
          <a class="btn btn-line btn-wide" href="{{ route('login') }}" data-modal="login">Sign in to buy</a>
        @endauth
      </div>

      <div class="pack">
        <h3>Boost</h3>
        <p class="big">5,000 views</p>
        <p class="cost">2,000 pts</p>
        @auth
          <form method="POST" action="{{ route('advertising.purchase') }}">
            @csrf
            <input type="hidden" name="package_id" value="cpm_5k">
            <button type="submit" class="btn btn-line btn-wide">Activate</button>
          </form>
        @else
          <a class="btn btn-line btn-wide" href="{{ route('login') }}" data-modal="login">Sign in to buy</a>
        @endauth
      </div>

      <div class="pack pack-hot">
        <span class="ribbon">Best Value</span>
        <h3>Popular</h3>
        <p class="big">25,000 views</p>
        <p class="cost">8,000 pts</p>
        @auth
          <form method="POST" action="{{ route('advertising.purchase') }}">
            @csrf
            <input type="hidden" name="package_id" value="cpm_25k">
            <button type="submit" class="btn btn-fill btn-wide">Activate</button>
          </form>
        @else
          <a class="btn btn-fill btn-wide" href="{{ route('login') }}" data-modal="login">Sign in to buy</a>
        @endauth
      </div>

      <div class="pack">
        <h3>Blast</h3>
        <p class="big">100,000 views</p>
        <p class="cost">25,000 pts</p>
        @auth
          <form method="POST" action="{{ route('advertising.purchase') }}">
            @csrf
            <input type="hidden" name="package_id" value="cpm_100k">
            <button type="submit" class="btn btn-line btn-wide">Activate</button>
          </form>
        @else
          <a class="btn btn-line btn-wide" href="{{ route('login') }}" data-modal="login">Sign in to buy</a>
        @endauth
      </div>
    </div>

    <h2 class="sec-title">Flat-Rate Time Placements</h2>
    <p class="sec-sub">Fixed-time promotional banners and spotlight slots on high-traffic discovery feeds.</p>
    <div class="tiles tiles-3">
      <div class="tile" style="--c:#ff2bd6">
        {!! icon('compass') !!}
        <h3>24 Hours Explore Spotlight</h3>
        <p>Featured spot at the top of the Explore page</p>
        <small style="color:var(--cyan);font-weight:700;">350 points</small>
      </div>

      <div class="tile" style="--c:#19e3ff">
        {!! icon('flame') !!}
        <h3>7 Days Explore Spotlight</h3>
        <p>Featured in top creator carousel for 1 full week</p>
        <small style="color:var(--cyan);font-weight:700;">2,000 points</small>
      </div>

      <div class="tile" style="--c:#ffd60a">
        {!! icon('star') !!}
        <h3>30 Days Creator Placement</h3>
        <p>Promoted across all feed sidebars and suggestions</p>
        <small style="color:var(--cyan);font-weight:700;">7,000 points</small>
      </div>
    </div>
  </main>

  @include('partials.footer')
@endsection
