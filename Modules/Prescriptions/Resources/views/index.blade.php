<x-app-layout>
    <x-page-header title="Prescriptions" subtitle="Create and manage prescriptions">
        <x-slot name="actions">
            @can('prescriptions.create')
                <x-button href="{{ route('prescriptions.create') }}" icon="document-text">New Prescription</x-button>
            @endcan
        </x-slot>
    </x-page-header>

    <x-alerts />

    <!-- Filters -->
    <div class="card mb-4 p-4">
        <form method="GET" action="{{ route('prescriptions.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="min-w-64 flex-1">
                <x-label for="q">Search</x-label>
                <x-input name="q" id="q" :value="request('q')" placeholder="Patient name, phone, Rx number..." />
            </div>
            <div class="w-40">
                <x-label for="status">Status</x-label>
                <x-select name="status" id="status" :options="$statuses" :value="request('status')" placeholder="Any" />
            </div>
            <x-button type="submit" variant="secondary" icon="search">Filter</x-button>
            @if (request()->hasAny(['q', 'status']))
                <x-button href="{{ route('prescriptions.index') }}" variant="ghost" icon="x-mark">Clear</x-button>
            @endif
        </form>
    </div>

    <div class="card overflow-hidden">
        <x-table :headers="['#', 'Patient', 'Date', 'Doctor', 'Items', 'Status', '']">
            @forelse ($prescriptions as $prescription)
                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/40">
                    <td class="td">
                        <a href="{{ route('prescriptions.show', $prescription) }}" class="font-medium text-slate-900 hover:underline dark:text-white">
                            <x-badge variant="primary">{{ $prescription->prescription_number }}</x-badge>
                        </a>
                    </td>
                    <td class="td">
                        @if ($prescription->patient)
                            @can('patients.view')
                                <a href="{{ route('patients.show', $prescription->patient) }}" class="font-medium text-slate-900 hover:underline dark:text-white">
                                    {{ $prescription->patient->full_name }}
                                </a>
                            @else
                                <span class="font-medium text-slate-900 dark:text-white">{{ $prescription->patient->full_name }}</span>
                            @endcan
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="td">{{ $prescription->created_at->format('M d, Y') }}</td>
                    <td class="td">{{ $prescription->doctor?->name ?: '—' }}</td>
                    <td class="td">{{ $prescription->items_count }}</td>
                    <td class="td">
                        <x-badge variant="{{ $prescription->status?->color() ?? 'gray' }}">{{ $prescription->status?->label() ?? '—' }}</x-badge>
                    </td>
                    <td class="td">
                        <div class="flex items-center justify-end gap-1">
                            <x-button :href="route('prescriptions.show', $prescription)" variant="ghost" size="sm" icon="eye">View</x-button>
                            @can('prescriptions.update')
                                <x-button :href="route('prescriptions.edit', $prescription)" variant="ghost" size="sm" icon="pencil">Edit</x-button>
                            @endcan
                            @can('prescriptions.print')
                                <x-button :href="route('prescriptions.print', $prescription)" variant="ghost" size="sm" icon="document-text">Print</x-button>
                            @endcan
                            @can('prescriptions.delete')
                                <form method="POST" action="{{ route('prescriptions.destroy', $prescription) }}"
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
                    <td colspan="7">
                        <x-empty-state message="No prescriptions found." icon="document-text"
                                       :actionLabel="auth()->user()->can('prescriptions.create') ? 'New Prescription' : null"
                                       :actionHref="auth()->user()->can('prescriptions.create') ? route('prescriptions.create') : null" />
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700">
            <x-pagination :paginator="$prescriptions" />
        </div>
    </div>
</x-app-layout>
