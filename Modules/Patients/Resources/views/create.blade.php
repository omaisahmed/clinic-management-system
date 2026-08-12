<x-app-layout>
    <x-page-header title="Register Patient" subtitle="Create a new patient record">
        <x-slot name="actions">
            <x-button href="{{ route('patients.index') }}" variant="secondary" icon="arrow-left">Back to Patients</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <form method="POST" action="{{ route('patients.store') }}" enctype="multipart/form-data" class="mt-6">
        @csrf
        @include('patients::partials.form', ['patient' => null])
    </form>
</x-app-layout>
