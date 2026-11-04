<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Post;
use App\Models\Blog;
use App\Models\Setting;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
            new Middleware('verified'),
        ];
    }

    public function index()
    {
        $user = Auth::user();
        $blog = $user->mainBlog;
        $myPosts = $blog ? $blog->posts()->orderBy('published_at', 'desc')->get() : collect();
        $settings = Setting::pluck('value', 'key')->all();

        return view('dashboard', [
            'section' => 'overview',
            'user' => $user,
            'blog' => $blog,
            'myPosts' => $myPosts,
            'settings' => $settings,
        ]);
    }

    public function createPost(Request $request)
    {
        $user = Auth::user();
        $blogs = $user->blogs;
        $selectedBlogId = $request->query('blog_id', $user->mainBlog?->id);
        $blog = $blogs->firstWhere('id', $selectedBlogId) ?? $user->mainBlog;
        $settings = Setting::pluck('value', 'key')->all();

        return view('dashboard', [
            'section' => 'new_post',
            'user' => $user,
            'blog' => $blog,
            'blogs' => $blogs,
            'settings' => $settings,
        ]);
    }

    public function storePost(Request $request)
    {
        $user = Auth::user();
        $blogId = $request->input('blog_id', $user->mainBlog?->id);
        $blog = $user->blogs()->findOrFail($blogId);

        $type = $request->input('type', 'text');
        $action = $request->input('post_action', 'publish'); // 'publish', 'queue', 'draft'

        // Check Queued posts limit (max 20 per blog queue) & allowed formats
        if ($action === 'queue') {
            if (!in_array($type, ['text', 'photo', 'link'])) {
                return redirect()->back()->withInput()->with('error', 'Only text, image, and link posts can be scheduled in the queue. Video and chat formats cannot be queued.');
            }
            $queuedCount = Post::where('blog_id', $blog->id)->where('status', 'queued')->count();
            if ($queuedCount >= 20) {
                return redirect()->back()->withInput()->with('error', "Blog @{$blog->slug} queue is full! Maximum 20 queued posts allowed per blog.");
            }
        }

        $mediaPath = null;
        $mediaType = null;

        // Handle MP4 video or photo upload
        // Handle media upload
    if ($request->hasFile('media_file')) {
    
        $file = $request->file('media_file');
    
        if (!$file->isValid()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'The uploaded file is invalid: ' . $file->getErrorMessage());
        }
    
        $extension = strtolower($file->getClientOriginalExtension());
    
        $imageExtensions = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
        $videoExtensions = ['mp4', 'mov', 'webm'];
    
        $isImage = in_array($extension, $imageExtensions);
        $isVideo = in_array($extension, $videoExtensions);
    
        if (!$isImage && !$isVideo) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Unsupported media format.');
        }
    
        // Queue does not allow videos
        if ($action === 'queue' && $isVideo) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Video posts cannot be scheduled in the queue.');
        }
    
        $maxImageMb = (float) Setting::get('max_image_mb', 2);
        $maxVideoMb = (float) Setting::get('max_video_mb', 50);
    
        $maxSizeKb = $isVideo
            ? ($maxVideoMb * 1024)
            : ($maxImageMb * 1024);
    
        $request->validate([
            'media_file' => [
                'required',
                'file',
                'max:' . (int) $maxSizeKb,
                'mimes:jpeg,jpg,png,gif,webp,mp4,mov,webm',
            ],
        ]);
    
        $filename = 'post_' .
            $user->id . '_' .
            time() . '_' .
            Str::random(8) . '.' .
            $extension;
    
        // Save to storage/app/public/uploads
        $path = $file->storeAs(
            'uploads',
            $filename,
            'public'
        );
    
        if (!$path) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Unable to save uploaded file.');
        }
    
        $mediaPath = $path;
        $mediaType = $isVideo ? 'video' : 'photo';
    
        // Set post type according to uploaded media
        $type = $mediaType;
    
        // Verify physical file exists
        if (!Storage::disk('public')->exists($path)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'File was uploaded but could not be found in storage.');
        }
    } elseif ($type === 'photo' || $type === 'photos' || $type === 'image' || $type === 'images') {
            $type = 'photo';
            $mediaType = 'photo';
        }

        $status = match($action) {
            'queue' => 'queued',
            'draft' => 'draft',
            default => 'published',
        };

        $scheduledAt = null;
        if ($status === 'queued') {
            if ($request->filled('scheduled_at')) {
                try {
                    $scheduledAt = \Carbon\Carbon::parse($request->input('scheduled_at'));
                } catch (\Throwable $e) {}
            } elseif ($request->filled('schedule_month') && $request->filled('schedule_day') && $request->filled('schedule_time')) {
                try {
                    $year = $request->input('schedule_year', date('Y'));
                    $month = str_pad((string)$request->input('schedule_month'), 2, '0', STR_PAD_LEFT);
                    $day = str_pad((string)$request->input('schedule_day'), 2, '0', STR_PAD_LEFT);
                    $time = $request->input('schedule_time', '12:00');
                    $scheduledAt = \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$year}-{$month}-{$day} {$time}");
                } catch (\Throwable $e) {}
            }

            if (!$scheduledAt) {
                $lastQueued = Post::where('blog_id', $blog->id)
                    ->where('status', 'queued')
                    ->whereNotNull('published_at')
                    ->orderBy('published_at', 'desc')
                    ->first();
                $scheduledAt = $lastQueued ? \Carbon\Carbon::parse($lastQueued->published_at)->addHours(2) : now()->addHours(2);
            }
        }

        $publishedAt = ($status === 'published') ? now() : ($status === 'queued' ? $scheduledAt : null);

        $rawTags = $request->input('tags', '');
        $tagArray = [];
        if (!empty($rawTags)) {
            $tagArray = array_values(array_unique(array_filter(array_map(function($t) {
                return strtolower(trim(ltrim((string)$t, '#')));
            }, explode(',', (string)$rawTags)))));
        }

        $post = Post::create([
            'blog_id' => $blog->id,
            'type' => $type,
            'status' => $status,
            'heading' => $request->input('heading'),
            'body' => $request->input('body'),
            'caption' => $request->input('caption'),
            'media_path' => $mediaPath ?: $request->input('media_url'),
            'media_type' => $mediaType,
            'media_meta' => !empty($tagArray) ? ['tags' => $tagArray] : null,
            'quote_text' => $request->input('quote'),
            'quote_source' => $request->input('source'),
            'link_url' => $request->input('url'),
            'link_title' => $request->input('link_title') ?: $request->input('heading'),
            'link_domain' => $request->input('url') ? parse_url($request->input('url'), PHP_URL_HOST) : null,
            'chat_payload' => $request->input('chat') ? json_encode(array_filter(explode("\n", (string)$request->input('chat')))) : null,
            'published_at' => $publishedAt,
        ]);

        if ($status === 'queued') {
            $formattedTime = $scheduledAt ? $scheduledAt->format('M d, Y h:i A') : 'soon';
            return redirect()->route('dashboard.queue', ['blog_id' => $blog->id])
                ->with('success', "Post added to @{$blog->slug}'s queue for {$formattedTime}! (Slots used: " . ($blog->posts()->where('status', 'queued')->count()) . "/20)");
        }

        return redirect()->route('post.show', ['id' => $post->id]);
    }

    public function queue(Request $request)
    {
        $user = Auth::user();
        $allBlogs = $user->blogs;
        $selectedBlogId = $request->query('blog_id', $user->mainBlog?->id);
        $blog = $allBlogs->firstWhere('id', $selectedBlogId) ?? $user->mainBlog;

        if ($blog) {
            // Auto-publish any due queued posts whose scheduled time has arrived
            Post::where('blog_id', $blog->id)
                ->where('status', 'queued')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->update(['status' => 'published']);
        }

        $queuedPosts = $blog ? $blog->posts()->where('status', 'queued')->orderBy('published_at', 'asc')->get() : collect();

        return view('dashboard', [
            'section' => 'queue',
            'user' => $user,
            'blog' => $blog,
            'allBlogs' => $allBlogs,
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

    public function editPost(Request $request, $id)
    {
        $user = Auth::user();
        $post = Post::whereHas('blog', fn($q) => $q->where('user_id', $user->id))->findOrFail($id);
        $blogs = $user->blogs;
        $blog = $post->blog ?: $user->mainBlog;
        $settings = Setting::pluck('value', 'key')->all();

        return view('dashboard', [
            'section' => 'edit_post',
            'user' => $user,
            'post' => $post,
            'blog' => $blog,
            'blogs' => $blogs,
            'settings' => $settings,
        ]);
    }

    public function updatePost(Request $request, $id)
    {
        $user = Auth::user();
        $post = Post::whereHas('blog', fn($q) => $q->where('user_id', $user->id))->findOrFail($id);

        if ($request->filled('blog_id')) {
            $targetBlog = $user->blogs()->find($request->input('blog_id'));
            if ($targetBlog) {
                $post->blog_id = $targetBlog->id;
            }
        }

        $type = $request->input('type', $post->type);
        $post->type = $type;

        if ($request->has('heading')) {
            $post->heading = $request->input('heading');
        }
        if ($request->has('body')) {
            $post->body = $request->input('body');
        }
        if ($request->has('caption')) {
            $post->caption = $request->input('caption');
        }
        if ($request->has('quote')) {
            $post->quote_text = $request->input('quote');
        }
        if ($request->has('source')) {
            $post->quote_source = $request->input('source');
        }
        if ($request->has('url')) {
            $post->link_url = $request->input('url');
            $post->link_domain = $request->input('url') ? parse_url($request->input('url'), PHP_URL_HOST) : null;
        }
        if ($request->has('chat')) {
            $post->chat_payload = $request->input('chat') ? json_encode(array_filter(explode("\n", (string)$request->input('chat')))) : null;
        }

        // Handle file upload if a new media file was submitted
        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            if ($file->isValid()) {
                $extension = strtolower($file->getClientOriginalExtension());
                $imageExtensions = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
                $videoExtensions = ['mp4', 'mov', 'webm'];
                $isImage = in_array($extension, $imageExtensions);
                $isVideo = in_array($extension, $videoExtensions);

                if ($isImage || $isVideo) {
                    $maxImageMb = (float) Setting::get('max_image_mb', 2);
                    $maxVideoMb = (float) Setting::get('max_video_mb', 50);
                    $maxSizeKb = $isVideo ? ($maxVideoMb * 1024) : ($maxImageMb * 1024);

                    $request->validate([
                        'media_file' => 'file|max:' . (int)$maxSizeKb . '|mimes:jpeg,jpg,png,gif,webp,mp4,mov,webm',
                    ]);

                    $filename = 'post_' . $user->id . '_' . time() . '_' . Str::random(8) . '.' . $extension;
                    $path = $file->storeAs('uploads', $filename, 'public');
                    if ($path) {
                        $post->media_path = $path;
                        $post->media_type = $isVideo ? 'video' : 'photo';
                        $post->type = $post->media_type;
                    }
                }
            }
        } elseif ($request->filled('media_url')) {
            $post->media_path = $request->input('media_url');
        }

        // Handle tags
        if ($request->has('tags')) {
            $rawTags = $request->input('tags', '');
            $tagArray = [];
            if (!empty($rawTags)) {
                $tagArray = array_values(array_unique(array_filter(array_map(function($t) {
                    return strtolower(trim(ltrim((string)$t, '#')));
                }, explode(',', (string)$rawTags)))));
            }
            $meta = is_array($post->media_meta) ? $post->media_meta : [];
            $meta['tags'] = $tagArray;
            $post->media_meta = $meta;
        }

        // Handle action status
        $action = $request->input('post_action', 'update');
        if ($action === 'publish' && $post->status !== 'published') {
            $post->status = 'published';
            $post->published_at = now();
        } elseif ($action === 'draft') {
            $post->status = 'draft';
        }

        $post->save();

        return redirect()->route('dashboard.posts')->with('success', 'Post #' . $post->id . ' updated successfully!');
    }

    public function posts(Request $request)
    {
        $user = Auth::user();
        $allBlogs = $user->blogs;
        $selectedBlogId = $request->query('blog_id', $user->mainBlog?->id);
        $blog = $allBlogs->firstWhere('id', $selectedBlogId) ?? $user->mainBlog;
        $myPosts = $blog ? $blog->posts()->orderBy('is_pinned', 'desc')->orderBy('published_at', 'desc')->get() : collect();

        return view('dashboard', [
            'section' => 'posts',
            'user' => $user,
            'blog' => $blog,
            'allBlogs' => $allBlogs,
            'myPosts' => $myPosts,
        ]);
    }

    public function togglePinPost(Request $request, $id)
    {
        $user = Auth::user();
        $post = Post::whereHas('blog', fn($q) => $q->where('user_id', $user->id))->findOrFail($id);

        if ($post->is_pinned) {
            $post->is_pinned = false;
            $post->save();
            return redirect()->back()->with('success', 'Post unpinned from blog feed.');
        } else {
            $pinnedCount = Post::where('blog_id', $post->blog_id)->where('is_pinned', true)->count();
            if ($pinnedCount >= 3) {
                return redirect()->back()->with('error', 'Maximum limit of 3 pinned posts per blog feed reached. Unpin a post first to pin this one.');
            }
            $post->is_pinned = true;
            $post->save();
            return redirect()->back()->with('success', 'Post pinned to top of your blog feed!');
        }
    }

    public function blogs(Request $request)
    {
        $user = Auth::user();
        $allBlogs = $user->blogs;
        $selectedBlogId = $request->query('blog_id', $user->mainBlog?->id);
        $blog = $allBlogs->firstWhere('id', $selectedBlogId) ?? $user->mainBlog;

        return view('dashboard', [
            'section' => 'blogs',
            'user' => $user,
            'blog' => $blog,
            'allBlogs' => $allBlogs,
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

        $avatarPath = null;
        $coverPath = null;

        $maxImageMb = (float) Setting::get('max_image_mb', 5);
        $maxImageBytes = (int)($maxImageMb * 1024 * 1024);

        if ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');
            if ($avatarFile->getSize() > $maxImageBytes) {
                return redirect()->back()->with('error', "Avatar image size exceeds the {$maxImageMb}MB limit set in admin settings. Please upload an image under {$maxImageMb}MB.");
            }
            $avatarName = 'avatar_' . $user->id . '_' . time() . '_' . Str::random(4) . '.' . $avatarFile->getClientOriginalExtension();
            $avatarDest = public_path('assets/avatars');
            if (!file_exists($avatarDest)) {
                @mkdir($avatarDest, 0755, true);
            }
            $avatarFile->move($avatarDest, $avatarName);
            $avatarPath = 'avatars/' . $avatarName;

            $storageDest = storage_path('app/public/avatars');
            if (!file_exists($storageDest)) {
                @mkdir($storageDest, 0755, true);
            }
            @copy($avatarDest . '/' . $avatarName, $storageDest . '/' . $avatarName);
        } elseif ($request->filled('avatar_data') && str_starts_with($request->input('avatar_data'), 'data:image/')) {
            $rawAv = substr($request->input('avatar_data'), strpos($request->input('avatar_data'), ',') + 1);
            if (((strlen($rawAv) * 3) / 4) > $maxImageBytes) {
                return redirect()->back()->with('error', "Avatar image size exceeds the {$maxImageMb}MB limit set in admin settings.");
            }
            $avatarPath = $request->input('avatar_data');
        }

        // Handle Cover Upload on creation
        if ($request->hasFile('cover')) {
            $coverFile = $request->file('cover');
            if ($coverFile->getSize() > $maxImageBytes) {
                return redirect()->back()->with('error', "Cover banner image size exceeds the {$maxImageMb}MB limit set in admin settings. Please upload an image under {$maxImageMb}MB.");
            }
            $coverName = 'cover_' . $user->id . '_' . time() . '_' . Str::random(4) . '.' . $coverFile->getClientOriginalExtension();
            $coverDest = public_path('assets/covers');
            if (!file_exists($coverDest)) {
                @mkdir($coverDest, 0755, true);
            }
            $coverFile->move($coverDest, $coverName);
            $coverPath = 'covers/' . $coverName;

            $storageCoverDest = storage_path('app/public/covers');
            if (!file_exists($storageCoverDest)) {
                @mkdir($storageCoverDest, 0755, true);
            }
            @copy($coverDest . '/' . $coverName, $storageCoverDest . '/' . $coverName);
        } elseif ($request->filled('cover_data') && str_starts_with($request->input('cover_data'), 'data:image/')) {
            $rawCov = substr($request->input('cover_data'), strpos($request->input('cover_data'), ',') + 1);
            if (((strlen($rawCov) * 3) / 4) > $maxImageBytes) {
                return redirect()->back()->with('error', "Cover banner image size exceeds the {$maxImageMb}MB limit set in admin settings.");
            }
            $coverPath = $request->input('cover_data');
        }

        $newBlog = Blog::create([
            'user_id' => $user->id,
            'slug' => strtolower($request->input('slug')),
            'title' => $request->input('title'),
            'tagline' => $request->input('tagline'),
            'color' => $request->input('color', '#ff2bd6'),
            'avatar_path' => $avatarPath,
            'cover_path' => $coverPath,
            'is_main' => 0,
            'status' => 'active',
            'searchable' => 1,
            'show_profile' => 0,
        ]);

        return redirect()->route('dashboard.blogs', ['blog_id' => $newBlog->id])
            ->with('success', 'New blog @' . $newBlog->slug . ' created successfully! You can customize its appearance below.');
    }

    public function updateBlog(Request $request)
    {
        $user = Auth::user();
        $blogId = $request->input('blog_id', $user->mainBlog?->id);
        $blog = $user->blogs()->findOrFail($blogId);

        $maxImageMb = (float) Setting::get('max_image_mb', 5);
        $maxImageBytes = (int)($maxImageMb * 1024 * 1024);

        $data = $request->only(['title', 'tagline', 'bio', 'color', 'website1', 'website2', 'website3', 'contact_email']);

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');
            if ($avatarFile->getSize() > $maxImageBytes) {
                return redirect()->back()->with('error', "Avatar image size exceeds the {$maxImageMb}MB limit set in admin settings. Please upload an image under {$maxImageMb}MB.");
            }
            $avatarName = 'avatar_' . $blog->id . '_' . time() . '_' . Str::random(4) . '.' . $avatarFile->getClientOriginalExtension();
            
            // Store directly to public/assets/avatars
            $avatarDest = public_path('assets/avatars');
            if (!file_exists($avatarDest)) {
                @mkdir($avatarDest, 0755, true);
            }
            $avatarFile->move($avatarDest, $avatarName);
            $data['avatar_path'] = 'avatars/' . $avatarName;

            // Also mirror to storage/app/public/avatars for filesystem parity
            $storageDest = storage_path('app/public/avatars');
            if (!file_exists($storageDest)) {
                @mkdir($storageDest, 0755, true);
            }
            @copy($avatarDest . '/' . $avatarName, $storageDest . '/' . $avatarName);
        } elseif ($request->filled('avatar_data') && str_starts_with($request->input('avatar_data'), 'data:image/')) {
            $rawAv = substr($request->input('avatar_data'), strpos($request->input('avatar_data'), ',') + 1);
            if (((strlen($rawAv) * 3) / 4) > $maxImageBytes) {
                return redirect()->back()->with('error', "Avatar image size exceeds the {$maxImageMb}MB limit set in admin settings.");
            }
            $data['avatar_path'] = $request->input('avatar_data');
        }

        // Handle Cover Banner Upload
        if ($request->hasFile('cover')) {
            $coverFile = $request->file('cover');
            if ($coverFile->getSize() > $maxImageBytes) {
                return redirect()->back()->with('error', "Cover banner image size exceeds the {$maxImageMb}MB limit set in admin settings. Please upload an image under {$maxImageMb}MB.");
            }
            $coverName = 'cover_' . $blog->id . '_' . time() . '_' . Str::random(4) . '.' . $coverFile->getClientOriginalExtension();
            
            // Store directly to public/assets/covers
            $coverDest = public_path('assets/covers');
            if (!file_exists($coverDest)) {
                @mkdir($coverDest, 0755, true);
            }
            $coverFile->move($coverDest, $coverName);
            $data['cover_path'] = 'covers/' . $coverName;

            // Also mirror to storage/app/public/covers for filesystem parity
            $storageCoverDest = storage_path('app/public/covers');
            if (!file_exists($storageCoverDest)) {
                @mkdir($storageCoverDest, 0755, true);
            }
            @copy($coverDest . '/' . $coverName, $storageCoverDest . '/' . $coverName);
        } elseif ($request->filled('cover_data') && str_starts_with($request->input('cover_data'), 'data:image/')) {
            $rawCov = substr($request->input('cover_data'), strpos($request->input('cover_data'), ',') + 1);
            if (((strlen($rawCov) * 3) / 4) > $maxImageBytes) {
                return redirect()->back()->with('error', "Cover banner image size exceeds the {$maxImageMb}MB limit set in admin settings.");
            }
            $data['cover_path'] = $request->input('cover_data');
        } elseif ($request->boolean('remove_cover') || $request->input('cover_data') === 'CLEAR') {
            $data['cover_path'] = null;
        }

        $blog->update($data);

        return redirect()->route('dashboard.blogs', ['blog_id' => $blog->id])
            ->with('success', 'Blog @' . $blog->slug . ' appearance, avatar, and cover updated successfully!');
    }

    public function deleteBlog($id, Request $request)
    {
        $user = Auth::user();
        $blog = $user->blogs()->findOrFail($id);

        if ($blog->is_main) {
            return redirect()->back()->with('error', 'Cannot delete your primary account profile blog. You can only delete additional secondary blogs.');
        }

        if ($user->blogs()->count() <= 1) {
            return redirect()->back()->with('error', 'You must keep at least one active blog.');
        }

        $slug = $blog->slug;

        // Cascade delete blog posts and clean promotions
        try {
            \App\Models\Promotion::where('blog_id', $blog->id)->delete();
            $blog->posts()->delete();
            $blog->delete();
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Could not delete blog: ' . $e->getMessage());
        }

        $primaryBlog = $user->mainBlog ?: $user->blogs()->first();
        return redirect()->route('dashboard.blogs', ['blog_id' => $primaryBlog?->id])
            ->with('success', "Additional blog @{$slug} and all of its posts have been permanently deleted.");
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

    public function deletePost($id, Request $request)
    {
        $user = Auth::user();
        $post = Post::with('blog')->findOrFail($id);

        $isOwner = $post->blog && ($post->blog->user_id === $user->id);
        if (!$isOwner && !$user->is_admin) {
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'message' => 'You can only delete your own posts.'], 403);
            }
            return redirect()->back()->with('error', 'You can only delete your own posts.');
        }

        $post->delete();

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'message' => 'Post deleted successfully.']);
        }

        return redirect()->back()->with('success', 'Post has been deleted successfully.');
    }

    public function messages(Request $request)
    {
        $user = Auth::user();
        $tab = $request->query('tab', 'inbox');
        $settings = Setting::pluck('value', 'key')->all();

        $receivedMessages = $user->receivedMessages()->with('sender.mainBlog')->paginate(15);
        $sentMessages = $user->sentMessages()->with('recipient.mainBlog')->paginate(15);

        // Mark unread received messages as read when viewing inbox
        if ($tab === 'inbox') {
            $user->receivedMessages()->where('is_read', false)->update(['is_read' => true]);
        }

        return view('dashboard', [
            'section' => 'messages',
            'tab' => $tab,
            'user' => $user,
            'blog' => $user->mainBlog,
            'receivedMessages' => $receivedMessages,
            'sentMessages' => $sentMessages,
            'settings' => $settings,
        ]);
    }

    public function deleteMessage($id)
    {
        $user = Auth::user();
        $msg = Message::where(function($q) use ($user) {
            $q->where('recipient_id', $user->id)->orWhere('sender_id', $user->id);
        })->findOrFail($id);

        $msg->delete();
        return redirect()->back()->with('success', 'Private message deleted.');
    }

    public function notifications()
    {
        $user = Auth::user();
        $settings = Setting::pluck('value', 'key')->all();

        return view('dashboard', [
            'section' => 'notifications',
            'user' => $user,
            'blog' => $user->mainBlog,
            'settings' => $settings,
        ]);
    }

    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        $user->notify_new_follower = $request->has('notify_new_follower');
        $user->notify_private_message = $request->has('notify_private_message');
        $user->save();

        return redirect()->back()->with('success', 'Notification preferences saved successfully!');
    }
}

