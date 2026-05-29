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
                @php
                    $madaaqIntegrations = $integrations->where('provider', 'madaaq');
                    $madaaqCount = $madaaqIntegrations->count();
                @endphp
                <div x-data="{ showMadaaqModal: false, showAddForm: false }" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 hover:shadow-md transition-all flex flex-col justify-between group relative overflow-hidden">
                    <div>
                        <div class="w-16 h-16 bg-orange-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-500 overflow-hidden text-3xl border border-orange-100 shadow-sm">
                            📡
                        </div>
                        <h4 class="text-xl font-black text-slate-900 mb-2 tracking-tight group-hover:text-orange-600 transition-colors">MadaaQ</h4>
                        <p class="text-xs text-slate-500 font-bold leading-relaxed mb-4">تكامل مباشر مع منصة MadaaQ لاستقبال تحصيلات المشتركين وتوثيقها في صناديقك المالية.</p>

                        {{-- Show count of active integrations --}}
                        @if($madaaqCount > 0)
                            <div class="flex items-center gap-2 mb-5">
                                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                <span class="text-[11px] font-black text-emerald-600">{{ $madaaqCount }} ربط{{ $madaaqCount > 1 ? 'ات نشطة' : ' نشط' }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-2">
                        @if($madaaqCount > 0)
                            <button @click="showMadaaqModal = true; showAddForm = false" class="w-full px-5 py-2.5 bg-slate-100 text-slate-900 rounded-xl font-black text-xs shadow-sm hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h7"/></svg>
                                إدارة الربط ({{ $madaaqCount }})
                            </button>
                        @endif
                        <button @click="showMadaaqModal = true; showAddForm = true" class="w-full px-5 py-2.5 {{ $madaaqCount > 0 ? 'bg-orange-50 text-orange-600 border border-orange-200' : 'bg-orange-600 text-white shadow-lg shadow-orange-500/20' }} rounded-xl font-black text-xs hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                            ربط جديد مع MadaaQ
                        </button>
                    </div>

                    <!-- MadaaQ Multi-Integration Modal -->
                    <div x-show="showMadaaqModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-md" x-cloak x-transition>
                        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl text-right relative overflow-hidden border border-slate-100/50 max-h-[90vh] flex flex-col" @click.away="showMadaaqModal = false; showAddForm = false">
                            
                            <!-- Header -->
                            <div class="bg-white/95 border-b border-slate-100 px-6 py-4 flex justify-between items-center backdrop-blur-md flex-shrink-0">
                                <div>
                                    <h3 class="text-lg font-black text-slate-900">تكاملات MadaaQ</h3>
                                    <p class="text-[10px] font-bold text-slate-400 mt-0.5">{{ $madaaqCount }} ربط نشط</p>
                                </div>
                                <button @click="showMadaaqModal = false; showAddForm = false" class="text-slate-400 hover:text-slate-600 transition-all p-1.5 hover:bg-slate-50 rounded-xl border border-transparent hover:border-slate-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <!-- Scrollable Content -->
                            <div class="overflow-y-auto flex-1 p-6 space-y-5">

                                {{-- ===== EXISTING INTEGRATIONS LIST ===== --}}
                                @if($madaaqCount > 0)
                                    <div class="space-y-3">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">الربطات النشطة حالياً</p>
                                        @foreach($madaaqIntegrations as $mi)
                                            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 flex items-center justify-between gap-3">
                                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                                    <div class="w-8 h-8 bg-orange-100 rounded-xl flex items-center justify-center text-base flex-shrink-0">📡</div>
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-black text-slate-900 truncate">{{ $mi->target->name ?? 'غير محدد' }}</p>
                                                        <div class="flex items-center gap-1.5 mt-0.5">
                                                            <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
                                                            <p class="text-[10px] font-bold text-emerald-600">نشط</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <form action="{{ route('integrations.destroy', $mi->id) }}" method="POST" onsubmit="return confirm('إلغاء ربط {{ $mi->target->name ?? 'هذا الصندوق' }} من MadaaQ؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all flex-shrink-0" title="إلغاء الربط">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="border-t border-slate-100 pt-4">
                                        <button @click="showAddForm = !showAddForm" class="w-full flex items-center justify-center gap-2 text-[11px] font-black text-orange-600 hover:text-orange-700 transition-colors py-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" :class="showAddForm ? 'rotate-45' : ''" style="transition: transform 0.2s"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                            <span x-text="showAddForm ? 'إلغاء إضافة ربط جديد' : 'إضافة ربط MadaaQ جديد'"></span>
                                        </button>
                                    </div>
                                @endif

                                {{-- ===== ADD NEW INTEGRATION FORM ===== --}}
                                <div x-show="showAddForm" x-transition>
                                    <div class="bg-orange-50 rounded-2xl p-4 border border-orange-100 text-right mb-4">
                                        <p class="text-xs font-black text-orange-700 mb-1">كيف تحصل على الـ Secret Code؟</p>
                                        <p class="text-[11px] font-bold text-orange-600 leading-relaxed">
                                            ادخل إلى لوحة تحكم MadaaQ ← الإعدادات ← الربط الخارجي، ثم انسخ الـ Secret Code الخاص بالمنصة التي تريد ربطها.
                                        </p>
                                    </div>

                                    <form action="{{ route('integrations.store') }}" method="POST" class="space-y-4 text-right">
                                        @csrf
                                        <input type="hidden" name="provider" value="madaaq">

                                        <div>
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 mr-1">اسم الربط (للتمييز)</label>
                                            <input 
                                                type="text" 
                                                name="name" 
                                                placeholder="مثلاً: MadaaQ - الدفع الشهري..." 
                                                class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-orange-400 outline-none text-right" 
                                                required
                                            >
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 mr-1">Secret Code من MadaaQ</label>
                                            <input 
                                                type="text" 
                                                name="webhook_secret" 
                                                placeholder="الصق الـ Secret Code هنا..." 
                                                class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-orange-400 outline-none text-right" 
                                                required
                                                autocomplete="off"
                                            >
                                            <p class="text-[10px] font-bold text-slate-400 mt-1.5 mr-1">ستجده في إعدادات الربط الخارجي بمنصة MadaaQ</p>
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 mr-1">الصندوق المستهدف</label>
                                            <select name="target_id" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-orange-400 outline-none" required>
                                                <option value="">اختر الصندوق الذي ستُودع فيه التحصيلات...</option>
                                                @foreach($funds as $fund)
                                                    <option value="{{ $fund->id }}">{{ $fund->name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="target_type" value="App\Models\InvestmentFund">
                                        </div>

                                        <div class="pt-1">
                                            <button type="submit" class="w-full px-5 py-3 bg-orange-600 text-white rounded-xl font-black text-sm shadow-lg shadow-orange-500/20 hover:scale-105 active:scale-95 transition-all">
                                                تفعيل الربط مع MadaaQ
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                {{-- If no integrations and form not shown --}}
                                @if($madaaqCount === 0)
                                    <div x-show="!showAddForm" class="text-center py-4 space-y-3">
                                        <div class="text-4xl">📡</div>
                                        <p class="text-sm font-bold text-slate-400">لا يوجد ربط نشط مع MadaaQ</p>
                                        <p class="text-xs font-bold text-slate-300">اضغط "ربط جديد" لإضافة أول تكامل</p>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

                    
                    @php
                        $madaaqIntegration = $integrations->where('provider', 'madaaq')->first();
                    @endphp

                    @if($madaaqIntegration)
                        <button @click="showMadaaqModal = true" class="w-full px-5 py-2.5 bg-slate-100 text-slate-900 rounded-xl font-black text-xs shadow-sm hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-2">
                            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                            تكامل نشط
                        </button>
                    @else
                        <button @click="showMadaaqModal = true" class="w-full px-5 py-2.5 bg-orange-600 text-white rounded-xl font-black text-xs shadow-lg shadow-orange-500/20 hover:scale-105 active:scale-95 transition-all">ربط مع MadaaQ</button>
                    @endif

                    <!-- MadaaQ Setup Modal -->
                    <div x-show="showMadaaqModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-md" x-cloak x-transition>
                        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl text-right relative overflow-hidden border border-slate-100/50" @click.away="showMadaaqModal = false">
                            
                            <!-- Sticky Header inside Modal -->
                            <div class="sticky top-0 bg-white/95 border-b border-slate-100 px-6 py-4 flex justify-between items-center z-10 backdrop-blur-md">
                                <h3 class="text-lg font-black text-slate-900">
                                    @if($madaaqIntegration) تكامل MadaaQ @else ربط مع MadaaQ @endif
                                </h3>
                                <button @click="showMadaaqModal = false" class="text-slate-400 hover:text-slate-600 transition-all p-1.5 hover:bg-slate-50 rounded-xl border border-transparent hover:border-slate-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <!-- Modal Content -->
                            <div class="p-6 space-y-5">
                                @if($madaaqIntegration)
                                    {{-- Integration is active - show status only --}}
                                    <div class="text-center space-y-5">
                                        <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto text-3xl border border-emerald-100 shadow-sm">✅</div>
                                        <div>
                                            <p class="text-lg font-black text-slate-900">التكامل نشط ويعمل</p>
                                            <p class="text-xs font-bold text-slate-400 mt-1 leading-relaxed">
                                                يتم استقبال تحصيلات MadaaQ تلقائياً وتسجيلها في صندوقك المالي.
                                            </p>
                                        </div>
                                        <div class="bg-slate-50 rounded-2xl p-4 text-right border border-slate-100">
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">الصندوق المستهدف</p>
                                            <p class="text-sm font-black text-slate-900">{{ $madaaqIntegration->target->name ?? 'غير محدد' }}</p>
                                        </div>
                                        <div class="flex gap-3 pt-1">
                                            <button @click="showMadaaqModal = false" class="flex-1 px-5 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-black text-xs hover:scale-105 active:scale-95 transition-all">إغلاق</button>
                                            <form action="{{ route('integrations.destroy', $madaaqIntegration->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء ربط MadaaQ؟')" class="flex-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full px-5 py-2.5 bg-rose-50 text-rose-600 rounded-xl font-black text-xs hover:scale-105 active:scale-95 transition-all border border-rose-100">إلغاء الربط</button>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    {{-- No integration - show secret code input form --}}
                                    <div class="bg-orange-50 rounded-2xl p-4 border border-orange-100 text-right">
                                        <p class="text-xs font-black text-orange-700 mb-1">كيف يعمل الربط؟</p>
                                        <p class="text-[11px] font-bold text-orange-600 leading-relaxed">
                                            ادخل إلى لوحة تحكم MadaaQ ← الإعدادات ← الربط الخارجي، ثم انسخ الـ Secret Code وأدخله هنا.
                                        </p>
                                    </div>

                                    <form action="{{ route('integrations.store') }}" method="POST" class="space-y-4 text-right">
                                        @csrf
                                        <input type="hidden" name="provider" value="madaaq">
                                        <input type="hidden" name="name" value="MadaaQ Integration">

                                        <div>
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 mr-1">Secret Code من MadaaQ</label>
                                            <input 
                                                type="text" 
                                                name="webhook_secret" 
                                                placeholder="الصق الـ Secret Code هنا..." 
                                                class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-orange-400 outline-none text-right" 
                                                required
                                                autocomplete="off"
                                            >
                                            <p class="text-[10px] font-bold text-slate-400 mt-1.5 mr-1">ستجده في إعدادات الربط الخارجي بمنصة MadaaQ</p>
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 mr-1">الصندوق المالي المستهدف</label>
                                            <select name="target_id" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-orange-400 outline-none" required>
                                                <option value="">اختر الصندوق الذي ستُودع فيه التحصيلات...</option>
                                                @foreach($funds as $fund)
                                                    <option value="{{ $fund->id }}">{{ $fund->name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="target_type" value="App\Models\InvestmentFund">
                                        </div>

                                        <div class="pt-1">
                                            <button type="submit" class="w-full px-5 py-3 bg-orange-600 text-white rounded-xl font-black text-sm shadow-lg shadow-orange-500/20 hover:scale-105 active:scale-95 transition-all">
                                                تفعيل الربط مع MadaaQ
                                            </button>
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
                                <form action="{{ route('integrations.destroy', $integration->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من تعطيل هذا الربط وحذف إعداداته؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-black text-rose-500 uppercase tracking-widest hover:underline bg-transparent border-0 p-0 cursor-pointer">تعطيل التكامل</button>
                                </form>
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
