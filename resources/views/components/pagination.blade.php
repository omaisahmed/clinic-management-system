@props(['paginator'])

@if ($paginator->hasPages())
    <div class="mt-4 flex items-center justify-between">
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Showing <span class="font-medium">{{ $paginator->firstItem() ?? 0 }}</span>–
            <span class="font-medium">{{ $paginator->lastItem() ?? 0 }}</span> of
            <span class="font-medium">{{ $paginator->total() }}</span>
        </p>

        <nav class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="rounded-lg border border-slate-200 p-2 text-slate-300 dark:border-slate-700 dark:text-slate-600">
                    <x-icon name="chevron-left" class="h-4 w-4" />
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="rounded-lg border border-slate-200 p-2 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                    <x-icon name="chevron-left" class="h-4 w-4" />
                </a>
            @endif

            @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                <a href="{{ $url }}"
                   class="rounded-lg px-3 py-1.5 text-sm font-medium {{ $page === $paginator->currentPage() ? 'bg-[var(--color-primary)] text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                    {{ $page }}
                </a>
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="rounded-lg border border-slate-200 p-2 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                    <x-icon name="chevron-right" class="h-4 w-4" />
                </a>
            @else
                <span class="rounded-lg border border-slate-200 p-2 text-slate-300 dark:border-slate-700 dark:text-slate-600">
                    <x-icon name="chevron-right" class="h-4 w-4" />
                </span>
            @endif
        </nav>
    </div>
@endif
