<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Promotion;
use App\Models\Post;
use App\Models\Blog;
use App\Models\Setting;

class PromotionController extends Controller
{
    public function index()
    {
        Promotion::expireOverdue();

        $user = Auth::user();
        $userPosts = $user ? Post::whereHas('blog', fn($q) => $q->where('user_id', $user->id))->get() : collect();
        $userBlogs = $user ? Blog::where('user_id', $user->id)->get() : collect();
        $activePromotions = $user ? Promotion::where('user_id', $user->id)->with(['post', 'blog'])->orderBy('created_at', 'desc')->get() : collect();

        $cpmPackages = [
            ['id' => 'cpm_1k', 'name' => '1,000 Post Impressions', 'type' => 'views', 'views' => 1000, 'points' => (int)Setting::get('pkg_cpm_1k', 500)],
            ['id' => 'cpm_5k', 'name' => '5,000 Post Impressions', 'type' => 'views', 'views' => 5000, 'points' => (int)Setting::get('pkg_cpm_5k', 2000)],
            ['id' => 'cpm_25k', 'name' => '25,000 Post Impressions', 'type' => 'views', 'views' => 25000, 'points' => (int)Setting::get('pkg_cpm_25k', 8000)],
            ['id' => 'cpm_100k', 'name' => '100,000 Post Impressions', 'type' => 'views', 'views' => 100000, 'points' => (int)Setting::get('pkg_cpm_100k', 25000)],
        ];

        $flatPackages = [
            ['id' => 'flat_1d', 'name' => '24 Hours Explore Spotlight', 'type' => 'flat', 'days' => 1, 'points' => (int)Setting::get('pkg_flat_1d', 350)],
            ['id' => 'flat_7d', 'name' => '7 Days Explore Spotlight', 'type' => 'flat', 'days' => 7, 'points' => (int)Setting::get('pkg_flat_7d', 2000)],
            ['id' => 'flat_30d', 'name' => '30 Days Creator Placement', 'type' => 'flat', 'days' => 30, 'points' => (int)Setting::get('pkg_flat_30d', 7000)],
        ];

        return view('pages.advertising', compact('user', 'userPosts', 'userBlogs', 'activePromotions', 'cpmPackages', 'flatPackages'));
    }

    public function purchase(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to purchase advertising packages.');
        }

        $user = Auth::user();
        $packageId = $request->input('package_id');
        $target = $request->input('target');
        $postId = $request->input('post_id');
        $blogId = $request->input('blog_id');

        if ($target) {
            if (str_starts_with($target, 'blog_')) {
                $blogId = (int)str_replace('blog_', '', $target);
                $postId = null;
            } elseif (str_starts_with($target, 'post_')) {
                $postId = (int)str_replace('post_', '', $target);
                $blogId = null;
            }
        }

        $isCpm = str_starts_with($packageId, 'cpm_');
        if ($isCpm && !$postId) {
            $hasPosts = Post::whereHas('blog', fn($q) => $q->where('user_id', $user->id))->exists();
            if ($hasPosts) {
                return redirect()->back()->with('error', 'Impression packages deliver views to individual posts. Please select which post you would like to boost, or choose a Flat-Rate Spotlight Placement for your blog profile.');
            }
        }

        // If neither was explicitly specified for a flat rate, promote user's active blog
        if (!$postId && !$blogId) {
            $mainBlog = $user->mainBlog ?: $user->blogs()->first();
            $blogId = $mainBlog ? $mainBlog->id : null;
        }

        $packages = [
            'cpm_1k' => ['name' => '1,000 Post Impressions', 'views' => 1000, 'days' => 0, 'points' => (int)Setting::get('pkg_cpm_1k', 500)],
            'cpm_5k' => ['name' => '5,000 Post Impressions', 'views' => 5000, 'days' => 0, 'points' => (int)Setting::get('pkg_cpm_5k', 2000)],
            'cpm_25k' => ['name' => '25,000 Post Impressions', 'views' => 25000, 'days' => 0, 'points' => (int)Setting::get('pkg_cpm_25k', 8000)],
            'cpm_100k' => ['name' => '100,000 Post Impressions', 'views' => 100000, 'days' => 0, 'points' => (int)Setting::get('pkg_cpm_100k', 25000)],
            'flat_1d' => ['name' => '24 Hours Explore Spotlight', 'views' => 0, 'days' => 1, 'points' => (int)Setting::get('pkg_flat_1d', 350)],
            'flat_7d' => ['name' => '7 Days Explore Spotlight', 'views' => 0, 'days' => 7, 'points' => (int)Setting::get('pkg_flat_7d', 2000)],
            'flat_30d' => ['name' => '30 Days Creator Placement', 'views' => 0, 'days' => 30, 'points' => (int)Setting::get('pkg_flat_30d', 7000)],
        ];

        if (!isset($packages[$packageId])) {
            return redirect()->back()->with('error', 'Invalid promotion package selected.');
        }

        $pkg = $packages[$packageId];
        if ($user->points < $pkg['points']) {
            return redirect()->back()->with('error', 'Insufficient points balance! You need ' . $pkg['points'] . ' points. Earn more via referrals or upgrade to Premium.');
        }

        $user->decrement('points', $pkg['points']);

        $duration = $pkg['days'];
        $expiresAt = $duration > 0 ? now()->addDays($duration) : null;

        Promotion::create([
            'user_id' => $user->id,
            'post_id' => $postId ?: null,
            'blog_id' => $postId ? null : ($blogId ?: null),
            'package_name' => $pkg['name'],
            'placement' => 'explore_top',
            'points_spent' => $pkg['points'],
            'target_views' => $pkg['views'],
            'duration_days' => $duration,
            'starts_at' => now(),
            'expires_at' => $expiresAt,
            'status' => 'active',
        ]);

        $targetDesc = $postId ? 'Post #' . $postId : ($blogId ? 'Blog profile' : 'Explore feed');
        return redirect()->back()->with('success', 'Promotion activated successfully for ' . $targetDesc . '! ' . $pkg['points'] . ' points deducted.');
    }

    public function assignPost(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $promoId = (int)$request->input('promo_id');
        $postId = (int)$request->input('post_id');

        $promo = Promotion::where('id', $promoId)->where('user_id', $user->id)->first();
        if (!$promo) {
            return redirect()->back()->with('error', 'Campaign not found or unauthorized.');
        }

        $post = Post::find($postId);
        if (!$post) {
            return redirect()->back()->with('error', 'Selected post not found.');
        }

        $promo->update([
            'post_id' => $postId,
            'blog_id' => null,
        ]);

        return redirect()->back()->with('success', 'Success! Post #' . $postId . ' has been assigned to your ' . $promo->package_name . ' campaign. It is now boosted at the top of feeds!');
    }

    public function refund(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $promoId = (int)$request->input('promo_id');

        $promo = Promotion::where('id', $promoId)->where('user_id', $user->id)->first();
        if (!$promo) {
            return redirect()->back()->with('error', 'Campaign not found.');
        }

        $points = $promo->points_spent ?: 0;
        if ($points > 0) {
            $user->increment('points', $points);
        }

        $promo->delete();

        return redirect()->back()->with('success', 'Campaign canceled. Refund of ' . number_format($points) . ' points has been returned to your balance!');
    }
}
