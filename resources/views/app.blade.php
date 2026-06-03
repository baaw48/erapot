<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="E-RAPOT - Sistem Informasi Raport Digital">

        <title inertia>{{ config('app.name', 'E-RAPOT') }}</title>

        @php
            $sekolah = \App\Models\Sekolah::first();
            $favicon = $sekolah && $sekolah->getLogoUrl() ? $sekolah->getLogoUrl() : asset('favicon.ico');
        @endphp
        <link rel="icon" href="{{ $favicon }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>

    <body class="font-sans antialiased bg-slate-50 text-slate-800 dark:bg-slate-900 dark:text-slate-100 transition-colors duration-300">
        @inertia
    </body>
</html>
