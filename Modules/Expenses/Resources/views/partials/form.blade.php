@php
    $expense = $expense ?? null;
    $categoryValue = old('category_id', $expense?->category_id ?? '');
@endphp

<div class="mx-auto mt-6 max-w-2xl">
    <div class="card p-6">
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <x-label for="category_id">Category</x-label>
                <x-select name="category_id" id="category_id" :options="$categories" :value="$categoryValue" placeholder="Uncategorized" />
            </div>
            <div>
                <x-label for="description" :required="true">Description</x-label>
                <x-input name="description" id="description" :required="true" :value="old('description', $expense?->description)" placeholder="e.g. Medical supplies" />
            </div>
            <div>
                <x-label for="amount" :required="true">Amount</x-label>
                <x-input type="number" step="0.01" min="0" name="amount" id="amount" :required="true" :value="old('amount', $expense?->amount)" placeholder="0.00" />
            </div>
            <div>
                <x-label for="expense_date" :required="true">Expense Date</x-label>
                <x-date-input name="expense_date" id="expense_date" :value="old('expense_date', $expense?->expense_date?->format('Y-m-d'))" />
            </div>
            <div>
                <x-label for="payment_method" :required="true">Payment Method</x-label>
                <x-select name="payment_method" id="payment_method" :options="$methods" :value="old('payment_method', $expense?->payment_method?->value)" placeholder="Select method" />
            </div>
            <div class="sm:col-span-2">
                <x-label for="notes">Notes</x-label>
                <x-textarea name="notes" id="notes" rows="3" placeholder="Additional notes">{{ old('notes', $expense?->notes) }}</x-textarea>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-3">
            <x-button :href="route('expenses.index')" variant="ghost">Cancel</x-button>
            <x-button type="submit" icon="check">
                {{ $expense ? 'Save Changes' : 'Record Expense' }}
            </x-button>
        </div>
    </div>
</div>
