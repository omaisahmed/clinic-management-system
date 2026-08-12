<x-app-layout>
    <x-page-header title="New Prescription" subtitle="Create a new prescription">
        <x-slot name="actions">
            <x-button href="{{ route('prescriptions.index') }}" variant="secondary" icon="arrow-left">Back to Prescriptions</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <form method="POST" action="{{ route('prescriptions.store') }}" class="mt-6">
        @csrf
        @include('prescriptions::partials.form', ['prescription' => null])
    </form>
</x-app-layout>
