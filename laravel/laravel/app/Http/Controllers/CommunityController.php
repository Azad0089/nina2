<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;

class CommunityController extends Controller
{
    public function index(Request $request)
    {
        $community = [
            'title' => Setting::get('community_title', 'Nina.Sucks Community'),
            'tagline' => Setting::get('community_tagline', 'The uncensored hub for creator discovery & expression.'),
            'bio' => Setting::get('community_bio', "Welcome to the Nina.Sucks official community!\nHere creators, writers, curators, and free thinkers showcase their blogs, exchange thoughts, and grow without algorithm walls.\nFeel free to explore our featured creator blogs or start your own in 30 seconds."),
            'website1' => Setting::get('community_url1', 'https://nina.sucks'),
            'website2' => Setting::get('community_url2', 'https://t.me/ninasucks'),
            'website3' => Setting::get('community_url3', 'https://twitter.com/ninasucks'),
            'email' => Setting::get('community_email', 'community@nina.sucks'),
            'color' => Setting::get('primary_color', '#ff2bd6'),
        ];

        $featuredCreators = Blog::with('user')
            ->where('status', 'active')
            ->where(function($q) {
                $q->where('is_featured', 1)
                  ->orWhereHas('user', function($uq) {
                      $uq->where('badge_vip', 1)
                        ->orWhere('badge_id', 1)
                        ->orWhere('badge_pro', 1);
                  });
            })
            ->take(12)
            ->get();

        $communityPosts = Post::with(['blog.user'])
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->take(12)
            ->get();

        $stats = [
            'total_creators' => User::where('status', 'active')->count(),
            'total_blogs' => Blog::where('status', 'active')->count(),
            'total_posts' => Post::where('status', 'published')->count(),
        ];

        return view('community', compact('community', 'featuredCreators', 'communityPosts', 'stats'));
    }
}
