@php $limits = $classifiedLimits ?? ['title_min'=>10,'title_max'=>80,'body_min'=>30,'body_max'=>5000,'tags_min'=>2,'tags_max'=>6]; @endphp
<div class="max-w-3xl mx-auto space-y-6">
  <div class="p-6 rounded-2xl bg-[#0f0716] border border-white/10">
    <h2 class="text-xl font-display font-black text-white mb-2">Sex classifieds</h2>
    <p class="text-sm text-slate-400">Ads are anonymous on the public page. The other person only learns who you are if they get your private message. Title {{ $limits['title_min'] }}–{{ $limits['title_max'] }} characters. Description {{ $limits['body_min'] }}–{{ $limits['body_max'] }} characters. Tags {{ $limits['tags_min'] }}–{{ $limits['tags_max'] }}.</p>
  </div>

  @foreach(($myAds ?? []) as $ad)
    <div class="p-6 rounded-2xl bg-[#0f0716] border border-white/10">
      <a href="{{ route('classifieds.show', $ad->id) }}" class="text-pink-400 font-bold">{{ $ad->title }}</a>
      <span class="text-xs text-slate-400">{{ $ad->status }} @if($ad->expires_at) · expires {{ $ad->expires_at->format('M j, Y') }}@endif</span>
      <form method="post" action="{{ route('dashboard.classifieds.update', $ad->id) }}" enctype="multipart/form-data" class="grid gap-2 mt-3">
        @csrf
        <input name="title" value="{{ $ad->title }}" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
        <textarea name="description" rows="5" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">{{ $ad->description }}</textarea>
        <input name="tags" value="{{ implode(', ', (array) $ad->tags) }}" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
        <input name="price" value="{{ $ad->price }}" placeholder="Price (optional)" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
        <input name="location" value="{{ $ad->location }}" placeholder="Location (optional)" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
        <input name="contact_email" value="{{ $ad->contact_email }}" placeholder="Email (optional)" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
        <input name="phone" value="{{ $ad->phone }}" placeholder="Phone (optional)" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
        <input type="file" name="image_one" accept="image/*">
        <input type="file" name="image_two" accept="image/*">
        <button class="px-4 py-2 rounded-lg bg-pink-600 text-white text-sm font-bold w-fit">Save ad</button>
      </form>
      <form method="post" action="{{ route('dashboard.classifieds.delete', $ad->id) }}" class="mt-2">@csrf<button class="text-xs text-rose-300">Delete ad</button></form>
    </div>
  @endforeach

  <div class="p-6 rounded-2xl bg-[#0f0716] border border-white/10">
    <h3 class="font-bold text-white mb-3">New classified ad</h3>
    <form method="post" action="{{ route('dashboard.classifieds.store') }}" enctype="multipart/form-data" class="grid gap-2">
      @csrf
      <input name="title" required placeholder="Title" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
      <textarea name="description" required rows="6" placeholder="Full description" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white"></textarea>
      <input name="tags" required placeholder="tag one, tag two" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
      <input name="price" placeholder="Price (optional)" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
      <input name="location" placeholder="Location (optional)" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
      <input name="contact_email" placeholder="Contact email (optional)" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
      <input name="phone" placeholder="Phone (optional)" class="bg-[#170a22] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
      <label class="text-xs text-slate-400">Image 1 (optional)<input type="file" name="image_one" accept="image/*"></label>
      <label class="text-xs text-slate-400">Image 2 (optional)<input type="file" name="image_two" accept="image/*"></label>
      <button class="px-4 py-2 rounded-lg bg-pink-600 text-white text-sm font-bold w-fit">Publish ad</button>
    </form>
  </div>
</div>
