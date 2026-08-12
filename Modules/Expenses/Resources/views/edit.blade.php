<x-app-layout>
    <x-page-header title="Edit Expense" :subtitle="$expense->description">
        <x-slot name="actions">
            <x-button :href="route('expenses.show', $expense)" variant="secondary" icon="arrow-left">Back to Expense</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <form method="POST" action="{{ route('expenses.update', $expense) }}" class="mt-6">
        @csrf
        @method('PUT')
        @include('expenses::partials.form')
    </form>
</x-app-layout>
