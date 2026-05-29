<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/20">
        
        <!-- Sticky Header -->
        <div class="bg-white/95 border-b border-slate-100 sticky top-0 z-40 backdrop-blur-xl py-6 px-6">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight">الربط الآلي (Integrations)</h2>
                    <p class="text-slate-500 font-bold mt-2 text-sm">اربط متجرك أو نظام فواتيرك لاستيراد العمليات المالية تلقائياً وبكل أمان.</p>
                </div>
                <div class="flex items-center gap-4">
                    <button class="px-5 py-2.5 bg-white border border-slate-200 text-slate-900 rounded-xl font-black text-sm shadow-sm hover:bg-slate-50 transition-all">دليل المطورين (API)</button>
                </div>
            </div>
        </div>

        <!-- Main Content Container -->
        <div class="max-w-7xl mx-auto px-6 py-10 space-y-10">

            <!-- Provider Selection Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                
                <!-- Shopify -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 hover:shadow-md transition-all flex flex-col justify-between group relative overflow-hidden">
                    <div>
                        <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-500 overflow-hidden p-3 border border-emerald-100 shadow-sm">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/e/e1/Shopify_Logo.png" class="w-full h-full object-contain" alt="Shopify">
                        </div>
                        <h4 class="text-xl font-black text-slate-900 mb-2 tracking-tight group-hover:text-emerald-600 transition-colors">Shopify</h4>
                        <p class="text-xs text-slate-500 font-bold leading-relaxed mb-6">مزامنة مبيعات متجرك الإلكتروني تلقائياً مع صناديق الاستثمار الخاصة بك.</p>
                    </div>
                    <button class="w-full px-5 py-2.5 bg-emerald-600 text-white rounded-xl font-black text-xs shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95 transition-all">تفعيل التكامل</button>
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
                }" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 hover:shadow-md transition-all flex flex-col justify-between group relative overflow-hidden">
                    <div>
                        <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-500 overflow-hidden text-3xl border border-indigo-100 shadow-sm">
                            💳
                        </div>
                        <h4 class="text-xl font-black text-slate-900 mb-2 tracking-tight group-hover:text-indigo-600 transition-colors">ShamCash</h4>
                        <p class="text-xs text-slate-500 font-bold leading-relaxed mb-6">ربط احترافي مع حسابات شام كاش لاستيراد التحويلات الواردة آلياً وبشكل لحظي.</p>
                    </div>
                    <button @click="startLink()" class="w-full px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-xs shadow-lg shadow-indigo-500/20 hover:scale-105 active:scale-95 transition-all">
                        {{ auth()->user()->shamcash_token ? 'تحديث الربط (QR)' : 'ربط عبر QR Code' }}
                    </button>

                    <!-- QR Code Modal -->
                    <div x-show="showQrModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-md" x-cloak x-transition>
                        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl text-right relative overflow-hidden border border-slate-100/50" @click.away="showQrModal = false; polling = false;">
                            
                            <!-- Sticky Header inside Modal -->
                            <div class="sticky top-0 bg-white/95 border-b border-slate-100 px-6 py-4 flex justify-between items-center z-10 backdrop-blur-md">
                                <h3 class="text-lg font-black text-slate-900">ربط حساب شام كاش</h3>
                                <button @click="showQrModal = false; polling = false;" class="text-slate-400 hover:text-slate-600 transition-all p-1.5 hover:bg-slate-50 rounded-xl border border-transparent hover:border-slate-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <!-- Modal Content -->
                            <div class="p-6 text-center space-y-6">
                                <div class="bg-slate-50 p-6 rounded-2xl inline-block border border-slate-100 shadow-inner">
                                    <template x-if="qrPayload">
                                        <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(qrPayload)" class="w-48 h-48 mx-auto rounded-xl" alt="Scan QR">
                                    </template>
                                    <template x-if="!qrPayload">
                                        <div class="w-48 h-48 flex items-center justify-center">
                                            <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                                        </div>
                                    </template>
                                </div>

                                <p class="text-xs font-bold text-slate-400 leading-relaxed px-4">
                                    افتح تطبيق شام كاش على جهازك، اذهب إلى "الأجهزة المرتبطة" واختر "مسح QR"
                                </p>

                                <div class="flex items-center justify-center gap-2 pt-2">
                                    <div :class="status === 'completed' ? 'bg-emerald-500' : 'bg-amber-500'" class="w-2.5 h-2.5 rounded-full animate-pulse shadow-lg shadow-current"></div>
                                    <span class="text-xs font-black text-slate-500" x-text="status === 'completed' ? 'تم الربط بنجاح!' : 'بانتظار التأكيد من التطبيق...'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MadaaQ Integration Card -->
                <div x-data="{ showMadaaqModal: false }" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 hover:shadow-md transition-all flex flex-col justify-between group relative overflow-hidden">
                    <div>
                        <div class="w-16 h-16 bg-orange-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-500 overflow-hidden text-3xl border border-orange-100 shadow-sm">
                            📡
                        </div>
                        <h4 class="text-xl font-black text-slate-900 mb-2 tracking-tight group-hover:text-orange-600 transition-colors">MadaaQ</h4>
                        <p class="text-xs text-slate-500 font-bold leading-relaxed mb-6">تكامل مباشر مع منصة MadaaQ لاستقبال تحصيلات المشتركين وتوثيقها في صناديقك المالية.</p>
                    </div>
                    
                    @php
                        $madaaqIntegration = $integrations->where('provider', 'madaaq')->first();
                    @endphp

                    @if($madaaqIntegration)
                        <button @click="showMadaaqModal = true" class="w-full px-5 py-2.5 bg-slate-100 text-slate-900 rounded-xl font-black text-xs shadow-sm hover:scale-105 active:scale-95 transition-all">عرض بيانات الـ Webhook</button>
                    @else
                        <button @click="showMadaaqModal = true" class="w-full px-5 py-2.5 bg-orange-600 text-white rounded-xl font-black text-xs shadow-lg shadow-orange-500/20 hover:scale-105 active:scale-95 transition-all">إعداد الـ Webhook</button>
                    @endif

                    <!-- MadaaQ Setup Modal -->
                    <div x-show="showMadaaqModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-md" x-cloak x-transition>
                        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl text-right relative overflow-hidden border border-slate-100/50" @click.away="showMadaaqModal = false">
                            
                            <!-- Sticky Header inside Modal -->
                            <div class="sticky top-0 bg-white/95 border-b border-slate-100 px-6 py-4 flex justify-between items-center z-10 backdrop-blur-md">
                                <h3 class="text-lg font-black text-slate-900">إعداد تكامل MadaaQ</h3>
                                <button @click="showMadaaqModal = false" class="text-slate-400 hover:text-slate-600 transition-all p-1.5 hover:bg-slate-50 rounded-xl border border-transparent hover:border-slate-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <!-- Modal Content -->
                            <div class="p-6 space-y-6">
                                @if($madaaqIntegration)
                                    <div class="space-y-4 text-right">
                                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                            <p class="text-[9px] font-black text-slate-450 uppercase tracking-widest mb-1">Webhook URL</p>
                                            <code class="text-xs font-bold text-indigo-600 break-all select-all">{{ url('/api/webhooks/madaaq') }}</code>
                                        </div>
                                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                            <p class="text-[9px] font-black text-slate-450 uppercase tracking-widest mb-1">X-MadaaQ-Key (Security Key)</p>
                                            <code class="text-xs font-bold text-orange-600 break-all select-all">{{ $madaaqIntegration->webhook_secret }}</code>
                                        </div>
                                        <p class="text-xs font-bold text-slate-500 leading-relaxed text-center">
                                            استخدم هذه البيانات في لوحة تحكم MadaaQ لتفعيل المزامنة التلقائية.
                                        </p>
                                        <button @click="showMadaaqModal = false" class="w-full px-5 py-2.5 bg-slate-900 text-white rounded-xl font-black text-xs hover:scale-105 active:scale-95 transition-all">إغلاق</button>
                                    </div>
                                @else
                                    <form action="{{ route('integrations.store') }}" method="POST" class="space-y-4 text-right">
                                        @csrf
                                        <input type="hidden" name="provider" value="madaaq">
                                        
                                        <div>
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 mr-1">اسم التكامل</label>
                                            <input type="text" name="name" value="MadaaQ Integration" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none" required>
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 mr-1">الرمز السري المولد من MadaaQ (Secret Code)</label>
                                            <input type="text" name="webhook_secret" placeholder="أدخل الرمز السري المولد من لوحة تحكم MadaaQ..." class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none" required>
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 mr-1">الصندوق المالي المستهدف</label>
                                            <select name="target_id" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none" required>
                                                <option value="">اختر الصندوق...</option>
                                                @foreach($funds as $fund)
                                                    <option value="{{ $fund->id }}">{{ $fund->name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="target_type" value="App\Models\InvestmentFund">
                                        </div>

                                        <div class="pt-2">
                                            <button type="submit" class="w-full px-5 py-2.5 bg-orange-600 text-white rounded-xl font-black text-xs shadow-lg shadow-orange-500/20 hover:scale-105 active:scale-95 transition-all">تفعيل وحفظ البيانات</button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WHMCS -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 hover:shadow-md transition-all flex flex-col justify-between group relative overflow-hidden">
                    <div>
                        <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-500 overflow-hidden border border-blue-100 shadow-sm">
                            <span class="text-xl font-black text-blue-600 italic">WHMCS</span>
                        </div>
                        <h4 class="text-xl font-black text-slate-900 mb-2 tracking-tight group-hover:text-blue-600 transition-colors">WHMCS</h4>
                        <p class="text-xs text-slate-500 font-bold leading-relaxed mb-6">تتبع فواتير عملائك في أنظمة الاستضافة والخدمات السحابية بشكل لحظي.</p>
                    </div>
                    <button class="w-full px-5 py-2.5 bg-blue-600 text-white rounded-xl font-black text-xs shadow-lg shadow-blue-500/20 hover:scale-105 active:scale-95 transition-all">تفعيل التكامل</button>
                </div>
            </div>

            <!-- Active Connections List -->
            <div class="space-y-4">
                <h3 class="text-xl font-black text-slate-900 tracking-tight px-2">التكاملات النشطة حالياً</h3>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    
                    @if(auth()->user()->shamcash_token)
                        <!-- ShamCash Active -->
                        <div class="p-6 flex flex-col md:flex-row items-center justify-between border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                            <div class="flex items-center gap-4 w-full">
                                <div class="w-12 h-12 bg-white shadow-sm rounded-xl flex items-center justify-center text-xl border border-slate-100">
                                    💳
                                </div>
                                <div>
                                    <h5 class="text-md font-black text-slate-900">ShamCash API Integration</h5>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">المزود:</span>
                                        <span class="text-[9px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded uppercase">ShamCash Platform</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-4 mt-4 md:mt-0 w-full md:w-auto justify-end">
                                <div class="text-left">
                                    <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest mb-0.5">حالة المزامنة</p>
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                        <span class="text-xs font-black text-emerald-600 uppercase">متصل ومزامن</span>
                                    </div>
                                </div>
                                <div class="h-8 w-px bg-slate-100"></div>
                                <form action="{{ route('shamcash.saveToken') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="token" value="">
                                    <button type="submit" class="text-xs font-black text-rose-500 uppercase tracking-widest hover:underline">إزالة الربط</button>
                                </form>
                            </div>
                        </div>
                    @endif

                    @forelse($integrations as $integration)
                        <div class="p-6 flex flex-col md:flex-row items-center justify-between border-b border-slate-100 last:border-0 hover:bg-slate-50/50 transition-colors">
                            <div class="flex items-center gap-4 w-full">
                                <div class="w-12 h-12 bg-white shadow-sm rounded-xl flex items-center justify-center text-xl border border-slate-100">
                                    {{ $integration->provider == 'shopify' ? '🛍️' : '☁️' }}
                                </div>
                                <div>
                                    <h5 class="text-md font-black text-slate-900">{{ $integration->name }}</h5>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">المستهدف:</span>
                                        <span class="text-[9px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded uppercase">{{ $integration->target->name ?? 'غير محدد' }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-4 mt-4 md:mt-0 w-full md:w-auto justify-end">
                                <div class="text-left">
                                    <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest mb-0.5">حالة الربط</p>
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                        <span class="text-xs font-black text-emerald-600 uppercase">نشط ويعمل</span>
                                    </div>
                                </div>
                                <div class="h-8 w-px bg-slate-100"></div>
                                <button class="text-xs font-black text-rose-500 uppercase tracking-widest hover:underline">تعطيل التكامل</button>
                            </div>
                        </div>
                    @empty
                        @if(!auth()->user()->shamcash_token)
                            <div class="p-16 text-center">
                                <div class="text-5xl mb-4">🏜️</div>
                                <p class="text-slate-400 font-bold text-lg">لا توجد تكاملات نشطة حالياً.</p>
                                <p class="text-slate-350 font-bold text-xs mt-1">اختر أحد المزودين أعلاه لبدء الأتمتة.</p>
                            </div>
                        @endif
                    @endforelse
                </div>
            </div>

            <!-- Security Notice Banner -->
            <div class="bg-slate-900 p-8 rounded-2xl relative overflow-hidden shadow-xl">
                <div class="absolute right-0 top-0 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl"></div>
                <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
                    <div class="w-16 h-16 bg-indigo-600 rounded-xl flex items-center justify-center text-3xl shadow-lg shadow-indigo-500/20">🛡️</div>
                    <div class="text-right">
                        <h4 class="text-lg font-black text-white mb-1">خصوصية وأمان البيانات</h4>
                        <p class="text-indigo-200/80 font-bold text-xs leading-relaxed max-w-2xl">
                            نحن نستخدم تقنيات التشفير المتقدمة وبروتوكولات Webhooks الآمنة لضمان وصول بيانات مبيعاتك فقط إلى صناديقك المحددة دون مشاركتها مع أي طرف ثالث.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
