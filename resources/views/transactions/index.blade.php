<x-app-layout>
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
    }
</style>

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/20"
     x-data="{
         showModal: false,
         showEditModal: false,
         showFilters: false,
         editingTransaction: {},
         type: 'income'
     }">

    {{-- ===================== STICKY HEADER ===================== --}}
    <div class="bg-white border-b border-slate-100 sticky top-0 z-40 backdrop-blur-xl bg-white/90 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20 flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-base font-black text-slate-900 leading-none">العمليات المالية</h1>
                    @if(request()->has('source_id'))
                        <p class="text-[10px] font-bold text-indigo-500 mt-0.5">
                            فلترة: {{ $transactions->first()?->transactionable?->name ?? 'مصدر محدد' }}
                            <a href="{{ route('transactions.index') }}" class="text-rose-500 hover:underline mr-1">× إلغاء</a>
                        </p>
                    @else
                        <p class="text-[10px] font-bold text-slate-400 mt-0.5">سجل كامل لجميع التدفقات المالية</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Filter Toggle --}}
                <button @click="showFilters = !showFilters"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl font-black text-sm border transition-all"
                    :class="showFilters ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 text-slate-600 border-slate-200 hover:border-indigo-300'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    فلترة
                    @if(request()->hasAny(['month','year','type','source_type','currency','payment_method_id']))
                        <span class="w-2 h-2 bg-rose-500 rounded-full"></span>
                    @endif
                </button>

                {{-- Print --}}
                <button onclick="window.print()"
                    class="w-9 h-9 bg-amber-50 text-amber-600 border border-amber-100 rounded-xl flex items-center justify-center hover:bg-amber-500 hover:text-white transition-all no-print">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                </button>

                {{-- Add --}}
                <button @click="showModal = true"
                    class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black text-sm shadow-lg shadow-indigo-500/20 transition-all hover:scale-105">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                    </svg>
                    عملية جديدة
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">

        {{-- ===================== STATS BAR ===================== --}}
        @php
            $totalIncome  = $transactions->where('type', 'income')->sum('amount');
            $totalExpense = $transactions->where('type', 'expense')->sum('amount');
            $totalCapital = $transactions->where('type', 'capital')->sum('amount');
            $net = $totalIncome - $totalExpense;
        @endphp
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-4 border border-emerald-100 shadow-sm">
                <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest mb-1">إجمالي الإيرادات</p>
                <p class="text-xl font-black text-emerald-700 tracking-tighter">{{ number_format($totalIncome, 0) }}</p>
                <p class="text-[9px] font-bold text-emerald-500 mt-0.5">{{ $transactions->where('type','income')->count() }} عملية</p>
            </div>
            <div class="bg-gradient-to-br from-rose-50 to-pink-50 rounded-2xl p-4 border border-rose-100 shadow-sm">
                <p class="text-[9px] font-black text-rose-600 uppercase tracking-widest mb-1">إجمالي المصاريف</p>
                <p class="text-xl font-black text-rose-700 tracking-tighter">{{ number_format($totalExpense, 0) }}</p>
                <p class="text-[9px] font-bold text-rose-500 mt-0.5">{{ $transactions->where('type','expense')->count() }} عملية</p>
            </div>
            <div class="bg-gradient-to-br from-indigo-50 to-violet-50 rounded-2xl p-4 border border-indigo-100 shadow-sm">
                <p class="text-[9px] font-black text-indigo-600 uppercase tracking-widest mb-1">رأس المال</p>
                <p class="text-xl font-black text-indigo-700 tracking-tighter">{{ number_format($totalCapital, 0) }}</p>
                <p class="text-[9px] font-bold text-indigo-500 mt-0.5">{{ $transactions->where('type','capital')->count() }} عملية</p>
            </div>
            <div class="rounded-2xl p-4 border shadow-sm {{ $net >= 0 ? 'bg-gradient-to-br from-emerald-600 to-teal-700 border-emerald-500' : 'bg-gradient-to-br from-rose-600 to-pink-700 border-rose-500' }}">
                <p class="text-[9px] font-black text-white/60 uppercase tracking-widest mb-1">صافي التدفق</p>
                <p class="text-xl font-black text-white tracking-tighter">{{ $net >= 0 ? '+' : '' }}{{ number_format($net, 0) }}</p>
                <p class="text-[9px] font-bold text-white/60 mt-0.5">{{ $transactions->count() }} عملية إجمالاً</p>
            </div>
        </div>

        {{-- ===================== FILTERS ===================== --}}
        <div x-show="showFilters" x-cloak x-transition class="no-print">
            <form action="{{ route('transactions.index') }}" method="GET"
                  class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    <select name="month" class="bg-slate-50 border-0 rounded-xl px-3 py-2.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-400 outline-none">
                        <option value="">كل الأشهر</option>
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                {{ ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'][$m-1] }}
                            </option>
                        @endforeach
                    </select>

                    <select name="year" class="bg-slate-50 border-0 rounded-xl px-3 py-2.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-400 outline-none">
                        <option value="">كل السنوات</option>
                        @foreach(range(date('Y'), date('Y')-5) as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>

                    <select name="source_type" class="bg-slate-50 border-0 rounded-xl px-3 py-2.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-400 outline-none">
                        <option value="">كل المصادر</option>
                        <option value="InvestmentFund" {{ request('source_type') == 'InvestmentFund' ? 'selected' : '' }}>صناديق الاستثمار</option>
                        <option value="Wallet" {{ request('source_type') == 'Wallet' ? 'selected' : '' }}>المحافظ الشخصية</option>
                    </select>

                    <select name="type" class="bg-slate-50 border-0 rounded-xl px-3 py-2.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-400 outline-none">
                        <option value="">كل الأنواع</option>
                        <option value="income"  {{ request('type') == 'income'  ? 'selected' : '' }}>إيرادات</option>
                        <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>مصاريف</option>
                        <option value="capital" {{ request('type') == 'capital' ? 'selected' : '' }}>رأس مال</option>
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
                            <option value="{{ $pm->id }}" {{ request('payment_method_id') == $pm->id ? 'selected' : '' }}>
                                {{ $pm->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-sm hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20">
                        تطبيق الفلاتر
                    </button>
                    <a href="{{ route('transactions.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-500 rounded-xl font-black text-sm hover:bg-slate-200 transition-all">
                        تصفير
                    </a>
                </div>
            </form>
        </div>

        {{-- ===================== TRANSACTIONS LIST ===================== --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

            {{-- List Header --}}
            <div class="px-6 py-3 border-b border-slate-50 bg-slate-50/50 hidden sm:grid grid-cols-12 gap-4">
                <div class="col-span-1"></div>
                <div class="col-span-4 text-[9px] font-black text-slate-400 uppercase tracking-widest">البيان / التصنيف</div>
                <div class="col-span-2 text-[9px] font-black text-slate-400 uppercase tracking-widest">التاريخ</div>
                <div class="col-span-2 text-[9px] font-black text-slate-400 uppercase tracking-widest">المصدر</div>
                <div class="col-span-2 text-[9px] font-black text-slate-400 uppercase tracking-widest text-left">المبلغ</div>
                <div class="col-span-1"></div>
            </div>

            @forelse($transactions as $tx)
            <div class="px-4 sm:px-6 py-4 border-b border-slate-50 hover:bg-slate-50/50 transition-all group sm:grid sm:grid-cols-12 sm:gap-4 sm:items-center flex flex-wrap gap-3"
                 x-data="{ openMenu: false }">

                {{-- Icon --}}
                <div class="col-span-1 flex-shrink-0">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg
                        @if($tx->type === 'income') bg-emerald-100
                        @elseif($tx->type === 'capital') bg-indigo-100
                        @else bg-rose-100 @endif">
                        {{ $tx->categoryRelation?->icon ?? ($tx->type === 'income' ? '📈' : ($tx->type === 'capital' ? '💼' : '📉')) }}
                    </div>
                </div>

                {{-- Description & Category --}}
                <div class="col-span-4 flex-1 min-w-0">
                    <p class="font-black text-slate-900 text-sm truncate group-hover:text-indigo-600 transition-colors">
                        {{ $tx->description ?: ($tx->categoryRelation?->name ?? $tx->category) }}
                        @if($tx->invoice_path)
                            <a href="{{ asset('storage/' . $tx->invoice_path) }}" target="_blank"
                               class="inline-flex items-center justify-center w-5 h-5 bg-indigo-50 text-indigo-500 rounded-md text-[9px] mr-1 hover:bg-indigo-500 hover:text-white transition-all">📄</a>
                        @endif
                    </p>
                    <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                        <span class="text-[9px] font-black px-2 py-0.5 rounded-lg
                            @if($tx->type === 'income') bg-emerald-50 text-emerald-600
                            @elseif($tx->type === 'capital') bg-indigo-50 text-indigo-600
                            @else bg-rose-50 text-rose-600 @endif">
                            @if($tx->type === 'income') إيراد
                            @elseif($tx->type === 'capital') رأس مال
                            @else مصروف @endif
                        </span>
                        @if($tx->categoryRelation)
                            <span class="text-[9px] font-bold text-slate-400">{{ $tx->categoryRelation->name }}</span>
                        @endif
                    </div>
                </div>

                {{-- Date --}}
                <div class="col-span-2">
                    <p class="text-xs font-bold text-slate-500">{{ $tx->transaction_date->format('d M Y') }}</p>
                    <p class="text-[9px] font-bold text-slate-300">{{ $tx->transaction_date->diffForHumans() }}</p>
                </div>

                {{-- Source --}}
                <div class="col-span-2 flex flex-col gap-1">
                    @if($tx->transactionable)
                        <span class="text-[9px] font-black text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg truncate max-w-[120px]">
                            {{ $tx->transactionable->name ?? '—' }}
                        </span>
                    @endif
                    @if($tx->paymentMethod)
                        <span class="text-[9px] font-black text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded-lg truncate max-w-[120px]">
                            🏦 {{ $tx->paymentMethod->name }}
                        </span>
                    @endif
                </div>

                {{-- Amount --}}
                <div class="col-span-2 text-left">
                    <p class="font-black text-base tracking-tighter
                        @if($tx->type === 'income') text-emerald-600
                        @elseif($tx->type === 'capital') text-indigo-600
                        @else text-rose-600 @endif">
                        {{ $tx->type === 'income' ? '+' : ($tx->type === 'capital' ? '●' : '-') }}{{ number_format($tx->original_amount ?? $tx->amount, 2) }}
                        <span class="text-[9px] opacity-50 font-bold">{{ $tx->currency }}</span>
                    </p>
                    @if($tx->original_amount && $tx->original_amount != $tx->amount)
                        <p class="text-[9px] font-bold text-slate-400">≈ {{ number_format($tx->amount, 2) }} USD</p>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="col-span-1 flex justify-end relative">
                    <button @click="openMenu = !openMenu"
                        class="w-8 h-8 bg-slate-50 border border-slate-100 text-slate-400 rounded-xl flex items-center justify-center hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v.01M12 12v.01M12 19v.01"/>
                        </svg>
                    </button>

                    <div x-show="openMenu" @click.away="openMenu = false" x-cloak x-transition
                         class="absolute left-0 top-10 w-44 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 overflow-hidden">
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
                            class="w-full text-right px-5 py-3 text-xs font-black text-amber-600 hover:bg-amber-50 transition-colors flex items-center gap-2 border-b border-slate-50">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            تعديل
                        </button>
                        <form action="{{ route('transactions.destroy', $tx->id) }}" method="POST"
                              onsubmit="return confirm('حذف العملية؟')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-full text-right px-5 py-3 text-xs font-black text-rose-600 hover:bg-rose-50 transition-colors flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                حذف
                            </button>
                        </form>
                    </div>
                </div>

            </div>
            @empty
                <div class="py-24 text-center">
                    <div class="text-5xl mb-4 opacity-20">🏜️</div>
                    <p class="font-black text-slate-400 text-base">لا توجد عمليات</p>
                    <p class="text-slate-300 font-bold text-sm mt-1">ابدأ بتسجيل أول عملية مالية</p>
                    <button @click="showModal = true" class="mt-5 text-sm font-black text-indigo-600 hover:underline">
                        + تسجيل عملية جديدة
                    </button>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($transactions->hasPages())
            <div class="no-print">{{ $transactions->links() }}</div>
        @endif

    </div>

    {{-- ===================== ADD TRANSACTION MODAL ===================== --}}
    <div x-show="showModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-white rounded-3xl w-full max-w-xl shadow-2xl text-right overflow-y-auto max-h-[95vh]" @click.away="showModal = false">
            <div class="sticky top-0 bg-white border-b border-slate-100 px-8 py-5 flex items-center justify-between rounded-t-3xl">
                <h3 class="text-xl font-black text-gray-900">تسجيل عملية مالية</h3>
                <button @click="showModal = false" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-600 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-5">
                @csrf

                {{-- Type --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">نوع العملية</label>
                    <div class="grid grid-cols-3 gap-2 p-1.5 bg-gray-100 rounded-2xl">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="income" x-model="type" class="hidden peer">
                            <div class="py-3 text-center rounded-xl text-xs font-black transition-all peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm text-slate-500">📈 إيراد</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="expense" x-model="type" class="hidden peer">
                            <div class="py-3 text-center rounded-xl text-xs font-black transition-all peer-checked:bg-white peer-checked:text-rose-600 peer-checked:shadow-sm text-slate-500">📉 مصروف</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="capital" x-model="type" class="hidden peer">
                            <div class="py-3 text-center rounded-xl text-xs font-black transition-all peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm text-slate-500">💼 رأس مال</div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">المصدر</label>
                        <select name="source_type" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="InvestmentFund">صندوق استثمار</option>
                            <option value="Wallet">محفظة شخصية</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">تحديد المصدر</label>
                        <select name="source_id" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            @foreach($funds as $f) <option value="{{ $f->id }}">{{ $f->name }}</option> @endforeach
                            @foreach($wallets as $w) <option value="{{ $w->id }}">{{ $w->name }}</option> @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">المبلغ</label>
                        <input type="number" step="0.01" name="amount" required
                               class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-xl focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">التاريخ</label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                               class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>

                {{-- Category dynamic --}}
                <div x-data="{
                    allCats: {{ \App\Models\Category::where('is_default',true)->orWhere('user_id',auth()->id())->get()->map(fn($c)=>['id'=>$c->id,'name'=>$c->name,'icon'=>$c->icon,'type'=>$c->type])->toJson() }},
                    get filtered() { return this.allCats.filter(c => c.type === this.$root.type); }
                }">
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">التصنيف</label>
                    <select name="category_id" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <template x-for="cat in filtered" :key="cat.id">
                            <option :value="cat.id" x-text="cat.icon + ' ' + cat.name"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">وسيلة الدفع / الحساب</label>
                    <select name="payment_method_id" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">— بدون حساب محدد —</option>
                        @foreach($paymentMethods as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }} ({{ number_format($pm->balance,0) }} {{ $pm->currency }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">البيان / الوصف</label>
                    <input type="text" name="description"
                           class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                           placeholder="تفاصيل إضافية...">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">فاتورة / إيصال (اختياري)</label>
                    <input type="file" name="invoice" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <button type="submit" class="w-full py-4 rounded-2xl font-black text-base text-white shadow-lg transition-all"
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

    {{-- ===================== EDIT TRANSACTION MODAL ===================== --}}
    <div x-show="showEditModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-white rounded-3xl w-full max-w-xl shadow-2xl text-right overflow-y-auto max-h-[95vh]" @click.away="showEditModal = false">
            <div class="sticky top-0 bg-white border-b border-slate-100 px-8 py-5 flex items-center justify-between rounded-t-3xl">
                <h3 class="text-xl font-black text-gray-900">تعديل الحركة المالية</h3>
                <button @click="showEditModal = false" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-600 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="`/transactions/${editingTransaction.id}`" method="POST" class="p-8 space-y-5">
                @csrf @method('PUT')

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">نوع العملية</label>
                    <div class="grid grid-cols-3 gap-2 p-1.5 bg-gray-100 rounded-2xl">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="income" x-model="type" class="hidden peer">
                            <div class="py-3 text-center rounded-xl text-xs font-black transition-all peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm text-slate-500">📈 إيراد</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="expense" x-model="type" class="hidden peer">
                            <div class="py-3 text-center rounded-xl text-xs font-black transition-all peer-checked:bg-white peer-checked:text-rose-600 peer-checked:shadow-sm text-slate-500">📉 مصروف</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="capital" x-model="type" class="hidden peer">
                            <div class="py-3 text-center rounded-xl text-xs font-black transition-all peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm text-slate-500">💼 رأس مال</div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">المبلغ</label>
                        <input type="number" step="0.01" name="amount" x-model="editingTransaction.amount" required
                               class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-xl focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">التاريخ</label>
                        <input type="date" name="transaction_date" x-model="editingTransaction.transaction_date" required
                               class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">البيان / الوصف</label>
                    <input type="text" name="description" x-model="editingTransaction.description"
                           class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                           placeholder="بيان الحركة...">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">وسيلة الدفع</label>
                    <select name="payment_method_id" x-model="editingTransaction.payment_method_id"
                            class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">— بدون حساب محدد —</option>
                        @foreach($paymentMethods as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }} ({{ number_format($pm->balance,0) }} {{ $pm->currency }})</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-black text-base shadow-lg shadow-indigo-500/20 transition-all">
                    ✓ حفظ التعديلات
                </button>
            </form>
        </div>
    </div>

</div>
</x-app-layout>
