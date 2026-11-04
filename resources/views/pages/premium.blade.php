@extends('layouts.app')

@section('title', 'Go Premium with Crypto | ' . site_name())
@section('description', 'Direct cryptocurrency subscriptions powered by NOWPayments. Get an instant PRO verified badge and monthly points to amplify your reach.')

@section('content')
  @include('partials.header')

  <main class="page" id="main">
    @include('partials.page-hero', [
      'hero' => [
        'title' => 'Go Premium with Crypto',
        'lead' => 'Direct cryptocurrency subscriptions powered by NOWPayments. Get an instant PRO verified badge and monthly points to amplify your reach.',
        'color' => '#ffd60a'
      ]
    ])

    <!-- Status / Alert Banners -->
    @if(isset($success) && $success)
      <div class="panel" style="background:rgba(34,197,94,0.15);border:1px solid #22c55e;color:#22c55e;padding:20px;border-radius:12px;margin-bottom:28px;text-align:center;box-shadow:0 0 25px rgba(34,197,94,0.2);">
        <div style="font-size:28px;margin-bottom:6px;">🎉 💎</div>
        <h3 style="margin:0 0 6px;color:#fff;font-size:18px;font-weight:800;">Subscription Activated!</h3>
        <p style="margin:0;font-size:14px;color:#86efac;">
          Your crypto payment has been confirmed! You now have the <b style="color:#fff;">{!! badge('pro') !!} PRO verified badge</b>, lifetime blog prestige, and monthly promotion points.
        </p>
        @if(!empty($orderId))
          <div style="margin-top:10px;font-family:monospace;font-size:12px;color:#cbd5e1;">Order Reference: <b>{{ $orderId }}</b></div>
        @endif
        <div style="margin-top:14px;">
          <a href="{{ route('dashboard') }}" class="btn btn-fill btn-sm" style="background:#22c55e;color:#07030d;font-weight:800;">Go to Creator Studio</a>
        </div>
      </div>
    @elseif(isset($activeDonation) && $activeDonation && in_array($activeDonation->status, ['pending', 'waiting', 'confirming', 'sending']))
      <!-- Live Blockchain Transaction Tracker -->
      <div class="panel" id="liveOrderTracker" style="background:rgba(25,227,255,0.08);border:1px solid rgba(25,227,255,0.4);border-radius:12px;padding:20px;margin-bottom:28px;box-shadow:0 0 30px rgba(25,227,255,0.15);">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;border-bottom:1px solid rgba(255,255,255,0.1);padding-bottom:12px;margin-bottom:14px;">
          <div style="display:flex;align-items:center;gap:10px;">
            <span style="font-size:22px;">⏳</span>
            <div>
              <h4 style="margin:0;color:#fff;font-size:15px;font-weight:800;">Awaiting Crypto Payment</h4>
              <span style="font-size:12px;color:var(--mute);">Order <code style="color:var(--cyan);">{{ $activeDonation->order_id }}</code> &bull; {{ $activeDonation->plan === 'premium_plus' ? 'Premium+ ($30)' : 'Premium ($15)' }}</span>
            </div>
          </div>
          <div>
            <span id="trackerStatusBadge" style="padding:4px 12px;border-radius:999px;font-size:12px;font-weight:800;background:rgba(255,214,10,0.2);color:#ffd60a;border:1px solid rgba(255,214,10,0.4);text-transform:uppercase;">
              {{ $activeDonation->status }}
            </span>
          </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;">
          <p style="margin:0;font-size:13px;color:#cbd5e1;max-width:550px;">
            We are monitoring the blockchain via NOWPayments. Once network confirmations are detected, your PRO badge and bonus points will be applied automatically!
          </p>
          <div style="display:flex;gap:10px;flex-wrap:wrap;">
            @if(!empty($activeDonation->pay_address) && str_starts_with($activeDonation->pay_address, 'http'))
              <a href="{{ $activeDonation->pay_address }}" target="_blank" class="btn btn-fill btn-sm" style="background:var(--pink);color:#fff;font-weight:700;">
                Open NOWPayments Portal &rarr;
              </a>
            @endif
            <button type="button" onclick="pollOrderStatus('{{ $activeDonation->order_id }}')" class="btn btn-line btn-sm" id="btnManualPoll">
              Check Status Now
            </button>
          </div>
        </div>
      </div>
    @elseif(isset($canceled) && $canceled)
      <div class="panel" style="background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.4);color:#fca5a5;padding:16px;border-radius:8px;margin-bottom:24px;text-align:center;">
        Transaction cancelled or timed out. You can restart your crypto donation at any time below.
      </div>
    @elseif(session('error'))
      <div class="panel" style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.4);color:#fca5a5;padding:16px;border-radius:8px;margin-bottom:24px;text-align:center;">
        {{ session('error') }}
      </div>
    @endif

    <!-- Gateway Trust Banner -->
    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:14px 20px;margin-bottom:28px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;">
      <div style="display:flex;align-items:center;gap:12px;">
        <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#ff2bd6,#19e3ff);display:flex;align-items:center;justify-content:center;font-size:18px;">⚡</div>
        <div>
          <div style="font-size:13px;font-weight:800;color:#fff;">Official Gateway: NOWPayments.io</div>
          <div style="font-size:11px;color:var(--mute);">Non-custodial, zero chargebacks, 150+ supported cryptocurrencies</div>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:8px;font-size:11px;font-weight:700;color:var(--mute);flex-wrap:wrap;">
        <span style="background:rgba(255,255,255,0.06);padding:3px 8px;border-radius:6px;color:#f59e0b;">₿ BTC</span>
        <span style="background:rgba(255,255,255,0.06);padding:3px 8px;border-radius:6px;color:#60a5fa;">Ξ ETH</span>
        <span style="background:rgba(255,255,255,0.06);padding:3px 8px;border-radius:6px;color:#34d399;">₮ USDT (TRC20)</span>
        <span style="background:rgba(255,255,255,0.06);padding:3px 8px;border-radius:6px;color:#a78bfa;">◎ SOL</span>
        <span style="background:rgba(255,255,255,0.06);padding:3px 8px;border-radius:6px;color:#cbd5e1;">Ł LTC</span>
        <span style="background:rgba(255,255,255,0.06);padding:3px 8px;border-radius:6px;color:#fbbf24;">Ð DOGE</span>
        <span style="background:rgba(255,255,255,0.06);padding:3px 8px;border-radius:6px;color:#f97316;">ɱ XMR</span>
        <span style="background:rgba(255,43,214,0.15);padding:3px 8px;border-radius:6px;color:#ff2bd6;">+150 Coins</span>
      </div>
    </div>

    @if(isset($donationsEnabled) && !$donationsEnabled)
      <div class="panel" style="background:#f59e0b22;border:1px solid #f59e0b;color:#f59e0b;padding:16px;border-radius:8px;margin-bottom:24px;text-align:center;">
        <b>Notice:</b> Crypto donations and premium upgrades are currently paused by the platform administrator.
      </div>
    @else
