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
        <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13px;">
          <thead>
            <tr style="border-bottom:1px solid var(--line);color:var(--mute);text-transform:uppercase;font-size:11px;letter-spacing:1px;">
              <th style="padding:10px;">Package</th>
              <th style="padding:10px;">Target (What Is Advertised)</th>
              <th style="padding:10px;">Where It Shows</th>
              <th style="padding:10px;">Progress (Delivered / Target)</th>
              <th style="padding:10px;">Status / Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($activePromotions as $promo)
              <tr style="border-bottom:1px solid var(--line);vertical-align:top;">
                <td style="padding:10px;">
                  <b>{{ $promo->package_name }}</b>
                  <div style="font-size:11px;color:var(--mute);margin-top:2px;">{{ number_format($promo->points_spent ?? 0) }} pts</div>
                </td>
                <td style="padding:10px;">
                  @if($promo->post)
                    <span style="color:var(--cyan);font-weight:700;">{!! icon('flame') !!} Boosted Post #{{ $promo->post->id }}</span>
                    <div style="font-size:12px;color:var(--text);margin-top:2px;">
                      {{ Str::limit($promo->post->heading ?: ($promo->post->caption ?: 'Post #' . $promo->post->id), 45) }}
                    </div>
                    <div style="margin-top:4px;">
                      <a href="{{ route('post.show', $promo->post->id) }}" target="_blank" style="color:var(--pink);text-decoration:underline;font-size:11px;">View Post &rarr;</a>
                    </div>
                  @elseif($promo->blog)
                    <span style="color:var(--pink);font-weight:700;">{!! icon('compass') !!} Blog Profile: &#64;{{ $promo->blog->slug }}</span>
                    <div style="margin-top:4px;">
                      <a href="{{ route('blog.show', $promo->blog->slug) }}" target="_blank" style="color:var(--cyan);text-decoration:underline;font-size:11px;">Open Profile &rarr;</a>
                    </div>
                  @elseif($promo->target_views > 0)
                    <!-- Unassigned Campaign Views -->
                    <div style="padding:8px 10px;background:rgba(255,43,214,0.1);border:1px dashed var(--pink);border-radius:6px;">
                      <span style="color:var(--yellow);font-weight:800;">
                        {!! icon('alert') !!} Unassigned Views ({{ number_format($promo->target_views) }} Views)
                      </span>
                      <p style="font-size:11px;color:var(--mute);margin:4px 0 6px;">Select which post to assign these impressions to:</p>
                      @if(isset($userPosts) && $userPosts->isNotEmpty())
                        <form method="POST" action="{{ route('advertising.assignPost') }}" style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
                          @csrf
                          <input type="hidden" name="promo_id" value="{{ $promo->id }}">
                          <select name="post_id" required style="padding:6px 8px;background:var(--ink);color:var(--text);border:1px solid var(--line);border-radius:6px;font-size:12px;">
                            <option value="">-- Choose Post --</option>
                            @foreach($userPosts as $up)
                              <option value="{{ $up->id }}">Post #{{ $up->id }}: {{ Str::limit($up->heading ?: ($up->caption ?: 'Post #' . $up->id), 35) }}</option>
                            @endforeach
                          </select>
                          <button type="submit" class="btn btn-fill btn-xs" style="padding:6px 12px;font-size:11px;">Link Post Now</button>
                        </form>
                      @endif
                      <form method="POST" action="{{ route('advertising.refund') }}" style="margin-top:6px;">
                        @csrf
                        <input type="hidden" name="promo_id" value="{{ $promo->id }}">
                        <button type="submit" class="btn btn-ghost btn-xs" style="color:#fca5a5;font-size:10px;padding:3px 8px;" onclick="return confirm('Refund points for this campaign?')">Refund Points</button>
                      </form>
                    </div>
                  @else
                    <span style="color:var(--yellow);font-weight:700;">Blog Profile Spotlight</span>
                  @endif
                </td>
                <td style="padding:10px;color:var(--mute);font-size:12px;">
                  @if($promo->post_id)
                    <span style="color:var(--cyan);font-weight:600;">Explore &amp; Home Feeds</span> (Top Pinned with ⚡ Boosted badge)
                  @else
                    <span style="color:var(--pink);font-weight:600;">Featured Blogs</span> (Top Creator Carousel)
                  @endif
                </td>
                <td style="padding:10px;">
                  @if($promo->target_views > 0)
                    <b>{{ number_format($promo->current_views ?? 0) }}</b> / {{ number_format($promo->target_views) }} views
                    <div style="width:100%;max-width:140px;height:4px;background:rgba(255,255,255,0.1);border-radius:2px;margin-top:5px;overflow:hidden;">
                      <div style="height:100%;background:var(--cyan);width:{{ min(100, round((($promo->current_views ?? 0) / max(1, $promo->target_views)) * 100)) }}%;"></div>
                    </div>
                  @else
                    <b>{{ $promo->duration_days }} Days Fixed Spotlight</b>
                    @if($promo->expires_at)
                      <div style="font-size:11px;margin-top:4px;color:{{ $promo->is_expired ? 'var(--mute)' : 'var(--cyan)' }};">
                        {{ $promo->is_expired ? 'Ended ' . $promo->expires_at->diffForHumans() : 'Ends ' . $promo->expires_at->diffForHumans() }}
                      </div>
                    @endif
                  @endif
                </td>
                <td style="padding:10px;">
                  @php
                    $isEnded = $promo->is_expired || $promo->status === 'completed';
                  @endphp
                  @if($isEnded)
                    <span style="color:#94a3b8;font-weight:700;padding:2px 8px;border-radius:4px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);font-size:11px;">
                      COMPLETED
                    </span>
                  @elseif($promo->status === 'cancelled')
                    <span style="color:#f87171;font-weight:700;padding:2px 8px;border-radius:4px;background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);font-size:11px;">
                      CANCELLED
                    </span>
                  @else
                    <span style="color:#10b981;font-weight:700;padding:2px 8px;border-radius:4px;background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);font-size:11px;">
                      ACTIVE
                    </span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

    <!-- Purchase Promotion Form -->
    @if(auth()->check())
      <div id="buy-form" class="panel" style="margin-bottom:36px;background:var(--paper);border:1px solid var(--line);border-radius:10px;padding:24px;transition:outline .3s;">
        <h2 style="font:800 20px var(--f-display);margin-bottom:6px;display:flex;align-items:center;gap:8px;">
          <span style="color:var(--pink);">{!! icon('flame') !!}</span>
          <span>Activate Promotion with Points</span>
        </h2>
        <p style="color:var(--mute);font-size:13px;margin-bottom:16px;">
          Select your package and choose whether to boost an individual post (CPM Views) or spotlight your entire blog profile (Featured Carousel).
        </p>
        <form method="POST" action="{{ route('advertising.purchase') }}">
          @csrf
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
            <div>
              <label style="display:block;font-weight:600;font-size:13px;margin-bottom:6px;color:var(--mute);">1. Choose Target (Specific Post or Blog Profile)</label>
              <select name="target" id="target-select" style="width:100%;padding:10px;background:var(--ink);color:var(--text);border:1px solid var(--line);border-radius:6px;">
                @if(isset($userPosts) && $userPosts->isNotEmpty())
                  <optgroup label="Boost Specific Post (Target CPM Impression Views)">
                    @foreach($userPosts as $up)
                      <option value="post_{{ $up->id }}">Post #{{ $up->id }}: {{ Str::limit($up->heading ?: ($up->caption ?: 'Post #' . $up->id), 40) }}</option>
                    @endforeach
                  </optgroup>
                @endif
                @if(isset($userBlogs) && $userBlogs->isNotEmpty())
                  <optgroup label="Blog Profile (Spotlight in Featured Blogs Listing)">
                    @foreach($userBlogs as $ub)
                      <option value="blog_{{ $ub->id }}">Promote Blog: &#64;{{ $ub->slug }} ({{ $ub->title }})</option>
                    @endforeach
                  </optgroup>
                @endif
              </select>
              <small id="target-hint" style="display:block;color:var(--cyan);margin-top:6px;font-size:12px;">
                <b>Individual Post</b>: Injected into Explore and Trending feeds with a neon <i>⚡ Boosted</i> badge until views are fulfilled.
              </small>
            </div>
            <div>
              <label style="display:block;font-weight:600;font-size:13px;margin-bottom:6px;color:var(--mute);">2. Choose Promotion Package</label>
              <select name="package_id" id="package-select" onchange="handlePackageChange(this.value)" style="width:100%;padding:10px;background:var(--ink);color:var(--text);border:1px solid var(--line);border-radius:6px;">
                <optgroup label="Impression Packages (CPM Post Views)">
                  @foreach($cpmPackages as $cp)
                    <option value="{{ $cp['id'] }}">{{ $cp['name'] }} — {{ number_format($cp['points']) }} Points</option>
                  @endforeach
                </optgroup>
                <optgroup label="Spotlight Fixed Time Placements">
                  @foreach($flatPackages as $fp)
                    <option value="{{ $fp['id'] }}">{{ $fp['name'] }} — {{ number_format($fp['points']) }} Points</option>
                  @endforeach
                </optgroup>
              </select>
            </div>
          </div>
          <button type="submit" class="btn btn-fill">{!! icon('flame') !!} Activate Boost with Points</button>
        </form>
      </div>
    @endif

    <!-- Explanatory Guide: How Advertising Works & Where Ads Appear -->
    <div style="margin-bottom:36px;background:rgba(255,255,255,0.02);border:1px solid var(--line);border-radius:12px;padding:24px;">
      <h2 style="font:800 20px var(--f-display);color:#fff;margin-bottom:6px;display:flex;align-items:center;gap:8px;">
        <span style="color:var(--yellow);">{!! icon('compass') !!}</span>
        <span>How Advertising Works &amp; Where Your Ads Appear</span>
      </h2>
      <p style="color:var(--mute);font-size:13.5px;margin-bottom:20px;">
        Nina.Sucks provides three distinct promotion channels. Here is what is promoted, where it appears, and how views are delivered:
      </p>

      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:18px;">
        <div style="background:var(--ink);border:1px solid rgba(25,227,255,0.3);border-radius:10px;padding:18px;">
          <div style="display:flex;align-items:center;gap:8px;color:var(--cyan);font-weight:800;font-size:15px;margin-bottom:8px;">
            {!! icon('flame') !!}
            <span>1. Post Impression Boosts (CPM)</span>
          </div>
          <ul style="font-size:12.5px;color:var(--text);display:flex;flex-direction:column;gap:8px;padding-left:0;line-height:1.5;">
            <li><b>What is advertised:</b> Any individual post you selected.</li>
            <li><b>Where it shows:</b> Pinned at the top of the <b>Explore Feed</b> and <b>Home Feed</b> with a bright <span style="color:#19e3ff;font-weight:700;">⚡ Boosted</span> badge.</li>
            <li><b>Delivery:</b> Real impressions are tracked live every time a visitor views the feed until your purchased views (1,000 to 100,000) are delivered.</li>
          </ul>
        </div>

        <div style="background:var(--ink);border:1px solid rgba(255,43,214,0.3);border-radius:10px;padding:18px;">
          <div style="display:flex;align-items:center;gap:8px;color:var(--pink);font-weight:800;font-size:15px;margin-bottom:8px;">
            {!! icon('compass') !!}
            <span>2. Blog Profile Spotlight (Flat Rate)</span>
          </div>
          <ul style="font-size:12.5px;color:var(--text);display:flex;flex-direction:column;gap:8px;padding-left:0;line-height:1.5;">
            <li><b>What is advertised:</b> Your creator blog profile (&#64;username).</li>
            <li><b>Where it shows:</b> In the <b>Featured Blogs</b> carousel at the top of Home and Explore.</li>
            <li><b>Delivery:</b> Stays pinned for the full duration (24 Hours, 7 Days, or 30 Days) to drive profile clicks and followers.</li>
          </ul>
        </div>

        <div style="background:var(--ink);border:1px solid rgba(255,214,10,0.3);border-radius:10px;padding:18px;">
          <div style="display:flex;align-items:center;gap:8px;color:var(--yellow);font-weight:800;font-size:15px;margin-bottom:8px;">
            {!! icon('megaphone') !!}
            <span>3. Sponsored Commercial Ads</span>
          </div>
          <ul style="font-size:12.5px;color:var(--text);display:flex;flex-direction:column;gap:8px;padding-left:0;line-height:1.5;">
            <li><b>What is advertised:</b> Custom branded campaigns, sponsor links, or product banners.</li>
            <li><b>Where it shows:</b> In the <b>Sponsored Ads</b> sidebar box on Explore and feed banners.</li>
            <li><b>Delivery:</b> Displayed sitewide with automatic click and impression analytics.</li>
          </ul>
        </div>
      </div>
    </div>

    <h2 class="sec-title">How To Earn Points</h2>
    <div class="tiles tiles-3" style="margin-bottom:30px;">
      <div class="tile" style="--c:#ff2bd6">{!! icon('crown') !!}<h3>Premium</h3><p>{{ number_format(\App\Models\Setting::get('premium_points_monthly', 5000)) }} bonus points monthly</p></div>
      <div class="tile" style="--c:#ffd60a">{!! icon('crown') !!}<h3>Premium+</h3><p>{{ number_format(\App\Models\Setting::get('premium_plus_points_monthly', 11000)) }} bonus points monthly</p></div>
      <div class="tile" style="--c:#19e3ff">{!! icon('users') !!}<h3>Referral Program</h3><p>{{ number_format(\App\Models\Setting::get('referral_reward_points', 100)) }} reward points per member</p></div>
    </div>

    <h2 class="sec-title">Impression Packages (CPM Views)</h2>
    <p class="sec-sub">Select your target post above, or choose an impression tier below to amplify views in the Explore feed.</p>
    <div class="packs" style="margin-bottom:36px;">
      @foreach($cpmPackages as $pkg)
        <div class="pack {{ $pkg['id'] === 'cpm_25k' ? 'pack-hot' : '' }}">
          @if($pkg['id'] === 'cpm_25k')
            <span class="ribbon">Best Value</span>
          @endif
          <h3>{{ $pkg['name'] }}</h3>
          <p class="big">{{ number_format($pkg['views']) }} views</p>
          <p class="cost">{{ number_format($pkg['points']) }} pts</p>
          <small style="color:var(--mute);display:block;margin-bottom:12px;">Top of feed impression boost</small>
          @auth
            <button type="button" class="btn {{ $pkg['id'] === 'cpm_25k' ? 'btn-fill' : 'btn-line' }} btn-wide" onclick="selectPackage('{{ $pkg['id'] }}', 'post')">Select Package</button>
          @else
            <a class="btn btn-line btn-wide" href="{{ route('login') }}" data-modal="login">Sign in to buy</a>
          @endauth
        </div>
      @endforeach
    </div>

    <h2 class="sec-title">Flat-Rate Spotlight Placements</h2>
    <p class="sec-sub">Fixed-time promotional slots in the top Featured Blogs showcase.</p>
    <div class="tiles tiles-3">
      @foreach($flatPackages as $fp)
        <div class="tile" style="--c:{{ $fp['days'] === 1 ? '#ff2bd6' : ($fp['days'] === 7 ? '#19e3ff' : '#ffd60a') }}">
          {!! icon($fp['days'] === 1 ? 'compass' : ($fp['days'] === 7 ? 'flame' : 'star')) !!}
          <h3>{{ $fp['name'] }}</h3>
          <p>Featured in top creator carousel for {{ $fp['days'] }} {{ $fp['days'] === 1 ? 'day' : 'days' }}.</p>
          <small style="color:var(--cyan);font-weight:700;display:block;margin-bottom:12px;">{{ number_format($fp['points']) }} points</small>
          @auth
            <button type="button" class="btn btn-xs btn-fill" style="width:100%;" onclick="selectPackage('{{ $fp['id'] }}', 'blog')">Select Package</button>
          @else
            <a class="btn btn-xs btn-line" href="{{ route('login') }}" data-modal="login" style="width:100%;text-align:center;">Sign in to book</a>
          @endauth
        </div>
      @endforeach
    </div>

    <script>
      function selectPackage(pkgId, preferredType) {
        const pkgSelect = document.getElementById('package-select');
        const targetSelect = document.getElementById('target-select');
        if (pkgSelect) {
          pkgSelect.value = pkgId;
          handlePackageChange(pkgId);
        }
        if (targetSelect && preferredType === 'post') {
          const postOpt = targetSelect.querySelector('optgroup[label*="Post"] option');
          if (postOpt) targetSelect.value = postOpt.value;
        } else if (targetSelect && preferredType === 'blog') {
          const blogOpt = targetSelect.querySelector('optgroup[label*="Blog"] option');
          if (blogOpt) targetSelect.value = blogOpt.value;
        }
        const formEl = document.getElementById('buy-form');
        if (formEl) {
          formEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
          formEl.style.outline = '2px solid var(--cyan)';
          setTimeout(() => { formEl.style.outline = 'none'; }, 2000);
        }
      }

      function handlePackageChange(pkgId) {
        const isCpm = String(pkgId).startsWith('cpm_');
        const hint = document.getElementById('target-hint');
        if (hint) {
          if (isCpm) {
            hint.innerHTML = '<b>Individual Post</b>: Injected into Explore and Trending feeds with a neon <i>⚡ Boosted</i> badge until views are fulfilled.';
            hint.style.color = 'var(--cyan)';
          } else {
            hint.innerHTML = '<b>Blog Profile</b>: Displayed in the top <i>Featured Blogs</i> showcase on Home and Explore.';
            hint.style.color = 'var(--pink)';
          }
        }
      }
    </script>
  </main>
@endsection
