<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/20 font-sans" x-data="{ 
        editingEquity: null, 
        showModal: false, 
        showAssetModal: false, 
        showPartnerModal: false, 
        showAccountModal: false,
        showTransferModal: false,
        reconcilingId: null, 
        reconcilingName: '', 
        reconcilingBalance: 0,
        partnerSource: 'existing',
        type: 'expense'
    }">

        {{-- Sticky Header --}}
        <div class="bg-white/95 border-b border-slate-100 sticky top-0 z-40 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                <div class="flex items-center justify-between h-16 gap-4">

                    {{-- Icon + Name + Breadcrumb --}}
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 bg-indigo-600 text-white rounded-xl flex items-center justify-center text-lg shadow-md shadow-indigo-500/20 flex-shrink-0">
                            {{ $fund->icon ?? '🏘️' }}
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                <a href="{{ route('funds.index') }}" class="hover:text-indigo-600 transition-colors">الكيانات</a>
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                                <span class="text-indigo-600 truncate">{{ $fund->name }}</span>
                            </div>
                            <h1 class="text-base font-black text-slate-900 tracking-tight truncate leading-tight">{{ $fund->name }}</h1>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        {{-- Small icon buttons --}}
                        <button @click="showAccountModal = true"
                            class="flex items-center gap-1.5 px-3 py-2 text-xs font-black text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            الحسابات
                        </button>
                        <button @click="showPartnerModal = true"
                            class="flex items-center gap-1.5 px-3 py-2 text-xs font-black text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                            الشركاء
                        </button>
                        <button @click="showAssetModal = true"
                            class="flex items-center gap-1.5 px-3 py-2 text-xs font-black text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            الأصول
                        </button>

                        <div class="w-px h-6 bg-slate-200 mx-1"></div>

                        {{-- Transfer button --}}
                        <button @click="showTransferModal = true"
                            class="flex items-center gap-1.5 px-4 py-2.5 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-xl font-black text-xs transition-all border border-amber-200/50 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            تحويل داخلي
                        </button>

                        {{-- Register operation button --}}
                        <button @click="showModal = true"
                            class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-sm shadow-lg shadow-indigo-500/20 hover:scale-105 active:scale-95 transition-all">
                            تسجيل عملية
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-6">

            <!-- Stats Overview: Sleek Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-gradient-to-br from-indigo-50/40 via-white to-slate-50/20 rounded-2xl p-5 border border-indigo-100/60 shadow-sm hover:shadow-md transition-all group">
                    <div class="w-11 h-11 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 mb-4 group-hover:scale-105 group-hover:rotate-3 transition-transform border border-indigo-100 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1.5">رأس المال المستثمر</p>
                    <div class="flex items-baseline gap-1.5">
                        <p class="text-2xl font-black text-slate-900 tracking-tighter group-hover:text-indigo-600 transition-colors">{{ number_format($fund->total_invested_capital, 0) }}</p>
                        <span class="text-[10px] font-black text-slate-400 uppercase">{{ $fund->currency }}</span>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-50/40 via-white to-slate-50/20 rounded-2xl p-5 border border-blue-100/60 shadow-sm hover:shadow-md transition-all group">
                    <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 mb-4 group-hover:scale-105 group-hover:rotate-3 transition-transform border border-blue-100 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1.5">القيمة السوقية الحالية</p>
                    <div class="flex items-baseline gap-1.5">
                        <p class="text-2xl font-black text-indigo-700 tracking-tighter">{{ number_format($fund->current_value, 0) }}</p>
                        <span class="text-[10px] font-black text-slate-400 uppercase">{{ $fund->currency }}</span>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-amber-50/40 via-white to-slate-50/20 rounded-2xl p-5 border border-amber-100/60 shadow-sm hover:shadow-md transition-all group">
                    <div class="w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 mb-4 group-hover:scale-105 group-hover:rotate-3 transition-transform border border-amber-100 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1.5">إجمالي قيمة الأصول</p>
                    <div class="flex items-baseline gap-1.5">
                        <p class="text-2xl font-black text-slate-900 tracking-tighter group-hover:text-amber-600 transition-colors">{{ number_format($fund->assets->sum('value'), 0) }}</p>
                        <span class="text-[10px] font-black text-slate-400 uppercase">{{ $fund->currency }}</span>
                    </div>
                </div>

                @php
                    $income = \App\Models\Transaction::where('transactionable_id', $fund->id)->where('transactionable_type', \App\Models\InvestmentFund::class)->where('type', 'income')->sum('amount');
                    $expense = \App\Models\Transaction::where('transactionable_id', $fund->id)->where('transactionable_type', \App\Models\InvestmentFund::class)->where('type', 'expense')->sum('amount');
                    $capitalSum = \App\Models\Transaction::where('transactionable_id', $fund->id)->where('transactionable_type', \App\Models\InvestmentFund::class)->where('type', 'capital')->sum('amount');
                    $profit = $income - $expense;
                @endphp

                <div class="bg-gradient-to-br from-violet-50/40 via-white to-slate-50/20 rounded-2xl p-5 border border-violet-100/60 shadow-sm hover:shadow-md transition-all group">
                    <div class="w-11 h-11 bg-violet-50 rounded-xl flex items-center justify-center text-violet-600 mb-4 group-hover:scale-105 group-hover:rotate-3 transition-transform border border-violet-100 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1.5">حركات رأس المال</p>
                    <div class="flex items-baseline gap-1.5">
                        <p class="text-2xl font-black text-violet-700 tracking-tighter">{{ number_format($capitalSum, 0) }}</p>
                        <span class="text-[10px] font-black text-slate-400 uppercase">{{ $fund->currency }}</span>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-slate-50 via-white to-slate-50/20 rounded-2xl p-5 border border-slate-100 shadow-sm hover:shadow-md transition-all group
                    {{ $profit >= 0 ? 'from-emerald-50/30 border-emerald-100/60' : 'from-rose-50/30 border-rose-100/60' }}">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 group-hover:rotate-3 transition-transform border shadow-sm
                        {{ $profit >= 0 ? 'bg-emerald-50 text-emerald-600 border-emerald-100 shadow-emerald-500/5' : 'bg-rose-50 text-rose-600 border-rose-100 shadow-rose-500/5' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1.5">صافي الأرباح / الخسائر</p>
                    <div class="flex items-baseline gap-1.5">
                        <p class="text-2xl font-black tracking-tighter {{ $profit >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $profit >= 0 ? '+' : '' }}{{ number_format($profit, 0) }}</p>
                        <span class="text-[10px] font-black text-slate-400 uppercase">{{ $fund->currency }}</span>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- Left Column: Accounts & Recent Activity -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- Accounts Card -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-base font-black text-slate-900">حسابات الصندوق</h3>
                            <button @click="showAccountModal = true" class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all border border-indigo-100 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            @forelse($paymentMethods as $pm)
                                <div class="bg-slate-50/50 rounded-2xl border border-slate-100 p-2">
                                    <!-- Parent Account Header -->
                                    <div class="flex items-center justify-between p-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-xl shadow-sm border border-slate-100/80">
                                                {{ $pm->type == 'bank' ? '🏦' : '💵' }}
                                            </div>
                                            <div>
                                                <h4 class="text-xs font-black text-slate-900">{{ $pm->name }}</h4>
                                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ $pm->children->count() }} حسابات عملة</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sub Accounts (Currencies) -->
                                    <div class="grid grid-cols-1 gap-2 p-1">
                                        @foreach($pm->children as $child)
                                            <div class="group relative overflow-hidden bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-950 p-4 rounded-xl text-white shadow-md hover:-translate-y-0.5 transition-all duration-300 border border-white/5">
                                                <div class="absolute -right-8 -bottom-8 w-24 h-24 bg-white/5 rounded-full blur-2xl"></div>
                                                <div class="relative z-10 flex justify-between items-center">
                                                    <div>
                                                        <p class="text-[9px] font-black text-white/40 uppercase tracking-widest mb-1">{{ $child->currency }}</p>
                                                        <div class="flex items-baseline gap-1.5">
                                                            <p class="text-xl font-black tracking-tighter">{{ number_format($child->balance, 0) }}</p>
                                                            <span class="text-[9px] font-black text-white/60 uppercase tracking-widest">{{ $child->currency }}</span>
                                                        </div>
                                                    </div>
                                                    <button @click="reconcilingId = {{ $child->id }}; reconcilingName = '{{ $pm->name }} ({{ $child->currency }})'; reconcilingBalance = {{ $child->balance }}; showAccountModal = false;" class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center text-white/50 hover:bg-white hover:text-indigo-950 transition-all border border-white/10">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10 border border-dashed border-slate-200 rounded-2xl bg-slate-50/30">
                                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest italic">لا توجد حسابات مضافة 🏝️</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-base font-black text-slate-900">آخر التحركات</h3>
                            <a href="{{ route('funds.transactions', $fund->id) }}" class="text-[10px] font-black text-indigo-600 hover:bg-indigo-50 px-3 py-1.5 rounded-lg transition-colors uppercase tracking-widest border border-indigo-100">عرض الكل</a>
                        </div>
                        <div class="space-y-4">
                            @foreach($transactions as $transaction)
                                <div class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50/80 transition-all border border-transparent hover:border-slate-100/80 group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shadow-sm border border-white transition-transform group-hover:rotate-3" 
                                             style="background-color: {{ $transaction->categoryRelation ? $transaction->categoryRelation->color : ($transaction->type == 'income' ? '#10B981' : '#EF4444') }}15; 
                                                     color: {{ $transaction->categoryRelation ? $transaction->categoryRelation->color : ($transaction->type == 'income' ? '#10B981' : '#EF4444') }};">
                                            {{ $transaction->categoryRelation ? $transaction->categoryRelation->icon : ($transaction->type == 'income' ? '↓' : '↑') }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-slate-900 mb-0.5 leading-snug">{{ $transaction->description ?: ($transaction->categoryRelation ? $transaction->categoryRelation->name : $transaction->category) }}</p>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none">{{ $transaction->transaction_date->format('Y/m/d') }} · {{ $transaction->categoryRelation ? $transaction->categoryRelation->name : $transaction->category }}</p>
                                        </div>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-base font-black 
                                            {{ $transaction->type == 'income' ? 'text-emerald-600' : ($transaction->type == 'capital' ? 'text-violet-600' : 'text-rose-600') }} 
                                            tracking-tighter leading-none">
                                            {{ $transaction->type == 'income' ? '+' : ($transaction->type == 'capital' ? '●' : '-') }}{{ number_format($transaction->amount, 0) }}
                                        </p>
                                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mt-1">
                                            {{ $transaction->paymentMethod->currency ?? $fund->currency }}
                                            @if($transaction->type == 'capital')
                                                <span class="text-violet-400">· رأس مال</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right Column: Assets & Equity -->
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- Assets & Properties -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/20">
                            <div>
                                <h3 class="text-base font-black text-slate-900">الأصول والممتلكات</h3>
                                <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">إجمالي الأصول غير النقدية المضافة للصندوق</p>
                            </div>
                            <button @click="showAssetModal = true" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-black hover:bg-indigo-700 transition-all shadow-md shadow-indigo-500/20 uppercase tracking-widest">إضافة أصل جديد</button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-6">
                            @forelse($fund->assets as $asset)
                                <div class="p-5 bg-slate-50/30 rounded-xl border border-slate-100 hover:border-indigo-100 hover:bg-white transition-all group shadow-sm hover:shadow-md">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-2xl shadow-sm border border-slate-100 group-hover:rotate-3 transition-transform">
                                            @if($asset->type == 'car') 🚗 @elseif($asset->type == 'furniture') 🪑 @elseif($asset->type == 'inventory') 📦 @else 🏢 @endif
                                        </div>
                                        <span class="text-[10px] font-black bg-indigo-600 text-white px-3 py-1.5 rounded-lg uppercase tracking-widest shadow-md shadow-indigo-500/10">{{ number_format($asset->value, 0) }} {{ $fund->currency }}</span>
                                    </div>
                                    <h4 class="text-base font-black text-slate-900 mb-1">{{ $asset->name }}</h4>
                                    <p class="text-[9px] font-bold text-slate-400 tracking-widest mb-4 uppercase">تاريخ الشراء: {{ $asset->purchase_date->format('Y-m-d') }}</p>
                                    <div class="flex items-center gap-3">
                                        <div class="h-1.5 flex-1 bg-slate-200 rounded-full overflow-hidden shadow-inner">
                                            <div class="h-full bg-indigo-600 rounded-full" style="width: 100%"></div>
                                        </div>
                                        <span class="text-[9px] font-black text-indigo-600 uppercase">قيمة كاملة</span>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-16 bg-slate-50/10 rounded-2xl border border-dashed border-slate-200">
                                    <p class="text-slate-300 font-black tracking-widest uppercase italic text-sm">لا توجد أصول مضافة حالياً 🏗️</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Equities & Partners -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/20">
                            <div>
                                <h3 class="text-base font-black text-slate-900">توزيع الحصص والشركاء</h3>
                                <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">تقسيم ملكية الصندوق والأرباح المستحقة</p>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-right border-collapse">
                                <thead class="bg-slate-50/50 border-b border-slate-100">
                                    <tr>
                                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">الشريك</th>
                                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">نوع الحصة</th>
                                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">المساهمة</th>
                                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">النسبة</th>
                                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">القيمة الحالية</th>
                                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @foreach($fund->equities as $equity)
                                        <tr class="hover:bg-indigo-50/10 transition-all group">
                                            <td class="px-6 py-5">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 bg-white text-indigo-600 rounded-xl flex items-center justify-center font-black text-base shadow-sm border border-slate-100 group-hover:scale-105 transition-transform">
                                                        {{ mb_substr($equity->partner->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <p class="font-black text-slate-900 text-sm mb-0.5">{{ $equity->partner->name }}</p>
                                                        @if($equity->partner->linked_user_id == auth()->id())
                                                            <span class="px-2 py-0.5 bg-emerald-600 text-white text-[8px] font-black rounded shadow-sm uppercase tracking-widest">أنت (المدير)</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-5 text-center">
                                                <span class="px-3 py-1.5 {{ $equity->equity_type == 'fixed' ? 'bg-amber-50 text-amber-700 border border-amber-100/50' : 'bg-slate-100 text-slate-600 border border-slate-200/50' }} rounded-lg text-[9px] font-black uppercase tracking-widest">
                                                    {{ $equity->equity_type == 'fixed' ? 'نسبة ثابتة' : 'رأس مال' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-5 text-center">
                                                <p class="text-sm font-black text-slate-900">{{ number_format($equity->amount, 0) }}</p>
                                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $fund->currency }}</p>
                                            </td>
                                            <td class="px-4 py-5 text-center">
                                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full border-2 border-indigo-100 bg-white shadow-sm group-hover:scale-105 transition-transform">
                                                    <span class="text-xs font-black text-indigo-600">{{ number_format($equity->percentage, 1) }}%</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-5 text-center">
                                                <p class="text-sm font-black text-emerald-600 tracking-tighter">{{ number_format(($equity->percentage / 100) * $fund->current_value, 0) }}</p>
                                                <p class="text-[9px] font-black text-emerald-400 uppercase tracking-widest">{{ $fund->currency }}</p>
                                            </td>
                                            <td class="px-6 py-5 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button @click="editingEquity = {{ $equity->id }}" class="p-2 bg-white border border-slate-100 text-slate-400 hover:text-indigo-600 hover:shadow-sm rounded-lg transition-all">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    </button>
                                                    <form action="{{ route('funds.removePartner', $equity->id) }}" method="POST" onsubmit="return confirm('استبعاد الشريك من الصندوق؟')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="p-2 bg-white border border-slate-100 text-slate-400 hover:text-rose-600 hover:shadow-sm rounded-lg transition-all">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals: Same logic, but updated design -->
        @include('funds.partials.modals')

    </div>
</x-app-layout>
