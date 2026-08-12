<x-app-layout>
    <x-page-header title="Queue" subtitle="Daily patient queue">
        <x-slot name="actions">
            @can('queue.create')
                <x-button href="#add" icon="user-plus">Add to Queue</x-button>
            @endcan
        </x-slot>
    </x-page-header>

    <x-alerts />

    @can('queue.create')
        <div id="add" class="card mb-4 p-4">
            <form method="POST" action="{{ route('queue.store') }}" class="flex flex-wrap items-end gap-3">
                @csrf
                <div class="min-w-56 flex-1">
                    <x-label for="patient_id" :required="true">Patient</x-label>
                    <x-select name="patient_id" id="patient_id" :options="$patients" placeholder="Select patient" />
                </div>
                <div class="w-44">
                    <x-label for="doctor_id">Doctor</x-label>
                    <x-select name="doctor_id" id="doctor_id" :options="$doctors" placeholder="Unassigned" />
                </div>
                <div class="w-56">
                    <x-label for="appointment_id">Appointment</x-label>
                    <x-select name="appointment_id" id="appointment_id" :options="$appointments" placeholder="None" />
                </div>
                <div class="w-64">
                    <x-label for="notes">Notes</x-label>
                    <x-input name="notes" id="notes" placeholder="Notes" />
                </div>
                <x-button type="submit" icon="plus">Add to Queue</x-button>
            </form>
        </div>
    @endcan

    <div class="card mb-4 p-4">
        <form method="GET" action="{{ route('queue.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="w-44">
                <x-label for="date">Date</x-label>
                <x-date-input name="date" id="date" :value="$date" />
            </div>
            <x-button type="submit" variant="secondary" icon="search">Filter</x-button>
            @if (request('date') && request('date') !== today()->toDateString())
                <x-button href="{{ route('queue.index') }}" variant="ghost" icon="clock">Go to Today</x-button>
            @endif
        </form>
    </div>

    @can('queue.update')
        <div class="card mb-4 flex flex-wrap items-center justify-between gap-3 p-4">
            <div>
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Call Next Patient</h3>
                <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">Advance the earliest waiting patient to In Progress.</p>
            </div>
            <form method="POST" action="{{ route('queue.call-next') }}">
                @csrf
                <x-button type="submit" icon="arrow-right">Call Next Patient</x-button>
            </form>
        </div>
    @endcan

    <div class="mb-4 grid gap-4 sm:grid-cols-3">
        <div class="card p-5">
            <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $waitingCount }}</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Waiting</p>
        </div>
        <div class="card p-5">
            <p class="text-3xl font-bold text-amber-500">{{ $inProgressCount }}</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">In Progress</p>
        </div>
        <div class="card p-5">
            <p class="text-3xl font-bold text-green-500">{{ $completedCount }}</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Completed</p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <x-table :headers="['Token', 'Patient', 'Doctor', 'Entered', 'Status', 'Actions']">
            @forelse ($entries as $entry)
                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/40">
                    <td class="td">
                        <x-badge variant="primary">#{{ $entry->token_number }}</x-badge>
                    </td>
                    <td class="td">
                        @if ($entry->patient)
                            <div>
                                <a href="{{ route('patients.show', $entry->patient) }}" class="font-medium text-slate-900 hover:underline dark:text-white">
                                    {{ $entry->patient->full_name }}
                                </a>
                                @if ($entry->patient->phone)
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $entry->patient->phone }}</p>
                                @endif
                            </div>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="td">{{ $doctors[$entry->doctor_id] ?? '—' }}</td>
                    <td class="td text-slate-500">{{ $entry->entered_at?->format('h:i A') ?: '—' }}</td>
                    <td class="td">
                        <x-badge variant="{{ $entry->status->color() }}">{{ $entry->status->label() }}</x-badge>
                    </td>
                    <td class="td">
                        <div class="flex items-center justify-end gap-1">
                            @can('queue.update')
                                @if ($entry->status->value === 'waiting')
                                    <form method="POST" action="{{ route('queue.status', $entry) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="in_progress">
                                        <x-button type="submit" variant="secondary" size="sm" icon="arrow-right">Start</x-button>
                                    </form>
                                @endif
                                @if ($entry->status->value === 'in_progress')
                                    <form method="POST" action="{{ route('queue.status', $entry) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <x-button type="submit" variant="secondary" size="sm" icon="check">Complete</x-button>
                                    </form>
                                    <form method="POST" action="{{ route('queue.status', $entry) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="skipped">
                                        <x-button type="submit" variant="ghost" size="sm" icon="chevron-right">Skip</x-button>
                                    </form>
                                @endif
                                @if ($entry->status->value === 'waiting')
                                    <form method="POST" action="{{ route('queue.status', $entry) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="cancelled">
                                        <x-button type="submit" variant="ghost" size="sm" icon="x-mark">Cancel</x-button>
                                    </form>
                                @endif
                            @endcan
                            @can('queue.delete')
                                <form method="POST" action="{{ route('queue.destroy', $entry) }}"
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
                        <x-empty-state message="No patients in the queue." icon="clock" />
                    </td>
                </tr>
            @endforelse
        </x-table>
    </div>
</x-app-layout>
