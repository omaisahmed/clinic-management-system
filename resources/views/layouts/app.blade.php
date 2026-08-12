<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      class="h-full"
      style="--color-primary: {{ setting('branding.primary_color', '#0d9488') }}; --color-secondary: {{ setting('branding.secondary_color', '#0891b2') }}; --color-accent: {{ setting('branding.accent_color', '#f59e0b') }};">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'Clinic CMS') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @if (current_clinic()?->favicon_url)
            <link rel="icon" href="{{ current_clinic()->favicon_url }}">
        @endif

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('head')
    </head>
    <body x-data="darkMode" x-init="init" class="h-full font-sans">
        <div class="min-h-full" x-data="sidebar" x-on:keydown.escape.window="close()">
            @include('layouts.sidebar')

            <div class="flex min-h-full flex-col lg:pl-64">
                @include('layouts.header')

                <main class="flex-1 px-4 pb-12 pt-4 sm:px-6 lg:px-8">
                    @isset($header)
                        {{ $header }}
                    @endisset

                    {{ $slot }}
                </main>
            </div>

            <x-confirm-dialog />
            <x-toast />
        </div>

        {{ $script ?? '' }}

        @stack('scripts')
    </body>
</html>
