@props(['label', 'value', 'icon', 'trend' => null, 'trendUp' => true, 'tone' => 'primary'])

@php
    $iconTones = [
        'primary' => 'bg-[var(--color-primary-100)] text-[var(--color-primary-600)]',
        'green' => 'bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300',
        'red' => 'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-300',
        'amber' => 'bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-300',
        'blue' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300',
        'purple' => 'bg-purple-100 text-purple-600 dark:bg-purple-900/40 dark:text-purple-300',
    ];
@endphp

<div class="card p-5 transition hover:shadow-md">
    <div class="flex items-start justify-between">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $iconTones[$tone] ?? $iconTones['primary'] }}">
            <x-icon :name="$icon" class="h-5 w-5" />
        </div>

        @if ($trend !== null)
            <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $trendUp ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                <x-icon :name="$trendUp ? 'arrow-right' : 'arrow-left'" class="h-3.5 w-3.5 -rotate-45" />
                {{ $trend }}
            </span>
        @endif
    </div>

    <p class="mt-3 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $value }}</p>
    <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">{{ $label }}</p>
</div>
