<?php

if (!function_exists('site_name')) {
    function site_name() {
        try {
            $name = \App\Models\Setting::get('site_title');
            if ($name) return $name;
        } catch (\Throwable $e) {}
        return config('app.name', 'Nina.Sucks');
    }
}

if (!function_exists('site_logo_url')) {
    function site_logo_url() {
        try {
            $logo = \App\Models\Setting::get('site_logo');
            if ($logo) return asset($logo);
        } catch (\Throwable $e) {}
        return asset('assets/logo.png');
    }
}

if (!function_exists('media_url')) {
    function media_url($path) {
        if (!$path) return asset('assets/hero.jpg');
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
        return asset('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('initial')) {
    function initial($str) {
        $str = trim((string)$str);
        return $str !== '' ? strtoupper(mb_substr($str, 0, 1)) : '?';
    }
}

if (!function_exists('icon')) {
    function icon($name) {
        return '<svg class="ic"><use href="#i-' . htmlspecialchars($name) . '"/></svg>';
    }
}

if (!function_exists('badge')) {
    function badge($badge) {
        if (!$badge) return '';
        if ($badge === 'pro') return '<span class="badge b-pro bd bd-pro" title="Premium verified">' . icon('verified') . ' PRO</span>';
        if ($badge === 'id') return '<span class="badge b-id bd bd-id" title="Identity verified">' . icon('verified') . ' ID</span>';
        if ($badge === 'vip') return '<span class="badge b-vip bd bd-vip" title="VIP member">' . icon('star') . ' VIP</span>';
        return '';
    }
}

if (!function_exists('crop_style')) {
    function crop_style($crop = null) {
        if (!is_array($crop)) return '--px:50%;--py:50%;--s:1';
        $px = $crop['px'] ?? 50;
        $py = $crop['py'] ?? 50;
        $s  = $crop['scale'] ?? 1;
        return "--px:{$px}%;--py:{$py}%;--s:{$s}";
    }
}

if (!function_exists('fmt_count')) {
    function fmt_count($num) {
        $num = (int)$num;
        if ($num >= 1000000) return round($num / 1000000, 1) . 'M';
        if ($num >= 1000) return round($num / 1000, 1) . 'k';
        return (string)$num;
    }
}

if (!function_exists('time_ago')) {
    function time_ago($dateOrMinutes) {
        if (is_numeric($dateOrMinutes)) {
            $m = (int)$dateOrMinutes;
            if ($m < 60) return $m . 'm';
            if ($m < 1440) return floor($m / 60) . 'h';
            return floor($m / 1440) . 'd';
        }
        if ($dateOrMinutes instanceof \Carbon\Carbon) {
            return $dateOrMinutes->diffForHumans(null, true);
        }
        return '5m';
    }
}

if (!function_exists('nav_items')) {
    function nav_items() {
        return [
            ['href' => '/', 'label' => 'Explore', 'icon' => 'compass', 'key' => 'explore'],
            ['href' => '/community', 'label' => 'Community', 'icon' => 'users', 'key' => 'community'],
            ['href' => '/trending', 'label' => 'Trending', 'icon' => 'flame', 'key' => 'trending'],
            ['href' => '/advertising', 'label' => 'Advertising', 'icon' => 'megaphone', 'key' => 'advertising'],
            ['href' => '/premium', 'label' => 'Premium', 'icon' => 'crown', 'key' => 'premium'],
            ['href' => '/support', 'label' => 'Support', 'icon' => 'help', 'key' => 'support'],
        ];
    }
}

if (!function_exists('footer_links')) {
    function footer_links() {
        return [
            ['href' => '/about', 'label' => 'About'],
            ['href' => '/faq', 'label' => 'FAQ'],
            ['href' => '/terms', 'label' => 'Terms'],
            ['href' => '/privacy', 'label' => 'Privacy'],
            ['href' => '/rules', 'label' => 'Rules'],
            ['href' => '/compliance', 'label' => '2257 Compliance'],
            ['href' => '/safety', 'label' => 'Safety'],
            ['href' => '/dmca', 'label' => 'DMCA'],
            ['href' => '/cookies', 'label' => 'Cookies'],
            ['href' => '/contact', 'label' => 'Contact'],
        ];
    }
}

if (!function_exists('post_types')) {
    function post_types() {
        return [
            'text'  => ['Text', 'text'],
            'photo' => ['Photo', 'photo'],
            'quote' => ['Quote', 'quote'],
            'link'  => ['Link', 'link'],
            'chat'  => ['Chat', 'chat'],
            'video' => ['Video', 'video'],
        ];
    }
}

if (!function_exists('hero_slides')) {
    function hero_slides() {
        try {
            $saved = \App\Models\Setting::get('hero_slides');
            if ($saved) {
                $decoded = is_string($saved) ? json_decode($saved, true) : $saved;
                if (!empty($decoded) && is_array($decoded)) {
                    return $decoded;
                }
            }
        } catch (\Throwable $e) {}

        $isLoggedIn = auth()->check();

        return [
            [
                'title' => 'Follow your desires.',
                'text'  => 'An adult microblogging community. Post photos, text, video, quotes and chats. Fully responsive and Tumblr-inspired.',
                'img'   => 'hero.jpg',
                'alt'   => 'Platform highlights',
                'color' => '#ff2bd6',
                'x' => 50, 'y' => 30, 'z' => 1.05, 'mx' => 50, 'my' => 25, 'mz' => 1.15,
                'cta'   => $isLoggedIn ? [
                    ['label' => 'Create new post', 'href' => '/dashboard/post', 'class' => 'btn-fill'],
                    ['label' => 'Explore feed', 'href' => '#feed', 'class' => 'btn-line'],
                ] : [
                    ['label' => 'Create blog', 'href' => '/register', 'class' => 'btn-fill', 'modal' => 'register'],
                    ['label' => 'Explore feed', 'href' => '#feed', 'class' => 'btn-line'],
                ]
            ],
            [
                'title' => 'Earn & promote with points.',
                'text'  => 'Collect points via membership or referrals. Spend points in our ad marketplace to get 1,000 to 100,000 views on your posts.',
                'img'   => 'hero.jpg',
                'alt'   => 'Ad Marketplace',
                'color' => '#19e3ff',
                'x' => 50, 'y' => 50, 'z' => 1.1, 'mx' => 50, 'my' => 40, 'mz' => 1.2,
                'cta'   => [
                    ['label' => 'View Marketplace', 'href' => '/advertising', 'class' => 'btn-fill'],
                    ['label' => 'Go Premium', 'href' => '/premium', 'class' => 'btn-line'],
                ]
            ],
            [
                'title' => 'Customized Creator Blogs.',
                'text'  => 'Create as many blogs as you want. Each with their own colors, bios, website links, custom avatar and banner styling.',
                'img'   => 'hero.jpg',
                'alt'   => 'Customized creator blogs',
                'color' => '#ffd60a',
                'x' => 50, 'y' => 45, 'z' => 1.0, 'mx' => 50, 'my' => 35, 'mz' => 1.1,
                'cta'   => $isLoggedIn ? [
                    ['label' => 'Creator Studio', 'href' => '/dashboard', 'class' => 'btn-fill'],
                    ['label' => 'Trending Blogs', 'href' => '/trending', 'class' => 'btn-line'],
                ] : [
                    ['label' => 'Start Now', 'href' => '/register', 'class' => 'btn-fill', 'modal' => 'register'],
                    ['label' => 'Trending Blogs', 'href' => '/trending', 'class' => 'btn-line'],
                ]
            ],
        ];
    }
}
