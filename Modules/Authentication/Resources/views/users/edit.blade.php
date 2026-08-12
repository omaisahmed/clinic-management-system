<x-app-layout>
    <x-page-header title="Edit User" subtitle="{{ $user->name }}"
                   :actions="[['label' => 'Back', 'href' => route('authentication.users.index'), 'icon' => 'arrow-left']]" />

    <x-alerts />

    <div class="max-w-2xl">
        <div class="card p-6">
            <form method="POST" action="{{ route('authentication.users.update', $user) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="flex items-center gap-4">
                    <x-avatar :user="$user" :size="64" />
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ $user->name }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <x-label for="name" :required="true">Full Name</x-label>
                        <x-input name="name" id="name" :value="$user->name" :required="true" />
                    </div>
                    <div>
                        <x-label for="phone">Phone</x-label>
                        <x-input name="phone" id="phone" :value="$user->phone" />
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <x-label for="email" :required="true">Email</x-label>
                        <x-input type="email" name="email" id="email" :value="$user->email" :required="true" />
                    </div>
                    <div>
                        <x-label for="gender">Gender</x-label>
                        <x-select name="gender" id="gender"
                                  :options="['male' => 'Male', 'female' => 'Female', 'other' => 'Other']"
                                  :value="$user->gender"
                                  placeholder="Select gender" />
                    </div>
                </div>

                <div>
                    <x-label for="role" :required="true">Role</x-label>
                    <x-select name="role" id="role" :options="$roles" :value="$user->roles->first()?->name" :required="true" />
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <x-label for="password">New Password <span class="text-xs font-normal text-slate-400">(leave blank to keep)</span></x-label>
                        <x-input type="password" name="password" id="password" autocomplete="new-password" />
                    </div>
                    <div>
                        <x-label for="password_confirmation">Confirm Password</x-label>
                        <x-input type="password" name="password_confirmation" id="password_confirmation" />
                    </div>
                </div>

                <div>
                    <x-checkbox name="is_active" label="Active account" :checked="$user->is_active" />
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-200 pt-5 dark:border-slate-700">
                    <x-button :href="route('authentication.users.index')" variant="secondary">Cancel</x-button>
                    <x-button type="submit" icon="check">Save Changes</x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
