<!-- ============ LEFT SIDEBAR ============ -->
<aside class="side" id="side" aria-label="Site menu">
  <a class="side-logo" href="{{ url('/') }}" aria-label="{{ site_name() }} home">
    <img src="{{ site_logo_url() }}" alt="{{ site_name() }}" width="200" height="150">
  </a>

  <nav class="side-nav" aria-label="Main">
    @foreach(nav_items() as $item)
      @php $on = ($active_nav ?? 'explore') === $item['key']; @endphp
      <a href="{{ url($item['href']) }}" class="nav-link{{ $on ? ' is-active' : '' }}"{!! $on ? ' aria-current="page"' : '' !!}>
        {!! icon($item['icon']) !!}{{ $item['label'] }}
      </a>
    @endforeach

    @auth
      <a href="{{ route('dashboard') }}" class="nav-link{{ ($active_nav ?? '') === 'dashboard' ? ' is-active' : '' }}">
        {!! icon('settings') !!}Dashboard
      </a>
      @if(auth()->user()->role === 'admin')
        <a href="{{ url('/admin') }}" class="nav-link{{ ($active_nav ?? '') === 'admin' ? ' is-active' : '' }}">
          {!! icon('shield') !!}Admin
        </a>
      @endif
    @endauth
  </nav>

  <div class="side-auth">
    @guest
      <a href="{{ route('register') }}" data-modal="register" class="btn btn-fill">{!! icon('userplus') !!}Sign up</a>
      <a href="{{ route('login') }}" data-modal="login" class="btn btn-line">{!! icon('login') !!}Sign in</a>
    @else
      <div style="padding:10px 14px;background:rgba(255,255,255,0.05);border:1px solid var(--line);border-radius:2px;display:flex;align-items:center;gap:10px;">
        <span class="av" style="--c:var(--pink);width:32px;height:32px;font-size:14px;font-weight:700;">{{ initial(auth()->user()->username) }}</span>
        <div style="flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;">
          <b style="font-size:14px;display:block;">{{ auth()->user()->username }} {!! badge(auth()->user()->badge_vip ? 'vip' : (auth()->user()->badge_id ? 'id' : (auth()->user()->badge_pro ? 'pro' : ''))) !!}</b>
          <small style="color:var(--mute);font-size:12px;">{{ auth()->user()->points ?? 0 }} pts</small>
        </div>
      </div>
      <a href="{{ route('dashboard.post.create') }}" class="btn btn-fill" style="width:100%">{!! icon('plus') !!}New post</a>
      <a href="{{ route('logout') }}" class="btn btn-ghost" style="width:100%">Sign out</a>
    @endguest
  </div>

  <footer class="side-foot">
    @foreach(footer_links() as $l)
      <a href="{{ url($l['href']) }}">{{ $l['label'] }}</a>
    @endforeach
    <span>&copy; {{ date('Y') }} {{ site_name() }} &middot; 18+ only</span>
  </footer>
</aside>
<div class="scrim" id="scrim"></div>

<!-- ============ TOP BAR ============ -->
<header class="top">
  <button class="icon-btn menu-btn" id="menuBtn" aria-label="Open menu" aria-controls="side" aria-expanded="false">{!! icon('menu') !!}</button>
  <a class="top-logo" href="{{ url('/') }}" aria-label="{{ site_name() }} home">
    <img src="{{ asset('assets/favicon-192.png') }}" alt="" width="36" height="36">
    <span>{{ site_name() }}</span>
  </a>
  <form class="search" role="search" action="{{ route('trending') }}" method="get">
    {!! icon('search') !!}
    <input type="search" name="tag" placeholder="Search blogs, tags and posts" aria-label="Search blogs, tags and posts" value="{{ request('tag') }}">
  </form>
  @guest
    <div style="display:flex;align-items:center;gap:8px;">
      <a href="{{ route('login') }}" data-modal="login" class="btn btn-line" style="padding:8px 14px;font-size:13px;">Sign in</a>
      <a href="{{ route('register') }}" data-modal="register" class="btn btn-fill top-signup" style="padding:8px 14px;font-size:13px;">Sign up</a>
    </div>
  @else
    <div style="display:flex;align-items:center;gap:8px;">
      <a href="{{ route('dashboard') }}" class="btn btn-line" style="padding:6px 12px;font-size:13px;display:flex;align-items:center;gap:8px;border-radius:99px;" title="Go to Creator Studio">
        <span class="av" style="--c:var(--pink);width:24px;height:24px;font-size:11px;font-weight:700;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;background:var(--pink);color:#14000f;">{{ initial(auth()->user()->username) }}</span>
        <b style="font-weight:700;">{{ auth()->user()->username }}</b>
        <span style="font-size:11px;color:var(--pink);font-weight:700;">{{ auth()->user()->points ?? 0 }}p</span>
      </a>
      <a href="{{ route('dashboard.post.create') }}" class="btn btn-fill top-signup" style="padding:8px 14px;font-size:13px;">{!! icon('plus') !!} Post</a>
    </div>
  @endguest
</header>
