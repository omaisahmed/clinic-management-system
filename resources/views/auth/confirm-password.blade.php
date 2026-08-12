<x-guest-layout>
    <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Confirm your password</h1>
    <p class="mt-1 mb-6 text-sm text-slate-500 dark:text-slate-400">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="mt-1 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">
            {{ __('Confirm') }}
        </x-primary-button>
    </form>
</x-guest-layout>
