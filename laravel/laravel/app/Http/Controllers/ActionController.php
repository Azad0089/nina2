<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Follow;
use App\Models\Blog;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class ActionController extends Controller
{
    public function like(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['ok' => false, 'message' => 'Please sign in.'], 401);
        }

        $postId = (int)$request->input('post_id');
        $userId = Auth::id();

        $existing = Like::where('user_id', $userId)->where('post_id', $postId)->first();
        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            Like::create(['user_id' => $userId, 'post_id' => $postId, 'created_at' => now()]);
            $liked = true;
        }

        $count = Like::where('post_id', $postId)->count();
        return response()->json(['ok' => true, 'liked' => $liked, 'count' => $count]);
    }

    public function follow(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['ok' => false, 'message' => 'Please sign in to follow blogs.'], 401);
        }

        $slug = ltrim(trim((string)$request->input('blog')), '@');
        $blog = Blog::where('slug', $slug)->first();
        if (!$blog) {
            $userTarget = \App\Models\User::where('username', $slug)->first();
            if ($userTarget) {
                $blog = $userTarget->mainBlog ?? $userTarget->blogs()->first();
            }
        }

        if (!$blog) {
            return response()->json(['ok' => false, 'message' => 'Blog not found.'], 404);
        }

        $userId = Auth::id();
        $existing = Follow::where('user_id', $userId)->where('blog_id', $blog->id)->first();
        if ($existing) {
            $existing->delete();
            $following = false;
        } else {
            Follow::create(['user_id' => $userId, 'blog_id' => $blog->id, 'created_at' => now()]);
            $following = true;
        }

        $count = $blog->followers()->count();
        return response()->json(['ok' => true, 'following' => $following, 'count' => $count]);
    }

    public function reblog(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['ok' => false, 'message' => 'Please sign in to reblog posts.'], 401);
        }

        $postId = (int)$request->input('post_id');
        $original = Post::findOrFail($postId);
        $user = Auth::user();
        $myBlog = $user->mainBlog ?? $user->blogs()->first();

        if (!$myBlog) {
            $myBlog = Blog::create([
                'user_id' => $user->id,
                'slug' => strtolower($user->username),
                'title' => $user->username . "'s Blog",
                'status' => 'active',
                'is_main' => 1,
                'color' => '#ff2bd6',
            ]);
        }

        Post::create([
            'blog_id' => $myBlog->id,
            'type' => $original->type,
            'status' => 'published',
            'heading' => $original->heading,
            'body' => $original->body,
            'caption' => $original->caption,
            'media_path' => $original->media_path,
            'quote_text' => $original->quote_text,
            'quote_source' => $original->quote_source,
            'link_url' => $original->link_url,
            'link_title' => $original->link_title,
            'link_domain' => $original->link_domain,
            'chat_payload' => $original->chat_payload,
            'reblog_of_post_id' => $original->id,
            'published_at' => now(),
        ]);

        $reblogCount = Post::where('reblog_of_post_id', $original->id)->count();

        return response()->json([
            'ok' => true,
            'count' => $reblogCount,
            'message' => 'Post successfully reblogged to @' . $myBlog->slug . '!'
        ]);
    }

    public function comment(Request $request)
    {
        if (!Auth::check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'message' => 'Please sign in to comment.'], 401);
            }
            return redirect()->route('login')->with('error', 'Please sign in to leave a comment.');
        }

        $request->validate([
            'post_id' => 'required|integer',
            'comment' => 'required|string|max:1000',
        ]);

        $postId = (int)$request->input('post_id');
        $post = Post::findOrFail($postId);
        $user = Auth::user();

        $comment = \App\Models\Comment::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'comment' => trim($request->input('comment')),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'comment' => [
                    'id' => $comment->id,
                    'username' => $user->username,
                    'badge' => $user->badge_vip ? 'vip' : ($user->badge_id ? 'id' : ($user->badge_pro ? 'pro' : '')),
                    'comment' => e($comment->comment),
                    'created_at' => $comment->created_at->diffForHumans(),
                ],
                'count' => $post->comments()->count(),
                'message' => 'Comment posted!'
            ]);
        }

        return redirect()->back()->with('success', 'Comment posted successfully!');
    }

    public function report(Request $request)
    {
        $target_type = $request->input('target_type', 'post');
        $target_id = $request->input('target_id');
        $reason = $request->input('reason', 'Inappropriate content');

        \App\Models\Report::create([
            'target_type' => $target_type,
            'target_id' => (string)$target_id,
            'reason' => $reason,
            'status' => 'open',
        ]);

        return response()->json(['ok' => true, 'message' => 'Report received. Our moderation team will review it.']);
    }

    public function contact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'message' => 'required|string|max:2000',
        ]);

        \App\Models\SupportTicket::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'topic' => $request->input('topic', 'General Inquiry'),
            'message' => $request->input('message'),
            'status' => 'open',
        ]);

        return redirect()->back()->with('success', 'Your message has been sent successfully. We will respond shortly!');
    }
}
