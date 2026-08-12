<x-app-layout>
    <x-page-header title="Edit Invoice" :subtitle="$invoice->invoice_number">
        <x-slot name="actions">
            <x-button href="{{ route('billing.show', $invoice) }}" variant="secondary" icon="arrow-left">Back to Invoice</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <form method="POST" action="{{ route('billing.update', $invoice) }}" class="mt-6">
        @csrf
        @method('PUT')
        @include('billing::partials.form')
    </form>
</x-app-layout>
