<x-app-layout>
    <x-page-header title="Revenue" subtitle="Monthly revenue vs expenses" />

    <x-alerts />

    <x-card title="Summary">
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="card p-5">
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ money($totalRevenue) }}</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Total Revenue</p>
            </div>
            <div class="card p-5">
                <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ money($totalExpenses) }}</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Total Expenses</p>
            </div>
            <div class="card p-5">
                <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ money($net) }}</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Net</p>
            </div>
        </div>
    </x-card>

    <div class="card mt-6 overflow-hidden">
        <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-700">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Monthly Revenue vs Expenses</h3>
            <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">Last 12 months</p>
        </div>

        @if ($hasRevenueData)
            <x-table :headers="['Month', 'Revenue', 'Expenses', 'Net']">
                @foreach ($months as $i => $month)
                    <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/40">
                        <td class="td font-medium text-slate-900 dark:text-white">
                            {{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $month)->format('M Y') }}
                        </td>
                        <td class="td text-green-600 dark:text-green-400">{{ money($revenue[$i]) }}</td>
                        <td class="td text-red-600 dark:text-red-400">{{ money($expenses[$i]) }}</td>
                        <td class="td font-medium text-slate-900 dark:text-white">{{ money($revenue[$i] - $expenses[$i]) }}</td>
                    </tr>
                @endforeach
            </x-table>
        @else
            <div class="p-5">
                <x-empty-state message="No revenue or expense data available yet." icon="banknotes" />
            </div>
        @endif
    </div>
</x-app-layout>
