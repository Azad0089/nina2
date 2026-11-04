@extends('layouts.app')

@section('title', ($community['title'] ?? 'Community Hub') . ' | ' . site_name())
@section('description', $community['tagline'] ?? 'Connect with creators, explore featured profiles and discover active blogs on ' . site_name())

@php
  $isMember = $isMember ?? ($stats['is_member'] ?? false);
@endphp

@section('content')
  @include('partials.header')

  <main class="page" id="main">
    <header class="blog-head" style="--c:{{ $community['color'] ?? '#ff2bd6' }};position:relative;">
      @if(!empty($community['cover']))
        <div class="blog-cover" style="background-image:url('{{ media_url($community['cover']) }}');background-size:cover;background-position:center;height:240px;position:relative;">
          @if(auth()->check() && auth()->user()->role === 'admin')
            <div style="position:absolute;top:16px;right:16px;display:flex;gap:8px;z-index:10;">
              <button type="button" onclick="openCommunityCoverModal()" class="btn btn-xs btn-line" style="background:rgba(7,3,13,0.88);backdrop-filter:blur(6px);border-color:var(--cyan);color:var(--cyan);font-weight:700;cursor:pointer;">
                {!! icon('camera') !!} <span>Change Community Cover</span>
              </button>
              <a href="{{ route('admin.settings') }}" class="btn btn-xs btn-ghost" style="background:rgba(7,3,13,0.88);backdrop-filter:blur(6px);border:1px solid var(--line);color:var(--mute);">
                <span>Settings</span>
              </a>
            </div>
          @endif
        </div>
      @else
        <div class="blog-cover ph" style="background:linear-gradient(135deg, rgba(255,43,214,0.35) 0%, rgba(25,227,255,0.25) 100%), #111;height:240px;position:relative;display:flex;align-items:center;justify-content:center;">
          @if(auth()->check() && auth()->user()->role === 'admin')
            <div style="position:absolute;top:16px;right:16px;display:flex;gap:8px;z-index:10;">
              <button type="button" onclick="openCommunityCoverModal()" class="btn btn-xs btn-line" style="background:rgba(7,3,13,0.88);backdrop-filter:blur(6px);border-color:var(--cyan);color:var(--cyan);font-weight:700;cursor:pointer;">
                {!! icon('camera') !!} <span>Upload Community Cover</span>
              </button>
              <a href="{{ route('admin.settings') }}" class="btn btn-xs btn-ghost" style="background:rgba(7,3,13,0.88);backdrop-filter:blur(6px);border:1px solid var(--line);color:var(--mute);">
                <span>Settings</span>
              </a>
            </div>
          @endif
        </div>
      @endif

      <div class="panel" style="margin-top:-46px;position:relative">
        <div style="display:flex;gap:16px;align-items:flex-end;flex-wrap:wrap">
          @if(!empty($community['avatar']))
            <img src="{{ media_url($community['avatar']) }}" alt="Community" style="width:88px;height:88px;object-fit:cover;border-radius:50%;border:4px solid var(--black);background:var(--paper);">
          @else
            <span class="av" style="--c:{{ $community['color'] ?? '#ff2bd6' }};width:88px;height:88px;font-size:34px;border:4px solid var(--black);border-radius:50%;">
              {!! icon('users') !!}
            </span>
          @endif

          <div style="flex:1">
            <h1 style="font:800 28px var(--f-display);display:flex;align-items:center;gap:10px;flex-wrap:wrap">
              {{ $community['title'] }}
              <span class="badge b-vip" title="Official Community">{!! icon('star') !!}</span>
            </h1>
            <p style="color:var(--mute)">
              {{ $community['tagline'] }} &middot; {{ $stats['total_creators'] ?? 0 }} members &middot; {{ $stats['total_blogs'] ?? 0 }} blogs &middot; {{ $stats['total_posts'] ?? 0 }} unique community posts
            </p>
          </div>

          <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            @if(auth()->check())
              <form method="POST" action="{{ route('community.join') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn {{ !empty($isMember) ? 'btn-line' : 'btn-fill' }}" style="{{ !empty($isMember) ? 'border-color:var(--cyan);color:var(--cyan);' : '' }}">
                  {!! !empty($isMember) ? icon('verified') . ' Member (Joined)' : icon('users') . ' Join Community' !!}
                </button>
              </form>
            @else
              <a class="btn btn-fill" href="{{ route('register') }}" data-modal="register">{!! icon('users') !!} Join Community</a>
            @endif
          </div>
        </div>

        <!-- Community Profile Box: Bio, 3 Links & Email -->
        <div class="blog-profile" style="margin-top:18px;padding-top:18px;border-top:1px solid var(--line)">
          @if(!empty($community['bio']))
            <p style="font-size:15px;line-height:1.6;color:var(--text);white-space:pre-line;">{{ $community['bio'] }}</p>
          @endif

          <p class="row-actions" style="margin-top:12px;display:flex;gap:10px;flex-wrap:wrap;">
            @if(!empty($community['website1']))
              <a class="btn-xs" href="{{ $community['website1'] }}" target="_blank" rel="nofollow noopener">{!! icon('link') !!} {{ preg_replace('/^https?:\/\//', '', $community['website1']) }}</a>
            @endif
            @if(!empty($community['website2']))
              <a class="btn-xs" href="{{ $community['website2'] }}" target="_blank" rel="nofollow noopener">{!! icon('link') !!} {{ preg_replace('/^https?:\/\//', '', $community['website2']) }}</a>
            @endif
            @if(!empty($community['website3']))
              <a class="btn-xs" href="{{ $community['website3'] }}" target="_blank" rel="nofollow noopener">{!! icon('link') !!} {{ preg_replace('/^https?:\/\//', '', $community['website3']) }}</a>
            @endif
            @if(!empty($community['email']))
              <a class="btn-xs" href="mailto:{{ $community['email'] }}">{!! icon('mail') !!} {{ $community['email'] }}</a>
            @endif
          </p>
        </div>
      </div>
    </header>

    @if(session('success'))
      <div style="max-width:1120px;margin:16px auto 0;padding:0 16px;">
        <div style="background:#10b98122;border:1px solid #10b981;color:#10b981;padding:12px 16px;border-radius:8px;font-size:14px;">
          {{ session('success') }}
        </div>
      </div>
    @endif

    <!-- Community Sections -->
    <div style="max-width:1120px;margin:28px auto 60px;padding:0 16px;">

      <!-- Post Publisher to Community Hub -->
      @if(auth()->check())
        <div style="background:var(--paper);border:1px solid var(--line);border-radius:12px;padding:24px;margin-bottom:36px;box-shadow:0 0 30px rgba(255,43,214,0.06);">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px;">
            <div style="display:flex;align-items:center;gap:10px;">
              <span class="av" style="--c:var(--pink);width:36px;height:36px;font-size:16px;">{{ initial(auth()->user()->username) }}</span>
              <div>
                <b style="font-size:15px;color:#fff;">Publish Directly to Community Hub</b>
                <span style="font-size:12px;color:var(--mute);display:block;">Post as &#64;{{ auth()->user()->username }} &middot; Unique to this page</span>
              </div>
            </div>
            <span style="font-size:11px;padding:4px 8px;border-radius:4px;background:rgba(25,227,255,0.1);color:var(--cyan);border:1px solid rgba(25,227,255,0.3);font-weight:700;">
              Community Exclusive &middot; Won't Duplicate in Feeds
            </span>
          </div>

          <form method="POST" action="{{ route('community.post.store') }}" enctype="multipart/form-data" id="communityComposer">
            @csrf
            <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;">
              <label style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:rgba(255,255,255,0.05);border:1px solid var(--line);border-radius:6px;font-size:12px;cursor:pointer;color:#fff;">
                <input type="radio" name="type" value="text" checked onchange="toggleCommunityFields('text')">
                <span>{!! icon('document') !!} Text Note</span>
              </label>
              <label style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:rgba(255,255,255,0.05);border:1px solid var(--line);border-radius:6px;font-size:12px;cursor:pointer;color:#fff;">
                <input type="radio" name="type" value="photo" onchange="toggleCommunityFields('photo')">
                <span>{!! icon('photo') !!} Photo</span>
              </label>
              <label style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:rgba(255,255,255,0.05);border:1px solid var(--line);border-radius:6px;font-size:12px;cursor:pointer;color:#fff;">
                <input type="radio" name="type" value="video" onchange="toggleCommunityFields('video')">
                <span>{!! icon('play') !!} Video</span>
              </label>
              <label style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:rgba(255,255,255,0.05);border:1px solid var(--line);border-radius:6px;font-size:12px;cursor:pointer;color:#fff;">
                <input type="radio" name="type" value="quote" onchange="toggleCommunityFields('quote')">
                <span>{!! icon('chat') !!} Quote</span>
              </label>
            </div>

            <div style="margin-bottom:12px;">
              <input type="text" name="heading" placeholder="Post title or topic (optional)" class="w-full" style="width:100%;padding:10px 14px;background:var(--ink);border:1px solid var(--line);border-radius:8px;color:#fff;font-size:14px;">
            </div>

            <div style="margin-bottom:12px;">
              <textarea name="body" id="communityBody" rows="3" data-caption-max="{{ (int) \App\Models\Setting::get('post_max_caption_length', 450) }}" placeholder="Share your update, art, or message with the community..." required style="width:100%;padding:10px 14px;background:var(--ink);border:1px solid var(--line);border-radius:8px;color:#fff;font-size:14px;line-height:1.5;"></textarea>
              <p id="communityCaptionHint" hidden style="margin:6px 0 0;font-size:12px;color:var(--mute);">Photo and video text is limited to {{ (int) \App\Models\Setting::get('post_max_caption_length', 450) }} characters (about 2–3 sentences).</p>
            </div>

            <div id="communityMediaUploadBox" style="display:none;margin-bottom:12px;padding:12px;background:rgba(255,255,255,0.02);border:1px dashed var(--line);border-radius:8px;">
              <label style="display:block;font-size:12px;font-weight:700;color:var(--mute);margin-bottom:6px;">Upload Media (Image or MP4 Video)</label>
              <input type="file" name="media_file" accept="image/*,video/mp4" style="font-size:12px;color:var(--text);">
            </div>

            <div id="communityQuoteBox" style="display:none;margin-bottom:12px;">
              <input type="text" name="quote_source" placeholder="Quote author or source" style="width:100%;padding:10px 14px;background:var(--ink);border:1px solid var(--line);border-radius:8px;color:#fff;font-size:14px;">
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
              <div style="flex:1;min-width:200px;">
                <input type="text" name="tags" placeholder="Tags (e.g. art, community, chill)" style="width:100%;padding:8px 12px;background:var(--ink);border:1px solid var(--line);border-radius:6px;color:#fff;font-size:12px;">
              </div>
              <button type="submit" class="btn btn-fill" style="padding:8px 20px;">
                {!! icon('plus') !!} Publish to Community
              </button>
            </div>
          </form>
        </div>

        <script>
          function toggleCommunityFields(type) {
            const mediaBox = document.getElementById('communityMediaUploadBox');
            const quoteBox = document.getElementById('communityQuoteBox');
            const body = document.getElementById('communityBody');
            const hint = document.getElementById('communityCaptionHint');
            if (mediaBox) mediaBox.style.display = (type === 'photo' || type === 'video') ? 'block' : 'none';
            if (quoteBox) quoteBox.style.display = (type === 'quote') ? 'block' : 'none';
            if (body) {
              const max = parseInt(body.getAttribute('data-caption-max') || '450', 10);
              if (type === 'photo' || type === 'video') {
                body.setAttribute('maxlength', String(max));
              } else {
                body.removeAttribute('maxlength');
              }
            }
            if (hint) hint.hidden = !(type === 'photo' || type === 'video');
          }
        </script>
      @endif

      <!-- Community Members Showcase (Joined Only) -->
      <section style="margin-bottom:40px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
          <div>
            <h2 style="font:800 20px var(--f-display);display:flex;align-items:center;gap:8px;margin:0 0 4px;">
              {!! icon('users') !!} Community Hub Members ({{ isset($communityMembersList) ? $communityMembersList->count() : 0 }})
            </h2>
            <p style="color:var(--mute);font-size:13px;margin:0;">Only creators who have explicitly chosen to join the Community Hub.</p>
          </div>
          @if(auth()->check() && empty($isMember))
            <form method="POST" action="{{ route('community.join') }}" style="margin:0;">
              @csrf
              <button type="submit" class="btn btn-xs btn-fill">+ Join to appear here</button>
            </form>
          @endif
        </div>

        @php
          $displayMembers = isset($communityMembersList) ? $communityMembersList : collect();
        @endphp

        @if($displayMembers->isEmpty())
          <div style="text-align:center;padding:36px 20px;background:var(--paper);border:1px dashed var(--line);border-radius:12px;">
            <p style="color:var(--mute);font-size:14px;margin:0 0 12px;">No members have joined the Community Hub yet.</p>
            @if(auth()->check())
              <form method="POST" action="{{ route('community.join') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-fill btn-sm">Be the First Member &mdash; Join Community Hub</button>
              </form>
            @endif
          </div>
        @else
          <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(240px, 1fr));gap:16px;">
            @foreach($displayMembers as $cBlog)
              <div style="background:var(--paper);border:1px solid var(--line);border-radius:10px;padding:16px;display:flex;flex-direction:column;gap:10px;position:relative;">
                <div style="display:flex;align-items:center;gap:12px;">
                  <a href="{{ blog_url($cBlog) }}" style="flex-shrink:0;text-decoration:none;">
                    @if($cBlog->avatar_path)
                      <img src="{{ media_url($cBlog->avatar_path) }}" alt="" width="48" height="48" style="border-radius:50%;object-fit:cover;display:block;">
                    @else
                      <span class="av" style="--c:{{ $cBlog->color ?? '#ff2bd6' }};width:48px;height:48px;font-size:20px;border-radius:50%;display:grid;place-items:center;">{{ initial($cBlog->slug) }}</span>
                    @endif
                  </a>
                  <div style="flex:1;min-width:0;">
                    <a href="{{ blog_url($cBlog) }}" style="font-weight:700;color:var(--text);text-decoration:none;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:15px;line-height:1.25;" title="{{ $cBlog->title }}">
                      {{ $cBlog->title }}
                    </a>
                    <span style="font-size:12px;color:var(--mute);display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px;">&#64;{{ $cBlog->slug }}</span>
                  </div>
                </div>

                @if($cBlog->user && badge($cBlog->user))
                  <div style="display:flex;align-items:center;gap:4px;flex-wrap:wrap;">
                    {!! badge($cBlog->user) !!}
                  </div>
                @endif

                @if($cBlog->bio)
                  <p style="font-size:13px;color:var(--mute);line-height:1.4;margin:0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                    {{ $cBlog->bio }}
                  </p>
                @endif

                <div style="margin-top:auto;padding-top:8px;border-top:1px solid var(--line);display:flex;justify-content:space-between;align-items:center;">
                  <span style="font-size:12px;color:var(--mute);">{{ $cBlog->posts()->count() }} posts</span>
                  <a class="btn-xs" href="{{ blog_url($cBlog) }}" style="padding:4px 10px;background:var(--line);color:var(--text);border-radius:4px;text-decoration:none;">Visit Blog</a>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </section>

      <!-- Unique Community Feed -->
      <section>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
          <h2 style="font:800 20px var(--f-display);display:flex;align-items:center;gap:8px;">
            {!! icon('flame') !!} Unique Community Posts (Shared Hub)
          </h2>
          <span style="color:var(--mute);font-size:13px;">{{ $communityPosts->count() }} unique {{ $communityPosts->count() === 1 ? 'post' : 'posts' }}</span>
        </div>

        @if($communityPosts->isEmpty())
          <div style="text-align:center;padding:50px 20px;background:var(--paper);border:1px dashed var(--line);border-radius:12px;">
            <div style="font-size:40px;margin-bottom:12px;">🤝</div>
            <h3 style="font:800 18px var(--f-display);color:#fff;margin-bottom:6px;">No posts published to Community Hub yet</h3>
            <p style="color:var(--mute);max-width:440px;margin:0 auto 16px;font-size:14px;line-height:1.5;">
              Posts published here stay strictly on this Community Page and won't duplicate to the explore feed.
            </p>
            @if(auth()->check())
              <p style="font-size:13px;color:var(--pink);font-weight:700;">Use the composer above to publish the very first community post!</p>
            @else
              <a class="btn btn-fill" href="{{ route('register') }}" data-modal="register">Join Community to Post</a>
            @endif
          </div>
        @else
          <div class="feed" id="feed" data-view="grid">
            @foreach($communityPosts as $post)
              @include('components.card', ['p' => $post])
            @endforeach
          </div>
          @if($communityPosts->hasPages())
              <div style="margin-top:30px;display:flex;justify-content:center;">
                  {{ $communityPosts->links() }}
              </div>
          @endif
        @endif
      </section>
    </div>

    @if(auth()->check() && auth()->user()->role === 'admin')
      <!-- Community Cover Modal -->
      <div id="communityCoverModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.85);backdrop-filter:blur(8px);z-index:9999;align-items:center;justify-content:center;padding:16px;">
        <div style="background:var(--paper);border:1px solid var(--line);border-radius:14px;max-width:540px;width:100%;padding:28px;position:relative;box-shadow:0 25px 60px rgba(0,0,0,0.7);">
          <button type="button" onclick="closeCommunityCoverModal()" aria-label="Close" style="position:absolute;top:18px;right:18px;background:none;border:none;color:var(--mute);font-size:24px;cursor:pointer;line-height:1;">&times;</button>
          <h3 style="font:800 20px var(--f-display);color:#fff;margin:0 0 6px;display:flex;align-items:center;gap:10px;">
            {!! icon('camera') !!} <span>Community Hub Cover Banner</span>
          </h3>
          <p style="color:var(--mute);font-size:13px;line-height:1.5;margin:0 0 20px;">
            Upload or customize the wide banner image displayed across the top of the Community Hub. Max file size: 5MB.
          </p>

          <form method="POST" action="{{ route('community.cover') }}" enctype="multipart/form-data" id="commCoverModalForm">
            @csrf

            <div style="margin-bottom:16px;">
              <label style="display:block;font-size:12px;font-weight:700;color:var(--text);margin-bottom:6px;">Upload Image File (Max 5MB)</label>
              <input type="file" name="community_cover_file" id="commBladeCoverFile" accept="image/*" style="width:100%;padding:10px;background:var(--ink);border:1px solid var(--line);border-radius:8px;color:#fff;font-size:12px;">
              <p id="commBladeCoverSizeErr" style="display:none;color:#f87171;font-size:11px;margin-top:6px;font-weight:700;">File exceeds 5MB limit. Please choose an image under 5MB.</p>
            </div>

            <div style="margin-bottom:16px;">
              <label style="display:block;font-size:12px;font-weight:700;color:var(--text);margin-bottom:6px;">Or Enter Image URL / Asset Path</label>
              <input type="text" id="commBladeCoverUrl" name="community_cover" value="{{ $community['cover'] ?? '' }}" placeholder="assets/hero.jpg or https://..." style="width:100%;padding:10px 14px;background:var(--ink);border:1px solid var(--line);border-radius:8px;color:#fff;font-size:13px;font-family:monospace;">
            </div>

            <!-- Live Preview Box -->
            <div style="margin-bottom:20px;">
              <label style="display:block;font-size:11px;font-weight:700;color:var(--mute);margin-bottom:6px;">Cover Banner Preview</label>
              <div style="height:140px;border-radius:8px;overflow:hidden;border:1px solid var(--line);background:#07030d;display:flex;align-items:center;justify-content:center;">
                <img id="commBladeCoverPreview" src="{{ media_url($community['cover'] ?? 'hero.jpg') }}" alt="Cover Preview" style="width:100%;height:100%;object-fit:cover;">
              </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;">
              <button type="button" onclick="closeCommunityCoverModal()" class="btn btn-line btn-sm">Cancel</button>
              <button type="submit" class="btn btn-fill btn-sm" style="background:var(--cyan);border-color:var(--cyan);color:#000;font-weight:800;">
                Save Cover Banner
              </button>
            </div>
          </form>
        </div>
      </div>

      <script>
        function openCommunityCoverModal() {
          const m = document.getElementById('communityCoverModal');
          if (m) m.style.display = 'flex';
        }
        function closeCommunityCoverModal() {
          const m = document.getElementById('communityCoverModal');
          if (m) m.style.display = 'none';
        }
        document.getElementById('commBladeCoverFile')?.addEventListener('change', function(e) {
          const file = e.target.files[0];
          const errEl = document.getElementById('commBladeCoverSizeErr');
          if (!file) return;
          if (file.size > 5 * 1024 * 1024) {
            if (errEl) errEl.style.display = 'block';
            this.value = '';
            return;
          }
          if (errEl) errEl.style.display = 'none';
          const reader = new FileReader();
          reader.onload = function(evt) {
            const dataUrl = evt.target.result;
            const preview = document.getElementById('commBladeCoverPreview');
            const urlInput = document.getElementById('commBladeCoverUrl');
            if (preview) preview.src = dataUrl;
            if (urlInput) urlInput.value = dataUrl;
          };
          reader.readAsDataURL(file);
        });
        document.getElementById('commBladeCoverUrl')?.addEventListener('input', function(e) {
          const preview = document.getElementById('commBladeCoverPreview');
          if (preview && e.target.value) preview.src = e.target.value;
        });
      </script>
    @endif
  </main>
@endsection
