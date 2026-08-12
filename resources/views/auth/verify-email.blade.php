<x-guest-layout>
    <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Verify your email</h1>
    <p class="mt-1 mb-6 text-sm text-slate-500 dark:text-slate-400">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-800 dark:bg-green-950 dark:text-green-200">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex flex-col items-stretch justify-between gap-3 sm:flex-row sm:items-center">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="text-sm font-medium text-[var(--color-primary)] hover:text-[var(--color-primary-700)]">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
