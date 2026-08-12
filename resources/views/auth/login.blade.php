<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Welcome back</h1>
    <p class="mt-1 mb-6 text-sm text-slate-500 dark:text-slate-400">Sign in to access your clinic dashboard.</p>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="mt-1 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-[var(--color-primary)] focus:ring-[var(--color-primary)] dark:border-slate-600 dark:bg-slate-800" name="remember">
                <span class="ms-2 text-sm text-slate-600 dark:text-slate-300">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-[var(--color-primary)] hover:text-[var(--color-primary-700)]" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-full">
            <x-icon name="lock" class="h-4 w-4" />
            {{ __('Sign in') }}
        </x-primary-button>
    </form>
</x-guest-layout>
