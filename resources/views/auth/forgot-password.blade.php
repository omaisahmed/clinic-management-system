<x-guest-layout>
    <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Forgot your password?</h1>
    <p class="mt-1 mb-6 text-sm text-slate-500 dark:text-slate-400">
        {{ __('No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">
            <x-icon name="mail" class="h-4 w-4" />
            {{ __('Email Password Reset Link') }}
        </x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm">
        <a class="font-medium text-[var(--color-primary)] hover:text-[var(--color-primary-700)]" href="{{ route('login') }}">Back to sign in</a>
    </p>
</x-guest-layout>
