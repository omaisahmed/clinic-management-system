@props(['message' => 'No records found.', 'icon' => 'clipboard-list', 'actionLabel' => null, 'actionHref' => null])

<div class="flex flex-col items-center justify-center px-6 py-12 text-center">
    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700">
        <x-icon :name="$icon" class="h-6 w-6 text-slate-400 dark:text-slate-400" />
    </div>
    <p class="mt-4 text-sm font-medium text-slate-600 dark:text-slate-300">{{ $message }}</p>

    @if ($actionLabel && $actionHref)
        <x-button :href="$actionHref" variant="primary" size="sm" class="mt-4" icon="plus">{{ $actionLabel }}</x-button>
    @endif

    {{ $slot }}
</div>
