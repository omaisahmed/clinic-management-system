<x-app-layout>
    <x-page-header :title="$test->test_name" :subtitle="$test->patient?->full_name ?: 'No patient assigned'">
        <x-slot name="actions">
            @can('lab_tests.manage_results')
                <x-button :href="route('lab_tests.result', $test)" variant="secondary" icon="beaker">
                    {{ $test->result ? 'Edit Result' : 'Enter Result' }}
                </x-button>
            @endcan
            @can('lab_tests.update')
                <x-button :href="route('lab_tests.edit', $test)" variant="secondary" icon="pencil">Edit</x-button>
            @endcan
            <x-button href="{{ route('lab_tests.index') }}" variant="ghost" icon="arrow-left">Back</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="card p-6">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Test Details</h3>
                <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                    <x-detail label="Patient">
                        @if ($test->patient)
                            <a href="{{ route('patients.show', $test->patient) }}" class="text-[var(--color-primary)] hover:underline">
                                {{ $test->patient->full_name }}
                            </a>
                        @else
                            —
                        @endif
                    </x-detail>
                    <x-detail label="Test">{{ $test->test_name }}</x-detail>
                    <x-detail label="Category">{{ $test->category ?: '—' }}</x-detail>
                    <x-detail label="Sample">{{ $test->sample_type ?: '—' }}</x-detail>
                    <x-detail label="Collection Date">{{ $test->collection_date?->format('M d, Y') ?: '—' }}</x-detail>
                    <x-detail label="Price">{{ $test->price !== null ? money($test->price) : '—' }}</x-detail>
                    <x-detail label="Status">
                        <x-badge :variant="$test->status->color()">{{ $test->status->label() }}</x-badge>
                    </x-detail>
                    <x-detail label="Requested">{{ $test->created_at->format('M d, Y') }}</x-detail>
                </dl>
            </div>

            @if ($test->visit)
                <div class="card p-6">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">Visit</h3>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                        <a href="{{ route('visits.show', $test->visit) }}" class="text-[var(--color-primary)] hover:underline">
                            {{ $test->visit->visit_number }}
                        </a>
                        · {{ $test->visit->visit_date?->format('M d, Y') }}
                    </p>
                </div>
            @endif

            @if ($test->notes)
                <div class="card p-6">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">Notes</h3>
                    <p class="mt-3 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $test->notes }}</p>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Result</h3>
                <div class="mt-4">
                    @if ($test->result)
                        <p class="whitespace-pre-line text-sm text-slate-700 dark:text-slate-200">{{ $test->result }}</p>
                        @if ($test->result_date)
                            <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                                Reported {{ $test->result_date->format('M d, Y') }}
                            </p>
                        @endif
                    @else
                        <p class="text-sm text-slate-400">—</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">No result recorded yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
