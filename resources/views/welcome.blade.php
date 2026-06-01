<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>كاشلي | Kashly - إدارة الأموال والشركاء الذكية</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Readex Pro', 'sans-serif'],
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
        body { font-family: 'Readex Pro', sans-serif; background-color: #FDFDFC; overflow-x: hidden; }
        .glass { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(16px); }
        .hero-gradient { background: radial-gradient(circle at top right, #fdf4ff, transparent 45%), radial-gradient(circle at bottom left, #f0f9ff, transparent 45%); }
        .dark-glass { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(16px); }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased" x-data="{ 
    startingCapital: 5000, 
    monthlyGrowth: 8, 
    years: 5, 
    showSimResult: true,
    webhookSent: false,
    mockBalance: 12450.00,
    mockTransactions: [
        { desc: 'دفعة من المشترك: أحمد علي', amount: 35.00, type: 'income', time: 'منذ ثوانٍ' },
        { desc: 'فاتورة خادم: Starlink Cloud', amount: 110.00, type: 'expense', time: 'منذ دقيقة' }
    ],
    sendWebhook() {
        if(this.webhookSent) return;
        this.webhookSent = true;
        setTimeout(() => {
            this.mockBalance += 45.00;
            this.mockTransactions.unshift({
                desc: 'دفعة MadaaQ: رائد خولاني',
                amount: 45.00,
                type: 'income',
                time: 'الآن'
            });
            this.webhookSent = false;
        }, 1200);
    }
}">
    <!-- Navbar -->
    <nav class="fixed top-0 w-full z-50 glass border-b border-slate-100/80">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-black text-xl shadow-lg shadow-indigo-500/30">K</div>
                <span class="text-2xl font-black tracking-tight text-gray-900">كاشلي</span>
            </div>
            <div class="hidden md:flex items-center gap-8 font-bold text-gray-500">
                <a href="#features" class="hover:text-indigo-600 transition-colors">المميزات</a>
                <a href="#simulator" class="hover:text-indigo-600 transition-colors">الحاسبة التفاعلية</a>
                <a href="#integrations" class="hover:text-indigo-600 transition-colors">التكامل الآلي</a>
                <a href="#testimonials" class="hover:text-indigo-600 transition-colors">آراء العملاء</a>
            </div>
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-black shadow-lg shadow-indigo-500/20 hover:scale-105 transition-all text-sm">لوحة التحكم</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-gray-600 font-bold hover:text-indigo-600">دخول</a>
                    <a href="{{ route('register') }}" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-black shadow-lg shadow-indigo-500/20 hover:scale-105 transition-all text-sm">ابدأ الآن مجاناً</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="hero-gradient pt-32 pb-20">
        <section class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
            <div class="text-right">
                <div class="inline-block px-4 py-1.5 bg-indigo-50 text-indigo-600 rounded-full text-xs font-black uppercase tracking-widest mb-6">إدارة مالية ذكية للمستثمرين والشبكات</div>
                <h1 class="text-5xl lg:text-7xl font-black text-gray-900 leading-[1.15] mb-8">تحكم بأموالك وشركائك، <br/><span class="text-indigo-600">ببساطة واحترافية.</span></h1>
                <p class="text-lg text-gray-500 leading-relaxed mb-10 max-w-xl ml-auto">كاشلي هي المنصة العربية المبتكرة لإدارة أصولك، وصناديقك الاستثمارية، وتوزيع حصص الشركاء، مع ربط آلي كامل وويب هوكس لحظية لمبيعاتك ومصاريف شبكتك.</p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-start">
                    <a href="{{ route('register') }}" class="px-10 py-4 bg-indigo-600 text-white rounded-2xl font-black text-lg shadow-xl shadow-indigo-500/30 hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                        ابدأ رحلتك مجاناً
                        <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                    <a href="#simulator" class="px-10 py-4 bg-white border border-slate-200 text-gray-900 rounded-2xl font-black text-lg shadow-sm hover:bg-slate-50 transition-all flex items-center justify-center">جرّب الحاسبة التفاعلية</a>
                </div>

                <div class="mt-12 flex items-center gap-6 justify-start text-gray-400 font-bold text-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                        لا حاجة لبطاقة ائتمان
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                        ربط خارجي في ثوانٍ
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="absolute -inset-4 bg-indigo-500/10 rounded-[3rem] blur-3xl"></div>
                <!-- Interactive Webhook mockup screen -->
                <div class="relative bg-slate-900 rounded-[2.5rem] shadow-2xl border-[6px] border-slate-800 text-white overflow-hidden p-6 font-sans">
                    <div class="flex items-center justify-between border-b border-white/5 pb-4 mb-6">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        </div>
                        <span class="text-[10px] font-black text-white/40 tracking-widest uppercase">Kashly Live Sync Simulator</span>
                    </div>

                    <div class="space-y-6 text-right">
                        <div>
                            <p class="text-[10px] font-black text-white/40 uppercase tracking-widest">إجمالي سيولة الصندوق</p>
                            <p class="text-4xl font-black tracking-tight text-white mt-1 transition-all duration-500 flex items-center justify-end gap-2">
                                <span>$</span><span x-text="mockBalance.toFixed(2)"></span>
                            </p>
                        </div>

                        <div>
                            <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-3">سجل العمليات المزامنة لحظياً</p>
                            <div class="space-y-2">
                                <template x-for="tx in mockTransactions" :key="tx.desc + tx.amount">
                                    <div class="flex justify-between items-center bg-white/5 rounded-xl p-3 border border-white/5 hover:bg-white/10 transition-colors">
                                        <div class="text-left">
                                            <p class="font-black text-sm" :class="tx.type === 'income' ? 'text-emerald-400' : 'text-rose-400'" x-text="(tx.type === 'income' ? '+' : '-') + '$' + tx.amount.toFixed(2)"></p>
                                            <p class="text-[8px] text-white/30 font-bold mt-0.5" x-text="tx.time"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-white/95" x-text="tx.desc"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Webhook Action -->
                        <div class="bg-indigo-600/10 border border-indigo-500/20 rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="text-right">
                                <p class="text-[10px] font-black text-indigo-400">محاكاة ربط الويب هوك</p>
                                <p class="text-xs font-bold text-white/70 mt-1">اضغط لمحاكاة عملية دفع قادمة من MadaaQ</p>
                            </div>
                            <button @click="sendWebhook()" 
                                    :disabled="webhookSent"
                                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black text-xs transition-all shadow-md active:scale-95 flex items-center gap-2">
                                <span x-show="!webhookSent">⚡ إرسال دفعـة</span>
                                <span x-show="webhookSent" class="flex items-center gap-1.5">
                                    <span class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                    جاري المعالجة...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dynamic Brand Stats -->
        <section class="max-w-7xl mx-auto px-6 mt-32 grid grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm text-center hover:shadow-md transition-shadow">
                <div class="text-4xl font-black text-indigo-600 mb-2">+150,000</div>
                <div class="text-xs font-black text-slate-400 uppercase tracking-widest">حركة مالية مزامنة</div>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm text-center hover:shadow-md transition-shadow">
                <div class="text-4xl font-black text-emerald-600 mb-2">+800</div>
                <div class="text-xs font-black text-slate-400 uppercase tracking-widest">محفظة وصندوق استثماري</div>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm text-center hover:shadow-md transition-shadow">
                <div class="text-4xl font-black text-rose-600 mb-2">99.99%</div>
                <div class="text-xs font-black text-slate-400 uppercase tracking-widest">دقة حسابات الشركاء والأصول</div>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm text-center hover:shadow-md transition-shadow">
                <div class="text-4xl font-black text-indigo-600 mb-2">24/7</div>
                <div class="text-xs font-black text-slate-400 uppercase tracking-widest">مزامنة سحابية لحظية</div>
            </div>
        </section>
    </main>

    <!-- Premium Features Showcase -->
    <section id="features" class="py-32 bg-white relative">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-20">
                <h2 class="text-4xl lg:text-5xl font-black text-slate-900 mb-6">كل ما تحتاجه لإدارة وتتبع ثروتك</h2>
                <p class="text-lg text-slate-400 max-w-2xl mx-auto font-bold leading-relaxed">من تتبع الديون والسيولة النقدية اليومية إلى إدارة استثمارات معقدة مع شركاء متعددين وحسابات أصول متكاملة.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="group p-10 bg-slate-50 rounded-[3rem] hover:bg-white hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500 border border-transparent hover:border-indigo-50">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-3xl shadow-sm mb-8 group-hover:scale-110 transition-transform">👥</div>
                    <h3 class="text-2xl font-black text-slate-900 mb-4">إدارة الصناديق والشركاء</h3>
                    <p class="text-slate-400 leading-relaxed font-bold">نظام متطور لتوزيع الحصص التأسيسية، تتبع مساهمات الشركاء، وتوليد تقارير الأرباح والخسائر لكل شريك تلقائياً.</p>
                </div>

                <!-- Feature 2 -->
                <div class="group p-10 bg-slate-50 rounded-[3rem] hover:bg-white hover:shadow-2xl hover:shadow-emerald-500/10 transition-all duration-500 border border-transparent hover:border-emerald-50">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-3xl shadow-sm mb-8 group-hover:scale-110 transition-transform">🔌</div>
                    <h3 class="text-2xl font-black text-slate-900 mb-4">تكامل آلي خارجي (API)</h3>
                    <p class="text-slate-400 leading-relaxed font-bold">تكامل فوري وآمن مع Shopify و MadaaQ و WHMCS لاستقبال وتصنيف العمليات تلقائياً وتحديث أرصدة الخزينة.</p>
                </div>

                <!-- Feature 3 -->
                <div class="group p-10 bg-slate-50 rounded-[3rem] hover:bg-white hover:shadow-2xl hover:shadow-rose-500/10 transition-all duration-500 border border-transparent hover:border-rose-50">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-3xl shadow-sm mb-8 group-hover:scale-110 transition-transform">⚖️</div>
                    <h3 class="text-2xl font-black text-slate-900 mb-4">إدارة الديون والالتزامات</h3>
                    <p class="text-slate-400 leading-relaxed font-bold">تتبع متكامل للمستحقات والديون وخطط السداد الجزئي مع تنبيهات ذكية لآجال الاستحقاق والتسديد الفعلي.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Simulator Section -->
    <section id="simulator" class="py-32 bg-slate-50 relative overflow-hidden">
        <div class="absolute -right-32 -top-32 w-96 h-96 bg-indigo-500/5 rounded-full blur-[120px]"></div>
        <div class="absolute -left-32 -bottom-32 w-96 h-96 bg-emerald-500/5 rounded-full blur-[120px]"></div>
        
        <div class="max-w-4xl mx-auto px-6 relative z-10">
            <div class="text-center mb-16">
                <span class="text-xs font-black text-indigo-600 uppercase tracking-widest bg-indigo-50 px-3.5 py-1.5 rounded-full">محاكاة الاستثمار والنمو</span>
                <h2 class="text-4xl font-black text-slate-900 mt-6 mb-4">احسب نمو استثماراتك المستهدفة</h2>
                <p class="text-sm text-slate-400 font-bold">استخدم حاسبتنا التفاعلية لتقدير أرباح استثمارك السنوي المتوقع</p>
            </div>

            <div class="bg-white rounded-[3rem] border border-slate-100 p-8 shadow-xl">
                <div class="grid md:grid-cols-2 gap-10">
                    <!-- Inputs -->
                    <div class="space-y-6">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-black text-slate-400 uppercase tracking-widest">رأس المال المستثمر</span>
                                <span class="text-base font-black text-indigo-600" x-text="'$' + startingCapital.toLocaleString()"></span>
                            </div>
                            <input type="range" min="1000" max="100000" step="1000" x-model="startingCapital" class="w-full accent-indigo-600 cursor-pointer h-2 bg-slate-100 rounded-lg appearance-none">
                            <div class="flex justify-between text-[10px] text-slate-400 font-bold mt-1.5">
                                <span>$1,000</span>
                                <span>$100,000</span>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-black text-slate-400 uppercase tracking-widest">معدل العائد الشهري المتوقع</span>
                                <span class="text-base font-black text-emerald-600" x-text="monthlyGrowth + '%'"></span>
                            </div>
                            <input type="range" min="1" max="25" step="1" x-model="monthlyGrowth" class="w-full accent-emerald-600 cursor-pointer h-2 bg-slate-100 rounded-lg appearance-none">
                            <div class="flex justify-between text-[10px] text-slate-400 font-bold mt-1.5">
                                <span>1% شهرياً</span>
                                <span>25% شهرياً</span>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-black text-slate-400 uppercase tracking-widest">فترة الاستثمار (بالسنوات)</span>
                                <span class="text-base font-black text-indigo-600" x-text="years + ' سنوات'"></span>
                            </div>
                            <input type="range" min="1" max="10" step="1" x-model="years" class="w-full accent-indigo-600 cursor-pointer h-2 bg-slate-100 rounded-lg appearance-none">
                            <div class="flex justify-between text-[10px] text-slate-400 font-bold mt-1.5">
                                <span>سنة واحدة</span>
                                <span>10 سنوات</span>
                            </div>
                        </div>
                    </div>

                    <!-- Results Output -->
                    <div class="bg-slate-900 rounded-[2rem] p-6 text-white flex flex-col justify-between text-right relative overflow-hidden">
                        <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-white/5 rounded-full blur-xl"></div>
                        
                        <div class="space-y-4 relative z-10">
                            <p class="text-[10px] font-black text-white/40 uppercase tracking-widest leading-none">القيمة الإجمالية المقدرة لاستثمارك</p>
                            <p class="text-4xl font-black tracking-tight text-white mt-2 leading-none"
                               x-text="'$' + Math.round(startingCapital * Math.pow((1 + (monthlyGrowth/100)), (years * 12))).toLocaleString()">
                            </p>
                            
                            <div class="h-px bg-white/10 my-4"></div>
                            
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-black text-white/50">رأس المال التأسيسي:</span>
                                <span class="font-black text-white/90" x-text="'$' + startingCapital.toLocaleString()"></span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-black text-emerald-400">صافي الأرباح المتوقعة:</span>
                                <span class="font-black text-emerald-400" 
                                      x-text="'+$' + Math.round(startingCapital * Math.pow((1 + (monthlyGrowth/100)), (years * 12)) - startingCapital).toLocaleString()">
                                </span>
                            </div>
                        </div>

                        <div class="pt-6 relative z-10">
                            <a href="{{ route('register') }}" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black text-sm shadow-lg shadow-indigo-500/20 hover:scale-[1.02] active:scale-95 transition-all text-center block leading-none">
                                ابدأ استثمارك الآن مع كاشلي
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Carousel Section -->
    <section id="testimonials" class="py-32 bg-white relative">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-20">
                <span class="text-xs font-black text-indigo-600 uppercase tracking-widest bg-indigo-50 px-3.5 py-1.5 rounded-full">آراء وقصص نجاح عملائنا</span>
                <h2 class="text-4xl font-black text-slate-900 mt-6 mb-4">ماذا يقول كبار المستثمرين عن كاشلي؟</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-slate-50/50 p-8 rounded-[2.5rem] border border-slate-100 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-xl font-black">👤</div>
                        <div class="text-right">
                            <h4 class="font-black text-slate-900 text-base leading-none">م. أمجد الخولاني</h4>
                            <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">مستثمر تقني - مؤسس ومدير شبكات MadaaQ</p>
                        </div>
                    </div>
                    <p class="text-slate-500 font-bold text-xs leading-relaxed">
                        "لقد ساعدتنا منصة كاشلي في أتمتة وتوحيد كافة العمليات المالية واشتراكات عملائنا مع MadaaQ بشكل لحظي وموثوق بنسبة 100%. نظام توزيع الشركاء وحل مشاكل أسعار الصرف فائق الذكاء."
                    </p>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-slate-50/50 p-8 rounded-[2.5rem] border border-slate-100 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center text-xl font-black">👤</div>
                        <div class="text-right">
                            <h4 class="font-black text-slate-900 text-base leading-none">أ. رائد حمود</h4>
                            <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">رائد أعمال - مدير محفظة أصول الياسمين</p>
                        </div>
                    </div>
                    <p class="text-slate-500 font-bold text-xs leading-relaxed">
                        "لم أعد بحاجة لجداول البيانات المعقدة! بفضل كاشلي، أستطيع متابعة وتقييم أصولنا غير النقدية والعقارية وتصدير تقارير محاسبية فورية وتوزيع الأرباح على الشركاء بنقرة زر."
                    </p>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-slate-50/50 p-8 rounded-[2.5rem] border border-slate-100 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-rose-100 rounded-full flex items-center justify-center text-xl font-black">👤</div>
                        <div class="text-right">
                            <h4 class="font-black text-slate-900 text-base leading-none">م. علاء الكيلاني</h4>
                            <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">مستثمر عقاري وشريك مساهم</p>
                        </div>
                    </div>
                    <p class="text-slate-500 font-bold text-xs leading-relaxed">
                        "أعجبتني جداً ميزة الشفافية التي يقدمها كاشلي؛ فكل شريك يمتلك حساباً مخصصاً يرى فيه حصته وأرباحه الموزعة الفردية بالدقيقة مع تفاصيل كامل عمليات الصندوق المالي."
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Brand Integrations Showcase -->
    <section id="integrations" class="py-20 bg-indigo-600 overflow-hidden relative">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
        <div class="max-w-7xl mx-auto px-6 text-center relative z-10">
            <h2 class="text-3xl lg:text-4xl font-black text-white mb-12">يدعم الربط الخارجي والويب هوكس لكل منصاتك</h2>
            <div class="flex flex-wrap justify-center items-center gap-12 opacity-80 hover:opacity-100 transition-opacity">
                <div class="flex items-center gap-3 text-white">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center font-black">M</div>
                    <span class="text-xl font-black uppercase tracking-tighter">MadaaQ Webhook</span>
                </div>
                <div class="flex items-center gap-3 text-white">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center font-black">S</div>
                    <span class="text-xl font-black uppercase tracking-tighter">Shopify API</span>
                </div>
                <div class="flex items-center gap-3 text-white">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center font-black">W</div>
                    <span class="text-xl font-black uppercase tracking-tighter">WHMCS</span>
                </div>
                <div class="flex items-center gap-3 text-white">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center font-black">S</div>
                    <span class="text-xl font-black uppercase tracking-tighter">Stripe Sync</span>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-32">
        <div class="max-w-5xl mx-auto px-6 bg-slate-900 rounded-[4rem] p-16 text-center relative overflow-hidden shadow-2xl">
            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-600/20 blur-[100px] -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-emerald-600/20 blur-[100px] translate-y-1/2 -translate-x-1/2"></div>
            
            <h2 class="text-4xl lg:text-6xl font-black text-white mb-8 relative z-10 leading-tight">جاهز لضبط وإدارة ثروتك <br/>ومضاعفة أرباحك الاستثمارية؟</h2>
            <p class="text-xl text-slate-400 mb-12 relative z-10 max-w-xl mx-auto">انضم إلى مئات الشركاء والمستثمرين ومدراء الشبكات والصناديق الاستثمارية الذين يستخدمون كاشلي يومياً بنجاح.</p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center relative z-10">
                <a href="{{ route('register') }}" class="px-12 py-5 bg-indigo-600 text-white rounded-3xl font-black text-xl shadow-xl shadow-indigo-500/20 hover:scale-105 transition-all">سجل مجاناً الآن وابدأ الاستخدام</a>
                <a href="#features" class="px-12 py-5 bg-white/10 text-white rounded-3xl font-black text-xl hover:bg-white/20 transition-all">تعرف على المزيد</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-20 border-t border-slate-100">
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
                <div class="text-slate-400 text-sm font-bold">
                    © {{ date('Y') }} كاشلي. جميع الحقوق محفوظة.
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
