<x-app-layout>
    <x-page-header title="Appointments" subtitle="Schedule and manage patient appointments">
        <x-slot name="actions">
            @can('appointments.create')
                <x-button href="{{ route('appointments.create') }}" icon="calendar">New Appointment</x-button>
            @endcan
        </x-slot>
    </x-page-header>

    <x-alerts />

    <!-- Filters -->
    <div class="card mb-4 p-4">
        <form method="GET" action="{{ route('appointments.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="min-w-64 flex-1">
                <x-label for="q">Search</x-label>
                <x-input name="q" id="q" :value="request('q')" placeholder="Patient name, phone, reason..." />
            </div>
            <div class="w-44">
                <x-label for="status">Status</x-label>
                <x-select name="status" id="status" :options="$statuses" :value="request('status')" placeholder="Any" />
            </div>
            <div class="w-44">
                <x-label for="date">Date</x-label>
                <x-date-input name="date" id="date" :value="request('date')" />
            </div>
            <x-button type="submit" variant="secondary" icon="search">Filter</x-button>
            @if (request()->hasAny(['q', 'status', 'date']))
                <x-button href="{{ route('appointments.index') }}" variant="ghost" icon="x-mark">Clear</x-button>
            @endif
        </form>
    </div>

    <div class="card overflow-hidden">
        <x-table :headers="['Patient', 'Type', 'Date & Time', 'Doctor', 'Status', '']">
            @forelse ($appointments as $appointment)
                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/40">
                    <td class="td">
                        @if ($appointment->patient)
                            <a href="{{ route('patients.show', $appointment->patient) }}"
                               class="font-medium text-slate-900 hover:underline dark:text-white">
                                {{ $appointment->patient->full_name }}
                            </a>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="td">{{ $appointment->appointment_type->label() }}</td>
                    <td class="td">{{ $appointment->appointment_date->format('M d, Y') }} · {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }}</td>
                    <td class="td">{{ $appointment->doctor?->name ?: '—' }}</td>
                    <td class="td">
                        <x-badge variant="{{ $appointment->status->color() }}">{{ $appointment->status->label() }}</x-badge>
                    </td>
                    <td class="td">
                        <div class="flex items-center justify-end gap-1">
                            <x-button :href="route('appointments.show', $appointment)" variant="ghost" size="sm" icon="eye">View</x-button>
                            @can('appointments.update')
                                <x-button :href="route('appointments.edit', $appointment)" variant="ghost" size="sm" icon="pencil">Edit</x-button>
                            @endcan
                            @can('appointments.delete')
                                <form method="POST" action="{{ route('appointments.destroy', $appointment) }}"
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
                        <x-empty-state message="No appointments found." icon="calendar"
                                       :actionLabel="auth()->user()->can('appointments.create') ? 'New Appointment' : null"
                                       :actionHref="auth()->user()->can('appointments.create') ? route('appointments.create') : null" />
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700">
            <x-pagination :paginator="$appointments" />
        </div>
    </div>
</x-app-layout>
