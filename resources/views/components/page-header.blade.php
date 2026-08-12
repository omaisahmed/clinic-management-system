@props(['title' => '', 'subtitle' => null, 'icon' => null, 'actions' => null])

<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        @if ($icon)
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[var(--color-primary-100)] text-[var(--color-primary-600)]">
                <x-icon :name="$icon" class="h-5 w-5" />
            </span>
        @endif
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
            @endif
        </div>
    </div>

    @isset($actions)
        <div class="flex items-center gap-2">
            @if (is_array($actions))
                @foreach ($actions as $action)
                    <x-button :href="$action['href'] ?? null" variant="secondary" :icon="$action['icon'] ?? null">
                        {{ $action['label'] ?? '' }}
                    </x-button>
                @endforeach
            @else
                {{ $actions }}
            @endif
        </div>
    @endisset
</div>
