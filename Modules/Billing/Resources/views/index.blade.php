<x-app-layout>
    <x-page-header title="Billing" subtitle="Invoices and receivables">
        <x-slot name="actions">
            @can('billing.create')
                <x-button href="{{ route('billing.create') }}" icon="receipt">New Invoice</x-button>
            @endcan
        </x-slot>
    </x-page-header>

    <x-alerts />

    <div class="mb-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Total Outstanding" :value="money($outstanding)" icon="wallet" tone="amber" />
        <x-stat-card label="Total Invoices" :value="$totalInvoices" icon="receipt" tone="primary" />
    </div>

    <!-- Filters -->
    <div class="card mb-4 p-4">
        <form method="GET" action="{{ route('billing.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="min-w-64 flex-1">
                <x-label for="q">Search</x-label>
                <x-input name="q" id="q" :value="request('q')" placeholder="Patient name, phone, invoice number..." />
            </div>
            <div class="w-40">
                <x-label for="status">Status</x-label>
                <x-select name="status" id="status" :options="$statuses" :value="request('status')" placeholder="Any" />
            </div>
            <x-button type="submit" variant="secondary" icon="search">Filter</x-button>
            @if (request()->hasAny(['q', 'status']))
                <x-button href="{{ route('billing.index') }}" variant="ghost" icon="x-mark">Clear</x-button>
            @endif
        </form>
    </div>

    <div class="card overflow-hidden">
        <x-table :headers="['Invoice', 'Patient', 'Date', 'Total', 'Paid', 'Status', '']">
            @forelse ($invoices as $invoice)
                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/40">
                    <td class="td">
                        <a href="{{ route('billing.show', $invoice) }}" class="font-medium text-slate-900 hover:underline dark:text-white">
                            <x-badge variant="primary">{{ $invoice->invoice_number }}</x-badge>
                        </a>
                    </td>
                    <td class="td">
                        @if ($invoice->patient)
                            @can('patients.view')
                                <a href="{{ route('patients.show', $invoice->patient) }}" class="font-medium text-slate-900 hover:underline dark:text-white">
                                    {{ $invoice->patient->full_name }}
                                </a>
                            @else
                                <span class="font-medium text-slate-900 dark:text-white">{{ $invoice->patient->full_name }}</span>
                            @endcan
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="td">{{ $invoice->issue_date?->format('M d, Y') ?: '—' }}</td>
                    <td class="td">{{ money($invoice->total) }}</td>
                    <td class="td font-medium text-green-600 dark:text-green-400">{{ money($invoice->paid_amount) }}</td>
                    <td class="td">
                        <x-badge variant="{{ $invoice->status?->color() ?? 'gray' }}">{{ $invoice->status?->label() ?? '—' }}</x-badge>
                    </td>
                    <td class="td">
                        <div class="flex items-center justify-end gap-1">
                            <x-button :href="route('billing.show', $invoice)" variant="ghost" size="sm" icon="eye">View</x-button>
                            @can('billing.update')
                                <x-button :href="route('billing.edit', $invoice)" variant="ghost" size="sm" icon="pencil">Edit</x-button>
                            @endcan
                            @can('billing.delete')
                                <form method="POST" action="{{ route('billing.destroy', $invoice) }}"
                                      x-data x-on:submit.prevent="$dispatch('confirm-submit')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="submit" variant="ghost" size="sm" icon="trash" class="text-red-600">Delete</x-button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <x-empty-state message="No invoices found." icon="receipt"
                                       :actionLabel="auth()->user()->can('billing.create') ? 'New Invoice' : null"
                                       :actionHref="auth()->user()->can('billing.create') ? route('billing.create') : null" />
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700">
            <x-pagination :paginator="$invoices" />
        </div>
    </div>
</x-app-layout>
