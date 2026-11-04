<?php

if (!function_exists('video_embed_from_url')) {
    function video_embed_from_url(?string $url): ?array
    {
        $url = trim((string) $url);
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }
        $parts = parse_url($url);
        $host = strtolower(preg_replace('/^www\./', '', (string) ($parts['host'] ?? '')));
        $path = (string) ($parts['path'] ?? '');
        $query = [];
        parse_str((string) ($parts['query'] ?? ''), $query);
        $embed = null;

        if (in_array($host, ['youtube.com', 'm.youtube.com', 'music.youtube.com', 'youtu.be'], true)) {
            $id = $host === 'youtu.be' ? trim($path, '/') : (string) ($query['v'] ?? '');
            if ($id === '' && preg_match('#/(?:embed|shorts|live)/([\w-]{6,})#', $path, $m)) {
                $id = $m[1];
            }
            if ($id !== '') {
                $embed = 'https://www.youtube.com/embed/' . rawurlencode($id);
            }
        } elseif (str_contains($host, 'vimeo.com') && preg_match('#(\d{6,})#', $path, $m)) {
            $embed = 'https://player.vimeo.com/video/' . $m[1];
        } elseif ((str_contains($host, 'dailymotion.com') || $host === 'dai.ly') && preg_match('#([a-z0-9]{5,})#i', trim($path, '/'), $m)) {
            $embed = 'https://www.dailymotion.com/embed/video/' . $m[1];
        } elseif (str_contains($host, 'pornhub.com')) {
            $id = (string) ($query['viewkey'] ?? '');
            if ($id === '' && preg_match('#/embed/([a-z0-9]+)#i', $path, $m)) {
                $id = $m[1];
            }
            if ($id !== '') {
                $embed = 'https://www.pornhub.com/embed/' . rawurlencode($id);
            }
        } elseif ((str_contains($host, 'xvideos.com') || str_contains($host, 'xvideos.es')) && preg_match('#(?:embedframe/|video\.?)(\d+)#', $path, $m)) {
            $embed = 'https://www.xvideos.com/embedframe/' . $m[1];
        } elseif (str_contains($host, 'xnxx.com') && preg_match('#/(?:embedframe|video-)/?([a-z0-9]+)#i', $path, $m)) {
            $embed = 'https://www.xnxx.com/embedframe/' . $m[1];
        } elseif (str_contains($host, 'xhamster.com') && (preg_match('#/embed/(\d+)#', $path, $m) || preg_match('#-(\d{5,})(?:/|$)#', $path, $m))) {
            $embed = 'https://xhamster.com/embed/' . $m[1];
        } elseif (str_contains($host, 'redtube.com') && preg_match('#(\d{4,})#', $path . (string) ($query['id'] ?? ''), $m)) {
            $embed = 'https://embed.redtube.com/?id=' . $m[1];
        } elseif (str_contains($host, 'youporn.com') && preg_match('#/(?:embed|watch)/(\d+)#', $path, $m)) {
            $embed = 'https://www.youporn.com/embed/' . $m[1];
        } elseif (str_contains($host, 'spankbang.com') && preg_match('#/([a-z0-9]{4,})(?:/|$)#i', $path, $m)) {
            $embed = 'https://spankbang.com/' . $m[1] . '/embed/';
        } elseif (str_contains($host, 'tube8.com') && preg_match('#(\d{5,})#', $path, $m)) {
            $embed = 'https://www.tube8.com/embed/' . $m[1];
        } elseif (str_contains($host, 'eporner.com') && preg_match('#/(?:video-|embed/)([a-z0-9]+)#i', $path, $m)) {
            $embed = 'https://www.eporner.com/embed/' . $m[1];
        } elseif (str_contains($host, 'tnaflix.com') && preg_match('#video(\d+)#', $path, $m)) {
            $embed = 'https://player.tnaflix.com/video/' . $m[1];
        } elseif (str_contains($host, 'motherless.com') && preg_match('#/([A-Za-z0-9]{5,})#', $path, $m)) {
            $embed = 'https://motherless.com/embed/' . $m[1];
        } elseif (str_contains($host, 'drtuber.com') && preg_match('#/video/(\d+)#', $path, $m)) {
            $embed = 'https://www.drtuber.com/embed/' . $m[1];
        } elseif (str_contains($host, 'sunporno.com') && preg_match('#/v/(\d+)#', $path, $m)) {
            $embed = 'https://embeds.sunporno.com/embed/' . $m[1];
        } elseif (preg_match('#/embed#i', $path)) {
            $embed = $url;
        } elseif (preg_match('#\.(mp4|webm|mov|m4v|ogg)(\?.*)?$#i', $path)) {
            return ['provider' => 'file', 'embed_url' => $url, 'source_url' => $url, 'kind' => 'file'];
        }

        if (!$embed) {
            return null;
        }
        return ['provider' => $host, 'embed_url' => $embed, 'source_url' => $url, 'kind' => 'iframe'];
    }
}

