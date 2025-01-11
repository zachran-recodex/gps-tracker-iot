<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Fixed Header with Logo -->
        <div class="fixed top-0 left-0 right-0 bg-white shadow-sm z-10">
            <div class="max-w-sm mx-auto px-4 py-3 flex justify-center items-center">
                <span class="text-xl font-bold text-gray-800">GPS Tracker</span>
            </div>
        </div>

        <!-- Page Content with padding top for fixed header -->
        <main class="pt-16 pb-16">
            {{ $slot }}
        </main>

        <!-- Bottom Navigation -->
        <nav class="fixed bottom-0 left-0 right-0 bg-white shadow-lg">
            <div class="max-w-sm mx-auto px-4">
                <div class="flex justify-between py-3">
                    <a href="{{ route('dashboard') }}"
                        class="flex flex-col items-center text-gray-600 hover:text-gray-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                        <span class="text-xs mt-1">Map</span>
                    </a>

                    <a href="{{ route('profile.edit') }}"
                        class="flex flex-col items-center text-gray-600 hover:text-gray-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="text-xs mt-1">Profile</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}"
                        class="flex flex-col items-center text-gray-600 hover:text-gray-900">
                        @csrf
                        <button type="submit" class="flex flex-col items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span class="text-xs mt-1">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </div>
</body>

</html>
