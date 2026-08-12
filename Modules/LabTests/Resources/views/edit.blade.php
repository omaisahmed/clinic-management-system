<x-app-layout>
    <x-page-header title="Edit Lab Test" :subtitle="$test->test_name">
        <x-slot name="actions">
            <x-button :href="route('lab_tests.show', $test)" variant="secondary" icon="arrow-left">Back to Test</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <form method="POST" action="{{ route('lab_tests.update', $test) }}" class="mt-6">
        @csrf
        @method('PUT')
        @include('lab_tests::partials.form')
    </form>
</x-app-layout>
