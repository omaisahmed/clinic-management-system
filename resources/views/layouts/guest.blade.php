<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'Clinic CMS') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @if (function_exists('current_clinic') && current_clinic()?->favicon_url)
            <link rel="icon" href="{{ current_clinic()->favicon_url }}">
        @endif

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full bg-slate-100 font-sans text-slate-900 antialiased dark:bg-slate-900 dark:text-slate-100">
        <div class="flex min-h-full">
            <!-- Brand panel -->
            <div class="relative hidden w-1/2 max-w-2xl flex-col justify-between overflow-hidden p-12 text-white lg:flex"
                 style="background: linear-gradient(135deg, var(--color-primary-700), var(--color-primary) 55%, var(--color-secondary));">
                <svg class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 text-white/10" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3.375 4.5C2.504 4.5 1.75 5.253 1.75 6.125v2.25c0 .242.032.477.094.704A6.001 6.001 0 0113.125 12.5a6 6 0 01-3.094 5.42 6.75 6.75 0 01-2.219.83H7.5a.75.75 0 010-1.5h.141a5.25 5.25 0 000-10.5H4.5V6.125c0-.621-.504-1.125-1.125-1.125zM9.75 9a.75.75 0 01.75-.75h5.25a.75.75 0 010 1.5h-5.25a.75.75 0 01-.75-.75zm.75 2.25h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 010-1.5z"/>
                </svg>
                <svg class="pointer-events-none absolute -bottom-32 -left-24 h-[30rem] w-[30rem] text-white/10" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                </svg>

                <div class="relative flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/15 shadow-lg ring-1 ring-white/25 backdrop-blur">
                        <x-icon name="stethoscope" class="h-6 w-6" />
                    </span>
                    <div>
                        <p class="text-lg font-bold tracking-tight">{{ current_clinic()?->name ?? config('app.name', 'Clinic CMS') }}</p>
                        <p class="text-sm text-white/70">{{ current_clinic()?->tagline ?? 'Clinic Management System' }}</p>
                    </div>
                </div>

                <div class="relative">
                    <h1 class="max-w-md text-3xl font-bold leading-tight tracking-tight">
                        Run your clinic smoothly, from check-in to checkout.
                    </h1>
                    <p class="mt-3 max-w-md text-white/80">
                        Appointments, patients, prescriptions, lab tests and billing — all in one secure place for your team.
                    </p>

                    <ul class="mt-8 space-y-3">
                        @foreach ([
                            ['calendar', 'Smart appointment scheduling & queue management'],
                            ['user-group', 'Centralized patient records & medical history'],
                            ['capsule', 'Electronic prescriptions & medicine tracking'],
                            ['receipt', 'Simple billing, payments & revenue reports'],
                        ] as $feature)
                            <li class="flex items-center gap-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/15 ring-1 ring-white/25">
                                    <x-icon :name="$feature[0]" class="h-4 w-4" />
                                </span>
                                <span class="text-sm text-white/85">{{ $feature[1] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <p class="relative text-xs text-white/50">&copy; {{ date('Y') }} {{ current_clinic()?->name ?? config('app.name') }}. All rights reserved.</p>
            </div>

            <!-- Form panel -->
            <div class="flex w-full items-center justify-center p-6 sm:p-10 lg:w-1/2">
                <div class="w-full max-w-md">
                    <div class="mb-8 flex items-center gap-3 lg:hidden">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl text-white shadow-md"
                              style="background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));">
                            <x-icon name="stethoscope" class="h-5 w-5" />
                        </span>
                        <div>
                            <p class="text-base font-bold text-slate-900 dark:text-white">{{ current_clinic()?->name ?? config('app.name') }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Clinic Management System</p>
                        </div>
                    </div>

                    <div class="card p-8">
                        {{ $slot }}
                    </div>

                    <p class="mt-6 text-center text-xs text-slate-400 dark:text-slate-500">&copy; {{ date('Y') }} {{ config('app.name', 'Clinic CMS') }}</p>
                </div>
            </div>
        </div>
    </body>
</html>
