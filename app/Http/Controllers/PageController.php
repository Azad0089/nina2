<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function advertising()
    {
        return view('pages.advertising');
    }

    public function premium()
    {
        return view('pages.premium');
    }

    public function support()
    {
        return view('pages.support');
    }

    public function terms()
    {
        $site = site_name();
        $legal = \App\Models\Setting::get('legal_name', $site . ' Media Ltd.');
        $addr = \App\Models\Setting::get('legal_address', 'International Digital Commerce District');
        $mail = \App\Models\Setting::get('community_email', 'support@nina.sucks');
        $rulesUrl = url('/rules');
        $dmcaUrl = url('/dmca');
        $premiumUrl = url('/premium');
        $advUrl = url('/advertising');
        $contactUrl = url('/contact');

        $bodyHtml = <<<HTML
<div class="legal-doc" style="display:flex;flex-direction:column;gap:28px;">
  <section id="acceptance">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">Acceptance of these terms</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">These Terms of Service ("Terms") are an agreement between you and {$legal} ("we", "us"), the operator of {$site}. If you do not agree, do not use the site.</p>
    <p style="color:var(--text);line-height:1.7;">We may update the Terms from time to time. When we make important changes we will show a notice on the site. Continuing to use {$site} after a change means you accept the new Terms.</p>
  </section>

  <section id="eligibility">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">Eligibility: adults only</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">{$site} contains adult content. You must be at least <strong>18 years old</strong>, or the age of majority where you live if that is higher, to enter the site or create an account.</p>
    <p style="color:var(--text);line-height:1.7;">By using the site you confirm that adult content is legal where you live and that you are not entering on behalf of a minor. We may ask for proof of age at any time and may suspend accounts that cannot provide it.</p>
  </section>

  <section id="account">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">Your account</h2>
    <ul style="color:var(--text);line-height:1.8;padding-left:24px;list-style-type:disc;">
      <li>Give accurate information and keep your password secret. You are responsible for everything done through your account.</li>
      <li>One person may create multiple blogs under one account. Do not create accounts to avoid a suspension or ban.</li>
      <li>Your first blog is also your public profile. Anything you put in its bio, links and email box is visible to everyone.</li>
      <li>Tell us straight away at <a href="mailto:{$mail}" style="color:#ff2bd6;text-decoration:underline;">{$mail}</a> if you think your account was accessed without permission.</li>
    </ul>
  </section>

  <section id="content">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">Your content</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">You own the content you post. By posting it you give {$site} a worldwide, non-exclusive, royalty-free licence to host, store, display, resize, distribute and promote it on the site and in our feeds, so that the service can work. This licence ends when you delete the content, except for copies that are reasonably retained in backups or shared by other users through reblogs.</p>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">You confirm that you have all rights needed to post your content, that every person shown is an adult who has consented to the content being published, and that you keep the records the law requires (see <a href="{$dmcaUrl}" style="color:#19e3ff;text-decoration:underline;">2257 &amp; DMCA</a>).</p>
    <p style="color:var(--text);line-height:1.7;">Adult content must be labelled and categorised correctly. The full list of what is allowed and not allowed is in our <a href="{$rulesUrl}" style="color:#ffd60a;text-decoration:underline;">Community Rules</a>.</p>
  </section>

  <section id="conduct">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">What you may not do</h2>
    <ul style="color:var(--text);line-height:1.8;padding-left:24px;list-style-type:disc;">
      <li>Post anything that breaks the <a href="{$rulesUrl}" style="color:#ffd60a;text-decoration:underline;">Community Rules</a> or the law.</li>
      <li>Harass, threaten, dox or impersonate other people.</li>
      <li>Upload malware, scrape the site at scale, or try to break or bypass security, rate limits or age checks.</li>
      <li>Use the site to send spam or to sell illegal goods or services.</li>
      <li>Sell, transfer or share your account without our written permission.</li>
    </ul>
  </section>

  <section id="blogs">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">Blogs, communities and subdomains</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">You may create additional blogs and communities. Each blog can have its own name, design and subdomain. Blog names and subdomains are handed out first come, first served, and we may reclaim names that impersonate others, infringe trademarks or are inactive for a long time.</p>
    <p style="color:var(--text);line-height:1.7;">Public blogs can be indexed by search engines. Suspended or banned blogs are hidden from everyone except our administrators.</p>
  </section>

  <section id="premium">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">Premium, donations, points and promotion</h2>
    <ul style="color:var(--text);line-height:1.8;padding-left:24px;list-style-type:disc;">
      <li><strong>Donations.</strong> Premium and Premium+ are awarded for voluntary cryptocurrency donations that help pay the running costs of the site. Donating at or above the published amounts gives you the matching status for one year.</li>
      <li><strong>Benefits.</strong> Benefits (badge, monthly points) are listed on the <a href="{$premiumUrl}" style="color:#ff2bd6;text-decoration:underline;">Premium page</a>. We may change amounts and prices; changes do not affect periods that are already paid for.</li>
      <li><strong>Points.</strong> Points have no cash value, cannot be sold or transferred, and can only be spent in the <a href="{$advUrl}" style="color:#19e3ff;text-decoration:underline;">advertising marketplace</a>. Points expire when the benefit that produced them ends, unless stated otherwise.</li>
      <li><strong>Payments.</strong> Crypto payments are processed by third-party providers and are final. We cannot reverse blockchain transactions; where the law gives you a right to a refund we will honour it.</li>
      <li>We may turn donations, premium and promotions on or off at any time.</li>
    </ul>
  </section>

  <section id="moderation">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">Moderation and enforcement</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">We may remove content, hide or disable blogs, and suspend, ban or delete accounts that break these Terms or the Community Rules, with or without warning where the breach is serious.</p>
    <ul style="color:var(--text);line-height:1.8;padding-left:24px;list-style-type:disc;margin-bottom:10px;">
      <li><strong>Suspension</strong> is temporary. You cannot sign in until it is lifted, and suspended blogs are hidden.</li>
      <li><strong>A ban</strong> is normally permanent. Banned accounts and blogs stay in our system but are not visible.</li>
      <li><strong>Deletion</strong> is permanent.</li>
    </ul>
    <p style="color:var(--text);line-height:1.7;">If you think we made a mistake, you can appeal by writing to us through the <a href="{$contactUrl}" style="color:#19e3ff;text-decoration:underline;" data-modal="support">contact page</a>.</p>
  </section>

  <section id="copyright">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">Copyright</h2>
    <p style="color:var(--text);line-height:1.7;">We respect intellectual property and respond to valid notices. See the <a href="{$dmcaUrl}" style="color:#ff1f4b;text-decoration:underline;">DMCA policy</a> for how to report infringing content and how to send a counter-notice.</p>
  </section>

  <section id="disclaimer">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">Disclaimers and liability</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">{$site} is provided "as is" and "as available". We do not promise that the service will always be available or error-free, and we are not responsible for content posted by users.</p>
    <p style="color:var(--text);line-height:1.7;">To the fullest extent allowed by law, we are not liable for indirect or consequential losses, loss of profits, data or goodwill arising from your use of the site. Nothing in these Terms limits liability that cannot be limited by law.</p>
  </section>

  <section id="law">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">Governing law</h2>
    <p style="color:var(--text);line-height:1.7;">These Terms are governed by the laws of the country in which {$legal} is established, without regard to conflict-of-law rules. Mandatory consumer rights in your country of residence are not affected.</p>
  </section>

  <section id="contact">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">Contact</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:6px;">Questions about these Terms? Write to <a href="mailto:{$mail}" style="color:#ff2bd6;text-decoration:underline;">{$mail}</a> or use the <a href="{$contactUrl}" style="color:#19e3ff;text-decoration:underline;" data-modal="support">contact form</a>.</p>
    <p style="color:var(--mute);font-size:13px;line-height:1.6;">{$legal}, {$addr}</p>
  </section>
</div>
HTML;

        return view('pages.content-page', [
            'title' => 'Terms of Service',
            'lead' => "Please read these terms before using {$site}. By entering the site or creating an account you agree to them.",
            'color' => '#ff2bd6',
            'bodyHtml' => $bodyHtml
        ]);
    }

    public function privacy()
    {
        $site = site_name();
        $legal = \App\Models\Setting::get('legal_name', $site . ' Media Ltd.');
        $addr = \App\Models\Setting::get('legal_address', 'International Digital Commerce District');
        $mail = \App\Models\Setting::get('community_email', 'privacy@nina.sucks');
        $cookieUrl = url('/cookies');
        $contactUrl = url('/contact');

        $bodyHtml = <<<HTML
<div class="legal-doc" style="display:flex;flex-direction:column;gap:28px;">
  <section id="overview">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">1. Overview &amp; Commitment</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">{$legal} ("we", "our", or "us") operates {$site}. We are committed to safeguarding the privacy and personal data of our users. This Privacy Policy explains what information we collect, how we process and store it, and the rights and choices available to you.</p>
    <p style="color:var(--text);line-height:1.7;">By accessing {$site}, creating an account, or publishing content, you acknowledge the terms set out in this Policy.</p>
  </section>

  <section id="information-collected">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">2. Information We Collect</h2>
    <ul style="color:var(--text);line-height:1.8;padding-left:24px;list-style-type:disc;margin-bottom:10px;">
      <li><strong>Account Credentials:</strong> Username, blog subdomains, email address, password hashes, and optional referral attribution.</li>
      <li><strong>Public Profile &amp; Blog Data:</strong> Bio texts, external social and website links, contact emails provided in blog headers, theme configurations, avatars, and cover imagery.</li>
      <li><strong>User-Generated Content:</strong> Text posts, photos, video uploads, quotes, chat transcripts, tags, comments, likes, and reblogs.</li>
      <li><strong>Transaction &amp; Donation Data:</strong> Cryptographic order IDs, payment gateway transaction references, payment timestamps, and subscription tier assignments. We do not store private cryptographic keys or credit card numbers.</li>
      <li><strong>Technical &amp; Log Information:</strong> IP addresses, browser user agent strings, HTTP request headers, device types, operating systems, and session activity timestamps.</li>
      <li><strong>Identity Verification Records:</strong> Government-issued identification documents submitted voluntarily for creator verification under 18 U.S.C. § 2257, encrypted and retained in restricted-access compliance repositories.</li>
    </ul>
  </section>

  <section id="how-we-use-data">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">3. How We Use Your Information</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">We use your data solely for lawful operational and platform purposes, including:</p>
    <ul style="color:var(--text);line-height:1.8;padding-left:24px;list-style-type:disc;">
      <li>Providing, maintaining, hosting, and optimizing your microblogs and content feeds.</li>
      <li>Enforcing our Terms of Service, Community Rules, and age verification safeguards.</li>
      <li>Preventing fraud, bot abuse, spam, non-consensual content, and malicious attacks.</li>
      <li>Processing voluntary cryptocurrency donations and allocating membership reward points.</li>
      <li>Responding to support tickets, DMCA takedown requests, appeals, and compliance inquiries.</li>
    </ul>
  </section>

  <section id="cookies-storage">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">4. Cookies &amp; Local Storage</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">We use session cookies and local storage tokens to remember your login state, age confirmation status, and feed view preferences (Grid vs. List). Detailed cookie disclosures and browser controls can be reviewed in our <a href="{$cookieUrl}" style="color:#19e3ff;text-decoration:underline;">Cookie Policy</a>.</p>
  </section>

  <section id="data-sharing">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">5. Data Sharing &amp; Third Parties</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">We <strong>do not sell, rent, or trade</strong> your personal data to data brokers or advertising networks. We may share data only under the following strict circumstances:</p>
    <ul style="color:var(--text);line-height:1.8;padding-left:24px;list-style-type:disc;">
      <li><strong>Service Providers:</strong> Cloud infrastructure hosts, security providers (e.g., Cloudflare Turnstile for bot mitigation), and crypto payment processors (NOWPayments, Bitcart).</li>
      <li><strong>Legal &amp; Compliance Obligations:</strong> Valid legal processes, court orders, subpoenas, or emergency requests where child protection or prevention of imminent harm is at stake.</li>
    </ul>
  </section>

  <section id="user-rights">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">6. Your Privacy Rights (GDPR &amp; CCPA)</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">Regardless of where you live, you have the right to:</p>
    <ul style="color:var(--text);line-height:1.8;padding-left:24px;list-style-type:disc;">
      <li><strong>Access &amp; Export:</strong> Request a copy of your personal data and blog posts.</li>
      <li><strong>Rectification:</strong> Correct or update any inaccurate profile or account details.</li>
      <li><strong>Erasure:</strong> Request the deletion of your account, blogs, and uploaded content.</li>
      <li><strong>Withdraw Consent:</strong> Revoke consent for optional features at any time.</li>
    </ul>
    <p style="color:var(--text);line-height:1.7;">To exercise any of these rights, contact us at <a href="mailto:{$mail}" style="color:#ff2bd6;text-decoration:underline;">{$mail}</a> or submit a request via our <a href="{$contactUrl}" style="color:#19e3ff;text-decoration:underline;">Contact Desk</a>.</p>
  </section>

  <section id="retention-security">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">7. Data Retention &amp; Security</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">We apply industry-standard TLS encryption in transit and AES encryption at rest. Passwords are cryptographically salted and hashed using Argon2/Bcrypt. We retain account data as long as your account remains active or as required by statutory recordkeeping mandates (including 18 U.S.C. § 2257).</p>
  </section>

  <section id="minors-policy">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">8. Protection of Minors</h2>
    <p style="color:var(--text);line-height:1.7;">{$site} is strictly an adult community for individuals aged 18 and older. We do not knowingly collect or maintain data from minors. Any accounts belonging to or depicting minors are terminated immediately and reported to the National Center for Missing &amp; Exploited Children (NCMEC) and appropriate law enforcement authorities.</p>
  </section>
</div>
HTML;

        return view('pages.content-page', [
            'title' => 'Privacy Policy',
            'lead' => "How {$site} handles, protects, and respects your personal data and creative content.",
            'color' => '#19e3ff',
            'bodyHtml' => $bodyHtml
        ]);
    }

    public function rules()
    {
        $site = site_name();
        $mail = \App\Models\Setting::get('community_email', 'support@nina.sucks');
        $advUrl = url('/advertising');
        $contactReportUrl = url('/contact') . '?topic=report';

        $bodyHtml = <<<HTML
<div class="legal-doc" style="display:flex;flex-direction:column;gap:28px;">
  <section id="adults">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">Adults only, always</h2>
    <p style="color:var(--text);line-height:1.7;">Everyone who appears in content on {$site} must be <strong>18 or older</strong>, and so must everyone who uses the site. Content that shows, suggests or sexualises anyone under 18, including drawn or AI-generated content that appears to be a minor, is banned. Accounts are removed immediately and reported to the authorities.</p>
  </section>

  <section id="consent">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">Consent</h2>
    <p style="color:var(--text);line-height:1.7;">Only post content of people who agreed to it being published. Never post intimate content of someone without their permission, including "revenge" content, hidden-camera content or leaked private photos. Remove it when the person asks.</p>
  </section>

  <section id="banned">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">Never allowed</h2>
    <ul style="color:var(--text);line-height:1.8;padding-left:24px;list-style-type:disc;">
      <li>Anything involving minors (see above).</li>
      <li>Non-consensual sex, rape content, or content where a person is unable to consent.</li>
      <li>Real violence, gore, torture, or harm to animals.</li>
      <li>Threats, doxxing, stalking or harassment.</li>
      <li>Hate speech, or content that promotes violence against people because of who they are.</li>
      <li>Selling illegal drugs, weapons, stolen data or other illegal goods and services, and human trafficking or coerced sex work.</li>
      <li>Scams, phishing, malware and fake giveaways.</li>
    </ul>
  </section>

  <section id="label">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">Label your content</h2>
    <p style="color:var(--text);line-height:1.7;">Choose the right content category for your blog and tag your posts honestly. Good tags help readers find what they like and help us keep the site organised. Misleading or spammy tags can be removed.</p>
  </section>

  <section id="respect">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">Be decent to each other</h2>
    <p style="color:var(--text);line-height:1.7;">Disagree without abuse. Do not send unwanted explicit messages. If someone tells you to stop contacting them, stop. Do not impersonate other people or blogs.</p>
  </section>

  <section id="spam">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">No spam or manipulation</h2>
    <ul style="color:var(--text);line-height:1.8;padding-left:24px;list-style-type:disc;">
      <li>Do not mass-follow, mass-message or flood tags.</li>
      <li>Do not buy, sell or trade followers, likes or reblogs.</li>
      <li>Do not abuse points, referrals or the promotion system, for example with fake accounts.</li>
      <li>Promotion is allowed in the <a href="{$advUrl}" style="color:#ffd60a;text-decoration:underline;">advertising marketplace</a> and on your own blog.</li>
    </ul>
  </section>

  <section id="reporting">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">Reporting</h2>
    <p style="color:var(--text);line-height:1.7;">See something that breaks these rules? Use the report option on the post or blog, or <a href="{$contactReportUrl}" style="color:#19e3ff;text-decoration:underline;" data-modal="report">send a report</a>. Reports are reviewed by our moderators. Include a link so we can find it quickly.</p>
  </section>

  <section id="enforcement">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">What happens if rules are broken</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:12px;">Depending on how serious it is, we may delete a post, suspend a blog (temporary, hidden while suspended), suspend an account (you cannot sign in until it is lifted), ban an account or blog (usually permanent), or delete it entirely. Serious breaches may be reported to the authorities.</p>
    <p style="color:var(--text);line-height:1.7;">You can appeal any decision at <a href="mailto:{$mail}" style="color:#ffd60a;text-decoration:underline;">{$mail}</a>. Please include your blog name and a short explanation.</p>
  </section>
</div>
HTML;

        return view('pages.content-page', [
            'title' => 'Community Rules',
            'lead' => "{$site} is a place for adults to be creative and loud. These rules keep it that way.",
            'color' => '#ffd60a',
            'bodyHtml' => $bodyHtml
        ]);
    }

    public function dmca()
    {
        $site = site_name();
        $legal = \App\Models\Setting::get('legal_name', $site . ' Media Ltd.');
        $addr = \App\Models\Setting::get('legal_address', 'International Digital Commerce District');
        $mail = \App\Models\Setting::get('community_email', 'dmca@nina.sucks');
        $contactUrl = url('/contact') . '?topic=legal';

        $bodyHtml = <<<HTML
<div class="legal-doc" style="display:flex;flex-direction:column;gap:28px;">
  <section id="dmca-policy">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">1. Digital Millennium Copyright Act Policy</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">{$site} respects the intellectual property rights of others and expects all users and blog operators to do the same. In accordance with Title 17, United States Code, Section 512(c), copyright owners or their authorized agents may submit a formal takedown notice to our designated Copyright Agent.</p>
  </section>

  <section id="dmca-notice">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">2. Submitting a DMCA Takedown Notice</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">To file a legally sufficient notice of copyright infringement under 17 U.S.C. § 512(c)(3), you must provide written communication containing the following elements:</p>
    <ul style="color:var(--text);line-height:1.8;padding-left:24px;list-style-type:disc;margin-bottom:10px;">
      <li>A physical or electronic signature of a person authorized to act on behalf of the owner of the copyright interest.</li>
      <li>Identification of the copyrighted work claimed to have been infringed, or, if multiple copyrighted works at a single online site are covered by a single notification, a representative list of such works.</li>
      <li>Identification of the material that is claimed to be infringing and information reasonably sufficient to permit us to locate the material (including exact URLs or Post IDs).</li>
      <li>Information reasonably sufficient to permit us to contact you, such as an address, telephone number, and an electronic mail address.</li>
      <li>A statement that you have a good faith belief that use of the material in the manner complained of is not authorized by the copyright owner, its agent, or the law.</li>
      <li>A statement that the information in the notification is accurate, and under penalty of perjury, that you are authorized to act on behalf of the owner of an exclusive right that is allegedly infringed.</li>
    </ul>
  </section>

  <section id="dmca-counternotice">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">3. Counter-Notification Procedure</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">If you believe your content was removed by mistake or misidentification, you may submit a counter-notification under 17 U.S.C. § 512(g)(3) containing:</p>
    <ul style="color:var(--text);line-height:1.8;padding-left:24px;list-style-type:disc;">
      <li>Your physical or electronic signature.</li>
      <li>Identification of the material that has been removed or to which access has been disabled and the location at which the material appeared before removal.</li>
      <li>A statement under penalty of perjury that you have a good faith belief that the material was removed or disabled as a result of mistake or misidentification.</li>
      <li>Your name, physical address, and telephone number, and a statement that you consent to the jurisdiction of the Federal District Court for the judicial district in which your address is located (or if outside the U.S., for any judicial district in which the service provider may be found).</li>
    </ul>
  </section>

  <section id="repeat-infringers">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">4. Repeat Infringer Policy</h2>
    <p style="color:var(--text);line-height:1.7;">In accordance with the DMCA and other applicable laws, {$site} maintains a strict policy of terminating, in appropriate circumstances and at our sole discretion, accounts of users who are determined to be repeat copyright infringers.</p>
  </section>

  <section id="designated-agent">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">5. Designated DMCA Copyright Agent</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:6px;">All formal infringement notices and counter-notifications must be sent to our Designated Agent:</p>
    <div style="padding:14px;background:rgba(255,255,255,0.03);border:1px solid var(--line);border-radius:4px;color:var(--text);font-size:13.5px;line-height:1.6;">
      <b>DMCA Legal Notice Officer</b><br>
      {$legal}<br>
      {$addr}<br>
      Email: <a href="mailto:{$mail}" style="color:#ff2bd6;text-decoration:underline;">{$mail}</a><br>
      Web Portal: <a href="{$contactUrl}" style="color:#19e3ff;text-decoration:underline;">Online DMCA Submission Form</a>
    </div>
  </section>
</div>
HTML;

        return view('pages.content-page', [
            'title' => 'DMCA & Copyright Notice',
            'lead' => 'Designated copyright agent, statutory notice requirements, and takedown procedures.',
            'color' => '#ff1f4b',
            'bodyHtml' => $bodyHtml
        ]);
    }

    public function cookies()
    {
        $site = site_name();
        $mail = \App\Models\Setting::get('community_email', 'support@nina.sucks');
        $privacyUrl = url('/privacy');

        $bodyHtml = <<<HTML
<div class="legal-doc" style="display:flex;flex-direction:column;gap:28px;">
  <section id="what">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">What are cookies</h2>
    <p style="color:var(--text);line-height:1.7;">Cookies are small text files stored by your browser. We also use similar technology, such as local storage, to remember your choices.</p>
  </section>

  <section id="used">
    <h2 style="font:800 20px var(--f-display);margin-bottom:12px;color:#fff;">What we use</h2>
    <div class="table-wrap" style="overflow-x:auto;">
      <table style="width:100%;border-collapse:collapse;margin:12px 0;font-size:14px;text-align:left;">
        <thead>
          <tr style="border-bottom:1px solid rgba(255,255,255,0.15);color:var(--mute);text-transform:uppercase;font-size:12px;letter-spacing:0.05em;">
            <th style="padding:10px 12px;">Type</th>
            <th style="padding:10px 12px;">Purpose</th>
            <th style="padding:10px 12px;">Examples</th>
          </tr>
        </thead>
        <tbody style="border-bottom:1px solid rgba(255,255,255,0.08);">
          <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
            <td style="padding:12px;font-weight:700;color:var(--text);"><span class="badge b-pro" style="background:#8a3dff;">Essential</span></td>
            <td style="padding:12px;color:var(--text);">Sign-in session, security tokens (CSRF), captcha</td>
            <td style="padding:12px;font-family:monospace;font-size:13px;color:#19e3ff;">session cookie, Cloudflare Turnstile</td>
          </tr>
          <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
            <td style="padding:12px;font-weight:700;color:var(--text);"><span class="badge b-id">Preferences</span></td>
            <td style="padding:12px;color:var(--text);">Remember that you confirmed you are 18+, accepted this notice, or chose grid or list view</td>
            <td style="padding:12px;font-family:monospace;font-size:13px;color:#19e3ff;">age18, cookieOk, feedView</td>
          </tr>
          <tr>
            <td style="padding:12px;font-weight:700;color:var(--text);"><span class="badge b-vip">Analytics</span></td>
            <td style="padding:12px;color:var(--text);">Count visits and see which pages are popular</td>
            <td style="padding:12px;font-family:monospace;font-size:13px;color:#19e3ff;">Google Analytics or similar, if enabled by the site owner</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>

  <section id="control">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">Your choices</h2>
    <p style="color:var(--text);line-height:1.7;">You can block or delete cookies in your browser settings. Essential cookies are needed for sign-in and security, so parts of the site will not work without them. If you clear your browser data, you will be asked to confirm your age again.</p>
  </section>

  <section id="search">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">Search engines</h2>
    <p style="color:var(--text);line-height:1.7;">The age notice and cookie notice do not hide the pages from search engine crawlers, so public blogs stay discoverable.</p>
  </section>

  <section id="contact">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">Questions</h2>
    <p style="color:var(--text);line-height:1.7;">Write to <a href="mailto:{$mail}" style="color:#ff2bd6;text-decoration:underline;">{$mail}</a>. More about how we handle data is in the <a href="{$privacyUrl}" style="color:#19e3ff;text-decoration:underline;">Privacy Policy</a>.</p>
  </section>
</div>
HTML;

        return view('pages.content-page', [
            'title' => 'Cookie Policy',
            'lead' => "What the small files and browser storage on {$site} do.",
            'color' => '#8a3dff',
            'bodyHtml' => $bodyHtml
        ]);
    }

    public function about()
    {
        $name = site_name();
        $lead = 'Decentralized expression, unlimited creative blogs, and pure adult microblogging.';
        $email = \App\Models\Setting::get('community_email');
        $organization = [
            '@type' => 'Organization',
            'name' => $name,
            'url' => url('/'),
            'description' => $name . ' is an adult microblogging community for photos, text, video, quotes, links, and chats.',
        ];
        if ($email) {
            $organization['email'] = $email;
        }

        return view('pages.content-page', [
            'title' => 'About ' . $name,
            'lead' => $lead,
            'color' => '#ff2bd6',
            'bodyHtml' => '<h2>Our Philosophy</h2><p>' . $name . ' was conceived as a vibrant home for unfiltered adult self-expression, photography, literature, and visual culture. Unlike sanitized corporate platforms governed by opaque algorithms, ' . $name . ' gives creators complete ownership of their content, customizable appearance, and an open reverse-chronological feed.</p><h2>Unlimited Blogs for Creators</h2><p>Every account holder can create unlimited independent blogs for distinct personas, projects, or genres. Customize accent palettes, banners, avatar artwork, and bio external links.</p><h2>Creator Economy & Community</h2><p>Earn reward points simply by sharing, following, and referring peers. Boost your creations in our fair community ad marketplace or verify your identity for life.</p>',
            'pageSchema' => [
                '@context' => 'https://schema.org',
                '@type' => 'AboutPage',
                'name' => 'About ' . $name,
                'url' => url('/about'),
                'description' => $lead,
                'mainEntity' => $organization,
            ],
        ]);
    }

    public function faq()
    {
        $faqs = [
            ['How do multiple blogs work?', 'From your Creator Studio dashboard under "My Blogs", you can spin up unlimited additional blogs at any time. Each blog receives its own unique web address, title, theme color, avatar, and post archive.'],
            ['How do reward points work?', 'You earn reward points by referring friends (100 points per referral) and through active community participation. Points can be spent in the Advertising Marketplace to boost your posts to thousands of members.'],
            ['How does the post queue work?', 'The post queue lets you schedule up to 20 text, photo, quote, and link posts. Queued posts are scheduled and automatically published sequentially.'],
            ['What are the ID Verified Creator badges?', 'Creators who submit proof of legal age and identity receive the permanent green ID Verified badge, unlocking boosted discovery and reader trust.'],
        ];
        $bodyHtml = '';
        $questions = [];
        foreach ($faqs as [$question, $answer]) {
            $bodyHtml .= '<h2>' . e($question) . '</h2><p>' . e($answer) . '</p>';
            $questions[] = [
                '@type' => 'Question',
                'name' => $question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $answer,
                ],
            ];
        }

        return view('pages.content-page', [
            'title' => 'Frequently Asked Questions',
            'lead' => 'Everything you need to know about blogging, points, and platform features.',
            'color' => '#19e3ff',
            'bodyHtml' => $bodyHtml,
            'pageSchema' => [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'name' => 'Frequently Asked Questions',
                'url' => url('/faq'),
                'description' => 'Everything you need to know about blogging, points, and platform features.',
                'mainEntity' => $questions,
            ],
        ]);
    }

    public function compliance()
    {
        $site = site_name();
        $legal = \App\Models\Setting::get('legal_name', $site . ' Media Ltd.');
        $addr = \App\Models\Setting::get('legal_address', 'International Digital Commerce District');
        $mail = \App\Models\Setting::get('community_email', 'compliance@nina.sucks');

        $bodyHtml = <<<HTML
<div class="legal-doc" style="display:flex;flex-direction:column;gap:28px;">
  <section id="statutory-notice">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">1. Statutory Compliance Notice (18 U.S.C. § 2257 &amp; 2257A)</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">All visual depictions, photographs, digital imagery, and motion pictures depicting actual sexually explicit conduct as defined in 18 U.S.C. § 2256(2)(A) published or displayed on {$site} are produced and maintained in compliance with the federal recordkeeping requirements of Title 18, United States Code, Section 2257 and Section 2257A, and Title 28, Code of Federal Regulations, Part 75.</p>
  </section>

  <section id="custodian-of-records">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">2. Custodian of Records &amp; Inspection Location</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">The original age-verification records required under 28 C.F.R. Part 75 (including copies of government-issued photographic identification, performer consent forms, and proof of legal majority) are maintained by the designated Custodian of Records at the following location:</p>
    <div style="padding:14px;background:rgba(255,255,255,0.03);border:1px solid var(--line);border-radius:4px;color:var(--text);font-size:13.5px;line-height:1.6;">
      <b>Records Compliance Department</b><br>
      {$legal}<br>
      {$addr}<br>
      Electronic Inquiries: <a href="mailto:{$mail}" style="color:#ffd60a;text-decoration:underline;">{$mail}</a>
    </div>
  </section>

  <section id="user-generated-exemption">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">3. User-Generated Content &amp; Secondary Producer Notice</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">{$site} functions as an interactive microblogging service and hosting intermediary. With respect to material created, produced, and uploaded by independent registered users ("Primary Producers"):</p>
    <ul style="color:var(--text);line-height:1.8;padding-left:24px;list-style-type:disc;">
      <li>Each user agrees, by creating an account and accepting our Terms, that they are the primary producer and maintain all required 18 U.S.C. § 2257 records for all individuals depicted in their uploaded material.</li>
      <li>Users attest under penalty of perjury that all performers and subjects appearing in adult material were at least 18 years of age at the time of creation.</li>
      <li>{$site} is not the primary producer of third-party user uploads and relies on statutory exemptions under 28 C.F.R. § 75.1 where applicable.</li>
    </ul>
  </section>

  <section id="exempt-material">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">4. Exempt Material</h2>
    <p style="color:var(--text);line-height:1.7;">Visual depictions consisting solely of simulated depictions, textual narratives, animated art, artistic non-explicit representations, or visual material created prior to the statutory enactment dates are exempt from the recordkeeping requirements of 18 U.S.C. § 2257.</p>
  </section>
</div>
HTML;

        return view('pages.content-page', [
            'title' => '18 U.S.C. § 2257 Recordkeeping Compliance',
            'lead' => 'Statutory age verification, recordkeeping custodian details, and producer obligations.',
            'color' => '#ffd60a',
            'bodyHtml' => $bodyHtml
        ]);
    }

    public function safety()
    {
        $site = site_name();
        $mail = \App\Models\Setting::get('community_email', 'safety@nina.sucks');
        $rulesUrl = url('/rules');
        $contactReportUrl = url('/contact') . '?topic=report';

        $bodyHtml = <<<HTML
<div class="legal-doc" style="display:flex;flex-direction:column;gap:28px;">
  <section id="core-principles">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">1. Trust, Safety &amp; Community Standards</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">At {$site}, creator safety, bodily autonomy, and non-negotiable consent form the bedrock of our microblogging platform. We are dedicated to providing a safe, consensual space for adult expression while eradicating abusive, illegal, or non-consensual activity.</p>
  </section>

  <section id="zero-tolerance">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">2. Zero-Tolerance Violations</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">We enforce strict, immediate, and permanent termination without exception for:</p>
    <ul style="color:var(--text);line-height:1.8;padding-left:24px;list-style-type:disc;">
      <li><strong>Child Sexual Abuse Material (CSAM):</strong> Any depiction, representation, or suggestion involving a minor results in instantaneous account deletion, digital asset forfeiture, and immediate reporting to NCMEC and international law enforcement.</li>
      <li><strong>Non-Consensual Intimate Imagery (NCII):</strong> Revenge porn, hidden-camera material, or leaked private media uploaded without explicit recorded consent.</li>
      <li><strong>Extortion &amp; Doxxing:</strong> Publishing private residential addresses, phone numbers, real-world identities, financial data, or sextortion threats.</li>
      <li><strong>Human Trafficking &amp; Coercion:</strong> Any promotion, solicitation, or facilitation of coerced sexual acts or human exploitation.</li>
    </ul>
  </section>

  <section id="reporting-tools">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">3. Rapid Reporting &amp; Moderation Triage</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">Every post and blog includes an integrated <strong>Report / Flag</strong> button. Reports are triaged 24/7 by human moderation personnel. You can also file expedited safety tickets directly through our <a href="{$contactReportUrl}" style="color:#19e3ff;text-decoration:underline;" data-modal="report">Safety &amp; Moderation Desk</a>.</p>
  </section>

  <section id="take-down-appeals">
    <h2 style="font:800 20px var(--f-display);margin-bottom:8px;color:#fff;">4. Immediate NCII Takedown &amp; Victim Support</h2>
    <p style="color:var(--text);line-height:1.7;margin-bottom:10px;">If you find your likeness or private intimate material uploaded to {$site} without your consent, write immediately to <a href="mailto:{$mail}" style="color:#ff1f4b;text-decoration:underline;">{$mail}</a> with the relevant links. Such material is prioritized and expunged within hours.</p>
  </section>
</div>
HTML;

        return view('pages.content-page', [
            'title' => 'Trust, Safety & Community Protection',
            'lead' => 'Our zero-tolerance standards, non-consensual imagery elimination, and creator safety protocols.',
            'color' => '#ff1f4b',
            'bodyHtml' => $bodyHtml
        ]);
    }

    private function ensurePasswordResetsTable()
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('password_resets')) {
                \Illuminate\Support\Facades\Schema::create('password_resets', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('email')->index();
                    $table->string('token')->index();
                    $table->timestamp('created_at')->nullable();
                });
            }
        } catch (\Throwable $e) {}
    }

    public function forgotView()
    {
        return view('pages.content-page', [
            'title' => 'Password Recovery',
            'lead' => 'Reset your password and regain access to your blogs.',
            'color' => '#19e3ff',
            'bodyHtml' => '<div style="max-width:440px;margin:20px auto;background:rgba(255,255,255,0.03);padding:24px;border:1px solid var(--line);border-radius:8px;"><p style="margin-bottom:16px;">Enter your registered account email or username and we will send a password reset link to your email address.</p><form method="POST" action="' . route('auth.forgot') . '"><input type="hidden" name="_token" value="' . csrf_token() . '"><div style="margin-bottom:14px;"><label style="display:block;font-size:13px;margin-bottom:6px;color:var(--mute);">Email or Username</label><input type="text" name="email" required placeholder="admin or you@example.com" style="width:100%;padding:10px;background:var(--ink);border:1px solid var(--line);color:var(--text);border-radius:4px;"></div><button type="submit" class="btn btn-fill" style="width:100%;">Send Reset Link to Email</button></form></div>'
        ]);
    }

    public function postForgot(Request $request)
    {
        $this->ensurePasswordResetsTable();

        $input = trim((string)$request->input('email', $request->input('login', '')));
        if (empty($input)) {
            return redirect()->back()->with('error', 'Please enter your account email or username.');
        }

        $user = \App\Models\User::where('email', $input)->orWhere('username', $input)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'No account found with username or email: ' . $input);
        }

        $token = \Illuminate\Support\Str::random(48);
        try {
            \Illuminate\Support\Facades\DB::table('password_resets')->updateOrInsert(
                ['email' => $user->email],
                ['token' => $token, 'created_at' => now()]
            );
        } catch (\Throwable $e) {}

        $resetUrl = url('/reset-password?token=' . $token);

        // Send reset link in email
        send_password_reset_email($user->email, $user->username, $resetUrl);

        $masked = mask_email($user->email);
        return redirect()->back()->with('success', "A password reset link has been sent to your email address (<strong>{$masked}</strong>).<br><br>Please check your mail inbox (and spam folder) to choose a new password.");
    }

    public function resetPasswordView(Request $request)
    {
        $this->ensurePasswordResetsTable();

        $token = $request->query('token');
        $valid = false;
        try {
            $record = \Illuminate\Support\Facades\DB::table('password_resets')->where('token', $token)->first();
            $valid = $record && (strtotime($record->created_at) > time() - 3600);
        } catch (\Throwable $e) {
            $valid = !empty($token);
        }

        return view('pages.content-page', [
            'title' => 'Set New Password',
            'lead' => $valid ? 'Choose a new secure password for your account.' : 'Reset link expired or invalid.',
            'color' => '#19e3ff',
            'bodyHtml' => $valid ? (
                '<div style="max-width:440px;margin:20px auto;background:rgba(255,255,255,0.03);padding:24px;border:1px solid var(--line);border-radius:8px;">' .
                '<form method="POST" action="' . route('password.update') . '">' .
                '<input type="hidden" name="_token" value="' . csrf_token() . '">' .
                '<input type="hidden" name="token" value="' . htmlspecialchars((string)$token) . '">' .
                '<div style="margin-bottom:14px;"><label style="display:block;font-size:13px;margin-bottom:6px;color:var(--mute);">New Password</label><input type="password" name="password" minlength="4" required style="width:100%;padding:10px;background:var(--ink);border:1px solid var(--line);color:var(--text);border-radius:4px;"></div>' .
                '<div style="margin-bottom:18px;"><label style="display:block;font-size:13px;margin-bottom:6px;color:var(--mute);">Confirm New Password</label><input type="password" name="password_confirmation" minlength="4" required style="width:100%;padding:10px;background:var(--ink);border:1px solid var(--line);color:var(--text);border-radius:4px;"></div>' .
                '<button type="submit" class="btn btn-fill" style="width:100%;">Save Password &amp; Sign In</button>' .
                '</form></div>'
            ) : (
                '<div style="text-align:center;padding:24px;"><p style="color:#f43f5e;font-weight:bold;margin-bottom:16px;">This password reset link is invalid or has expired.</p><a href="' . route('forgot') . '" class="btn btn-line">Request New Reset Link</a></div>'
            )
        ]);
    }

    public function postResetPassword(Request $request)
    {
        $this->ensurePasswordResetsTable();

        $token = $request->input('token');
        $password = $request->input('password');
        $confirmation = $request->input('password_confirmation');

        if (strlen((string)$password) < 4) {
            return redirect()->back()->with('error', 'Password must be at least 4 characters long.');
        }
        if ($password !== $confirmation) {
            return redirect()->back()->with('error', 'Passwords do not match.');
        }

        try {
            $record = \Illuminate\Support\Facades\DB::table('password_resets')->where('token', $token)->first();
            if ($record) {
                $user = \App\Models\User::where('email', $record->email)->first();
                if ($user) {
                    $user->password_hash = \Illuminate\Support\Facades\Hash::make($password);
                    $user->save();
                    \Illuminate\Support\Facades\DB::table('password_resets')->where('email', $record->email)->delete();
                    \Illuminate\Support\Facades\Auth::login($user);
                    return redirect()->route('dashboard')->with('success', 'Your password has been reset successfully! Welcome back.');
                }
            }
        } catch (\Throwable $e) {}

        return redirect()->route('login')->with('success', 'Your password has been updated. Please sign in with your new password.');
    }

    public function contactView(Request $request)
    {
        return view('pages.contact');
    }
}
