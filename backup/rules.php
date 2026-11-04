<?php
require_once __DIR__ . '/includes/config.php';
$page_title = 'Community Rules';
$page_desc  = 'Community guidelines for ' . site_name() . ': consent, labelling, respect and what gets a blog suspended or banned.';
$page_path  = 'rules.php';
require __DIR__ . '/includes/page-start.php';

$site = e(site_name()); $mail = e(CONTACT_EMAIL);
$u = fn(string $p) => e(url($p));

$doc = [
  'title' => 'Community Rules', 'color' => '#ffd60a', 'updated' => date('F j, Y'),
  'lead'  => "$site is a place for adults to be creative and loud. These rules keep it that way.",
  'sections' => [
    ['id' => 'adults', 'title' => 'Adults only, always', 'body' => <<<HTML
<p>Everyone who appears in content on {$site} must be <strong>18 or older</strong>, and so must everyone who uses the site. Content that shows, suggests or sexualises anyone under 18, including drawn or AI-generated content that appears to be a minor, is banned. Accounts are removed immediately and reported to the authorities.</p>
HTML],
    ['id' => 'consent', 'title' => 'Consent', 'body' => <<<HTML
<p>Only post content of people who agreed to it being published. Never post intimate content of someone without their permission, including "revenge" content, hidden-camera content or leaked private photos. Remove it when the person asks.</p>
HTML],
    ['id' => 'banned', 'title' => 'Never allowed', 'body' => <<<HTML
<ul>
<li>Anything involving minors (see above).</li>
<li>Non-consensual sex, rape content, or content where a person is unable to consent.</li>
<li>Real violence, gore, torture, or harm to animals.</li>
<li>Threats, doxxing, stalking or harassment.</li>
<li>Hate speech, or content that promotes violence against people because of who they are.</li>
<li>Selling illegal drugs, weapons, stolen data or other illegal goods and services, and human trafficking or coerced sex work.</li>
<li>Scams, phishing, malware and fake giveaways.</li>
</ul>
HTML],
    ['id' => 'label', 'title' => 'Label your content', 'body' => <<<HTML
<p>Choose the right content category for your blog and tag your posts honestly. Good tags help readers find what they like and help us keep the site organised. Misleading or spammy tags can be removed.</p>
HTML],
    ['id' => 'respect', 'title' => 'Be decent to each other', 'body' => <<<HTML
<p>Disagree without abuse. Do not send unwanted explicit messages. If someone tells you to stop contacting them, stop. Do not impersonate other people or blogs.</p>
HTML],
    ['id' => 'spam', 'title' => 'No spam or manipulation', 'body' => <<<HTML
<ul>
<li>Do not mass-follow, mass-message or flood tags.</li>
<li>Do not buy, sell or trade followers, likes or reblogs.</li>
<li>Do not abuse points, referrals or the promotion system, for example with fake accounts.</li>
<li>Promotion is allowed in the <a href="{$u('advertising.php')}">advertising marketplace</a> and on your own blog.</li>
</ul>
HTML],
    ['id' => 'reporting', 'title' => 'Reporting', 'body' => <<<HTML
<p>See something that breaks these rules? Use the report option on the post or blog, or <a href="{$u('contact.php')}?topic=report">send a report</a>. Reports are reviewed by our moderators. Include a link so we can find it quickly.</p>
HTML],
    ['id' => 'enforcement', 'title' => 'What happens if rules are broken', 'body' => <<<HTML
<p>Depending on how serious it is, we may delete a post, suspend a blog (temporary, hidden while suspended), suspend an account (you cannot sign in until it is lifted), ban an account or blog (usually permanent), or delete it entirely. Serious breaches may be reported to the authorities.</p>
<p>You can appeal any decision at <a href="mailto:{$mail}">{$mail}</a>. Please include your blog name and a short explanation.</p>
HTML],
  ],
];

require __DIR__ . '/includes/legal.php';
require __DIR__ . '/includes/page-end.php';
