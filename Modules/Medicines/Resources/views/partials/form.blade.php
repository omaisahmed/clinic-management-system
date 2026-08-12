<div class="grid gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <!-- Basic info -->
        <div class="card p-6">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Basic Information</h3>
            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-label for="name" :required="true">Medicine Name</x-label>
                    <x-input name="name" id="name" :required="true" placeholder="Paracetamol 500mg" :value="old('name', $medicine->name ?? '')" />
                </div>
                <div>
                    <x-label for="generic_name">Generic Name</x-label>
                    <x-input name="generic_name" id="generic_name" placeholder="Acetaminophen" :value="old('generic_name', $medicine->generic_name ?? '')" />
                </div>
                <div>
                    <x-label for="brand">Brand</x-label>
                    <x-input name="brand" id="brand" placeholder="Panadol" :value="old('brand', $medicine->brand ?? '')" />
                </div>
                <div>
                    <x-label for="category">Category</x-label>
                    <x-input name="category" id="category" placeholder="Analgesic" :value="old('category', $medicine->category ?? '')" />
                </div>
                <div>
                    <x-label for="strength">Strength</x-label>
                    <x-input name="strength" id="strength" placeholder="500mg" :value="old('strength', $medicine->strength ?? '')" />
                </div>
                <div>
                    <x-label for="unit">Unit</x-label>
                    <x-input name="unit" id="unit" placeholder="tablet, bottle, ml..." :value="old('unit', $medicine->unit ?? '')" />
                </div>
                <div>
                    <x-label for="expiry_date">Expiry Date</x-label>
                    <x-date-input name="expiry_date" id="expiry_date" :value="old('expiry_date', $medicine?->expiry_date?->format('Y-m-d') ?? '')" />
                </div>
            </div>
        </div>

        <!-- Pricing & stock -->
        <div class="card p-6">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Pricing & Stock</h3>
            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div>
                    <x-label for="cost_price">Cost Price</x-label>
                    <x-input type="number" name="cost_price" id="cost_price" step="0.01" min="0" placeholder="0.00" :value="old('cost_price', $medicine->cost_price ?? '')" />
                </div>
                <div>
                    <x-label for="selling_price">Selling Price</x-label>
                    <x-input type="number" name="selling_price" id="selling_price" step="0.01" min="0" placeholder="0.00" :value="old('selling_price', $medicine->selling_price ?? '')" />
                </div>
                <div>
                    <x-label for="stock" :required="true">Stock Quantity</x-label>
                    <x-input type="number" name="stock" id="stock" min="0" step="1" :required="true" :value="old('stock', $medicine->stock ?? 0)" />
                </div>
                <div>
                    <x-label for="reorder_level" :required="true">Reorder Level</x-label>
                    <x-input type="number" name="reorder_level" id="reorder_level" min="0" step="1" :required="true" :value="old('reorder_level', $medicine->reorder_level ?? 10)" />
                </div>
                <div class="sm:col-span-2">
                    <x-label for="description">Description</x-label>
                    <x-textarea name="description" id="description" rows="3" placeholder="Usage notes, side effects, storage...">{{ old('description', $medicine->description ?? '') }}</x-textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pb-4">
            <x-button :href="route('medicines.index')" variant="ghost">Cancel</x-button>
            <x-button type="submit" icon="check">
                {{ isset($medicine) ? 'Save Changes' : 'Add Medicine' }}
            </x-button>
        </div>
    </div>
</div>
