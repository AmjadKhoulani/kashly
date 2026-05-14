<x-app-layout>
    <div class="py-12 px-6">
        <div class="max-w-7xl mx-auto space-y-12">
            
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h2 class="text-4xl font-black text-gray-900 tracking-tight">الربط الآلي (Integrations)</h2>
                    <p class="text-gray-500 font-bold mt-2">اربط متجرك أو نظام فواتيرك لاستيراد العمليات المالية تلقائياً وبكل أمان.</p>
                </div>
                <div class="flex items-center gap-4">
                    <button class="bg-white border border-gray-100 text-gray-900 px-8 py-4 rounded-[2rem] text-sm font-black shadow-sm hover:bg-gray-50 transition-all">دليل المطورين (API)</button>
                </div>
            </div>

            <!-- Provider Selection -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Shopify -->
                <div class="bg-white p-10 rounded-[4rem] border border-gray-50 shadow-sm hover:shadow-2xl hover:shadow-emerald-500/5 transition-all group">
                    <div class="w-20 h-20 bg-emerald-50 rounded-[2rem] flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-500 overflow-hidden p-4">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/e/e1/Shopify_Logo.png" class="w-full h-full object-contain" alt="Shopify">
                    </div>
                    <h4 class="text-2xl font-black text-gray-900 mb-3 tracking-tight">Shopify</h4>
                    <p class="text-sm text-gray-500 font-bold leading-relaxed mb-8">مزامنة مبيعات متجرك الإلكتروني تلقائياً مع صناديق الاستثمار الخاصة بك.</p>
                    <button class="w-full bg-emerald-600 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-emerald-500/20 hover:bg-emerald-700 transition-all">تفعيل التكامل</button>
                </div>

                <!-- WHMCS -->
                <div class="bg-white p-10 rounded-[4rem] border border-gray-50 shadow-sm hover:shadow-2xl hover:shadow-blue-500/5 transition-all group">
                    <div class="w-20 h-20 bg-blue-50 rounded-[2rem] flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-500 overflow-hidden">
                        <span class="text-2xl font-black text-blue-600 italic">WHMCS</span>
                    </div>
                    <h4 class="text-2xl font-black text-gray-900 mb-3 tracking-tight">WHMCS</h4>
                    <p class="text-sm text-gray-500 font-bold leading-relaxed mb-8">تتبع فواتير عملائك في أنظمة الاستضافة والخدمات السحابية بشكل لحظي.</p>
                    <button class="w-full bg-blue-600 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-blue-500/20 hover:bg-blue-700 transition-all">تفعيل التكامل</button>
                </div>

                <!-- Stripe -->
                <div class="bg-white p-10 rounded-[4rem] border border-gray-50 shadow-sm hover:shadow-2xl hover:shadow-indigo-500/5 transition-all group opacity-60">
                    <div class="w-20 h-20 bg-indigo-50 rounded-[2rem] flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-500 overflow-hidden p-4">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/ba/Stripe_Logo%2C_revised_2016.svg/1200px-Stripe_Logo%2C_revised_2016.svg.png" class="w-full h-full object-contain" alt="Stripe">
                    </div>
                    <h4 class="text-2xl font-black text-gray-900 mb-3 tracking-tight">Stripe</h4>
                    <p class="text-sm text-gray-500 font-bold leading-relaxed mb-8">قريباً: ربط مباشر مع حساب سترايب لتسجيل الدفعات الواردة عالمياً.</p>
                    <button class="w-full bg-gray-100 text-gray-400 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest cursor-not-allowed">قريباً</button>
                </div>
            </div>

            <!-- Active Connections List -->
            <div class="space-y-6">
                <h3 class="text-2xl font-black text-gray-900 tracking-tight px-4">التكاملات النشطة حالياً</h3>
                <div class="bg-white rounded-[4rem] border border-gray-50 shadow-sm overflow-hidden">
                    @forelse($integrations as $integration)
                        <div class="p-10 flex flex-col md:flex-row items-center justify-between border-b border-gray-50 last:border-0 hover:bg-gray-50/50 transition-colors">
                            <div class="flex items-center gap-6 w-full">
                                <div class="w-16 h-16 bg-white shadow-sm rounded-2xl flex items-center justify-center text-2xl border border-gray-50">
                                    {{ $integration->provider == 'shopify' ? '🛍️' : '☁️' }}
                                </div>
                                <div>
                                    <h5 class="text-xl font-black text-gray-900">{{ $integration->name }}</h5>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mr-1">المستهدف:</span>
                                        <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-3 py-1 rounded-lg uppercase">{{ $integration->target->name ?? 'غير محدد' }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-6 mt-6 md:mt-0 w-full md:w-auto justify-end">
                                <div class="text-left">
                                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">حالة الربط</p>
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                        <span class="text-xs font-black text-emerald-600 uppercase">نشط ويعمل</span>
                                    </div>
                                </div>
                                <div class="h-10 w-px bg-gray-100"></div>
                                <button class="text-[10px] font-black text-rose-500 uppercase tracking-widest hover:underline">تعطيل التكامل</button>
                            </div>
                        </div>
                    @empty
                        <div class="p-24 text-center">
                            <div class="text-6xl mb-6">🏜️</div>
                            <p class="text-gray-400 font-bold text-xl">لا توجد تكاملات نشطة حالياً.</p>
                            <p class="text-gray-300 font-bold text-sm mt-2">اختر أحد المزودين أعلاه لبدء الأتمتة.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Security Notice -->
            <div class="bg-gray-900 p-12 rounded-[4rem] relative overflow-hidden">
                <div class="absolute right-0 top-0 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl"></div>
                <div class="relative z-10 flex flex-col md:flex-row items-center gap-10">
                    <div class="w-20 h-20 bg-indigo-500 rounded-[2rem] flex items-center justify-center text-4xl shadow-2xl shadow-indigo-500/20">🛡️</div>
                    <div class="text-right">
                        <h4 class="text-2xl font-black text-white mb-3">خصوصية وأمان البيانات</h4>
                        <p class="text-indigo-200 font-bold leading-relaxed max-w-2xl">
                            نحن نستخدم تقنيات التشفير المتقدمة وبروتوكولات Webhooks الآمنة لضمان وصول بيانات مبيعاتك فقط إلى صناديقك المحددة دون مشاركتها مع أي طرف ثالث.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
