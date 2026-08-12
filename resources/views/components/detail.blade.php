@props(['label' => null])

<div>
    <dt class="text-xs uppercase tracking-wide text-slate-400">{{ $label }}</dt>
    <dd class="mt-1 text-sm font-medium text-slate-900 dark:text-white">{{ $slot }}</dd>
</div>
