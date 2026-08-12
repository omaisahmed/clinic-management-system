<div class="mx-auto max-w-4xl space-y-6">
    <div class="card p-6">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Test Details</h3>
        <div class="mt-5 grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <x-label for="patient_id" :required="true">Patient</x-label>
                <x-select name="patient_id" id="patient_id" :options="$patients" :value="old('patient_id', $test->patient_id ?? request('patient'))" placeholder="Select patient" />
            </div>
            <div>
                <x-label for="visit_id">Visit</x-label>
                <x-select name="visit_id" id="visit_id" :options="$visits" :value="old('visit_id', $test->visit_id ?? '')" placeholder="None" />
            </div>
            <div>
                <x-label for="test_name" :required="true">Test Name</x-label>
                <x-input name="test_name" id="test_name" :required="true" placeholder="Complete Blood Count, X-Ray..." :value="old('test_name', $test->test_name ?? '')" />
            </div>
            <div>
                <x-label for="category">Category</x-label>
                <x-input name="category" id="category" placeholder="Hematology, Radiology..." :value="old('category', $test->category ?? '')" />
            </div>
            <div>
                <x-label for="sample_type">Sample Type</x-label>
                <x-input name="sample_type" id="sample_type" placeholder="Blood, Urine, Swab..." :value="old('sample_type', $test->sample_type ?? '')" />
            </div>
            <div>
                <x-label for="price">Price</x-label>
                <x-input type="number" step="0.01" min="0" name="price" id="price" placeholder="0.00" :value="old('price', $test->price ?? '')" />
            </div>
            @if (isset($test) && $test !== null)
                <div>
                    <x-label for="status">Status</x-label>
                    <x-select name="status" id="status" :options="$statuses" :value="old('status', $test->status?->value ?? '')" placeholder="Select status" />
                </div>
            @endif
            <div>
                <x-label for="collection_date">Collection Date</x-label>
                <x-date-input name="collection_date" id="collection_date" :value="old('collection_date', $test?->collection_date?->format('Y-m-d') ?? '')" />
            </div>
        </div>
    </div>

    @if (isset($test) && $test !== null)
        <div class="card p-6">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Result</h3>
            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-label for="result">Result</x-label>
                    <x-textarea name="result" id="result" rows="4" placeholder="Test findings / values...">{{ old('result', $test->result ?? '') }}</x-textarea>
                </div>
                <div>
                    <x-label for="result_date">Result Date</x-label>
                    <x-date-input name="result_date" id="result_date" :value="old('result_date', $test->result_date?->format('Y-m-d') ?? '')" />
                </div>
            </div>
        </div>
    @endif

    <div class="card p-6">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Notes</h3>
        <div class="mt-5 grid gap-5">
            <div>
                <x-label for="notes">Notes</x-label>
                <x-textarea name="notes" id="notes" rows="3" placeholder="Additional instructions or remarks">{{ old('notes', $test->notes ?? '') }}</x-textarea>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 pb-4">
        <x-button :href="isset($test) && $test !== null ? route('lab_tests.show', $test) : route('lab_tests.index')" variant="ghost">Cancel</x-button>
        <x-button type="submit" icon="check">
            {{ isset($test) && $test !== null ? 'Save Changes' : 'Create Lab Test' }}
        </x-button>
    </div>
</div>
