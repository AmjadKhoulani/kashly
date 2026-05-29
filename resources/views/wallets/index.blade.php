<x-app-layout>
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/20"
     x-data="{ showCreateModal: false }">

    {{-- ===================== STICKY HEADER ===================== --}}
    <div class="bg-white border-b border-slate-100 sticky top-0 z-40 backdrop-blur-xl bg-white/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20 flex-shrink-0">
                    <span class="text-white text-xl">💰</span>
                </div>
                <div>
                    <h1 class="text-base font-black text-slate-900 leading-none">المحافظ الشخصية</h1>
                    <p class="text-[10px] font-bold text-slate-400 mt-0.5">أدر مدخراتك وأموالك الشخصية</p>
                </div>
            </div>
            <button @click="showCreateModal = true"
                class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black text-sm shadow-lg shadow-indigo-500/20 transition-all hover:scale-105">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                </svg>
                محفظة جديدة
            </button>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-6">

        {{-- ===================== SUMMARY BAR ===================== --}}
        @if($wallets->isNotEmpty())
        @php
            $totalUSD = $wallets->where('currency','USD')->sum('balance');
            $totalSYP = $wallets->where('currency','SYP')->sum('balance');
            $totalOther = $wallets->whereNotIn('currency',['USD','SYP'])->count();
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-4 border border-emerald-100 shadow-sm">
                <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest mb-1">إجمالي USD</p>
                <p class="text-xl font-black text-emerald-700 tracking-tighter">{{ number_format($totalUSD, 2) }} <span class="text-xs opacity-60">$</span></p>
            </div>
            <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-2xl p-4 border border-amber-100 shadow-sm">
                <p class="text-[9px] font-black text-amber-600 uppercase tracking-widest mb-1">إجمالي SYP</p>
                <p class="text-xl font-black text-amber-700 tracking-tighter">{{ number_format($totalSYP, 0) }} <span class="text-xs opacity-60">ل.س</span></p>
            </div>
            <div class="bg-gradient-to-br from-indigo-50 to-violet-50 rounded-2xl p-4 border border-indigo-100 shadow-sm col-span-2 sm:col-span-1">
                <p class="text-[9px] font-black text-indigo-600 uppercase tracking-widest mb-1">عدد المحافظ</p>
                <p class="text-xl font-black text-indigo-700">{{ $wallets->count() }} <span class="text-xs font-bold opacity-60">محفظة</span></p>
            </div>
        </div>
        @endif

        {{-- ===================== WALLETS GRID ===================== --}}
        @php
            $gradients = [
                ['from-indigo-500', 'to-violet-600', 'shadow-indigo-500/25'],
                ['from-emerald-500', 'to-teal-600',  'shadow-emerald-500/25'],
                ['from-amber-500',  'to-orange-600', 'shadow-amber-500/25'],
                ['from-rose-500',   'to-pink-600',   'shadow-rose-500/25'],
                ['from-sky-500',    'to-cyan-600',   'shadow-sky-500/25'],
                ['from-violet-500', 'to-purple-600', 'shadow-violet-500/25'],
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($wallets as $index => $wallet)
            @php $g = $gradients[$index % count($gradients)]; @endphp
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
                        <div class="flex flex-col items-end gap-1.5">
                            <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white text-[9px] font-black uppercase tracking-widest rounded-xl border border-white/20">
                                {{ $wallet->currency }}
                            </span>
                            @if($wallet->custodian_name)
                                <span class="px-3 py-1 bg-black/20 backdrop-blur-sm text-white/80 text-[9px] font-black rounded-xl">
                                    🔑 {{ $wallet->custodian_name }}
                                </span>
                            @endif
                        </div>
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
                <div class="col-span-full py-28 text-center bg-white rounded-3xl border border-dashed border-slate-200 shadow-sm">
                    <div class="text-6xl mb-4 opacity-20">🕳️</div>
                    <p class="font-black text-slate-400 text-lg">لا توجد محافظ شخصية</p>
                    <p class="text-slate-300 font-bold text-sm mt-1">أنشئ محفظتك الأولى لمتابعة أموالك الشخصية</p>
                    <button @click="showCreateModal = true" class="mt-5 text-sm font-black text-indigo-600 hover:underline">
                        + إنشاء محفظة جديدة
                    </button>
                </div>
            @endforelse
        </div>

    </div>

    {{-- ===================== CREATE WALLET MODAL ===================== --}}
    <div x-show="showCreateModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl text-right" @click.away="showCreateModal = false">
            <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xl font-black text-gray-900">محفظة جديدة</h3>
                <button @click="showCreateModal = false" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-600 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('wallets.store') }}" method="POST" class="p-8 space-y-5">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">اسم المحفظة</label>
                    <input type="text" name="name" required
                           class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-base focus:ring-2 focus:ring-indigo-500 outline-none"
                           placeholder="مثلاً: كاش شخصي، صندوق الطوارئ...">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">أمين العهدة (اختياري)</label>
                    <input type="text" name="custodian_name"
                           class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-base focus:ring-2 focus:ring-indigo-500 outline-none"
                           placeholder="اسم الشخص المسؤول...">
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
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-black text-base shadow-lg shadow-indigo-500/20 transition-all">
                    ✓ إنشاء المحفظة
                </button>
            </form>
        </div>
    </div>

</div>
</x-app-layout>