@php
  $bitcartVal = $settings['bitcart_enabled'] ?? ($siteSettings['bitcart_enabled'] ?? \App\Models\Setting::get('bitcart_enabled', '0'));
  $bitcartEnabled = !empty($bitcartVal) && $bitcartVal !== '0' && $bitcartVal !== false && $bitcartVal !== 'false';

  $qrVal = $settings['qr_code_enabled'] ?? ($siteSettings['qr_code_enabled'] ?? \App\Models\Setting::get('qr_code_enabled', '0'));
  $qrCodeEnabled = !empty($qrVal) && $qrVal !== '0' && $qrVal !== false && $qrVal !== 'false';

  $premiumPriceUsd = $settings['premium_price_usd'] ?? ($siteSettings['premium_price_usd'] ?? \App\Models\Setting::get('premium_price_usd', 15));
  $premiumPlusPriceUsd = $settings['premium_plus_price_usd'] ?? ($siteSettings['premium_plus_price_usd'] ?? \App\Models\Setting::get('premium_plus_price_usd', 30));

  $premiumPtsRaw = $settings['premium_points_monthly'] ?? ($siteSettings['premium_points_monthly'] ?? \App\Models\Setting::get('premium_points_monthly', 5000));
  $premiumPlusPtsRaw = $settings['premium_plus_points_monthly'] ?? ($siteSettings['premium_plus_points_monthly'] ?? \App\Models\Setting::get('premium_plus_points_monthly', 11000));

  $premiumPts = number_format(is_numeric($premiumPtsRaw) ? (int)$premiumPtsRaw : 5000);
  $premiumPlusPts = number_format(is_numeric($premiumPlusPtsRaw) ? (int)$premiumPlusPtsRaw : 11000);

  $userPlan = auth()->check() ? auth()->user()->plan : 'free';
  $userSubscriptionExpiresAt = auth()->check() ? auth()->user()->subscription_expires_at : null;
