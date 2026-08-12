@props(['name' => null, 'label' => null, 'checked' => false, 'value' => '1'])

<label class="flex cursor-pointer items-start gap-3">
    <input
        type="checkbox"
        @if ($name) name="{{ $name }}" @endif
        value="{{ $value }}"
        @checked(old($name, $checked))
        {{ $attributes->merge([
            'class' => 'mt-0.5 h-4 w-4 rounded border-slate-300 text-[var(--color-primary)] focus:ring-[var(--color-primary)] dark:border-slate-600 dark:bg-slate-800',
        ]) }}
    />
    @if ($label)
        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $label }}</span>
    @endif
    {{ $slot }}
</label>

@if ($name !== null && $errors->has($name))
    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $errors->first($name) }}</p>
@endif
