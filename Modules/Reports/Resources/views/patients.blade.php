<x-app-layout>
    <x-page-header title="Patient Reports" subtitle="Patient demographics and billing" />

    <x-alerts />

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-stat-card label="Total Patients" :value="$totalPatients" icon="users" tone="purple" />
        <x-stat-card label="Male" :value="$malePatients" icon="user" tone="blue" />
        <x-stat-card label="Female" :value="$femalePatients" icon="user" tone="purple" />
    </div>

    <div class="card mt-6 overflow-hidden">
        <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-700">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Top Patients by Billing</h3>
            <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">By total invoice amount</p>
        </div>

        <x-table :headers="['Patient', 'Total Billed']">
            @forelse ($topPatients as $patient)
                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/40">
                    <td class="td font-medium text-slate-900 dark:text-white">{{ $patient->name }}</td>
                    <td class="td text-green-600 dark:text-green-400">{{ money($patient->total_billed) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">
                        <x-empty-state message="No billed patients yet." icon="users" />
                    </td>
                </tr>
            @endforelse
        </x-table>
    </div>
</x-app-layout>
