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

if (!function_exists('site_domain')) {
    function site_domain() {
        try {
            $domain = \App\Models\Setting::get('site_domain');
            if ($domain) return $domain;
        } catch (\Throwable $e) {}
        $appUrl = config('app.url', 'http://localhost:3000');
        $host = parse_url($appUrl, PHP_URL_HOST);
        $port = parse_url($appUrl, PHP_URL_PORT);
        if ($host && $host !== 'localhost' && $host !== '127.0.0.1') {
            return $port ? "{$host}:{$port}" : $host;
        }
        return 'nina.sucks';
    }
}

if (!function_exists('user_url')) {
    function user_url($user) {
        $username = is_object($user) ? ($user->username ?? '') : (string)$user;
        $username = trim($username);
        return url('/u/' . $username);
    }
}

if (!function_exists('blog_url')) {
    function blog_url($blog, $path = '') {
        if (empty($blog)) return url('/');
        $cleanPath = $path ? '/' . ltrim($path, '/') : '';
        
        if (is_object($blog)) {
            $user = $blog->user ?? null;
            if (!$user && !empty($blog->user_id)) {
                try {
                    $user = \App\Models\User::find($blog->user_id);
                } catch (\Throwable $e) {}
            }
            $username = $user ? $user->username : ($blog->slug ?? '');
            $slug = $blog->slug ?? $username;
            
            // If it's the main blog or slug matches username, it lives at /u/username
            if (!empty($blog->is_main) || strtolower($slug) === strtolower($username)) {
                return url('/u/' . $username . $cleanPath);
            }
            // Additional blogs live at /u/username/blog-slug
            return url('/u/' . $username . '/' . $slug . $cleanPath);
        }

        $slug = (string)$blog;
        return url('/u/' . $slug . $cleanPath);
    }
}

if (!function_exists('blog_subdomain')) {
    function blog_subdomain($blog) {
        if (is_object($blog)) {
            $user = $blog->user ?? null;
            $username = $user ? $user->username : ($blog->slug ?? '');
            $slug = $blog->slug ?? $username;
            if (!empty($blog->is_main) || strtolower($slug) === strtolower($username)) {
                return "/u/{$username}";
            }
            return "/u/{$username}/{$slug}";
        }
        return "/u/" . (string)$blog;
    }
}

