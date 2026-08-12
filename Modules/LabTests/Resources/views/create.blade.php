<x-app-layout>
    <x-page-header title="New Lab Test" subtitle="Request a laboratory test for a patient">
        <x-slot name="actions">
            <x-button href="{{ route('lab_tests.index') }}" variant="secondary" icon="arrow-left">Back to Lab Tests</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <form method="POST" action="{{ route('lab_tests.store') }}" class="mt-6">
        @csrf
        @include('lab_tests::partials.form', ['test' => null])
    </form>
</x-app-layout>
