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
        <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Readex Pro', 'sans-serif'],
                        },
                        fontSize: {
                            'xs': ['0.7rem', { lineHeight: '1rem' }],
                            'sm': ['0.775rem', { lineHeight: '1.25rem' }],
                            'base': ['0.875rem', { lineHeight: '1.5rem' }],
                            'lg': ['1rem', { lineHeight: '1.75rem' }],
                            'xl': ['1.1rem', { lineHeight: '1.75rem' }],
                            '2xl': ['1.25rem', { lineHeight: '2rem' }],
                            '3xl': ['1.5rem', { lineHeight: '2.25rem' }],
                            '4xl': ['1.85rem', { lineHeight: '2.5rem' }],
                            '5xl': ['2.4rem', { lineHeight: '3rem' }],
                            '6xl': ['3.1rem', { lineHeight: '1' }],
                            '7xl': ['3.8rem', { lineHeight: '1' }],
                            '8xl': ['5rem', { lineHeight: '1' }],
                            '9xl': ['6.5rem', { lineHeight: '1' }],
                        }
                    }
                }
            }
        </script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>
            body { font-family: 'Readex Pro', sans-serif; background-color: #FDFDFC; }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-8">
                <a href="/" class="flex flex-col items-center gap-4">
                    <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-black text-2xl shadow-xl shadow-indigo-500/30">K</div>
                    <span class="text-2xl font-black tracking-tight text-gray-900 tracking-wider">كاشلي</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-10 py-12 bg-white shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden sm:rounded-[3rem]">
                {{ $slot }}
            </div>

            <div class="mt-8 text-sm font-bold text-gray-400">
                &copy; {{ date('Y') }} جميع الحقوق محفوظة لشركة كاشلي
            </div>
        </div>
    </body>
</html>
