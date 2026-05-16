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
            [x-cloak] { display: none !important; }
            
            /* Global Background Upgrade */
            body { 
                font-family: 'Tajawal', sans-serif; 
                background-color: #F1F5F9; /* Rich Tinted Off-White */
            }

            /* Premium Card 2.0 - Stronger definition */
            .premium-card { 
                background: #FDFDFF; /* Very light tinted background instead of pure white */
                border-radius: 4rem; 
                border: 2px solid #E2E8F0; /* Visible, solid border */
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08), 0 10px 20px -5px rgba(0, 0, 0, 0.04);
                transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                z-index: 10;
            }

            .premium-card:hover {
                transform: translateY(-8px) scale(1.01);
                box-shadow: 0 40px 80px -20px rgba(79, 70, 229, 0.15);
                border-color: #C7D2FE;
            }

            /* Color Accents for Cards */
            .card-blue { background-color: #F0F9FF; border-color: #BAE6FD; }
            .card-green { background-color: #F0FDF4; border-color: #BBF7D0; }
            .card-yellow { background-color: #FFFBEB; border-color: #FEF3C7; }
            .card-red { background-color: #FEF2F2; border-color: #FECACA; }

            /* Professional Inputs */
            .premium-input {
                background-color: #FFFFFF;
                border: 2px solid #E5E7EB;
                border-radius: 2rem;
                padding: 1.5rem 1.8rem;
                font-weight: 800;
                color: #1E293B;
                transition: all 0.3s ease;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            }

            .premium-input:focus {
                border-color: #6366F1;
                box-shadow: 0 0 0 5px rgba(99, 102, 241, 0.15);
                outline: none;
                transform: scale(1.02);
            }

            /* Premium Scrollbar */
            ::-webkit-scrollbar { width: 8px; }
            ::-webkit-scrollbar-track { background: #F1F5F9; }
            ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
            ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }
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
