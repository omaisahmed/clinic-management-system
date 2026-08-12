<x-app-layout>
    <x-page-header title="Payment" :subtitle="money($payment->amount) . ' · ' . $payment->patient?->full_name">
        <x-slot name="actions">
            @can('payments.update')
                <x-button :href="route('payments.edit', $payment)" variant="secondary" icon="pencil">Edit</x-button>
            @endcan
            <x-button :href="route('payments.index')" variant="ghost" icon="arrow-left">Back</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <!-- Amount header -->
    <div class="card overflow-hidden">
        <div class="bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-secondary)] px-6 py-8 text-center">
            <p class="text-xs uppercase tracking-wide text-white/70">Amount Received</p>
            <p class="mt-1 text-3xl font-bold text-white">{{ money($payment->amount) }}</p>
            <p class="mt-1 text-sm text-white/80">{{ $payment->patient?->full_name ?: 'Unknown patient' }}</p>
        </div>
    </div>

    <!-- Details -->
    <div class="mt-6 card p-6">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Details</h3>
        <dl class="mt-4 grid gap-4 sm:grid-cols-2">
            <x-detail label="Patient">
                @if ($payment->patient)
                    <a href="{{ route('patients.show', $payment->patient) }}" class="text-[var(--color-primary)] hover:underline">
                        {{ $payment->patient->full_name }}
                    </a>
                @else
                    —
                @endif
            </x-detail>
            <x-detail label="Invoice">
                @if (class_exists(\Modules\Billing\Models\Invoice::class) && $payment->invoice)
                    {{ $payment->invoice->invoice_number }}
                @else
                    —
                @endif
            </x-detail>
            <x-detail label="Amount">{{ money($payment->amount) }}</x-detail>
            <x-detail label="Method">{{ $payment->method?->label() ?: '—' }}</x-detail>
            <x-detail label="Reference">{{ $payment->reference ?: '—' }}</x-detail>
            <x-detail label="Payment Date">{{ $payment->payment_date?->format('M d, Y') ?: '—' }}</x-detail>
            <x-detail label="Notes">{{ $payment->notes ?: '—' }}</x-detail>
        </dl>
    </div>
</x-app-layout>