if (!function_exists('blog_path_url')) {
    function blog_path_url($blog, $path = '') {
        return blog_url($blog, $path);
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
    function media_url($path)
    {
        if (!$path) {
            return asset('assets/hero.jpg');
        }

        // Base64 image
        if (str_starts_with($path, 'data:image/')) {
            return $path;
        }

        // External URL
        if (str_starts_with($path, 'http://') ||
            str_starts_with($path, 'https://')) {
            return $path;
        }

        $path = ltrim($path, '/');

        // Already a storage path
        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        // Laravel public disk paths
        return asset('storage/' . $path);
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

        $badgeKeys = [];

        if ($badge instanceof \App\Models\User) {
            if (!empty($badge->badge_vip)) $badgeKeys['vip'] = true;
            if (!empty($badge->badge_id)) $badgeKeys['id'] = true;
            if (!empty($badge->badge_pro)) $badgeKeys['pro'] = true;
            if (!empty($badge->badge)) {
                $parts = preg_split('/[,\s]+/', strtolower(trim((string)$badge->badge)));
                foreach ($parts as $p) {
                    $p = trim($p);
                    if ($p === 'vip') $badgeKeys['vip'] = true;
                    if ($p === 'id' || $p === 'verified') $badgeKeys['id'] = true;
                    if ($p === 'pro' || $p === 'premium') $badgeKeys['pro'] = true;
                }
            }
        } elseif ($badge instanceof \App\Models\Blog) {
            if ($badge->user) {
                return badge($badge->user);
            }
            if (!empty($badge->badge)) {
                return badge($badge->badge);
            }
            return '';
        } elseif ($badge instanceof \App\Models\Post) {
            if ($badge->blog) {
                return badge($badge->blog);
            }
            if (!empty($badge->badge)) {
                return badge($badge->badge);
            }
            return '';
        } elseif (is_array($badge)) {
            foreach ($badge as $item) {
                if (is_string($item)) {
                    $item = strtolower(trim($item));
                    if ($item === 'vip') $badgeKeys['vip'] = true;
                    if ($item === 'id' || $item === 'verified') $badgeKeys['id'] = true;
                    if ($item === 'pro' || $item === 'premium') $badgeKeys['pro'] = true;
                }
            }
        } else {
            $bStr = strtolower(trim((string)$badge));
            if (str_contains($bStr, ',') || str_contains($bStr, ' ')) {
                $parts = preg_split('/[,\s]+/', $bStr);
                foreach ($parts as $p) {
                    $p = trim($p);
                    if ($p === 'vip') $badgeKeys['vip'] = true;
                    if ($p === 'id' || $p === 'verified') $badgeKeys['id'] = true;
                    if ($p === 'pro' || $p === 'premium') $badgeKeys['pro'] = true;
                }
            } else {
                if ($bStr === 'vip') $badgeKeys['vip'] = true;
                if ($bStr === 'id' || $bStr === 'verified') $badgeKeys['id'] = true;
                if ($bStr === 'pro' || $bStr === 'premium') $badgeKeys['pro'] = true;
            }
        }

        $html = '';
        if (!empty($badgeKeys['vip'])) {
            $html .= '<span class="badge b-vip" title="VIP: community favorite">' . icon('star') . '<span>VIP</span></span> ';
        }
        if (!empty($badgeKeys['id'])) {
            $html .= '<span class="badge b-id" title="Verified: ID-checked creator">' . icon('verified') . '<span>ID</span></span> ';
        }
        if (!empty($badgeKeys['pro'])) {
            $html .= '<span class="badge b-pro" title="Premium: yearly supporter">' . icon('crown') . '<span>PRO</span></span> ';
        }
        return trim($html);
    }
}

if (!function_exists('badge_legend')) {
    function badge_legend() {
        return [
            ['badge' => 'pro', 'icon' => 'crown', 'color' => '#ffd60a', 'text' => 'Premium: yearly supporter'],
            ['badge' => 'id', 'icon' => 'verified', 'color' => '#19e3ff', 'text' => 'Verified: ID-checked creator'],
            ['badge' => 'vip', 'icon' => 'star', 'color' => '#ff2bd6', 'text' => 'VIP: community favorite'],
        ];
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
            ['href' => '/support', 'label' => 'Support'],
            ['href' => '/contact', 'label' => 'Contact Support'],
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
                'text'  => 'An adult microblogging community. Post photos, text, video, quotes and chats. Uncensored, creative & free.',
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

if (!function_exists('publish_matured_queued_posts')) {
    function publish_matured_queued_posts() {
        try {
            $now = now();
            return \App\Models\Post::where('status', 'queued')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', $now)
                ->update(['status' => 'published']);
        } catch (\Throwable $e) {
            return 0;
        }
    }
}

if (!function_exists('send_smtp_email')) {
    function send_smtp_email($to, $subject, $bodyHtml) {
        $mailMethod = \App\Models\Setting::get('mail_method', 'auto');
        $smtpEnabled = \App\Models\Setting::get('smtp_enabled', '0');
        $fromEmail = \App\Models\Setting::get('smtp_from_email', 'welcome@nina.sucks');
        $fromName = \App\Models\Setting::get('smtp_from_name', 'Nina Microblogging');

        // Helper function for sending via PHP native mail()
        $sendViaPhpMail = function() use ($to, $subject, $bodyHtml, $fromEmail, $fromName) {
            $headers = [];
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=UTF-8';
            $headers[] = 'From: ' . $fromName . ' <' . $fromEmail . '>';
            $headers[] = 'Reply-To: ' . $fromEmail;
            $headers[] = 'X-Mailer: PHP/' . (function_exists('phpversion') ? phpversion() : '8.2');

            $headersStr = implode("\r\n", $headers);
            $sent = @mail($to, $subject, $bodyHtml, $headersStr);
            if ($sent) {
                return ['success' => true, 'transport' => 'php_mail', 'message' => 'Email sent via PHP mail() function.'];
            }
            return ['success' => false, 'transport' => 'php_mail', 'reason' => 'PHP mail() returned false or local MTA is not configured.'];
        };

        // If explicit PHP mail() mode selected
        if ($mailMethod === 'php_mail') {
            return $sendViaPhpMail();
        }

        // Check if SMTP is enabled
        if ($smtpEnabled === '1' || $mailMethod === 'smtp' || $mailMethod === 'auto') {
            $host = \App\Models\Setting::get('smtp_host', '');
            if (!empty($host)) {
                $port = (int)\App\Models\Setting::get('smtp_port', 587);
                $username = \App\Models\Setting::get('smtp_username', '');
                $password = \App\Models\Setting::get('smtp_password', '');
                $encryption = \App\Models\Setting::get('smtp_encryption', 'tls');

                try {
                    config([
                        'mail.default' => 'smtp',
                        'mail.mailers.smtp.host' => $host,
                        'mail.mailers.smtp.port' => $port,
                        'mail.mailers.smtp.username' => $username,
                        'mail.mailers.smtp.password' => $password,
                        'mail.mailers.smtp.encryption' => ($encryption === 'none' ? null : $encryption),
                        'mail.from.address' => $fromEmail,
                        'mail.from.name' => $fromName,
                    ]);

                    \Illuminate\Support\Facades\Mail::html($bodyHtml, function ($message) use ($to, $subject, $fromEmail, $fromName) {
                        $message->to($to)
                                ->from($fromEmail, $fromName)
                                ->subject($subject);
                    });

                    return ['success' => true, 'transport' => 'smtp', 'message' => 'Email sent successfully via SMTP!'];
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning("SMTP Error: " . $e->getMessage() . ". Attempting PHP mail() fallback.");
                    if ($mailMethod === 'auto') {
                        $phpRes = $sendViaPhpMail();
                        if ($phpRes['success']) {
                            return $phpRes;
                        }
                    }
                    return ['success' => false, 'transport' => 'smtp', 'reason' => 'SMTP error: ' . $e->getMessage()];
                }
            } elseif ($mailMethod === 'auto') {
                return $sendViaPhpMail();
            }
        }

        if ($smtpEnabled !== '1' && $mailMethod !== 'php_mail') {
            return ['success' => false, 'reason' => 'Email delivery is disabled. Enable SMTP Mail or choose PHP mail() in Admin settings.'];
        }

        return ['success' => false, 'reason' => 'Email could not be delivered. Please check mail host and credentials.'];
    }
}

if (!function_exists('send_welcome_email')) {
    function send_welcome_email($toEmail, $username, $blogSlug = null) {
        $welcomeEnabled = \App\Models\Setting::get('welcome_email_enabled', '1');
        if ($welcomeEnabled === '0') {
            return ['success' => false, 'reason' => 'Welcome email sending is disabled in settings.'];
        }

        $siteTitle = \App\Models\Setting::get('site_title', 'Nina.Sucks');
        $blogSlug = $blogSlug ?: $username;
        $blogUrl = url("/blog.php?u=" . urlencode($blogSlug));
        $loginUrl = url("/login.php");
        $supportEmail = \App\Models\Setting::get('community_email', 'support@nina.sucks');

        $defaultSubj = 'Welcome to {site_title}, @{username}!';
        $defaultBody = '<div style="max-width:600px;margin:0 auto;background:#0c0617;color:#f2e9f8;font-family:sans-serif;border:1px solid #ff2bd6;border-radius:12px;padding:28px;"><div style="text-align:center;margin-bottom:24px;"><h1 style="color:#ff2bd6;margin:0 0 8px;font-size:28px;">Welcome to {site_title}! 🎉</h1><p style="color:#19e3ff;font-size:16px;margin:0;">Your creator blog @{username} is now live.</p></div><p style="font-size:15px;line-height:1.6;color:#e8daf2;">Hi <b>@{username}</b>,</p><p style="font-size:15px;line-height:1.6;color:#e8daf2;">Thank you for joining {site_title}, the uncensored creator community. You have been awarded <b>100 starter points</b> to kickstart your posts and boost your reach.</p><div style="background:rgba(255,255,255,0.05);border:1px dashed rgba(255,43,214,0.4);border-radius:8px;padding:18px;margin:20px 0;text-align:center;"><p style="margin:0 0 12px;font-size:14px;color:#a291b5;">Your Personal Blog URL:</p><a href="{blog_url}" style="display:inline-block;background:#ff2bd6;color:#000;font-weight:bold;text-decoration:none;padding:12px 24px;border-radius:6px;font-size:15px;">Visit @{username}\'s Blog</a></div><p style="font-size:14px;line-height:1.5;color:#a291b5;">Log in anytime at <a href="{login_url}" style="color:#19e3ff;">{login_url}</a> to share photos, videos, quotes, and interact with other blogs.</p><hr style="border:0;border-top:1px solid rgba(255,255,255,0.1);margin:24px 0;"><p style="font-size:12px;color:#76648c;text-align:center;margin:0;">Sent by {site_title} System. Need help? Contact <a href="mailto:{support_email}" style="color:#19e3ff;">{support_email}</a></p></div>';

        $subjectTpl = \App\Models\Setting::get('welcome_email_subject', $defaultSubj);
        $bodyTpl = \App\Models\Setting::get('welcome_email_body', $defaultBody);

        $replacements = [
            '{username}' => $username,
            '{site_title}' => $siteTitle,
            '{blog_url}' => $blogUrl,
            '{login_url}' => $loginUrl,
            '{support_email}' => $supportEmail,
        ];

        $subject = str_replace(array_keys($replacements), array_values($replacements), $subjectTpl);
        $bodyHtml = str_replace(array_keys($replacements), array_values($replacements), $bodyTpl);

        return send_smtp_email($toEmail, $subject, $bodyHtml);
    }
}

if (!function_exists('mask_email')) {
    function mask_email($email) {
        $parts = explode('@', (string)$email);
        if (count($parts) !== 2) return $email;
        $local = $parts[0];
        $domain = $parts[1];
        if (strlen($local) <= 2) {
            return substr($local, 0, 1) . '*@' . $domain;
        }
        $maskLen = min(strlen($local) - 2, 4);
        return substr($local, 0, 1) . str_repeat('*', $maskLen) . substr($local, -1) . '@' . $domain;
    }
}

if (!function_exists('send_password_reset_email')) {
    function send_password_reset_email($toEmail, $username, $resetUrl) {
        $siteTitle = \App\Models\Setting::get('site_title', 'Nina.Sucks');
        $supportEmail = \App\Models\Setting::get('community_email', 'support@nina.sucks');
        $subject = "Reset Your Password - {$siteTitle}";

        $bodyHtml = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="margin:0;padding:24px 12px;background-color:#07030d;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#f3e8ff;">
  <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:560px;background:#0f0716;border:1px solid rgba(255,43,214,0.3);border-radius:12px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,0.6);">
    <tr>
      <td style="padding:28px 24px 20px;text-align:center;border-bottom:1px solid rgba(255,255,255,0.08);background:linear-gradient(180deg, rgba(255,43,214,0.12) 0%, transparent 100%);">
        <h1 style="color:#ff2bd6;margin:0 0 6px;font-size:26px;font-weight:900;letter-spacing:-0.02em;">{$siteTitle}</h1>
        <p style="color:#19e3ff;font-size:13px;font-weight:700;margin:0;text-transform:uppercase;letter-spacing:0.05em;">Password Reset Instructions</p>
      </td>
    </tr>
    <tr>
      <td style="padding:28px 24px;">
        <p style="font-size:15px;line-height:1.6;color:#ffffff;margin:0 0 16px;">Hello <strong>@{$username}</strong>,</p>
        <p style="font-size:14px;line-height:1.6;color:#cbd5e1;margin:0 0 24px;">We received a request to reset the password for your account on <strong>{$siteTitle}</strong>. Click the button below to choose a new password:</p>
        <div style="text-align:center;margin:32px 0;">
          <a href="{$resetUrl}" style="background:linear-gradient(135deg, #ff2bd6 0%, #19e3ff 100%);color:#07030d;font-weight:800;font-size:15px;padding:14px 32px;text-decoration:none;border-radius:30px;display:inline-block;box-shadow:0 0 20px rgba(255,43,214,0.4);letter-spacing:0.02em;">Reset My Password</a>
        </div>
        <p style="font-size:13px;line-height:1.6;color:#94a3b8;margin:24px 0 8px;">Or copy and paste this link into your web browser:</p>
        <div style="background:#07030d;border:1px solid rgba(255,255,255,0.1);border-radius:6px;padding:12px;word-break:break-all;font-family:monospace;font-size:12px;color:#19e3ff;">
          <a href="{$resetUrl}" style="color:#19e3ff;text-decoration:underline;">{$resetUrl}</a>
        </div>
        <p style="font-size:12px;line-height:1.5;color:#64748b;margin:24px 0 0;padding-top:16px;border-top:1px solid rgba(255,255,255,0.08);">
          This password reset link will expire in 60 minutes. If you did not request a password reset, you can safely disregard this email. Your current password will remain unchanged.
        </p>
      </td>
    </tr>
    <tr>
      <td style="padding:16px 24px;background:#07030d;text-align:center;border-top:1px solid rgba(255,255,255,0.06);font-size:11px;color:#64748b;">
        Sent by {$siteTitle} System &middot; Need help? Contact <a href="mailto:{$supportEmail}" style="color:#a78bfa;text-decoration:none;">{$supportEmail}</a>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

        return send_smtp_email($toEmail, $subject, $bodyHtml);
    }
}

if (!function_exists('send_verification_email')) {
    function send_verification_email($toEmail, $username, $verifyUrl) {
        $siteTitle = \App\Models\Setting::get('site_title', 'Nina.Sucks');
        $supportEmail = \App\Models\Setting::get('community_email', 'support@nina.sucks');
        $subjectTpl = \App\Models\Setting::get('email_verification_subject', 'Verify your email address - {site_title}');
        $subject = str_replace(['{username}', '{site_title}'], [$username, $siteTitle], $subjectTpl);

        $bodyHtml = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="margin:0;padding:24px 12px;background-color:#07030d;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#f3e8ff;">
  <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:560px;background:#0f0716;border:1px solid rgba(25,227,255,0.35);border-radius:12px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,0.6);">
    <tr>
      <td style="padding:28px 24px 20px;text-align:center;border-bottom:1px solid rgba(255,255,255,0.08);background:linear-gradient(180deg, rgba(25,227,255,0.12) 0%, transparent 100%);">
        <h1 style="color:#19e3ff;margin:0 0 6px;font-size:26px;font-weight:900;letter-spacing:-0.02em;">{$siteTitle}</h1>
        <p style="color:#ff2bd6;font-size:13px;font-weight:700;margin:0;text-transform:uppercase;letter-spacing:0.05em;">Email Verification Required</p>
      </td>
    </tr>
    <tr>
      <td style="padding:28px 24px;">
        <p style="font-size:15px;line-height:1.6;color:#ffffff;margin:0 0 16px;">Hello <strong>@{$username}</strong>,</p>
        <p style="font-size:14px;line-height:1.6;color:#cbd5e1;margin:0 0 24px;">Thank you for signing up on <strong>{$siteTitle}</strong>. To complete your registration and activate your creator account, please verify your email address by clicking the button below:</p>
        <div style="text-align:center;margin:32px 0;">
          <a href="{$verifyUrl}" style="background:linear-gradient(135deg, #19e3ff 0%, #ff2bd6 100%);color:#07030d;font-weight:800;font-size:15px;padding:14px 32px;text-decoration:none;border-radius:30px;display:inline-block;box-shadow:0 0 20px rgba(25,227,255,0.4);letter-spacing:0.02em;">Verify Email Address</a>
        </div>
        <p style="font-size:13px;line-height:1.6;color:#94a3b8;margin:24px 0 8px;">If you're having trouble clicking the button, copy and paste this link into your browser:</p>
        <div style="background:#07030d;border:1px solid rgba(255,255,255,0.1);border-radius:6px;padding:12px;word-break:break-all;font-family:monospace;font-size:12px;color:#19e3ff;">
          <a href="{$verifyUrl}" style="color:#19e3ff;text-decoration:underline;">{$verifyUrl}</a>
        </div>
        <p style="font-size:12px;line-height:1.5;color:#64748b;margin:24px 0 0;padding-top:16px;border-top:1px solid rgba(255,255,255,0.08);">
          If you did not create an account on {$siteTitle}, no further action is required and you can safely disregard this message.
        </p>
      </td>
    </tr>
    <tr>
      <td style="padding:16px 24px;background:#07030d;text-align:center;border-top:1px solid rgba(255,255,255,0.06);font-size:11px;color:#64748b;">
        Sent by {$siteTitle} System &middot; Need help? Contact <a href="mailto:{$supportEmail}" style="color:#a78bfa;text-decoration:none;">{$supportEmail}</a>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

        return send_smtp_email($toEmail, $subject, $bodyHtml);
    }
}

if (!function_exists('send_admin_new_user_alert')) {
    function send_admin_new_user_alert($user) {
        $enabled = \App\Models\Setting::get('admin_notify_signup', '1');
        if ($enabled !== '1') {
            return ['success' => false, 'reason' => 'Admin signup alert disabled in settings.'];
        }

        $adminEmail = \App\Models\Setting::get('admin_notification_email')
            ?: \App\Models\Setting::get('community_email')
            ?: \App\Models\User::where('role', 'admin')->value('email')
            ?: 'probloggerrohit@gmail.com';

        if (empty($adminEmail)) {
            return ['success' => false, 'reason' => 'No admin notification email configured.'];
        }

        $siteTitle = site_name();
        $adminUsersUrl = url('/admin/users');
        $userProfileUrl = url('/u/' . $user->username);
        $totalUsers = \App\Models\User::count();
        $regIp = request()->ip() ?? '127.0.0.1';
        $timeStr = now()->toDayDateTimeString();
        $refUser = $user->referrer ? $user->referrer->username : null;

        $subject = "[New Signup] @{$user->username} has registered on {$siteTitle}";

        $refHtml = $refUser ? "<p style=\"font-size:14px;color:#cbd5e1;margin:6px 0;\"><strong>Referred By:</strong> @{$refUser}</p>" : "";

        $bodyHtml = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="margin:0;padding:24px 12px;background-color:#07030d;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#f3e8ff;">
  <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:560px;background:#0f0716;border:1px solid rgba(255,43,214,0.35);border-radius:12px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,0.6);">
    <tr>
      <td style="padding:24px;text-align:center;border-bottom:1px solid rgba(255,255,255,0.08);background:linear-gradient(180deg, rgba(255,43,214,0.15) 0%, transparent 100%);">
        <h1 style="color:#ff2bd6;margin:0 0 6px;font-size:24px;font-weight:900;">{$siteTitle} Admin Alert</h1>
        <p style="color:#19e3ff;font-size:13px;font-weight:700;margin:0;text-transform:uppercase;letter-spacing:0.05em;">New User Registration</p>
      </td>
    </tr>
    <tr>
      <td style="padding:28px 24px;">
        <p style="font-size:15px;line-height:1.6;color:#ffffff;margin:0 0 16px;">Hello Admin,</p>
        <p style="font-size:14px;line-height:1.6;color:#cbd5e1;margin:0 0 20px;">A new member has just registered an account on <strong>{$siteTitle}</strong>. Here are the account details:</p>
        <div style="background:#07030d;border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:16px;margin:16px 0;">
          <p style="font-size:14px;color:#cbd5e1;margin:6px 0;"><strong>Username:</strong> <span style="color:#ff2bd6;font-weight:bold;">@{$user->username}</span></p>
          <p style="font-size:14px;color:#cbd5e1;margin:6px 0;"><strong>Email:</strong> {$user->email}</p>
          <p style="font-size:14px;color:#cbd5e1;margin:6px 0;"><strong>Joined:</strong> {$timeStr}</p>
          <p style="font-size:14px;color:#cbd5e1;margin:6px 0;"><strong>Registration IP:</strong> <code style="color:#19e3ff;font-family:monospace;">{$regIp}</code></p>
          {$refHtml}
          <p style="font-size:14px;color:#cbd5e1;margin:6px 0;"><strong>Total Registered Accounts:</strong> {$totalUsers}</p>
        </div>
        <div style="text-align:center;margin:28px 0 16px;">
          <a href="{$adminUsersUrl}" style="background:linear-gradient(135deg, #ff2bd6 0%, #19e3ff 100%);color:#07030d;font-weight:800;font-size:14px;padding:12px 28px;text-decoration:none;border-radius:24px;display:inline-block;box-shadow:0 0 18px rgba(255,43,214,0.4);">Manage Users in Admin Panel</a>
        </div>
      </td>
    </tr>
    <tr>
      <td style="padding:14px 24px;background:#07030d;text-align:center;border-top:1px solid rgba(255,255,255,0.06);font-size:11px;color:#64748b;">
        Automated alert for {$siteTitle} Administrators &middot; Manage settings in Admin Control Panel
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

        return send_smtp_email($adminEmail, $subject, $bodyHtml);
    }
}

if (!function_exists('send_new_follower_email')) {
    function send_new_follower_email($recipientUser, $followerUser, $followedBlog) {
        if (!$recipientUser || !$recipientUser->email) {
            return ['success' => false, 'reason' => 'No recipient email found.'];
        }

        if (method_exists($recipientUser, 'prefersFollowerNotification') && !$recipientUser->prefersFollowerNotification()) {
            return ['success' => false, 'reason' => 'User disabled new follower notifications.'];
        }

        $siteTitle = site_name();
        $followerUsername = $followerUser->username;
        $blogName = $followedBlog ? $followedBlog->slug : $recipientUser->username;
        $followerUrl = url('/u/' . $followerUsername);
        $settingsUrl = url('/dashboard?section=notifications');

        $subject = "@{$followerUsername} is now following your blog on {$siteTitle}!";

        $bodyHtml = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="margin:0;padding:24px 12px;background-color:#07030d;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#f3e8ff;">
  <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:560px;background:#0f0716;border:1px solid rgba(255,43,214,0.3);border-radius:12px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,0.6);">
    <tr>
      <td style="padding:24px;text-align:center;border-bottom:1px solid rgba(255,255,255,0.08);background:linear-gradient(180deg, rgba(255,43,214,0.12) 0%, transparent 100%);">
        <h1 style="color:#ff2bd6;margin:0 0 6px;font-size:24px;font-weight:900;">New Follower! 🎉</h1>
        <p style="color:#19e3ff;font-size:13px;font-weight:700;margin:0;text-transform:uppercase;letter-spacing:0.05em;">{$siteTitle} Community</p>
      </td>
    </tr>
    <tr>
      <td style="padding:28px 24px;">
        <p style="font-size:15px;line-height:1.6;color:#ffffff;margin:0 0 16px;">Hi <strong>@{$recipientUser->username}</strong>,</p>
        <p style="font-size:14px;line-height:1.6;color:#cbd5e1;margin:0 0 24px;">Great news! <strong style="color:#ff2bd6;">@{$followerUsername}</strong> just started following your blog <strong style="color:#19e3ff;">@{$blogName}</strong> on {$siteTitle}.</p>
        <div style="text-align:center;margin:28px 0 20px;">
          <a href="{$followerUrl}" style="background:linear-gradient(135deg, #ff2bd6 0%, #19e3ff 100%);color:#07030d;font-weight:800;font-size:14px;padding:12px 28px;text-decoration:none;border-radius:24px;display:inline-block;box-shadow:0 0 18px rgba(255,43,214,0.4);">View @{$followerUsername}'s Profile</a>
        </div>
        <p style="font-size:12px;color:#94a3b8;line-height:1.5;margin-top:24px;border-top:1px solid rgba(255,255,255,0.08);padding-top:16px;">
          You received this email because you have new follower notifications turned on. You can turn on/off notifications anytime in your <a href="{$settingsUrl}" style="color:#19e3ff;">Notification Preferences</a>.
        </p>
      </td>
    </tr>
    <tr>
      <td style="padding:14px 24px;background:#07030d;text-align:center;border-top:1px solid rgba(255,255,255,0.06);font-size:11px;color:#64748b;">
        Sent by {$siteTitle} &middot; <a href="{$settingsUrl}" style="color:#a78bfa;text-decoration:none;">Manage notification settings</a>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

        return send_smtp_email($recipientUser->email, $subject, $bodyHtml);
    }
}

if (!function_exists('private_message_user_id')) {
    function private_message_user_id($table, string $column): void
    {
        $type = 'integer';
        try {
            $type = strtolower((string) \Illuminate\Support\Facades\Schema::getColumnType('users', 'id'));
        } catch (\Throwable $e) {}

        if (str_contains($type, 'bigint')) {
            $table->unsignedBigInteger($column);
        } else {
            $table->unsignedInteger($column);
        }

        $table->foreign($column)->references('id')->on('users')->cascadeOnDelete();
    }
}

if (!function_exists('ensure_private_message_tables')) {
    function ensure_private_message_tables(): void
    {
        static $done = false;
        if ($done) {
            return;
        }

        try {
            $schema = \Illuminate\Support\Facades\Schema::class;

            if ($schema::hasTable('messages')
                && $schema::hasColumn('messages', 'attachment_path')
                && $schema::hasColumn('messages', 'attachment_name')
                && $schema::hasColumn('messages', 'attachment_mime')
                && $schema::hasColumn('messages', 'attachment_size')
                && $schema::hasTable('user_blocks')) {
                $done = true;
                return;
            }

            if (!$schema::hasTable('messages')) {
                $schema::create('messages', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    private_message_user_id($table, 'sender_id');
                    private_message_user_id($table, 'recipient_id');
                    $table->string('subject', 190)->nullable();
                    $table->text('body')->nullable();
                    $table->boolean('is_read')->default(false);
                    $table->string('attachment_path')->nullable();
                    $table->string('attachment_name')->nullable();
                    $table->string('attachment_mime', 120)->nullable();
                    $table->unsignedBigInteger('attachment_size')->nullable();
                    $table->timestamps();
                });
            } else {
                $add = [];
                if (!$schema::hasColumn('messages', 'attachment_path')) {
                    $add[] = 'attachment_path';
                }
                if (!$schema::hasColumn('messages', 'attachment_name')) {
                    $add[] = 'attachment_name';
                }
                if (!$schema::hasColumn('messages', 'attachment_mime')) {
                    $add[] = 'attachment_mime';
                }
                if (!$schema::hasColumn('messages', 'attachment_size')) {
                    $add[] = 'attachment_size';
                }
                if ($add) {
                    $schema::table('messages', function (\Illuminate\Database\Schema\Blueprint $table) use ($add) {
                        if (in_array('attachment_path', $add, true)) {
                            $table->string('attachment_path')->nullable();
                        }
                        if (in_array('attachment_name', $add, true)) {
                            $table->string('attachment_name')->nullable();
                        }
                        if (in_array('attachment_mime', $add, true)) {
                            $table->string('attachment_mime', 120)->nullable();
                        }
                        if (in_array('attachment_size', $add, true)) {
                            $table->unsignedBigInteger('attachment_size')->nullable();
                        }
                    });
                }
            }

            if (!$schema::hasTable('user_blocks')) {
                $schema::create('user_blocks', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    private_message_user_id($table, 'blocker_id');
                    private_message_user_id($table, 'blocked_id');
                    $table->timestamps();
                    $table->unique(['blocker_id', 'blocked_id']);
                });
            }

            $done = true;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Private message schema ensure failed: ' . $e->getMessage());
        }
    }
}

if (!function_exists('send_private_message_email')) {
    function send_private_message_email($recipientUser, $senderUser, $messageObj) {
        if (!$recipientUser || !$recipientUser->email) {
            return ['success' => false, 'reason' => 'No recipient email found.'];
        }

        if (method_exists($recipientUser, 'prefersMessageNotification') && !$recipientUser->prefersMessageNotification()) {
            return ['success' => false, 'reason' => 'User disabled private message notifications.'];
        }

        $siteTitle = site_name();
        $senderUsername = $senderUser->username;
        $inboxUrl = url('/dashboard/messages?with=' . rawurlencode($senderUsername));
        $settingsUrl = url('/dashboard/notifications');
        $rawBody = trim(strip_tags((string) ($messageObj->body ?? '')));
        if ($rawBody === '' && !empty($messageObj->attachment_name)) {
            $rawBody = 'Sent a file: ' . $messageObj->attachment_name;
        }
        if ($rawBody === '') {
            $rawBody = 'Sent you a private message.';
        }
        $snippet = htmlspecialchars(\Illuminate\Support\Str::limit($rawBody, 160), ENT_QUOTES, 'UTF-8');
        $subjectLine = htmlspecialchars(!empty($messageObj->subject) ? $messageObj->subject : "Private message from @{$senderUsername}", ENT_QUOTES, 'UTF-8');
        $senderUsernameSafe = htmlspecialchars($senderUsername, ENT_QUOTES, 'UTF-8');

        $subject = "New private message from @{$senderUsername} - {$siteTitle}";

        $bodyHtml = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="margin:0;padding:24px 12px;background-color:#07030d;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#f3e8ff;">
  <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:560px;background:#0f0716;border:1px solid rgba(25,227,255,0.35);border-radius:12px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,0.6);">
    <tr>
      <td style="padding:24px;text-align:center;border-bottom:1px solid rgba(255,255,255,0.08);background:linear-gradient(180deg, rgba(25,227,255,0.12) 0%, transparent 100%);">
        <h1 style="color:#19e3ff;margin:0 0 6px;font-size:24px;font-weight:900;">New Private Message ✉️</h1>
        <p style="color:#ff2bd6;font-size:13px;font-weight:700;margin:0;text-transform:uppercase;letter-spacing:0.05em;">{$siteTitle} Inbox</p>
      </td>
    </tr>
    <tr>
      <td style="padding:28px 24px;">
        <p style="font-size:15px;line-height:1.6;color:#ffffff;margin:0 0 16px;">Hi <strong>@{$recipientUser->username}</strong>,</p>
        <p style="font-size:14px;line-height:1.6;color:#cbd5e1;margin:0 0 18px;">You have received a new private message from <strong style="color:#ff2bd6;">@{$senderUsernameSafe}</strong>:</p>
        <div style="background:#07030d;border:1px solid rgba(25,227,255,0.25);border-left:4px solid #19e3ff;border-radius:8px;padding:16px;margin:16px 0;">
          <p style="font-size:13px;color:#a291b5;margin:0 0 8px;font-weight:bold;">Subject: {$subjectLine}</p>
          <p style="font-size:14px;color:#ffffff;line-height:1.6;margin:0;font-style:italic;">"{$snippet}"</p>
        </div>
        <div style="text-align:center;margin:28px 0 20px;">
          <a href="{$inboxUrl}" style="background:linear-gradient(135deg, #19e3ff 0%, #ff2bd6 100%);color:#07030d;font-weight:800;font-size:14px;padding:12px 28px;text-decoration:none;border-radius:24px;display:inline-block;box-shadow:0 0 18px rgba(25,227,255,0.4);">Read &amp; Reply in Your Inbox</a>
        </div>
        <p style="font-size:12px;color:#94a3b8;line-height:1.5;margin-top:24px;border-top:1px solid rgba(255,255,255,0.08);padding-top:16px;">
          You received this email because you have private message notifications turned on. You can turn on/off notifications anytime in your <a href="{$settingsUrl}" style="color:#19e3ff;">Notification Preferences</a>.
        </p>
      </td>
    </tr>
    <tr>
      <td style="padding:14px 24px;background:#07030d;text-align:center;border-top:1px solid rgba(255,255,255,0.06);font-size:11px;color:#64748b;">
        Sent by {$siteTitle} &middot; <a href="{$settingsUrl}" style="color:#a78bfa;text-decoration:none;">Manage notification settings</a>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

        return send_smtp_email($recipientUser->email, $subject, $bodyHtml);
    }
}





