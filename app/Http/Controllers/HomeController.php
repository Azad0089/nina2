<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Blog;

use App\Models\Advertisement;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Auto-publish any matured scheduled posts
        publish_matured_queued_posts();

        $type = $request->query('type', 'all');
        $view = $request->query('view', 'grid');

        $query = Post::with(['blog.user'])->where('status', 'published')
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

        $postsCount = (clone $query)->count();
        $posts = $query->take(24)->get();

        // Fetch active post promotions and inject them at top with views increment
        try {
            \App\Models\Promotion::expireOverdue();
            $activePostPromos = \App\Models\Promotion::where('status', 'active')
                ->whereNotNull('post_id')
                ->where(function($q) {
                    $q->whereNull('target_views')->orWhereRaw('current_views < target_views');
                })
                ->get();

            if ($activePostPromos->isNotEmpty()) {
                $boostedPostIds = $activePostPromos->pluck('post_id')->unique();
                $boostedPosts = Post::whereIn('id', $boostedPostIds)
                    ->with(['blog', 'blog.user', 'likes', 'comments'])
                    ->get();

                foreach ($boostedPosts as $bp) {
                    $bp->is_promoted = true;
                    $promo = $activePostPromos->firstWhere('post_id', $bp->id);
                    if ($promo) {
                        $promo->increment('current_views');
                        $bp->increment('views_count');
                        if ($promo->target_views && $promo->current_views >= $promo->target_views) {
                            $promo->update(['status' => 'completed']);
                        }
                    }
                }
                $posts = $boostedPosts->concat($posts)->unique('id')->take(24);
            }
        } catch (\Throwable $e) {}

        // Fetch featured & promoted blogs respecting admin max setting, admin pinning, and fair rotation
        $featuredBlogs = Blog::getFeaturedForDisplay();
        $activeAds = Advertisement::where('is_active', true)->orderBy('created_at', 'desc')->get();

        // Dynamically compute trending tags from real posts
        $allPublished = Post::publicFeed()->get(['id', 'heading', 'caption', 'body', 'media_meta']);
        $tagCounts = [];
        foreach ($allPublished as $p) {
            foreach ($p->tags_list as $t) {
                $tClean = strtolower(trim(ltrim($t, '#')));
                if ($tClean !== '') {
                    $tagCounts[$tClean] = ($tagCounts[$tClean] ?? 0) + 1;
                }
            }
        }
        arsort($tagCounts);
        $trendingTags = !empty($tagCounts) ? array_keys(array_slice($tagCounts, 0, 10)) : ['neon', 'cyberpunk', 'aesthetic', 'art'];

        return view('explore', compact('posts', 'postsCount', 'featuredBlogs', 'activeAds', 'type', 'view', 'trendingTags'));
    }

    public function search(Request $request)
    {
        publish_matured_queued_posts();
        $needle = ltrim(trim((string) ($request->query('q') ?? $request->query('tag') ?? '')), '#');
        $posts = Post::publicFeed()->whereRaw('0=1')->paginate(12, ['*'], 'posts_page');
        $blogs = Blog::whereRaw('0=1')->paginate(12, ['*'], 'blogs_page');
        $tags = [];
        if ($needle !== '') {
            $like = '%' . addcslashes($needle, '%_\\') . '%';
            $posts = Post::with(['blog.user'])->publicFeed()
                ->where(function ($q) use ($like) {
                    $q->where('heading', 'like', $like)
                        ->orWhere('body', 'like', $like)
                        ->orWhere('caption', 'like', $like)
                        ->orWhere('quote_text', 'like', $like)
                        ->orWhere('link_title', 'like', $like)
                        ->orWhere('media_meta', 'like', $like);
                })
                ->orderByDesc('published_at')
                ->paginate(12, ['*'], 'posts_page')
                ->withQueryString();
            $blogs = Blog::with('user')->where('status', 'active')
                ->where(function ($q) use ($like) {
                    $q->where('title', 'like', $like)
                        ->orWhere('slug', 'like', $like)
                        ->orWhere('bio', 'like', $like)
                        ->orWhere('tagline', 'like', $like);
                })
                ->orderByDesc('id')
                ->paginate(12, ['*'], 'blogs_page')
                ->withQueryString();
            $rows = Post::publicFeed()
                ->where(function ($q) use ($like) {
                    $q->where('media_meta', 'like', $like)
                        ->orWhere('heading', 'like', $like)
                        ->orWhere('body', 'like', $like)
                        ->orWhere('caption', 'like', $like);
                })
                ->limit(300)
                ->get(['heading', 'caption', 'body', 'quote_text', 'media_meta']);
            foreach ($rows as $row) {
                foreach ($row->tags_list as $tag) {
                    if ($tag !== '' && str_contains($tag, strtolower($needle))) {
                        $tags[$tag] = ($tags[$tag] ?? 0) + 1;
                    }
                }
            }
            arsort($tags);
        }
        return view('search', [
            'query' => $needle,
            'posts' => $posts,
            'blogs' => $blogs,
            'tags' => $tags,
            'active_nav' => 'explore',
        ]);
    }

    public function tag($tag)
    {
        return redirect()->route('trending', ['tag' => $tag]);
    }

    public function feed(Request $request)
    {
        publish_matured_queued_posts();

        $offset = (int)$request->query('offset', 0);
        $limit  = min(24, max(1, (int)$request->query('limit', 24)));
        $type   = $request->query('type', 'all');

        $query = Post::with(['blog.user'])->where('status', 'published')
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
        $html = '';
        foreach ($posts as $post) {
            $html .= view('components.card', ['p' => $post])->render();
        }

        return response($html, 200)->header('Content-Type', 'text/html; charset=utf-8');
    }

    public function trending(Request $request)
    {
        publish_matured_queued_posts();

        $rawTag = $request->query('tag') ?? $request->query('q');
        $selectedTag = $rawTag ? strtolower(trim(ltrim($rawTag, '#'))) : null;

        // Query published posts (excluding isolated community-only posts)
        $query = Post::with(['blog.user'])->where('status', 'published')
            ->where(function($q) {
                $q->whereNull('is_community')->orWhere('is_community', 0);
            })
            ->where(function($q) {
                $q->whereNull('media_meta->is_community')->orWhere('media_meta->is_community', false);
            })
            ->when(\Illuminate\Support\Facades\Schema::hasColumn('posts', 'member_community_id'), function ($q) {
                $q->whereNull('member_community_id');
            });

        if ($selectedTag) {
            $query->where(function($q) use ($selectedTag) {
                $q->where('body', 'like', "%#{$selectedTag}%")
                  ->orWhere('caption', 'like', "%#{$selectedTag}%")
                  ->orWhere('heading', 'like', "%#{$selectedTag}%")
                  ->orWhere('heading', 'like', "%{$selectedTag}%")
                  ->orWhere('body', 'like', "%{$selectedTag}%")
                  ->orWhere('media_meta', 'like', "%{$selectedTag}%");
            });
        }

        // Fix: Sort strictly by REAL likes count descending!
        $topPosts = $query->withCount('likes')->orderByDesc('likes_count')->orderByDesc('views_count')->take(12)->get();

        // Fetch featured & promoted blogs respecting admin max setting, admin pinning, and fair rotation
        $featuredBlogs = Blog::getFeaturedForDisplay();

        // Calculate REAL tag frequencies dynamically from all published posts
        $allPublished = Post::where('status', 'published')
            ->when(\Illuminate\Support\Facades\Schema::hasColumn('posts', 'member_community_id'), function ($q) {
                $q->whereNull('member_community_id');
            })
            ->get(['id', 'heading', 'caption', 'body', 'quote_text', 'media_meta']);
        $tagCounts = [];
        foreach ($allPublished as $p) {
            $tList = $p->tags_list;
            foreach ($tList as $t) {
                $tClean = strtolower(trim(ltrim($t, '#')));
                if ($tClean !== '') {
                    $tagCounts[$tClean] = ($tagCounts[$tClean] ?? 0) + 1;
                }
            }
        }
        arsort($tagCounts);

        if (empty($tagCounts)) {
            $tagCounts = [
                'aesthetic' => 1,
                'cyberpunk' => 1,
                'neon' => 1,
            ];
        }

        return view('trending', compact('topPosts', 'featuredBlogs', 'tagCounts', 'selectedTag'));
    }
}
