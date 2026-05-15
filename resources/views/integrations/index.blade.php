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
                }" class="bg-white p-10 rounded-[4rem] border border-gray-50 shadow-sm hover:shadow-2xl hover:shadow-indigo-500/5 transition-all group">
                    <div class="w-20 h-20 bg-indigo-50 rounded-[2rem] flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-500 overflow-hidden p-3 text-3xl">
                        💳
                    </div>
                    <h4 class="text-2xl font-black text-gray-900 mb-3 tracking-tight">ShamCash</h4>
                    <p class="text-sm text-gray-500 font-bold leading-relaxed mb-8">ربط احترافي مع حسابات شام كاش لاستيراد التحويلات الواردة آلياً وبشكل لحظي.</p>
                    <button @click="startLink()" class="w-full bg-indigo-600 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 transition-all">
                        {{ auth()->user()->shamcash_token ? 'تحديث الربط (QR)' : 'ربط عبر QR Code' }}
                    </button>

                    <!-- QR Code Modal -->
                    <div x-show="showQrModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
                        <div class="bg-white rounded-[4rem] w-full max-w-sm p-10 shadow-2xl relative text-center" @click.away="showQrModal = false; polling = false;">
                            <h3 class="text-xl font-black text-gray-900 mb-6">ربط حساب شام كاش</h3>
                            
                            <div class="bg-gray-50 p-6 rounded-[2.5rem] mb-6 inline-block">
                                <template x-if="qrPayload">
                                    <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(qrPayload)" class="w-48 h-48 mx-auto" alt="Scan QR">
                                </template>
                                <template x-if="!qrPayload">
                                    <div class="w-48 h-48 flex items-center justify-center">
                                        <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                                    </div>
                                </template>
                            </div>

                            <p class="text-[11px] font-bold text-gray-500 leading-relaxed px-4">
                                افتح تطبيق شام كاش على جهازك، اذهب إلى "الأجهزة المرتبطة" واختر "مسح QR"
                            </p>

                            <div class="mt-8 flex items-center justify-center gap-2">
                                <div :class="status === 'completed' ? 'bg-emerald-500' : 'bg-amber-500'" class="w-2 h-2 rounded-full animate-pulse"></div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400" x-text="status === 'completed' ? 'تم الربط بنجاح!' : 'بانتظار التأكيد من التطبيق...'"></span>
                            </div>
                        </div>
                    </div>
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
