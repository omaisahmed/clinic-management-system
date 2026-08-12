@php
    $social = $patient->socialHistory;
@endphp

<div class="card p-6" x-data="{ open: false }">
    <div class="flex items-center justify-between">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Social History</h3>
        @can('medical_records.create')
            <x-button size="sm" variant="secondary" icon="pencil" x-on:click="open = !open">Edit</x-button>
        @endcan
    </div>

    @if ($social)
        <div class="mt-4 grid gap-4 sm:grid-cols-3">
            <div class="rounded-lg border border-slate-200 px-4 py-3 dark:border-slate-700">
                <p class="text-xs uppercase tracking-wide text-slate-400">Smoking</p>
                <p class="mt-1 text-sm font-medium text-slate-900 dark:text-white">{{ $social->smoking ? 'Yes' : 'No' }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 px-4 py-3 dark:border-slate-700">
                <p class="text-xs uppercase tracking-wide text-slate-400">Alcohol</p>
                <p class="mt-1 text-sm font-medium text-slate-900 dark:text-white">{{ $social->alcohol ? 'Yes' : 'No' }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 px-4 py-3 dark:border-slate-700">
                <p class="text-xs uppercase tracking-wide text-slate-400">Occupation</p>
                <p class="mt-1 text-sm font-medium text-slate-900 dark:text-white">{{ $social->occupation ?: '—' }}</p>
            </div>
        </div>
        @if ($social->notes)
            <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">{{ $social->notes }}</p>
        @endif
    @else
        <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">No social history recorded.</p>
    @endif

    <template x-if="open">
        <form method="POST" action="{{ route('medical-records.store', [$patient, 'social']) }}" class="mt-4 rounded-lg bg-slate-50 p-4 dark:bg-slate-800/60">
            @csrf
            <div class="grid gap-4 sm:grid-cols-3">
                <x-checkbox name="smoking" value="1" :checked="$social?->smoking" label="Smoking" />
                <x-checkbox name="alcohol" value="1" :checked="$social?->alcohol" label="Alcohol" />
                <div>
                    <x-label for="occupation">Occupation</x-label>
                    <x-input name="occupation" id="occupation" :value="$social?->occupation" placeholder="Occupation" />
                </div>
                <div class="sm:col-span-3 flex justify-end">
                    <x-button type="submit" size="sm" icon="check">Save Social History</x-button>
                </div>
            </div>
        </form>
    </template>
</div>
