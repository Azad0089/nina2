<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Post;
use App\Models\Blog;
use App\Models\Setting;
use App\Models\Verification;
use App\Models\Donation;
use App\Models\Report;
use App\Models\SupportTicket;
use App\Models\UserLogin;
use App\Models\Promotion;
use App\Models\Advertisement;

class AdminController extends Controller
{
    private function checkAdmin()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access. Administrator privileges required.');
        }
    }

    public function index(Request $request)
    {
        $this->checkAdmin();

        $section = $request->query('section', 'overview');
        $allPosts = Post::with(['blog.user'])->orderBy('created_at', 'desc')->take(100)->get();
        $allUsers = User::withCount(['blogs', 'referrals'])->with(['donations', 'logins'])->orderBy('created_at', 'desc')->get();
        $allBlogs = Blog::with('user')->orderBy('created_at', 'desc')->get();
        $reports = Report::with('reporter')->orderBy('created_at', 'desc')->get();
        $tickets = SupportTicket::orderBy('created_at', 'desc')->get();
        $verifications = Verification::with('user')->orderBy('created_at', 'desc')->get();
        $donations = Donation::with('user')->orderBy('created_at', 'desc')->get();
        $logins = UserLogin::with('user')->orderBy('logged_at', 'desc')->take(150)->get();
        $promotions = Promotion::with(['user', 'post', 'blog'])->orderBy('created_at', 'desc')->get();
        $allAds = Advertisement::orderBy('created_at', 'desc')->get();
        $heroSlides = hero_slides();

        $settings = Setting::pluck('value', 'key')->all();

        $title = ucfirst($section);
        $staticPageEditor = null;
        if ($section === 'pages') {
            $staticPageEditor = app(PageController::class)->editorState((string) $request->query('page', 'about'));
        }

        return view('admin', compact(
            'section', 'allPosts', 'allUsers', 'allBlogs', 'reports', 'tickets',
            'verifications', 'donations', 'logins', 'promotions', 'allAds', 'heroSlides', 'settings', 'title',
            'staticPageEditor'
        ));
    }

    public function referrals(Request $request)
    {
        $request->merge(['section' => 'referrals']);
        return $this->index($request);
    }

    public function ads(Request $request)
    {
        $request->merge(['section' => 'ads']);
        return $this->index($request);
    }

    public function slider(Request $request)
    {
        $request->merge(['section' => 'slider']);
        return $this->index($request);
    }

    public function posts(Request $request)
    {
        $request->merge(['section' => 'posts']);
        return $this->index($request);
    }

    public function users(Request $request)
    {
        $request->merge(['section' => 'users']);
        return $this->index($request);
    }

    public function blogs(Request $request)
    {
        $request->merge(['section' => 'blogs']);
        return $this->index($request);
    }

    public function reports(Request $request)
    {
        $request->merge(['section' => 'reports']);
        return $this->index($request);
    }

    public function support(Request $request)
    {
        $request->merge(['section' => 'support']);
        return $this->index($request);
    }

    public function verifications(Request $request)
    {
        $request->merge(['section' => 'verifications']);
        return $this->index($request);
    }

    public function donations(Request $request)
    {
        $request->merge(['section' => 'donations']);
        return $this->index($request);
    }

    public function logins(Request $request)
    {
        $request->merge(['section' => 'logins']);
        return $this->index($request);
    }

    public function settings(Request $request)
    {
        $request->merge(['section' => 'settings']);
        return $this->index($request);
    }

    public function pages(Request $request)
    {
        $request->merge(['section' => 'pages']);
        return $this->index($request);
    }

    public function savePage(Request $request)
    {
        $this->checkAdmin();

        $key = (string) $request->input('page_key', '');
        $defs = static_page_definitions();
        if (!isset($defs[$key])) {
            return redirect()->route('admin.pages')->with('error', 'That page cannot be edited.');
        }

        if ($request->input('reset') === '1') {
            Setting::where('key', 'static_page_' . $key)->delete();
            return redirect()->route('admin.pages', ['page' => $key])->with('success', $defs[$key]['label'] . ' restored to the original text.');
        }

        $title = mb_substr(trim((string) $request->input('title', '')), 0, 180);
        $lead = mb_substr(trim((string) $request->input('lead', '')), 0, 500);
        $body = static_page_sanitize((string) $request->input('body', ''));
        if (mb_strlen($body) > 200000) {
            return redirect()->route('admin.pages', ['page' => $key])->with('error', 'Page text is too long.');
        }

        Setting::set('static_page_' . $key, json_encode([
            'title' => $title,
            'lead' => $lead,
            'body' => $body,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return redirect()->route('admin.pages', ['page' => $key])->with('success', $defs[$key]['label'] . ' page text saved.');
    }

    public function saveSettings(Request $request)
    {
        $this->checkAdmin();

        $allowed = [
            'site_title', 'meta_description', 'custom_schema', 'site_logo', 'site_favicon',
            'primary_color', 'secondary_color', 'head_scripts', 'donations_enabled',
            'bitcart_enabled', 'qr_code_enabled',
            'premium_price_usd', 'premium_plus_price_usd',
            'premium_price_points', 'premium_plus_price_points',
            'referral_reward_points', 'max_media_mb', 'max_image_mb', 'max_video_mb', 'max_audio_mb', 'max_message_file_mb',
            'allowed_image_ext', 'allowed_video_ext',
            'pkg_cpm_1k', 'pkg_cpm_5k', 'pkg_cpm_25k', 'pkg_cpm_100k',
            'pkg_flat_1d', 'pkg_flat_7d', 'pkg_flat_30d',
            'featured_blogs_max_count',
            'enable_adult_warning',
            'feed_text_excerpt_limit', 'feed_caption_excerpt_limit',
            'post_max_text_length', 'post_max_caption_length',
            'premium_points_monthly', 'premium_plus_points_monthly',
            'community_title', 'community_tagline', 'community_bio',
            'community_avatar', 'community_cover',
            'community_url1', 'community_url2', 'community_url3', 'community_email',
            'nowpayments_api_key', 'nowpayments_ipn_secret', 'nowpayments_sandbox',
            'bitcart_url', 'bitcart_store_id', 'bitcart_product_id', 'bitcart_api_url',
            'google_client_id', 'google_client_secret', 'turnstile_site_key', 'turnstile_secret_key',
            'meta_verification',
            'smtp_enabled', 'mail_method', 'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password',
            'smtp_encryption', 'smtp_from_email', 'smtp_from_name',
            'welcome_email_enabled', 'welcome_email_subject', 'welcome_email_body',
            'email_verification_enabled', 'email_verification_subject', 'email_verification_body',
            'admin_notify_signup', 'admin_notification_email'
        ];

        $settingsToSave = $request->only($allowed);

        // Check toggles that might not be sent when unchecked or sent as hidden '0'
        $settingsToSave['enable_adult_warning'] = ($request->input('enable_adult_warning') == '1') ? '1' : '0';
        $settingsToSave['bitcart_enabled'] = ($request->input('bitcart_enabled') == '1') ? '1' : '0';
        $settingsToSave['qr_code_enabled'] = ($request->input('qr_code_enabled') == '1') ? '1' : '0';
        $settingsToSave['smtp_enabled'] = ($request->input('smtp_enabled') == '1') ? '1' : '0';
        $settingsToSave['welcome_email_enabled'] = ($request->input('welcome_email_enabled') == '1') ? '1' : '0';
        $settingsToSave['email_verification_enabled'] = ($request->input('email_verification_enabled') == '1') ? '1' : '0';
        $settingsToSave['admin_notify_signup'] = ($request->input('admin_notify_signup') == '1') ? '1' : '0';

        if (array_key_exists('max_message_file_mb', $settingsToSave)) {
            $settingsToSave['max_message_file_mb'] = (string) max(0, min(500, (float) $settingsToSave['max_message_file_mb']));
        }
        if (array_key_exists('post_max_caption_length', $settingsToSave)) {
            $settingsToSave['post_max_caption_length'] = (string) max(80, min(2000, (int) $settingsToSave['post_max_caption_length']));
        }
        if (array_key_exists('feed_caption_excerpt_limit', $settingsToSave)) {
            $settingsToSave['feed_caption_excerpt_limit'] = (string) max(30, min(1000, (int) $settingsToSave['feed_caption_excerpt_limit']));
        }

        $maxImageMb = (float) Setting::get('max_image_mb', 5);
        $maxImageBytes = (int)($maxImageMb * 1024 * 1024);

        // Handle Community Avatar File Upload
        if ($request->hasFile('community_avatar_file')) {
            $file = $request->file('community_avatar_file');
            if ($file->getSize() > $maxImageBytes) {
                return redirect()->back()->with('error', "Community avatar image exceeds the {$maxImageMb}MB limit.");
            }
            $ext = strtolower($file->getClientOriginalExtension());
            $filename = 'community_av_' . time() . '.' . $ext;
            $file->storeAs('uploads', $filename, 'public');
            $pubDir = public_path('uploads');
            if (!file_exists($pubDir)) @mkdir($pubDir, 0755, true);
            @copy($file->getRealPath(), $pubDir . '/' . $filename);
            $settingsToSave['community_avatar'] = 'uploads/' . $filename;
        } elseif (empty($settingsToSave['community_avatar'])) {
            unset($settingsToSave['community_avatar']);
        }

        // Handle Community Cover File Upload
        if ($request->hasFile('community_cover_file')) {
            $file = $request->file('community_cover_file');
            if ($file->getSize() > $maxImageBytes) {
                return redirect()->back()->with('error', "Community cover image exceeds the {$maxImageMb}MB limit.");
            }
            $ext = strtolower($file->getClientOriginalExtension());
            $filename = 'community_cover_' . time() . '.' . $ext;
            $file->storeAs('uploads', $filename, 'public');
            $pubDir = public_path('uploads');
            if (!file_exists($pubDir)) @mkdir($pubDir, 0755, true);
            @copy($file->getRealPath(), $pubDir . '/' . $filename);
            $settingsToSave['community_cover'] = 'uploads/' . $filename;
        } elseif (empty($settingsToSave['community_cover'])) {
            unset($settingsToSave['community_cover']);
        }

        foreach ($settingsToSave as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->back()->with('success', 'Platform settings saved successfully.');
    }

    public function deletePost($id)
    {
        $this->checkAdmin();
        $post = Post::findOrFail($id);
        $post->delete();
        return redirect()->back()->with('success', 'Post #' . $id . ' has been removed.');
    }

    public function toggleUserStatus($id, Request $request)
    {
        $this->checkAdmin();
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Cannot change your own admin account status.');
        }

        $newStatus = $request->input('status', 'active');
        $user->status = in_array($newStatus, ['active', 'suspended', 'banned']) ? $newStatus : 'active';
        $user->save();

        return redirect()->back()->with('success', "User @{$user->username} status set to {$user->status}.");
    }

    public function deleteUser($id)
    {
        $this->checkAdmin();
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Cannot delete your own admin account.');
        }
        $name = $user->username;
        $user->delete();

        return redirect()->back()->with('success', "User @{$name} deleted permanently.");
    }

    public function updateUserPassword($id, Request $request)
    {
        $this->checkAdmin();
        $password = trim($request->input('password', ''));
        if (strlen($password) < 4) {
            return redirect()->back()->with('error', 'Password must be at least 4 characters long.');
        }

        $user = User::findOrFail($id);
        $user->password_hash = \Illuminate\Support\Facades\Hash::make($password);
        $user->save();

        return redirect()->back()->with('success', "Password for @{$user->username} updated successfully.");
    }

    public function toggleBadge($id, Request $request)
    {
        $this->checkAdmin();
        $user = User::findOrFail($id);
        $badge = $request->input('badge'); // 'pro', 'id', 'vip'

        if ($badge === 'vip') {
            $user->badge_vip = !$user->badge_vip;
        } elseif ($badge === 'id') {
            $user->badge_id = !$user->badge_id;
        } elseif ($badge === 'pro') {
            $user->badge_pro = !$user->badge_pro;
        }
        $user->save();

        return redirect()->back()->with('success', "Updated badges for @{$user->username}.");
    }

    public function toggleEmailVerification($id)
    {
        $this->checkAdmin();
        $user = User::findOrFail($id);
        if ($user->email_verified_at) {
            $user->email_verified_at = null;
            $user->email_verification_token = \Illuminate\Support\Str::random(48);
            $user->save();
            return redirect()->back()->with('success', "Marked @{$user->username} as unverified.");
        } else {
            $user->email_verified_at = now();
            $user->email_verification_token = null;
            $user->save();
            return redirect()->back()->with('success', "Marked @{$user->username}'s email as verified!");
        }
    }

    public function setUserPlan($id, Request $request)
    {
        $this->checkAdmin();
        $user = User::findOrFail($id);
        $plan = $request->input('plan'); // 'free', 'premium', 'premium_plus'

        if ($plan === 'premium_plus') {
            $user->badge_pro = 1;
            $user->badge_vip = 1;
            $user->save();

            \App\Models\Donation::updateOrCreate(
                ['user_id' => $user->id, 'gateway' => 'admin'],
                [
                    'order_id' => 'ADMIN_SUB_' . $user->id,
                    'payment_id' => 'ADMIN_SUB_' . $user->id . '_' . time(),
                    'amount_usd' => 30.00,
                    'crypto_amount' => 0,
                    'crypto_currency' => 'ADMIN',
                    'plan' => 'premium_plus',
                    'status' => 'confirmed',
                ]
            );

            $bonus = (int)Setting::get('premium_plus_points_monthly', 11000);
            if ($request->boolean('award_points', true)) {
                $user->increment('points', $bonus);
            }

            return redirect()->back()->with('success', "Upgraded @{$user->username} to Premium+ ($30/VIP) with {$bonus} points awarded!");
        } elseif ($plan === 'premium') {
            $user->badge_pro = 1;
            $user->badge_vip = 0;
            $user->save();

            \App\Models\Donation::updateOrCreate(
                ['user_id' => $user->id, 'gateway' => 'admin'],
                [
                    'order_id' => 'ADMIN_SUB_' . $user->id,
                    'payment_id' => 'ADMIN_SUB_' . $user->id . '_' . time(),
                    'amount_usd' => 15.00,
                    'crypto_amount' => 0,
                    'crypto_currency' => 'ADMIN',
                    'plan' => 'premium',
                    'status' => 'confirmed',
                ]
            );

            $bonus = (int)Setting::get('premium_points_monthly', 5000);
            if ($request->boolean('award_points', true)) {
                $user->increment('points', $bonus);
            }

            return redirect()->back()->with('success', "Set @{$user->username} to Premium ($15/PRO) with {$bonus} points awarded!");
        } else {
            // Free plan
            $user->badge_pro = 0;
            $user->badge_vip = 0;
            $user->save();

            \App\Models\Donation::where('user_id', $user->id)
                ->whereIn('status', ['confirmed', 'finished'])
                ->update(['status' => 'refunded']);

            return redirect()->back()->with('success', "Downgraded @{$user->username} to Standard Free plan.");
        }
    }

    public function adjustPoints($id, Request $request)
    {
        $this->checkAdmin();
        $user = User::findOrFail($id);
        $amount = (int)$request->input('points', 0);
        $action = $request->input('action', 'add');

        if ($action === 'add') {
            $user->increment('points', $amount);
        } else {
            $user->points = max(0, $user->points - $amount);
            $user->save();
        }

        return redirect()->back()->with('success', "Adjusted points for @{$user->username}. Current balance: {$user->points}.");
    }

    public function toggleBlogStatus($id)
    {
        $this->checkAdmin();
        $blog = Blog::findOrFail($id);
        $blog->status = ($blog->status === 'active') ? 'suspended' : 'active';
        $blog->save();

        return redirect()->back()->with('success', "Blog @{$blog->slug} status is now {$blog->status}.");
    }

    public function toggleBlogFeatured($id)
    {
        $this->checkAdmin();
        $blog = Blog::findOrFail($id);
        $blog->is_featured = !$blog->is_featured;
        $blog->save();

        return redirect()->back()->with('success', "Blog @{$blog->slug} featured status toggled.");
    }

    public function deleteBlog($id)
    {
        $this->checkAdmin();
        $blog = Blog::findOrFail($id);
        $slug = $blog->slug;

        // Cascade delete blog posts and clean promotions
        try {
            Promotion::where('blog_id', $blog->id)->delete();
            $blog->posts()->delete();
            $blog->delete();
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Error deleting blog: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', "Blog @{$slug} and all its posts have been permanently deleted.");
    }

    public function reviewVerification($id, Request $request)
    {
        $this->checkAdmin();
        $v = Verification::findOrFail($id);
        $action = $request->input('action'); // 'approve', 'reject'

        if ($action === 'approve') {
            $v->status = 'approved';
            $v->user->badge_id = true;
            $v->user->save();
        } else {
            $v->status = 'rejected';
        }
        $v->admin_notes = $request->input('notes');
        $v->save();

        return redirect()->back()->with('success', "Verification for @{$v->user->username} has been {$v->status}.");
    }

    public function replyTicket($id, Request $request)
    {
        $this->checkAdmin();
        $ticket = SupportTicket::findOrFail($id);
        $ticket->admin_reply = $request->input('reply');
        $ticket->status = 'replied';
        $ticket->save();

        return redirect()->back()->with('success', "Reply saved for ticket #{$ticket->id}.");
    }

    public function resolveReport($id)
    {
        $this->checkAdmin();
        $report = Report::findOrFail($id);
        $report->status = 'resolved';
        $report->save();

        return redirect()->back()->with('success', "Report #{$report->id} marked as resolved.");
    }

    public function createAd(Request $request)
    {
        $this->checkAdmin();
        $request->validate([
            'title' => 'required|string|max:150',
            'link_url' => 'required|url',
        ]);

        Advertisement::create([
            'title' => $request->input('title'),
            'sponsor_name' => $request->input('sponsor_name'),
            'link_url' => $request->input('link_url'),
            'image_url' => $request->input('image_url') ?: 'hero.jpg',
            'description' => $request->input('description'),
            'placement' => $request->input('placement', 'sidebar'),
            'is_active' => (bool)$request->input('is_active', true),
        ]);

        return redirect()->back()->with('success', 'Advertisement created successfully.');
    }

    public function deleteAd($id)
    {
        $this->checkAdmin();
        $ad = Advertisement::findOrFail($id);
        $ad->delete();
        return redirect()->back()->with('success', 'Advertisement deleted.');
    }

    public function toggleAd($id)
    {
        $this->checkAdmin();
        $ad = Advertisement::findOrFail($id);
        $ad->is_active = !$ad->is_active;
        $ad->save();
        return redirect()->back()->with('success', 'Advertisement status updated.');
    }

    public function addSlide(Request $request)
    {
        $this->checkAdmin();
        $request->validate([
            'title' => 'required|string|max:150',
            'text' => 'required|string|max:300',
        ]);

        $currentSlides = hero_slides();
        $newSlide = [
            'title' => $request->input('title'),
            'text' => $request->input('text'),
            'img' => $request->input('img') ?: 'hero.jpg',
            'alt' => $request->input('alt') ?: $request->input('title'),
            'color' => $request->input('color') ?: '#ff2bd6',
            'x' => 50, 'y' => 30, 'z' => 1.05, 'mx' => 50, 'my' => 25, 'mz' => 1.15,
            'cta' => [
                [
                    'label' => $request->input('cta1_label') ?: 'Explore Now',
                    'href' => $request->input('cta1_url') ?: '/register',
                    'class' => 'btn-fill'
                ],
                [
                    'label' => $request->input('cta2_label') ?: 'Trending',
                    'href' => $request->input('cta2_url') ?: '/trending',
                    'class' => 'btn-line'
                ]
            ]
        ];

        $currentSlides[] = $newSlide;
        Setting::set('hero_slides', json_encode($currentSlides));

        return redirect()->back()->with('success', 'New slide added to homepage hero slider.');
    }

    public function deleteSlide($index)
    {
        $this->checkAdmin();
        $currentSlides = hero_slides();
        $idx = (int)$index;
        if (isset($currentSlides[$idx])) {
            array_splice($currentSlides, $idx, 1);
            Setting::set('hero_slides', json_encode(array_values($currentSlides)));
            return redirect()->back()->with('success', 'Slide removed from hero slider.');
        }

        return redirect()->back()->with('error', 'Slide not found.');
    }

    public function testSmtp(Request $request)
    {
        $this->checkAdmin();
        $testEmail = trim($request->input('test_email', ''));
        if (empty($testEmail) || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['ok' => false, 'reason' => 'Invalid email address provided for test.']);
        }

        $result = send_welcome_email($testEmail, 'AdminTester', 'admin');
        if (!empty($result['success'])) {
            return response()->json(['ok' => true, 'message' => 'Test welcome email sent successfully to ' . $testEmail . '!']);
        }

        return response()->json(['ok' => false, 'reason' => $result['reason'] ?? 'Failed to send test email. Check host/credentials.']);
    }

    public function updateCommunityCover(Request $request)
    {
        $this->checkAdmin();
        $cover = trim($request->input('community_cover', ''));
        if ($request->hasFile('community_cover_file')) {
            $file = $request->file('community_cover_file');
            if ($file->getSize() > 5 * 1024 * 1024) {
                return redirect()->back()->with('error', 'Cover image exceeds 5MB limit.');
            }
            $filename = 'community_cover_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads', $filename, 'public');
            $cover = $path;
        }

        if (!empty($cover)) {
            Setting::set('community_cover', $cover);
        }

        return redirect()->back()->with('success', 'Community Hub cover banner updated successfully!');
    }
}
