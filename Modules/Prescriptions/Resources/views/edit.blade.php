<x-app-layout>
    <x-page-header title="Edit Prescription" :subtitle="$prescription->prescription_number">
        <x-slot name="actions">
            <x-button href="{{ route('prescriptions.show', $prescription) }}" variant="secondary" icon="arrow-left">Back to Prescription</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <form method="POST" action="{{ route('prescriptions.update', $prescription) }}" class="mt-6">
        @csrf
        @method('PUT')
        @include('prescriptions::partials.form')
    </form>
</x-app-layout>
