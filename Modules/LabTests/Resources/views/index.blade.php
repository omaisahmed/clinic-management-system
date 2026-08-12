<x-app-layout>
    <x-page-header title="Lab Tests" subtitle="Request and manage laboratory tests">
        <x-slot name="actions">
            @can('lab_tests.create')
                <x-button href="{{ route('lab_tests.create') }}" icon="beaker">New Lab Test</x-button>
            @endcan
        </x-slot>
    </x-page-header>

    <x-alerts />

    <!-- Filters -->
    <div class="card mb-4 p-4">
        <form method="GET" action="{{ route('lab_tests.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="min-w-64 flex-1">
                <x-label for="q">Search</x-label>
                <x-input name="q" id="q" :value="request('q')" placeholder="Test name, category, patient, phone..." />
            </div>
            <div class="w-44">
                <x-label for="status">Status</x-label>
                <x-select name="status" id="status" :options="$statuses" :value="request('status')" placeholder="Any" />
            </div>
            <div class="w-44">
                <x-label for="category">Category</x-label>
                <x-select name="category" id="category" :options="$categories" :value="request('category')" placeholder="Any" />
            </div>
            <x-button type="submit" variant="secondary" icon="search">Search</x-button>
            @if (request()->hasAny(['q', 'status', 'category']))
                <x-button href="{{ route('lab_tests.index') }}" variant="ghost" icon="x-mark">Clear</x-button>
            @endif
        </form>
    </div>

    <div class="card overflow-hidden">
        <x-table :headers="['Test', 'Patient', 'Category', 'Status', 'Result', 'Date', '']">
            @forelse ($tests as $test)
                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/40">
                    <td class="td">
                        <a href="{{ route('lab_tests.show', $test) }}" class="font-medium text-slate-900 hover:underline dark:text-white">
                            {{ $test->test_name }}
                        </a>
                    </td>
                    <td class="td">
                        @if ($test->patient)
                            <a href="{{ route('patients.show', $test->patient) }}" class="text-slate-700 hover:underline dark:text-slate-200">
                                {{ $test->patient->full_name }}
                            </a>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="td text-slate-500">{{ $test->category ?: '—' }}</td>
                    <td class="td">
                        <x-badge :variant="$test->status->color()">{{ $test->status->label() }}</x-badge>
                    </td>
                    <td class="td">
                        @if ($test->result)
                            <span class="block max-w-48 truncate text-sm text-slate-700 dark:text-slate-200">{{ $test->result }}</span>
                        @elseif ($test->status === \Modules\LabTests\Enums\LabTestStatus::Completed)
                            <span class="text-xs font-medium text-green-600 dark:text-green-400">Recorded</span>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="td text-slate-500">{{ $test->created_at->format('M d, Y') }}</td>
                    <td class="td">
                        <div class="flex items-center justify-end gap-1">
                            <x-button :href="route('lab_tests.show', $test)" variant="ghost" size="sm" icon="eye">View</x-button>
                            @can('lab_tests.update')
                                <x-button :href="route('lab_tests.edit', $test)" variant="ghost" size="sm" icon="pencil">Edit</x-button>
                            @endcan
                            @can('lab_tests.manage_results')
                                <x-button :href="route('lab_tests.result', $test)" variant="ghost" size="sm" icon="beaker">Result</x-button>
                            @endcan
                            @can('lab_tests.delete')
                                <form method="POST" action="{{ route('lab_tests.destroy', $test) }}"
                                      x-data x-on:submit.prevent="$dispatch('confirm-submit')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="submit" variant="ghost" size="sm" icon="trash" class="text-red-600">Delete</x-button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <x-empty-state message="No lab tests found." icon="beaker"
                                       :actionLabel="auth()->user()->can('lab_tests.create') ? 'New Lab Test' : null"
                                       :actionHref="auth()->user()->can('lab_tests.create') ? route('lab_tests.create') : null" />
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700">
            <x-pagination :paginator="$tests" />
        </div>
    </div>
</x-app-layout>
