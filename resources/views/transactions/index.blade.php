<x-app-layout>
<style>
    @media print {
        .no-print { display: none !important; }
        .print-show { display: block !important; }
        body { background: white !important; font-family: Arial, sans-serif; }
    }
    .custom-scroll::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .custom-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scroll::-webkit-scrollbar-thumb {
        background: rgba(156, 163, 175, 0.2);
        border-radius: 10px;
    }
    .custom-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(156, 163, 175, 0.4);
    }
</style>

<div class="min-h-screen bg-slate-900 text-slate-100 selection:bg-indigo-500 selection:text-white"
     x-data="{
         showModal: false,
         showEditModal: false,
         showFilters: false,
         editingTransaction: {},
         type: 'income',
         search: ''
     }">

    {{-- ===================== GLOWING DECORATIONS ===================== --}}
    <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute top-1/3 left-0 w-96 h-96 bg-purple-600/10 rounded-full blur-[120px] pointer-events-none"></div>

    {{-- ===================== PREMIUM HEADER ===================== --}}
    <div class="border-b border-slate-800/80 bg-slate-900/80 backdrop-blur-xl sticky top-0 z-40 no-print">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/25 flex-shrink-0 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <svg class="w-5.5 h-5.5 text-white w-[22px] h-[22px] transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-base font-black tracking-tight text-white">العمليات المالية</h1>
                    @if(request()->has('source_id'))
                        <p class="text-[10px] font-bold text-indigo-400 mt-1 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                            تصفية حسب: {{ $transactions->first()?->transactionable?->name ?? '—' }}
                            <a href="{{ route('transactions.index') }}" class="text-rose-400 hover:text-rose-300 font-extrabold hover:underline mr-1 flex items-center">× إلغاء</a>
                        </p>
                    @else
                        <p class="text-[10px] font-bold text-slate-400 mt-1 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            {{ $transactions->total() }} عملية مسجّلة إجمالاً
                        </p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                {{-- Search Input --}}
                <div class="relative hidden md:block">
                    <svg class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input x-model="search" type="text" placeholder="بحث سريع عن أي عملية..."
                           class="bg-slate-800/80 border border-slate-700/60 rounded-xl pl-4 pr-10 py-2 text-xs font-bold text-slate-200 placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none w-48 focus:w-64 transition-all duration-300">
                </div>

                {{-- Filter Button --}}
                <button @click="showFilters = !showFilters"
                    class="flex items-center gap-2 px-3.5 py-2 rounded-xl font-bold text-xs border transition-all duration-200"
                    :class="showFilters ? 'bg-indigo-600 text-white border-indigo-500 shadow-lg shadow-indigo-600/20' : 'bg-slate-800/80 text-slate-300 border-slate-700/50 hover:bg-slate-800 hover:border-slate-650'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    <span>تصفية</span>
                    @if(request()->hasAny(['month','year','type','source_type','currency','payment_method_id']))
                        <span class="w-2 h-2 bg-rose-500 rounded-full animate-bounce"></span>
                    @endif
                </button>

                {{-- Print Button --}}
                <button onclick="window.print()"
                    class="w-9 h-9 bg-slate-800/80 text-slate-400 border border-slate-700/50 rounded-xl flex items-center justify-center hover:bg-slate-700 hover:text-white transition-all no-print">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                </button>

                {{-- Add Button --}}
                <button @click="showModal = true"
                    class="flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-650 hover:from-indigo-600 hover:to-purple-700 text-white rounded-xl font-black text-xs shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/35 transition-all hover:scale-[1.03]">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="hidden sm:inline">تسجيل عملية</span>
                    <span class="sm:hidden">إضافة</span>
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 space-y-6 relative z-10">

        {{-- ===================== STATISTICS CARDS ===================== --}}
        @php
            $totalIncome  = $transactions->where('type','income')->sum('amount');
            $totalExpense = $transactions->where('type','expense')->sum('amount');
            $totalCapital = $transactions->where('type','capital')->sum('amount');
            $net = $totalIncome - $totalExpense;
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            {{-- Income Card --}}
            <div class="relative overflow-hidden rounded-2xl border border-emerald-500/20 bg-slate-800/40 p-4 backdrop-blur-sm group hover:border-emerald-500/40 transition-all duration-300">
                <div class="absolute -right-3 -top-3 w-16 h-16 bg-emerald-500/5 rounded-full blur-xl group-hover:bg-emerald-500/10 transition-all"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-emerald-400/90 tracking-wide">الإيرادات</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 flex items-center justify-center text-sm">📈</div>
                </div>
                <p class="text-xl font-extrabold text-emerald-400 tracking-tight ltr" dir="ltr">{{ number_format($totalIncome, 0) }}</p>
                <div class="flex items-center gap-1 mt-1 text-[9px] text-slate-400 font-bold">
                    <span>{{ $transactions->where('type','income')->count() }} عملية</span>
                </div>
            </div>

            {{-- Expense Card --}}
            <div class="relative overflow-hidden rounded-2xl border border-rose-500/20 bg-slate-800/40 p-4 backdrop-blur-sm group hover:border-rose-500/40 transition-all duration-300">
                <div class="absolute -right-3 -top-3 w-16 h-16 bg-rose-500/5 rounded-full blur-xl group-hover:bg-rose-500/10 transition-all"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-rose-400/90 tracking-wide">المصاريف</span>
                    <div class="w-8 h-8 rounded-xl bg-rose-500/10 flex items-center justify-center text-sm">📉</div>
                </div>
                <p class="text-xl font-extrabold text-rose-400 tracking-tight ltr" dir="ltr">{{ number_format($totalExpense, 0) }}</p>
                <div class="flex items-center gap-1 mt-1 text-[9px] text-slate-400 font-bold">
                    <span>{{ $transactions->where('type','expense')->count() }} عملية</span>
                </div>
            </div>

            {{-- Capital Card --}}
            <div class="relative overflow-hidden rounded-2xl border border-indigo-500/20 bg-slate-800/40 p-4 backdrop-blur-sm group hover:border-indigo-500/40 transition-all duration-300">
                <div class="absolute -right-3 -top-3 w-16 h-16 bg-indigo-500/5 rounded-full blur-xl group-hover:bg-indigo-500/10 transition-all"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-indigo-400/90 tracking-wide">رأس المال</span>
                    <div class="w-8 h-8 rounded-xl bg-indigo-500/10 flex items-center justify-center text-sm">💼</div>
                </div>
                <p class="text-xl font-extrabold text-indigo-400 tracking-tight ltr" dir="ltr">{{ number_format($totalCapital, 0) }}</p>
                <div class="flex items-center gap-1 mt-1 text-[9px] text-slate-400 font-bold">
                    <span>{{ $transactions->where('type','capital')->count() }} عملية</span>
                </div>
            </div>

            {{-- Net Balance Card --}}
            <div class="relative overflow-hidden rounded-2xl border p-4 backdrop-blur-sm group transition-all duration-300
                {{ $net >= 0 ? 'bg-gradient-to-br from-emerald-500/15 via-emerald-600/5 to-transparent border-emerald-500/30 hover:border-emerald-500/50' : 'bg-gradient-to-br from-rose-500/15 via-rose-600/5 to-transparent border-rose-500/30 hover:border-rose-500/50' }}">
                <div class="absolute -right-3 -top-3 w-16 h-16 rounded-full blur-xl transition-all {{ $net >= 0 ? 'bg-emerald-500/10' : 'bg-rose-500/10' }}"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-white/90 tracking-wide">الصافي</span>
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm {{ $net >= 0 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }}">
                        {{ $net >= 0 ? '✓' : '⚠️' }}
                    </div>
                </div>
                <p class="text-xl font-extrabold tracking-tight ltr {{ $net >= 0 ? 'text-emerald-450' : 'text-rose-450' }}" dir="ltr">
                    {{ $net >= 0 ? '+' : '' }}{{ number_format($net, 0) }}
                </p>
                <div class="flex items-center gap-1 mt-1 text-[9px] text-slate-400 font-bold">
                    <span>{{ $transactions->count() }} إجمالي العمليات المعروضة</span>
                </div>
            </div>
        </div>

        {{-- ===================== FILTERS OVERLAY ===================== --}}
        <div x-show="showFilters" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="no-print">
            <form action="{{ route('transactions.index') }}" method="GET"
                  class="bg-slate-800/50 border border-slate-700/60 rounded-2xl shadow-xl p-5 backdrop-blur-md">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-4">
                    <select name="month" class="bg-slate-900 border border-slate-700/50 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-350 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                        <option value="">كل الأشهر</option>
                        @foreach(['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'] as $i => $name)
                            <option value="{{ $i+1 }}" {{ request('month') == $i+1 ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <select name="year" class="bg-slate-900 border border-slate-700/50 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-350 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                        <option value="">كل السنوات</option>
                        @foreach(range(date('Y'), date('Y')-5) as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                    <select name="type" class="bg-slate-900 border border-slate-700/50 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-350 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                        <option value="">كل الأنواع</option>
                        <option value="income"  {{ request('type') == 'income'  ? 'selected' : '' }}>إيرادات</option>
                        <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>مصاريف</option>
                        <option value="capital" {{ request('type') == 'capital' ? 'selected' : '' }}>رأس مال</option>
                    </select>
                    <select name="source_type" class="bg-slate-900 border border-slate-700/50 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-350 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                        <option value="">كل المصادر</option>
                        <option value="InvestmentFund" {{ request('source_type') == 'InvestmentFund' ? 'selected' : '' }}>صناديق استثمار</option>
                        <option value="Wallet" {{ request('source_type') == 'Wallet' ? 'selected' : '' }}>محافظ شخصية</option>
                    </select>
                    <select name="currency" class="bg-slate-900 border border-slate-700/50 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-350 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                        <option value="">كل العملات</option>
                        <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="SYP" {{ request('currency') == 'SYP' ? 'selected' : '' }}>SYP</option>
                        <option value="TRY" {{ request('currency') == 'TRY' ? 'selected' : '' }}>TRY</option>
                    </select>
                    <select name="payment_method_id" class="bg-slate-900 border border-slate-700/50 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-350 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                        <option value="">كل الحسابات</option>
                        @foreach($paymentMethods as $pm)
                            <option value="{{ $pm->id }}" {{ request('payment_method_id') == $pm->id ? 'selected' : '' }}>{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-5 py-2 bg-indigo-655 text-white rounded-xl font-bold text-xs hover:bg-indigo-700 transition-all shadow-md shadow-indigo-600/10">تطبيق الفلترة</button>
                    <a href="{{ route('transactions.index') }}" class="px-5 py-2 bg-slate-700 text-slate-300 rounded-xl font-bold text-xs hover:bg-slate-650 transition-all">تصفير الكل</a>
                </div>
            </form>
        </div>

        {{-- ===================== TRANSACTIONS DATA-LIST ===================== --}}
        @php
            $grouped = $transactions->getCollection()->groupBy(fn($tx) => $tx->transaction_date->format('Y-m-d'));
        @endphp

        <div class="space-y-4">
            @forelse($grouped as $date => $group)
            @php
                $dateObj   = \Carbon\Carbon::parse($date);
                $dayIncome  = $group->where('type','income')->sum('amount');
                $dayExpense = $group->where('type','expense')->sum('amount');
            @endphp

            <div x-show="search === '' || {{ json_encode($group->map(fn($t) => strtolower(($t->description ?? '') . ' ' . ($t->category ?? '') . ' ' . ($t->transactionable?->name ?? '')))->toArray()) }}.some(item => item.includes(search.toLowerCase()))"
                 class="space-y-2">
                
                {{-- Date Group Header --}}
                <div class="flex items-center justify-between px-4 py-2 bg-slate-800/30 rounded-xl border border-slate-700/40 backdrop-blur-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-3 bg-indigo-500 rounded-full"></span>
                        <span class="text-xs font-black text-slate-350 tracking-tight">
                            {{ $dateObj->isToday() ? 'اليوم' : ($dateObj->isYesterday() ? 'أمس' : $dateObj->translatedFormat('l، d M Y')) }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 text-[9px] font-black">
                        @if($dayIncome > 0)
                            <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-400 rounded-md border border-emerald-500/20 ltr" dir="ltr">+{{ number_format($dayIncome, 0) }} USD</span>
                        @endif
                        @if($dayExpense > 0)
                            <span class="px-2 py-0.5 bg-rose-500/10 text-rose-400 rounded-md border border-rose-500/20 ltr" dir="ltr">-{{ number_format($dayExpense, 0) }} USD</span>
                        @endif
                    </div>
                </div>

                {{-- Transactions List Inside Group --}}
                <div class="divide-y divide-slate-800/80 rounded-2xl border border-slate-800/80 bg-slate-900/40 overflow-hidden">
                    @foreach($group as $tx)
                    <div class="group flex items-center justify-between gap-3 sm:gap-4 px-4 py-3.5 hover:bg-slate-800/20 transition-all relative"
                         x-data="{ openMenu: false }"
                         x-show="search === '' || '{{ strtolower(addslashes($tx->description ?? $tx->category)) }}'.includes(search.toLowerCase()) || '{{ strtolower(addslashes($tx->transactionable?->name ?? '')) }}'.includes(search.toLowerCase())">
                        
                        {{-- Left side info --}}
                        <div class="flex items-center gap-3.5 min-w-0">
                            {{-- Category icon with dynamic colored gradient background --}}
                            <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-xl flex-shrink-0 transition-transform duration-300 group-hover:scale-105 shadow-inner
                                @if($tx->type === 'income') bg-gradient-to-br from-emerald-500/10 to-teal-500/20 border border-emerald-550/20
                                @elseif($tx->type === 'capital') bg-gradient-to-br from-indigo-500/10 to-violet-500/20 border border-indigo-550/20
                                @else bg-gradient-to-br from-rose-500/10 to-pink-500/20 border border-rose-550/20 @endif">
                                {{ $tx->categoryRelation?->icon ?? ($tx->type === 'income' ? '📈' : ($tx->type === 'capital' ? '💼' : '📉')) }}
                            </div>

                            {{-- Title & descriptive badges --}}
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-extrabold text-slate-100 text-sm leading-normal group-hover:text-indigo-400 transition-colors duration-200 truncate">
                                        {{ $tx->description ?: ($tx->categoryRelation?->name ?? $tx->category) }}
                                    </p>
                                    @if($tx->invoice_path)
                                        <a href="{{ asset('storage/' . $tx->invoice_path) }}" target="_blank"
                                           class="inline-flex items-center justify-center w-5 h-5 bg-indigo-500/10 text-indigo-400 rounded-lg text-[9px] hover:bg-indigo-500 hover:text-white transition-all flex-shrink-0" title="عرض الفاتورة">📄</a>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                                    {{-- Type badge --}}
                                    <span class="text-[9px] font-black px-1.5 py-0.5 rounded-md
                                        @if($tx->type === 'income') bg-emerald-500/10 text-emerald-400
                                        @elseif($tx->type === 'capital') bg-indigo-500/10 text-indigo-400
                                        @else bg-rose-500/10 text-rose-400 @endif">
                                        @if($tx->type === 'income') إيراد @elseif($tx->type === 'capital') رأس مال @else مصروف @endif
                                    </span>

                                    @if($tx->categoryRelation && $tx->description)
                                        <span class="text-[9px] font-bold text-slate-450 bg-slate-800/80 border border-slate-700/60 px-1.5 py-0.5 rounded-md">{{ $tx->categoryRelation->name }}</span>
                                    @endif

                                    @if($tx->transactionable)
                                        <span class="text-[9px] font-bold text-slate-400 bg-slate-850 border border-slate-700/40 px-1.5 py-0.5 rounded-md truncate max-w-[120px]">
                                            {{ $tx->transactionable->name }}
                                        </span>
                                    @endif

                                    @if($tx->paymentMethod)
                                        <span class="text-[9px] font-bold text-indigo-400 bg-indigo-500/5 border border-indigo-500/10 px-1.5 py-0.5 rounded-md">
                                            🏦 {{ $tx->paymentMethod->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Right side amount & actions --}}
                        <div class="flex items-center gap-3.5">
                            <div class="text-left ltr" dir="ltr">
                                <p class="font-extrabold text-base tracking-tight leading-none
                                    @if($tx->type === 'income') text-emerald-400
                                    @elseif($tx->type === 'capital') text-indigo-400
                                    @else text-rose-450 @endif">
                                    {{ $tx->type === 'income' ? '+' : ($tx->type === 'capital' ? '' : '−') }}{{ number_format($tx->original_amount ?? $tx->amount, 0) }}
                                </p>
                                <p class="text-[9px] font-bold text-slate-500 mt-1">
                                    {{ $tx->currency ?? 'USD' }}
                                    @if($tx->original_amount && $tx->original_amount != $tx->amount)
                                        <span class="text-slate-400/80">≈ {{ number_format($tx->amount, 0) }}$</span>
                                    @endif
                                </p>
                            </div>

                            {{-- Row contextual options --}}
                            <div class="relative no-print">
                                <button @click="openMenu = !openMenu"
                                    class="w-8 h-8 text-slate-500 rounded-xl flex items-center justify-center hover:bg-slate-800 hover:text-slate-200 transition-all opacity-100 md:opacity-0 group-hover:opacity-100">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/>
                                    </svg>
                                </button>

                                <div x-show="openMenu" @click.away="openMenu = false" x-cloak x-transition
                                     class="absolute left-0 top-9 w-40 bg-slate-800 border border-slate-700/80 rounded-2xl shadow-xl z-50 overflow-hidden py-1">
                                    <button @click="
                                        editingTransaction = {
                                            id: '{{ $tx->id }}',
                                            amount: '{{ $tx->original_amount ?? $tx->amount }}',
                                            type: '{{ $tx->type }}',
                                            category: '{{ $tx->category }}',
                                            description: '{{ addslashes($tx->description) }}',
                                            transaction_date: '{{ $tx->transaction_date->format('Y-m-d') }}',
                                            payment_method_id: '{{ $tx->payment_method_id }}'
                                        };
                                        type = '{{ $tx->type }}';
                                        showEditModal = true;
                                        openMenu = false"
                                        class="w-full text-right px-4 py-2.5 text-xs font-black text-amber-400 hover:bg-amber-500/10 transition-colors flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        تعديل العملية
                                    </button>
                                    <form action="{{ route('transactions.destroy', $tx->id) }}" method="POST" onsubmit="return confirm('هل تريد حذف هذه العملية الماليّة؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-full text-right px-4 py-2.5 text-xs font-black text-rose-450 hover:bg-rose-500/10 transition-colors flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            حذف نهائي
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                    @endforeach
                </div>
            </div>
            @empty
                <div class="bg-slate-800/20 rounded-3xl border-2 border-dashed border-slate-700/60 py-24 text-center">
                    <div class="text-6xl mb-4 opacity-30">🏜️</div>
                    <p class="font-extrabold text-slate-400 text-base">لا توجد عمليات مسجّلة متطابقة</p>
                    <p class="text-slate-500 font-bold text-sm mt-1">ابدأ بتسجيل أول عملية مالية لك في النظام</p>
                    <button @click="showModal = true" class="mt-6 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black shadow-lg shadow-indigo-600/20 transition-all">+ تسجيل عملية</button>
                </div>
            @endforelse
        </div>

        {{-- Pagination Block --}}
        @if($transactions->hasPages())
            <div class="no-print pt-4 flex justify-center custom-scroll">{{ $transactions->links() }}</div>
        @endif

    </div>

    {{-- ===================== LUXURY ADD TRANSACTION MODAL ===================== --}}
    <div x-show="showModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-slate-950/80 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg shadow-2xl text-right overflow-y-auto max-h-[90vh] custom-scroll" @click.away="showModal = false">
            <div class="sticky top-0 bg-slate-900 border-b border-slate-800 px-6 py-4.5 flex items-center justify-between rounded-t-3xl z-10">
                <div>
                    <h3 class="text-base font-black text-white">تسجيل حركة مالية جديدة</h3>
                    <p class="text-[10px] font-bold text-indigo-400 mt-1"
                       x-text="type === 'income' ? 'تسجيل إيرادات ومكاسب واردة' : (type === 'expense' ? 'تسجيل مصاريف وتكاليف صادرة' : 'تسجيل إضافة رأس مال')"></p>
                </div>
                <button @click="showModal = false" class="w-8 h-8 bg-slate-800 text-slate-400 rounded-xl flex items-center justify-center hover:bg-rose-500/20 hover:text-rose-400 transition-all">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf

                {{-- Custom Segmented Control tabs --}}
                <div class="p-1.5 bg-slate-950 border border-slate-800 rounded-2xl grid grid-cols-3 gap-1">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="income" x-model="type" class="hidden peer">
                        <div class="py-2 rounded-xl text-xs font-black text-center transition-all peer-checked:bg-slate-800 peer-checked:text-emerald-400 peer-checked:shadow-sm text-slate-400 hover:text-slate-200">
                            📈 إيراد
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="expense" x-model="type" class="hidden peer">
                        <div class="py-2 rounded-xl text-xs font-black text-center transition-all peer-checked:bg-slate-800 peer-checked:text-rose-450 peer-checked:shadow-sm text-slate-400 hover:text-slate-200">
                            📉 مصروف
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="capital" x-model="type" class="hidden peer">
                        <div class="py-2 rounded-xl text-xs font-black text-center transition-all peer-checked:bg-slate-800 peer-checked:text-indigo-400 peer-checked:shadow-sm text-slate-400 hover:text-slate-200">
                            💼 رأس مال
                        </div>
                    </label>
                </div>

                {{-- High focus Amount Input --}}
                <div class="bg-slate-950 border border-slate-800 rounded-2xl p-4.5 relative overflow-hidden group">
                    <label class="block text-[9px] font-black text-slate-500 uppercase mb-2 tracking-widest text-center">القيمة المالية بالدولار (USD)</label>
                    <input type="number" step="0.01" name="amount" required
                           class="w-full bg-transparent border-0 font-extrabold text-4xl text-center outline-none placeholder-slate-800 focus:ring-0 focus:border-0"
                           :class="{
                               'text-emerald-450': type === 'income',
                               'text-rose-450': type === 'expense',
                               'text-indigo-400': type === 'capital'
                           }"
                           placeholder="0.00">
                </div>

                <div class="grid grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-[9px] font-black text-slate-400 uppercase mb-2 tracking-widest">نوع المصدر</label>
                        <select name="source_type" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 font-bold text-xs text-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                            <option value="InvestmentFund">صندوق استثماري</option>
                            <option value="Wallet">محفظة شخصية</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-slate-400 uppercase mb-2 tracking-widest">تحديد المصدر</label>
                        <select name="source_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 font-bold text-xs text-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                            @foreach($funds as $f) <option value="{{ $f->id }}">{{ $f->name }}</option> @endforeach
                            @foreach($wallets as $w) <option value="{{ $w->id }}">{{ $w->name }}</option> @endforeach
                        </select>
                    </div>
                </div>

                <div x-data="{
                    allCats: {{ \App\Models\Category::where('is_default',true)->orWhere('user_id',auth()->id())->get()->map(fn($c)=>['id'=>$c->id,'name'=>$c->name,'icon'=>$c->icon,'type'=>$c->type])->toJson() }},
                    get filtered() { return this.allCats.filter(c => c.type === this.$root.type); }
                }">
                    <label class="block text-[9px] font-black text-slate-400 uppercase mb-2 tracking-widest">التصنيف والفرع</label>
                    <select name="category_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 font-bold text-xs text-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                        <template x-for="cat in filtered" :key="cat.id">
                            <option :value="cat.id" x-text="cat.icon + ' ' + cat.name"></option>
                        </template>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-[9px] font-black text-slate-400 uppercase mb-2 tracking-widest">التاريخ</label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 font-bold text-xs text-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-slate-400 uppercase mb-2 tracking-widest">الحساب المالي</label>
                        <select name="payment_method_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 font-bold text-xs text-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                            <option value="">— بدون —</option>
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[9px] font-black text-slate-400 uppercase mb-2 tracking-widest">تفاصيل / البيان</label>
                    <input type="text" name="description"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 font-bold text-xs text-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none"
                           placeholder="أضف وصفاً مختصراً يوضّح طبيعة هذه العملية...">
                </div>

                <div>
                    <label class="block text-[9px] font-black text-slate-400 uppercase mb-2 tracking-widest">المرفقات (إيصال أو فاتورة)</label>
                    <input type="file" name="invoice" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 font-bold text-xs outline-none text-slate-450 focus:ring-2 focus:ring-indigo-500">
                </div>

                <button type="submit"
                    class="w-full py-4 rounded-2xl font-black text-xs text-white shadow-xl transition-all hover:scale-[1.01] hover:shadow-indigo-500/15"
                    :class="{
                        'bg-gradient-to-r from-emerald-500 to-emerald-600': type === 'income',
                        'bg-gradient-to-r from-rose-500 to-rose-600': type === 'expense',
                        'bg-gradient-to-r from-indigo-500 to-indigo-600': type === 'capital'
                    }">
                    ✓ تأكيد وتسجيل المعاملة الماليّة
                </button>
            </form>
        </div>
    </div>

    {{-- ===================== LUXURY EDIT TRANSACTION MODAL ===================== --}}
    <div x-show="showEditModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-slate-950/80 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg shadow-2xl text-right overflow-y-auto max-h-[90vh] custom-scroll" @click.away="showEditModal = false">
            <div class="sticky top-0 bg-slate-900 border-b border-slate-800 px-6 py-4.5 flex items-center justify-between rounded-t-3xl z-10">
                <h3 class="text-base font-black text-white">تعديل تفاصيل العملية الماليّة</h3>
                <button @click="showEditModal = false" class="w-8 h-8 bg-slate-800 text-slate-400 rounded-xl flex items-center justify-center hover:bg-rose-500/20 hover:text-rose-400 transition-all">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="`/transactions/${editingTransaction.id}`" method="POST" class="p-6 space-y-4">
                @csrf @method('PUT')

                <div class="p-1.5 bg-slate-950 border border-slate-800 rounded-2xl grid grid-cols-3 gap-1">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="income" x-model="type" class="hidden peer">
                        <div class="py-2 rounded-xl text-xs font-black text-center transition-all peer-checked:bg-slate-800 peer-checked:text-emerald-400 peer-checked:shadow-sm text-slate-400">📈 إيراد</div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="expense" x-model="type" class="hidden peer">
                        <div class="py-2 rounded-xl text-xs font-black text-center transition-all peer-checked:bg-slate-800 peer-checked:text-rose-455 peer-checked:shadow-sm text-slate-400">📉 مصروف</div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="capital" x-model="type" class="hidden peer">
                        <div class="py-2 rounded-xl text-xs font-black text-center transition-all peer-checked:bg-slate-800 peer-checked:text-indigo-400 peer-checked:shadow-sm text-slate-400">💼 رأس مال</div>
                    </label>
                </div>

                <div class="bg-slate-950 border border-slate-800 rounded-2xl p-4.5">
                    <label class="block text-[9px] font-black text-slate-500 uppercase mb-2 tracking-widest text-center">المبلغ المستحق (USD)</label>
                    <input type="number" step="0.01" name="amount" x-model="editingTransaction.amount" required
                           class="w-full bg-transparent border-0 font-extrabold text-4xl text-center outline-none focus:ring-0 focus:border-0"
                           :class="{
                               'text-emerald-450': type === 'income',
                               'text-rose-455': type === 'expense',
                               'text-indigo-400': type === 'capital'
                           }">
                </div>

                <div>
                    <label class="block text-[9px] font-black text-slate-400 uppercase mb-2 tracking-widest">البيان / الوصف</label>
                    <input type="text" name="description" x-model="editingTransaction.description"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 font-bold text-xs text-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none"
                           placeholder="تعديل تفاصيل البيان...">
                </div>

                <div class="grid grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-[9px] font-black text-slate-400 uppercase mb-2 tracking-widest">تعديل التاريخ</label>
                        <input type="date" name="transaction_date" x-model="editingTransaction.transaction_date" required
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 font-bold text-xs text-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-slate-400 uppercase mb-2 tracking-widest">تعديل الحساب</label>
                        <select name="payment_method_id" x-model="editingTransaction.payment_method_id"
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 font-bold text-xs text-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                            <option value="">— بدون —</option>
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3.5 rounded-2xl font-black text-xs shadow-lg shadow-indigo-600/10 transition-all hover:scale-[1.01]">
                    ✓ حفظ وإثبات التعديلات الجديدة
                </button>
            </form>
        </div>
    </div>

</div>
</x-app-layout>
