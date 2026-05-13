<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>كاشلي | Kashly - إدارة الأموال والشركاء</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@100..900&display=swap" rel="stylesheet">

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Noto Sans Arabic', 'sans-serif'],
                    },
                    colors: {
                        kashly: {
                            indigo: '#6366f1',
                            emerald: '#10b981',
                            rose: '#f43f5e',
                            bg: '#FDFDFC',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Noto Sans Arabic', sans-serif; background-color: #FDFDFC; overflow-x: hidden; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
        .hero-gradient { background: radial-gradient(circle at top right, #fdf4ff, transparent 40%), radial-gradient(circle at bottom left, #f0f9ff, transparent 40%); }
    </style>
</head>
<body class="antialiased">
    <!-- Navbar -->
    <nav class="fixed top-0 w-full z-50 glass border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-black text-xl shadow-lg shadow-indigo-500/30">K</div>
                <span class="text-2xl font-black tracking-tight text-gray-900">كاشلي</span>
            </div>
            <div class="hidden md:flex items-center gap-8 font-bold text-gray-500">
                <a href="#features" class="hover:text-indigo-600 transition-colors">المميزات</a>
                <a href="#integrations" class="hover:text-indigo-600 transition-colors">التكاملات</a>
                <a href="#contact" class="hover:text-indigo-600 transition-colors">تواصل معنا</a>
            </div>
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-black shadow-lg shadow-indigo-500/20 hover:scale-105 transition-all text-sm">لوحة التحكم</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-gray-600 font-bold hover:text-indigo-600">دخول</a>
                    <a href="{{ route('register') }}" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-black shadow-lg shadow-indigo-500/20 hover:scale-105 transition-all text-sm">ابدأ الآن</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="hero-gradient pt-32 pb-20">
        <!-- Hero Section -->
        <section class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
            <div class="text-right">
                <div class="inline-block px-4 py-1.5 bg-indigo-50 text-indigo-600 rounded-full text-xs font-black uppercase tracking-widest mb-6">إدارة مالية ذكية</div>
                <h1 class="text-5xl lg:text-7xl font-black text-gray-900 leading-[1.15] mb-8">تحكم بأموالك، <br/><span class="text-indigo-600">بكل ذكاء واحترافية.</span></h1>
                <p class="text-xl text-gray-500 leading-relaxed mb-10 max-w-xl ml-auto">كاشلي هي المنصة العربية الأولى التي تجمع لك إدارة الاستثمارات، الشركاء، والتكامل مع متاجرك الإلكترونية في مكان واحد. بساطة في التصميم، قوة في الأداء.</p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-start">
                    <a href="{{ route('register') }}" class="px-10 py-4 bg-indigo-600 text-white rounded-2xl font-black text-lg shadow-xl shadow-indigo-500/30 hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                        ابدأ رحلتك مجاناً
                        <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                    <a href="#features" class="px-10 py-4 bg-white border border-gray-100 text-gray-900 rounded-2xl font-black text-lg shadow-lg hover:bg-gray-50 transition-all flex items-center justify-center">اكتشف المميزات</a>
                </div>

                <div class="mt-12 flex items-center gap-6 justify-start text-gray-400 font-bold text-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                        لا حاجة لبطاقة ائتمان
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                        تكامل مباشر في ثوانٍ
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="absolute -inset-4 bg-indigo-500/10 rounded-[3rem] blur-3xl"></div>
                <img src="{{ asset('images/mockup.png') }}" alt="Kashly Dashboard" class="relative rounded-[2.5rem] shadow-2xl border-8 border-white hover:scale-[1.02] transition-transform duration-700">
                
                <!-- Floating Card -->
                <div class="absolute -bottom-10 -right-10 bg-white p-6 rounded-3xl shadow-2xl border border-gray-100 hidden md:block animate-bounce">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center font-black text-xl">+</div>
                        <div>
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">آخر عملية</div>
                            <div class="text-xl font-black text-gray-900">$2,450.00</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="max-w-7xl mx-auto px-6 mt-32 grid grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm text-center">
                <div class="text-4xl font-black text-indigo-600 mb-2">+100K</div>
                <div class="text-sm font-bold text-gray-400">عملية مالية</div>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm text-center">
                <div class="text-4xl font-black text-emerald-600 mb-2">+500</div>
                <div class="text-sm font-bold text-gray-400">صندوق استثماري</div>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm text-center">
                <div class="text-4xl font-black text-rose-600 mb-2">99.9%</div>
                <div class="text-sm font-bold text-gray-400">دقة في البيانات</div>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm text-center">
                <div class="text-4xl font-black text-indigo-600 mb-2">24/7</div>
                <div class="text-sm font-bold text-gray-400">تحديثات فورية</div>
            </div>
        </section>
    </main>

    <!-- Features Section -->
    <section id="features" class="py-32 bg-white relative">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-20">
                <h2 class="text-4xl lg:text-5xl font-black text-gray-900 mb-6">كل ما تحتاجه لإدارة ثروتك</h2>
                <p class="text-lg text-gray-500 max-w-2xl mx-auto font-bold">من تتبع المصاريف اليومية إلى إدارة استثمارات معقدة مع شركاء متعددين.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="group p-10 bg-gray-50 rounded-[3rem] hover:bg-white hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500 border border-transparent hover:border-indigo-50">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-3xl shadow-sm mb-8 group-hover:scale-110 transition-transform">💼</div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4">إدارة الصناديق والشركاء</h3>
                    <p class="text-gray-500 leading-relaxed font-bold">نظام متطور لتوزيع الحصص، تتبع رؤوس الأموال، وتوليد تقارير الأرباح لكل شريك تلقائياً.</p>
                </div>

                <!-- Feature 2 -->
                <div class="group p-10 bg-gray-50 rounded-[3rem] hover:bg-white hover:shadow-2xl hover:shadow-emerald-500/10 transition-all duration-500 border border-transparent hover:border-emerald-50">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-3xl shadow-sm mb-8 group-hover:scale-110 transition-transform">🔌</div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4">ربط آلي بالكامل</h3>
                    <p class="text-gray-500 leading-relaxed font-bold">تكامل مباشر مع Shopify و WHMCS لسحب مبيعاتك وأرباحك فور حدوثها دون تدخل يدوي.</p>
                </div>

                <!-- Feature 3 -->
                <div class="group p-10 bg-gray-50 rounded-[3rem] hover:bg-white hover:shadow-2xl hover:shadow-rose-500/10 transition-all duration-500 border border-transparent hover:border-rose-50">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-3xl shadow-sm mb-8 group-hover:scale-110 transition-transform">📊</div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4">تحليلات ذكية</h3>
                    <p class="text-gray-500 leading-relaxed font-bold">رسوم بيانية متقدمة تساعدك على فهم تدفقاتك المالية واتخاذ قرارات استثمارية مبنية على أرقام حقيقية.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Integrations Section -->
    <section id="integrations" class="py-20 bg-indigo-600 overflow-hidden relative">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
        <div class="max-w-7xl mx-auto px-6 text-center relative z-10">
            <h2 class="text-3xl lg:text-4xl font-black text-white mb-12">يدعم منصاتك المفضلة</h2>
            <div class="flex flex-wrap justify-center items-center gap-12 opacity-80 hover:opacity-100 transition-opacity">
                <div class="flex items-center gap-3 text-white">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center font-black">S</div>
                    <span class="text-xl font-black uppercase tracking-tighter">Shopify</span>
                </div>
                <div class="flex items-center gap-3 text-white">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center font-black">W</div>
                    <span class="text-xl font-black uppercase tracking-tighter">WHMCS</span>
                </div>
                <div class="flex items-center gap-3 text-white">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center font-black">S</div>
                    <span class="text-xl font-black uppercase tracking-tighter">Stripe</span>
                </div>
                <div class="flex items-center gap-3 text-white">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center font-black">P</div>
                    <span class="text-xl font-black uppercase tracking-tighter">PayPal</span>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-32">
        <div class="max-w-5xl mx-auto px-6 bg-gray-900 rounded-[4rem] p-16 text-center relative overflow-hidden shadow-2xl">
            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-600/20 blur-[100px] -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-emerald-600/20 blur-[100px] translate-y-1/2 -translate-x-1/2"></div>
            
            <h2 class="text-4xl lg:text-6xl font-black text-white mb-8 relative z-10 leading-tight">جاهز لضبط أموالك <br/>ومضاعفة أرباحك؟</h2>
            <p class="text-xl text-gray-400 mb-12 relative z-10 max-w-xl mx-auto">انضم إلى مئات المستثمرين الذين يستخدمون كاشلي يومياً لإدارة ثرواتهم بنجاح.</p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center relative z-10">
                <a href="{{ route('register') }}" class="px-12 py-5 bg-indigo-600 text-white rounded-3xl font-black text-xl shadow-xl shadow-indigo-500/20 hover:scale-105 transition-all">سجل مجاناً الآن</a>
                <a href="#features" class="px-12 py-5 bg-white/10 text-white rounded-3xl font-black text-xl hover:bg-white/20 transition-all">تعرف علينا أكثر</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-20 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-10">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-black text-sm shadow-lg shadow-indigo-500/30">K</div>
                    <span class="text-xl font-black tracking-tight text-gray-900">كاشلي</span>
                </div>
                <div class="flex gap-8 font-bold text-gray-400 text-sm">
                    <a href="#" class="hover:text-indigo-600 transition-colors">عن المنصة</a>
                    <a href="#" class="hover:text-indigo-600 transition-colors">الشروط والأحكام</a>
                    <a href="#" class="hover:text-indigo-600 transition-colors">سياسة الخصوصية</a>
                </div>
                <div class="text-gray-400 text-sm font-bold">
                    © {{ date('Y') }} كاشلي. جميع الحقوق محفوظة.
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
