<form method="get" action="{{ $action }}" class="flex flex-col sm:flex-row gap-2 mb-4">
  @if(request('filter'))
    <input type="hidden" name="filter" value="{{ request('filter') }}">
  @endif
  <input type="search" name="q" value="{{ request('q') }}" placeholder="Search this list" class="flex-1 bg-[#170a22] border border-white/10 rounded-xl px-3 py-2 text-sm text-white">
  <button class="px-4 py-2 rounded-xl bg-pink-600 text-white text-sm font-bold" type="submit">Search</button>
</form>
@if(isset($list) && is_object($list) && method_exists($list, 'links'))
  <div class="mb-4 text-sm text-slate-300">{{ $list->links() }}</div>
@endif
