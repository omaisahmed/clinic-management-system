<x-app-layout>
    <x-page-header title="Payments" subtitle="Payments received">
        <x-slot name="actions">
            @can('payments.create')
                <x-button href="{{ route('payments.create') }}" icon="banknotes">Record Payment</x-button>
            @endcan
        </x-slot>
    </x-page-header>

    <x-alerts />

    <!-- Filters -->
    <div class="card mb-4 p-4">
        <form method="GET" action="{{ route('payments.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="min-w-64 flex-1">
                <x-label for="q">Search</x-label>
                <x-input name="q" id="q" :value="request('q')" placeholder="Patient name, phone, reference..." />
            </div>
            <div class="w-44">
                <x-label for="method">Method</x-label>
                <x-select name="method" id="method" :options="$methods" :value="request('method')" placeholder="Any" />
            </div>
            <div class="w-40">
                <x-label for="date_from">From</x-label>
                <x-date-input name="date_from" id="date_from" :value="request('date_from')" />
            </div>
            <div class="w-40">
                <x-label for="date_to">To</x-label>
                <x-date-input name="date_to" id="date_to" :value="request('date_to')" />
            </div>
            <x-button type="submit" variant="secondary" icon="search">Filter</x-button>
            @if (request()->hasAny(['q', 'method', 'date_from', 'date_to']))
                <x-button href="{{ route('payments.index') }}" variant="ghost" icon="x-mark">Clear</x-button>
            @endif
        </form>
    </div>

    <div class="card overflow-hidden">
        <x-table :headers="['Date', 'Patient', 'Invoice', 'Method', 'Amount', '']">
            @forelse ($payments as $payment)
                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/40">
                    <td class="td">{{ $payment->payment_date->format('M d, Y') }}</td>
                    <td class="td">
                        @if ($payment->patient)
                            <a href="{{ route('patients.show', $payment->patient) }}" class="font-medium text-slate-900 hover:underline dark:text-white">
                                {{ $payment->patient->full_name }}
                            </a>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="td">
                        @if (class_exists(\Modules\Billing\Models\Invoice::class) && $payment->invoice && Route::has('billing.show'))
                            <a href="{{ route('billing.show', $payment->invoice) }}" class="font-medium text-slate-900 hover:underline dark:text-white">
                                {{ $payment->invoice->invoice_number }}
                            </a>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="td">{{ $payment->method?->label() ?: '—' }}</td>
                    <td class="td">
                        <span class="font-semibold text-slate-900 dark:text-white">{{ money($payment->amount) }}</span>
                    </td>
                    <td class="td">
                        <div class="flex items-center justify-end gap-1">
                            <x-button :href="route('payments.show', $payment)" variant="ghost" size="sm" icon="eye">View</x-button>
                            @can('payments.update')
                                <x-button :href="route('payments.edit', $payment)" variant="ghost" size="sm" icon="pencil">Edit</x-button>
                            @endcan
                            @can('payments.delete')
                                <form method="POST" action="{{ route('payments.destroy', $payment) }}"
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
                    <td colspan="6">
                        <x-empty-state message="No payments found." icon="banknotes"
                                       :actionLabel="auth()->user()->can('payments.create') ? 'Record Payment' : null"
                                       :actionHref="auth()->user()->can('payments.create') ? route('payments.create') : null" />
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700">
            <x-pagination :paginator="$payments" />
        </div>
    </div>
</x-app-layout>
