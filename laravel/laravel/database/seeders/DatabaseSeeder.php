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
            'meta_description' => 'Adult microblogging community. Post photos, text, video, quotes and chats. Tumblr-inspired & censorship-free.',
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

        // 4. Welcome Announcement Post
        Post::firstOrCreate(
            ['blog_id' => $adminBlog->id, 'heading' => 'Welcome to Nina.Sucks!'],
            [
                'type' => 'text',
                'status' => 'published',
                'heading' => 'Welcome to Nina.Sucks!',
                'body' => 'Welcome to our Tumblr-inspired community! Share your text, links, photos, quotes, and join our creative community without algorithmic blocks.',
                'published_at' => now(),
            ]
        );
    }
}

if (!class_exists('DatabaseSeeder')) {
    class_alias(\Database\Seeders\DatabaseSeeder::class, 'DatabaseSeeder');
}
