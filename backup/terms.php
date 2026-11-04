<?php
require_once __DIR__ . '/includes/config.php';
/* NOTE: template legal text. Have a lawyer review and adapt it before launch. */
$page_title = 'Terms of Service';
$page_desc  = 'The rules for using ' . site_name() . ': eligibility, accounts, content, premium donations, moderation and more.';
$page_path  = 'terms.php';
require __DIR__ . '/includes/page-start.php';

$site = e(site_name()); $legal = e(LEGAL_NAME); $mail = e(CONTACT_EMAIL); $addr = e(LEGAL_ADDRESS);
$u = fn(string $p) => e(url($p));

$doc = [
  'title' => 'Terms of Service', 'color' => '#ff2bd6', 'updated' => date('F j, Y'),
  'lead'  => "Please read these terms before using $site. By entering the site or creating an account you agree to them.",
  'sections' => [
    ['id' => 'acceptance', 'title' => 'Acceptance of these terms', 'body' => <<<HTML
<p>These Terms of Service ("Terms") are an agreement between you and {$legal} ("we", "us"), the operator of {$site}. If you do not agree, do not use the site.</p>
<p>We may update the Terms from time to time. When we make important changes we will show a notice on the site. Continuing to use {$site} after a change means you accept the new Terms.</p>
HTML],
    ['id' => 'eligibility', 'title' => 'Eligibility: adults only', 'body' => <<<HTML
<p>{$site} contains adult content. You must be at least <strong>18 years old</strong>, or the age of majority where you live if that is higher, to enter the site or create an account.</p>
<p>By using the site you confirm that adult content is legal where you live and that you are not entering on behalf of a minor. We may ask for proof of age at any time and may suspend accounts that cannot provide it.</p>
HTML],
    ['id' => 'account', 'title' => 'Your account', 'body' => <<<HTML
<ul>
<li>Give accurate information and keep your password secret. You are responsible for everything done through your account.</li>
<li>One person may create multiple blogs under one account. Do not create accounts to avoid a suspension or ban.</li>
<li>Your first blog is also your public profile. Anything you put in its bio, links and email box is visible to everyone.</li>
<li>Tell us straight away at <a href="mailto:{$mail}">{$mail}</a> if you think your account was accessed without permission.</li>
</ul>
HTML],
    ['id' => 'content', 'title' => 'Your content', 'body' => <<<HTML
<p>You own the content you post. By posting it you give {$site} a worldwide, non-exclusive, royalty-free licence to host, store, display, resize, distribute and promote it on the site and in our feeds, so that the service can work. This licence ends when you delete the content, except for copies that are reasonably retained in backups or shared by other users through reblogs.</p>
<p>You confirm that you have all rights needed to post your content, that every person shown is an adult who has consented to the content being published, and that you keep the records the law requires (see <a href="{$u('dmca.php')}">2257 &amp; DMCA</a>).</p>
<p>Adult content must be labelled and categorised correctly. The full list of what is allowed and not allowed is in our <a href="{$u('rules.php')}">Community Rules</a>.</p>
HTML],
    ['id' => 'conduct', 'title' => 'What you may not do', 'body' => <<<HTML
<ul>
<li>Post anything that breaks the <a href="{$u('rules.php')}">Community Rules</a> or the law.</li>
<li>Harass, threaten, dox or impersonate other people.</li>
<li>Upload malware, scrape the site at scale, or try to break or bypass security, rate limits or age checks.</li>
<li>Use the site to send spam or to sell illegal goods or services.</li>
<li>Sell, transfer or share your account without our written permission.</li>
</ul>
HTML],
    ['id' => 'blogs', 'title' => 'Blogs, communities and subdomains', 'body' => <<<HTML
<p>You may create additional blogs and communities. Each blog can have its own name, design and subdomain. Blog names and subdomains are handed out first come, first served, and we may reclaim names that impersonate others, infringe trademarks or are inactive for a long time.</p>
<p>Public blogs can be indexed by search engines. Suspended or banned blogs are hidden from everyone except our administrators.</p>
HTML],
    ['id' => 'premium', 'title' => 'Premium, donations, points and promotion', 'body' => <<<HTML
<ul>
<li><strong>Donations.</strong> Premium and Premium+ are awarded for voluntary cryptocurrency donations that help pay the running costs of the site. Donating at or above the published amounts gives you the matching status for one year.</li>
<li><strong>Benefits.</strong> Benefits (badge, monthly points) are listed on the <a href="{$u('premium.php')}">Premium page</a>. We may change amounts and prices; changes do not affect periods that are already paid for.</li>
<li><strong>Points.</strong> Points have no cash value, cannot be sold or transferred, and can only be spent in the <a href="{$u('advertising.php')}">advertising marketplace</a>. Points expire when the benefit that produced them ends, unless stated otherwise.</li>
<li><strong>Payments.</strong> Crypto payments are processed by third-party providers and are final. We cannot reverse blockchain transactions; where the law gives you a right to a refund we will honour it.</li>
<li>We may turn donations, premium and promotions on or off at any time.</li>
</ul>
HTML],
    ['id' => 'moderation', 'title' => 'Moderation and enforcement', 'body' => <<<HTML
<p>We may remove content, hide or disable blogs, and suspend, ban or delete accounts that break these Terms or the Community Rules, with or without warning where the breach is serious.</p>
<ul>
<li><strong>Suspension</strong> is temporary. You cannot sign in until it is lifted, and suspended blogs are hidden.</li>
<li><strong>A ban</strong> is normally permanent. Banned accounts and blogs stay in our system but are not visible.</li>
<li><strong>Deletion</strong> is permanent.</li>
</ul>
<p>If you think we made a mistake, you can appeal by writing to us through the <a href="{$u('contact.php')}">contact page</a>.</p>
HTML],
    ['id' => 'copyright', 'title' => 'Copyright', 'body' => <<<HTML
<p>We respect intellectual property and respond to valid notices. See the <a href="{$u('dmca.php')}">DMCA policy</a> for how to report infringing content and how to send a counter-notice.</p>
HTML],
    ['id' => 'disclaimer', 'title' => 'Disclaimers and liability', 'body' => <<<HTML
<p>{$site} is provided "as is" and "as available". We do not promise that the service will always be available or error-free, and we are not responsible for content posted by users.</p>
<p>To the fullest extent allowed by law, we are not liable for indirect or consequential losses, loss of profits, data or goodwill arising from your use of the site. Nothing in these Terms limits liability that cannot be limited by law.</p>
HTML],
    ['id' => 'law', 'title' => 'Governing law', 'body' => <<<HTML
<p>These Terms are governed by the laws of the country in which {$legal} is established, without regard to conflict-of-law rules. Mandatory consumer rights in your country of residence are not affected.</p>
HTML],
    ['id' => 'contact', 'title' => 'Contact', 'body' => <<<HTML
<p>Questions about these Terms? Write to <a href="mailto:{$mail}">{$mail}</a> or use the <a href="{$u('contact.php')}">contact form</a>.</p>
<p>{$legal}, {$addr}</p>
HTML],
  ],
];

require __DIR__ . '/includes/legal.php';
require __DIR__ . '/includes/page-end.php';
