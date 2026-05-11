<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'كاشلي') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Tailwind & Alpine CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            slate: {
                                850: '#141c2f',
                                950: '#0a0f1d',
                            }
                        },
                        fontFamily: {
                            sans: ['Cairo', 'sans-serif'],
                        },
                    }
                }
            }
        </script>
        <style type="text/css">
            [x-cloak] { display: none !important; }
            body {
                background: radial-gradient(circle at top right, #1e1b4b, #0f172a, #020617);
                background-attachment: fixed;
            }
            .glass {
                background: rgba(30, 41, 59, 0.4);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.08);
            }
            .hide-scrollbar::-webkit-scrollbar {
                display: none;
            }
            .hide-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        </style>
    </head>
    <body class="font-sans antialiased text-slate-200">
        <div class="min-h-screen flex">
            <!-- Sidebar placeholder - will implement later -->
            @include('layouts.navigation')

            <div class="flex-1">
                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-slate-900/50 border-b border-slate-800 backdrop-blur-md sticky top-0 z-30">
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
