@props(['type' => 'info', 'dismissible' => false, 'title' => null])

@php
    $styles = match ($type) {
        'success' => ['bg-green-50 text-green-800 border-green-200 dark:bg-green-950 dark:text-green-200 dark:border-green-800', 'check-circle'],
        'error' => ['bg-red-50 text-red-800 border-red-200 dark:bg-red-950 dark:text-red-200 dark:border-red-800', 'x-circle'],
        'warning' => ['bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950 dark:text-amber-200 dark:border-amber-800', 'alert-triangle'],
        default => ['bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950 dark:text-blue-200 dark:border-blue-800', 'information-circle'],
    };
@endphp

<div x-data="{ show: true }" x-show="show"
     {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-lg border px-4 py-3 ' . $styles[0]]) }}>
    <x-icon :name="$styles[1]" class="mt-0.5 h-5 w-5 shrink-0" />
    <div class="flex-1 text-sm">
        @if ($title)
            <p class="font-semibold">{{ $title }}</p>
        @endif
        {{ $slot }}
    </div>
    @if ($dismissible)
        <button type="button" x-on:click="show = false" class="shrink-0 opacity-60 hover:opacity-100">
            <x-icon name="x-mark" class="h-4 w-4" />
        </button>
    @endif
</div>
