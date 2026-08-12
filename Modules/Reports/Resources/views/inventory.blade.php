<x-app-layout>
    <x-page-header title="Inventory" subtitle="Medicine stock overview" />

    <x-alerts />

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-stat-card label="Total Medicines" :value="$totalMedicines" icon="capsule" tone="primary" />
        <x-stat-card label="Low Stock Items" :value="$lowStockCount" icon="alert-triangle" tone="red" />
        <x-stat-card label="Total Stock Quantity" :value="$totalStock" icon="clipboard-list" tone="green" />
    </div>

    <div class="card mt-6 overflow-hidden">
        <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-700">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Low Stock Medicines</h3>
            <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">Stock at or below the reorder level</p>
        </div>

        <x-table :headers="['Name', 'Stock', 'Reorder Level']">
            @forelse ($lowStock as $medicine)
                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/40">
                    <td class="td font-medium text-slate-900 dark:text-white">{{ $medicine->name }}</td>
                    <td class="td font-semibold text-red-600 dark:text-red-400">{{ $medicine->stock }}</td>
                    <td class="td">{{ $medicine->reorder_level }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">
                        <x-empty-state message="No medicine inventory available yet." icon="capsule" />
                    </td>
                </tr>
            @endforelse
        </x-table>
    </div>
</x-app-layout>
