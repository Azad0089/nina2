<div class="max-w-3xl mx-auto space-y-6">
  <div class="p-6 rounded-2xl bg-[#0f0716] border border-white/10">
    <h2 class="text-xl font-display font-black text-white mb-2">Your communities</h2>
    <p class="text-sm text-slate-400 mb-4">
      You can create
      {{ $communityLimit > 0 ? $communityLimit : 'unlimited' }}
      communities. Current: {{ isset($ownedCommunities) ? $ownedCommunities->count() : 0 }}.
    </p>
    @foreach(($ownedCommunities ?? []) as $community)
      <div class="p-4 mb-3 rounded-xl bg-white/5 border border-white/10">
        <a href="{{ route('member-community.show', $community->slug) }}" class="font-bold text-pink-400">{{ $community->title }}</a>
        <form method="post" action="{{ route('dashboard.communities.update', $community->id) }}" enctype="multipart/form-data" class="grid gap-2 mt-3">
          @csrf
          <input name="title" value="{{ $community->title }}" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
          <input name="slug" value="{{ $community->slug }}" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
          <input name="tagline" value="{{ $community->tagline }}" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
          <textarea name="bio" rows="3" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">{{ $community->bio }}</textarea>
          <input name="color" value="{{ $community->color }}" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
          <select name="join_mode" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
            @foreach(['open' => 'Open to everyone', 'approval' => 'Join with owner approval', 'invite' => 'Invite only'] as $mode => $label)
              <option value="{{ $mode }}" {{ $community->join_mode === $mode ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
          <input name="website1" value="{{ $community->website1 }}" placeholder="Link 1" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
          <input name="website2" value="{{ $community->website2 }}" placeholder="Link 2" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
          <input name="website3" value="{{ $community->website3 }}" placeholder="Link 3" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
          <input name="contact_email" value="{{ $community->contact_email }}" placeholder="Email" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
          <input type="file" name="avatar" accept="image/*">
          <input type="file" name="cover" accept="image/*">
          <button class="px-4 py-2 rounded-lg bg-pink-600 text-white text-sm font-bold w-fit">Save community</button>
        </form>
        <form method="post" action="{{ route('dashboard.communities.delete', $community->id) }}" class="mt-2">@csrf<button class="text-xs text-rose-300">Delete community</button></form>
      </div>
    @endforeach
  </div>

  <div class="p-6 rounded-2xl bg-[#0f0716] border border-white/10">
    <h3 class="font-bold text-white mb-3">New community</h3>
    <form method="post" action="{{ route('dashboard.communities.store') }}" enctype="multipart/form-data" class="grid gap-2">
      @csrf
      <input name="title" required placeholder="Title" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
      <input name="slug" required placeholder="url-name" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
      <input name="tagline" placeholder="Tagline" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
      <textarea name="bio" rows="4" placeholder="Bio" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white"></textarea>
      <input name="color" value="#ff2bd6" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
      <select name="join_mode" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
        <option value="open">Open to everyone</option>
        <option value="approval">Join with owner approval</option>
        <option value="invite">Invite only</option>
      </select>
      <input name="website1" placeholder="Link" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
      <input name="contact_email" placeholder="Email" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
      <input type="file" name="avatar" accept="image/*">
      <input type="file" name="cover" accept="image/*">
      <button class="px-4 py-2 rounded-lg bg-pink-600 text-white text-sm font-bold w-fit">Create community</button>
    </form>
  </div>
</div>
