<x-app-layout>
    <x-page-header title="Expenses" subtitle="Track clinic spending">
        <x-slot name="actions">
            @can('expenses.create')
                <x-button href="{{ route('expenses.create') }}" icon="banknotes">Record Expense</x-button>
            @endcan
        </x-slot>
    </x-page-header>

    <x-alerts />

    <div class="mb-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Filtered Total" :value="money($total)" icon="banknotes" tone="red" />
    </div>

    <!-- Filters -->
    <div class="card mb-4 p-4">
        <form method="GET" action="{{ route('expenses.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="min-w-64 flex-1">
                <x-label for="q">Search</x-label>
                <x-input name="q" id="q" :value="request('q')" placeholder="Description, category..." />
            </div>
            <div class="w-48">
                <x-label for="category">Category</x-label>
                <x-select name="category" id="category" :options="$categories" :value="request('category')" placeholder="Any" />
            </div>
            <div class="w-40">
                <x-label for="date_from">From</x-label>
                <x-date-input name="date_from" id="date_from" :value="request('date_from')" />
            </div>
            <div class="w-40">
                <x-label for="date_to">To</x-label>
                <x-date-input name="date_to" id="date_to" :value="request('date_to')" />
            </div>
            <x-button type="submit" variant="secondary" icon="search">Filter</x-button>
            @if (request()->hasAny(['q', 'category', 'date_from', 'date_to']))
                <x-button href="{{ route('expenses.index') }}" variant="ghost" icon="x-mark">Clear</x-button>
            @endif
        </form>
    </div>

    <div class="card overflow-hidden">
        <x-table :headers="['Date', 'Description', 'Category', 'Method', 'Amount', '']">
            @forelse ($expenses as $expense)
                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/40">
                    <td class="td">{{ $expense->expense_date?->format('M d, Y') }}</td>
                    <td class="td">
                        <a href="{{ route('expenses.show', $expense) }}" class="font-medium text-slate-900 hover:underline dark:text-white">
                            {{ $expense->description }}
                        </a>
                    </td>
                    <td class="td">
                        @if ($expense->category)
                            <x-badge variant="primary">{{ $expense->category->name }}</x-badge>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="td">{{ $expense->payment_method?->label() ?: '—' }}</td>
                    <td class="td">
                        <span class="font-semibold text-slate-900 dark:text-white">{{ money($expense->amount) }}</span>
                    </td>
                    <td class="td">
                        <div class="flex items-center justify-end gap-1">
                            <x-button :href="route('expenses.show', $expense)" variant="ghost" size="sm" icon="eye">View</x-button>
                            @can('expenses.update')
                                <x-button :href="route('expenses.edit', $expense)" variant="ghost" size="sm" icon="pencil">Edit</x-button>
                            @endcan
                            @can('expenses.delete')
                                <form method="POST" action="{{ route('expenses.destroy', $expense) }}"
                                      x-data x-on:submit.prevent="$dispatch('confirm-submit')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="submit" variant="ghost" size="sm" icon="trash" class="text-red-600">Delete</x-button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <x-empty-state message="No expenses found." icon="banknotes"
                                       :actionLabel="auth()->user()->can('expenses.create') ? 'Record Expense' : null"
                                       :actionHref="auth()->user()->can('expenses.create') ? route('expenses.create') : null" />
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700">
            <x-pagination :paginator="$expenses" />
        </div>
    </div>

    <!-- Categories -->
    <x-card title="Categories" subtitle="Organize expenses into categories" class="mt-6">
        <x-slot name="header">
            <span class="hidden"></span>
        </x-slot>
        @can('expenses.create')
            <form method="POST" action="{{ route('expenses.categories.store') }}" class="flex flex-wrap items-end gap-3">
                @csrf
                <div class="min-w-64 flex-1">
                    <x-label for="name">New Category</x-label>
                    <x-input name="name" id="name" placeholder="e.g. Utilities" />
                </div>
                <x-button type="submit" variant="secondary" icon="plus">Add Category</x-button>
            </form>
        @endcan

        <div class="mt-4 flex flex-wrap gap-2">
            @forelse ($categories as $id => $name)
                <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-3 py-1 text-sm dark:border-slate-700">
                    {{ $name }}
                    @can('expenses.delete')
                        <form method="POST" action="{{ route('expenses.categories.destroy', $id) }}"
                              x-data x-on:submit.prevent="$dispatch('confirm-submit')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700" title="Delete {{ $name }}">
                                <x-icon name="trash" class="h-3.5 w-3.5" />
                            </button>
                        </form>
                    @endcan
                </span>
            @empty
                <p class="text-sm text-slate-400">No categories yet.</p>
            @endforelse
        </div>
    </x-card>
</x-app-layout>
