<?php
require_once __DIR__ . '/includes/config.php';
/* NOTE: template legal text. Have a lawyer review and adapt it before launch (GDPR / local law). */
$page_title = 'Privacy Policy';
$page_desc  = 'What data ' . site_name() . ' collects, why, who we share it with and how you can control it.';
$page_path  = 'privacy.php';
require __DIR__ . '/includes/page-start.php';

$site = e(site_name()); $legal = e(LEGAL_NAME); $mail = e(CONTACT_EMAIL); $addr = e(LEGAL_ADDRESS);
$u = fn(string $p) => e(url($p));

$doc = [
  'title' => 'Privacy Policy', 'color' => '#19e3ff', 'updated' => date('F j, Y'),
  'lead'  => "How $site handles your personal data, in plain language.",
  'sections' => [
    ['id' => 'who', 'title' => 'Who we are', 'body' => <<<HTML
<p>{$legal}, {$addr}, runs {$site} and is the controller of the personal data described here. You can reach us at <a href="mailto:{$mail}">{$mail}</a>.</p>
HTML],
    ['id' => 'collect', 'title' => 'What we collect', 'body' => <<<HTML
<ul>
<li><strong>Account data:</strong> email address, blog name, password (stored only as a secure hash), and the details you add to your profile such as bio, website links and contact email.</li>
<li><strong>Sign-in with Google:</strong> if you use it, we receive your name, email address and Google account ID.</li>
<li><strong>Content:</strong> posts, images, videos, messages, follows, likes and reblogs.</li>
<li><strong>Technical data:</strong> IP address, login history, browser and device information and pages visited. We use this for security, abuse prevention and moderation.</li>
<li><strong>Payments:</strong> donations are paid in cryptocurrency through third-party processors. We store the transaction reference, amount, currency and status, not your wallet keys.</li>
<li><strong>Identity verification (optional):</strong> if you ask for the Verified badge you send us photo ID. We use it only to check your identity and keep it access-restricted.</li>
<li><strong>Support messages</strong> and reports you send us.</li>
</ul>
HTML],
    ['id' => 'use', 'title' => 'How we use it', 'body' => <<<HTML
<ul>
<li>To run the service: create accounts, show blogs and feeds, deliver messages and notifications.</li>
<li>To keep the site safe: prevent fraud and abuse, enforce our <a href="{$u('rules.php')}">rules</a>, verify age and investigate reports.</li>
<li>To process donations, award premium status and points, and run the referral programme.</li>
<li>To measure and improve the site with analytics.</li>
<li>To meet legal duties, such as record keeping and answering lawful requests.</li>
</ul>
<p>Where the GDPR applies, our legal bases are contract (running your account), legitimate interests (security, moderation, analytics), legal obligation, and consent where we ask for it.</p>
HTML],
    ['id' => 'public', 'title' => 'What is public', 'body' => <<<HTML
<p>Blogs and posts are public by default and can be found through search engines. Your main blog acts as your profile, so its bio, website links and contact email are visible to everyone. Do not post anything there you do not want the world to see.</p>
HTML],
    ['id' => 'sharing', 'title' => 'Who we share data with', 'body' => <<<HTML
<p>We do not sell your personal data. We share it only with service providers who help us run the site, under contract:</p>
<ul>
<li>Hosting, storage and content delivery providers.</li>
<li>Cloudflare, for the Turnstile captcha and security.</li>
<li>Google, for sign-in and (if enabled) analytics.</li>
<li>Payment processors such as NOWPayments and BitCart.</li>
<li>Email delivery providers.</li>
<li>Authorities, where the law requires it or to protect people from harm.</li>
</ul>
HTML],
    ['id' => 'cookies', 'title' => 'Cookies', 'body' => <<<HTML
<p>We use cookies for sign-in, security, your age confirmation and preferences, and for analytics. Details are in the <a href="{$u('cookies.php')}">Cookie Policy</a>.</p>
HTML],
    ['id' => 'retention', 'title' => 'How long we keep data', 'body' => <<<HTML
<p>We keep account data while your account exists. When you delete your account we remove your personal data and public content, except what we must keep by law or for security, such as records of banned accounts, payment records and login logs. ID verification images are deleted once the check is complete, unless the law requires otherwise.</p>
HTML],
    ['id' => 'rights', 'title' => 'Your rights', 'body' => <<<HTML
<p>Depending on where you live you may have the right to access, correct, delete or export your data, to object to or restrict some processing, to withdraw consent, and to complain to your data protection authority. To use these rights, write to <a href="mailto:{$mail}">{$mail}</a> or use the <a href="{$u('contact.php')}">contact form</a>.</p>
HTML],
    ['id' => 'age', 'title' => 'Age limit', 'body' => <<<HTML
<p>{$site} is only for adults (18+). We do not knowingly collect data from anyone under 18. If you believe a minor has an account, tell us at once and we will remove it.</p>
HTML],
    ['id' => 'security', 'title' => 'Security and transfers', 'body' => <<<HTML
<p>We protect data with encryption in transit, hashed passwords and access controls. No system is perfectly secure. Our providers may process data in other countries; where required we use safeguards such as standard contractual clauses.</p>
HTML],
    ['id' => 'changes', 'title' => 'Changes to this policy', 'body' => <<<HTML
<p>We will update this page when our practices change and show a notice for important changes.</p>
HTML],
  ],
];

require __DIR__ . '/includes/legal.php';
require __DIR__ . '/includes/page-end.php';
