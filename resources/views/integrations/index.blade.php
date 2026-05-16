<x-app-layout>
    <div class="py-12 px-6">
        <div class="max-w-7xl mx-auto space-y-12">
            
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
                <div>
                    <h2 class="text-5xl font-black text-slate-900 tracking-tight">الربط الآلي (Integrations)</h2>
                    <p class="text-slate-500 font-bold mt-3 text-lg">اربط متجرك أو نظام فواتيرك لاستيراد العمليات المالية تلقائياً وبكل أمان.</p>
                </div>
                <div class="flex items-center gap-4">
                    <button class="bg-white border-2 border-slate-100 text-slate-900 px-10 py-5 rounded-[2.5rem] text-lg font-black shadow-lg hover:bg-slate-50 transition-all">دليل المطورين (API)</button>
                </div>
            </div>

            <!-- Provider Selection -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Shopify -->
                <div class="premium-card bg-white p-12 border-2 border-slate-100 hover:border-emerald-200 transition-all duration-500 group shadow-xl">
                    <div class="w-24 h-24 bg-emerald-50 rounded-[2.5rem] flex items-center justify-center mb-10 group-hover:scale-110 transition-transform duration-500 overflow-hidden p-5 border-2 border-white shadow-lg">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/e/e1/Shopify_Logo.png" class="w-full h-full object-contain" alt="Shopify">
                    </div>
                    <h4 class="text-3xl font-black text-slate-900 mb-4 tracking-tight group-hover:text-emerald-600 transition-colors">Shopify</h4>
                    <p class="text-base text-slate-500 font-bold leading-relaxed mb-10">مزامنة مبيعات متجرك الإلكتروني تلقائياً مع صناديق الاستثمار الخاصة بك.</p>
                    <button class="w-full bg-emerald-600 text-white py-5 rounded-2xl text-xs font-black uppercase tracking-widest shadow-xl shadow-emerald-500/30 hover:bg-emerald-700 transition-all">تفعيل التكامل</button>
                </div>

                <!-- ShamCash Integration Card -->
                <div x-data="{ 
                    showQrModal: false, 
                    qrPayload: '', 
                    sessionId: '', 
                    polling: false,
                    status: 'pending',
                    async startLink() {
                        this.showQrModal = true;
                        this.status = 'pending';
                        this.qrPayload = '';
                        try {
                            let response = await fetch('{{ route('shamcash.initiate') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            });
                            let data = await response.json();
                            this.qrPayload = data.qr_payload;
                            this.sessionId = data.id;
                            this.startPolling();
                        } catch (e) {
                            alert('خطأ في الاتصال بالخدمة');
                        }
                    },
                    startPolling() {
                        this.polling = true;
                        let interval = setInterval(async () => {
                            if (!this.polling) {
                                clearInterval(interval);
                                return;
                            }
                            let response = await fetch('/shamcash/status/' + this.sessionId);
                            let data = await response.json();
                            if (data.status === 'completed') {
                                this.status = 'completed';
                                this.polling = false;
                                clearInterval(interval);
                                window.location.href = '{{ route('integrations.index') }}?success=shamcash';
                            }
                        }, 3000);
                    }
                }" class="premium-card bg-white p-12 border-2 border-slate-100 hover:border-indigo-200 transition-all duration-500 group shadow-xl">
                    <div class="w-24 h-24 bg-indigo-50 rounded-[2.5rem] flex items-center justify-center mb-10 group-hover:scale-110 transition-transform duration-500 overflow-hidden p-4 text-5xl border-2 border-white shadow-lg">
                        💳
                    </div>
                    <h4 class="text-3xl font-black text-slate-900 mb-4 tracking-tight group-hover:text-indigo-600 transition-colors">ShamCash</h4>
                    <p class="text-base text-slate-500 font-bold leading-relaxed mb-10">ربط احترافي مع حسابات شام كاش لاستيراد التحويلات الواردة آلياً وبشكل لحظي.</p>
                    <button @click="startLink()" class="w-full bg-indigo-600 text-white py-5 rounded-2xl text-xs font-black uppercase tracking-widest shadow-xl shadow-indigo-500/30 hover:bg-indigo-700 transition-all">
                        {{ auth()->user()->shamcash_token ? 'تحديث الربط (QR)' : 'ربط عبر QR Code' }}
                    </button>

                    <!-- QR Code Modal -->
                    <div x-show="showQrModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/80 backdrop-blur-xl" x-cloak x-transition>
                        <div class="bg-white rounded-[4rem] w-full max-w-sm p-12 shadow-2xl relative text-center border-2 border-white/20" @click.away="showQrModal = false; polling = false;">
                            <h3 class="text-2xl font-black text-slate-900 mb-8">ربط حساب شام كاش</h3>
                            
                            <div class="bg-slate-50 p-8 rounded-[3rem] mb-8 inline-block border-2 border-slate-100 shadow-inner">
                                <template x-if="qrPayload">
                                    <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(qrPayload)" class="w-56 h-56 mx-auto rounded-2xl" alt="Scan QR">
                                </template>
                                <template x-if="!qrPayload">
                                    <div class="w-56 h-56 flex items-center justify-center">
                                        <div class="w-10 h-10 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                                    </div>
                                </template>
                            </div>

                            <p class="text-xs font-black text-slate-400 leading-relaxed px-4 uppercase tracking-widest">
                                افتح تطبيق شام كاش على جهازك، اذهب إلى "الأجهزة المرتبطة" واختر "مسح QR"
                            </p>

                            <div class="mt-10 flex items-center justify-center gap-3">
                                <div :class="status === 'completed' ? 'bg-emerald-500' : 'bg-amber-500'" class="w-3 h-3 rounded-full animate-pulse shadow-lg shadow-current"></div>
                                <span class="text-xs font-black uppercase tracking-widest text-slate-500" x-text="status === 'completed' ? 'تم الربط بنجاح!' : 'بانتظار التأكيد من التطبيق...'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MadaaQ Integration Card -->
                <div class="premium-card bg-white p-12 border-2 border-slate-100 hover:border-orange-200 transition-all duration-500 group shadow-xl">
                    <div class="w-24 h-24 bg-orange-50 rounded-[2.5rem] flex items-center justify-center mb-10 group-hover:scale-110 transition-transform duration-500 overflow-hidden p-4 text-5xl border-2 border-white shadow-lg">
                        📡
                    </div>
                    <h4 class="text-3xl font-black text-slate-900 mb-4 tracking-tight group-hover:text-orange-600 transition-colors">MadaaQ</h4>
                    <p class="text-base text-slate-500 font-bold leading-relaxed mb-10">تكامل مباشر مع منصة MadaaQ لاستقبال تحصيلات المشتركين وتوثيقها في صناديقك المالية.</p>
                    <button class="w-full bg-orange-600 text-white py-5 rounded-2xl text-xs font-black uppercase tracking-widest shadow-xl shadow-orange-500/30 hover:bg-orange-700 transition-all">إعداد الـ Webhook</button>
                </div>

                <!-- WHMCS -->
                <div class="premium-card bg-white p-12 border-2 border-slate-100 hover:border-blue-200 transition-all duration-500 group shadow-xl">
                    <div class="w-24 h-24 bg-blue-50 rounded-[2.5rem] flex items-center justify-center mb-10 group-hover:scale-110 transition-transform duration-500 overflow-hidden border-2 border-white shadow-lg">
                        <span class="text-3xl font-black text-blue-600 italic">WHMCS</span>
                    </div>
                    <h4 class="text-3xl font-black text-slate-900 mb-4 tracking-tight group-hover:text-blue-600 transition-colors">WHMCS</h4>
                    <p class="text-base text-slate-500 font-bold leading-relaxed mb-10">تتبع فواتير عملائك في أنظمة الاستضافة والخدمات السحابية بشكل لحظي.</p>
                    <button class="w-full bg-blue-600 text-white py-5 rounded-2xl text-xs font-black uppercase tracking-widest shadow-xl shadow-blue-500/30 hover:bg-blue-700 transition-all">تفعيل التكامل</button>
                </div>
            </div>

            <!-- Active Connections List -->
            <div class="space-y-6">
                <h3 class="text-2xl font-black text-gray-900 tracking-tight px-4">التكاملات النشطة حالياً</h3>
                <div class="bg-white rounded-[4rem] border border-gray-50 shadow-sm overflow-hidden">
                    
                    @if(auth()->user()->shamcash_token)
                        <!-- ShamCash Active -->
                        <div class="p-10 flex flex-col md:flex-row items-center justify-between border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                            <div class="flex items-center gap-6 w-full">
                                <div class="w-16 h-16 bg-white shadow-sm rounded-2xl flex items-center justify-center text-2xl border border-gray-100">
                                    💳
                                </div>
                                <div>
                                    <h5 class="text-xl font-black text-gray-900">ShamCash API Integration</h5>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mr-1">المزود:</span>
                                        <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-3 py-1 rounded-lg uppercase">ShamCash Platform</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-6 mt-6 md:mt-0 w-full md:w-auto justify-end">
                                <div class="text-left">
                                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">حالة المزامنة</p>
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                        <span class="text-xs font-black text-emerald-600 uppercase">متصل ومزامن</span>
                                    </div>
                                </div>
                                <div class="h-10 w-px bg-gray-100"></div>
                                <form action="{{ route('shamcash.saveToken') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="token" value="">
                                    <button type="submit" class="text-[10px] font-black text-rose-500 uppercase tracking-widest hover:underline">إزالة الربط</button>
                                </form>
                            </div>
                        </div>
                    @endif

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
                        @if(!auth()->user()->shamcash_token)
                            <div class="p-24 text-center">
                                <div class="text-6xl mb-6">🏜️</div>
                                <p class="text-gray-400 font-bold text-xl">لا توجد تكاملات نشطة حالياً.</p>
                                <p class="text-gray-300 font-bold text-sm mt-2">اختر أحد المزودين أعلاه لبدء الأتمتة.</p>
                            </div>
                        @endif
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
