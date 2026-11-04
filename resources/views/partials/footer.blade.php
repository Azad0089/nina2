<footer class="site-footer" style="margin-top:auto;border-top:1px solid rgba(255,255,255,0.08);padding:32px 20px 48px;background:rgba(7,3,13,0.95);font-size:13px;color:#94a3b8;">
  <div style="max-width:1100px;margin:0 auto;display:flex;flex-wrap:wrap;gap:24px;justify-content:space-between;align-items:center;">
    <div style="display:flex;flex-wrap:wrap;gap:16px 20px;align-items:center;">
      @if(function_exists('footer_links'))
        @foreach(footer_links() as $l)
          <a href="{{ url($l['href']) }}" {!! in_array($l['href'], ['/support', '/contact']) ? 'data-modal="support"' : '' !!} style="color:#cbd5e1;text-decoration:none;transition:color 0.2s;" onmouseover="this.style.color='#ff2bd6'" onmouseout="this.style.color='#cbd5e1'">{{ $l['label'] }}</a>
        @endforeach
      @else
        <a href="{{ url('/about') }}" style="color:#cbd5e1;text-decoration:none;">About</a>
        <a href="{{ url('/terms') }}" style="color:#cbd5e1;text-decoration:none;">Terms</a>
        <a href="{{ url('/privacy') }}" style="color:#cbd5e1;text-decoration:none;">Privacy</a>
        <a href="{{ url('/rules') }}" style="color:#cbd5e1;text-decoration:none;">Rules</a>
        <a href="{{ url('/dmca') }}" style="color:#cbd5e1;text-decoration:none;">DMCA</a>
        <a href="{{ url('/compliance') }}" style="color:#cbd5e1;text-decoration:none;">2257</a>
        <a href="{{ url('/support') }}" style="color:#cbd5e1;text-decoration:none;" data-modal="support">Support</a>
        <a href="{{ url('/premium') }}" style="color:#cbd5e1;text-decoration:none;">Premium</a>
      @endif
    </div>
    <div style="color:#64748b;font-size:12px;">
      &copy; {{ date('Y') }} {{ function_exists('site_name') ? site_name() : 'Nina.Sucks' }} &middot; 18+ Adult Microblogging Community
    </div>
  </div>
  @stack('footer')
</footer>
