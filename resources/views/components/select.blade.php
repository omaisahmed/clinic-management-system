@props([
    'name' => null,
    'value' => null,
    'options' => [],
    'placeholder' => null,
])

@php
    $hasError = $name !== null && $errors->has($name);
@endphp

<select
    @if ($name) name="{{ $name }}" @endif
    {{ $attributes->merge([
        'class' => 'input ' . ($hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''),
    ]) }}
>
    @if ($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif

    @foreach ($options as $optionValue => $optionLabel)
        <option value="{{ $optionValue }}" @selected($value == $optionValue)>
            {{ $optionLabel }}
        </option>
    @endforeach

    {{ $slot }}
</select>

@if ($name !== null && $hasError)
    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $errors->first($name) }}</p>
@endif
