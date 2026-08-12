<x-app-layout>
    <x-page-header :title="$visit->visit_number . ' · ' . ($visit->patient?->full_name ?? 'Patient')" :subtitle="$visit->visit_date?->format('M d, Y')">
        <x-slot name="actions">
            @if ($visit->status === \Modules\Visits\Enums\VisitStatus::InProgress)
                @can('visits.complete')
                    <form method="POST" action="{{ route('visits.complete', $visit) }}" class="inline">
                        @csrf
                        @method('PUT')
                        <x-button type="submit" icon="check">Mark Completed</x-button>
                    </form>
                @endcan
            @endif
            @can('visits.update')
                <x-button :href="route('visits.edit', $visit)" variant="secondary" icon="pencil">Edit</x-button>
            @endcan
            <x-button href="{{ route('visits.index') }}" variant="ghost" icon="arrow-left">Back to Visits</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="card p-6">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Chief Complaint</h3>
                <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">{{ $visit->chief_complaint ?: 'No chief complaint recorded.' }}</p>
            </div>

            <div class="card p-6">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Diagnosis</h3>
                <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">{{ $visit->diagnosis ?: 'No diagnosis recorded.' }}</p>
            </div>

            @if ($visit->notes)
                <div class="card p-6">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">Notes</h3>
                    <p class="mt-3 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $visit->notes }}</p>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Visit Information</h3>
                <dl class="mt-4 grid gap-4">
                    <x-detail label="Patient">
                        @if ($visit->patient)
                            @can('patients.view')
                                <a href="{{ route('patients.show', $visit->patient) }}" class="hover:underline">{{ $visit->patient->full_name }}</a>
                            @else
                                {{ $visit->patient->full_name }}
                            @endcan
                        @else
                            —
                        @endif
                    </x-detail>
                    <x-detail label="Phone">{{ $visit->patient?->phone ?: '—' }}</x-detail>
                    <x-detail label="Visit Number">{{ $visit->visit_number }}</x-detail>
                    <x-detail label="Date">{{ $visit->visit_date?->format('M d, Y') ?: '—' }}</x-detail>
                    <x-detail label="Status">
                        <x-badge variant="{{ $visit->status?->color() ?? 'gray' }}">{{ $visit->status?->label() ?? '—' }}</x-badge>
                    </x-detail>
                    <x-detail label="Doctor">{{ $visit->doctor?->name ?: '—' }}</x-detail>
                </dl>
            </div>

            <div class="card p-6">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Vitals</h3>
                <dl class="mt-4 grid grid-cols-2 gap-4">
                    <x-detail label="Temp">{{ $visit->temperature !== null ? $visit->temperature . ' °C' : '—' }}</x-detail>
                    <x-detail label="BP">{{ $visit->blood_pressure ?: '—' }}</x-detail>
                    <x-detail label="Heart Rate">{{ $visit->heart_rate !== null ? $visit->heart_rate . ' bpm' : '—' }}</x-detail>
                    <x-detail label="Resp. Rate">{{ $visit->respiratory_rate !== null ? $visit->respiratory_rate . ' /min' : '—' }}</x-detail>
                    <x-detail label="Weight">{{ $visit->weight !== null ? $visit->weight . ' kg' : '—' }}</x-detail>
                    <x-detail label="Height">{{ $visit->height !== null ? $visit->height . ' cm' : '—' }}</x-detail>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
