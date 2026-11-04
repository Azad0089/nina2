<?php
require_once __DIR__ . '/includes/config.php';
/* NOTE: template legal text. Have a lawyer review it and fill in the custodian of records details. */
$page_title = '2257 & DMCA';
$page_desc  = 'Copyright takedown (DMCA) procedure and record-keeping (2257) compliance statement for ' . site_name() . '.';
$page_path  = 'dmca.php';
require __DIR__ . '/includes/page-start.php';

$site = e(site_name()); $legal = e(LEGAL_NAME); $mail = e(CONTACT_EMAIL); $addr = e(LEGAL_ADDRESS);
$u = fn(string $p) => e(url($p));

$doc = [
  'title' => '2257 & DMCA', 'color' => '#ff1f4b', 'updated' => date('F j, Y'),
  'lead'  => "Copyright takedowns, record-keeping and how to report illegal content on $site.",
  'sections' => [
    ['id' => 'dmca', 'title' => 'Copyright infringement notices (DMCA)', 'body' => <<<HTML
<p>{$site} respects the rights of creators. If you believe that content on the site infringes your copyright, send a notice to our designated agent containing:</p>
<ol>
<li>Your physical or electronic signature.</li>
<li>A description of the copyrighted work you say was infringed.</li>
<li>The exact URL(s) of the material, so we can find it.</li>
<li>Your name, address, phone number and email address.</li>
<li>A statement that you have a good-faith belief that the use is not authorised by the owner, its agent or the law.</li>
<li>A statement, under penalty of perjury, that the information is accurate and that you are the owner or authorised to act for the owner.</li>
</ol>
<p><strong>Designated agent:</strong> {$legal}, {$addr}, <a href="mailto:{$mail}">{$mail}</a></p>
HTML],
    ['id' => 'counter', 'title' => 'Counter-notice', 'body' => <<<HTML
<p>If your content was removed and you believe that was a mistake, you may send a counter-notice with your signature, the URL of the removed material, a statement under penalty of perjury that you believe it was removed by mistake, your contact details, and your consent to the jurisdiction of the courts where you live. We may restore the content within 10 to 14 business days unless the complainant starts a court action.</p>
HTML],
    ['id' => 'repeat', 'title' => 'Repeat infringers', 'body' => <<<HTML
<p>We disable accounts of users who repeatedly infringe copyright.</p>
HTML],
    ['id' => '2257', 'title' => '18 U.S.C. 2257 compliance statement', 'body' => <<<HTML
<p>{$site} is a platform where users publish their own content. We do not produce the content that users upload. Users who upload sexually explicit content confirm that all performers were at least 18 years old when it was made and that the user holds the records required by 18 U.S.C. 2257 and 2257A, or that the content is exempt.</p>
<p>Records for content produced by {$site} itself, if any, are held by the custodian of records:</p>
<p><strong>{$legal}</strong><br>{$addr}</p>
HTML],
    ['id' => 'illegal', 'title' => 'Reporting illegal content', 'body' => <<<HTML
<p>We have zero tolerance for child sexual abuse material and for non-consensual content. Report it immediately through the <a href="{$u('contact.php')}?topic=report">report form</a> or at <a href="mailto:{$mail}">{$mail}</a>, with the URL. We remove such content and report it to the competent authorities and child-protection hotlines.</p>
HTML],
    ['id' => 'removal', 'title' => 'Content removal request by a person shown', 'body' => <<<HTML
<p>If you appear in content that was published without your consent, tell us using the <a href="{$u('contact.php')}?topic=report">report form</a>. We prioritise these requests.</p>
HTML],
  ],
];

require __DIR__ . '/includes/legal.php';
require __DIR__ . '/includes/page-end.php';
