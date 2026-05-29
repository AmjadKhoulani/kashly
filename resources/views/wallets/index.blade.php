<x-app-layout>
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/20"
     x-data="{ showCreateModal: false, isCustody: false }">

    {{-- ===================== STICKY HEADER ===================== --}}
    <div class="bg-white border-b border-slate-100 sticky top-0 z-40 backdrop-blur-xl bg-white/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20 flex-shrink-0">
                    <span class="text-white text-xl">💰</span>
                </div>
                <div>
                    <h1 class="text-base font-black text-slate-900 leading-none">المحافظ والعهد</h1>
                    <p class="text-[10px] font-bold text-slate-400 mt-0.5">أدر المحافظ الشخصية والعهد المستقلة</p>
                </div>
            </div>
            <button @click="showCreateModal = true"
                class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black text-sm shadow-lg shadow-indigo-500/20 transition-all hover:scale-105">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                </svg>
                محفظة أو عهدة جديدة
            </button>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-8">

        {{-- ===================== SUMMARY BAR ===================== --}}
        @if($wallets->isNotEmpty())
        @php
            $totalUSD = $wallets->where('currency','USD')->sum('balance');
            $totalSYP = $wallets->where('currency','SYP')->sum('balance');
            $personalCount = $wallets->filter(fn($w) => empty($w->custodian_name))->count();
            $custodyCount = $wallets->filter(fn($w) => !empty($w->custodian_name))->count();
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-4 border border-emerald-100 shadow-sm">
                <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest mb-1">إجمالي USD</p>
                <p class="text-xl font-black text-emerald-700 tracking-tighter">{{ number_format($totalUSD, 2) }} <span class="text-xs opacity-60">$</span></p>
            </div>
            <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-2xl p-4 border border-amber-100 shadow-sm">
                <p class="text-[9px] font-black text-amber-600 uppercase tracking-widest mb-1">إجمالي SYP</p>
                <p class="text-xl font-black text-amber-700 tracking-tighter">{{ number_format($totalSYP, 0) }} <span class="text-xs opacity-60">ل.س</span></p>
            </div>
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100/50 rounded-2xl p-4 border border-indigo-100 shadow-sm">
                <p class="text-[9px] font-black text-indigo-600 uppercase tracking-widest mb-1">المحافظ الشخصية</p>
                <p class="text-xl font-black text-indigo-700">{{ $personalCount }} <span class="text-xs font-bold opacity-60">محفظة</span></p>
            </div>
            <div class="bg-gradient-to-br from-violet-50 to-violet-100/50 rounded-2xl p-4 border border-violet-100 shadow-sm">
                <p class="text-[9px] font-black text-violet-600 uppercase tracking-widest mb-1">العهد المستقلة</p>
                <p class="text-xl font-black text-violet-700">{{ $custodyCount }} <span class="text-xs font-bold opacity-60">عهدة</span></p>
            </div>
        </div>
        @endif

        @php
            $gradientsPersonal = [
                ['from-indigo-500', 'to-indigo-600', 'shadow-indigo-500/20'],
                ['from-sky-500', 'to-blue-600', 'shadow-blue-500/20'],
                ['from-emerald-500', 'to-teal-600',  'shadow-emerald-500/20'],
            ];
            $gradientsCustody = [
                ['from-amber-500',  'to-orange-600', 'shadow-amber-500/20'],
                ['from-rose-500',   'to-red-600',   'shadow-rose-500/20'],
                ['from-violet-500', 'to-purple-600', 'shadow-violet-500/20'],
            ];

            $personalWallets = $wallets->filter(fn($w) => empty($w->custodian_name));
            $custodyWallets = $wallets->filter(fn($w) => !empty($w->custodian_name));
        @endphp

        {{-- ===================== 1. PERSONAL WALLETS SECTION ===================== --}}
        <div class="space-y-4">
            <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                <span class="text-xl">👤</span>
                <h2 class="text-lg font-black text-slate-800">المحافظ الشخصية المستقلة</h2>
                <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 text-xs font-black rounded-full">{{ $personalWallets->count() }}</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($personalWallets as $index => $wallet)
                @php $g = $gradientsPersonal[$index % count($gradientsPersonal)]; @endphp
                <a href="{{ route('wallets.show', $wallet->id) }}"
                   class="group relative bg-gradient-to-br {{ $g[0] }} {{ $g[1] }} rounded-3xl p-7 overflow-hidden shadow-xl {{ $g[2] }} hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 block">

                    {{-- Background decorations --}}
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="absolute -left-5 -bottom-5 w-28 h-28 bg-black/10 rounded-full blur-xl"></div>

                    <div class="relative z-10">
                        {{-- Top row --}}
                        <div class="flex justify-between items-start mb-8">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center text-2xl border border-white/30 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                                💳
                            </div>
                            <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white text-[9px] font-black uppercase tracking-widest rounded-xl border border-white/20">
                                {{ $wallet->currency }}
                            </span>
                        </div>

                        {{-- Balance --}}
                        <div class="mb-6">
                            <p class="text-white/60 text-[9px] font-black uppercase tracking-widest mb-1">الرصيد المتاح</p>
                            <p class="text-4xl font-black text-white tracking-tighter leading-none">
                                {{ number_format($wallet->balance, 0) }}
                                <span class="text-base opacity-60">{{ $wallet->currency }}</span>
                            </p>
                            @if($wallet->balance != floor($wallet->balance))
                                <p class="text-white/50 text-xs font-bold mt-0.5">{{ number_format($wallet->balance, 2) }}</p>
                            @endif
                        </div>

                        {{-- Footer --}}
                        <div class="flex items-center justify-between pt-4 border-t border-white/20">
                            <div>
                                <p class="text-white/80 font-black text-sm">{{ $wallet->name }}</p>
                            </div>
                            <div class="flex items-center gap-1.5 text-white/70 group-hover:text-white transition-colors">
                                <span class="text-[10px] font-black uppercase tracking-widest">تفاصيل</span>
                                <svg class="w-3.5 h-3.5 group-hover:translate-x-[-2px] transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l-7 7 7 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>
                @empty
                    <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-dashed border-slate-200 shadow-sm">
                        <div class="text-5xl mb-3 opacity-25">💳</div>
                        <p class="font-black text-slate-400 text-base">لا توجد محافظ شخصية مستقلة</p>
                        <p class="text-slate-300 font-bold text-xs mt-1">اضغط على زر الإضافة لإنشاء محفظة جديدة</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ===================== 2. CUSTODIES SECTION ===================== --}}
        <div class="space-y-4 pt-4">
            <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                <span class="text-xl">💼</span>
                <h2 class="text-lg font-black text-slate-800">العهد المالية بعهدة أشخاص (المستقلة)</h2>
                <span class="px-2.5 py-0.5 bg-amber-50 text-amber-700 text-xs font-black rounded-full">{{ $custodyWallets->count() }}</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($custodyWallets as $index => $wallet)
                @php $g = $gradientsCustody[$index % count($gradientsCustody)]; @endphp
                <a href="{{ route('wallets.show', $wallet->id) }}"
                   class="group relative bg-gradient-to-br {{ $g[0] }} {{ $g[1] }} rounded-3xl p-7 overflow-hidden shadow-xl {{ $g[2] }} hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 block">

                    {{-- Background decorations --}}
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="absolute -left-5 -bottom-5 w-28 h-28 bg-black/10 rounded-full blur-xl"></div>

                    <div class="relative z-10">
                        {{-- Top row --}}
                        <div class="flex justify-between items-start mb-8">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center text-2xl border border-white/30 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                                💼
                            </div>
                            <div class="flex flex-col items-end gap-1.5">
                                <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white text-[9px] font-black uppercase tracking-widest rounded-xl border border-white/20">
                                    {{ $wallet->currency }}
                                </span>
                                <span class="px-3 py-1 bg-black/25 backdrop-blur-sm text-white/95 text-[9px] font-black rounded-xl">
                                    🔑 المسؤول: {{ $wallet->custodian_name }}
                                </span>
                            </div>
                        </div>

                        {{-- Balance --}}
                        <div class="mb-6">
                            <p class="text-white/60 text-[9px] font-black uppercase tracking-widest mb-1">الرصيد المتاح بالعهدة</p>
                            <p class="text-4xl font-black text-white tracking-tighter leading-none">
                                {{ number_format($wallet->balance, 0) }}
                                <span class="text-base opacity-60">{{ $wallet->currency }}</span>
                            </p>
                            @if($wallet->balance != floor($wallet->balance))
                                <p class="text-white/50 text-xs font-bold mt-0.5">{{ number_format($wallet->balance, 2) }}</p>
                            @endif
                        </div>

                        {{-- Footer --}}
                        <div class="flex items-center justify-between pt-4 border-t border-white/20">
                            <div>
                                <p class="text-white/80 font-black text-sm">{{ $wallet->name }}</p>
                            </div>
                            <div class="flex items-center gap-1.5 text-white/70 group-hover:text-white transition-colors">
                                <span class="text-[10px] font-black uppercase tracking-widest">تفاصيل</span>
                                <svg class="w-3.5 h-3.5 group-hover:translate-x-[-2px] transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l-7 7 7 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>
                @empty
                    <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-dashed border-slate-200 shadow-sm">
                        <div class="text-5xl mb-3 opacity-25">💼</div>
                        <p class="font-black text-slate-400 text-base">لا توجد عهد مالية مستقلة</p>
                        <p class="text-slate-300 font-bold text-xs mt-1">أضف عهدة مالية لمتابعة المبالغ المسلّمة لأشخاص آخرين</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ===================== CREATE WALLET MODAL ===================== --}}
    <div x-show="showCreateModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl text-right" @click.away="showCreateModal = false">
            <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xl font-black text-gray-900">إضافة جديدة</h3>
                <button @click="showCreateModal = false" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-600 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('wallets.store') }}" method="POST" class="p-8 space-y-5">
                @csrf

                {{-- Type selector (ChoiceChip styling) --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">نوع المحفظة</label>
                    <div class="grid grid-cols-2 gap-2 bg-slate-100 p-1.5 rounded-2xl">
                        <button type="button" @click="isCustody = false"
                                :class="!isCustody ? 'bg-white text-indigo-700 shadow-md shadow-indigo-500/5' : 'text-slate-500 hover:text-slate-700'"
                                class="py-2.5 text-xs font-black rounded-xl transition-all flex items-center justify-center gap-1.5">
                            <span>💳</span> محفظة شخصية
                        </button>
                        <button type="button" @click="isCustody = true"
                                :class="isCustody ? 'bg-white text-amber-700 shadow-md shadow-amber-500/5' : 'text-slate-500 hover:text-slate-700'"
                                class="py-2.5 text-xs font-black rounded-xl transition-all flex items-center justify-center gap-1.5">
                            <span>💼</span> عهدة شخص مستقل
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">اسم المحفظة / العهدة</label>
                    <input type="text" name="name" required
                           class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-base focus:ring-2 focus:ring-indigo-500 outline-none"
                           placeholder="مثلاً: كاش شخصي، عهدة مصاريف البناء...">
                </div>

                <div x-show="isCustody" x-transition>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">اسم أمين العهدة</label>
                    <input type="text" name="custodian_name" :required="isCustody"
                           class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-base focus:ring-2 focus:ring-indigo-500 outline-none"
                           placeholder="مثال: أحمد المحمد...">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">الرصيد الافتتاحي</label>
                        <input type="number" name="balance" value="0" step="0.01" required
                               class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">العملة</label>
                        <select name="currency" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
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
                    :class="isCustody ? 'bg-amber-600 hover:bg-amber-700 shadow-amber-500/20' : 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-500/20'"
                    class="w-full text-white py-4 rounded-2xl font-black text-base shadow-lg transition-all">
                    ✓ حفظ وإضافة
                </button>
            </form>
        </div>
    </div>

</div>
</x-app-layout>
