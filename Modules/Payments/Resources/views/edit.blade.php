<x-app-layout>
    <x-page-header title="Edit Payment" :subtitle="money($payment->amount) . ' · ' . $payment->patient?->full_name">
        <x-slot name="actions">
            <x-button :href="route('payments.show', $payment)" variant="secondary" icon="arrow-left">Back to Payment</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <form method="POST" action="{{ route('payments.update', $payment) }}" class="mt-6">
        @csrf
        @method('PUT')
        @include('payments::partials.form')
    </form>
</x-app-layout>
