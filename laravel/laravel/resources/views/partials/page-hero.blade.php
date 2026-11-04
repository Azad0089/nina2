@php
  $heroColor = $hero['color'] ?? '#ff2bd6';
@endphp
<section class="page-hero" style="--c:{{ $heroColor }}">
  <nav class="crumbs" aria-label="Breadcrumb">
    <a href="{{ url('/') }}">Home</a>
    <span aria-hidden="true">/</span>
    <span>{{ $hero['title'] ?? 'Nina.Sucks' }}</span>
  </nav>
  <h1>{{ $hero['title'] ?? '' }}</h1>
  @if(!empty($hero['lead']))
    <p>{{ $hero['lead'] }}</p>
  @endif
</section>
