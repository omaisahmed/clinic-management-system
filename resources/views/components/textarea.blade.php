@props([
    'name' => null,
    'rows' => 3,
    'placeholder' => null,
])

@php
    $hasError = $name !== null && $errors->has($name);
@endphp

<div>
    <textarea
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if ($name) name="{{ $name }}" @endif
        {{ $attributes->merge([
            'class' => 'input ' . ($hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''),
        ]) }}
    >{{ old($name, $slot) }}</textarea>

    @if ($name !== null && $hasError)
        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $errors->first($name) }}</p>
    @endif
</div>
