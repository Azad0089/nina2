<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Follow;
use App\Models\Blog;
use App\Models\Post;
use App\Models\User;
use App\Models\Message;
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

    public function getComments(Request $request)
    {
        $postId = (int)$request->input('post_id');
        $post = Post::with(['blog.user', 'comments.user'])->find($postId);

        if (!$post) {
            return response()->json(['ok' => false, 'message' => 'Post not found.'], 404);
        }

        $commentsList = $post->comments->map(function ($c) {
            $u = $c->user;
            return [
                'id' => $c->id,
                'user_id' => $c->user_id,
                'username' => $u ? $u->username : 'Member',
                'badge' => $u ? ($u->badge_vip ? 'vip' : ($u->badge_id ? 'id' : ($u->badge_pro ? 'pro' : ''))) : '',
                'avatar' => $u && $u->mainBlog && $u->mainBlog->avatar_path ? media_url($u->mainBlog->avatar_path) : '',
                'initial' => initial($u ? $u->username : 'M'),
                'color' => $u && $u->mainBlog && $u->mainBlog->color ? $u->mainBlog->color : '#ff2bd6',
                'comment' => e($c->comment),
                'created_at' => $c->created_at ? $c->created_at->diffForHumans() : 'just now',
            ];
        });

        return response()->json([
            'ok' => true,
            'post' => [
                'id' => $post->id,
                'blog' => $post->blog ? $post->blog->slug : 'creator',
                'title' => $post->heading ?: ($post->caption ?: ($post->quote_text ?: 'Post #' . $post->id)),
                'color' => $post->blog ? $post->blog->color : '#ff2bd6',
            ],
            'count' => $commentsList->count(),
            'comments' => $commentsList,
            'is_logged_in' => Auth::check(),
            'current_user' => Auth::check() ? [
                'id' => Auth::id(),
                'username' => Auth::user()->username,
                'badge' => Auth::user()->badge_vip ? 'vip' : (Auth::user()->badge_id ? 'id' : (Auth::user()->badge_pro ? 'pro' : '')),
                'initial' => initial(Auth::user()->username),
            ] : null,
        ]);
    }

    public function follow(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['ok' => false, 'message' => 'Please sign in to follow blogs.'], 401);
        }

        $rawBlog = trim((string)$request->input('blog'));
        $slug = ltrim($rawBlog, '@');
        $blog = null;

        if (is_numeric($slug)) {
            $blog = Blog::find((int)$slug);
        }

        if (!$blog) {
            $blog = Blog::whereRaw('LOWER(slug) = ?', [strtolower($slug)])->first();
        }

        if (!$blog) {
            $userTarget = \App\Models\User::whereRaw('LOWER(username) = ?', [strtolower($slug)])->first();
            if ($userTarget) {
                $blog = $userTarget->mainBlog ?? $userTarget->blogs()->first();
            }
        }

        if (!$blog) {
            return response()->json(['ok' => false, 'message' => 'Blog not found.'], 404);
        }

        $userId = Auth::id();
        if ($blog->user_id === $userId) {
            return response()->json(['ok' => false, 'message' => 'You cannot follow your own blog.'], 422);
        }

        $existing = Follow::where('user_id', $userId)->where('blog_id', $blog->id)->first();
        if ($existing) {
            $existing->delete();
            $following = false;
        } else {
            Follow::create(['user_id' => $userId, 'blog_id' => $blog->id, 'created_at' => now()]);
            $following = true;

            // Send email notification to blog owner if enabled
            $blogOwner = $blog->user;
            if ($blogOwner && $blogOwner->id !== $userId) {
                try {
                    send_new_follower_email($blogOwner, Auth::user(), $blog);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('New follower notification error: ' . $e->getMessage());
                }
            }
        }

        $count = $blog->followers()->count();
        return response()->json([
            'ok' => true,
            'following' => $following,
            'count' => $count,
            'blog' => $blog->slug,
            'blog_id' => $blog->id,
            'message' => $following ? 'Now following @' . $blog->slug : 'Unfollowed @' . $blog->slug
        ]);
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
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'badge' => $user->badge_vip ? 'vip' : ($user->badge_id ? 'id' : ($user->badge_pro ? 'pro' : '')),
                    'avatar' => $user->mainBlog && $user->mainBlog->avatar_path ? media_url($user->mainBlog->avatar_path) : '',
                    'initial' => initial($user->username),
                    'color' => $user->mainBlog && $user->mainBlog->color ? $user->mainBlog->color : '#ff2bd6',
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
        $details = $request->input('details');

        \App\Models\Report::create([
            'user_id' => Auth::id(),
            'target_type' => $target_type,
            'target_id' => (string)$target_id,
            'reason' => $reason,
            'details' => $details,
            'status' => 'open',
        ]);

        if ($request->wantsJson() || $request->ajax() || $request->is('*/actions/*')) {
            return response()->json(['ok' => true, 'message' => 'Report received. Our moderation team will review it.']);
        }

        return redirect()->back()->with('success', 'Report received. Our moderation team will review it.');
    }

    public function contact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'message' => 'required|string|max:2000',
        ]);

        $message = $request->input('message');
        if ($request->filled('link')) {
            $message = "Target Link: " . $request->input('link') . "\n\n" . $message;
        }

        \App\Models\SupportTicket::create([
            'user_id' => auth()->id(),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'topic' => $request->input('topic', 'General support'),
            'message' => $message,
            'status' => 'open',
        ]);

        return redirect()->back()->with('success', 'Your message has been sent successfully. We will respond shortly!');
    }

    public function sendPrivateMessage(Request $request)
    {
        if (!Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'message' => 'Please sign in to send private messages.'], 401);
            }
            return redirect()->route('login')->with('error', 'Please sign in to send private messages.');
        }

        $request->validate([
            'body' => 'required|string|min:1|max:3000',
            'subject' => 'nullable|string|max:190',
        ]);

        $recipientId = $request->input('recipient_id');
        $recipientUsername = $request->input('recipient_username') ?: $request->input('recipient');

        $recipient = null;
        if ($recipientId) {
            $recipient = User::find($recipientId);
        } elseif ($recipientUsername) {
            $recipient = User::whereRaw('LOWER(username) = ?', [strtolower(ltrim($recipientUsername, '@'))])->first();
        }

        if (!$recipient) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'message' => 'Recipient user not found.'], 404);
            }
            return redirect()->back()->with('error', 'Recipient user not found.');
        }

        if ($recipient->id === Auth::id()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'message' => 'You cannot send a private message to yourself.'], 422);
            }
            return redirect()->back()->with('error', 'You cannot send a private message to yourself.');
        }

        $msg = Message::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $recipient->id,
            'subject' => $request->input('subject') ?: 'Private message',
            'body' => trim($request->input('body')),
            'is_read' => false,
        ]);

        try {
            send_private_message_email($recipient, Auth::user(), $msg);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Private message notification error: ' . $e->getMessage());
        }

        if ($request->wantsJson() || $request->ajax() || $request->is('*/actions/*')) {
            return response()->json([
                'ok' => true,
                'message' => 'Your message to @' . $recipient->username . ' has been sent!',
                'message_id' => $msg->id,
            ]);
        }

        return redirect()->back()->with('success', 'Your private message to @' . $recipient->username . ' has been sent!');
    }

    public function deletePost(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['ok' => false, 'message' => 'Please sign in.'], 401);
        }

        $postId = (int)$request->input('post_id', $request->input('id'));
        $user = Auth::user();

        $post = Post::with('blog')->find($postId);
        if (!$post) {
            return response()->json(['ok' => false, 'message' => 'Post not found.'], 404);
        }

        // Verify that post belongs to a blog owned by current user (or current user is admin)
        $isOwner = $post->blog && ($post->blog->user_id === $user->id);
        if (!$isOwner && !$user->is_admin) {
            return response()->json(['ok' => false, 'message' => 'You can only delete your own posts.'], 403);
        }

        $post->delete();

        if ($request->wantsJson() || $request->ajax() || $request->is('*/actions/*')) {
            return response()->json(['ok' => true, 'message' => 'Post deleted successfully.', 'post_id' => $postId]);
        }

        return redirect()->back()->with('success', 'Post deleted successfully.');
    }

    public function togglePin(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['ok' => false, 'message' => 'Please sign in.'], 401);
        }

        $postId = (int)$request->input('post_id', $request->input('id'));
        $user = Auth::user();

        $post = Post::with('blog')->find($postId);
        if (!$post) {
            return response()->json(['ok' => false, 'message' => 'Post not found.'], 404);
        }

        $isOwner = $post->blog && ($post->blog->user_id === $user->id);
        if (!$isOwner && !$user->is_admin) {
            return response()->json(['ok' => false, 'message' => 'You can only pin/unpin posts on your own blogs.'], 403);
        }

        if ($post->is_pinned) {
            $post->is_pinned = false;
            $post->save();
            return response()->json(['ok' => true, 'is_pinned' => false, 'message' => 'Post unpinned from blog feed.']);
        } else {
            $pinnedCount = Post::where('blog_id', $post->blog_id)->where('is_pinned', true)->count();
            if ($pinnedCount >= 3) {
                return response()->json(['ok' => false, 'message' => 'Maximum limit of 3 pinned posts per blog feed reached. Unpin another post first.'], 400);
            }
            $post->is_pinned = true;
            $post->save();
            return response()->json(['ok' => true, 'is_pinned' => true, 'message' => 'Post pinned to top of blog feed!']);
        }
    }
}
