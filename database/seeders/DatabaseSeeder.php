<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Blog;
use App\Models\Post;
use App\Models\Setting;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Platform API Credentials & Default Settings
        $defaultSettings = [
            'site_title' => 'Nina.Sucks',
            'meta_description' => 'Adult microblogging community. Post photos, text, video, quotes and chats. Uncensored & free.',
            'site_logo' => 'assets/logo.png',
            'primary_color' => '#ff2bd6',
            'secondary_color' => '#19e3ff',
            'donations_enabled' => '1',
            'referral_reward_points' => '100',
            'max_media_mb' => '50',
            // Community Settings
            'community_title' => 'Nina.Sucks Creator Community',
            'community_tagline' => 'The uncensored hub for creator discovery, visual art & free expression.',
            'community_bio' => "Welcome to the Nina.Sucks official community!\nConnect with like-minded creators, share stories, quotes, photos and chats without algorithmic barriers.\nCreate unlimited blogs, customize your pages, and earn points to boost your reach across the network.",
            'community_url1' => 'https://nina.sucks',
            'community_url2' => 'https://twitter.com/ninasucks',
            'community_url3' => 'https://t.me/ninasucks',
            'community_email' => 'community@nina.sucks',
            // API Credentials provided by user
            'nowpayments_api_key' => '4DMYGP5-7V04ZZJ-G3JTHJH-XGNZHD5',
            'nowpayments_ipn_secret' => 'mIV4c5kQrKwYN4PEGRV4L5MM8UQUhAEz',
            'bitcart_url' => 'https://bitcart.server5.click/store/01M2V1SFTBTD7TCN1HKKNKS2WK',
            'google_client_id' => '421189417454-gbbjstrtcarq898bkjdemholu8te82op.apps.googleusercontent.com',
            'google_client_secret' => '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',
            'turnstile_site_key' => '0x4AAAAAAE8QOvWsSh5TL-MQ',
            'turnstile_secret_key' => '0x4AAAAAAE8QOkJScve1toziPVGAdBajjJI',
            // Marketplace Promotion Packages (Points)
            'pkg_cpm_1k' => '500',
            'pkg_cpm_5k' => '2000',
            'pkg_cpm_25k' => '8000',
            'pkg_cpm_100k' => '25000',
            'pkg_flat_1d' => '350',
            'pkg_flat_7d' => '2000',
            'pkg_flat_30d' => '7000',
        ];

        foreach ($defaultSettings as $key => $val) {
            Setting::updateOrCreate(['key' => $key], ['value' => $val]);
        }

        // 2. Seed or update Admin User
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'email' => 'admin@example.com',
                'password_hash' => Hash::make('ChangeMe123!'),
                'role' => 'admin',
                'points' => 50000,
                'badge_pro' => true,
                'badge_id' => true,
                'badge_vip' => true,
                'status' => 'active',
                'referral_code' => 'ADMIN001',
            ]
        );

        $admin->password_hash = Hash::make('ChangeMe123!');
        $admin->role = 'admin';
        $admin->badge_pro = true;
        $admin->badge_id = true;
        $admin->badge_vip = true;
        $admin->status = 'active';
        $admin->save();

        // 3. Seed Admin Main Blog / Profile
        $adminBlog = Blog::firstOrCreate(
            ['user_id' => $admin->id, 'is_main' => 1],
            [
                'slug' => 'admin',
                'title' => 'Admin Central',
                'tagline' => 'Official staff updates, notices and announcements',
                'color' => '#ff2bd6',
                'is_main' => 1,
                'status' => 'active',
                'bio' => "Official administrator blog & platform announcements.\nContact staff or request community features.",
                'website1' => 'https://nina.sucks',
                'website2' => 'https://twitter.com/ninasucks',
                'website3' => 'https://t.me/ninasucks',
                'contact_email' => 'admin@nina.sucks',
                'is_featured' => 1,
            ]
        );

        // 4. Seed Creators and Demo Blogs & Photo Posts
        $creators = [
            [
                'username' => 'neon_vixen',
                'email' => 'vixen@nina.sucks',
                'badge' => 'vip',
                'title' => 'Neon Vixen',
                'tagline' => 'Midnight aesthetics, neon glow & cyber fantasies',
                'color' => '#ff2bd6',
                'bio' => "Creating vivid visual aesthetics in the heart of the neon underground.\nDM for collaborations and custom shoots.",
                'posts' => [
                    [
                        'type' => 'photo',
                        'media_path' => 'hero.jpg',
                        'caption' => 'Midnight reflections across the skyline. The neon glow never sleeps in this city.',
                        'tags' => ['neon', 'nightshift', 'cyberpunk', 'photography'],
                    ],
                    [
                        'type' => 'photo',
                        'media_path' => 'hero.jpg',
                        'caption' => 'Golden hour turning into magenta twilight. Rooftop photo sessions.',
                        'tags' => ['neon', 'lifestyle', 'bts', 'aesthetic'],
                    ],
                ]
            ],
            [
                'username' => 'cyber_ghost',
                'email' => 'ghost@nina.sucks',
                'badge' => 'pro',
                'title' => 'Cyber Ghost',
                'tagline' => 'Dark synth, glitches and late night thoughts',
                'color' => '#19e3ff',
                'bio' => "Lost in the digital realm. Exploring dystopian visuals and soundscapes.",
                'posts' => [
                    [
                        'type' => 'text',
                        'heading' => 'The Art of Unfiltered Microblogging',
                        'body' => 'Remember when blogging felt personal and free? No algorithms dictating what you must see, no corporate sanitized feeds, just raw thoughts, photos, and mutuals reblogging whatever sparks joy at 3 AM.',
                        'tags' => ['nostalgia', 'blogging', 'freedom'],
                    ],
                    [
                        'type' => 'photo',
                        'media_path' => 'hero.jpg',
                        'caption' => 'Deep neural renders in high definition. Exploring cybernetic textures.',
                        'tags' => ['cyberpunk', 'art', 'renders', 'visuals'],
                    ],
                ]
            ],
            [
                'username' => 'velvet_muse',
                'email' => 'muse@nina.sucks',
                'badge' => 'id',
                'title' => 'Velvet Muse',
                'tagline' => 'Sensual poetry, intimate photography & art',
                'color' => '#ffd60a',
                'bio' => "Visual artist and writer. Celebrating beauty, softness and unfiltered expression.",
                'posts' => [
                    [
                        'type' => 'photo',
                        'media_path' => 'hero.jpg',
                        'caption' => 'Candlelight and velvet cushions. A quiet evening away from the storm.',
                        'tags' => ['cozy', 'selfie', 'lifestyle', 'art'],
                    ],
                    [
                        'type' => 'quote',
                        'quote_text' => 'We are all creatures of the electric twilight, looking for something real between the glow of our screens.',
                        'quote_source' => 'Velvet Confessions, Vol. 1',
                        'tags' => ['thoughts', 'poetry', 'nightshift'],
                    ],
                ]
            ],
            [
                'username' => 'retro_wave',
                'email' => 'wave@nina.sucks',
                'badge' => 'pro',
                'title' => 'Retro Wave',
                'tagline' => '80s nostalgia, VHS tapes and analog dreams',
                'color' => '#8a3dff',
                'bio' => "Living in 1984 through a CRT monitor. Mixtapes, neon grids, and vintage synths.",
                'posts' => [
                    [
                        'type' => 'link',
                        'link_title' => 'Synthwave Radio - 24/7 Chill Beats for Night Owls',
                        'link_url' => 'https://retro-synth.fm',
                        'link_domain' => 'retro-synth.fm',
                        'caption' => 'Tune in to the latest stream, perfect background soundtrack for writing posts.',
                        'tags' => ['music', 'synthwave', 'bts'],
                    ],
                    [
                        'type' => 'photo',
                        'media_path' => 'hero.jpg',
                        'caption' => 'Analog cassette tapes and neon backlights.',
                        'tags' => ['retro', 'vintage', 'synthwave'],
                    ],
                ]
            ],
        ];

        foreach ($creators as $cData) {
            $user = User::firstOrCreate(
                ['username' => $cData['username']],
                [
                    'email' => $cData['email'],
                    'password_hash' => Hash::make('Secret123!'),
                    'role' => 'user',
                    'points' => 5000,
                    'badge_pro' => ($cData['badge'] === 'pro'),
                    'badge_id' => ($cData['badge'] === 'id'),
                    'badge_vip' => ($cData['badge'] === 'vip'),
                    'status' => 'active',
                    'referral_code' => strtoupper($cData['username']),
                ]
            );

            $blog = Blog::firstOrCreate(
                ['user_id' => $user->id, 'is_main' => 1],
                [
                    'slug' => $cData['username'],
                    'title' => $cData['title'],
                    'tagline' => $cData['tagline'],
                    'color' => $cData['color'],
                    'is_main' => 1,
                    'status' => 'active',
                    'bio' => $cData['bio'],
                    'is_featured' => 1,
                ]
            );

            foreach ($cData['posts'] as $pData) {
                Post::firstOrCreate(
                    [
                        'blog_id' => $blog->id,
                        'caption' => $pData['caption'] ?? ($pData['heading'] ?? ($pData['quote_text'] ?? null)),
                    ],
                    [
                        'type' => $pData['type'],
                        'status' => 'published',
                        'heading' => $pData['heading'] ?? null,
                        'body' => $pData['body'] ?? null,
                        'caption' => $pData['caption'] ?? null,
                        'media_path' => $pData['media_path'] ?? null,
                        'media_type' => ($pData['type'] === 'photo') ? 'photo' : null,
                        'quote_text' => $pData['quote_text'] ?? null,
                        'quote_source' => $pData['quote_source'] ?? null,
                        'link_url' => $pData['link_url'] ?? null,
                        'link_title' => $pData['link_title'] ?? null,
                        'link_domain' => $pData['link_domain'] ?? null,
                        'media_meta' => !empty($pData['tags']) ? ['tags' => $pData['tags']] : null,
                        'published_at' => now()->subMinutes(rand(10, 500)),
                    ]
                );
            }
        }

        // 5. Welcome Announcement Post
        Post::firstOrCreate(
            ['blog_id' => $adminBlog->id, 'heading' => 'Welcome to Nina.Sucks!'],
            [
                'type' => 'text',
                'status' => 'published',
                'heading' => 'Welcome to Nina.Sucks!',
                'body' => 'Welcome to our microblogging community! Share your text, links, photos, quotes, and join our creative community without algorithmic blocks. #community #updates #welcome',
                'media_meta' => ['tags' => ['community', 'updates', 'welcome']],
                'published_at' => now(),
            ]
        );

        Post::firstOrCreate(
            ['blog_id' => $adminBlog->id, 'heading' => 'Creator Freedom & Uncensored Microblogging'],
            [
                'type' => 'text',
                'status' => 'published',
                'heading' => 'Creator Freedom & Uncensored Microblogging',
                'body' => 'Build your audience on your own terms. Create unlimited blogs, customize your theme, and connect with people who share your passion. #creators #cyberpunk #neon',
                'media_meta' => ['tags' => ['creators', 'cyberpunk', 'neon']],
                'published_at' => now()->subHours(2),
            ]
        );
    }
}

if (!class_exists('DatabaseSeeder')) {
    class_alias(\Database\Seeders\DatabaseSeeder::class, 'DatabaseSeeder');
}
