<x-app-layout>
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/20"
     x-data="{ showCreateModal: false }">

    {{-- ===================== STICKY HEADER ===================== --}}
    <div class="bg-white border-b border-slate-100 sticky top-0 z-40 backdrop-blur-xl bg-white/90 shadow-sm shadow-slate-100/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-violet-650 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/15 flex-shrink-0">
                    <span class="text-white text-xl">💰</span>
                </div>
                <div>
                    <h1 class="text-base font-black text-slate-905 leading-none">المحافظ الشخصية</h1>
                    <p class="text-[10px] font-bold text-slate-400 mt-1">أدر مدخراتك، حساباتك البنكية، وعهودك الشخصية</p>
                </div>
            </div>
            <button @click="showCreateModal = true"
                class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-755 text-white rounded-xl font-black text-sm shadow-lg shadow-indigo-500/15 transition-all hover:scale-103">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                </svg>
                حساب أو عهدة جديدة
            </button>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-8">

        {{-- ===================== SUMMARY BAR ===================== --}}
        @if($wallets->isNotEmpty())
        @php
            $totalUSD = $wallets->where('currency','USD')->sum('balance');
            $totalSYP = $wallets->where('currency','SYP')->sum('balance');
            $totalOther = $wallets->whereNotIn('currency',['USD','SYP'])->count();
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50/50 rounded-2xl p-4.5 border border-emerald-100 shadow-xs">
                <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest mb-1.5">إجمالي USD</p>
                <p class="text-xl font-black text-emerald-700 tracking-tighter">{{ number_format($totalUSD, 2) }} <span class="text-xs opacity-60">$</span></p>
            </div>
            <div class="bg-gradient-to-br from-amber-50 to-yellow-50/50 rounded-2xl p-4.5 border border-amber-100 shadow-xs">
                <p class="text-[9px] font-black text-amber-600 uppercase tracking-widest mb-1.5">إجمالي SYP</p>
                <p class="text-xl font-black text-amber-700 tracking-tighter">{{ number_format($totalSYP, 0) }} <span class="text-xs opacity-60">ل.س</span></p>
            </div>
            <div class="bg-gradient-to-br from-indigo-50 to-violet-50/50 rounded-2xl p-4.5 border border-indigo-100 shadow-xs col-span-2 sm:col-span-1">
                <p class="text-[9px] font-black text-indigo-600 uppercase tracking-widest mb-1.5">إجمالي الحسابات</p>
                <p class="text-xl font-black text-indigo-700">{{ $wallets->count() }} <span class="text-xs font-bold opacity-60">حساب / عهدة</span></p>
            </div>
        </div>
        @endif

        {{-- ===================== WALLETS GROUPED LIST ===================== --}}
        @php
            $bankAccounts = $wallets->filter(fn($w) => empty(trim($w->custodian_name)));
            $custodies = $wallets->filter(fn($w) => !empty(trim($w->custodian_name)));
            
            $gradientsBank = [
                ['from-indigo-500', 'to-indigo-650', 'shadow-indigo-500/15'],
                ['from-sky-500', 'to-sky-655', 'shadow-sky-500/15'],
                ['from-emerald-500', 'to-emerald-650', 'shadow-emerald-500/15'],
            ];
            
            $gradientsCustody = [
                ['from-amber-500', 'to-amber-650', 'shadow-amber-500/15'],
                ['from-rose-500', 'to-rose-650', 'shadow-rose-500/15'],
                ['from-violet-500', 'to-violet-650', 'shadow-violet-500/15'],
            ];
        @endphp

        {{-- 💳 SECTION 1: الحسابات البنكية والائتمانية --}}
        <div class="space-y-4.5">
            <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                <span class="w-1.5 h-3.5 bg-indigo-600 rounded-full"></span>
                <h2 class="text-sm font-black text-slate-800">💳 الحسابات البنكية والبطاقات الائتمانية</h2>
                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded-md text-[10px] font-extrabold">{{ $bankAccounts->count() }}</span>
            </div>
            
            @if($bankAccounts->isEmpty())
                <div class="py-12 text-center bg-white rounded-3xl border border-dashed border-slate-200">
                    <p class="text-slate-400 font-bold text-xs">لا توجد حسابات بنكية مسجلة بعد</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($bankAccounts as $index => $wallet)
                    @php $g = $gradientsBank[$index % count($gradientsBank)]; @endphp
                    <a href="{{ route('wallets.show', $wallet->id) }}"
                       class="group relative bg-gradient-to-br {{ $g[0] }} {{ $g[1] }} rounded-3xl p-6.5 overflow-hidden shadow-lg {{ $g[2] }} hover:shadow-xl hover:scale-[1.02] transition-all duration-300 block">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-6">
                                <div class="w-11 h-11 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-xl border border-white/30 group-hover:scale-110 transition-transform">
                                    💳
                                </div>
                                <span class="px-2.5 py-1 bg-white/20 backdrop-blur-sm text-white text-[9px] font-black tracking-widest rounded-lg border border-white/25">
                                    {{ $wallet->currency }}
                                </span>
                            </div>
                            <div class="mb-5">
                                <p class="text-white/60 text-[9px] font-black uppercase tracking-widest mb-1">الرصيد المتاح</p>
                                <p class="text-3xl font-black text-white tracking-tighter leading-none">
                                    {{ number_format($wallet->balance, 0) }}
                                    <span class="text-sm opacity-60">{{ $wallet->currency }}</span>
                                </p>
                            </div>
                            <div class="flex items-center justify-between pt-3 border-t border-white/20">
                                <p class="text-white/90 font-black text-xs">{{ $wallet->name }}</p>
                                <div class="flex items-center gap-1 text-white/70 text-[9px] font-black">
                                    <span>تفاصيل</span>
                                    <svg class="w-3 h-3 group-hover:translate-x-[-2px] transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l-7 7 7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- 💼 SECTION 2: العهد الشخصية والأموال السائلة --}}
        <div class="space-y-4.5">
            <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                <span class="w-1.5 h-3.5 bg-amber-500 rounded-full"></span>
                <h2 class="text-sm font-black text-slate-800">💼 العهد الشخصية والأموال السائلة</h2>
                <span class="px-2 py-0.5 bg-amber-50 text-amber-600 rounded-md text-[10px] font-extrabold">{{ $custodies->count() }}</span>
            </div>
            
            @if($custodies->isEmpty())
                <div class="py-12 text-center bg-white rounded-3xl border border-dashed border-slate-200">
                    <p class="text-slate-400 font-bold text-xs">لا توجد عهد شخصية أو مبالغ سائلة مسجلة بعد</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($custodies as $index => $wallet)
                    @php $g = $gradientsCustody[$index % count($gradientsCustody)]; @endphp
                    <a href="{{ route('wallets.show', $wallet->id) }}"
                       class="group relative bg-gradient-to-br {{ $g[0] }} {{ $g[1] }} rounded-3xl p-6.5 overflow-hidden shadow-lg {{ $g[2] }} hover:shadow-xl hover:scale-[1.02] transition-all duration-300 block">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-6">
                                <div class="w-11 h-11 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-xl border border-white/30 group-hover:scale-110 transition-transform">
                                    💼
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <span class="px-2 py-0.5 bg-white/20 text-white text-[8px] font-black tracking-widest rounded border border-white/25">
                                        {{ $wallet->currency }}
                                    </span>
                                    <span class="px-2 py-0.5 bg-black/15 text-white/90 text-[8px] font-black rounded">
                                        🔑 عهدة: {{ $wallet->custodian_name }}
                                    </span>
                                </div>
                            </div>
                            <div class="mb-5">
                                <p class="text-white/60 text-[9px] font-black uppercase tracking-widest mb-1">الرصيد المتوفر</p>
                                <p class="text-3xl font-black text-white tracking-tighter leading-none">
                                    {{ number_format($wallet->balance, 0) }}
                                    <span class="text-sm opacity-60">{{ $wallet->currency }}</span>
                                </p>
                            </div>
                            <div class="flex items-center justify-between pt-3 border-t border-white/20">
                                <p class="text-white/90 font-black text-xs">{{ $wallet->name }}</p>
                                <div class="flex items-center gap-1 text-white/70 text-[9px] font-black">
                                    <span>تفاصيل</span>
                                    <svg class="w-3 h-3 group-hover:translate-x-[-2px] transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l-7 7 7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- ===================== CREATE WALLET MODAL ===================== --}}
    <div x-show="showCreateModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition
         x-data="{ isCustody: false }">
        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl text-right" @click.away="showCreateModal = false">
            <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-black text-gray-900">إضافة حساب أو عهدة جديدة</h3>
                <button @click="showCreateModal = false" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-600 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('wallets.store') }}" method="POST" class="p-8 space-y-4">
                @csrf
                
                {{-- Segmented selector for type --}}
                <div class="p-1 bg-slate-100 border border-slate-200/50 rounded-2xl grid grid-cols-2 gap-1 mb-1">
                    <button type="button" @click="isCustody = false"
                            :class="!isCustody ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                            class="py-2 text-center rounded-xl text-xs font-black transition-all">
                        💳 حساب بنكي / ائتمان
                    </button>
                    <button type="button" @click="isCustody = true"
                            :class="isCustody ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                            class="py-2 text-center rounded-xl text-xs font-black transition-all">
                        💼 عهدة مالية شخصية
                    </button>
                </div>

                <div>
                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-2 tracking-widest"
                           x-text="!isCustody ? 'اسم الحساب البنكي' : 'اسم محفظة العهدة'"></label>
                    <input type="text" name="name" required
                           class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                           placeholder="مثلاً: كاش شخصي، حساب بنك بيمو، بطاقة ميزة...">
                </div>

                <div x-show="isCustody" x-cloak x-transition>
                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-2 tracking-widest">أمين العهدة (اسم الشخص المسؤول) *</label>
                    <input type="text" name="custodian_name" :required="isCustody"
                           class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                           placeholder="اسم أمين العهدة...">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[9px] font-black text-gray-400 uppercase mb-2 tracking-widest">الرصيد الافتتاحي</label>
                        <input type="number" name="balance" value="0" step="0.01" required
                               class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-gray-400 uppercase mb-2 tracking-widest">العملة</label>
                        <select name="currency" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-xs focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="USD">USD - دولار</option>
                            <option value="SYP">SYP - ليرة سورية</option>
                            <option value="TRY">TRY - ليرة تركية</option>
                            <option value="SAR">SAR - ريال</option>
                            <option value="AED">AED - درهم</option>
                            <option value="EUR">EUR - يورو</option>
                        </select>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-black text-sm shadow-lg shadow-indigo-500/20 transition-all">
                    ✓ تأكيد وإنشاء المحفظة
                </button>
            </form>
        </div>
    </div>

</div>
</x-app-layout>
