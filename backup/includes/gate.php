<!-- ============ AGE GATE (added by JS; crawlers are never blocked) ============ -->
<div class="gate" id="gate" role="dialog" aria-modal="true" aria-labelledby="gate-h" hidden>
  <div class="gate-box">
    <img src="<?= e(site_logo_url()) ?>" alt="<?= e(site_name()) ?>" width="200" height="150">
    <h2 id="gate-h">This site is for adults only</h2>
    <p><?= e(site_name()) ?> contains adult content. You must be 18 or older, or the legal age in your country, to enter.</p>
    <div class="cta">
      <button class="btn btn-fill" id="gateYes" type="button">I'm 18+ Adult</button>
      <a class="btn btn-line" href="https://www.google.com">Leave</a>
    </div>
  </div>
</div>

<!-- ============ COOKIE NOTICE ============ -->
<div class="cookie" id="cookie" role="region" aria-label="Cookie notice" hidden>
  <p>We use cookies to keep you signed in and to measure traffic. <a href="<?= e(url('cookies.php')) ?>">Cookie policy</a></p>
  <button class="btn btn-fill" id="cookieOk" type="button">Accept</button>
</div>
