<x-app-layout>
    <x-page-header title="Record Payment" subtitle="Record a payment received from a patient">
        <x-slot name="actions">
            <x-button href="{{ route('payments.index') }}" variant="secondary" icon="arrow-left">Back to Payments</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <form method="POST" action="{{ route('payments.store') }}" class="mt-6">
        @csrf
        @include('payments::partials.form', ['payment' => null])
    </form>
</x-app-layout>
