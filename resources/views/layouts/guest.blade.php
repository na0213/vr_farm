<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow">

        <title>FARM360 管理画面</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/css/farm360.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
            <a href="{{ route('index') }}" class="font-semibold text-gray-600 hover:text-gray-900">公開サイトへ</a>
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <a href="{{ route('index') }}" aria-label="FARM360 公開サイトへ">
                <x-application-logo />
            </a>
            <p class="mt-1 text-sm text-gray-500">管理画面</p>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white shadow-md overflow-hidden sm:rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
