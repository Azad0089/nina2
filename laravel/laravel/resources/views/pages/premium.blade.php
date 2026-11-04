@extends('layouts.app')

@section('title', 'Go Premium & Support | ' . site_name())
@section('description', 'Crypto donations keep Nina.Sucks running. Unlock a Premium verified badge and thousands of monthly promotion points.')

@section('content')
  @include('partials.header')

  <main class="page" id="main">
    @include('partials.page-hero', [
      'hero' => [
        'title' => 'Go Premium & Creator Support',
        'lead' => 'Crypto donations keep our servers fast and free of big-tech censorship. In return, you receive exclusive badges and monthly reward points to supercharge your blog views.',
        'color' => '#ffd60a'
      ]
    ])

    @if(isset($donationsEnabled) && !$donationsEnabled)
      <div class="panel" style="background:#f59e0b22;border:1px solid #f59e0b;color:#f59e0b;padding:16px;border-radius:8px;margin-bottom:24px;text-align:center;">
        <b>Notice:</b> Crypto donations and premium upgrades are currently paused by the platform administrator.
      </div>
    @else
      <div class="plans">
        <!-- Premium Tier -->
        <section class="plan" style="--c:#ff2bd6">
          <h2>Premium</h2>
          <p class="price"><span>Donate</span> $15<small> in crypto</small></p>
          <p class="pts">{!! badge('pro') !!} 5,000 points / month</p>
          <ul>
            <li>{!! icon('verified') !!} <b>Premium verified badge</b> for 1 full year</li>
            <li>{!! icon('verified') !!} <b>5,000 bonus points</b> every month</li>
            <li>{!! icon('verified') !!} Spend points to boost post views across Explore & Trending</li>
            <li>{!! icon('verified') !!} Priority support & unlimited blogs creation</li>
            <li>{!! icon('verified') !!} Renewable each year with another donation</li>
          </ul>

          <form method="POST" action="{{ route('premium.checkout') }}" style="margin-top:20px;">
            @csrf
            <input type="hidden" name="plan" value="premium">
            <div style="margin-bottom:12px;text-align:left;">
              <label style="display:block;font-size:12px;color:var(--mute);margin-bottom:4px;">Select Gateway</label>
              <select name="gateway" style="width:100%;padding:8px;background:var(--ink);color:var(--text);border:1px solid var(--line);border-radius:4px;">
                <option value="nowpayments">NOWPayments (BTC, ETH, USDT, LTC, XMR + 150 coins)</option>
                <option value="bitcart">BitCart Store</option>
              </select>
            </div>
            @auth
              <button type="submit" class="btn btn-fill btn-wide">Donate $15 with Crypto</button>
            @else
              <a class="btn btn-fill btn-wide" href="{{ route('login') }}" data-modal="login">Sign in to donate</a>
            @endauth
          </form>
        </section>

        <!-- Premium+ Tier -->
        <section class="plan plan-hot" style="--c:#ffd60a">
          <span class="ribbon">Best Value &middot; Most Points</span>
          <h2>Premium+</h2>
          <p class="price"><span>Donate</span> $30<small> in crypto</small></p>
          <p class="pts">{!! badge('pro') !!} 11,000 points / month</p>
          <ul>
            <li>{!! icon('verified') !!} <b>Premium verified badge</b> for 1 full year</li>
            <li>{!! icon('verified') !!} <b>11,000 bonus points</b> every month</li>
            <li>{!! icon('verified') !!} Massive post impressions in the Ad Marketplace</li>
            <li>{!! icon('verified') !!} Featured placements on Explore and Community spotlights</li>
            <li>{!! icon('verified') !!} Highest community priority</li>
          </ul>

          <form method="POST" action="{{ route('premium.checkout') }}" style="margin-top:20px;">
            @csrf
            <input type="hidden" name="plan" value="premium_plus">
            <div style="margin-bottom:12px;text-align:left;">
              <label style="display:block;font-size:12px;color:var(--mute);margin-bottom:4px;">Select Gateway</label>
              <select name="gateway" style="width:100%;padding:8px;background:var(--ink);color:var(--text);border:1px solid var(--line);border-radius:4px;">
                <option value="nowpayments">NOWPayments (BTC, ETH, USDT, LTC, XMR + 150 coins)</option>
                <option value="bitcart">BitCart Store</option>
              </select>
            </div>
            @auth
              <button type="submit" class="btn btn-fill btn-wide">Donate $30 with Crypto</button>
            @else
              <a class="btn btn-fill btn-wide" href="{{ route('login') }}" data-modal="login">Sign in to donate</a>
            @endauth
          </form>
        </section>
      </div>
    @endif

    <p class="fine center" style="margin-top:20px;">
      Payments are processed over 150+ cryptocurrencies via NOWPayments and BitCart. Upgrades and reward points are granted automatically upon blockchain confirmation.
    </p>

    <!-- Badges Showcase -->
    <h2 class="sec-title" style="margin-top:50px;">Three Types of Community Badges</h2>
    <p class="sec-sub">Users can hold any or all three badges simultaneously on their profile and blogs.</p>
    <div class="tiles tiles-3">
      <div class="tile" style="--c:#ffd60a">
        {!! badge('pro') !!}
        <h3>Premium Verified</h3>
        <p>Awarded to members who donate $15 or $30 in crypto. Active for one year and comes with thousands of monthly promotional points.</p>
      </div>

      <div class="tile" style="--c:#19e3ff">
        {!! badge('id') !!}
        <h3>Identity Verified</h3>
        <p>For creators who submit proof of identity in their dashboard. The badge is permanently attached to their account for life.</p>
      </div>

      <div class="tile" style="--c:#ff2bd6">
        {!! badge('vip') !!}
        <h3>VIP Member</h3>
        <p>Granted manually by site administration to top creators and outstanding contributors. Permanent lifetime recognition.</p>
      </div>
    </div>
  </main>

  @include('partials.footer')
@endsection
