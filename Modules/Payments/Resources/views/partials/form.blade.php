@php
    $payment = $payment ?? null;
    $patientValue = old('patient_id', $payment?->patient_id ?? request('patient') ?? '');
    $invoiceValue = old('invoice_id', $payment?->invoice_id ?? request('invoice') ?? '');
@endphp

<div class="mx-auto mt-6 max-w-2xl">
    <div class="card p-6">
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <x-label for="patient_id" :required="true">Patient</x-label>
                <x-select name="patient_id" id="patient_id" :options="$patients" :value="$patientValue" placeholder="Select patient" />
            </div>
            <div>
                <x-label for="invoice_id">Invoice</x-label>
                <x-select name="invoice_id" id="invoice_id" :options="$invoices" :value="$invoiceValue" placeholder="No invoice" />
            </div>
            <div>
                <x-label for="amount" :required="true">Amount</x-label>
                <x-input type="number" step="0.01" min="0" name="amount" id="amount" :required="true" :value="old('amount', $payment?->amount)" placeholder="0.00" />
            </div>
            <div>
                <x-label for="method" :required="true">Method</x-label>
                <x-select name="method" id="method" :options="$methods" :value="old('method', $payment?->method?->value)" placeholder="Select method" />
            </div>
            <div>
                <x-label for="reference">Reference</x-label>
                <x-input name="reference" id="reference" :value="old('reference', $payment?->reference)" placeholder="Receipt / transaction number" />
            </div>
            <div>
                <x-label for="payment_date" :required="true">Payment Date</x-label>
                <x-date-input name="payment_date" id="payment_date" :value="old('payment_date', $payment?->payment_date?->format('Y-m-d'))" />
            </div>
            <div class="sm:col-span-2">
                <x-label for="notes">Notes</x-label>
                <x-textarea name="notes" id="notes" rows="3" placeholder="Additional notes">{{ old('notes', $payment?->notes) }}</x-textarea>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-3">
            <x-button :href="route('payments.index')" variant="ghost">Cancel</x-button>
            <x-button type="submit" icon="check">
                {{ $payment ? 'Save Changes' : 'Record Payment' }}
            </x-button>
        </div>
    </div>
</div>
