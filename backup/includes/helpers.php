<?php
/** Small template helpers. */

/** Inline SVG icon from the sprite in icons.php: icon('heart') */
function icon(string $id, string $class = ''): string {
    return '<svg class="ic' . ($class ? ' ' . e($class) : '') . '" aria-hidden="true"><use href="#i-' . e($id) . '"/></svg>';
}

/** Badge icon for premium / id / vip (empty string if none) */
function badge(?string $type): string {
    return match ($type) {
        'pro' => '<svg class="ic bd bd-pro" role="img" aria-label="Premium member"><use href="#i-crown"/></svg>',
        'id'  => '<svg class="ic bd bd-id" role="img" aria-label="Identity verified"><use href="#i-verified"/></svg>',
        'vip' => '<svg class="ic bd bd-vip" role="img" aria-label="VIP"><use href="#i-star"/></svg>',
        default => '',
    };
}

/**
 * Inline style for a cropped image: <span class="crop" style="<?= crop_style($c) ?>"><img ...></span>
 * $c = ['x' => 43, 'y' => 22, 'z' => 1.6, 'ratio' => '4/5']   (x/y focus point in %, z zoom, ratio optional)
 * For normal uncropped images use x=50, y=50, z=1.
 */
function crop_style(array $c): string {
    $x = (float)($c['x'] ?? 50); $y = (float)($c['y'] ?? 50); $z = (float)($c['z'] ?? 1);
    $s = "--px:{$x}%;--py:{$y}%;--z:{$z};";
    if (!empty($c['ratio'])) { $s .= 'aspect-ratio:' . $c['ratio'] . ';'; }
    return $s;
}

function fmt_count(int $n): string {
    return $n >= 1000 ? rtrim(rtrim(number_format($n / 1000, 1), '0'), '.') . 'k' : (string)$n;
}
function time_ago(int $minutes): string {
    if ($minutes < 60)    return $minutes . 'm';
    if ($minutes < 1440)  return intdiv($minutes, 60) . 'h';
    if ($minutes < 10080) return intdiv($minutes, 1440) . 'd';
    return intdiv($minutes, 10080) . 'w';
}

/** Absolute URL of the current page's canonical address */
function canonical(string $path = ''): string {
    return SITE_URL . url($path);
}

/** First letter of a name, upper-cased (works with or without the mbstring extension) */
function initial(string $name): string {
    $c = function_exists('mb_substr') ? mb_substr($name, 0, 1) : substr($name, 0, 1);
    return function_exists('mb_strtoupper') ? mb_strtoupper($c) : strtoupper($c);
}