@endphp

    @auth
      @if($userPlan === 'premium_plus')
        <div class="panel" style="background:rgba(255,214,10,0.12);border:1px solid #ffd60a;border-radius:12px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
          <div style="display:flex;align-items:center;gap:12px;">
            <span style="font-size:28px;">👑</span>
            <div>
              <div style="font-size:15px;font-weight:900;color:#ffd60a;">You have an active Premium+ (VIP) Membership!</div>
              <div style="font-size:12px;color:#cbd5e1;">
                You hold the highest subscription tier with VIP badges and {{ $premiumPlusPts }} monthly promotional points.
                @if($userSubscriptionExpiresAt)
                  Active until <b>{{ $userSubscriptionExpiresAt->format('M d, Y') }}</b> ({{ $userSubscriptionExpiresAt->diffForHumans() }}).
                @endif
              </div>
            </div>
          </div>
          <span style="padding:6px 14px;border-radius:999px;font-size:12px;font-weight:800;background:rgba(255,214,10,0.2);color:#ffd60a;border:1px solid #ffd60a;text-transform:uppercase;">
            Active VIP Tier
          </span>
        </div>
      @elseif($userPlan === 'premium')
        <div class="panel" style="background:rgba(255,43,214,0.12);border:1px solid #ff2bd6;border-radius:12px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
          <div style="display:flex;align-items:center;gap:12px;">
            <span style="font-size:28px;">💎</span>
            <div>
              <div style="font-size:15px;font-weight:900;color:#ff2bd6;">You have an active Premium Membership!</div>
              <div style="font-size:12px;color:#cbd5e1;">
                You hold the PRO badge with {{ $premiumPts }} monthly points.
                @if($userSubscriptionExpiresAt)
                  Active until <b>{{ $userSubscriptionExpiresAt->format('M d, Y') }}</b> ({{ $userSubscriptionExpiresAt->diffForHumans() }}).
                @endif
                You cannot re-order Premium while active, but you can upgrade to Premium+ ($30 VIP) below!
              </div>
            </div>
          </div>
          <span style="padding:6px 14px;border-radius:999px;font-size:12px;font-weight:800;background:rgba(255,43,214,0.2);color:#ff2bd6;border:1px solid #ff2bd6;text-transform:uppercase;">
            Active PRO Tier
          </span>
        </div>
      @endif
    @endauth

      <div class="plans">
        <!-- PLAN 1: PREMIUM -->
        <section class="plan" style="--c:#ff2bd6">
          <h2>Premium</h2>
          <p class="price"><span>Donate</span> ${{ $premiumPriceUsd }}<small> in crypto / year</small></p>
          <p class="pts">{!! badge('pro') !!} {{ $premiumPts }} points / month</p>
          <ul>
            <li>{!! icon('verified') !!} <b>Premium verified badge</b> on profile &amp; blogs</li>
            <li>{!! icon('verified') !!} <b>{{ $premiumPts }} bonus points</b> credited every month</li>
            <li>{!! icon('verified') !!} Spend points to boost post views across Explore &amp; Trending</li>
            <li>{!! icon('verified') !!} High priority in search &amp; explore recommendations</li>
            <li>{!! icon('verified') !!} Direct crypto payment via {{ $bitcartEnabled ? 'BitCart or NOWPayments' : 'NOWPayments' }}</li>
          </ul>

          <form method="POST" action="{{ route('premium.checkout') }}" style="margin-top:20px;">
            @csrf
            <input type="hidden" name="plan" value="premium">
            
            <div style="margin-bottom:12px;text-align:left;">
              @if($bitcartEnabled)
                <label style="display:block;font-size:11px;text-transform:uppercase;font-weight:700;color:var(--mute);margin-bottom:4px;">Select Gateway</label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
                  <label style="display:flex;align-items:center;gap:6px;padding:8px 10px;background:rgba(255,255,255,0.03);border:1px solid var(--line);border-radius:6px;cursor:pointer;font-size:12px;font-weight:700;">
                    <input type="radio" name="gateway" value="bitcart" style="accent-color:var(--pink);">
                    <span>🛒 BitCart (0% Fee)</span>
                  </label>
                  <label style="display:flex;align-items:center;gap:6px;padding:8px 10px;background:rgba(255,255,255,0.03);border:1px solid var(--line);border-radius:6px;cursor:pointer;font-size:12px;font-weight:700;">
                    <input type="radio" name="gateway" value="nowpayments" checked style="accent-color:var(--pink);">
                    <span>⚡ NOWPayments</span>
                  </label>
                </div>
              @else
                <input type="hidden" name="gateway" value="nowpayments">
              @endif

              <label style="display:block;font-size:11px;text-transform:uppercase;font-weight:700;color:var(--mute);margin-bottom:4px;">Preferred Crypto (NOWPayments)</label>
              <select name="pay_currency" style="width:100%;padding:9px 10px;background:var(--ink);color:var(--text);border:1px solid var(--line);border-radius:6px;font-size:13px;outline:none;">
                <option value="">Auto (Choose on invoice)</option>
                <option value="usdttrc20" selected>USDT (TRC-20) &bull; Lowest Fees</option>
                <option value="btc">Bitcoin (BTC)</option>
                <option value="eth">Ethereum (ETH)</option>
                <option value="ltc">Litecoin (LTC)</option>
                <option value="sol">Solana (SOL)</option>
                <option value="doge">Dogecoin (DOGE)</option>
              </select>
            </div>

            @auth
              @if($userPlan === 'premium_plus')
                <div style="padding:14px;border-radius:8px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.12);color:#cbd5e1;text-align:center;">
                  <div style="font-weight:800;font-size:13px;color:#94a3b8;margin-bottom:4px;">✓ Included in Premium+</div>
                  <div style="font-size:11px;color:var(--mute);">You hold the top tier VIP membership with all Premium perks included.</div>
                </div>
              @elseif($userPlan === 'premium')
                <div style="padding:14px;border-radius:8px;background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.4);color:#86efac;text-align:center;">
                  <div style="font-weight:800;font-size:13px;color:#22c55e;margin-bottom:4px;">✓ Current Active Plan</div>
                  <div style="font-size:11px;color:#cbd5e1;">You already have Premium ($15 PRO). To upgrade to VIP and get 11,000 pts/mo, select Premium+ on the right.</div>
                </div>
              @else
                <button class="btn btn-fill btn-wide" type="submit" style="background:linear-gradient(135deg,#ff2bd6,#d946ef);font-weight:800;font-size:14px;box-shadow:0 4px 15px rgba(255,43,214,0.35);">
                  🚀 Checkout ${{ $premiumPriceUsd }} Subscription
                </button>
                @if($qrCodeEnabled)
                  <div style="margin-top:10px;display:flex;justify-content:center;gap:12px;">
                    <button type="button" onclick="openDirectPaymentModal('premium', {{ $premiumPriceUsd }})" style="background:transparent;border:none;color:var(--cyan);font-size:12px;cursor:pointer;text-decoration:underline;">
                      Or pay with direct QR Code
                    </button>
                  </div>
                @endif
              @endif
            @else
              <a class="btn btn-fill btn-wide" href="{{ route('login') }}" data-modal="login">Sign in to donate</a>
            @endauth
          </form>
        </section>

        <!-- PLAN 2: PREMIUM+ ($30) -->
        <section class="plan plan-hot" style="--c:#ffd60a">
          <span class="ribbon">Best Value &middot; Most Points</span>
          <h2>Premium+</h2>
          <p class="price"><span>Donate</span> ${{ $premiumPlusPriceUsd }}<small> in crypto / year</small></p>
          <p class="pts">{!! badge('pro') !!} {{ $premiumPlusPts }} points / month</p>
          <ul>
            <li>{!! icon('verified') !!} <b>Premium verified badge</b> on profile &amp; all blogs</li>
            <li>{!! icon('verified') !!} <b>{{ $premiumPlusPts }} bonus points</b> every month (2.2x bonus!)</li>
            <li>{!! icon('verified') !!} Pinned spot on homepage discover carousel</li>
            <li>{!! icon('verified') !!} Custom theme accents &amp; highlight glow</li>
            <li>{!! icon('verified') !!} Direct crypto payment via {{ $bitcartEnabled ? 'BitCart or NOWPayments' : 'NOWPayments' }}</li>
          </ul>

          <form method="POST" action="{{ route('premium.checkout') }}" style="margin-top:20px;">
            @csrf
            <input type="hidden" name="plan" value="premium_plus">
            
            <div style="margin-bottom:12px;text-align:left;">
              @if($bitcartEnabled)
                <label style="display:block;font-size:11px;text-transform:uppercase;font-weight:700;color:var(--mute);margin-bottom:4px;">Select Gateway</label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
                  <label style="display:flex;align-items:center;gap:6px;padding:8px 10px;background:rgba(255,255,255,0.03);border:1px solid var(--line);border-radius:6px;cursor:pointer;font-size:12px;font-weight:700;">
                    <input type="radio" name="gateway" value="bitcart" style="accent-color:var(--pink);">
                    <span>🛒 BitCart (0% Fee)</span>
                  </label>
                  <label style="display:flex;align-items:center;gap:6px;padding:8px 10px;background:rgba(255,255,255,0.03);border:1px solid var(--line);border-radius:6px;cursor:pointer;font-size:12px;font-weight:700;">
                    <input type="radio" name="gateway" value="nowpayments" checked style="accent-color:var(--pink);">
                    <span>⚡ NOWPayments</span>
                  </label>
                </div>
              @else
                <input type="hidden" name="gateway" value="nowpayments">
              @endif

              <label style="display:block;font-size:11px;text-transform:uppercase;font-weight:700;color:var(--mute);margin-bottom:4px;">Preferred Crypto (NOWPayments)</label>
              <select name="pay_currency" style="width:100%;padding:9px 10px;background:var(--ink);color:var(--text);border:1px solid var(--line);border-radius:6px;font-size:13px;outline:none;">
                <option value="">Auto (Choose on invoice)</option>
                <option value="usdttrc20" selected>USDT (TRC-20) &bull; Lowest Fees</option>
                <option value="btc">Bitcoin (BTC)</option>
                <option value="eth">Ethereum (ETH)</option>
                <option value="ltc">Litecoin (LTC)</option>
                <option value="sol">Solana (SOL)</option>
                <option value="doge">Dogecoin (DOGE)</option>
              </select>
            </div>

            @auth
              @if($userPlan === 'premium_plus')
                <div style="padding:14px;border-radius:8px;background:rgba(255,214,10,0.12);border:1px solid rgba(255,214,10,0.4);color:#ffd60a;text-align:center;">
                  <div style="font-weight:900;font-size:14px;color:#ffd60a;margin-bottom:4px;">★ Current Active Tier (VIP)</div>
                  <div style="font-size:11px;color:#cbd5e1;">You are already at the highest tier! Your VIP perks and 11,000 monthly points are active for 1 year.</div>
                </div>
              @elseif($userPlan === 'premium')
                <button class="btn btn-fill btn-wide" type="submit" style="background:linear-gradient(135deg,#ffd60a,#f59e0b);color:#07030d;font-weight:900;font-size:14px;box-shadow:0 4px 18px rgba(255,214,10,0.35);">
                  ⭐ Upgrade to Premium+ (${{ $premiumPlusPriceUsd }} VIP)
                </button>
                @if($qrCodeEnabled)
                  <div style="margin-top:10px;display:flex;justify-content:center;gap:12px;">
                    <button type="button" onclick="openDirectPaymentModal('premium_plus', {{ $premiumPlusPriceUsd }})" style="background:transparent;border:none;color:var(--pink);font-size:12px;cursor:pointer;text-decoration:underline;">
                      Or pay with direct QR Code
                    </button>
                  </div>
                @endif
              @else
                <button class="btn btn-fill btn-wide" type="submit" style="background:linear-gradient(135deg,#ffd60a,#f59e0b);color:#07030d;font-weight:900;font-size:14px;box-shadow:0 4px 18px rgba(255,214,10,0.35);">
                  🚀 Checkout ${{ $premiumPlusPriceUsd }} Subscription
                </button>
                @if($qrCodeEnabled)
                  <div style="margin-top:10px;display:flex;justify-content:center;gap:12px;">
                    <button type="button" onclick="openDirectPaymentModal('premium_plus', {{ $premiumPlusPriceUsd }})" style="background:transparent;border:none;color:var(--pink);font-size:12px;cursor:pointer;text-decoration:underline;">
                      Or pay with direct QR Code
                    </button>
                  </div>
                @endif
              @endif
            @else
              <a class="btn btn-fill btn-wide" href="{{ route('login') }}" data-modal="login">Sign in to donate</a>
            @endauth
          </form>
        </section>
      </div>
    @endif

    <p class="fine center" style="margin-top:20px;">
      Payments are processed securely via NOWPayments.io API. Subscriptions last 365 days from confirmed on-chain settlement.
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

  <!-- DIRECT CRYPTO PAYMENT MODAL -->
  <div id="directCryptoModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.85);backdrop-filter:blur(8px);z-index:99999;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#0f0716;border:1px solid rgba(255,255,255,0.15);border-radius:16px;width:100%;max-width:480px;box-shadow:0 25px 50px rgba(0,0,0,0.9),0 0 30px rgba(25,227,255,0.15);overflow:hidden;color:#f8fafc;">
      <!-- Header -->
      <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid rgba(255,255,255,0.08);background:rgba(255,255,255,0.02);">
        <div style="display:flex;align-items:center;gap:10px;">
          <span style="font-size:20px;color:var(--cyan);">🪙</span>
          <h3 id="modalPlanTitle" style="margin:0;font-size:16px;font-weight:800;color:#fff;">Direct Crypto Payment</h3>
        </div>
        <button type="button" onclick="closeDirectPaymentModal()" style="background:transparent;border:none;color:#94a3b8;font-size:22px;cursor:pointer;line-height:1;">&times;</button>
      </div>

      <!-- Body -->
      <div style="padding:20px;">
        <!-- Step 1: Select Coin -->
        <div id="directStepSelect">
          <label style="display:block;font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:6px;">Select Cryptocurrency to Generate Address</label>
          <select id="modalDirectCurrency" style="width:100%;padding:10px 12px;background:#07030d;border:1px solid rgba(255,255,255,0.15);border-radius:8px;color:#fff;font-size:14px;outline:none;margin-bottom:14px;">
            <option value="usdttrc20" selected>USDT (TRC-20) &bull; Recommended (Lowest fees)</option>
            <option value="ltc">Litecoin (LTC) &bull; Fast &amp; Cheap</option>
            <option value="doge">Dogecoin (DOGE) &bull; Low Fee</option>
            <option value="trx">Tron (TRX)</option>
            <option value="btc">Bitcoin (BTC)</option>
            <option value="eth">Ethereum (ETH)</option>
            <option value="sol">Solana (SOL)</option>
            <option value="xmr">Monero (XMR)</option>
          </select>
          <button type="button" id="btnGenerateAddress" onclick="generateDirectAddress()" class="btn btn-fill btn-wide" style="background:linear-gradient(135deg,#19e3ff,#0284c7);color:#07030d;font-weight:900;font-size:14px;">
            Generate Deposit Address
          </button>
        </div>

        <!-- Step 2: Display Deposit Address & QR -->
        <div id="directStepPay" style="display:none;text-align:center;">
          <div style="font-size:12px;color:var(--mute);margin-bottom:6px;">Send exactly:</div>
          <div id="modalPayAmount" style="font-size:22px;font-weight:900;color:#19e3ff;font-family:monospace;margin-bottom:12px;">--</div>

          <div style="display:inline-block;padding:8px;background:#fff;border-radius:10px;margin-bottom:14px;">
            <img id="modalQrCode" src="" alt="Deposit QR" style="width:160px;height:160px;display:block;">
          </div>

          <div style="text-align:left;background:#07030d;border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:10px 12px;margin-bottom:14px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
              <span style="font-size:11px;color:var(--mute);text-transform:uppercase;font-weight:700;">Deposit Address</span>
              <button type="button" onclick="copyAddress()" style="background:transparent;border:none;color:var(--cyan);font-size:11px;font-weight:800;cursor:pointer;">📋 Copy</button>
            </div>
            <div id="modalPayAddress" style="font-family:monospace;font-size:12px;color:#fff;word-break:break-all;">--</div>
          </div>

          <div style="background:rgba(25,227,255,0.06);border:1px solid rgba(25,227,255,0.2);border-radius:8px;padding:10px;font-size:12px;color:#94a3b8;margin-bottom:16px;">
            <span style="color:#19e3ff;font-weight:700;">Live Status:</span>
            <span id="modalLiveStatus" style="font-weight:700;color:#ffd60a;">Waiting for blockchain transaction...</span>
          </div>

          <button type="button" id="btnCheckModalStatus" onclick="checkModalPaymentStatus()" class="btn btn-line btn-wide" style="color:#fff;border-color:rgba(255,255,255,0.2);">
            Check Payment Status
          </button>
        </div>

        <div id="modalDirectError" style="display:none;margin-top:12px;padding:10px;border-radius:8px;background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.4);color:#fca5a5;font-size:12px;font-weight:600;"></div>
      </div>
    </div>
  </div>

  <script>
    let currentModalPlan = 'premium';
    let currentModalUsd = 15;
    let activeModalOrderId = null;
    let activeModalPollTimer = null;

    const authUserPlan = "{{ auth()->check() ? auth()->user()->plan : 'free' }}";

    function openDirectPaymentModal(plan, amountUsd) {
      if (authUserPlan === 'premium_plus') {
        alert('You already have an active Premium+ (VIP) membership! You cannot order again while your 1-year subscription is active.');
        return;
      }
      if (authUserPlan === 'premium' && plan === 'premium') {
        alert('You already have an active Premium ($15 PRO) membership! You cannot order Premium again, but you can upgrade to Premium+ ($30 VIP).');
        return;
      }

      currentModalPlan = plan;
      currentModalUsd = amountUsd;
      document.getElementById('modalPlanTitle').textContent = plan === 'premium_plus' ? 'Pay $30 for Premium+' : 'Pay $15 for Premium';
      document.getElementById('directStepSelect').style.display = 'block';
      document.getElementById('directStepPay').style.display = 'none';
      document.getElementById('modalDirectError').style.display = 'none';
      document.getElementById('directCryptoModal').style.display = 'flex';
    }

    function closeDirectPaymentModal() {
      document.getElementById('directCryptoModal').style.display = 'none';
      if (activeModalPollTimer) clearInterval(activeModalPollTimer);
    }

    async function generateDirectAddress() {
      const btn = document.getElementById('btnGenerateAddress');
      const err = document.getElementById('modalDirectError');
      const currency = document.getElementById('modalDirectCurrency').value;
      err.style.display = 'none';
      btn.disabled = true;
      btn.textContent = 'Generating on NOWPayments...';

      try {
        const res = await fetch('{{ route("donations.nowpayments.direct") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ plan: currentModalPlan, pay_currency: currency })
        });
        const data = await res.json();
        if (!res.ok || !data.ok) {
          throw new Error(data.error || 'Failed to initialize payment');
        }

        activeModalOrderId = data.order_id;
        document.getElementById('modalPayAmount').textContent = `${data.pay_amount} ${data.pay_currency}`;
        document.getElementById('modalPayAddress').textContent = data.pay_address;
        document.getElementById('modalQrCode').src = `https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodeURIComponent(data.pay_address)}`;
        document.getElementById('directStepSelect').style.display = 'none';
        document.getElementById('directStepPay').style.display = 'block';

        // Start automatic polling every 8 seconds
        if (activeModalPollTimer) clearInterval(activeModalPollTimer);
        activeModalPollTimer = setInterval(checkModalPaymentStatus, 8000);
      } catch (e) {
        err.textContent = e.message;
        err.style.display = 'block';
      } finally {
        btn.disabled = false;
        btn.textContent = 'Generate Deposit Address';
      }
    }

    async function checkModalPaymentStatus() {
      if (!activeModalOrderId) return;
      try {
        const res = await fetch(`/api/donations/nowpayments/check/${activeModalOrderId}`);
        const data = await res.json();
        if (data && data.ok) {
          const statusEl = document.getElementById('modalLiveStatus');
          statusEl.textContent = data.status.toUpperCase();
          if (data.status === 'confirmed' || data.status === 'finished') {
            statusEl.style.color = '#22c55e';
            statusEl.textContent = 'CONFIRMED! UPGRADING ACCOUNT...';
            clearInterval(activeModalPollTimer);
            setTimeout(() => {
              window.location.href = `{{ route("premium") }}?success=1&order_id=${activeModalOrderId}`;
            }, 1500);
          } else if (data.status === 'confirming') {
            statusEl.style.color = '#19e3ff';
            statusEl.textContent = 'TRANSACTION DETECTED (CONFIRMING ON BLOCKCHAIN)...';
          }
        }
      } catch {}
    }

    function copyAddress() {
      const text = document.getElementById('modalPayAddress').textContent;
      navigator.clipboard.writeText(text).then(() => {
        alert('Deposit address copied to clipboard!');
      });
    }

    // Polling helper for the banner tracker if an active donation exists
    async function pollOrderStatus(orderId) {
      const btn = document.getElementById('btnManualPoll');
      if (btn) btn.textContent = 'Checking...';
      try {
        const res = await fetch(`/api/donations/nowpayments/check/${orderId}`);
        const data = await res.json();
        if (data && data.ok) {
          const badge = document.getElementById('trackerStatusBadge');
          if (badge) badge.textContent = data.status;
          if (data.status === 'confirmed' || data.status === 'finished') {
            window.location.href = `{{ route("premium") }}?success=1&order_id=${orderId}`;
          } else {
            alert(`Current status: ${data.status.toUpperCase()}`);
          }
        }
      } catch (e) {
        alert('Status check failed. Please try again.');
      } finally {
        if (btn) btn.textContent = 'Check Status Now';
      }
    }
  </script>
@endsection
