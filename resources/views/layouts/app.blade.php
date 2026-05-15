<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Kashly') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Tajawal', 'sans-serif'],
                        },
                    }
                }
            }
        </script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        <style>
            body { font-family: 'Tajawal', sans-serif; background-color: #F4F7FE; }
            .premium-card { 
                background: white; 
                border-radius: 3rem; 
                border: 1px solid rgba(0, 0, 0, 0.03);
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.03), 0 4px 6px -4px rgba(0, 0, 0, 0.03);
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .premium-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 25px 35px -10px rgba(79, 70, 229, 0.08);
                border-color: rgba(79, 70, 229, 0.1);
            }
            .premium-input {
                background-color: #f9fafb;
                border: 1px solid #e5e7eb;
                border-radius: 1.5rem;
                padding: 1.25rem 1.5rem;
                font-weight: 700;
                transition: all 0.3s ease;
                box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.01);
                color: #111827;
            }
            .premium-input:focus {
                background-color: white;
                border-color: #4f46e5;
                box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1), inset 0 2px 4px 0 rgba(0, 0, 0, 0.01);
                outline: none;
            }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-[#F4F7FE] text-gray-900">
        <div class="min-h-screen bg-[#F4F7FE]">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
