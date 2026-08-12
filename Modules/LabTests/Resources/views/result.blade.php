<x-app-layout>
    <x-page-header title="Enter Result" :subtitle="$test->test_name">
        <x-slot name="actions">
            <x-button :href="route('lab_tests.show', $test)" variant="secondary" icon="arrow-left">Back to Test</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <form method="POST" action="{{ route('lab_tests.result.update', $test) }}" class="mx-auto mt-6 max-w-2xl">
        @csrf
        @method('PUT')

        <div class="card p-6">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Result Details</h3>
            <div class="mt-5 grid gap-5">
                <div>
                    <x-label for="result" :required="true">Result</x-label>
                    <x-textarea name="result" id="result" rows="5" :required="true" placeholder="Findings, values, and remarks...">{{ old('result', $test->result ?? '') }}</x-textarea>
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <x-label for="status" :required="true">Status</x-label>
                        <x-select name="status" id="status" :options="$statuses" :value="old('status', $test->status?->value ?? '')" placeholder="Select status" />
                    </div>
                    <div>
                        <x-label for="result_date">Result Date</x-label>
                        <x-date-input name="result_date" id="result_date" :value="old('result_date', $test->result_date?->format('Y-m-d') ?? '')" />
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-3 pb-4">
            <x-button :href="route('lab_tests.show', $test)" variant="ghost">Cancel</x-button>
            <x-button type="submit" icon="check">Save Result</x-button>
        </div>
    </form>
</x-app-layout>
