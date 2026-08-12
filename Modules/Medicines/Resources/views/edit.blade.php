<x-app-layout>
    <x-page-header title="Edit Medicine" :subtitle="$medicine->name">
        <x-slot name="actions">
            <x-button :href="route('medicines.show', $medicine)" variant="secondary" icon="arrow-left">Back to Details</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <form method="POST" action="{{ route('medicines.update', $medicine) }}" class="mt-6">
        @csrf
        @method('PUT')
        @include('medicines::partials.form')
    </form>
</x-app-layout>
