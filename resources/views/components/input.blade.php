@props([
    'name' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'error' => null,
])

@php
    $hasError = $error !== null || ($name !== null && $errors->has($name));
    $errorMessage = $error ?? ($name !== null ? $errors->first($name) : null);
@endphp

<div>
    <input
        type="{{ $type }}"
        @if ($name) name="{{ $name }}" @endif
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'class' => 'input ' . ($hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''),
        ]) }}
    />

    @if ($hasError && $errorMessage)
        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $errorMessage }}</p>
    @endif
</div>
