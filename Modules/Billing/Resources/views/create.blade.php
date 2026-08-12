<x-app-layout>
    <x-page-header title="New Invoice" subtitle="Create a new invoice">
        <x-slot name="actions">
            <x-button href="{{ route('billing.index') }}" variant="secondary" icon="arrow-left">Back to Billing</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <form method="POST" action="{{ route('billing.store') }}" class="mt-6">
        @csrf
        @include('billing::partials.form', ['invoice' => null])
    </form>
</x-app-layout>
