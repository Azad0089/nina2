<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    public function show(Request $request, $slug)
    {
        $blog = Blog::with(['user'])->where('slug', $slug)->firstOrFail();
        $type = $request->query('type', 'all');

        $query = Post::where('blog_id', $blog->id)->where('status', 'published')->orderBy('published_at', 'desc');
        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $posts = $query->paginate(12);
        $followerCount = $blog->followers()->count();
        $isOwner = Auth::check() && Auth::id() === $blog->user_id;
        $isFollowing = Auth::check() ? Auth::user()->follows()->where('blog_id', $blog->id)->exists() : false;

        return view('blog', compact('blog', 'posts', 'type', 'followerCount', 'isOwner', 'isFollowing'));
    }

    public function legacyShow(Request $request)
    {
        $slug = $request->query('u', 'neon_vixen');
        return $this->show($request, $slug);
    }
}
