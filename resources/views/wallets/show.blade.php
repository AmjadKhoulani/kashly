<x-app-layout>
@php
    $totalIncome = $transactions->where('type', 'income')->sum('amount');
    $totalExpense = $transactions->where('type', 'expense')->sum('amount');
    $txCount = $transactions->total();
    
    // Last 7 months activity for chart
    $monthlyData = [];
    for ($i = 6; $i >= 0; $i--) {
        $month = now()->subMonths($i);
        $monthlyData[] = [
            'label' => $month->translatedFormat('M'),
            'income' => $transactions->filter(fn($t) => $t->transaction_date->format('Y-m') === $month->format('Y-m') && $t->type === 'income')->sum('amount'),
            'expense' => $transactions->filter(fn($t) => $t->transaction_date->format('Y-m') === $month->format('Y-m') && $t->type === 'expense')->sum('amount'),
        ];
    }
    $maxVal = collect($monthlyData)->max(fn($m) => max($m['income'], $m['expense']));
    if ($maxVal == 0) $maxVal = 1;
@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/30"
     x-data="{ 
        showModal: false, 
        showReconcile: false, 
        showAccountModal: false,
        showEditModal: false,
        editTx: {},
        type: 'expense', 
        payInAlternative: false, 
        walletCurrency: '{{ $wallet->currency }}', 
        selectedCurrency: '{{ $wallet->currency }}',
        altCurrency: 'USD',
        exchangeRate: '1.0',
        openEdit(tx) { this.editTx = tx; this.showEditModal = true; }
     }">

    {{-- ===================== HEADER ===================== --}}
    <div class="bg-white border-b border-slate-100 sticky top-0 z-40 backdrop-blur-xl bg-white/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('wallets.index') }}" class="w-9 h-9 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-500 rounded-xl flex items-center justify-center transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 bg-indigo-100 rounded-xl flex items-center justify-center text-lg">💰</div>
                        <div>
                            <h1 class="text-base font-black text-slate-900 leading-none">{{ $wallet->name }}</h1>
                            @if($wallet->custodian_name)
                                <p class="text-[10px] font-bold text-amber-600 mt-0.5">بعهدة: {{ $wallet->custodian_name }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="showReconcile = true" class="hidden sm:flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-600 border border-amber-200 rounded-xl font-black text-xs hover:bg-amber-100 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        مطابقة
                    </button>
                    <button @click="showModal = true" class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black text-sm shadow-lg shadow-indigo-500/20 transition-all hover:scale-105">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span class="hidden sm:inline">إضافة عملية</span>
                        <span class="sm:hidden">إضافة</span>
                    </button>
                    <form action="{{ route('wallets.destroy', $wallet->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه المحفظة؟')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-9 h-9 bg-rose-50 text-rose-400 hover:bg-rose-600 hover:text-white rounded-xl flex items-center justify-center transition-all border border-rose-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-8">

        {{-- ===================== BALANCE HERO + STATS ===================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Balance Card --}}
            <div class="lg:col-span-2 bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800 rounded-3xl p-8 text-white relative overflow-hidden shadow-2xl shadow-indigo-500/30">
                <div class="absolute -right-16 -top-16 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute -left-8 -bottom-8 w-48 h-48 bg-purple-500/20 rounded-full blur-2xl"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <p class="text-indigo-200 text-xs font-black uppercase tracking-widest mb-2">الرصيد الحالي</p>
                            <p class="text-6xl sm:text-7xl font-black tracking-tighter leading-none">
                                {{ number_format($wallet->balance, 2) }}
                            </p>
                            <p class="text-indigo-300 text-xl font-black mt-2">{{ $wallet->currency }}</p>
                            @if($wallet->currency === 'SYP' && $sypRate > 0)
                                <div class="mt-4 flex items-center gap-3">
                                    <div class="bg-white/15 backdrop-blur-sm border border-white/20 rounded-2xl px-5 py-3">
                                        <p class="text-white/60 text-[10px] font-black uppercase tracking-widest mb-1">ما يعادل بالدولار</p>
                                        <p class="text-2xl font-black text-emerald-300 tracking-tighter">
                                            ${{ number_format($wallet->balance / $sypRate, 2) }}
                                        </p>
                                    </div>
                                    <div class="bg-white/10 border border-white/15 rounded-2xl px-4 py-3 text-center">
                                        <p class="text-white/50 text-[10px] font-black uppercase tracking-widest mb-1">سعر الصرف</p>
                                        <p class="text-sm font-black text-amber-300">{{ number_format($sypRate, 0) }} ل.س</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span class="px-4 py-2 bg-white/20 backdrop-blur-sm text-white text-xs font-black rounded-2xl border border-white/20">
                                ✅ نشط
                            </span>
                            @if($wallet->currency === 'SYP' && $sypRate > 0)
                                <span class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/20 border border-emerald-400/30 rounded-xl text-[10px] font-black text-emerald-300">
                                    <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                                    سعر لحظي
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Mini Chart --}}
                    <div class="mt-4">
                        <p class="text-indigo-300 text-[10px] font-black uppercase tracking-widest mb-3">النشاط — آخر 7 أشهر</p>
                        <div class="flex items-end gap-1.5 h-20">
                            @foreach($monthlyData as $month)
                                @php
                                    $incH = $maxVal > 0 ? round(($month['income'] / $maxVal) * 100) : 10;
                                    $expH = $maxVal > 0 ? round(($month['expense'] / $maxVal) * 100) : 10;
                                @endphp
                                <div class="flex-1 flex items-end gap-0.5 group" title="{{ $month['label'] }}">
                                    <div class="flex-1 bg-emerald-400/60 hover:bg-emerald-400 rounded-t-lg transition-all duration-300" style="height: {{ max($incH, 4) }}%"></div>
                                    <div class="flex-1 bg-rose-400/60 hover:bg-rose-400 rounded-t-lg transition-all duration-300" style="height: {{ max($expH, 4) }}%"></div>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center gap-4 mt-3">
                            <span class="flex items-center gap-1.5 text-[10px] font-black text-emerald-300"><span class="w-2 h-2 bg-emerald-400 rounded-full"></span>إيداع</span>
                            <span class="flex items-center gap-1.5 text-[10px] font-black text-rose-300"><span class="w-2 h-2 bg-rose-400 rounded-full"></span>سحب</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Side Stats --}}
            <div class="flex flex-col gap-4">
                {{-- Total Income --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex items-center gap-4 flex-1">
                    <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">إجمالي الإيداعات</p>
                        <p class="text-2xl font-black text-emerald-600 tracking-tighter truncate">+{{ number_format($totalIncome, 2) }}</p>
                        <p class="text-[10px] text-slate-400 font-bold">{{ $wallet->currency }}</p>
                    </div>
                </div>

                {{-- Total Expense --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex items-center gap-4 flex-1">
                    <div class="w-12 h-12 bg-rose-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">إجمالي المصاريف</p>
                        <p class="text-2xl font-black text-rose-600 tracking-tighter truncate">-{{ number_format($totalExpense, 2) }}</p>
                        <p class="text-[10px] text-slate-400 font-bold">{{ $wallet->currency }}</p>
                    </div>
                </div>

                {{-- Transaction Count --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex items-center gap-4 flex-1">
                    <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">إجمالي الحركات</p>
                        <p class="text-2xl font-black text-indigo-600 tracking-tighter">{{ $txCount }}</p>
                        <p class="text-[10px] text-slate-400 font-bold">عملية مسجلة</p>
                    </div>
                </div>

                @if($wallet->currency === 'SYP' && $sypRate > 0)
                {{-- USD Equivalent --}}
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-6 border border-emerald-100 shadow-sm flex items-center gap-4 flex-1">
                    <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 mb-1">
                            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">ما يعادل بالدولار</p>
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                        </div>
                        <p class="text-2xl font-black text-emerald-700 tracking-tighter truncate">${{ number_format($wallet->balance / $sypRate, 2) }}</p>
                        <p class="text-[10px] text-emerald-500 font-bold">بسعر {{ number_format($sypRate, 0) }} ل.س/دولار</p>
                    </div>
                </div>
                @endif

            </div>
        </div>

        {{-- ===================== SUB ACCOUNTS ===================== --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-50">
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center text-sm">🏛️</span>
                    الحسابات والعهد التابعة
                </h3>
                <button @click="showAccountModal = true" class="flex items-center gap-1.5 text-xs font-black text-indigo-600 hover:text-indigo-800 transition-colors bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    إضافة عهدة
                </button>
            </div>
            @if($paymentMethods->isEmpty())
                <div class="py-10 text-center text-slate-400 text-sm font-bold">لا توجد حسابات أو عهد مرتبطة.</div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-px bg-slate-100">
                    @foreach($paymentMethods as $pm)
                        <div class="bg-white p-5 flex items-center justify-between hover:bg-slate-50/80 transition-all">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-lg">
                                    @switch($pm->type)
                                        @case('bank') 🏛️ @break
                                        @case('cash') 💵 @break
                                        @default 💳
                                    @endswitch
                                </div>
                                <div>
                                    <p class="font-black text-slate-900 text-sm">{{ $pm->name }}</p>
                                    @if($pm->custodian_name)
                                        <p class="text-[10px] font-bold text-amber-600">{{ $pm->custodian_name }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-left">
                                <p class="font-black text-slate-800 text-sm">{{ number_format($pm->balance, 2) }}</p>
                                <p class="text-[10px] font-black text-slate-400 uppercase">{{ $pm->currency }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ===================== TRANSACTIONS TABLE ===================== --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-50">
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                    </span>
                    سجل الحركات
                </h3>
                <span class="text-xs font-black text-slate-400 bg-slate-50 px-3 py-1.5 rounded-lg">{{ $txCount }} عملية</span>
            </div>

            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">التاريخ</th>
                            <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">البيان</th>
                            <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">الحساب</th>
                            <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">التصنيف</th>
                            <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">المبلغ</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">إجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-slate-50/60 transition-all group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-xs font-black text-slate-400 bg-slate-50 px-2.5 py-1 rounded-lg">{{ $tx->transaction_date->format('d M Y') }}</span>
                                </td>
                                <td class="px-4 py-4 max-w-[200px]">
                                    <p class="font-black text-slate-900 text-sm truncate">
                                        {{ $tx->description ?: ($tx->categoryRelation ? $tx->categoryRelation->name : $tx->category) }}
                                    </p>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if($tx->paymentMethod)
                                        <span class="px-3 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-lg text-[11px] font-black">
                                            {{ $tx->paymentMethod->name }}
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-slate-50 text-slate-500 border border-slate-100 rounded-lg text-[11px] font-black">الرئيسي</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-3 py-1 bg-white border border-slate-100 text-slate-600 rounded-lg text-[11px] font-black shadow-sm">
                                        {{ $tx->categoryRelation ? $tx->categoryRelation->icon : '📦' }} {{ $tx->categoryRelation ? $tx->categoryRelation->name : $tx->category }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-left">
                                    <div>
                                        <span class="text-lg font-black {{ $tx->type == 'income' ? 'text-emerald-600' : ($tx->type == 'capital' ? 'text-indigo-600' : 'text-rose-600') }} tracking-tighter">
                                            {{ $tx->type == 'income' ? '+' : ($tx->type == 'capital' ? '●' : '-') }}{{ number_format($tx->original_amount ?: $tx->amount, 2) }}
                                            <span class="text-xs opacity-50 font-bold">{{ $tx->currency }}</span>
                                        </span>
                                        @if($tx->original_amount && $tx->original_amount != $tx->amount)
                                            <p class="text-[10px] text-slate-400 font-bold">≈ {{ number_format($tx->amount, 2) }} USD</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <button 
                                            @click="openEdit({
                                                id: {{ $tx->id }},
                                                description: '{{ addslashes($tx->description) }}',
                                                amount: '{{ $tx->original_amount ?: $tx->amount }}',
                                                type: '{{ $tx->type }}',
                                                date: '{{ $tx->transaction_date->format('Y-m-d') }}'
                                            })"
                                            class="p-2 bg-slate-50 border border-slate-100 text-slate-400 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 rounded-xl transition-all"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <form action="{{ route('transactions.destroy', $tx->id) }}" method="POST" onsubmit="return confirm('حذف العملية؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 bg-slate-50 border border-slate-100 text-slate-400 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50 rounded-xl transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-20 text-center">
                                    <div class="text-5xl mb-4 opacity-20">🏝️</div>
                                    <p class="text-slate-400 font-black text-sm">لا توجد حركات مسجلة بعد</p>
                                    <button @click="showModal = true" class="mt-4 text-xs font-black text-indigo-600 hover:underline">+ إضافة أول عملية</button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-slate-50">
                @forelse($transactions as $tx)
                    <div class="p-5">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 
                                    {{ $tx->type == 'income' ? 'bg-emerald-50' : ($tx->type == 'capital' ? 'bg-indigo-50' : 'bg-rose-50') }}">
                                    @if($tx->type == 'income')
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                                    @elseif($tx->type == 'capital')
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @else
                                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-black text-slate-900 text-sm truncate">
                                        {{ $tx->description ?: ($tx->categoryRelation ? $tx->categoryRelation->name : $tx->category) }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                                        <span class="text-[10px] font-black text-slate-400">{{ $tx->transaction_date->format('d M Y') }}</span>
                                        @if($tx->paymentMethod)
                                            <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">{{ $tx->paymentMethod->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="text-left flex-shrink-0 flex flex-col items-end gap-2">
                                <span class="font-black text-base {{ $tx->type == 'income' ? 'text-emerald-600' : ($tx->type == 'capital' ? 'text-indigo-600' : 'text-rose-600') }}">
                                    {{ $tx->type == 'income' ? '+' : ($tx->type == 'capital' ? '●' : '-') }}{{ number_format($tx->original_amount ?: $tx->amount, 2) }}
                                    <span class="text-xs opacity-50">{{ $tx->currency }}</span>
                                </span>
                                <div class="flex gap-1">
                                    <button
                                        @click="openEdit({
                                            id: {{ $tx->id }},
                                            description: '{{ addslashes($tx->description) }}',
                                            amount: '{{ $tx->original_amount ?: $tx->amount }}',
                                            type: '{{ $tx->type }}',
                                            date: '{{ $tx->transaction_date->format('Y-m-d') }}'
                                        })"
                                        class="p-1.5 bg-slate-50 text-slate-400 hover:text-indigo-600 rounded-lg transition-all border border-slate-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('transactions.destroy', $tx->id) }}" method="POST" onsubmit="return confirm('حذف؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-slate-50 text-slate-400 hover:text-rose-600 rounded-lg transition-all border border-slate-100">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-16 text-center">
                        <div class="text-4xl mb-4 opacity-20">🏝️</div>
                        <p class="text-slate-400 font-black text-sm">لا توجد حركات بعد</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-slate-50 bg-slate-50/30">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ========== RECONCILE MODAL ========== --}}
    <div x-show="showReconcile" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-white rounded-3xl w-full max-w-md p-8 shadow-2xl relative text-right" @click.away="showReconcile = false">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-black text-gray-900">مطابقة رصيد</h3>
                <button @click="showReconcile = false" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-600 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <p class="text-gray-500 font-bold mb-6 text-sm leading-relaxed">أدخل المبلغ الحقيقي الموجود حالياً. سيسجل النظام عملية تسوية تلقائياً بالفرق.</p>
            <form action="{{ route('wallets.reconcile', $wallet->id) }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">المبلغ الحقيقي ({{ $wallet->currency }})</label>
                    <input type="number" name="actual_balance" required step="0.01" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-lg focus:ring-2 focus:ring-amber-400 outline-none" placeholder="0.00">
                </div>
                <button type="submit" class="w-full bg-amber-500 text-white py-4 rounded-2xl font-black text-base shadow-lg shadow-amber-500/20 hover:bg-amber-600 transition-all">تأكيد المطابقة</button>
            </form>
        </div>
    </div>

    {{-- ========== ADD ACCOUNT MODAL ========== --}}
    <div x-show="showAccountModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-white rounded-3xl w-full max-w-md p-8 shadow-2xl relative text-right" @click.away="showAccountModal = false">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-black text-gray-900">إضافة عهدة / حساب</h3>
                <button @click="showAccountModal = false" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-600 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('payment-methods.store') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="association_type" value="wallet">
                <input type="hidden" name="wallet_id" value="{{ $wallet->id }}">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">اسم الحساب / العهدة</label>
                    <input type="text" name="name" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-base focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="مثلاً: صندوق الدولار...">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">اسم أمين العهدة (اختياري)</label>
                    <input type="text" name="custodian_name" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-base focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="مثلاً: محمد...">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">نوع الحساب</label>
                    <select name="type" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-base focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="cash">نقد / عهدة كاش</option>
                        <option value="bank">حساب بنكي</option>
                        <option value="credit_card">بطاقة ائتمان</option>
                        <option value="debit_card">بطاقة دفع</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">الرصيد الافتتاحي</label>
                        <input type="number" step="0.01" name="balance" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-lg focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">العملة</label>
                        <select name="currency" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-base focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="USD">USD</option>
                            <option value="SYP">SYP</option>
                            <option value="TRY">TRY</option>
                            <option value="SAR">SAR</option>
                            <option value="EUR">EUR</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-black text-base shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 transition-all">حفظ الحساب</button>
            </form>
        </div>
    </div>

    {{-- ========== ADD TRANSACTION MODAL ========== --}}
    <div x-show="showModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-white rounded-3xl w-full max-w-xl shadow-2xl relative text-right overflow-y-auto max-h-[95vh] sm:max-h-[90vh]" @click.away="showModal = false">
            <div class="sticky top-0 bg-white border-b border-slate-100 px-8 py-5 flex items-center justify-between rounded-t-3xl">
                <h3 class="text-xl font-black text-gray-900">تسجيل عملية</h3>
                <button @click="showModal = false" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-600 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('transactions.store') }}" method="POST" class="p-8 space-y-6">
                @csrf
                <input type="hidden" name="source_type" value="Wallet">
                <input type="hidden" name="source_id" value="{{ $wallet->id }}">

                {{-- Type Toggle --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">نوع العملية</label>
                    <div class="grid grid-cols-3 gap-2 p-1.5 bg-gray-100 rounded-2xl">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="income" x-model="type" class="hidden peer">
                            <div class="py-3 text-center rounded-xl text-xs font-black transition-all peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm text-slate-500">📈 إيداع</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="expense" x-model="type" class="hidden peer">
                            <div class="py-3 text-center rounded-xl text-xs font-black transition-all peer-checked:bg-white peer-checked:text-rose-600 peer-checked:shadow-sm text-slate-500">📉 سحب</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="capital" x-model="type" class="hidden peer">
                            <div class="py-3 text-center rounded-xl text-xs font-black transition-all peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm text-slate-500">💼 رأس مال</div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">المبلغ</label>
                        <input type="number" name="amount" required step="0.01" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-xl focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">التاريخ</label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">الحساب المستهدف</label>
                    <select name="payment_method_id" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                        @change="let opt = $event.target.selectedOptions[0]; selectedCurrency = opt.getAttribute('data-currency') || walletCurrency;">
                        <option value="" data-currency="{{ $wallet->currency }}">الرصيد الرئيسي ({{ $wallet->currency }})</option>
                        @foreach($paymentMethods as $pm)
                            <option value="{{ $pm->id }}" data-currency="{{ $pm->currency }}">{{ $pm->name }} ({{ $pm->currency }})</option>
                        @endforeach
                    </select>
                </div>

                <div x-data="{
                    allCats: {{ \App\Models\Category::where('is_default', true)->orWhere('user_id', auth()->id())->get()->map(fn($c) => ['id'=>$c->id,'name'=>$c->name,'icon'=>$c->icon,'type'=>$c->type])->toJson() }},
                    get filtered() {
                        let f = this.allCats.filter(c => c.type === this.$root.type);
                        if (this.$root.type === 'capital' && f.length === 0)
                            return [{ id: '', name: 'رأس مال مساهم', icon: '💼', type: 'capital' }];
                        return f;
                    }
                }">
                    <div x-show="type !== 'capital' || filtered.length > 0">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">التصنيف</label>
                        <select name="category_id" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            <template x-for="cat in filtered" :key="cat.id">
                                <option :value="cat.id" x-text="cat.icon + ' ' + cat.name"></option>
                            </template>
                        </select>
                        <p class="text-[10px] mt-1.5 px-1 font-bold"
                           :class="type==='income' ? 'text-emerald-500' : type==='capital' ? 'text-indigo-500' : 'text-rose-500'"
                           x-text="type==='income' ? '✅ تصنيفات الإيداعات' : type==='capital' ? '💼 تصنيفات رأس المال' : '🔴 تصنيفات المصاريف'">
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">وصف إضافي</label>
                    <textarea name="description" rows="2" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none resize-none" placeholder="تفاصيل إضافية..."></textarea>
                </div>

                {{-- Exchange Rate --}}
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-black text-slate-600">الدفع بعملة بديلة</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="payInAlternative" class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-5 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                    <div class="grid grid-cols-2 gap-4" x-show="payInAlternative" x-cloak>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">العملة البديلة</label>
                            <select name="currency" x-model="altCurrency" class="w-full bg-white border border-slate-200 rounded-xl p-3 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                                <option value="USD">USD</option>
                                <option value="SYP">SYP</option>
                                <option value="TRY">TRY</option>
                                <option value="SAR">SAR</option>
                                <option value="EUR">EUR</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">سعر الصرف</label>
                            <input type="number" step="0.000001" name="exchange_rate" x-model="exchangeRate" class="w-full bg-white border border-slate-200 rounded-xl p-3 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="1.0">
                        </div>
                    </div>
                    <p class="text-[11px] font-bold text-center" 
                       x-show="payInAlternative" x-cloak
                       :class="payInAlternative ? 'text-indigo-600' : 'text-slate-400'">
                        💡 1 <span x-text="altCurrency"></span> = <span x-text="exchangeRate"></span> <span x-text="selectedCurrency"></span>
                    </p>
                    <p class="text-[11px] font-bold text-slate-400 text-center" x-show="!payInAlternative">
                        سيُسجل بعملة الحساب: <span class="font-black text-indigo-600" x-text="selectedCurrency"></span>
                    </p>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-black text-base shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 transition-all">✓ تأكيد العملية</button>
            </form>
        </div>
    </div>

    {{-- ========== EDIT TRANSACTION MODAL ========== --}}
    <div x-show="showEditModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl relative text-right" @click.away="showEditModal = false">
            <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xl font-black text-gray-900">تعديل العملية</h3>
                <button @click="showEditModal = false" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-600 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form :action="'/transactions/' + editTx.id" method="POST" class="p-8 space-y-5">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">المبلغ</label>
                    <input type="number" name="amount" step="0.01" :value="editTx.amount" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">نوع العملية</label>
                    <div class="grid grid-cols-3 gap-2 p-1.5 bg-gray-100 rounded-2xl">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="income" :checked="editTx.type === 'income'" class="hidden peer">
                            <div class="py-3 text-center rounded-xl text-xs font-black transition-all peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm text-slate-500">إيداع</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="expense" :checked="editTx.type === 'expense'" class="hidden peer">
                            <div class="py-3 text-center rounded-xl text-xs font-black transition-all peer-checked:bg-white peer-checked:text-rose-600 peer-checked:shadow-sm text-slate-500">سحب</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="capital" :checked="editTx.type === 'capital'" class="hidden peer">
                            <div class="py-3 text-center rounded-xl text-xs font-black transition-all peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm text-slate-500">رأس مال</div>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">الوصف</label>
                    <input type="text" name="description" :value="editTx.description" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="وصف اختياري...">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">التاريخ</label>
                    <input type="date" name="transaction_date" :value="editTx.date" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-black text-base shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 transition-all">✓ حفظ التعديلات</button>
            </form>
        </div>
    </div>

</div>
</x-app-layout>
