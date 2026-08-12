<div class="card p-6" x-data="{ open: false }">
    <div class="flex items-center justify-between">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Surgeries</h3>
        @can('medical_records.create')
            <x-button size="sm" variant="secondary" icon="plus" x-on:click="open = !open">Add Surgery</x-button>
        @endcan
    </div>

    @if ($patient->surgeries->isNotEmpty())
        <div class="mt-4 space-y-2">
            @foreach ($patient->surgeries as $surgery)
                <div class="flex items-start justify-between gap-4 rounded-lg border border-slate-200 px-4 py-3 dark:border-slate-700">
                    <div>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $surgery->surgery }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $surgery->date?->format('M d, Y') ?: 'Date unknown' }}</p>
                    </div>
                    @can('medical_records.delete')
                        <form method="POST" action="{{ route('medical-records.destroy', [$patient, 'surgeries', $surgery->id]) }}" onsubmit="return confirm('Remove this surgery?')">
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
        <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">No surgeries recorded.</p>
    @endif

    <template x-if="open">
        <form method="POST" action="{{ route('medical-records.store', [$patient, 'surgeries']) }}" class="mt-4 rounded-lg bg-slate-50 p-4 dark:bg-slate-800/60">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-label for="surgery" :required="true">Surgery</x-label>
                    <x-input name="surgery" id="surgery" required placeholder="e.g. Appendectomy" />
                </div>
                <div>
                    <x-label for="date">Date</x-label>
                    <x-date-input name="date" id="date" />
                </div>
                <div class="sm:col-span-2 flex justify-end">
                    <x-button type="submit" size="sm" icon="check">Save Surgery</x-button>
                </div>
            </div>
        </form>
    </template>
</div>
