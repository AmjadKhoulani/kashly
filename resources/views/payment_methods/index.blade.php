<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/20" x-data="{ showModal: false, assocType: 'wallet' }">
        
        <!-- Sticky Header -->
        <div class="bg-white/95 border-b border-slate-100 sticky top-0 z-40 backdrop-blur-xl py-6 px-6">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight">الحسابات ووسائل الدفع</h2>
                    <p class="text-slate-500 font-bold mt-2 text-sm">إدارة حساباتك البنكية، الخزينة، والبطاقات الائتمانية والعملات في مكان واحد.</p>
                </div>
                <button @click="showModal = true" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-sm shadow-lg shadow-indigo-500/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                    إضافة حساب جديد
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-6 py-10 space-y-10">

            <!-- Payment Methods Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($methods as $method)
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 hover:shadow-md transition-all flex flex-col justify-between group relative overflow-hidden">
                        <div class="absolute -right-6 -top-6 w-32 h-32 bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/10 transition-all duration-700"></div>
                        
                        <div class="relative">
                            <div class="flex justify-between items-start mb-6">
                                <div class="w-14 h-14 bg-slate-50 text-slate-900 rounded-xl flex items-center justify-center text-3xl group-hover:scale-110 group-hover:rotate-6 transition-all duration-500 border border-slate-100 shadow-sm">
                                    @switch($method->type)
                                        @case('bank') 🏛️ @break
                                        @case('cash') 💵 @break
                                        @case('credit_card') 💳 @break
                                        @case('debit_card') 💳 @break
                                        @default 💰
                                    @endswitch
                                </div>
                                <form action="{{ route('payment-methods.destroy', $method->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الحساب؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-10 h-10 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-sm border border-rose-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>

                            <h3 class="text-2xl font-black text-slate-900 mb-1 tracking-tight group-hover:text-indigo-600 transition-colors">{{ $method->name }}</h3>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-4">
                                @switch($method->type)
                                    @case('bank') حساب بنكي @break
                                    @case('cash') نقد / خزينة @break
                                    @case('credit_card') بطاقة ائتمان @break
                                    @case('debit_card') بطاقة صراف @break
                                    @default أخرى
                                @endswitch
                            </p>

                            <!-- Dependency Badges -->
                            <div class="mb-6 flex flex-wrap gap-2">
                                @if($method->fund)
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black bg-orange-50 text-orange-600 border border-orange-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500 ml-1.5"></span>
                                        صندوق: {{ $method->fund->name }}
                                    </span>
                                @elseif($method->wallet)
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black bg-indigo-50 text-indigo-600 border border-indigo-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 ml-1.5"></span>
                                        محفظة: {{ $method->wallet->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black bg-slate-50 text-slate-500 border border-slate-100">
                                        غير مرتبط
                                    </span>
                                @endif

                                @if($method->custodian_name)
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 ml-1.5"></span>
                                        عهدة: {{ $method->custodian_name }}
                                    </span>
                                @endif
                            </div>
                            
                            <div class="border-t border-slate-100 pt-4 mt-auto">
                                <p class="text-[10px] text-slate-400 font-black uppercase mb-1 tracking-widest">الرصيد المتاح</p>
                                <p class="text-3xl font-black text-indigo-600 tracking-tighter">{{ number_format($method->balance, 2) }} <span class="text-sm font-black">{{ $method->currency }}</span></p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-2xl border border-slate-100 shadow-sm p-16 text-center">
                        <div class="text-6xl mb-6">🏦</div>
                        <h3 class="text-2xl font-black text-slate-900 mb-2">لا توجد حسابات مضافة</h3>
                        <p class="text-slate-400 font-bold text-sm">ابدأ بإضافة حساباتك البنكية أو محافظك المالية لتتبع مصادر أموالك بدقة.</p>
                    </div>
                @endforelse
            </div>

        </div>

        <!-- Add Modal -->
        <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-md" x-cloak x-transition>
            <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl text-right relative overflow-hidden border border-slate-100/50" @click.away="showModal = false">
                
                <!-- Sticky Header inside Modal -->
                <div class="sticky top-0 bg-white/95 border-b border-slate-100 px-6 py-4 flex justify-between items-center z-10 backdrop-blur-md">
                    <h3 class="text-lg font-black text-slate-900">إضافة وسيلة دفع</h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 transition-all p-1.5 hover:bg-slate-50 rounded-xl border border-transparent hover:border-slate-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <form action="{{ route('payment-methods.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 mr-1">اسم الحساب / الوسيلة</label>
                        <input type="text" name="name" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="مثلاً: بنك الراجحي، خزينة المحل...">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 mr-1">اسم أمين العهدة / المسؤول (اختياري)</label>
                        <input type="text" name="custodian_name" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="مثلاً: يوسف، محمد...">
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 mr-1">نوع الحساب</label>
                        <select name="type" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="bank">حساب بنكي</option>
                            <option value="cash">نقد / كاش</option>
                            <option value="credit_card">بطاقة ائتمان (Credit)</option>
                            <option value="debit_card">بطاقة دفع (Debit)</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>

                    <!-- Association Section -->
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase mr-1">تبعية الحساب (صندوق أم محفظة شخصية)</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center justify-center p-3 bg-gray-50 border rounded-2xl cursor-pointer font-bold text-xs transition-all" :class="assocType === 'wallet' ? 'border-indigo-650 bg-indigo-50/50 text-indigo-700 border-indigo-600' : 'border-transparent text-slate-500'">
                                <input type="radio" name="association_type" value="wallet" x-model="assocType" class="hidden">
                                <span>محفظة شخصية 🚶</span>
                            </label>
                            <label class="flex items-center justify-center p-3 bg-gray-50 border rounded-2xl cursor-pointer font-bold text-xs transition-all" :class="assocType === 'fund' ? 'border-orange-650 bg-orange-50/50 text-orange-700 border-orange-600' : 'border-transparent text-slate-500'">
                                <input type="radio" name="association_type" value="fund" x-model="assocType" class="hidden">
                                <span>صندوق استثماري 🏛️</span>
                            </label>
                        </div>
                    </div>

                    <!-- Wallet Selection -->
                    <div x-show="assocType === 'wallet'">
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 mr-1">اختر المحفظة الشخصية</label>
                        <select name="wallet_id" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            @forelse($wallets as $wallet)
                                <option value="{{ $wallet->id }}">{{ $wallet->name }} ({{ number_format($wallet->balance, 2) }} {{ $wallet->currency }})</option>
                            @empty
                                <option value="">لا يوجد محافظ، يرجى إنشاء محفظة أولاً</option>
                            @endforelse
                        </select>
                    </div>

                    <!-- Fund Selection -->
                    <div x-show="assocType === 'fund'">
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 mr-1">اختر الصندوق الاستثماري</label>
                        <select name="fund_id" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            @forelse($funds as $fund)
                                <option value="{{ $fund->id }}">{{ $fund->name }} ({{ $fund->currency }})</option>
                            @empty
                                <option value="">لا يوجد صناديق، يرجى إنشاء صندوق أولاً</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 mr-1">الرصيد الافتتاحي</label>
                            <input type="number" step="0.01" name="balance" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-md focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 mr-1">العملة</label>
                            <select name="currency" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                                <option value="USD">USD</option>
                                <option value="TRY">TRY</option>
                                <option value="SAR">SAR</option>
                                <option value="EUR">EUR</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-sm shadow-lg shadow-indigo-500/20 hover:scale-105 active:scale-95 transition-all">حفظ الحساب</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
