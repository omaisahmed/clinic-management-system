<div class="mx-auto max-w-4xl space-y-6">
    <div class="card p-6">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Invoice Details</h3>
        <div class="mt-5 grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <x-label for="patient_id" :required="true">Patient</x-label>
                <x-select name="patient_id" id="patient_id" :options="$patients" :value="old('patient_id', $invoice->patient_id ?? request('patient'))" placeholder="Select patient" />
            </div>
            <div>
                <x-label for="visit_id">Visit</x-label>
                <x-select name="visit_id" id="visit_id" :options="$visits" :value="old('visit_id', $invoice->visit_id ?? '')" placeholder="None" />
            </div>
            <div>
                <x-label for="issue_date" :required="true">Issue Date</x-label>
                <x-date-input name="issue_date" id="issue_date" :value="old('issue_date', $invoice?->issue_date?->format('Y-m-d') ?? now()->format('Y-m-d'))" />
            </div>
            <div>
                <x-label for="due_date">Due Date</x-label>
                <x-date-input name="due_date" id="due_date" :value="old('due_date', $invoice?->due_date?->format('Y-m-d') ?? '')" />
            </div>
            <div>
                <x-label for="discount">Discount</x-label>
                <x-input type="number" step="0.01" min="0" name="discount" id="discount" placeholder="0.00" :value="old('discount', $invoice?->discount ?? 0)" />
            </div>
            <div>
                <x-label for="tax">Tax</x-label>
                <x-input type="number" step="0.01" min="0" name="tax" id="tax" placeholder="0.00" :value="old('tax', $invoice?->tax ?? 0)" />
            </div>
            @if (isset($invoice) && $invoice !== null)
                <div>
                    <x-label for="status">Status</x-label>
                    <x-select name="status" id="status" :options="$statuses" :value="old('status', $invoice->status?->value ?? '')" placeholder="Select status" />
                </div>
            @endif
            <div class="sm:col-span-2">
                <x-label for="notes">Notes</x-label>
                <x-textarea name="notes" id="notes" rows="3" placeholder="Additional notes">{{ old('notes', $invoice->notes ?? '') }}</x-textarea>
            </div>
        </div>
    </div>

    <div class="card p-6">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Line Items</h3>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Add one or more line items to this invoice.</p>

        <div class="mt-5 space-y-4"
             x-data="{ items: @js(old('items', isset($invoice) ? $invoice->items->map(fn($i) => ['description'=>$i->description,'quantity'=>$i->quantity,'unit_price'=>$i->unit_price])->values()->all() : [['description'=>'','quantity'=>1,'unit_price'=>0]])) }">
            <template x-for="(item, i) in items" :key="i">
                <div class="grid gap-3 rounded-xl border border-slate-200 p-4 dark:border-slate-700 sm:grid-cols-12">
                    <div class="sm:col-span-6">
                        <input type="text" x-model="item.description" :name="'items['+i+'][description]'" placeholder="Description (e.g. Consultation fee)" class="input">
                    </div>
                    <div class="sm:col-span-2">
                        <input type="number" x-model="item.quantity" :name="'items['+i+'][quantity]'" min="1" placeholder="Qty" class="input">
                    </div>
                    <div class="sm:col-span-2">
                        <input type="number" step="0.01" x-model="item.unit_price" :name="'items['+i+'][unit_price]'" min="0" placeholder="Price" class="input">
                    </div>
                    <div class="flex items-center justify-end sm:col-span-2">
                        <x-button type="button" variant="ghost" size="sm" icon="trash" class="text-red-600" x-on:click="items.splice(i, 1)" x-show="items.length > 1">Remove</x-button>
                    </div>
                </div>
            </template>

            <x-button type="button" variant="secondary" icon="plus" x-on:click="items.push({ description: '', quantity: 1, unit_price: 0 })">Add Item</x-button>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 pb-4">
        <x-button href="{{ route('billing.index') }}" variant="ghost">Cancel</x-button>
        <x-button type="submit" icon="check">
            {{ isset($invoice) && $invoice !== null ? 'Save Changes' : 'Create Invoice' }}
        </x-button>
    </div>
</div>
