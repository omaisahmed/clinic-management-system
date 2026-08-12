@php
    $severities = \Modules\MedicalRecords\Enums\AllergySeverity::choices();
@endphp

<div class="card p-6" x-data="{ open: false }">
    <div class="flex items-center justify-between">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Allergies</h3>
        @can('medical_records.create')
            <x-button size="sm" variant="secondary" icon="plus" x-on:click="open = !open">Add Allergy</x-button>
        @endcan
    </div>

    @if ($patient->allergies->isNotEmpty())
        <div class="mt-4 space-y-2">
            @foreach ($patient->allergies as $allergy)
                <div class="flex items-start justify-between gap-4 rounded-lg border border-slate-200 px-4 py-3 dark:border-slate-700">
                    <div class="flex items-center gap-3">
                        <x-badge :variant="\Modules\MedicalRecords\Enums\AllergySeverity::tryFrom($allergy->severity)?->color() ?? 'slate'">
                            {{ ucfirst($allergy->severity) }}
                        </x-badge>
                        <div>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $allergy->allergy }}</p>
                            @if ($allergy->reaction)
                                <p class="text-xs text-slate-500 dark:text-slate-400">Reaction: {{ $allergy->reaction }}</p>
                            @endif
                        </div>
                    </div>
                    @can('medical_records.delete')
                        <form method="POST" action="{{ route('medical-records.destroy', [$patient, 'allergies', $allergy->id]) }}" onsubmit="return confirm('Remove this allergy?')">
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
        <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">No known allergies recorded.</p>
    @endif

    <template x-if="open">
        <form method="POST" action="{{ route('medical-records.store', [$patient, 'allergies']) }}" class="mt-4 rounded-lg bg-slate-50 p-4 dark:bg-slate-800/60">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-label for="allergy" :required="true">Allergy</x-label>
                    <x-input name="allergy" id="allergy" required placeholder="e.g. Penicillin" />
                </div>
                <div>
                    <x-label for="severity">Severity</x-label>
                    <x-select name="severity" id="severity" :options="$severities" value="mild" />
                </div>
                <div class="sm:col-span-2">
                    <x-label for="reaction">Reaction</x-label>
                    <x-input name="reaction" id="reaction" placeholder="e.g. Hives, anaphylaxis" />
                </div>
                <div class="sm:col-span-2 flex justify-end">
                    <x-button type="submit" size="sm" icon="check">Save Allergy</x-button>
                </div>
            </div>
        </form>
    </template>
</div>
