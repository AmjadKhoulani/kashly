<x-app-layout>
    <div class="py-10 bg-[#F8FAFC] min-h-screen font-sans" x-data="{ 
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
        <div class="max-w-[1600px] mx-auto px-6 space-y-12">
            
            <!-- Header Section: Premium Hero -->
            <div class="relative overflow-hidden bg-white rounded-[4rem] border-2 border-slate-100 shadow-2xl shadow-indigo-500/10 p-12 md:p-16">
                <div class="absolute -right-24 -top-24 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-50"></div>
                <div class="absolute -left-24 -bottom-24 w-80 h-80 bg-emerald-50 rounded-full blur-3xl opacity-30"></div>
                
                <div class="relative z-10 flex flex-col lg:flex-row justify-between items-center gap-12">
                    <div class="flex flex-col md:flex-row items-center gap-10">
                        <div class="w-28 h-28 bg-gradient-to-br from-indigo-600 to-indigo-400 text-white rounded-[3rem] flex items-center justify-center text-5xl shadow-2xl shadow-indigo-500/40 transform -rotate-3 border-4 border-white/20">
                            {{ $fund->icon ?? '🏘️' }}
                        </div>
                        <div class="text-center md:text-right">
                            <nav class="flex items-center justify-center md:justify-start gap-3 text-xs font-black text-slate-400 uppercase tracking-widest mb-4">
                                <a href="{{ route('funds.index') }}" class="hover:text-indigo-600 transition-colors">صناديق الاستثمار</a>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                                <span class="text-indigo-600">{{ $fund->name }}</span>
                            </nav>
                            <h2 class="text-6xl font-black text-slate-900 tracking-tight mb-4">{{ $fund->name }}</h2>
                            <div class="flex items-center justify-center md:justify-start gap-4">
                                <span class="px-5 py-2 bg-indigo-50 text-indigo-700 text-xs font-black rounded-full border-2 border-indigo-100 shadow-sm">صندوق نشط ✅</span>
                                <span class="px-5 py-2 bg-emerald-50 text-emerald-700 text-xs font-black rounded-full border-2 border-emerald-100 shadow-sm">تحديث فوري ⚡</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap justify-center gap-6">
                        <div class="flex items-center bg-slate-50 p-2.5 rounded-[2.5rem] border-2 border-slate-100 shadow-inner">
                            <button @click="showAccountModal = true" class="px-8 py-4 text-xs font-black text-slate-600 hover:text-indigo-600 transition-all uppercase tracking-widest">الحسابات</button>
                            <div class="w-px h-8 bg-slate-200"></div>
                            <button @click="showPartnerModal = true" class="px-8 py-4 text-xs font-black text-slate-600 hover:text-indigo-600 transition-all uppercase tracking-widest">الشركاء</button>
                            <div class="w-px h-8 bg-slate-200"></div>
                            <button @click="showAssetModal = true" class="px-8 py-4 text-xs font-black text-slate-600 hover:text-indigo-600 transition-all uppercase tracking-widest">الأصول</button>
                        </div>
                        <div class="flex items-center gap-5">
                            <button @click="showTransferModal = true" class="bg-amber-100 hover:bg-amber-200 text-amber-700 px-10 py-5 rounded-[2rem] font-black text-sm transition-all flex items-center gap-3 border-2 border-amber-200/50 shadow-lg shadow-amber-200/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                تحويل داخلي
                            </button>
                            <button @click="showModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-12 py-5 rounded-[2rem] font-black text-lg shadow-2xl shadow-indigo-500/30 transition-all hover:scale-105 active:scale-95">تسجيل عملية</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Overview: Sleek Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                <div class="premium-card p-12 bg-white border-2 border-slate-100 group">
                    <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-8 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500 border border-indigo-100 shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-xs text-slate-400 font-black uppercase tracking-widest mb-4">رأس المال المستثمر</p>
                    <div class="flex items-baseline gap-3">
                        <p class="text-5xl font-black text-slate-900 tracking-tighter group-hover:text-indigo-600 transition-colors">{{ number_format($fund->total_invested_capital, 0) }}</p>
                        <span class="text-sm font-black text-slate-400 uppercase tracking-widest">{{ $fund->currency }}</span>
                    </div>
                </div>

                <div class="premium-card p-12 bg-white border-2 border-slate-100 group">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 mb-8 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500 border border-blue-100 shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <p class="text-xs text-slate-400 font-black uppercase tracking-widest mb-4">القيمة السوقية الحالية</p>
                    <div class="flex items-baseline gap-3">
                        <p class="text-5xl font-black text-indigo-700 tracking-tighter group-hover:scale-105 transition-transform duration-500">{{ number_format($fund->current_value, 0) }}</p>
                        <span class="text-sm font-black text-slate-400 uppercase tracking-widest">{{ $fund->currency }}</span>
                    </div>
                </div>

                <div class="premium-card p-12 bg-white border-2 border-slate-100 group">
                    <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 mb-8 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500 border border-amber-100 shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <p class="text-xs text-slate-400 font-black uppercase tracking-widest mb-4">إجمالي قيمة الأصول</p>
                    <div class="flex items-baseline gap-3">
                        <p class="text-5xl font-black text-slate-900 tracking-tighter group-hover:text-amber-600 transition-colors">{{ number_format($fund->assets->sum('value'), 0) }}</p>
                        <span class="text-sm font-black text-slate-400 uppercase tracking-widest">{{ $fund->currency }}</span>
                    </div>
                </div>

                @php
                    $income = \App\Models\Transaction::where('transactionable_id', $fund->id)->where('transactionable_type', \App\Models\InvestmentFund::class)->where('type', 'income')->sum('amount');
                    $expense = \App\Models\Transaction::where('transactionable_id', $fund->id)->where('transactionable_type', \App\Models\InvestmentFund::class)->where('type', 'expense')->sum('amount');
                    $profit = $income - $expense;
                @endphp
                <div class="premium-card p-12 bg-white border-2 border-slate-100 group">
                    <div class="w-16 h-16 {{ $profit >= 0 ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }} rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500 border shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <p class="text-xs text-slate-400 font-black uppercase tracking-widest mb-4">صافي الأرباح / الخسائر</p>
                    <div class="flex items-baseline gap-3">
                        <p class="text-5xl font-black {{ $profit >= 0 ? 'text-emerald-600' : 'text-rose-600' }} tracking-tighter">{{ $profit >= 0 ? '+' : '' }}{{ number_format($profit, 0) }}</p>
                        <span class="text-sm font-black text-slate-400 uppercase tracking-widest">{{ $fund->currency }}</span>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                
                <!-- Left Column: Accounts & Recent Activity -->
                <div class="lg:col-span-4 space-y-10">
                    
                <!-- Left Column: Accounts & Recent Activity -->
                <div class="lg:col-span-4 space-y-10">
                    
                    <!-- Accounts Card -->
                    <div class="premium-card bg-white border-2 border-slate-100 p-10">
                        <div class="flex justify-between items-center mb-10">
                            <h3 class="text-3xl font-black text-slate-900 tracking-tight">حسابات الصندوق</h3>
                            <button @click="showAccountModal = true" class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all border border-indigo-100 shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        </div>
                        
                        <div class="space-y-8">
                            @forelse($paymentMethods as $pm)
                                <div class="bg-slate-50/50 rounded-[3rem] border-2 border-slate-100 p-3">
                                    <!-- Parent Account Header -->
                                    <div class="flex items-center justify-between p-6">
                                        <div class="flex items-center gap-5">
                                            <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-3xl shadow-sm border border-slate-100">
                                                {{ $pm->type == 'bank' ? '🏦' : '💵' }}
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-black text-slate-900">{{ $pm->name }}</h4>
                                                <p class="text-xs font-black text-slate-400 uppercase tracking-widest mt-1">{{ $pm->children->count() }} حسابات عملة</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sub Accounts (Currencies) -->
                                    <div class="grid grid-cols-1 gap-3 p-3">
                                        @foreach($pm->children as $child)
                                            <div class="group relative overflow-hidden bg-gradient-to-br from-slate-900 to-indigo-950 p-8 rounded-[2.5rem] text-white shadow-xl hover:-translate-y-2 transition-all duration-500 border-2 border-white/5">
                                                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/5 rounded-full blur-3xl"></div>
                                                <div class="relative z-10 flex justify-between items-center">
                                                    <div>
                                                        <p class="text-xs font-black text-white/40 uppercase tracking-widest mb-2">{{ $child->currency }}</p>
                                                        <div class="flex items-baseline gap-3">
                                                            <p class="text-3xl font-black tracking-tighter">{{ number_format($child->balance, 0) }}</p>
                                                            <span class="text-xs font-black text-white/60 uppercase tracking-widest">{{ $child->currency }}</span>
                                                        </div>
                                                    </div>
                                                    <button @click="reconcilingId = {{ $child->id }}; reconcilingName = '{{ $pm->name }} ({{ $child->currency }})'; reconcilingBalance = {{ $child->balance }}; showAccountModal = false;" class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center text-white/40 hover:bg-white hover:text-indigo-900 transition-all border border-white/10">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-16 border-4 border-dashed border-slate-100 rounded-[3rem]">
                                    <p class="text-sm font-black text-slate-300 uppercase tracking-widest italic">لا توجد حسابات مضافة 🏝️</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="premium-card bg-white border-2 border-slate-100 p-10">
                        <div class="flex justify-between items-center mb-10">
                            <h3 class="text-3xl font-black text-slate-900 tracking-tight">آخر التحركات</h3>
                            <a href="{{ route('funds.transactions', $fund->id) }}" class="text-xs font-black text-indigo-600 hover:bg-indigo-50 px-4 py-2 rounded-xl transition-all uppercase tracking-widest">عرض الكل</a>
                        </div>
                        <div class="space-y-8">
                            @foreach($transactions as $transaction)
                                <div class="flex items-center justify-between p-6 rounded-[2.5rem] hover:bg-slate-50 transition-all border-2 border-transparent hover:border-slate-100 group">
                                    <div class="flex items-center gap-6">
                                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl shadow-inner border-2 border-white transition-transform group-hover:rotate-6" 
                                             style="background-color: {{ $transaction->category->color ?? ($transaction->type == 'income' ? '#10B981' : '#EF4444') }}20; 
                                                    color: {{ $transaction->category->color ?? ($transaction->type == 'income' ? '#10B981' : '#EF4444') }};">
                                            {{ $transaction->category->icon ?? ($transaction->type == 'income' ? '↓' : '↑') }}
                                        </div>
                                        <div>
                                            <p class="text-base font-black text-slate-900 mb-1">{{ $transaction->description ?: $transaction->category->name }}</p>
                                            <p class="text-xs font-black text-slate-400 uppercase tracking-widest">{{ $transaction->transaction_date->format('Y/m/d') }} • {{ $transaction->category->name }}</p>
                                        </div>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-2xl font-black {{ $transaction->type == 'income' ? 'text-emerald-600' : 'text-rose-600' }} tracking-tighter">
                                            {{ $transaction->type == 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 0) }}
                                        </p>
                                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mt-1">{{ $transaction->paymentMethod->currency ?? $fund->currency }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                </div>

                <!-- Right Column: Assets & Equity -->
                <div class="lg:col-span-8 space-y-10">
                    
                    <!-- Assets & Properties -->
                    <div class="premium-card bg-white border-2 border-slate-100 overflow-hidden">
                        <div class="p-12 border-b-2 border-slate-50 flex justify-between items-center bg-slate-50/30">
                            <div>
                                <h3 class="text-3xl font-black text-slate-900 tracking-tight">الأصول والممتلكات</h3>
                                <p class="text-sm font-black text-slate-400 mt-2 uppercase tracking-widest">إجمالي الأصول غير النقدية المضافة للصندوق</p>
                            </div>
                            <button @click="showAssetModal = true" class="px-8 py-4 bg-indigo-600 text-white rounded-2xl text-xs font-black hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20 uppercase tracking-widest">إضافة أصل جديد</button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 p-12">
                            @forelse($fund->assets as $asset)
                                <div class="p-10 bg-slate-50/50 rounded-[3rem] border-2 border-slate-100 hover:border-indigo-200 hover:bg-white transition-all group shadow-sm hover:shadow-xl">
                                    <div class="flex justify-between items-start mb-8">
                                        <div class="w-16 h-16 bg-white rounded-3xl flex items-center justify-center text-3xl shadow-md border border-slate-50 group-hover:rotate-6 transition-transform">
                                            @if($asset->type == 'car') 🚗 @elseif($asset->type == 'furniture') 🪑 @elseif($asset->type == 'inventory') 📦 @else 🏢 @endif
                                        </div>
                                        <span class="text-xs font-black bg-indigo-600 text-white px-5 py-2 rounded-xl uppercase tracking-widest shadow-lg shadow-indigo-500/20">{{ number_format($asset->value, 0) }} {{ $fund->currency }}</span>
                                    </div>
                                    <h4 class="text-2xl font-black text-slate-900 mb-3">{{ $asset->name }}</h4>
                                    <p class="text-xs font-black text-slate-400 tracking-widest mb-8 uppercase">تاريخ الشراء: {{ $asset->purchase_date->format('Y-m-d') }}</p>
                                    <div class="flex items-center gap-4">
                                        <div class="h-2 flex-1 bg-slate-200 rounded-full overflow-hidden shadow-inner">
                                            <div class="h-full bg-indigo-600 rounded-full" style="width: 100%"></div>
                                        </div>
                                        <span class="text-xs font-black text-indigo-600 uppercase">قيمة كاملة</span>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-24 bg-slate-50/30 rounded-[4rem] border-4 border-dashed border-slate-100">
                                    <p class="text-slate-300 font-black tracking-widest uppercase italic text-lg">لا توجد أصول مضافة حالياً 🏗️</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Equities & Partners -->
                    <div class="premium-card bg-white border-2 border-slate-100 overflow-hidden">
                        <div class="p-12 border-b-2 border-slate-50 flex justify-between items-center bg-slate-50/30">
                            <div>
                                <h3 class="text-3xl font-black text-slate-900 tracking-tight">توزيع الحصص والشركاء</h3>
                                <p class="text-sm font-black text-slate-400 mt-2 uppercase tracking-widest">تقسيم ملكية الصندوق والأرباح المستحقة</p>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-right border-collapse">
                                <thead class="bg-slate-50/80 border-b-2 border-slate-100">
                                    <tr>
                                        <th class="px-12 py-10 text-xs font-black text-slate-400 uppercase tracking-widest">الشريك</th>
                                        <th class="px-8 py-10 text-xs font-black text-slate-400 uppercase tracking-widest text-center">نوع الحصة</th>
                                        <th class="px-8 py-10 text-xs font-black text-slate-400 uppercase tracking-widest text-center">المساهمة</th>
                                        <th class="px-8 py-10 text-xs font-black text-slate-400 uppercase tracking-widest text-center">النسبة</th>
                                        <th class="px-8 py-10 text-xs font-black text-slate-400 uppercase tracking-widest text-center">القيمة الحالية</th>
                                        <th class="px-12 py-10 text-xs font-black text-slate-400 uppercase tracking-widest text-center">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y-2 divide-slate-50">
                                    @foreach($fund->equities as $equity)
                                        <tr class="hover:bg-indigo-50/30 transition-all group">
                                            <td class="px-12 py-12">
                                                <div class="flex items-center gap-6">
                                                    <div class="w-16 h-16 bg-white text-indigo-600 rounded-[2rem] flex items-center justify-center font-black text-2xl shadow-md border-2 border-slate-100 group-hover:scale-110 transition-transform">
                                                        {{ mb_substr($equity->partner->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <p class="font-black text-slate-900 text-xl mb-1">{{ $equity->partner->name }}</p>
                                                        @if($equity->partner->linked_user_id == auth()->id())
                                                            <span class="px-4 py-1.5 bg-emerald-600 text-white text-[10px] font-black rounded-lg shadow-sm uppercase tracking-widest">أنت (المدير)</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-12 text-center">
                                                <span class="px-5 py-2.5 {{ $equity->equity_type == 'fixed' ? 'bg-amber-100 text-amber-700 border-2 border-amber-200' : 'bg-slate-100 text-slate-600 border-2 border-slate-200' }} rounded-xl text-xs font-black uppercase tracking-widest">
                                                    {{ $equity->equity_type == 'fixed' ? 'نسبة ثابتة' : 'رأس مال' }}
                                                </span>
                                            </td>
                                            <td class="px-8 py-12 text-center">
                                                <p class="text-xl font-black text-slate-900">{{ number_format($equity->amount, 0) }}</p>
                                                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">{{ $fund->currency }}</p>
                                            </td>
                                            <td class="px-8 py-12 text-center">
                                                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full border-4 border-indigo-100 bg-white shadow-xl group-hover:scale-110 transition-transform">
                                                    <span class="text-base font-black text-indigo-600">{{ number_format($equity->percentage, 1) }}%</span>
                                                </div>
                                            </td>
                                            <td class="px-8 py-12 text-center">
                                                <p class="text-2xl font-black text-emerald-600 tracking-tighter">{{ number_format(($equity->percentage / 100) * $fund->current_value, 0) }}</p>
                                                <p class="text-xs font-black text-emerald-400 uppercase tracking-widest">{{ $fund->currency }}</p>
                                            </td>
                                            <td class="px-12 py-12 text-center">
                                                <div class="flex items-center justify-center gap-4">
                                                    <button @click="editingEquity = {{ $equity->id }}" class="p-4 bg-white border border-slate-100 text-slate-400 hover:text-indigo-600 hover:shadow-lg rounded-2xl transition-all">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    </button>
                                                    <form action="{{ route('funds.removePartner', $equity->id) }}" method="POST" onsubmit="return confirm('استبعاد الشريك من الصندوق؟')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="p-4 bg-white border border-slate-100 text-slate-400 hover:text-rose-600 hover:shadow-lg rounded-2xl transition-all">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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
