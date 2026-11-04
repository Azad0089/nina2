<?php require __DIR__ . '/gate.php'; ?>
<?php require __DIR__ . '/modals.php'; ?>

<?php if (TURNSTILE_SITE_KEY !== ''): ?>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<?php endif; ?>
<script>window.NINA = { base: <?= json_encode(BASE_PATH) ?>, openModal: <?= json_encode($open_modal ?? null) ?>, csrf: <?= json_encode(csrf_token()) ?>, loggedIn: <?= json_encode(is_logged_in()) ?>, loginUrl: <?= json_encode(url('login.php')) ?> };</script>
<script src="<?= e(url('js/main.js')) ?>"></script>
</body>
</html>
