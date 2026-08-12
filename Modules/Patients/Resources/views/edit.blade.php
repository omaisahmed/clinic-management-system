<x-app-layout>
    <x-page-header title="Edit Patient" :subtitle="$patient->full_name . ' · ' . $patient->patient_number">
        <x-slot name="actions">
            <x-button :href="route('patients.show', $patient)" variant="secondary" icon="arrow-left">Back to Profile</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <form method="POST" action="{{ route('patients.update', $patient) }}" enctype="multipart/form-data" class="mt-6">
        @csrf
        @method('PUT')
        @include('patients::partials.form')
    </form>
</x-app-layout>
