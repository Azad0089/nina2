<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\MemberCommunity;
use App\Models\MemberCommunityUser;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MemberCommunityController extends Controller
{
    public function index()
    {
        ensure_platform_tables();
        $perPage = max(6, min(60, (int) Setting::get('member_communities_per_page', 12)));
        $communities = MemberCommunity::with('owner')
            ->withCount(['activeMembers', 'posts'])
            ->where('status', 'active')
            ->orderByDesc('id')
            ->paginate($perPage);

        return view('communities.index', [
            'communities' => $communities,
            'active_nav' => 'member-communities',
        ]);
    }

    public function show(Request $request, string $slug)
    {
        ensure_platform_tables();
        $community = MemberCommunity::with('owner')->where('slug', $slug)->where('status', 'active')->firstOrFail();
        $membership = null;
        if (Auth::check()) {
            $membership = MemberCommunityUser::where('community_id', $community->id)->where('user_id', Auth::id())->first();
        }
        $isAdmin = $membership && $membership->status === 'active' && in_array($membership->role, ['owner', 'admin'], true);
        $tag = strtolower(trim(ltrim((string) $request->query('tag', ''), '#')));

        $postsQuery = Post::with(['blog.user'])
            ->where('member_community_id', $community->id)
            ->where('status', 'published');
        if ($tag !== '') {
            $postsQuery->where(function ($q) use ($tag) {
                $q->where('media_meta', 'like', '%"' . $tag . '"%')
                    ->orWhere('heading', 'like', '%#' . $tag . '%')
                    ->orWhere('body', 'like', '%#' . $tag . '%')
                    ->orWhere('caption', 'like', '%#' . $tag . '%');
            });
        }
        $posts = $postsQuery
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->paginate(20)
            ->withQueryString();

        $tagCounts = [];
        Post::where('member_community_id', $community->id)->where('status', 'published')
            ->get(['id', 'heading', 'caption', 'body', 'media_meta'])
            ->each(function ($post) use (&$tagCounts) {
                foreach ($post->tags_list as $item) {
                    if ($item !== '') {
                        $tagCounts[$item] = ($tagCounts[$item] ?? 0) + 1;
                    }
                }
            });
        arsort($tagCounts);

        $pending = $isAdmin
            ? MemberCommunityUser::with('user')->where('community_id', $community->id)->where('status', 'pending')->get()
            : collect();

        return view('communities.show', [
            'community' => $community,
            'membership' => $membership,
            'isAdmin' => $isAdmin,
            'posts' => $posts,
            'tagCounts' => $tagCounts,
            'tag' => $tag,
            'pending' => $pending,
            'active_nav' => 'member-communities',
        ]);
    }

    public function join(string $slug)
    {
        ensure_platform_tables();
        $community = MemberCommunity::where('slug', $slug)->where('status', 'active')->firstOrFail();
        $user = Auth::user();
        if ((int) $community->owner_id === (int) $user->id) {
            return back()->with('success', 'You own this community.');
        }
        $existing = MemberCommunityUser::where('community_id', $community->id)->where('user_id', $user->id)->first();
        if ($existing && $existing->status === 'active') {
            return back()->with('success', 'You are already a member.');
        }
        if ($community->join_mode === 'invite' && (!$existing || $existing->status !== 'invited')) {
            return back()->with('error', 'This community is invite only.');
        }
        $status = $community->join_mode === 'approval' && (!$existing || $existing->status !== 'invited') ? 'pending' : 'active';
        MemberCommunityUser::updateOrCreate(
            ['community_id' => $community->id, 'user_id' => $user->id],
            ['role' => 'member', 'status' => $status, 'created_at' => now()]
        );
        return back()->with('success', $status === 'pending' ? 'Join request sent. The owner will approve it.' : 'You joined this community.');
    }

    public function leave(string $slug)
    {
        ensure_platform_tables();
        $community = MemberCommunity::where('slug', $slug)->firstOrFail();
        if ((int) $community->owner_id === (int) Auth::id()) {
            return back()->with('error', 'The owner cannot leave. Delete the community instead.');
        }
        MemberCommunityUser::where('community_id', $community->id)->where('user_id', Auth::id())->delete();
        return back()->with('success', 'You left the community. Your old posts stay.');
    }

    public function approve(string $slug, int $userId)
    {
        $community = $this->owned($slug);
        MemberCommunityUser::where('community_id', $community->id)->where('user_id', $userId)->update(['status' => 'active']);
        return back()->with('success', 'Member approved.');
    }

    public function invite(Request $request, string $slug)
    {
        $community = $this->owned($slug);
        $username = strtolower(ltrim(trim((string) $request->input('username')), '@'));
        $user = User::whereRaw('LOWER(username) = ?', [$username])->first();
        if (!$user) {
            return back()->with('error', 'No user with that name.');
        }
        $existing = MemberCommunityUser::where('community_id', $community->id)->where('user_id', $user->id)->first();
        if ($existing && $existing->status === 'active') {
            return back()->with('success', '@' . $user->username . ' is already a member.');
        }
        MemberCommunityUser::updateOrCreate(
            ['community_id' => $community->id, 'user_id' => $user->id],
            ['role' => 'member', 'status' => 'invited', 'created_at' => now()]
        );
        return back()->with('success', 'Invite sent to @' . $user->username . '.');
    }

    public function storePost(Request $request, string $slug)
    {
        ensure_platform_tables();
        $community = MemberCommunity::where('slug', $slug)->where('status', 'active')->firstOrFail();
        $membership = MemberCommunityUser::where('community_id', $community->id)->where('user_id', Auth::id())->where('status', 'active')->first();
        if (!$membership) {
            return back()->with('error', 'Join this community before posting.');
        }
        $blog = Auth::user()->mainBlog ?: Auth::user()->blogs()->first();
        if (!$blog) {
            return back()->with('error', 'You need a blog before posting.');
        }
        $body = (string) $request->input('body', '');
        $type = $request->input('type', 'text');
        if (trim(strip_tags($body)) === '' && !$request->hasFile('media_file') && !$request->filled('video_url')) {
            return back()->with('error', 'Write something or add media.');
        }
        $tags = $this->tagsFrom($request->input('tags'));
        $mediaPath = null;
        $mediaType = null;
        $embed = null;
        if ($request->filled('video_url')) {
            $embed = video_embed_from_url($request->input('video_url'));
            if (!$embed) {
                return back()->with('error', 'That video link could not be embedded.');
            }
            $type = 'video';
            $mediaPath = $embed['source_url'];
            $mediaType = $embed['kind'] === 'file' ? 'video' : 'embed';
        } elseif ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $isVideo = in_array($ext, ['mp4', 'webm', 'mov'], true);
            $type = $isVideo ? 'video' : 'photo';
            $mediaType = $type;
            $name = 'community_' . Auth::id() . '_' . time() . '_' . Str::random(6) . '.' . $ext;
            $file->storeAs('uploads', $name, 'public');
            $dir = public_path('uploads');
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            @copy($file->getRealPath(), $dir . '/' . $name);
            $mediaPath = 'uploads/' . $name;
        }
        Post::create([
            'blog_id' => $blog->id,
            'member_community_id' => $community->id,
            'type' => in_array($type, ['text', 'photo', 'video', 'quote', 'link', 'chat'], true) ? $type : 'text',
            'status' => 'published',
            'is_community' => 0,
            'heading' => $request->input('heading'),
            'body' => $body,
            'caption' => plain_post_text($body),
            'media_path' => $mediaPath,
            'media_type' => $mediaType,
            'media_meta' => array_filter([
                'tags' => $tags,
                'embed_url' => $embed['embed_url'] ?? null,
                'embed_kind' => $embed['kind'] ?? null,
            ]),
            'published_at' => now(),
        ]);
        return back()->with('success', 'Posted to ' . $community->title . '.');
    }

    public function pin(string $slug, int $postId)
    {
        $community = $this->owned($slug);
        $post = Post::where('member_community_id', $community->id)->where('id', $postId)->firstOrFail();
        if (!$post->is_pinned) {
            $pinned = Post::where('member_community_id', $community->id)->where('is_pinned', 1)->count();
            if ($pinned >= 3) {
                return back()->with('error', 'You can pin up to 3 posts in this community.');
            }
        }
        $post->is_pinned = !$post->is_pinned;
        $post->save();
        return back()->with('success', $post->is_pinned ? 'Post pinned.' : 'Post unpinned.');
    }

    public function manage()
    {
        ensure_platform_tables();
        $user = Auth::user();
        $communities = MemberCommunity::where('owner_id', $user->id)->orderByDesc('id')->get();
        $limit = (int) Setting::get('max_user_communities', 5);
        return view('dashboard', [
            'section' => 'communities',
            'user' => $user,
            'blog' => $user->mainBlog,
            'ownedCommunities' => $communities,
            'communityLimit' => $limit,
        ]);
    }

    public function store(Request $request)
    {
        ensure_platform_tables();
        $user = Auth::user();
        $limit = (int) Setting::get('max_user_communities', 5);
        $owned = MemberCommunity::where('owner_id', $user->id)->where('status', 'active')->count();
        if ($limit > 0 && $owned >= $limit) {
            return back()->with('error', 'You can create up to ' . $limit . ' communities.');
        }
        $data = $this->validated($request);
        $data['owner_id'] = $user->id;
        $data['status'] = 'active';
        $community = MemberCommunity::create($data);
        MemberCommunityUser::create([
            'community_id' => $community->id,
            'user_id' => $user->id,
            'role' => 'owner',
            'status' => 'active',
            'created_at' => now(),
        ]);
        return redirect()->route('member-community.show', $community->slug)->with('success', 'Community created.');
    }

    public function update(Request $request, int $id)
    {
        $community = MemberCommunity::where('owner_id', Auth::id())->findOrFail($id);
        $community->fill($this->validated($request, $community->id))->save();
        return back()->with('success', 'Community updated.');
    }

    public function destroy(int $id)
    {
        $community = MemberCommunity::where('owner_id', Auth::id())->findOrFail($id);
        $community->status = 'removed';
        $community->save();
        return back()->with('success', 'Community removed. Existing posts were kept.');
    }

    private function owned(string $slug): MemberCommunity
    {
        ensure_platform_tables();
        $community = MemberCommunity::where('slug', $slug)->where('status', 'active')->firstOrFail();
        $membership = MemberCommunityUser::where('community_id', $community->id)->where('user_id', Auth::id())->where('status', 'active')->first();
        if (!$membership || !in_array($membership->role, ['owner', 'admin'], true)) {
            abort(403);
        }
        return $community;
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $slug = Str::slug((string) $request->input('slug', $request->input('title')));
        $request->merge(['slug' => $slug]);
        $unique = 'unique:member_communities,slug' . ($ignoreId ? ',' . $ignoreId : '');
        $request->validate([
            'title' => 'required|string|max:160',
            'slug' => 'required|alpha_dash|min:3|max:80|' . $unique,
            'tagline' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:5000',
            'color' => 'nullable|string|max:16',
            'join_mode' => 'required|in:open,approval,invite',
            'website1' => 'nullable|string|max:255',
            'website2' => 'nullable|string|max:255',
            'website3' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:190',
        ]);
        $data = $request->only(['slug', 'title', 'tagline', 'bio', 'color', 'join_mode', 'website1', 'website2', 'website3', 'contact_email']);
        $data['color'] = $data['color'] ?: '#ff2bd6';
        foreach (['avatar' => 'avatar_path', 'cover' => 'cover_path'] as $file => $column) {
            if ($request->hasFile($file)) {
                $upload = $request->file($file);
                $name = $file . '_' . time() . '_' . Str::random(5) . '.' . $upload->getClientOriginalExtension();
                $upload->storeAs('uploads', $name, 'public');
                $dir = public_path('uploads');
                if (!is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }
                @copy($upload->getRealPath(), $dir . '/' . $name);
                $data[$column] = 'uploads/' . $name;
            }
        }
        return $data;
    }

    private function tagsFrom($raw): array
    {
        return array_values(array_unique(array_filter(array_map(function ($tag) {
            return strtolower(trim(ltrim((string) $tag, '#')));
        }, explode(',', (string) $raw)))));
    }
}
