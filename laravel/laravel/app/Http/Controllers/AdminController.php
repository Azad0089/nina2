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
        $allUsers = User::withCount('blogs')->orderBy('created_at', 'desc')->get();
        $allBlogs = Blog::with('user')->orderBy('created_at', 'desc')->get();
        $reports = Report::orderBy('created_at', 'desc')->get();
        $tickets = SupportTicket::orderBy('created_at', 'desc')->get();
        $verifications = Verification::with('user')->orderBy('created_at', 'desc')->get();
        $donations = Donation::with('user')->orderBy('created_at', 'desc')->get();
        $logins = UserLogin::with('user')->orderBy('logged_at', 'desc')->take(50)->get();
        $promotions = Promotion::with(['user', 'post', 'blog'])->orderBy('created_at', 'desc')->get();
        $allAds = Advertisement::orderBy('created_at', 'desc')->get();
        $heroSlides = hero_slides();

        $settings = Setting::pluck('value', 'key')->all();

        $title = ucfirst($section);

        return view('admin', compact(
            'section', 'allPosts', 'allUsers', 'allBlogs', 'reports', 'tickets',
            'verifications', 'donations', 'logins', 'promotions', 'allAds', 'heroSlides', 'settings', 'title'
        ));
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

    public function saveSettings(Request $request)
    {
        $this->checkAdmin();

        $allowed = [
            'site_title', 'meta_description', 'site_logo', 'site_favicon',
            'primary_color', 'secondary_color', 'head_scripts', 'donations_enabled',
            'referral_reward_points', 'max_media_mb', 'pkg_cpm_1k', 'pkg_cpm_5k',
            'pkg_cpm_25k', 'pkg_cpm_100k', 'pkg_flat_1d', 'pkg_flat_7d', 'pkg_flat_30d',
            'community_title', 'community_tagline', 'community_bio',
            'community_url1', 'community_url2', 'community_url3', 'community_email',
            'nowpayments_api_key', 'nowpayments_ipn_secret', 'bitcart_url',
            'google_client_id', 'google_client_secret', 'turnstile_site_key', 'turnstile_secret_key'
        ];

        foreach ($request->only($allowed) as $key => $value) {
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
}
