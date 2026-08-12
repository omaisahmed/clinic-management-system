<x-app-layout>
    <x-page-header title="Staff" subtitle="Manage clinic team members and their roles">
        <x-slot name="actions">
            <x-button href="{{ route('authentication.users.create') }}" icon="plus">Add Staff Member</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <div class="card overflow-hidden">
        <x-table :headers="['User', 'Role', 'Status', 'Created', '']">
            @forelse ($users as $user)
                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/40">
                    <td class="td">
                        <div class="flex items-center gap-3">
                            <x-avatar :user="$user" :size="36" />
                            <div>
                                <p class="font-medium text-slate-900 dark:text-white">{{ $user->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="td">
                        <x-badge variant="{{ match ($user->roles->first()?->name) {
                            'doctor' => 'primary',
                            'clinic_admin', 'super_admin' => 'purple',
                            'receptionist' => 'blue',
                            'nurse' => 'teal',
                            'pharmacist' => 'amber',
                            'accountant' => 'green',
                            'lab_technician' => 'gray',
                            default => 'gray',
                        } }}">
                            {{ $user->roles->first()?->name ?? '—' }}
                        </x-badge>
                    </td>
                    <td class="td">
                        @if ($user->is_active)
                            <x-badge variant="green">Active</x-badge>
                        @else
                            <x-badge variant="red">Inactive</x-badge>
                        @endif
                    </td>
                    <td class="td text-slate-500">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="td">
                        <div class="flex items-center justify-end gap-1">
                            <x-button :href="route('authentication.users.edit', $user)" variant="ghost" size="sm" icon="pencil">Edit</x-button>
                            <form method="POST" action="{{ route('authentication.users.destroy', $user) }}"
                                  x-data x-on:submit.prevent="$dispatch('confirm-submit')" class="inline">
                                @csrf
                                @method('DELETE')
                                <x-button type="submit" variant="ghost" size="sm" icon="trash" class="text-red-600">Delete</x-button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-empty-state message="No staff members yet." icon="user-group" />
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700">
            <x-pagination :paginator="$users" />
        </div>
    </div>
</x-app-layout>
