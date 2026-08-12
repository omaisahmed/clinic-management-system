<x-app-layout>
    <x-page-header :title="$invoice->invoice_number . ' · ' . ($invoice->patient?->full_name ?? 'Invoice')" :subtitle="$invoice->issue_date?->format('M d, Y') ?: 'Issued'">
        <x-slot name="actions">
            @can('billing.update')
                <x-button :href="route('billing.edit', $invoice)" variant="secondary" icon="pencil">Edit</x-button>
            @endcan
            <x-button href="{{ route('billing.index') }}" variant="ghost" icon="arrow-left">Back to Billing</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="card p-6">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Line Items</h3>
                <div class="mt-4">
                    <x-table :headers="['Description', 'Qty', 'Unit Price', 'Amount']">
                        @forelse ($invoice->items as $item)
                            <tr class="align-top">
                                <td class="td font-medium text-slate-900 dark:text-white">{{ $item->description }}</td>
                                <td class="td">{{ $item->quantity }}</td>
                                <td class="td">{{ money($item->unit_price) }}</td>
                                <td class="td">{{ money($item->amount) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-empty-state message="No line items on this invoice." icon="receipt" />
                                </td>
                            </tr>
                        @endforelse
                    </x-table>
                </div>
            </div>

            @if ($invoice->notes)
                <div class="card p-6">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">Notes</h3>
                    <p class="mt-3 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $invoice->notes }}</p>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Invoice Details</h3>
                <dl class="mt-4 grid gap-4">
                    <x-detail label="Patient">
                        @if ($invoice->patient)
                            @can('patients.view')
                                <a href="{{ route('patients.show', $invoice->patient) }}" class="hover:underline">{{ $invoice->patient->full_name }}</a>
                            @else
                                {{ $invoice->patient->full_name }}
                            @endcan
                        @else
                            —
                        @endif
                    </x-detail>
                    <x-detail label="Invoice Number">{{ $invoice->invoice_number }}</x-detail>
                    <x-detail label="Issue Date">{{ $invoice->issue_date?->format('M d, Y') ?: '—' }}</x-detail>
                    <x-detail label="Due Date">{{ $invoice->due_date?->format('M d, Y') ?: '—' }}</x-detail>
                    <x-detail label="Status">
                        <x-badge variant="{{ $invoice->status?->color() ?? 'gray' }}">{{ $invoice->status?->label() ?? '—' }}</x-badge>
                    </x-detail>
                </dl>
            </div>

            <div class="card p-6">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Totals</h3>
                <dl class="mt-4 grid gap-4">
                    <x-detail label="Subtotal">{{ money($invoice->subtotal) }}</x-detail>
                    <x-detail label="Discount">{{ money($invoice->discount) }}</x-detail>
                    <x-detail label="Tax">{{ money($invoice->tax) }}</x-detail>
                    <x-detail label="Total">
                        <span class="font-semibold">{{ money($invoice->total) }}</span>
                    </x-detail>
                    <x-detail label="Paid">
                        <span class="text-green-600 dark:text-green-400">{{ money($invoice->paid_amount) }}</span>
                    </x-detail>
                    <x-detail label="Due">
                        @if ($invoice->due_amount > 0)
                            <span class="text-red-600 dark:text-red-400">{{ money($invoice->due_amount) }}</span>
                        @else
                            <span class="text-green-600 dark:text-green-400">{{ money($invoice->due_amount) }}</span>
                        @endif
                    </x-detail>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
