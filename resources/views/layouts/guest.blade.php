<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 py-6 sm:py-8 bg-gradient-to-br from-slate-50 to-indigo-50">
            <div class="w-full sm:max-w-md">
                <div class="flex justify-end mb-3">
                    <x-locale-switcher :align="app()->getLocale() === 'ar' ? 'left' : 'right'" />
                </div>

                <div class="text-center">
                    <a href="/" class="inline-block">
                        <x-application-logo class="w-20 h-20 fill-current text-indigo-600" />
                    </a>
                    <p class="mt-2 text-lg font-bold text-gray-800">{{ config('app.name') }}</p>
                    <p class="text-sm text-gray-500">{{ __('diwan.app_subtitle') }}</p>
                </div>

                <div class="w-full mt-6 px-6 py-4 bg-white shadow-md sm:rounded-lg">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
