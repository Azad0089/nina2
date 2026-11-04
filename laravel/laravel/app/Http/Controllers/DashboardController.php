<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Post;
use App\Models\Blog;
use App\Models\Setting;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $blog = $user->mainBlog;
        $myPosts = $blog ? $blog->posts()->orderBy('published_at', 'desc')->get() : collect();

        return view('dashboard', [
            'section' => 'overview',
            'user' => $user,
            'blog' => $blog,
            'myPosts' => $myPosts,
        ]);
    }

    public function createPost(Request $request)
    {
        $user = Auth::user();
        $blogs = $user->blogs;
        $selectedBlogId = $request->query('blog_id', $user->mainBlog?->id);
        $blog = $blogs->firstWhere('id', $selectedBlogId) ?? $user->mainBlog;

        return view('dashboard', [
            'section' => 'new_post',
            'user' => $user,
            'blog' => $blog,
            'blogs' => $blogs,
        ]);
    }

    public function storePost(Request $request)
    {
        $user = Auth::user();
        $blogId = $request->input('blog_id', $user->mainBlog?->id);
        $blog = $user->blogs()->findOrFail($blogId);

        $type = $request->input('type', 'text');
        $action = $request->input('post_action', 'publish'); // 'publish', 'queue', 'draft'

        // Check Queued posts limit (max 20 per blog queue)
        if ($action === 'queue') {
            if (in_array($type, ['video', 'chat'])) {
                return redirect()->back()->with('error', 'Only Text, Photo, Quote, and Link posts can be scheduled in the queue.');
            }
            $queuedCount = Post::where('blog_id', $blog->id)->where('status', 'queued')->count();
            if ($queuedCount >= 20) {
                return redirect()->back()->with('error', 'Blog queue is full! Maximum 20 queued posts allowed.');
            }
        }

        $mediaPath = null;
        $mediaType = null;

        // Handle MP4 video or photo upload
        if ($request->hasFile('media_file')) {
            $maxMb = (int)Setting::get('max_media_mb', 50);
            $request->validate([
                'media_file' => 'file|mimes:jpeg,png,jpg,gif,webp,mp4,mov,webm|max:' . ($maxMb * 1024),
            ]);

            $file = $request->file('media_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = 'post_' . $user->id . '_' . time() . '_' . Str::random(6) . '.' . $extension;
            $path = $file->storeAs('uploads', $filename, 'public');
            $mediaPath = $path;
            $mediaType = in_array($extension, ['mp4', 'mov', 'webm']) ? 'video' : 'photo';
            if ($mediaType === 'video') {
                $type = 'video';
            }
        }

        $status = match($action) {
            'queue' => 'queued',
            'draft' => 'draft',
            default => 'published',
        };

        $publishedAt = ($status === 'published') ? now() : null;

        $post = Post::create([
            'blog_id' => $blog->id,
            'type' => $type,
            'status' => $status,
            'heading' => $request->input('heading'),
            'body' => $request->input('body'),
            'caption' => $request->input('caption'),
            'media_path' => $mediaPath ?: $request->input('media_url'),
            'media_type' => $mediaType,
            'quote_text' => $request->input('quote'),
            'quote_source' => $request->input('source'),
            'link_url' => $request->input('url'),
            'link_title' => $request->input('link_title') ?: $request->input('heading'),
            'link_domain' => $request->input('url') ? parse_url($request->input('url'), PHP_URL_HOST) : null,
            'chat_payload' => $request->input('chat') ? json_encode(array_filter(explode("\n", (string)$request->input('chat')))) : null,
            'published_at' => $publishedAt,
        ]);

        if ($status === 'queued') {
            return redirect()->route('dashboard.queue')->with('success', 'Post added to your blog queue! (Slots used: ' . ($blog->posts()->where('status', 'queued')->count()) . '/20)');
        }

        return redirect()->route('post.show', ['id' => $post->id]);
    }

    public function queue()
    {
        $user = Auth::user();
        $blog = $user->mainBlog;
        $queuedPosts = $blog ? $blog->posts()->where('status', 'queued')->orderBy('created_at', 'asc')->get() : collect();

        return view('dashboard', [
            'section' => 'queue',
            'user' => $user,
            'blog' => $blog,
            'queuedPosts' => $queuedPosts,
        ]);
    }

    public function publishQueued($id)
    {
        $user = Auth::user();
        $post = Post::whereHas('blog', fn($q) => $q->where('user_id', $user->id))->findOrFail($id);
        $post->status = 'published';
        $post->published_at = now();
        $post->save();

        return redirect()->back()->with('success', 'Queued post published successfully!');
    }

    public function deleteQueued($id)
    {
        $user = Auth::user();
        $post = Post::whereHas('blog', fn($q) => $q->where('user_id', $user->id))->findOrFail($id);
        $post->delete();

        return redirect()->back()->with('success', 'Queued post deleted.');
    }

    public function posts()
    {
        $user = Auth::user();
        $blog = $user->mainBlog;
        $myPosts = $blog ? $blog->posts()->orderBy('published_at', 'desc')->get() : collect();

        return view('dashboard', [
            'section' => 'posts',
            'user' => $user,
            'blog' => $blog,
            'myPosts' => $myPosts,
        ]);
    }

    public function blogs()
    {
        $user = Auth::user();
        return view('dashboard', [
            'section' => 'blogs',
            'user' => $user,
            'blog' => $user->mainBlog,
            'allBlogs' => $user->blogs,
        ]);
    }

    public function storeNewBlog(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'slug' => 'required|alpha_dash|min:3|max:30|unique:blogs,slug',
            'title' => 'required|string|max:120',
            'tagline' => 'nullable|string|max:200',
            'color' => 'nullable|string|max:16',
        ]);

        Blog::create([
            'user_id' => $user->id,
            'slug' => strtolower($request->input('slug')),
            'title' => $request->input('title'),
            'tagline' => $request->input('tagline'),
            'color' => $request->input('color', '#ff2bd6'),
            'is_main' => 0,
            'status' => 'active',
            'searchable' => 1,
            'show_profile' => 0,
        ]);

        return redirect()->route('dashboard.blogs')->with('success', 'New blog @' . $request->input('slug') . ' created successfully!');
    }

    public function updateBlog(Request $request)
    {
        $user = Auth::user();
        $blogId = $request->input('blog_id', $user->mainBlog?->id);
        $blog = $user->blogs()->findOrFail($blogId);

        $data = $request->only(['title', 'tagline', 'bio', 'color', 'website1', 'website2', 'website3', 'contact_email']);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar_path'] = $path;
        }

        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('covers', 'public');
            $data['cover_path'] = $path;
        }

        $blog->update($data);

        return redirect()->route('dashboard.blogs')->with('success', 'Blog settings saved successfully.');
    }

    public function referrals()
    {
        $user = Auth::user();
        $referrals = $user->referrals()->latest()->get();
        $rewardPerRef = (int)Setting::get('referral_reward_points', 100);
        $totalEarned = $referrals->count() * $rewardPerRef;

        return view('dashboard', [
            'section' => 'referrals',
            'user' => $user,
            'blog' => $user->mainBlog,
            'referrals' => $referrals,
            'totalEarned' => $totalEarned,
            'rewardPerRef' => $rewardPerRef,
        ]);
    }
}

