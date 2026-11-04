  </main>
</div>
<script>window.NINA = { base: <?= json_encode(BASE_PATH) ?>, csrf: <?= json_encode(csrf_token()) ?>, loggedIn: true, loginUrl: <?= json_encode(url('login.php')) ?> };</script>
</body>
</html>
