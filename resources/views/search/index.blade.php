<x-app-layout>
    <x-page-header title="Search Results" icon="search" :subtitle="$subtitle">
        <x-slot name="actions">
            <form method="GET" action="{{ route('search.index') }}" class="flex items-center gap-2">
                <x-input name="q" value="{{ $term }}" placeholder="Search again..." class="min-w-56" />
                <x-button type="submit" variant="secondary" icon="search">Search</x-button>
            </form>
        </x-slot>
    </x-page-header>

    <x-alerts />

    @if ($term === '')
        <div class="card">
            <x-empty-state message="Enter a search term to find patients, invoices, medicines and more." icon="search" />
        </div>
    @elseif ($total === 0)
        <div class="card">
            <x-empty-state :message="$emptyMessage" icon="search" />
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            @foreach ($results as $group)
                <div class="card overflow-hidden">
                    <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                        <div class="flex items-center gap-2">
                            <x-icon :name="$group['icon']" class="h-5 w-5 text-slate-400" />
                            <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $group['label'] }}</h3>
                            <span class="text-xs text-slate-400">{{ $group['items']->count() }}</span>
                        </div>
                        <a href="{{ $group['href'] }}" class="text-sm font-medium text-[var(--color-primary)] hover:underline">View all</a>
                    </div>

                    <ul class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach ($group['items'] as $item)
                            <li>
                                <a href="{{ $item['href'] }}"
                                   class="flex items-start justify-between gap-3 px-5 py-3 transition hover:bg-slate-50 dark:hover:bg-slate-700/40">
                                    <div class="min-w-0">
                                        <p class="truncate font-medium text-slate-900 dark:text-white">{{ $item['title'] }}</p>
                                        @if ($item['subtitle'] !== '')
                                            <p class="truncate text-sm text-slate-500 dark:text-slate-400">{{ $item['subtitle'] }}</p>
                                        @endif
                                    </div>
                                    <x-icon name="chevron-right" class="mt-1 h-4 w-4 shrink-0 text-slate-300 dark:text-slate-600" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
