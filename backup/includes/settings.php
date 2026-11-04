<?php
declare(strict_types=1);
/** Site settings, cached per-request. Values live in the `settings` table and are edited from Admin > Settings. */

function all_settings(): array {
    static $cache = null;
    if ($cache !== null) return $cache;
    $cache = [];
    foreach (db()->query('SELECT skey, svalue FROM settings') as $row) {
        $cache[$row['skey']] = $row['svalue'];
    }
    return $cache;
}
function setting(string $key, string $default = ''): string {
    $all = all_settings();
    return $all[$key] ?? $default;
}
function setting_int(string $key, int $default = 0): int {
    $v = setting($key, (string)$default);
    return is_numeric($v) ? (int)$v : $default;
}
function setting_bool(string $key, bool $default = false): bool {
    $v = setting($key, $default ? '1' : '0');
    return $v === '1' || strtolower($v) === 'true';
}
function set_setting(string $key, string $value): void {
    $stmt = db()->prepare('INSERT INTO settings (skey, svalue) VALUES (?, ?) ON DUPLICATE KEY UPDATE svalue = VALUES(svalue)');
    $stmt->execute([$key, $value]);
}

function site_name(): string { return setting('site_name', 'Nina.Sucks'); }
function site_meta_description(): string { return setting('meta_description', ''); }
function site_logo_url(): string {
    $p = setting('logo_path', '');
    return $p !== '' ? media_url($p) : asset('logo.png');
}
function site_favicon_url(string $size): string {
    $p = setting('favicon_path', '');
    if ($p !== '') return media_url($p);
    return match ($size) {
        '32'  => asset('favicon-32.png'),
        '512' => asset('favicon-512.png'),
        'apple' => asset('apple-touch-icon.png'),
        default => asset('favicon-192.png'),
    };
}
function site_color_vars(): string {
    $p = e(setting('primary_color', '#ff2bd6'));
    $s = e(setting('secondary_color', '#19e3ff'));
    return "<style>:root{--pink:{$p};--cyan:{$s};}</style>";
}
