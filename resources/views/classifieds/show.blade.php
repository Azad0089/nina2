@extends('layouts.app')

@section('title', $ad->title . ' | ' . t('classifieds.title'))
@section('description', \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', $ad->description), 180))

@section('content')
  @include('partials.header')
  <main class="page" id="main">
    <article class="panel" style="max-width:820px;margin:20px auto;">
      <p><a href="{{ route('classifieds.index') }}">{{ t('classifieds.title') }}</a></p>
      <h1>{{ $ad->title }}</h1>
      @if($ad->image_one || $ad->image_two)
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin:12px 0;">
          @if($ad->image_one)<img src="{{ media_url($ad->image_one) }}" alt="" style="max-width:100%;border-radius:8px;">@endif
          @if($ad->image_two)<img src="{{ media_url($ad->image_two) }}" alt="" style="max-width:100%;border-radius:8px;">@endif
        </div>
      @endif
      <div style="white-space:pre-wrap;line-height:1.7;">{{ $ad->description }}</div>
      <p style="margin-top:12px;">
        @foreach((array) $ad->tags as $item)
          <a class="chip" href="{{ route('classifieds.index', ['tag' => $item]) }}">#{{ $item }}</a>
        @endforeach
      </p>
      <ul style="list-style:none;padding:0;color:var(--mute);">
        @if($ad->price)<li>Price: {{ $ad->price }}</li>@endif
        @if($ad->location)<li>Location: {{ $ad->location }}</li>@endif
        @if($ad->contact_email)<li>Email: {{ $ad->contact_email }}</li>@endif
        @if($ad->phone)<li>Phone: {{ $ad->phone }}</li>@endif
      </ul>

      @if(session('success'))<p class="form-msg ok">{{ session('success') }}</p>@endif
      @if(session('error'))<p class="form-msg error">{{ session('error') }}</p>@endif

      @auth
        <form method="post" action="{{ route('classifieds.message', $ad->id) }}" style="margin-top:18px;">
          @csrf
          <label>Private message
            <textarea name="body" rows="4" required style="width:100%;padding:10px;background:var(--ink);border:1px solid var(--line);color:#fff;"></textarea>
          </label>
          <button class="btn btn-fill" type="submit">{{ t('classifieds.contact') }}</button>
        </form>
        <form method="post" action="{{ route('classifieds.report', $ad->id) }}" style="margin-top:12px;">
          @csrf
          <input type="hidden" name="reason" value="Classified ad">
          <button class="btn btn-line" type="submit">{{ t('classifieds.report') }}</button>
        </form>
      @else
        <p style="margin-top:18px;"><a class="btn btn-fill" href="{{ route('login') }}">{{ t('classifieds.login_to_message') }}</a></p>
      @endauth
    </article>
  </main>
  @include('partials.footer')
@endsection
