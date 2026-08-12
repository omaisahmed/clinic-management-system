<x-app-layout>
    <x-page-header title="Add Staff Member" subtitle="Create a new team account"
                   :actions="[['label' => 'Back', 'href' => route('authentication.users.index'), 'icon' => 'arrow-left']]" />

    <x-alerts />

    <div class="max-w-2xl">
        <div class="card p-6">
            <form method="POST" action="{{ route('authentication.users.store') }}" class="space-y-5" enctype="multipart/form-data">
                @csrf

                <div>
                    <x-label for="name" :required="true">Full Name</x-label>
                    <x-input name="name" id="name" placeholder="e.g. Dr. John Smith" :required="true" />
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <x-label for="email" :required="true">Email</x-label>
                        <x-input type="email" name="email" id="email" placeholder="name@example.com" :required="true" />
                    </div>
                    <div>
                        <x-label for="phone">Phone</x-label>
                        <x-input name="phone" id="phone" placeholder="+1 555 0100" />
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <x-label for="role" :required="true">Role</x-label>
                        <x-select name="role" id="role" :options="$roles" placeholder="Select a role" :required="true" />
                    </div>
                    <div>
                        <x-label for="gender">Gender</x-label>
                        <x-select name="gender" id="gender"
                                  :options="['male' => 'Male', 'female' => 'Female', 'other' => 'Other']"
                                  placeholder="Select gender" />
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <x-label for="password" :required="true">Password</x-label>
                        <x-input type="password" name="password" id="password" :required="true" autocomplete="new-password" />
                    </div>
                    <div>
                        <x-label for="password_confirmation" :required="true">Confirm Password</x-label>
                        <x-input type="password" name="password_confirmation" id="password_confirmation" :required="true" />
                    </div>
                </div>

                <div>
                    <x-checkbox name="is_active" label="Active account" :checked="true" />
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-200 pt-5 dark:border-slate-700">
                    <x-button :href="route('authentication.users.index')" variant="secondary">Cancel</x-button>
                    <x-button type="submit" icon="plus">Create User</x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
