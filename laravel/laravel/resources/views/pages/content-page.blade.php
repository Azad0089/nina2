@extends('layouts.app')

@section('title', $title . ' | Nina.Sucks')

@section('content')
  @include('partials.header')

  <main class="page" id="main">
    @include('partials.page-hero', [
      'hero' => [
        'title' => $title,
        'lead' => $lead ?? 'Official policies, guidelines, and contact forms.',
        'color' => $color ?? '#19e3ff'
      ]
    ])

    <div class="panel" style="max-width:800px;margin:20px auto;line-height:1.7;">
      @if(session('success'))
        <div class="app-flash" style="margin-bottom:20px;">{{ session('success') }}</div>
      @endif

      {!! $bodyHtml ?? '' !!}

      @if(!empty($isContact))
        <form method="post" action="{{ route('action.contact') }}" style="margin-top:24px;display:flex;flex-direction:column;gap:14px;">
          @csrf
          <label class="field"><span>Your Name</span><input type="text" name="name" required></label>
          <label class="field"><span>Your Email</span><input type="email" name="email" required></label>
          <label class="field"><span>Topic</span>
            <select name="topic" style="width:100%;padding:10px;background:var(--ink);color:var(--text);border:1px solid var(--line);">
              <option value="general">General Support</option>
              <option value="report">Report Content / DMCA</option>
              <option value="verification">ID Verification Request</option>
              <option value="advertising">Advertising Query</option>
            </select>
          </label>
          <label class="field"><span>Message</span><textarea name="message" rows="5" required style="width:100%;padding:10px;background:var(--ink);color:var(--text);border:1px solid var(--line);"></textarea></label>
          <button type="submit" class="btn btn-fill" style="align-self:flex-start">Send Message</button>
        </form>
      @endif
    </div>
  </main>
@endsection
