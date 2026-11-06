<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// A copy with no application key is a new install. An existing site keeps its key and is left alone.
$ninaRoot = dirname(__DIR__);
$ninaInstalled = is_file($ninaRoot.'/storage/framework/installed');
$ninaEnv = is_file($ninaRoot.'/.env') ? (string) file_get_contents($ninaRoot.'/.env') : '';
$ninaHasKey = (bool) preg_match('/^APP_KEY=base64:[A-Za-z0-9+\/=]{20,}/m', $ninaEnv);
if (!$ninaInstalled && !$ninaHasKey && is_file(__DIR__.'/install.php')) {
    $ninaBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/install.php')), '/');
    header('Location: '.$ninaBase.'/install.php');
    exit;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
