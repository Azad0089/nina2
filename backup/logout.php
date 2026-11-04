<?php
require __DIR__ . '/includes/config.php';
log_out_user();
header('Location: ' . url('/'));
exit;
