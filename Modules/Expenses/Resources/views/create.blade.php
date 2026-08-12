<x-app-layout>
    <x-page-header title="Record Expense" subtitle="Track clinic spending">
        <x-slot name="actions">
            <x-button href="{{ route('expenses.index') }}" variant="secondary" icon="arrow-left">Back to Expenses</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <form method="POST" action="{{ route('expenses.store') }}">
        @csrf
        @include('expenses::partials.form', ['expense' => null])
    </form>
</x-app-layout>
