<?php
/**
 * Start of every inner page. Set these before including it:
 *   $page_title, $page_desc, $page_path (e.g. 'terms.php'), $active_nav (optional)
 */
require_once __DIR__ . '/config.php';
$page_title     = ($page_title ?? 'Page') . ' | ' . site_name();
$page_desc      = $page_desc ?? site_name() . ' adult blogging community.';
$page_canonical = canonical($page_path ?? '');
$active_nav     = $active_nav ?? '';
require __DIR__ . '/head.php';
require __DIR__ . '/header.php';
