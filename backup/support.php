<?php
require_once __DIR__ . '/includes/config.php';
$page_title = 'Help Center';
$page_desc  = 'Answers about blogs, profiles, scheduled posts, premium, points, advertising and safety on ' . site_name() . '.';
$page_path  = 'support.php';
$active_nav = 'support';
require __DIR__ . '/includes/page-start.php';

$hero = ['title' => 'Help Center', 'lead' => 'Quick answers. Cannot find yours? Ask us.', 'color' => '#19e3ff'];

$faq = [
  'Getting started' => [
    ['How do I start a blog?', 'Press Sign up, choose a blog name and confirm you are 18 or older. Your first blog is created automatically and doubles as your profile.'],
    ['What is the difference between my main blog and extra blogs?', 'Your main blog is also your profile: it has a bio box, three website links and one contact email. Extra blogs are regular blogs without the profile fields. You can create as many extra blogs as you like.'],
    ['Which post formats can I use?', 'Text, photo, video, quote, link and chat. Audio posts are not available.'],
    ['Can I schedule posts?', 'Yes. Each blog has its own queue of up to 20 scheduled posts. Text, photo and link posts can be queued; video and chat posts cannot.'],
  ],
  'Design & blogs' => [
    ['Can I change how my blog looks?', 'Every blog can have its own colors, header, cover image and layout in the blog settings.'],
    ['Do blogs get their own web address?', 'Yes. Each blog has a subdomain, for example yourname.' . (parse_url(SITE_URL, PHP_URL_HOST) ?: 'your-domain.com') . '. Public blogs can be found in search engines.'],
  ],
  'Premium & points' => [
    ['What does Premium give me?', 'A premium verified badge and monthly points for a year. Premium+ gives a bigger monthly points allowance. See the Premium page for the current amounts.'],
    ['What can I do with points?', 'Spend them in the Advertising marketplace to boost posts or profiles in Explore and other placements. You can also earn points by inviting people with your referral link.'],
    ['What happens when my year ends?', 'You keep your account and content. The premium badge and monthly points stop until you donate again.'],
  ],
  'Safety & account' => [
    ['How do I report a post or blog?', 'Use the report option on the post or blog, or send us a report from the contact page. Include the link.'],
    ['My account is suspended. What now?', 'Suspended accounts cannot sign in. Send an appeal from the contact page with your blog name and a short explanation.'],
    ['How do I get the Verified badge?', 'Creators who post a lot can request identity verification in their settings by sending a photo ID. Verified creators keep the badge for life.'],
  ],
];
?>
<main class="page" id="main">
  <?php require __DIR__ . '/includes/page-hero.php'; ?>

  <div class="tiles tiles-3">
    <a class="tile" href="<?= e(url('rules.php')) ?>" style="--c:#ffd60a"><?= icon('shield') ?><h3>Community Rules</h3><p>What is allowed and what is not.</p></a>
    <a class="tile" href="<?= e(url('contact.php?topic=report')) ?>" style="--c:#ff1f4b"><?= icon('flag') ?><h3>Report content</h3><p>Flag a post, blog or account.</p></a>
    <a class="tile" href="<?= e(url('contact.php')) ?>" style="--c:#19e3ff"><?= icon('mail') ?><h3>Contact support</h3><p>Talk to a real person.</p></a>
  </div>

  <?php foreach ($faq as $group => $items): ?>
    <section class="faq-group" aria-labelledby="g-<?= e(md5($group)) ?>">
      <h2 id="g-<?= e(md5($group)) ?>" class="sec-title"><?= e($group) ?></h2>
      <?php foreach ($items as [$q, $a]): ?>
        <details class="faq"><summary><?= e($q) ?></summary><p><?= e($a) ?></p></details>
      <?php endforeach; ?>
    </section>
  <?php endforeach; ?>
</main>
<?php require __DIR__ . '/includes/page-end.php'; ?>
