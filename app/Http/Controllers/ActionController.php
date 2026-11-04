<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Follow;
use App\Models\Blog;
use App\Models\Post;
use App\Models\User;
use App\Models\Message;
use App\Models\Setting;
use App\Models\UserBlock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        ensure_private_message_tables();

        $body = trim((string) $request->input('body'));
        $subject = trim((string) $request->input('subject'));

        if (mb_strlen($body) > 3000) {
            return $this->messageFail($request, 'Messages can be up to 3000 characters.');
        }
        if (mb_strlen($subject) > 190) {
            return $this->messageFail($request, 'Subject can be up to 190 characters.');
        }

        $recipientId = $request->input('recipient_id');
        $recipientUsername = $request->input('recipient_username') ?: $request->input('recipient');

        $recipient = null;
        if ($recipientId) {
            $recipient = User::find($recipientId);
        } elseif ($recipientUsername) {
            $recipient = User::whereRaw('LOWER(username) = ?', [strtolower(ltrim((string) $recipientUsername, '@'))])->first();
        }

        if (!$recipient) {
            return $this->messageFail($request, 'Recipient user not found.', 404);
        }

        if ($recipient->id === Auth::id()) {
            return $this->messageFail($request, 'You cannot send a private message to yourself.');
        }

        $me = Auth::user();
        if ($me->hasBlocked($recipient->id)) {
            return $this->messageFail($request, 'You blocked @' . $recipient->username . '. Unblock them before sending a message.');
        }
        if ($me->isBlockedBy($recipient->id)) {
            return $this->messageFail($request, 'You cannot send messages to this user.');
        }

        $storedPath = null;
        try {
            $attachment = $this->storeMessageAttachment($request);
            $storedPath = $attachment['attachment_path'] ?? null;

            if ($body === '' && !$attachment) {
                return $this->messageFail($request, 'Write a message or attach a file.');
            }

            $msg = Message::create(array_merge([
                'sender_id' => $me->id,
                'recipient_id' => $recipient->id,
                'subject' => $subject !== '' ? $subject : ($attachment ? 'Attachment' : 'Private message'),
                'body' => $body,
                'is_read' => false,
            ], $attachment ?? []));
        } catch (\RuntimeException $e) {
            if ($storedPath) {
                Storage::disk('local')->delete($storedPath);
            }
            return $this->messageFail($request, $e->getMessage());
        } catch (\Throwable $e) {
            if ($storedPath) {
                Storage::disk('local')->delete($storedPath);
            }
            Log::warning('Private message send error: ' . $e->getMessage());
            return $this->messageFail($request, 'Could not send your message. Please try again.');
        }

        try {
            send_private_message_email($recipient, $me, $msg);
        } catch (\Throwable $e) {
            Log::warning('Private message notification error: ' . $e->getMessage());
        }

        $ok = 'Your message to @' . $recipient->username . ' has been sent!';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => $ok,
                'message_id' => $msg->id,
            ]);
        }

        return redirect()->back()->with('success', $ok);
    }

    public function toggleBlock(Request $request)
    {
        if (!Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'message' => 'Please sign in to block users.'], 401);
            }
            return redirect()->route('login')->with('error', 'Please sign in to block users.');
        }

        ensure_private_message_tables();

        $target = null;
        if ($request->filled('user_id')) {
            $target = User::find($request->input('user_id'));
        } elseif ($request->filled('username')) {
            $target = User::whereRaw('LOWER(username) = ?', [strtolower(ltrim((string) $request->input('username'), '@'))])->first();
        }

        if (!$target) {
            return $this->messageFail($request, 'User not found.', 404);
        }
        if ($target->id === Auth::id()) {
            return $this->messageFail($request, 'You cannot block yourself.');
        }

        $existing = UserBlock::where('blocker_id', Auth::id())->where('blocked_id', $target->id)->first();
        if ($existing) {
            $existing->delete();
            $blocked = false;
            $message = 'You unblocked @' . $target->username . '. You can message each other again.';
        } else {
            UserBlock::create([
                'blocker_id' => Auth::id(),
                'blocked_id' => $target->id,
            ]);
            $blocked = true;
            $message = 'You blocked @' . $target->username . '. They can no longer message you.';
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'blocked' => $blocked, 'message' => $message]);
        }

        return redirect()->back()->with('success', $message);
    }

    private function messageFail(Request $request, string $message, int $status = 422)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => false, 'message' => $message], $status);
        }

        return redirect()->back()->withInput()->with('error', $message);
    }

    private function messageFileLimitMb(): float
    {
        $maxMb = (float) Setting::get('max_message_file_mb', 10);
        if ($maxMb < 0) {
            return 0;
        }
        if ($maxMb > 500) {
            return 500;
        }

        return $maxMb;
    }

    private function storeMessageAttachment(Request $request): ?array
    {
        if (!$request->hasFile('attachment')) {
            return null;
        }

        $file = $request->file('attachment');
        $maxMb = $this->messageFileLimitMb();
        if ($maxMb <= 0) {
            throw new \RuntimeException('File attachments in private messages are turned off.');
        }
        if (!$file || !$file->isValid()) {
            throw new \RuntimeException('The file could not be uploaded. It may be larger than the ' . $this->formatMb($maxMb) . ' MB limit.');
        }
        if ($file->getSize() <= 0) {
            throw new \RuntimeException('That file is empty.');
        }
        if ($file->getSize() > (int) round($maxMb * 1024 * 1024)) {
            throw new \RuntimeException('That file is larger than the ' . $this->formatMb($maxMb) . ' MB limit for private messages.');
        }

        $original = basename(str_replace('\\', '/', (string) $file->getClientOriginalName()));
        $parts = array_values(array_filter(explode('.', strtolower($original)), fn ($part) => $part !== ''));
        $ext = $parts ? (string) end($parts) : '';
        $dangerous = ['php', 'phtml', 'php3', 'php4', 'php5', 'phar', 'exe', 'js', 'html', 'htm', 'svg', 'bat', 'cmd', 'sh', 'cgi', 'pl', 'asp', 'aspx', 'htaccess'];
        foreach ($parts as $part) {
            if (in_array($part, $dangerous, true)) {
                throw new \RuntimeException('That file type is not allowed in private messages.');
            }
        }

        $allowed = [
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'webp' => ['image/webp'],
            'mp4' => ['video/mp4', 'application/octet-stream'],
            'webm' => ['video/webm', 'application/octet-stream'],
            'mov' => ['video/quicktime', 'application/octet-stream'],
            'mp3' => ['audio/mpeg', 'audio/mp3', 'application/octet-stream'],
            'wav' => ['audio/wav', 'audio/x-wav', 'application/octet-stream'],
            'ogg' => ['audio/ogg', 'application/ogg', 'application/octet-stream'],
            'm4a' => ['audio/mp4', 'audio/x-m4a', 'application/octet-stream'],
            'pdf' => ['application/pdf'],
            'txt' => ['text/plain'],
            'zip' => ['application/zip', 'application/x-zip-compressed', 'application/octet-stream'],
        ];
        if (!isset($allowed[$ext])) {
            throw new \RuntimeException('Allowed files: JPG, PNG, GIF, WEBP, MP4, WEBM, MOV, MP3, WAV, OGG, M4A, PDF, TXT, and ZIP.');
        }

        $mime = strtolower((string) $file->getMimeType());
        if ($mime !== '' && !in_array($mime, $allowed[$ext], true)) {
            throw new \RuntimeException('That file does not match an allowed private message format.');
        }
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true) && @getimagesize($file->getRealPath()) === false) {
            throw new \RuntimeException('That image file could not be read.');
        }

        $safeName = preg_replace('/[^\w.\- ()]+/u', '_', $original) ?: ('file.' . $ext);
        $safeName = mb_substr($safeName, 0, 180);
        $stored = $file->storeAs('message-attachments', (string) Str::uuid() . '.' . $ext, 'local');
        if (!$stored) {
            throw new \RuntimeException('The file could not be saved. Please try again.');
        }

        return [
            'attachment_path' => $stored,
            'attachment_name' => $safeName,
            'attachment_mime' => $mime !== '' ? $mime : ($allowed[$ext][0] ?? 'application/octet-stream'),
            'attachment_size' => $file->getSize(),
        ];
    }

    private function formatMb(float $mb): string
    {
        return rtrim(rtrim(number_format($mb, 1, '.', ''), '0'), '.');
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
}
