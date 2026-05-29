<x-app-layout>
<style>
    @media print {
        .no-print { display: none !important; }
        .print-show { display: block !important; }
        body { background: white !important; font-family: Arial, sans-serif; }
    }
</style>

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/20"
     x-data="{
         showModal: false,
         showEditModal: false,
         showFilters: false,
         editingTransaction: {},
         type: 'income',
         search: ''
     }">

    {{-- ===================== STICKY HEADER ===================== --}}
    <div class="bg-white/95 border-b border-slate-100 sticky top-0 z-40 backdrop-blur-xl no-print">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-xl flex items-center justify-center shadow-md shadow-indigo-500/20 flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-white w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-sm font-black text-slate-900 leading-none">العمليات المالية</h1>
                    @if(request()->has('source_id'))
                        <p class="text-[10px] font-bold text-indigo-500 mt-0.5">
                            فلترة بـ {{ $transactions->first()?->transactionable?->name ?? '—' }}
                            <a href="{{ route('transactions.index') }}" class="text-rose-500 hover:underline mr-1">× إلغاء</a>
                        </p>
                    @else
                        <p class="text-[10px] font-bold text-slate-400 mt-0.5">{{ $transactions->total() }} عملية مسجّلة</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Search --}}
                <div class="relative hidden sm:block">
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input x-model="search" type="text" placeholder="بحث..."
                           class="bg-slate-50 border border-slate-200 rounded-xl pl-3 pr-8 py-2 text-sm font-bold text-slate-700 placeholder-slate-400 focus:ring-2 focus:ring-indigo-400 focus:border-transparent outline-none w-40 focus:w-52 transition-all">
                </div>

                {{-- Filter --}}
                <button @click="showFilters = !showFilters"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl font-black text-xs border transition-all"
                    :class="showFilters ? 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-500/20' : 'bg-slate-50 text-slate-600 border-slate-200 hover:border-indigo-300'">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    فلترة
                    @if(request()->hasAny(['month','year','type','source_type','currency','payment_method_id']))
                        <span class="w-1.5 h-1.5 bg-rose-400 rounded-full"></span>
                    @endif
                </button>

                {{-- Print --}}
                <button onclick="window.print()"
                    class="w-8 h-8 bg-slate-50 text-slate-500 border border-slate-200 rounded-xl flex items-center justify-center hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all no-print">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                </button>

                {{-- Add --}}
                <button @click="showModal = true"
                    class="flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black text-sm shadow-md shadow-indigo-500/20 transition-all hover:scale-105">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="hidden sm:inline">عملية</span>
                    <span class="sm:hidden">+</span>
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-5 space-y-4">

        {{-- ===================== STATS ===================== --}}
        @php
            $totalIncome  = $transactions->where('type','income')->sum('amount');
            $totalExpense = $transactions->where('type','expense')->sum('amount');
            $totalCapital = $transactions->where('type','capital')->sum('amount');
            $net = $totalIncome - $totalExpense;
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white rounded-2xl p-3 sm:p-4 border border-emerald-100 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 left-0 w-12 h-12 bg-emerald-500/10 rounded-br-2xl flex items-center justify-center">
                    <span class="text-base">📈</span>
                </div>
                <p class="text-[8px] sm:text-[9px] font-black text-emerald-600 uppercase tracking-widest">إيرادات</p>
                <p class="text-sm sm:text-lg font-black text-emerald-700 tracking-tighter mt-0.5 ltr" dir="ltr">{{ number_format($totalIncome, 0) }}</p>
                <p class="text-[8px] font-bold text-emerald-400 mt-0.5 hidden sm:block">{{ $transactions->where('type','income')->count() }} عملية</p>
            </div>

            <div class="bg-white rounded-2xl p-3 sm:p-4 border border-rose-100 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 left-0 w-12 h-12 bg-rose-500/10 rounded-br-2xl flex items-center justify-center">
                    <span class="text-base">📉</span>
                </div>
                <p class="text-[8px] sm:text-[9px] font-black text-rose-600 uppercase tracking-widest">مصاريف</p>
                <p class="text-sm sm:text-lg font-black text-rose-700 tracking-tighter mt-0.5 ltr" dir="ltr">{{ number_format($totalExpense, 0) }}</p>
                <p class="text-[8px] font-bold text-rose-400 mt-0.5 hidden sm:block">{{ $transactions->where('type','expense')->count() }} عملية</p>
            </div>

            <div class="bg-white rounded-2xl p-3 sm:p-4 border border-indigo-100 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 left-0 w-12 h-12 bg-indigo-500/10 rounded-br-2xl flex items-center justify-center">
                    <span class="text-base">💼</span>
                </div>
                <p class="text-[8px] sm:text-[9px] font-black text-indigo-600 uppercase tracking-widest">رأس مال</p>
                <p class="text-sm sm:text-lg font-black text-indigo-700 tracking-tighter mt-0.5 ltr" dir="ltr">{{ number_format($totalCapital, 0) }}</p>
                <p class="text-[8px] font-bold text-indigo-400 mt-0.5 hidden sm:block">{{ $transactions->where('type','capital')->count() }} عملية</p>
            </div>

            <div class="rounded-2xl p-3 sm:p-4 border shadow-sm relative overflow-hidden
                {{ $net >= 0 ? 'bg-gradient-to-br from-emerald-500 to-teal-600 border-emerald-400' : 'bg-gradient-to-br from-rose-500 to-pink-600 border-rose-400' }}">
                <p class="text-[8px] sm:text-[9px] font-black text-white/70 uppercase tracking-widest">صافي</p>
                <p class="text-sm sm:text-lg font-black text-white tracking-tighter mt-0.5 ltr" dir="ltr">
                    {{ $net >= 0 ? '+' : '' }}{{ number_format($net, 0) }}
                </p>
                <p class="text-[8px] font-bold text-white/60 mt-0.5 hidden sm:block">{{ $transactions->count() }} إجمالاً</p>
            </div>
        </div>

        {{-- ===================== FILTERS ===================== --}}
        <div x-show="showFilters" x-cloak x-transition.duration.200ms class="no-print">
            <form action="{{ route('transactions.index') }}" method="GET"
                  class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5 mb-3">
                    <select name="month" class="bg-slate-50 border-0 rounded-xl px-3 py-2.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-400 outline-none">
                        <option value="">كل الأشهر</option>
                        @foreach(['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'] as $i => $name)
                            <option value="{{ $i+1 }}" {{ request('month') == $i+1 ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <select name="year" class="bg-slate-50 border-0 rounded-xl px-3 py-2.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-400 outline-none">
                        <option value="">كل السنوات</option>
                        @foreach(range(date('Y'), date('Y')-5) as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                    <select name="type" class="bg-slate-50 border-0 rounded-xl px-3 py-2.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-400 outline-none">
                        <option value="">كل الأنواع</option>
                        <option value="income"  {{ request('type') == 'income'  ? 'selected' : '' }}>إيرادات</option>
                        <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>مصاريف</option>
                        <option value="capital" {{ request('type') == 'capital' ? 'selected' : '' }}>رأس مال</option>
                    </select>
                    <select name="source_type" class="bg-slate-50 border-0 rounded-xl px-3 py-2.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-400 outline-none">
                        <option value="">كل المصادر</option>
                        <option value="InvestmentFund" {{ request('source_type') == 'InvestmentFund' ? 'selected' : '' }}>صناديق استثمار</option>
                        <option value="Wallet" {{ request('source_type') == 'Wallet' ? 'selected' : '' }}>محافظ شخصية</option>
                    </select>
                    <select name="currency" class="bg-slate-50 border-0 rounded-xl px-3 py-2.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-400 outline-none">
                        <option value="">كل العملات</option>
                        <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="SYP" {{ request('currency') == 'SYP' ? 'selected' : '' }}>SYP</option>
                        <option value="TRY" {{ request('currency') == 'TRY' ? 'selected' : '' }}>TRY</option>
                    </select>
                    <select name="payment_method_id" class="bg-slate-50 border-0 rounded-xl px-3 py-2.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-400 outline-none">
                        <option value="">كل الحسابات</option>
                        @foreach($paymentMethods as $pm)
                            <option value="{{ $pm->id }}" {{ request('payment_method_id') == $pm->id ? 'selected' : '' }}>{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-xl font-black text-xs hover:bg-indigo-700 transition-all shadow-md shadow-indigo-500/20">تطبيق</button>
                    <a href="{{ route('transactions.index') }}" class="px-5 py-2 bg-slate-100 text-slate-500 rounded-xl font-black text-xs hover:bg-slate-200 transition-all">تصفير</a>
                </div>
            </form>
        </div>

        {{-- ===================== TRANSACTIONS — GROUPED BY DATE ===================== --}}
        @php
            $grouped = $transactions->getCollection()->groupBy(fn($tx) => $tx->transaction_date->format('Y-m-d'));
        @endphp

        @forelse($grouped as $date => $group)
        @php
            $dateObj   = \Carbon\Carbon::parse($date);
            $dayIncome  = $group->where('type','income')->sum('amount');
            $dayExpense = $group->where('type','expense')->sum('amount');
        @endphp

        <div x-show="search === '' || {{ json_encode($group->map(fn($t) => strtolower(($t->description ?? '') . ' ' . ($t->category ?? '') . ' ' . ($t->transactionable?->name ?? '')))->toArray()) }}.some(item => item.includes(search.toLowerCase()))">
            {{-- Date Header --}}
            <div class="flex items-center justify-between mb-2.5 px-3 py-2 bg-slate-50 border border-slate-100/70 rounded-xl shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div>
                    <span class="text-xs font-black text-slate-700 tracking-tight">
                        {{ $dateObj->isToday() ? 'اليوم' : ($dateObj->isYesterday() ? 'أمس' : $dateObj->translatedFormat('l، d M Y')) }}
                    </span>
                </div>
                <div class="flex items-center gap-2 text-[10px] font-black">
                    @if($dayIncome > 0)
                        <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-600 rounded-lg border border-emerald-100/60 ltr" dir="ltr">+{{ number_format($dayIncome, 0) }} USD</span>
                    @endif
                    @if($dayExpense > 0)
                        <span class="px-2.5 py-0.5 bg-rose-50 text-rose-600 rounded-lg border border-rose-100/60 ltr" dir="ltr">-{{ number_format($dayExpense, 0) }} USD</span>
                    @endif
                </div>
            </div>

            {{-- Transactions in this day --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm divide-y divide-slate-50">
                @foreach($group as $tx)
                <div class="group flex items-center gap-3 sm:gap-4 px-4 py-3.5 hover:bg-slate-50/70 transition-all"
                     x-data="{ openMenu: false }"
                     x-show="search === '' || '{{ strtolower(addslashes($tx->description ?? $tx->category)) }}'.includes(search.toLowerCase()) || '{{ strtolower(addslashes($tx->transactionable?->name ?? '')) }}'.includes(search.toLowerCase())">

                    {{-- Icon --}}
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl flex items-center justify-center text-xl flex-shrink-0 transition-transform group-hover:scale-105
                        @if($tx->type === 'income') bg-gradient-to-br from-emerald-100 to-teal-100
                        @elseif($tx->type === 'capital') bg-gradient-to-br from-indigo-100 to-violet-100
                        @else bg-gradient-to-br from-rose-100 to-pink-100 @endif">
                        {{ $tx->categoryRelation?->icon ?? ($tx->type === 'income' ? '📈' : ($tx->type === 'capital' ? '💼' : '📉')) }}
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-black text-slate-900 text-sm leading-none group-hover:text-indigo-700 transition-colors truncate">
                                {{ $tx->description ?: ($tx->categoryRelation?->name ?? $tx->category) }}
                            </p>
                            @if($tx->invoice_path)
                                <a href="{{ asset('storage/' . $tx->invoice_path) }}" target="_blank"
                                   class="inline-flex items-center justify-center w-4 h-4 bg-indigo-50 text-indigo-500 rounded text-[8px] hover:bg-indigo-500 hover:text-white transition-all flex-shrink-0" title="فاتورة">📄</a>
                            @endif
                        </div>
                        <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                            {{-- Type badge --}}
                            <span class="text-[9px] font-black px-1.5 py-0.5 rounded-md
                                @if($tx->type === 'income') bg-emerald-50 text-emerald-600
                                @elseif($tx->type === 'capital') bg-indigo-50 text-indigo-600
                                @else bg-rose-50 text-rose-600 @endif">
                                @if($tx->type === 'income') إيراد @elseif($tx->type === 'capital') رأس مال @else مصروف @endif
                            </span>

                            @if($tx->categoryRelation && $tx->description)
                                <span class="text-[9px] font-bold text-slate-400 bg-slate-50 border border-slate-100 px-1.5 py-0.5 rounded-md">{{ $tx->categoryRelation->name }}</span>
                            @endif

                            @if($tx->transactionable)
                                <span class="text-[9px] font-bold text-slate-400 bg-slate-100/65 px-1.5 py-0.5 rounded-md truncate max-w-[100px]">
                                    {{ $tx->transactionable->name }}
                                </span>
                            @endif

                            @if($tx->paymentMethod)
                                <span class="text-[9px] font-bold text-indigo-500 bg-indigo-50/50 border border-indigo-100/30 px-1.5 py-0.5 rounded-md">
                                    🏦 {{ $tx->paymentMethod->name }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Amount --}}
                    <div class="text-left flex-shrink-0 ltr" dir="ltr">
                        <p class="font-black text-base tracking-tight leading-none
                            @if($tx->type === 'income') text-emerald-600
                            @elseif($tx->type === 'capital') text-indigo-600
                            @else text-rose-600 @endif">
                            {{ $tx->type === 'income' ? '+' : ($tx->type === 'capital' ? '' : '−') }}{{ number_format($tx->original_amount ?? $tx->amount, 0) }}
                        </p>
                        <p class="text-[9px] font-bold text-slate-400 mt-0.5">
                            {{ $tx->currency ?? 'USD' }}
                            @if($tx->original_amount && $tx->original_amount != $tx->amount)
                                <span class="text-slate-300">≈ {{ number_format($tx->amount,0) }}$</span>
                            @endif
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="relative flex-shrink-0">
                        <button @click="openMenu = !openMenu"
                            class="w-7 h-7 text-slate-400 rounded-lg flex items-center justify-center hover:bg-slate-100 hover:text-slate-600 transition-all opacity-100 sm:opacity-0 group-hover:opacity-100">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/>
                            </svg>
                        </button>

                        <div x-show="openMenu" @click.away="openMenu = false" x-cloak x-transition
                             class="absolute left-0 top-8 w-40 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 overflow-hidden py-1">
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
                                class="w-full text-right px-4 py-2.5 text-xs font-black text-amber-600 hover:bg-amber-50 transition-colors flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                تعديل
                            </button>
                            <form action="{{ route('transactions.destroy', $tx->id) }}" method="POST" onsubmit="return confirm('حذف؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full text-right px-4 py-2.5 text-xs font-black text-rose-600 hover:bg-rose-50 transition-colors flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    حذف
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
                @endforeach
            </div>
        </div>
        @empty
            <div class="bg-white rounded-2xl border border-dashed border-slate-200 py-24 text-center shadow-sm">
                <div class="text-5xl mb-4 opacity-20">🏜️</div>
                <p class="font-black text-slate-400 text-base">لا توجد عمليات</p>
                <p class="text-slate-300 font-bold text-sm mt-1">ابدأ بتسجيل أول عملية مالية</p>
                <button @click="showModal = true" class="mt-5 text-sm font-black text-indigo-600 hover:underline">+ تسجيل عملية</button>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if($transactions->hasPages())
            <div class="no-print pt-1">{{ $transactions->links() }}</div>
        @endif

    </div>

    {{-- ===================== ADD MODAL ===================== --}}
    <div x-show="showModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl text-right overflow-y-auto max-h-[95vh]" @click.away="showModal = false">
            <div class="sticky top-0 bg-white border-b border-slate-100 px-6 py-4 flex items-center justify-between rounded-t-3xl">
                <div>
                    <h3 class="text-lg font-black text-gray-900">عملية مالية جديدة</h3>
                    <p class="text-[10px] font-bold text-slate-400 mt-0.5"
                       x-text="type === 'income' ? 'تسجيل إيراد أو دخل جديد' : (type === 'expense' ? 'تسجيل مصروف أو التزام' : 'تسجيل حركة رأس مال')"></p>
                </div>
                <button @click="showModal = false" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-600 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf

                {{-- Type Tabs --}}
                <div class="p-1 bg-slate-100 rounded-2xl grid grid-cols-3 gap-1">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="income" x-model="type" class="hidden peer">
                        <div class="py-2.5 text-center rounded-xl text-xs font-black transition-all peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm text-slate-500 hover:text-slate-700">
                            📈 إيراد
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="expense" x-model="type" class="hidden peer">
                        <div class="py-2.5 text-center rounded-xl text-xs font-black transition-all peer-checked:bg-white peer-checked:text-rose-600 peer-checked:shadow-sm text-slate-500 hover:text-slate-700">
                            📉 مصروف
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="capital" x-model="type" class="hidden peer">
                        <div class="py-2.5 text-center rounded-xl text-xs font-black transition-all peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm text-slate-500 hover:text-slate-700">
                            💼 رأس مال
                        </div>
                    </label>
                </div>

                {{-- Amount (big, prominent) --}}
                <div class="bg-slate-50 rounded-2xl p-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest text-center">المبلغ</label>
                    <input type="number" step="0.01" name="amount" required
                           class="w-full bg-transparent border-0 font-black text-4xl text-center outline-none placeholder-slate-300 focus:ring-0"
                           :class="{
                               'text-emerald-600': type === 'income',
                               'text-rose-600': type === 'expense',
                               'text-indigo-600': type === 'capital'
                           }"
                           placeholder="0.00">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">المصدر</label>
                        <select name="source_type" class="w-full bg-gray-50 border-0 rounded-xl p-3 font-bold text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                            <option value="InvestmentFund">صندوق استثمار</option>
                            <option value="Wallet">محفظة شخصية</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">تحديد المصدر</label>
                        <select name="source_id" class="w-full bg-gray-50 border-0 rounded-xl p-3 font-bold text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                            @foreach($funds as $f) <option value="{{ $f->id }}">{{ $f->name }}</option> @endforeach
                            @foreach($wallets as $w) <option value="{{ $w->id }}">{{ $w->name }}</option> @endforeach
                        </select>
                    </div>
                </div>

                <div x-data="{
                    allCats: {{ \App\Models\Category::where('is_default',true)->orWhere('user_id',auth()->id())->get()->map(fn($c)=>['id'=>$c->id,'name'=>$c->name,'icon'=>$c->icon,'type'=>$c->type])->toJson() }},
                    get filtered() { return this.allCats.filter(c => c.type === this.$root.type); }
                }">
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">التصنيف</label>
                    <select name="category_id" class="w-full bg-gray-50 border-0 rounded-xl p-3 font-bold text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                        <template x-for="cat in filtered" :key="cat.id">
                            <option :value="cat.id" x-text="cat.icon + ' ' + cat.name"></option>
                        </template>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">التاريخ</label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                               class="w-full bg-gray-50 border-0 rounded-xl p-3 font-bold text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">الحساب</label>
                        <select name="payment_method_id" class="w-full bg-gray-50 border-0 rounded-xl p-3 font-bold text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                            <option value="">— بدون —</option>
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">البيان</label>
                    <input type="text" name="description"
                           class="w-full bg-gray-50 border-0 rounded-xl p-3 font-bold text-sm focus:ring-2 focus:ring-indigo-400 outline-none"
                           placeholder="وصف مختصر للعملية...">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">فاتورة / إيصال</label>
                    <input type="file" name="invoice" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full bg-gray-50 border-0 rounded-xl p-3 font-bold text-sm outline-none text-slate-500">
                </div>

                <button type="submit"
                    class="w-full py-3.5 rounded-2xl font-black text-sm text-white shadow-lg transition-all hover:scale-[1.01]"
                    :class="{
                        'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-500/20': type === 'income',
                        'bg-rose-600 hover:bg-rose-700 shadow-rose-500/20': type === 'expense',
                        'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-500/20': type === 'capital'
                    }">
                    ✓ تأكيد العملية
                </button>
            </form>
        </div>
    </div>

    {{-- ===================== EDIT MODAL ===================== --}}
    <div x-show="showEditModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl text-right overflow-y-auto max-h-[95vh]" @click.away="showEditModal = false">
            <div class="sticky top-0 bg-white border-b border-slate-100 px-6 py-4 flex items-center justify-between rounded-t-3xl">
                <h3 class="text-lg font-black text-gray-900">تعديل العملية</h3>
                <button @click="showEditModal = false" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-600 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="`/transactions/${editingTransaction.id}`" method="POST" class="p-6 space-y-4">
                @csrf @method('PUT')

                <div class="p-1 bg-slate-100 rounded-2xl grid grid-cols-3 gap-1">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="income" x-model="type" class="hidden peer">
                        <div class="py-2.5 text-center rounded-xl text-xs font-black transition-all peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm text-slate-500">📈 إيراد</div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="expense" x-model="type" class="hidden peer">
                        <div class="py-2.5 text-center rounded-xl text-xs font-black transition-all peer-checked:bg-white peer-checked:text-rose-600 peer-checked:shadow-sm text-slate-500">📉 مصروف</div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="capital" x-model="type" class="hidden peer">
                        <div class="py-2.5 text-center rounded-xl text-xs font-black transition-all peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm text-slate-500">💼 رأس مال</div>
                    </label>
                </div>

                <div class="bg-slate-50 rounded-2xl p-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest text-center">المبلغ</label>
                    <input type="number" step="0.01" name="amount" x-model="editingTransaction.amount" required
                           class="w-full bg-transparent border-0 font-black text-4xl text-center outline-none focus:ring-0"
                           :class="{
                               'text-emerald-600': type === 'income',
                               'text-rose-600': type === 'expense',
                               'text-indigo-600': type === 'capital'
                           }">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">البيان</label>
                    <input type="text" name="description" x-model="editingTransaction.description"
                           class="w-full bg-gray-50 border-0 rounded-xl p-3 font-bold text-sm focus:ring-2 focus:ring-indigo-400 outline-none"
                           placeholder="بيان العملية...">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">التاريخ</label>
                        <input type="date" name="transaction_date" x-model="editingTransaction.transaction_date" required
                               class="w-full bg-gray-50 border-0 rounded-xl p-3 font-bold text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">الحساب</label>
                        <select name="payment_method_id" x-model="editingTransaction.payment_method_id"
                                class="w-full bg-gray-50 border-0 rounded-xl p-3 font-bold text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                            <option value="">— بدون —</option>
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3.5 rounded-2xl font-black text-sm shadow-md shadow-indigo-500/20 transition-all">
                    ✓ حفظ التعديلات
                </button>
            </form>
        </div>
    </div>

</div>
</x-app-layout>
