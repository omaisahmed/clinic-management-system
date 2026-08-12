@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'size' => null,
    'icon' => null,
    'disabled' => false,
])

@php
    $classes = match ($variant) {
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'ghost' => 'btn-ghost',
        'danger' => 'btn-danger',
        default => 'btn-primary',
    };

    if ($size === 'sm') {
        $classes .= ' btn-sm';
    } elseif ($size === 'lg') {
        $classes .= ' btn-lg';
    }
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <x-icon :name="$icon" class="h-4 w-4" />
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} @disabled($disabled)>
        @if ($icon)
            <x-icon :name="$icon" class="h-4 w-4" />
        @endif
        {{ $slot }}
    </button>
@endif
