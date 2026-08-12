@props(['title' => null, 'subtitle' => null, 'actions' => null, 'padding' => true])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if (isset($header) && ! empty($header))
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-700">
            <div>
                @if (isset($title))
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $title }}</h3>
                @endif
                @if (isset($subtitle))
                    <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="flex shrink-0 items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div @if ($padding) class="p-5" @endif>
        {{ $slot }}
    </div>
</div>
