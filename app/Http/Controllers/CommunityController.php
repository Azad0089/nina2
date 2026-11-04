<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Blog;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;

class CommunityController extends Controller
{
    private function ensureCommunityTable()
    {
        try {
            if (!Schema::hasTable('community_members')) {
                Schema::create('community_members', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->constrained()->onDelete('cascade');
                    $table->timestamp('created_at')->useCurrent();
                    $table->unique('user_id');
                });
            }
        } catch (\Throwable $e) {
            // Safe fallback if table already exists or schema permissions differ
        }
    }

    public function index(Request $request)
    {
        $this->ensureCommunityTable();
        $user = Auth::user();
        $isMember = false;

        $memberUserIds = [];
        try {
            $memberUserIds = DB::table('community_members')->pluck('user_id')->toArray();
        } catch (\Throwable $e) {}

        if ($user) {
            $isMember = in_array($user->id, $memberUserIds);
        }

        $community = [
            'title' => Setting::get('community_title', 'Nina.Sucks Community'),
            'tagline' => Setting::get('community_tagline', 'The uncensored hub for creator discovery & collaborative expression.'),
            'bio' => Setting::get('community_bio', "Welcome to the Nina.Sucks official community!\nHere creators, writers, curators, and free thinkers join together, publish community posts, and connect without algorithm walls.\nJoin our community and share your thoughts directly to this shared space."),
            'avatar' => Setting::get('community_avatar', ''),
            'cover' => Setting::get('community_cover', ''),
            'website1' => Setting::get('community_url1', 'https://nina.sucks'),
            'website2' => Setting::get('community_url2', 'https://t.me/ninasucks'),
            'website3' => Setting::get('community_url3', 'https://twitter.com/ninasucks'),
            'email' => Setting::get('community_email', 'community@nina.sucks'),
            'color' => Setting::get('primary_color', '#ff2bd6'),
        ];

        // List community member blogs
        $communityMembersList = Blog::with('user')
            ->where('status', 'active')
            ->whereIn('user_id', $memberUserIds)
            ->orderBy('id', 'desc')
            ->take(30)
            ->get();

        // Unique posts published to the community hub
        $communityPosts = Post::with(['blog.user'])
            ->where('status', 'published')
            ->where(function ($q) {
                $q->where('is_community', 1)
                ->orWhere('media_meta->is_community', true);
            })
            ->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(30, ['*'], 'posts_page')
            ->withQueryString();

        $memberCount = count($memberUserIds);

        $stats = [
            'total_creators' => $memberCount,
            'total_blogs' => $communityMembersList->count(),
            'total_posts' => $communityPosts->count(),
            'is_member' => $isMember,
        ];

        return view('community', compact('community', 'communityMembersList', 'communityPosts', 'stats', 'isMember'));
    }

    public function join(Request $request)
    {
        $this->ensureCommunityTable();

        if (!Auth::check()) {
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'redirect' => route('login'), 'message' => 'Please log in to join.']);
            }
            return redirect()->route('login')->with('error', 'Please log in to join the community.');
        }

        $user = Auth::user();

        try {
            $exists = DB::table('community_members')->where('user_id', $user->id)->exists();
            if ($exists) {
                DB::table('community_members')->where('user_id', $user->id)->delete();
                $msg = 'You have left the Community Hub.';
                $joined = false;
            } else {
                DB::table('community_members')->insert([
                    'user_id' => $user->id,
                    'created_at' => now(),
                ]);
                $msg = 'Welcome to the Community! You can now publish posts directly to the Community Page.';
                $joined = true;
            }
        } catch (\Throwable $e) {
            $joined = true;
            $msg = 'You are now an active member of the Community!';
        }

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'joined' => $joined, 'message' => $msg]);
        }

        return redirect()->back()->with('success', $msg);
    }

    public function storePost(Request $request)
    {
        $this->ensureCommunityTable();

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to post in the community.');
        }

        $user = Auth::user();
        $blog = $user->mainBlog ?: $user->blogs()->first();

        if (!$blog) {
            return redirect()->back()->with('error', 'You must have an active blog to publish community posts.');
        }

        $type = $request->input('type', 'text');
        $mediaPath = null;
        $mediaType = null;

        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $isVideo = in_array($ext, ['mp4', 'webm', 'mov']);
            $type = $isVideo ? 'video' : 'photo';
            $mediaType = $type;

            $filename = 'community_' . $user->id . '_' . time() . '_' . \Illuminate\Support\Str::random(6) . '.' . $ext;
            $file->storeAs('uploads', $filename, 'public');

            // Copy to public/uploads
            $pubDir = public_path('uploads');
            if (!file_exists($pubDir)) @mkdir($pubDir, 0755, true);
            @copy($file->getRealPath(), $pubDir . '/' . $filename);

            $mediaPath = 'uploads/' . $filename;
        }

        $mediaMeta = [
            'is_community' => true,
            'community_posted_at' => now()->toIso8601String(),
            'tags' => array_values(array_filter(array_map('trim', explode(',', (string)$request->input('tags', ''))))),
        ];

        Post::create([
            'blog_id' => $blog->id,
            'type' => $type,
            'status' => 'published',
            'is_community' => 1,
            'heading' => $request->input('heading'),
            'body' => $request->input('body'),
            'caption' => $request->input('caption'),
            'quote_text' => $request->input('quote_text'),
            'quote_source' => $request->input('quote_source'),
            'link_url' => $request->input('link_url'),
            'link_title' => $request->input('link_title'),
            'media_path' => $mediaPath,
            'media_type' => $mediaType,
            'media_meta' => $mediaMeta,
            'published_at' => now(),
        ]);

        // Auto-join community if not joined
        try {
            DB::table('community_members')->insertOrIgnore([
                'user_id' => $user->id,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {}

        return redirect()->route('community')->with('success', 'Your unique community post was published successfully!');
    }
}
