<?php
// Your profile IS your main blog's info (bio, links, email) -- edited right there in "My blogs".
require_once __DIR__ . '/../includes/config.php';
require_login();
header('Location: ' . url('dashboard/blogs.php#main'));
exit;
