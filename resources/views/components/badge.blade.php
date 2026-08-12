@props(['variant' => 'gray', 'icon' => null])

@php
    $classes = match ($variant) {
        'green' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
        'red' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
        'amber' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
        'blue' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
        'purple' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
        'teal' => 'bg-teal-100 text-teal-800 dark:bg-teal-900/40 dark:text-teal-300',
        'primary' => 'bg-[var(--color-primary-100)] text-[var(--color-primary-700)]',
        default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium ' . $classes]) }}>
    @if ($icon)
        <x-icon :name="$icon" class="h-3 w-3" />
    @endif
    {{ $slot }}
</span>
