<x-app-layout>
    <x-page-header :title="$appointment->patient?->full_name . ' · ' . $appointment->appointment_type->label()"
                   :subtitle="$appointment->appointment_date->format('M d, Y') . ' · ' . \Carbon\Carbon::parse($appointment->start_time)->format('h:i A')">
        <x-slot name="actions">
            @can('appointments.update')
                <x-button :href="route('appointments.edit', $appointment)" variant="secondary" icon="pencil">Edit</x-button>
            @endcan
            <x-button href="{{ route('appointments.index') }}" variant="ghost" icon="arrow-left">Back to Appointments</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <div class="card mt-6 p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Appointment Details</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Booking reference {{ $appointment->id }}</p>
            </div>
            <x-badge variant="{{ $appointment->status->color() }}">{{ $appointment->status->label() }}</x-badge>
        </div>

        <dl class="mt-6 grid gap-6 sm:grid-cols-2">
            <x-detail label="Patient">
                @if ($appointment->patient)
                    <a href="{{ route('patients.show', $appointment->patient) }}"
                       class="font-medium text-[var(--color-primary)] hover:underline">
                        {{ $appointment->patient->full_name }}
                    </a>
                @else
                    —
                @endif
            </x-detail>
            <x-detail label="Type">{{ $appointment->appointment_type->label() }}</x-detail>
            <x-detail label="Status">
                <x-badge variant="{{ $appointment->status->color() }}">{{ $appointment->status->label() }}</x-badge>
            </x-detail>
            <x-detail label="Date & Time">{{ $appointment->appointment_date->format('M d, Y') }} · {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }}</x-detail>
            <x-detail label="Duration">{{ $appointment->duration }} mins</x-detail>
            <x-detail label="Doctor">{{ $appointment->doctor?->name ?: '—' }}</x-detail>
            <x-detail label="Reason">{{ $appointment->reason ?: '—' }}</x-detail>
            <div class="sm:col-span-2">
                <x-detail label="Notes">{{ $appointment->notes ?: '—' }}</x-detail>
            </div>
        </dl>
    </div>
</x-app-layout>
