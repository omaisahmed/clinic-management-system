@props(['user' => null, 'size' => 32, 'class' => ''])

@php
    $url = $user?->photo_url;
    $initials = $user?->initials ?? '?';
@endphp

@if ($url)
    <img src="{{ $url }}" alt="{{ $user?->name }}"
         style="width: {{ $size }}px; height: {{ $size }}px;"
         class="shrink-0 rounded-full object-cover {{ $class }}">
@else
    <span style="width: {{ $size }}px; height: {{ $size }}px; font-size: {{ (int) round($size * 0.4) }}px;"
          class="inline-flex shrink-0 items-center justify-center rounded-full bg-[var(--color-primary)] font-semibold text-white {{ $class }}">
        {{ $initials }}
    </span>
@endif
