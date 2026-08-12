<div class="card p-6" x-data="{ open: false }">
    <div class="flex items-center justify-between">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Family History</h3>
        @can('medical_records.create')
            <x-button size="sm" variant="secondary" icon="plus" x-on:click="open = !open">Add Entry</x-button>
        @endcan
    </div>

    @if ($patient->familyHistories->isNotEmpty())
        <div class="mt-4 space-y-2">
            @foreach ($patient->familyHistories as $entry)
                <div class="flex items-start justify-between gap-4 rounded-lg border border-slate-200 px-4 py-3 dark:border-slate-700">
                    <div>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $entry->condition }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $entry->relation ? 'Relation: ' . $entry->relation : 'Family history' }}</p>
                    </div>
                    @can('medical_records.delete')
                        <form method="POST" action="{{ route('medical-records.destroy', [$patient, 'family', $entry->id]) }}" onsubmit="return confirm('Remove this entry?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-slate-400 transition hover:text-red-600" title="Remove">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </form>
                    @endcan
                </div>
            @endforeach
        </div>
    @else
        <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">No family history recorded.</p>
    @endif

    <template x-if="open">
        <form method="POST" action="{{ route('medical-records.store', [$patient, 'family']) }}" class="mt-4 rounded-lg bg-slate-50 p-4 dark:bg-slate-800/60">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-label for="condition" :required="true">Condition</x-label>
                    <x-input name="condition" id="condition" required placeholder="e.g. Diabetes, Heart disease" />
                </div>
                <div>
                    <x-label for="relation">Relation</x-label>
                    <x-input name="relation" id="relation" placeholder="e.g. Father, Mother" />
                </div>
                <div class="sm:col-span-2 flex justify-end">
                    <x-button type="submit" size="sm" icon="check">Save Entry</x-button>
                </div>
            </div>
        </form>
    </template>
</div>
