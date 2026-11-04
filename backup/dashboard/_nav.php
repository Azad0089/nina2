<?php
/** Shared sidebar nav for every /dashboard page. Include after require_login(). */
$app_section = 'dashboard';
$app_nav = [
    ['key' => 'overview', 'label' => 'Overview',  'href' => url('dashboard/'),           'icon' => 'grid'],
    ['key' => 'posts',    'label' => 'My posts',  'href' => url('dashboard/posts.php'),  'icon' => 'text'],
    ['key' => 'new',      'label' => 'New post',  'href' => url('dashboard/post.php'),   'icon' => 'plus'],
    ['key' => 'blogs',    'label' => 'My blogs',  'href' => url('dashboard/blogs.php'),  'icon' => 'compass'],
    ['key' => 'profile',  'label' => 'Profile',   'href' => url('dashboard/profile.php'),'icon' => 'users'],
    ['key' => 'referral', 'label' => 'Referrals', 'href' => url('dashboard/referrals.php'), 'icon' => 'share'],
    ['key' => 'account',  'label' => 'Account',   'href' => url('dashboard/settings.php'),'icon' => 'settings'],
];
