<x-app-layout>
    <x-page-header title="Medicines" subtitle="Pharmacy inventory">
        <x-slot name="actions">
            @can('medicines.create')
                <x-button href="{{ route('medicines.create') }}" icon="capsule">Add Medicine</x-button>
            @endcan
        </x-slot>
    </x-page-header>

    <x-alerts />

    <!-- Filters -->
    <div class="card mb-4 p-4">
        <form method="GET" action="{{ route('medicines.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="min-w-64 flex-1">
                <x-label for="q">Search</x-label>
                <x-input name="q" id="q" :value="request('q')" placeholder="Name, generic name, brand, category..." />
            </div>
            <div class="w-44">
                <x-label for="category">Category</x-label>
                <x-select name="category" id="category" :options="$categories" :value="request('category')" placeholder="Any" />
            </div>
            <div class="flex items-end pb-2">
                <x-checkbox name="low_stock" :checked="request()->boolean('low_stock')">
                    <span class="text-sm text-slate-700 dark:text-slate-300">Low stock only</span>
                </x-checkbox>
            </div>
            <x-button type="submit" variant="secondary" icon="search">Filter</x-button>
            @if (request()->hasAny(['q', 'category', 'low_stock']))
                <x-button href="{{ route('medicines.index') }}" variant="ghost" icon="x-mark">Clear</x-button>
            @endif
        </form>
    </div>

    <!-- Summary -->
    <div class="mb-4 grid gap-4 sm:grid-cols-3">
        <div class="card p-5">
            <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $totalCount }}</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Total Medicines</p>
        </div>
        <div class="card p-5">
            <p class="text-3xl font-bold text-red-500">{{ $lowStockCount }}</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Low Stock</p>
        </div>
        <div class="card p-5">
            <p class="text-3xl font-bold text-amber-500">{{ $expiringCount }}</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Expiring in 30 Days</p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <x-table :headers="['Name', 'Category', 'Stock', 'Price', 'Expiry', '']">
            @forelse ($medicines as $medicine)
                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/40">
                    <td class="td">
                        <a href="{{ route('medicines.show', $medicine) }}" class="font-medium text-slate-900 hover:underline dark:text-white">
                            {{ $medicine->name }}
                        </a>
                    </td>
                    <td class="td">{{ $medicine->category ?: '—' }}</td>
                    <td class="td">
                        <span class="flex items-center gap-2">
                            @if ($medicine->is_low_stock)
                                <x-badge variant="red">Low</x-badge>
                            @else
                                <x-badge variant="green">In stock</x-badge>
                            @endif
                            <span class="font-medium text-slate-900 dark:text-white">{{ $medicine->stock }}</span>
                        </span>
                    </td>
                    <td class="td">{{ $medicine->selling_price !== null ? money($medicine->selling_price) : '—' }}</td>
                    <td class="td">
                        @if ($medicine->expiry_date)
                            <span class="{{ $medicine->expiry_date->lte(now()->addDays(30)) ? 'font-medium text-red-600 dark:text-red-400' : '' }}">
                                {{ $medicine->expiry_date->format('M d, Y') }}
                            </span>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="td">
                        <div class="flex items-center justify-end gap-1">
                            <x-button :href="route('medicines.show', $medicine)" variant="ghost" size="sm" icon="eye">View</x-button>
                            @can('medicines.update')
                                <x-button :href="route('medicines.edit', $medicine)" variant="ghost" size="sm" icon="pencil">Edit</x-button>
                            @endcan
                            @can('medicines.delete')
                                <form method="POST" action="{{ route('medicines.destroy', $medicine) }}"
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
                        <x-empty-state message="No medicines found." icon="capsule"
                                       :actionLabel="auth()->user()->can('medicines.create') ? 'Add Medicine' : null"
                                       :actionHref="auth()->user()->can('medicines.create') ? route('medicines.create') : null" />
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700">
            <x-pagination :paginator="$medicines" />
        </div>
    </div>
</x-app-layout>
