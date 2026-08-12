@php
    $statuses = \Modules\MedicalRecords\Enums\ConditionStatus::choices();
@endphp

<div class="card p-6" x-data="{ open: false }">
    <div class="flex items-center justify-between">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Conditions</h3>
        @can('medical_records.create')
            <x-button size="sm" variant="secondary" icon="plus" x-on:click="open = !open">Add Condition</x-button>
        @endcan
    </div>

    @if ($patient->conditions->isNotEmpty())
        <div class="mt-4 space-y-2">
            @foreach ($patient->conditions as $condition)
                <div class="flex items-start justify-between gap-4 rounded-lg border border-slate-200 px-4 py-3 dark:border-slate-700">
                    <div class="flex items-center gap-3">
                        <x-badge :variant="\Modules\MedicalRecords\Enums\ConditionStatus::tryFrom($condition->status)?->color() ?? 'slate'">
                            {{ ucfirst($condition->status) }}
                        </x-badge>
                        <div>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $condition->condition }}</p>
                            @if ($condition->diagnosis_date)
                                <p class="text-xs text-slate-500 dark:text-slate-400">Diagnosed {{ $condition->diagnosis_date->format('M d, Y') }}</p>
                            @endif
                        </div>
                    </div>
                    @can('medical_records.delete')
                        <form method="POST" action="{{ route('medical-records.destroy', [$patient, 'conditions', $condition->id]) }}" onsubmit="return confirm('Remove this condition?')">
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
        <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">No chronic conditions recorded.</p>
    @endif

    <template x-if="open">
        <form method="POST" action="{{ route('medical-records.store', [$patient, 'conditions']) }}" class="mt-4 rounded-lg bg-slate-50 p-4 dark:bg-slate-800/60">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-label for="condition" :required="true">Condition</x-label>
                    <x-input name="condition" id="condition" required placeholder="e.g. Hypertension" />
                </div>
                <div>
                    <x-label for="diagnosis_date">Diagnosis Date</x-label>
                    <x-date-input name="diagnosis_date" id="diagnosis_date" />
                </div>
                <div>
                    <x-label for="status">Status</x-label>
                    <x-select name="status" id="status" :options="$statuses" value="active" />
                </div>
                <div class="flex items-end justify-end">
                    <x-button type="submit" size="sm" icon="check">Save Condition</x-button>
                </div>
            </div>
        </form>
    </template>
</div>
