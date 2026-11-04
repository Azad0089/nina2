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
        $type = $request->query('type', 'all');
        $view = $request->query('view', 'grid');

        $query = Post::with(['blog.user'])->where('status', 'published')->orderBy('published_at', 'desc');
        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $posts = $query->take(8)->get();
        $featuredBlogs = Blog::where('status', 'active')->where('is_featured', 1)->take(6)->get();
        $activeAds = Advertisement::where('is_active', true)->orderBy('created_at', 'desc')->get();

        return view('explore', compact('posts', 'featuredBlogs', 'activeAds', 'type', 'view'));
    }

    public function search(Request $request)
    {
        $tag = $request->query('tag') ?? $request->query('q');
        return redirect()->route('trending', ['tag' => $tag]);
    }

    public function tag($tag)
    {
        return redirect()->route('trending', ['tag' => $tag]);
    }

    public function feed(Request $request)
    {
        $offset = (int)$request->query('offset', 0);
        $limit  = min(24, max(1, (int)$request->query('limit', 6)));
        $type   = $request->query('type', 'all');

        $query = Post::with(['blog.user'])->where('status', 'published')->orderBy('published_at', 'desc');
        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $posts = $query->skip($offset)->take($limit)->get();
        $html = '';
        foreach ($posts as $post) {
            $html .= view('components.card', ['p' => $post])->render();
        }

        return response($html, 200)->header('Content-Type', 'text/html; charset=utf-8');
    }

    public function trending()
    {
        $topPosts = Post::with(['blog.user'])->where('status', 'published')->orderBy('views_count', 'desc')->take(6)->get();
        $featuredBlogs = Blog::where('status', 'active')->where('is_featured', 1)->take(6)->get();

        return view('trending', compact('topPosts', 'featuredBlogs'));
    }
}
