<x-app-layout>
    <x-page-header title="Patients" subtitle="Register and manage patients">
        <x-slot name="actions">
            @can('patients.create')
                <x-button href="{{ route('patients.create') }}" icon="user-plus">Register Patient</x-button>
            @endcan
        </x-slot>
    </x-page-header>

    <x-alerts />

    <!-- Filters -->
    <div class="card mb-4 p-4">
        <form method="GET" action="{{ route('patients.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="min-w-64 flex-1">
                <x-label for="q">Search</x-label>
                <x-input name="q" id="q" :value="request('q')" placeholder="Name, patient ID, phone, email, CNIC..." />
            </div>
            <div class="w-40">
                <x-label for="blood_group">Blood Group</x-label>
                <x-select name="blood_group" id="blood_group" :options="$bloodGroups" :value="request('blood_group')" placeholder="Any" />
            </div>
            <div class="w-44">
                <x-label for="city">City</x-label>
                <x-input name="city" id="city" :value="request('city')" placeholder="City" />
            </div>
            <x-button type="submit" variant="secondary" icon="search">Search</x-button>
            @if (request()->hasAny(['q', 'blood_group', 'city']))
                <x-button href="{{ route('patients.index') }}" variant="ghost" icon="x-mark">Clear</x-button>
            @endif
        </form>
    </div>

    <div class="card overflow-hidden">
        <x-table :headers="['Patient', 'ID', 'Contact', 'Age / Gender', 'Blood', 'Registered', '']">
            @forelse ($patients as $patient)
                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/40">
                    <td class="td">
                        <div class="flex items-center gap-3">
                            @if ($patient->photo_url)
                                <img src="{{ $patient->photo_url }}" alt="{{ $patient->full_name }}" class="h-9 w-9 rounded-full object-cover">
                            @else
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[var(--color-primary)] text-xs font-bold text-white">
                                    {{ $patient->initials }}
                                </span>
                            @endif
                            <div>
                                <a href="{{ route('patients.show', $patient) }}" class="font-medium text-slate-900 hover:underline dark:text-white">
                                    {{ $patient->full_name }}
                                </a>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $patient->email ?: '—' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="td font-mono text-xs">
                        <x-badge variant="primary">{{ $patient->patient_number }}</x-badge>
                    </td>
                    <td class="td">{{ $patient->phone ?: '—' }}</td>
                    <td class="td">
                        {{ $patient->age !== null ? $patient->age . ' yrs' : '—' }}
                        <span class="text-slate-400">· {{ $patient->gender ? ucfirst($patient->gender) : '—' }}</span>
                    </td>
                    <td class="td">
                        @if ($patient->blood_group)
                            <x-badge variant="red">{{ $patient->blood_group }}</x-badge>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="td text-slate-500">{{ $patient->created_at->format('M d, Y') }}</td>
                    <td class="td">
                        <div class="flex items-center justify-end gap-1">
                            <x-button :href="route('patients.show', $patient)" variant="ghost" size="sm" icon="eye">View</x-button>
                            @can('patients.update')
                                <x-button :href="route('patients.edit', $patient)" variant="ghost" size="sm" icon="pencil">Edit</x-button>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <x-empty-state message="No patients found." icon="users"
                                       :actionLabel="auth()->user()->can('patients.create') ? 'Register Patient' : null"
                                       :actionHref="auth()->user()->can('patients.create') ? route('patients.create') : null" />
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700">
            <x-pagination :paginator="$patients" />
        </div>
    </div>
</x-app-layout>
