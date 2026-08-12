<x-app-layout>
    <x-page-header title="Visits" subtitle="Record and manage patient visits">
        <x-slot name="actions">
            @can('visits.create')
                <x-button href="{{ route('visits.create') }}" icon="stethoscope">New Visit</x-button>
            @endcan
        </x-slot>
    </x-page-header>

    <x-alerts />

    <!-- Filters -->
    <div class="card mb-4 p-4">
        <form method="GET" action="{{ route('visits.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="min-w-64 flex-1">
                <x-label for="q">Search</x-label>
                <x-input name="q" id="q" :value="request('q')" placeholder="Patient name, phone, chief complaint..." />
            </div>
            <div class="w-40">
                <x-label for="status">Status</x-label>
                <x-select name="status" id="status" :options="$statuses" :value="request('status')" placeholder="Any" />
            </div>
            <div class="w-44">
                <x-label for="date_from">From</x-label>
                <x-date-input name="date_from" id="date_from" :value="request('date_from')" />
            </div>
            <div class="w-44">
                <x-label for="date_to">To</x-label>
                <x-date-input name="date_to" id="date_to" :value="request('date_to')" />
            </div>
            <x-button type="submit" variant="secondary" icon="search">Filter</x-button>
            @if (request()->hasAny(['q', 'status', 'date_from', 'date_to']))
                <x-button href="{{ route('visits.index') }}" variant="ghost" icon="x-mark">Clear</x-button>
            @endif
        </form>
    </div>

    <div class="card overflow-hidden">
        <x-table :headers="['Visit #', 'Patient', 'Date', 'Doctor', 'Status', 'Actions']">
            @forelse ($visits as $visit)
                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/40">
                    <td class="td">
                        <a href="{{ route('visits.show', $visit) }}" class="font-medium text-slate-900 hover:underline dark:text-white">
                            <x-badge variant="primary">{{ $visit->visit_number }}</x-badge>
                        </a>
                    </td>
                    <td class="td">
                        @if ($visit->patient)
                            @can('patients.view')
                                <a href="{{ route('patients.show', $visit->patient) }}" class="font-medium text-slate-900 hover:underline dark:text-white">
                                    {{ $visit->patient->full_name }}
                                </a>
                            @else
                                <span class="font-medium text-slate-900 dark:text-white">{{ $visit->patient->full_name }}</span>
                            @endcan
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="td">{{ $visit->visit_date?->format('M d, Y') }}</td>
                    <td class="td">{{ $visit->doctor?->name ?: '—' }}</td>
                    <td class="td">
                        <x-badge variant="{{ $visit->status?->color() ?? 'gray' }}">{{ $visit->status?->label() ?? '—' }}</x-badge>
                    </td>
                    <td class="td">
                        <div class="flex items-center justify-end gap-1">
                            <x-button :href="route('visits.show', $visit)" variant="ghost" size="sm" icon="eye">View</x-button>
                            @can('visits.update')
                                <x-button :href="route('visits.edit', $visit)" variant="ghost" size="sm" icon="pencil">Edit</x-button>
                            @endcan
                            @if ($visit->status === \Modules\Visits\Enums\VisitStatus::InProgress)
                                <form method="POST" action="{{ route('visits.complete', $visit) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <x-button type="submit" variant="ghost" size="sm" icon="check">Complete</x-button>
                                </form>
                            @endif
                            @can('visits.delete')
                                <form method="POST" action="{{ route('visits.destroy', $visit) }}"
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
                        <x-empty-state message="No visits found." icon="stethoscope"
                                       :actionLabel="auth()->user()->can('visits.create') ? 'New Visit' : null"
                                       :actionHref="auth()->user()->can('visits.create') ? route('visits.create') : null" />
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700">
            <x-pagination :paginator="$visits" />
        </div>
    </div>
</x-app-layout>
