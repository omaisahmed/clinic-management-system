@props(['name' => null, 'accept' => '*/*', 'preview' => false, 'label' => null])

<div x-data="fileInput">
    <label class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-6 py-6 text-center transition hover:border-[var(--color-primary)] hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-800/60 dark:hover:border-[var(--color-primary)] dark:hover:bg-slate-800">
        <input
            type="file"
            accept="{{ $accept }}"
            @if ($name) name="{{ $name }}" @endif
            x-on:change="update($event)"
            class="sr-only"
            {{ $attributes }} />
        <x-icon name="upload" class="h-7 w-7 text-slate-400" />
        <p class="mt-2 text-sm font-medium text-slate-600 dark:text-slate-300">
            {{ $label ?? 'Click to upload or drag & drop' }}
        </p>
        <p class="mt-1 text-xs text-slate-400" x-text="fileName || 'No file selected'"></p>
    </label>

    @if ($preview)
        <template x-if="previewUrl">
            <img :src="previewUrl" class="mt-3 max-h-40 rounded-lg object-contain" alt="Preview" />
        </template>
    @endif

    @if ($name !== null && $errors->has($name))
        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $errors->first($name) }}</p>
    @endif
</div>
