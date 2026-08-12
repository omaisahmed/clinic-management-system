<x-app-layout>
    <x-page-header :title="$prescription->prescription_number . ' · ' . ($prescription->patient?->full_name ?? 'Prescription')" :subtitle="$prescription->created_at->format('M d, Y')">
        <x-slot name="actions">
            @can('prescriptions.update')
                <x-button :href="route('prescriptions.edit', $prescription)" variant="secondary" icon="pencil">Edit</x-button>
            @endcan
            @can('prescriptions.print')
                <x-button :href="route('prescriptions.print', $prescription)" variant="secondary" icon="document-text">Print</x-button>
            @endcan
            <x-button href="{{ route('prescriptions.index') }}" variant="ghost" icon="arrow-left">Back to Prescriptions</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="card p-6">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Medications</h3>
                <div class="mt-4">
                    <x-table :headers="['Name', 'Dosage', 'Frequency', 'Duration', 'Instructions']">
                        @forelse ($prescription->items as $item)
                            <tr class="align-top">
                                <td class="td font-medium text-slate-900 dark:text-white">{{ $item->name }}</td>
                                <td class="td">{{ $item->dosage ?: '—' }}</td>
                                <td class="td">{{ $item->frequency ?: '—' }}</td>
                                <td class="td">{{ $item->duration ?: '—' }}</td>
                                <td class="td">{{ $item->instructions ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <x-empty-state message="No medications on this prescription." icon="capsule" />
                                </td>
                            </tr>
                        @endforelse
                    </x-table>
                </div>
            </div>

            @if ($prescription->notes)
                <div class="card p-6">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">Notes</h3>
                    <p class="mt-3 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $prescription->notes }}</p>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Prescription Details</h3>
                <dl class="mt-4 grid gap-4">
                    <x-detail label="Patient">
                        @if ($prescription->patient)
                            @can('patients.view')
                                <a href="{{ route('patients.show', $prescription->patient) }}" class="hover:underline">{{ $prescription->patient->full_name }}</a>
                            @else
                                {{ $prescription->patient->full_name }}
                            @endcan
                        @else
                            —
                        @endif
                    </x-detail>
                    <x-detail label="Doctor">{{ $prescription->doctor?->name ?: '—' }}</x-detail>
                    <x-detail label="Date">{{ $prescription->created_at->format('M d, Y') }}</x-detail>
                    <x-detail label="Status">
                        <x-badge variant="{{ $prescription->status?->color() ?? 'gray' }}">{{ $prescription->status?->label() ?? '—' }}</x-badge>
                    </x-detail>
                    <x-detail label="Notes">{{ $prescription->notes ?: '—' }}</x-detail>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
