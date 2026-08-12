@props(['title' => null, 'maxWidth' => 'lg', 'closeable' => true, 'name' => null, 'show' => false, 'focusable' => false, 'trigger' => null])

@php
    $maxWidthClass = match ($maxWidth) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        default => 'max-w-lg',
    };
@endphp

<div x-data="{ open: @js((bool) $show) }"
     x-on:keydown.escape.window="open = false"
     @if ($name)
         x-on:open-modal.window="$event.detail == '{{ $name }}' ? open = true : null"
         x-on:close-modal.window="$event.detail == '{{ $name }}' ? open = false : null"
     @endif
     @if ($focusable)
         x-on:keydown.tab.window="$event.shiftKey && document.activeElement == $refs.focusable ? null : null"
     @endif
     {{ $attributes->whereDoesntStartWith('max-w') }}>
    @if ($trigger)
        {{ $trigger }}
    @endif

    <div x-cloak
         x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm"
         x-on:click="{{ $closeable ? 'open = false' : '' }}"></div>

    <div x-cloak
         x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="translate-y-4 opacity-0 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="translate-y-0 opacity-100 sm:scale-100"
         x-transition:leave-end="translate-y-4 opacity-0 sm:translate-y-0 sm:scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4"
         x-on:click="{{ $closeable ? 'open = false' : '' }}">
        <div class="w-full {{ $maxWidthClass }}"
             x-on:click.stop>
            <div class="card overflow-hidden">
                @if ($title || $closeable)
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $title }}</h3>
                        @if ($closeable)
                            <button type="button" x-on:click="open = false" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700">
                                <x-icon name="x-mark" class="h-5 w-5" />
                            </button>
                        @endif
                    </div>
                @endif

                <div class="p-5">{{ $slot }}</div>
            </div>
        </div>
    </div>
</div>
