<?php
require_once __DIR__ . '/includes/config.php';
$page_title = 'Cookie Policy';
$page_desc  = 'Which cookies ' . site_name() . ' uses and how you can control them.';
$page_path  = 'cookies.php';
require __DIR__ . '/includes/page-start.php';

$site = e(site_name()); $mail = e(CONTACT_EMAIL);
$u = fn(string $p) => e(url($p));

$doc = [
  'title' => 'Cookie Policy', 'color' => '#8a3dff', 'updated' => date('F j, Y'),
  'lead'  => "What the small files and browser storage on $site do.",
  'sections' => [
    ['id' => 'what', 'title' => 'What are cookies', 'body' => <<<HTML
<p>Cookies are small text files stored by your browser. We also use similar technology, such as local storage, to remember your choices.</p>
HTML],
    ['id' => 'used', 'title' => 'What we use', 'body' => <<<HTML
<div class="table-wrap"><table>
<thead><tr><th>Type</th><th>Purpose</th><th>Examples</th></tr></thead>
<tbody>
<tr><td>Essential</td><td>Sign-in session, security tokens (CSRF), captcha</td><td>session cookie, Cloudflare Turnstile</td></tr>
<tr><td>Preferences</td><td>Remember that you confirmed you are 18+, accepted this notice, or chose grid or list view</td><td>age18, cookieOk, feedView</td></tr>
<tr><td>Analytics</td><td>Count visits and see which pages are popular</td><td>Google Analytics or similar, if enabled by the site owner</td></tr>
</tbody></table></div>
HTML],
    ['id' => 'control', 'title' => 'Your choices', 'body' => <<<HTML
<p>You can block or delete cookies in your browser settings. Essential cookies are needed for sign-in and security, so parts of the site will not work without them. If you clear your browser data, you will be asked to confirm your age again.</p>
HTML],
    ['id' => 'search', 'title' => 'Search engines', 'body' => <<<HTML
<p>The age notice and cookie notice do not hide the pages from search engine crawlers, so public blogs stay discoverable.</p>
HTML],
    ['id' => 'contact', 'title' => 'Questions', 'body' => <<<HTML
<p>Write to <a href="mailto:{$mail}">{$mail}</a>. More about how we handle data is in the <a href="{$u('privacy.php')}">Privacy Policy</a>.</p>
HTML],
  ],
];

require __DIR__ . '/includes/legal.php';
require __DIR__ . '/includes/page-end.php';
