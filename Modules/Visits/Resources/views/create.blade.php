<x-app-layout>
    <x-page-header title="New Visit" subtitle="Start a consultation for a patient">
        <x-slot name="actions">
            <x-button href="{{ route('visits.index') }}" variant="secondary" icon="arrow-left">Back to Visits</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <form method="POST" action="{{ route('visits.store') }}" class="mt-6">
        @csrf
        @include('visits::partials.form', ['visit' => null])
    </form>
</x-app-layout>
