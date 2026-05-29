<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/20" x-data="{ showModal: false }">

        {{-- Sticky Header --}}
        <div class="bg-white/95 border-b border-slate-100 sticky top-0 z-40 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                <div class="flex items-center justify-between h-16">
                    <div>
                        <h1 class="text-lg font-black text-slate-900 tracking-tight">الكيانات الاستثمارية</h1>
                        <p class="text-xs text-slate-400 font-semibold">تتبع وإدارة محافظك الاستثمارية</p>
                    </div>
                    <button @click="showModal = true"
                        class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-sm shadow-lg shadow-indigo-500/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                        </svg>
                        كيان جديد
                    </button>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-6">

            {{-- Flash Messages --}}
            @if(session('error'))
                <div class="flex items-center gap-3 bg-rose-50 border border-rose-100 rounded-2xl px-5 py-3 shadow-sm">
                    <span class="text-lg">⚠️</span>
                    <div>
                        <p class="text-sm font-black text-rose-800">حدث خطأ أثناء المعالجة</p>
                        <p class="text-xs font-semibold text-rose-500 mt-0.5">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-100 rounded-2xl px-5 py-3 shadow-sm">
                    <span class="text-lg">✅</span>
                    <div>
                        <p class="text-sm font-black text-emerald-800">تمت العملية بنجاح</p>
                        <p class="text-xs font-semibold text-emerald-500 mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            {{-- Stats Bar --}}
            @php
                $totalProfit = $funds->sum('current_value') - $funds->sum('capital');
                $profitPercent = $funds->sum('capital') > 0 ? ($totalProfit / $funds->sum('capital')) * 100 : 0;
            @endphp
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl p-4 border border-indigo-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">رأس المال</p>
                        <p class="text-2xl font-black text-slate-900 tracking-tighter">${{ number_format($funds->sum('capital'), 0) }}</p>
                    </div>
                    <div class="w-10 h-10 bg-white/70 rounded-xl flex items-center justify-center text-xl shadow-sm">🏛️</div>
                </div>
                <div class="bg-gradient-to-br from-violet-50 to-indigo-50 rounded-2xl p-4 border border-violet-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-violet-400 uppercase tracking-widest mb-1">القيمة الحالية</p>
                        <p class="text-2xl font-black text-indigo-600 tracking-tighter">${{ number_format($funds->sum('current_value'), 0) }}</p>
                    </div>
                    <div class="w-10 h-10 bg-white/70 rounded-xl flex items-center justify-center text-xl shadow-sm">📈</div>
                </div>
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-4 border border-emerald-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-1">صافي العائد</p>
                        <p class="text-2xl font-black text-emerald-600 tracking-tighter">+{{ number_format($profitPercent, 1) }}%</p>
                    </div>
                    <div class="w-10 h-10 bg-white/70 rounded-xl flex items-center justify-center text-xl shadow-sm">💰</div>
                </div>
            </div>

            {{-- Funds Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                @foreach($funds as $fund)
                    @php
                        $fundProfit = $fund->current_value - $fund->capital;
                        $fundProfitPct = $fund->capital > 0 ? (($fund->current_value - $fund->capital) / $fund->capital) * 100 : 0;
                        $barPercent = min(100, ($fund->current_value / max($fund->capital, 1)) * 100);
                    @endphp
                    @php
                        $isLinked = isset($linkedFundIds[$fund->id]);
                        $linkedProvider = $isLinked ? strtoupper($linkedFundIds[$fund->id]) : null;
                    @endphp
                    <a href="{{ route('funds.show', $fund->id) }}"
                        class="bg-white rounded-2xl border {{ $isLinked ? 'border-orange-200' : 'border-slate-100' }} shadow-sm hover:shadow-md {{ $isLinked ? 'hover:border-orange-300' : 'hover:border-indigo-200' }} transition-all duration-300 block overflow-hidden group relative">
                        <div class="p-5">
                            {{-- Top row: icon + name + badge --}}
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-2xl border border-slate-100 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                                        {{ $fund->icon ?? '🏘️' }}
                                    </div>
                                    <div>
                                        <h3 class="text-base font-black text-slate-900 group-hover:text-indigo-600 transition-colors leading-tight">{{ $fund->name }}</h3>
                                        <p class="text-xs text-slate-400 font-semibold mt-0.5">{{ $fund->currency ?? 'USD' }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-1.5">
                                    <span class="px-3 py-1 {{ $fund->status == 'active' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-slate-50 text-slate-400 border-slate-100' }} text-[10px] font-black uppercase rounded-lg tracking-widest border shadow-sm">
                                        {{ $fund->status == 'active' ? '● نشط' : 'مغلق' }}
                                    </span>
                                    @if($isLinked)
                                        <span class="flex items-center gap-1 px-2.5 py-1 bg-orange-50 text-orange-600 border border-orange-200 text-[9px] font-black uppercase rounded-lg tracking-widest shadow-sm">
                                            <span class="w-1.5 h-1.5 bg-orange-500 rounded-full animate-pulse"></span>
                                            {{ $linkedProvider }} API
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Capital vs Current Value --}}
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">رأس المال</p>
                                    <p class="text-lg font-black text-slate-900 tracking-tighter">${{ number_format($fund->capital, 0) }}</p>
                                </div>
                                <div class="bg-indigo-50/60 rounded-xl p-3 border border-indigo-100">
                                    <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">القيمة الحالية</p>
                                    <div class="flex items-baseline gap-2">
                                        <p class="text-lg font-black text-indigo-600 tracking-tighter">${{ number_format($fund->current_value, 0) }}</p>
                                        <span class="text-[10px] font-black {{ $fundProfitPct >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }} px-1.5 py-0.5 rounded-md">
                                            {{ $fundProfitPct >= 0 ? '+' : '' }}{{ number_format($fundProfitPct, 1) }}%
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Progress Bar --}}
                            <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-l from-indigo-600 to-indigo-400 rounded-full transition-all duration-1000"
                                    style="width: {{ $barPercent }}%"></div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

        </div>

        {{-- Create Entity Modal --}}
        <div x-show="showModal"
            class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md"
            x-cloak x-transition>
            <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl relative text-right overflow-hidden"
                @click.away="showModal = false">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-8 py-5 border-b border-slate-100">
                    <button @click="showModal = false"
                        class="w-8 h-8 bg-slate-100 hover:bg-slate-200 rounded-xl flex items-center justify-center transition-all text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <h3 class="text-lg font-black text-slate-900">إنشاء كيان استثماري</h3>
                </div>

                {{-- Modal Form --}}
                <form action="{{ route('funds.store') }}" method="POST" class="p-8 space-y-5">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">اسم الكيان</label>
                        <input type="text" name="name" required
                            class="w-full bg-gray-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none"
                            placeholder="مثلاً: عمارة الياسمين، محفظة الأسهم...">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">رأس المال التأسيسي (اختياري)</label>
                        <input type="number" name="capital"
                            class="w-full bg-gray-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold text-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none"
                            placeholder="0.00">
                        <p class="text-[10px] text-slate-400 mt-2 pr-1">يمكنك تركه فارغاً إذا كان المشروع قيد التأسيس، وسيتم احتسابه من "مصاريف رأس المال".</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">تكرار توزيع الأرباح</label>
                            <select name="distribution_frequency"
                                class="w-full bg-gray-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
                                <option value="1">شهري</option>
                                <option value="3">كل 3 أشهر</option>
                                <option value="6">كل 6 أشهر</option>
                                <option value="12">سنوي</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">العملة الأساسية</label>
                            <select name="currency"
                                class="w-full bg-gray-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
                                <option value="USD">USD</option>
                                <option value="TRY">TRY</option>
                                <option value="SAR">SAR</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full px-5 py-3 bg-indigo-600 text-white rounded-xl font-black text-sm shadow-lg shadow-indigo-500/20 hover:scale-105 active:scale-95 transition-all">
                        تأكيد الإنشاء
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
