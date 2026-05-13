<x-app-layout>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="flex justify-between items-center px-4">
                <div>
                    <h2 class="text-3xl font-black text-gray-900">الربط الآلي</h2>
                    <p class="text-gray-500 text-sm mt-1">تكامل مع المنصات الخارجية لاستيراد العمليات تلقائياً.</p>
                </div>
                <div class="flex space-x-3 space-x-reverse">
                    <button class="bg-white border border-gray-200 text-gray-900 px-6 py-3 rounded-2xl text-sm font-black shadow-sm">دليل الربط</button>
                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl text-sm font-black shadow-lg shadow-indigo-500/20">إضافة تكامل</button>
                </div>
            </div>

            <!-- Available Providers -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="spendee-card p-8 border-2 border-transparent hover:border-indigo-100 transition-all">
                    <div class="w-16 h-16 bg-white shadow-sm rounded-2xl flex items-center justify-center mb-6 overflow-hidden">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/e/e1/Shopify_Logo.png" class="w-10 h-10 object-contain" alt="Shopify">
                    </div>
                    <h4 class="text-xl font-black text-gray-900 mb-2">Shopify</h4>
                    <p class="text-xs text-gray-500 mb-6">استيراد المبيعات والطلبات من متجرك على شوبيفاي مباشرة.</p>
                    <button class="w-full bg-gray-50 text-gray-900 py-3 rounded-xl text-xs font-black hover:bg-indigo-600 hover:text-white transition-all">تفعيل الآن</button>
                </div>

                <div class="spendee-card p-8 border-2 border-transparent hover:border-blue-100 transition-all">
                    <div class="w-16 h-16 bg-white shadow-sm rounded-2xl flex items-center justify-center mb-6 overflow-hidden">
                        <span class="text-2xl font-black text-blue-600 italic">WHMCS</span>
                    </div>
                    <h4 class="text-xl font-black text-gray-900 mb-2">WHMCS</h4>
                    <p class="text-xs text-gray-500 mb-6">مزامنة فواتير الاستضافة والخدمات السحابية تلقائياً.</p>
                    <button class="w-full bg-gray-50 text-gray-900 py-3 rounded-xl text-xs font-black hover:bg-blue-600 hover:text-white transition-all">تفعيل الآن</button>
                </div>

                <div class="spendee-card p-8 border-2 border-transparent hover:border-emerald-100 transition-all">
                    <div class="w-16 h-16 bg-white shadow-sm rounded-2xl flex items-center justify-center mb-6 overflow-hidden">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/ba/Stripe_Logo%2C_revised_2016.svg/1200px-Stripe_Logo%2C_revised_2016.svg.png" class="w-12 h-12 object-contain" alt="Stripe">
                    </div>
                    <h4 class="text-xl font-black text-gray-900 mb-2">Stripe</h4>
                    <p class="text-xs text-gray-500 mb-6">تسجيل جميع دفعات العملاء الواردة عبر بوابات سترايب.</p>
                    <button class="w-full bg-gray-50 text-gray-900 py-3 rounded-xl text-xs font-black hover:bg-emerald-600 hover:text-white transition-all">قريباً</button>
                </div>
            </div>

            <!-- Active Integrations -->
            <div class="space-y-6">
                <h3 class="text-xl font-black text-gray-900 px-4">التكاملات النشطة</h3>
                <div class="spendee-card overflow-hidden">
                    @forelse($integrations as $integration)
                        <div class="p-6 flex items-center justify-between border-b border-gray-50 last:border-0">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-lg ml-4">
                                    {{ $integration->provider == 'shopify' ? '🛍️' : '☁️' }}
                                </div>
                                <div>
                                    <h5 class="text-sm font-black text-gray-900">{{ $integration->name }}</h5>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase">المستهدف: {{ $integration->target->name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4 space-x-reverse">
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black rounded-lg uppercase">نشط</span>
                                <button class="text-gray-400 hover:text-rose-600 transition-colors">إيقاف</button>
                            </div>
                        </div>
                    @empty
                        <div class="p-20 text-center">
                            <p class="text-gray-400 font-bold">لا يوجد تكاملات نشطة حالياً.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
