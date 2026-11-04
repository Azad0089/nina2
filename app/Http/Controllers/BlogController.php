<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    /**
     * Show main profile / primary blog for a user at /u/{username}
     */
    public function showUser(Request $request, $username)
    {
        $user = User::with(['blogs' => function($q) {
            $q->where('status', 'active');
        }])->where('username', $username)->first();

        // If user not found, fallback to check if a blog exists with slug = username
        if (!$user) {
            $blog = Blog::with(['user'])->where('slug', $username)->first();
            if ($blog && $blog->user) {
                return redirect()->to(blog_url($blog));
            }
            abort(404, 'User or blog not found.');
        }

        // Get the primary/main blog for this user
        $blog = $user->blogs->firstWhere('is_main', 1) ?: $user->blogs->first();
        
        // If user has no blog record at all, fallback to an in-memory/stub blog
        if (!$blog) {
            $blog = new Blog([
                'user_id' => $user->id,
                'slug' => $user->username,
                'title' => $user->username,
                'tagline' => '',
                'color' => '#ff2bd6',
                'is_main' => 1,
                'status' => 'active',
            ]);
            $blog->user = $user;
        }

        return $this->renderBlogView($request, $blog, $user);
    }

    /**
     * Show a specific additional blog under a user at /u/{username}/{slug}
     */
    public function showUserBlog(Request $request, $username, $slug)
    {
        // If slug matches username, render the main user blog directly
        if (strtolower($username) === strtolower($slug)) {
            return $this->showUser($request, $username);
        }

        $user = User::with(['blogs' => function($q) {
            $q->where('status', 'active');
        }])->where('username', $username)->first();

        if ($user) {
            $blog = $user->blogs->firstWhere('slug', $slug);
            if ($blog) {
                return $this->renderBlogView($request, $blog, $user);
            }
        }

        // Fallback: check if blog exists by slug globally
        $blog = Blog::with(['user'])->where('slug', $slug)->firstOrFail();
        return $this->renderBlogView($request, $blog, $blog->user);
    }

    /**
     * Helper to query posts and render the blog blade template
     */
    private function renderBlogView(Request $request, Blog $blog, ?User $user = null)
    {
        publish_matured_queued_posts();

        $type = $request->query('type', 'all');

        $query = Post::where('blog_id', $blog->id ?: 0)->where('status', 'published')
            ->where(function($q) {
                $q->whereNull('is_community')->orWhere('is_community', 0);
            })
            ->where(function($q) {
                $q->whereNull('media_meta->is_community')->orWhere('media_meta->is_community', false);
            })
            ->when(\Illuminate\Support\Facades\Schema::hasColumn('posts', 'member_community_id'), function ($q) {
                $q->whereNull('member_community_id');
            })
            ->orderBy('published_at', 'desc');
        if ($type === 'photo' || $type === 'image' || $type === 'photos' || $type === 'images') {
            $query->where(function($q) {
                $q->whereIn('type', ['photo', 'image', 'photos', 'images'])
                  ->orWhere('media_type', 'photo')
                  ->orWhere('media_type', 'image')
                  ->orWhere(function($sub) {
                      $sub->whereNotNull('media_path')
                          ->where('media_path', '!=', '')
                          ->where('type', '!=', 'video');
                  });
            });
        } elseif ($type !== 'all') {
            $query->where('type', $type);
        }

        $posts = $query->paginate(12);
        $followerCount = $blog->id ? $blog->followers()->count() : 0;
        $isOwner = Auth::check() && ($blog->user_id ? Auth::id() === $blog->user_id : (Auth::user()->username === ($user->username ?? '')));
        $isFollowing = (Auth::check() && $blog->id) ? Auth::user()->follows()->where('blog_id', $blog->id)->exists() : false;

        $userBlogs = $user ? $user->blogs()->where('status', 'active')->get() : ($blog->user ? $blog->user->blogs()->where('status', 'active')->get() : collect([$blog]));
        $userCommunities = collect();
        $profileForCommunities = $user ?: $blog->user;
        if ($profileForCommunities) {
            try {
                ensure_platform_tables();
                $joinedIds = \App\Models\MemberCommunityUser::where('user_id', $profileForCommunities->id)->where('status', 'active')->pluck('community_id');
                $userCommunities = \App\Models\MemberCommunity::where('status', 'active')
                    ->where(function ($q) use ($profileForCommunities, $joinedIds) {
                        $q->where('owner_id', $profileForCommunities->id)->orWhereIn('id', $joinedIds);
                    })
                    ->orderBy('title')
                    ->get();
            } catch (\Throwable $e) {
                $userCommunities = collect();
            }
        }
        $profileForCommunities = $user ?: $blog->user;
        if ($profileForCommunities) {
            try {
                ensure_platform_tables();
                $joinedIds = \App\Models\MemberCommunityUser::where('user_id', $profileForCommunities->id)->where('status', 'active')->pluck('community_id');
                $userCommunities = \App\Models\MemberCommunity::where('status', 'active')
                    ->where(function ($q) use ($profileForCommunities, $joinedIds) {
                        $q->where('owner_id', $profileForCommunities->id)->orWhereIn('id', $joinedIds);
                    })
                    ->orderBy('title')
                    ->get();
            } catch (\Throwable $e) {
                $userCommunities = collect();
            }
        }

        $profileUser = $user ?: $blog->user;
        $isBlockedByMe = false;
        $hasBlockedMe = false;
        $messageBlockState = '';
        if (Auth::check() && $profileUser && Auth::id() !== $profileUser->id) {
            try {
                ensure_private_message_tables();
                $isBlockedByMe = Auth::user()->hasBlocked($profileUser->id);
                $hasBlockedMe = Auth::user()->isBlockedBy($profileUser->id);
                $messageBlockState = $isBlockedByMe ? 'by-me' : ($hasBlockedMe ? 'by-them' : '');
            } catch (\Throwable $e) {
                $isBlockedByMe = false;
                $hasBlockedMe = false;
                $messageBlockState = '';
            }
        }

        $messageMaxMb = 10;
        try {
            $messageMaxMb = (float) \App\Models\Setting::get('max_message_file_mb', 10);
        } catch (\Throwable $e) {}
        if ($messageMaxMb < 0) {
            $messageMaxMb = 0;
        }

        return view('blog', compact(
            'blog',
            'user',
            'userBlogs',
            'userCommunities',
            'posts',
            'type',
            'followerCount',
            'isOwner',
            'isFollowing',
            'isBlockedByMe',
            'hasBlockedMe',
            'messageBlockState',
            'messageMaxMb'
        ));
    }

    /**
     * Feed batch for infinite scrolling / AJAX loading at /u/{username}/feed or /u/{username}/{slug}/feed
     */
    public function userFeed(Request $request, $username, $slug = null)
    {
        publish_matured_queued_posts();

        if ($slug && strtolower($slug) !== strtolower($username)) {
            $blog = Blog::where('slug', $slug)->first();
        } else {
            $user = User::where('username', $username)->first();
            $blog = $user ? ($user->blogs()->where('is_main', 1)->first() ?: $user->blogs()->first()) : Blog::where('slug', $username)->first();
        }

        if (!$blog) {
            return response()->json(['posts' => []]);
        }

        $offset = max(0, (int)$request->query('offset', 0));
        $limit = min(24, max(1, (int)$request->query('limit', 6)));
        $type = $request->query('type', 'all');

        $query = Post::where('blog_id', $blog->id)->where('status', 'published')
            ->where(function($q) {
                $q->whereNull('is_community')->orWhere('is_community', 0);
            })
            ->where(function($q) {
                $q->whereNull('media_meta->is_community')->orWhere('media_meta->is_community', false);
            })
            ->when(\Illuminate\Support\Facades\Schema::hasColumn('posts', 'member_community_id'), function ($q) {
                $q->whereNull('member_community_id');
            })
            ->orderBy('published_at', 'desc');
        if ($type === 'photo' || $type === 'image' || $type === 'photos' || $type === 'images') {
            $query->where(function($q) {
                $q->whereIn('type', ['photo', 'image', 'photos', 'images'])
                  ->orWhere('media_type', 'photo')
                  ->orWhere('media_type', 'image')
                  ->orWhere(function($sub) {
                      $sub->whereNotNull('media_path')
                          ->where('media_path', '!=', '')
                          ->where('type', '!=', 'video');
                  });
            });
        } elseif ($type !== 'all') {
            $query->where('type', $type);
        }

        if ($request->filled('exclude')) {
            $excludeIds = array_filter(array_map('intval', explode(',', (string)$request->query('exclude'))));
            if (!empty($excludeIds)) {
                $query->whereNotIn('id', $excludeIds);
            }
        } else {
            $query->skip($offset);
        }

        $posts = $query->take($limit)->get();
        return view('partials.feed-batch', ['posts' => $posts]);
    }

    /**
     * Legacy /blog/{slug} route - redirects to canonical /u/... URL
     */
    public function show(Request $request, $slug)
    {
        $blog = Blog::with(['user'])->where('slug', $slug)->first();
        if ($blog && $blog->user) {
            return redirect()->to(blog_url($blog));
        }

        $user = User::where('username', $slug)->first();
        if ($user) {
            return redirect()->to('/u/' . $user->username);
        }

        abort(404, 'Blog not found.');
    }

    public function showSubdomain(Request $request, $subdomain)
    {
        if (in_array(strtolower($subdomain), ['www', 'api', 'admin', 'app', 'mail', 'staging', 'dev'])) {
            return redirect()->to(config('app.url', '/'));
        }
        return $this->show($request, $subdomain);
    }

    public function subdomainFeed(Request $request, $subdomain)
    {
        return $this->userFeed($request, $subdomain);
    }

    public function legacyShow(Request $request)
    {
        $slug = $request->query('u', 'neon_vixen');
        return $this->show($request, $slug);
    }

    /**
     * Dedicated All Featured & Promoted Creator Blogs Showcase page (/featured-blogs)
     * Lists all blogs that bought ads or are marked featured, paginated on bottom.
     */
    public function featuredList(Request $request)
    {
        try {
            \App\Models\Promotion::expireOverdue();
        } catch (\Throwable $e) {}

        $promotedBlogIds = [];
        try {
            $promotedBlogIds = \App\Models\Promotion::where('status', 'active')
                ->whereNotNull('blog_id')
                ->where(function($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->pluck('blog_id')
                ->unique()
                ->toArray();
        } catch (\Throwable $e) {}

        $query = Blog::where('status', 'active')
            ->where(function($q) use ($promotedBlogIds) {
                $q->where('is_featured', 1);
                if (!empty($promotedBlogIds)) {
                    $q->orWhereIn('id', $promotedBlogIds);
                }
            })
            ->with(['user', 'followers'])
            ->orderByRaw("CASE WHEN slug = 'admin' THEN 0 ELSE 1 END")
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc');

        $featuredBlogs = $query->paginate(24);

        return view('pages.featured-blogs', compact('featuredBlogs'));
    }
}

