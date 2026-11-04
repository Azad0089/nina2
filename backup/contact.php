<?php
require_once __DIR__ . '/includes/config.php';
$page_title = 'Contact';
$page_desc  = 'Contact ' . site_name() . ' support: questions, reports, appeals, verification, advertising and legal requests.';
$page_path  = 'contact.php';
$active_nav = 'support';
require __DIR__ . '/includes/page-start.php';

$topics = [
    'support'       => 'General support',
    'report'        => 'Report content or a blog',
    'appeal'        => 'Appeal a suspension or ban',
    'verification'  => 'Identity verification',
    'advertising'   => 'Advertising & promotion',
    'premium'       => 'Premium & donations',
    'legal'         => 'Legal, DMCA or privacy request',
    'other'         => 'Something else',
];
$topic = isset($topics[$_GET['topic'] ?? '']) ? $_GET['topic'] : 'support';
$msg   = $_SESSION['flash']['contact'] ?? null;      // ['type' => 'ok'|'error', 'text' => '...']
$hero  = ['title' => 'Contact us', 'lead' => 'Questions, reports and appeals go straight to the site admin.', 'color' => '#19e3ff'];
?>
<main class="page" id="main">
  <?php require __DIR__ . '/includes/page-hero.php'; ?>

  <div class="contact-grid">
    <section class="panel" aria-labelledby="cf-h">
      <h2 id="cf-h">Send a message</h2>

      <?php if ($msg): ?>
        <p class="form-msg <?= ($msg['type'] ?? '') === 'ok' ? 'ok' : '' ?>" role="alert"><?= e($msg['text'] ?? '') ?></p>
      <?php endif; ?>

      <form method="post" action="<?= e(url('actions/contact.php')) ?>" novalidate>
        <?= csrf_field() ?>
        <div class="two">
          <label class="field"><span>Your name</span><input type="text" name="name" autocomplete="name" required></label>
          <label class="field"><span>Your email</span><input type="email" name="email" autocomplete="email" required></label>
        </div>
        <label class="field"><span>Topic</span>
          <select name="topic">
            <?php foreach ($topics as $k => $label): ?><option value="<?= e($k) ?>"<?= $k === $topic ? ' selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
          </select>
        </label>
        <label class="field"><span>Link to the post or blog <em>(for reports and appeals)</em></span>
          <input type="url" name="link" placeholder="https://"></label>
        <label class="field"><span>Message</span>
          <textarea name="message" rows="6" required></textarea></label>
        <?= turnstile_widget() ?>
        <button class="btn btn-fill" type="submit">Send message</button>
      </form>
    </section>

    <aside class="side-info" aria-label="Other ways to get help">
      <a class="tile" href="<?= e(url('contact.php?topic=report')) ?>" style="--c:#ff1f4b"><?= icon('flag') ?><h3>Report content</h3><p>Something breaks the rules? Tell us where to look.</p></a>
      <a class="tile" href="<?= e(url('support.php')) ?>" style="--c:#19e3ff"><?= icon('help') ?><h3>Help center</h3><p>Answers to common questions about blogs, premium and ads.</p></a>
      <a class="tile" href="mailto:<?= e(CONTACT_EMAIL) ?>" style="--c:#ffd60a"><?= icon('mail') ?><h3>Email us</h3><p><?= e(CONTACT_EMAIL) ?></p></a>
      <p class="fine">We usually reply within 2 to 3 business days. Reports of illegal content are handled first.</p>
    </aside>
  </div>
</main>
<?php require __DIR__ . '/includes/page-end.php'; ?>
