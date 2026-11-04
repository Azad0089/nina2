<?php
/** Shared sidebar nav for every /admin page. Include after require_admin(). */
$app_section = 'admin';
$app_nav = [
    ['key' => 'overview',   'label' => 'Overview',    'href' => url('admin/'),               'icon' => 'grid'],
    ['key' => 'users',      'label' => 'Users',       'href' => url('admin/users.php'),      'icon' => 'users'],
    ['key' => 'blogs',      'label' => 'Blogs',       'href' => url('admin/blogs.php'),      'icon' => 'compass'],
    ['key' => 'posts',      'label' => 'Posts',       'href' => url('admin/posts.php'),      'icon' => 'photo'],
    ['key' => 'reports',    'label' => 'Reports',     'href' => url('admin/reports.php'),    'icon' => 'flag'],
    ['key' => 'tags',       'label' => 'Tags',        'href' => url('admin/tags.php'),       'icon' => 'tag'],
    ['key' => 'categories', 'label' => 'Categories',  'href' => url('admin/categories.php'), 'icon' => 'grid'],
    ['key' => 'support',    'label' => 'Support',     'href' => url('admin/support.php'),    'icon' => 'mail'],
    ['key' => 'promotions', 'label' => 'Promotions',  'href' => url('admin/promotions.php'), 'icon' => 'megaphone'],
    ['key' => 'settings',   'label' => 'Settings',    'href' => url('admin/settings.php'),   'icon' => 'settings'],
];
