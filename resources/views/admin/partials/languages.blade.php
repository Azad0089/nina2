@php
  $editor = $localeEditor ?? ['code' => '', 'pack' => ['name' => '', 'enabled' => 0, 'strings' => []], 'catalog' => [], 'packs' => [], 'default' => 'en'];
  $lines = [];
  foreach (($editor['catalog'] ?? []) as $key => $english) {
      $translated = trim((string) ($editor['pack']['strings'][$key] ?? ''));
      $lines[] = $key . '=' . ($translated !== '' ? $translated : $english);
  }
@endphp
<div class="bg-[#0f0716] border border-white/10 rounded-2xl p-6 shadow-xl space-y-5">
  <div>
    <h2 class="font-display font-bold text-xl text-white">Languages</h2>
    <p class="text-xs text-slate-400 mt-1">English is the original text. Add another language, translate the lines, turn it on, and you can make it the sitewide default. Switch the default back to English whenever you want. A language menu appears on the site only after another language is turned on.</p>
  </div>
  <div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.languages') }}" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-white/5 text-slate-300">New language</a>
    @foreach(($editor['packs'] ?? []) as $code => $pack)
      <a href="{{ route('admin.languages', ['lang' => $code]) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold {{ ($editor['code'] ?? '') === $code ? 'bg-pink-600 text-white' : 'bg-white/5 text-slate-300' }}">{{ $pack['name'] ?? $code }} ({{ $code }}){{ !empty($pack['enabled']) ? '' : ' · off' }}</a>
    @endforeach
  </div>
  <form method="post" action="{{ route('admin.languages.save') }}" class="space-y-3">
    @csrf
    <div class="grid sm:grid-cols-3 gap-3">
      <label class="text-xs text-slate-300">Code
        <input name="code" value="{{ $editor['code'] ?? '' }}" placeholder="es" class="mt-1 w-full bg-[#170a22] border border-white/10 rounded-xl px-3 py-2 text-sm text-white">
      </label>
      <label class="text-xs text-slate-300">Name
        <input name="name" value="{{ $editor['pack']['name'] ?? '' }}" placeholder="Spanish" class="mt-1 w-full bg-[#170a22] border border-white/10 rounded-xl px-3 py-2 text-sm text-white">
      </label>
      <label class="text-xs text-slate-300">Sitewide default
        <select name="default_locale" class="mt-1 w-full bg-[#170a22] border border-white/10 rounded-xl px-3 py-2 text-sm text-white">
          <option value="en" {{ ($editor['default'] ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
          @foreach(($editor['packs'] ?? []) as $code => $pack)
            @if(!empty($pack['enabled']) || $code === ($editor['code'] ?? ''))
              <option value="{{ $code }}" {{ ($editor['default'] ?? '') === $code ? 'selected' : '' }}>{{ $pack['name'] ?? $code }}</option>
            @endif
          @endforeach
        </select>
      </label>
    </div>
    <label class="flex items-center gap-2 text-sm text-slate-200">
      <input type="checkbox" name="enabled" value="1" {{ !empty($editor['pack']['enabled']) ? 'checked' : '' }}>
      Enabled. Visitors can choose it, and it can be the default language.
    </label>
    <label class="block text-xs text-slate-300">Translations, one <span class="font-mono">key=text</span> per line. Empty values keep the English original.
      <textarea name="strings" rows="16" class="mt-1 w-full bg-[#170a22] border border-white/10 rounded-xl px-3 py-2 text-xs text-white font-mono">{{ implode("\n", $lines) }}</textarea>
    </label>
    <div class="flex gap-3">
      <button class="px-4 py-2 rounded-xl bg-pink-600 text-white text-sm font-bold" type="submit">Save language</button>
    </div>
  </form>
  @if(!empty($editor['code']) && $editor['code'] !== 'en')
    <form method="post" action="{{ route('admin.languages.save') }}">
      @csrf
      <input type="hidden" name="code" value="{{ $editor['code'] }}">
      <input type="hidden" name="delete" value="1">
      <button class="text-xs text-rose-300" type="submit">Remove this language</button>
    </form>
  @endif
</div>
