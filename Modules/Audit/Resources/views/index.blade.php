<x-app-layout>
    <x-page-header title="Audit Logs" subtitle="Track sensitive actions across the system" />

    <x-alerts />

    <!-- Filters -->
    <div class="card mb-4 p-4">
        <form method="GET" action="{{ route('audit.logs.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="w-48">
                <x-label for="action">Action</x-label>
                <x-input name="action" id="action" :value="request('action')" placeholder="e.g. Patient Created" />
            </div>
            <div class="w-40">
                <x-label for="module">Module</x-label>
                <x-input name="module" id="module" :value="request('module')" placeholder="e.g. patients" />
            </div>
            <div>
                <x-label for="date">Date</x-label>
                <x-date-input name="date" id="date" :value="request('date')" />
            </div>
            <x-button type="submit" variant="secondary" icon="filter">Filter</x-button>
            @if (request()->has('action') || request()->has('module') || request()->has('date'))
                <x-button href="{{ route('audit.logs.index') }}" variant="ghost" icon="x-mark">Clear</x-button>
            @endif
        </form>
    </div>

    <div class="card overflow-hidden">
        <x-table :headers="['Time', 'User', 'Action', 'Module', 'Details']">
            @forelse ($logs as $log)
                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/40">
                    <td class="td whitespace-nowrap text-slate-500">{{ $log->created_at->format('M d, H:i:s') }}</td>
                    <td class="td">
                        @if ($log->user)
                            <div class="flex items-center gap-2">
                                <x-avatar :user="$log->user" :size="28" />
                                <span class="font-medium">{{ $log->user->name }}</span>
                            </div>
                        @else
                            <span class="text-slate-400">System</span>
                        @endif
                    </td>
                    <td class="td">
                        @php($variant = match ($log->action) {
                            'User Login', 'User Logout' => 'gray',
                            default => 'blue',
                        })
                        <x-badge :variant="$variant">
                            {{ $log->action }}
                        </x-badge>
                    </td>
                    <td class="td font-mono text-xs">{{ $log->module ?? '—' }}</td>
                    <td class="td">
                        <button type="button"
                                x-data="{ open: false }"
                                x-on:click="open = !open"
                                class="text-sm text-[var(--color-primary)] hover:underline">
                            {{ $log->changes ? 'View details' : '—' }}
                        </button>

                        <div x-cloak x-show="open" x-transition class="mt-2">
                            @if ($log->changes)
                                <div class="rounded-lg bg-slate-50 p-3 dark:bg-slate-700/40">
                                    <dl class="space-y-1 text-xs">
                                        @foreach ($log->changes as $k => $v)
                                            <div class="flex gap-2">
                                                <dt class="w-24 shrink-0 font-semibold text-slate-500 dark:text-slate-400">{{ $k }}:</dt>
                                                <dd class="text-slate-700 dark:text-slate-200">{{ is_scalar($v) ? $v : json_encode($v) }}</dd>
                                            </div>
                                        @endforeach
                                    </dl>
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-empty-state message="No audit log entries found." icon="shield-check" />
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700">
            <x-pagination :paginator="$logs" />
        </div>
    </div>
</x-app-layout>
