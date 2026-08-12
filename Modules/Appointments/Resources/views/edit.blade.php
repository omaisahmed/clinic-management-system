<x-app-layout>
    <x-page-header title="Edit Appointment" :subtitle="$appointment->patient?->full_name . ' · ' . $appointment->appointment_type->label()">
        <x-slot name="actions">
            <x-button :href="route('appointments.show', $appointment)" variant="secondary" icon="arrow-left">Back to Appointment</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="mt-6">
        @csrf
        @method('PUT')
        @include('appointments::partials.form')
    </form>
</x-app-layout>
