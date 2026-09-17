<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🏃‍♀️‍➡️</text></svg>">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#F2EFE7] relative">
            <div>
                <h2 style="font-size: xx-large;" class="text-2xl font-semibold text-[#3368A0]">Laju</h2>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white border border-[#C8DFDB] shadow-md overflow-hidden sm:rounded-xl">
                {{ $slot }}
                <div class="me-4 flex items-center space-x-2 text-sm">
                    <a href="{{ route('locale.switch', 'id') }}"
                    class="{{ app()->getLocale() === 'id' ? 'font-semibold text-gray-900' : 'text-gray-400' }}">
                        ID
                    </a>
                    <span class="text-gray-300">|</span>
                    <a href="{{ route('locale.switch', 'en') }}"
                    class="{{ app()->getLocale() === 'en' ? 'font-semibold text-gray-900' : 'text-gray-400' }}">
                        EN
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>