<x-app-layout>
    <x-page-header title="{{ $expense->description }}" :subtitle="money($expense->amount)">
        <x-slot name="actions">
            @can('expenses.update')
                <x-button :href="route('expenses.edit', $expense)" variant="secondary" icon="pencil">Edit</x-button>
            @endcan
            <x-button :href="route('expenses.index')" variant="ghost" icon="arrow-left">Back</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <!-- Details -->
    <div class="mt-6 card p-6">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Details</h3>
        <dl class="mt-4 grid gap-4 sm:grid-cols-2">
            <x-detail label="Description">{{ $expense->description }}</x-detail>
            <x-detail label="Category">{{ $expense->category?->name ?: '—' }}</x-detail>
            <x-detail label="Amount">{{ money($expense->amount) }}</x-detail>
            <x-detail label="Method">{{ $expense->payment_method?->label() ?: '—' }}</x-detail>
            <x-detail label="Expense Date">{{ $expense->expense_date?->format('M d, Y') ?: '—' }}</x-detail>
            <x-detail label="Recorded By">{{ $expense->recorder?->name ?: '—' }}</x-detail>
            <x-detail label="Notes">{{ $expense->notes ?: '—' }}</x-detail>
        </dl>
    </div>
</x-app-layout>
