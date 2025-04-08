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

        <!-- Favicons -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/biblioscanner-logo.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/biblioscanner-logo.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/biblioscanner-logo.png') }}">
        <link rel="manifest" href="{{ asset('images/biblioscanner-logo.png') }}">
        <link rel="mask-icon" href="{{ asset('images/biblioscanner-logo.png') }}" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#000000">
        <meta name="theme-color" content="#ffffff">
        <meta name="description" content="BiblioScanner - Discover more from your academic PDFs.">
        <meta name="keywords" content="BiblioScanner, academic, PDF, scanner, recommendations, citation, summary">
        <meta name="author" content="Aidil Ikbar">
        <link rel="icon" href="{{ asset('images/biblioscanner-logo.png') }}" type="image/png">

        <!-- Fontawesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>
        
    </body>
</html>
