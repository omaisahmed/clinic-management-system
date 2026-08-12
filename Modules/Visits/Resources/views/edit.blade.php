<x-app-layout>
    <x-page-header title="Edit Visit" :subtitle="$visit->visit_number">
        <x-slot name="actions">
            <x-button href="{{ route('visits.show', $visit) }}" variant="secondary" icon="arrow-left">Back to Visit</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <form method="POST" action="{{ route('visits.update', $visit) }}" class="mt-6">
        @csrf
        @method('PUT')
        @include('visits::partials.form')
    </form>
</x-app-layout>
