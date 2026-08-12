@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-800 dark:bg-green-950 dark:text-green-200']) }}>
        <x-icon name="check-circle" class="mt-0.5 h-5 w-5 shrink-0 text-green-500" />
        <div>{{ $status }}</div>
    </div>
@endif
