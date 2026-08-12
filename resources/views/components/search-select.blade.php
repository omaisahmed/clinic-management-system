@props([
    'name' => null,
    'value' => null,
    'options' => [],
    'placeholder' => 'Select...',
    'required' => false,
])

@php
    if (is_object($options) && method_exists($options, 'toArray')) {
        $options = $options->toArray();
    }

    $items = collect($options)
        ->map(fn ($label, $val): array => ['value' => (string) $val, 'label' => (string) $label])
        ->values()
        ->all();

    $selected = collect($items)->firstWhere('value', (string) $value) ?? null;
@endphp

<div x-data="searchSelect(@js($items), @js($selected))" class="relative">
    <input
        type="hidden"
        name="{{ $name }}"
        :value="selected ? selected.value : ''"
        @if ($required) required @endif
    />

    <button
        type="button"
        x-on:click="open = !open; if (open) $nextTick(() => $refs.search?.focus())"
        class="input flex w-full items-center justify-between gap-2 text-left"
        aria-haspopup="listbox"
        :aria-expanded="open.toString()"
    >
        <span :class="selected ? '' : 'text-slate-400 dark:text-slate-500'" x-cloak>
            <span x-show="selected" x-text="selected ? selected.label : ''"></span>
            <span x-show="!selected">{{ $placeholder }}</span>
        </span>
        <x-icon name="chevron-down" class="h-4 w-4 shrink-0 text-slate-400" />
    </button>

    <div
        x-show="open"
        x-on:click.outside="open = false"
        x-on:keydown.escape.window="open = false"
        x-cloak
        class="absolute z-30 mt-1 w-full overflow-hidden rounded-lg border border-slate-200 bg-white shadow-lg dark:border-slate-700 dark:bg-slate-800"
    >
        <div class="border-b border-slate-200 p-2 dark:border-slate-700">
            <input
                type="text"
                x-model="query"
                x-ref="search"
                placeholder="Search..."
                autocomplete="off"
                class="w-full rounded-md border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-900 focus:border-[var(--color-primary)] focus:outline-none dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
            />
        </div>

        <ul class="max-h-56 overflow-y-auto py-1" role="listbox">
            <template x-for="option in filtered" :key="option.value">
                <li>
                    <button
                        type="button"
                        x-on:click="select(option)"
                        class="flex w-full items-center justify-between gap-2 px-3 py-2 text-left text-sm"
                        :class="selected && selected.value === option.value
                            ? 'bg-[var(--color-primary-50)] text-[var(--color-primary-600)] dark:bg-slate-700 dark:text-white'
                            : 'text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-700'"
                    >
                        <span x-text="option.label"></span>
                        <x-icon name="check" class="h-4 w-4 shrink-0" x-show="selected && selected.value === option.value" />
                    </button>
                </li>
            </template>
            <li x-show="filtered.length === 0" class="px-3 py-2 text-sm text-slate-400">No matching options</li>
        </ul>
    </div>
</div>
