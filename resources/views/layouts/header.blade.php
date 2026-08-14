<header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8 dark:border-slate-700 dark:bg-slate-900/90">
    <!-- Mobile menu -->
    <button x-on:click="open = true"
            class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden dark:text-slate-400 dark:hover:bg-slate-800"
            aria-label="Open menu">
        <x-icon name="menu" class="h-6 w-6" />
    </button>

    <!-- Today / context -->
    <div class="hidden min-w-0 md:block">
        <p class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100">
            {{ now()->format('l, F j') }}
        </p>
        <p class="truncate text-xs text-slate-500 dark:text-slate-400">
            {{ current_clinic()?->name ?? config('app.name') }}
        </p>
    </div>

    <!-- Search -->
    <form method="GET" action="{{ route('search.index') }}" class="hidden flex-1 items-center justify-center md:flex">
        <div class="relative w-full max-w-md">
            <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input type="search"
                   name="q"
                   value="{{ request('q') }}"
                   placeholder="Search patients, invoices, medicines..."
                   autocomplete="off"
                   class="input !rounded-full !border-slate-200 !bg-slate-100 !pl-10 focus:!border-[var(--color-primary)] focus:!bg-white focus:!ring-[var(--color-primary)] dark:!border-slate-700 dark:!bg-slate-800" />
        </div>
    </form>

    <div class="ml-auto flex items-center gap-2">
        <!-- Theme toggle -->
        <div class="relative" x-data="{ open: false }" x-on:click.outside="open = false">
            <button x-on:click="open = !open"
                    class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800"
                    aria-label="Toggle theme">
                <x-icon name="moon" class="hidden h-5 w-5 dark:block" />
                <x-icon name="sun" class="h-5 w-5 dark:hidden" />
            </button>
            <div x-cloak
                 x-show="open"
                 x-transition
                 class="absolute right-0 z-50 mt-2 w-44 rounded-xl border border-slate-200 bg-white p-1 shadow-lg dark:border-slate-700 dark:bg-slate-800">
                <button x-on:click="set('light')"
                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-700">
                    <x-icon name="sun" class="h-4 w-4" /> Light
                </button>
                <button x-on:click="set('dark')"
                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-700">
                    <x-icon name="moon" class="h-4 w-4" /> Dark
                </button>
                <button x-on:click="set('system')"
                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-700">
                    <x-icon name="device-desktop" class="h-4 w-4" /> System
                </button>
            </div>
        </div>

        <!-- Notifications -->
        <a href="{{ Route::has('audit.logs.index') ? route('audit.logs.index') : '#' }}"
           class="relative rounded-lg p-2 text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800"
           aria-label="Notifications">
            <x-icon name="bell" class="h-5 w-5" />
        </a>

        <!-- User menu -->
        <div class="relative" x-data="{ open: false }" x-on:click.outside="open = false">
            <button x-on:click="open = !open"
                    class="flex items-center gap-2 rounded-lg p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800">
                <x-avatar :user="auth()->user()" :size="32" />
                <span class="hidden text-left sm:block">
                    <span class="block text-sm font-semibold text-slate-700 dark:text-slate-200">{{ auth()->user()->name }}</span>
                    <span class="block text-xs text-slate-500 dark:text-slate-400">{{ auth()->user()->roles->first()?->name }}</span>
                </span>
                <x-icon name="chevron-down" class="hidden h-4 w-4 text-slate-400 sm:block" />
            </button>

            <div x-cloak
                 x-show="open"
                 x-transition
                 class="absolute right-0 z-50 mt-2 w-56 rounded-xl border border-slate-200 bg-white py-1 shadow-lg dark:border-slate-700 dark:bg-slate-800">
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700">
                    <x-icon name="user" class="h-4 w-4" /> Profile
                </a>
                <a href="{{ route('authentication.users.edit', auth()->id()) }}"
                   class="hidden items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700">
                    <x-icon name="cog" class="h-4 w-4" /> Account
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950">
                        <x-icon name="logout" class="h-4 w-4" /> Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
