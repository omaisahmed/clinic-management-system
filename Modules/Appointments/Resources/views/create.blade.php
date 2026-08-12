<x-app-layout>
    <x-page-header title="New Appointment" subtitle="Schedule a patient appointment">
        <x-slot name="actions">
            <x-button href="{{ route('appointments.index') }}" variant="secondary" icon="arrow-left">Back to Appointments</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <form method="POST" action="{{ route('appointments.store') }}" class="mt-6">
        @csrf
        @include('appointments::partials.form', ['appointment' => null])
    </form>
</x-app-layout>
