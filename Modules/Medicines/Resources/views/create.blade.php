<x-app-layout>
    <x-page-header title="Add Medicine" subtitle="Add a new medicine to pharmacy inventory">
        <x-slot name="actions">
            <x-button href="{{ route('medicines.index') }}" variant="secondary" icon="arrow-left">Back to Medicines</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <form method="POST" action="{{ route('medicines.store') }}" class="mt-6">
        @csrf
        @include('medicines::partials.form', ['medicine' => null])
    </form>
</x-app-layout>