if (!function_exists('translation_catalog')) {
    function translation_catalog(): array
    {
        static $catalog;
        if ($catalog === null) {
            $path = function_exists('lang_path') ? lang_path('en.php') : base_path('lang/en.php');
            $catalog = is_file($path) ? (array) include $path : [];
        }
        return $catalog;
    }
}

if (!function_exists('locale_packs')) {
    function locale_packs(): array
    {
        try {
            $raw = \App\Models\Setting::get('locale_packs', '');
            $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
            return is_array($decoded) ? $decoded : [];
        } catch (\Throwable $e) {
            return [];
        }
    }
}

if (!function_exists('enabled_locales')) {
    function enabled_locales(): array
    {
        $locales = ['en' => 'English'];
        foreach (locale_packs() as $code => $pack) {
            $code = strtolower(preg_replace('/[^a-z]/i', '', (string) $code));
            if ($code === '' || $code === 'en' || empty($pack['enabled'])) {
                continue;
            }
            $locales[$code] = trim((string) ($pack['name'] ?? strtoupper($code))) ?: strtoupper($code);
        }
        return $locales;
    }
}

if (!function_exists('current_locale')) {
    function current_locale(): string
    {
        $enabled = enabled_locales();
        $requested = '';
        try {
            $requested = (string) (request()->cookie('nina_locale') ?: session('locale', ''));
        } catch (\Throwable $e) {}
        $requested = strtolower($requested);
        if ($requested !== '' && isset($enabled[$requested])) {
            return $requested;
        }
        try {
            $default = strtolower((string) \App\Models\Setting::get('default_locale', 'en'));
        } catch (\Throwable $e) {
            $default = 'en';
        }
        return isset($enabled[$default]) ? $default : 'en';
    }
}

if (!function_exists('t')) {
    function t(string $key, array $replace = []): string
    {
        $value = null;
        if (current_locale() !== 'en') {
            $pack = locale_packs()[current_locale()]['strings'] ?? [];
            if (is_array($pack) && isset($pack[$key]) && trim((string) $pack[$key]) !== '') {
                $value = (string) $pack[$key];
            }
        }
        if ($value === null) {
            $value = (string) (translation_catalog()[$key] ?? $key);
        }
        foreach ($replace as $name => $replacement) {
            $value = str_replace(':' . $name, (string) $replacement, $value);
        }
        return $value;
    }
}

if (!function_exists('classifieds_enabled')) {
    function classifieds_enabled(): bool
    {
        try {
            return (string) \App\Models\Setting::get('classifieds_enabled', '1') === '1';
        } catch (\Throwable $e) {
            return true;
        }
    }
}

if (!function_exists('ensure_platform_tables')) {
    function ensure_platform_tables(): void
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;
        try {
            $schema = \Illuminate\Support\Facades\Schema::class;
            if ($schema::hasTable('posts') && !$schema::hasColumn('posts', 'member_community_id')) {
                $schema::table('posts', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->unsignedBigInteger('member_community_id')->nullable()->index();
                });
            }
            if ($schema::hasTable('posts') && !$schema::hasColumn('posts', 'is_pinned')) {
                $schema::table('posts', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->boolean('is_pinned')->default(false);
                });
            }
            if (!$schema::hasTable('member_communities')) {
                $schema::create('member_communities', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    private_message_user_id($table, 'owner_id');
                    $table->string('slug', 80)->unique();
                    $table->string('title', 160);
                    $table->string('tagline', 255)->nullable();
                    $table->text('bio')->nullable();
                    $table->string('color', 16)->default('#ff2bd6');
                    $table->string('avatar_path', 255)->nullable();
                    $table->string('cover_path', 255)->nullable();
                    $table->string('website1', 255)->nullable();
                    $table->string('website2', 255)->nullable();
                    $table->string('website3', 255)->nullable();
                    $table->string('contact_email', 190)->nullable();
                    $table->string('join_mode', 20)->default('open');
                    $table->string('status', 20)->default('active');
                    $table->timestamps();
                });
            }
            if (!$schema::hasTable('member_community_users')) {
                $schema::create('member_community_users', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('community_id')->index();
                    private_message_user_id($table, 'user_id');
                    $table->string('role', 20)->default('member');
                    $table->string('status', 20)->default('active');
                    $table->timestamp('created_at')->useCurrent();
                    $table->unique(['community_id', 'user_id']);
                });
            }
            if (!$schema::hasTable('classified_ads')) {
                $schema::create('classified_ads', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    private_message_user_id($table, 'user_id');
                    $table->string('title', 180);
                    $table->mediumText('description');
                    $table->json('tags')->nullable();
                    $table->string('price', 80)->nullable();
                    $table->string('contact_email', 190)->nullable();
                    $table->string('phone', 60)->nullable();
                    $table->string('location', 160)->nullable();
                    $table->string('image_one', 255)->nullable();
                    $table->string('image_two', 255)->nullable();
                    $table->string('status', 20)->default('published');
                    $table->timestamp('expires_at')->nullable()->index();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            $done = false;
        }
    }
}
