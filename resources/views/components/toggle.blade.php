@props(['name' => null, 'label' => null, 'checked' => false])

<label class="inline-flex cursor-pointer items-center gap-3">
    <button type="button"
            role="switch"
            aria-checked="{{ $checked ? 'true' : 'false' }}"
            x-data="{ on: {{ $checked ? 'true' : 'false' }} }"
            x-on:click="on = !on"
            :class="on ? 'bg-[var(--color-primary)]' : 'bg-slate-300 dark:bg-slate-600'"
            class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition"
            {{ $attributes }}>
        @if ($name)
            <input type="hidden" :name="'{{ $name }}'" :value="on ? '1' : '0'" />
        @endif
        <span x-cloak
              :class="on ? 'translate-x-6' : 'translate-x-1'"
              class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition"></span>
    </button>
    @if ($label)
        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $label }}</span>
    @endif
    {{ $slot }}
</label>
