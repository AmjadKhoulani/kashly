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
        <div class="max-w-[1600px] mx-auto px-6 space-y-10">
            
            <!-- Header Section: Premium Hero -->
            <div class="relative overflow-hidden bg-white rounded-[3.5rem] border border-gray-100 shadow-xl shadow-indigo-500/5 p-10 md:p-14">
                <div class="absolute -right-24 -top-24 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-50"></div>
                <div class="absolute -left-24 -bottom-24 w-80 h-80 bg-emerald-50 rounded-full blur-3xl opacity-30"></div>
                
                <div class="relative z-10 flex flex-col lg:flex-row justify-between items-center gap-10">
                    <div class="flex flex-col md:flex-row items-center gap-8">
                        <div class="w-24 h-24 bg-gradient-to-br from-indigo-600 to-indigo-400 text-white rounded-[2.5rem] flex items-center justify-center text-4xl shadow-2xl shadow-indigo-500/40 transform -rotate-3">
                            {{ $fund->icon ?? '🏘️' }}
                        </div>
                        <div class="text-center md:text-right">
                            <nav class="flex items-center justify-center md:justify-start gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">
                                <a href="{{ route('funds.index') }}" class="hover:text-indigo-600 transition-colors">صناديق الاستثمار</a>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                                <span class="text-indigo-600">{{ $fund->name }}</span>
                            </nav>
                            <h2 class="text-5xl font-black text-gray-900 tracking-tight mb-3">{{ $fund->name }}</h2>
                            <div class="flex items-center justify-center md:justify-start gap-3">
                                <span class="px-4 py-1.5 bg-indigo-50 text-indigo-700 text-[10px] font-black rounded-full border border-indigo-100">صندوق نشط</span>
                                <span class="px-4 py-1.5 bg-emerald-50 text-emerald-700 text-[10px] font-black rounded-full border border-emerald-100">تحديث فوري</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap justify-center gap-4">
                        <div class="flex items-center bg-gray-50 p-2 rounded-[2rem] border border-gray-100">
                            <button @click="showAccountModal = true" class="px-6 py-3 text-[11px] font-black text-gray-600 hover:text-indigo-600 transition-all">الحسابات</button>
                            <div class="w-px h-6 bg-gray-200"></div>
                            <button @click="showPartnerModal = true" class="px-6 py-3 text-[11px] font-black text-gray-600 hover:text-indigo-600 transition-all">الشركاء</button>
                            <div class="w-px h-6 bg-gray-200"></div>
                            <button @click="showAssetModal = true" class="px-6 py-3 text-[11px] font-black text-gray-600 hover:text-indigo-600 transition-all">الأصول</button>
                        </div>
                        <div class="flex items-center gap-4">
                            <button @click="showTransferModal = true" class="bg-amber-100 hover:bg-amber-200 text-amber-700 px-8 py-5 rounded-3xl font-black text-xs transition-all flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                تحويل داخلي
                            </button>
                            <button @click="showModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-5 rounded-3xl font-black text-sm shadow-xl shadow-indigo-500/30 transition-all hover:scale-105">تسجيل عملية</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Overview: Sleek Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-white p-10 rounded-[3rem] border border-gray-100 shadow-sm group hover:border-indigo-200 transition-all">
                    <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-3">رأس المال المستثمر</p>
                    <div class="flex items-baseline gap-2">
                        <p class="text-3xl font-black text-gray-900 tracking-tighter">{{ number_format($fund->total_invested_capital, 0) }}</p>
                        <span class="text-xs font-bold text-gray-400">{{ $fund->currency }}</span>
                    </div>
                </div>

                <div class="bg-white p-10 rounded-[3rem] border border-gray-100 shadow-sm group hover:border-indigo-200 transition-all">
                    <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-3">القيمة السوقية الحالية</p>
                    <div class="flex items-baseline gap-2">
                        <p class="text-3xl font-black text-indigo-700 tracking-tighter">{{ number_format($fund->current_value, 0) }}</p>
                        <span class="text-xs font-bold text-gray-400">{{ $fund->currency }}</span>
                    </div>
                </div>

                <div class="bg-white p-10 rounded-[3rem] border border-gray-100 shadow-sm group hover:border-indigo-200 transition-all">
                    <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-3">إجمالي قيمة الأصول</p>
                    <div class="flex items-baseline gap-2">
                        <p class="text-3xl font-black text-gray-900 tracking-tighter">{{ number_format($fund->assets->sum('value'), 0) }}</p>
                        <span class="text-xs font-bold text-gray-400">{{ $fund->currency }}</span>
                    </div>
                </div>

                @php
                    $income = \App\Models\Transaction::where('transactionable_id', $fund->id)->where('transactionable_type', \App\Models\InvestmentFund::class)->where('type', 'income')->sum('amount');
                    $expense = \App\Models\Transaction::where('transactionable_id', $fund->id)->where('transactionable_type', \App\Models\InvestmentFund::class)->where('type', 'expense')->sum('amount');
                    $profit = $income - $expense;
                @endphp
                <div class="bg-white p-10 rounded-[3rem] border border-gray-100 shadow-sm group hover:border-indigo-200 transition-all">
                    <div class="w-12 h-12 {{ $profit >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-3">صافي الأرباح / الخسائر</p>
                    <div class="flex items-baseline gap-2">
                        <p class="text-3xl font-black {{ $profit >= 0 ? 'text-emerald-600' : 'text-rose-600' }} tracking-tighter">{{ $profit >= 0 ? '+' : '' }}{{ number_format($profit, 0) }}</p>
                        <span class="text-xs font-bold text-gray-400">{{ $fund->currency }}</span>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                
                <!-- Left Column: Accounts & Recent Activity -->
                <div class="lg:col-span-4 space-y-10">
                    
                    <!-- Accounts Card -->
                    <div class="bg-white rounded-[3.5rem] border border-gray-100 shadow-sm p-10">
                        <div class="flex justify-between items-center mb-10">
                            <h3 class="text-2xl font-black text-gray-900 tracking-tight">حسابات الصندوق</h3>
                            <button @click="showAccountModal = true" class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        </div>
                        
                        <div class="space-y-6">
                            @forelse($paymentMethods as $pm)
                                <div class="group relative overflow-hidden bg-gradient-to-br from-gray-900 to-indigo-950 p-8 rounded-[2.5rem] text-white shadow-xl hover:-translate-y-2 transition-all duration-500">
                                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>
                                    <div class="relative z-10 flex flex-col justify-between h-full">
                                        <div class="flex justify-between items-start mb-10">
                                            <div>
                                                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-1">{{ $pm->type == 'bank' ? 'حساب بنكي' : 'نقد / كاش' }}</p>
                                                <h4 class="text-lg font-black">{{ $pm->name }}</h4>
                                            </div>
                                            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-md">
                                                <span class="text-lg">{{ $pm->type == 'bank' ? '🏦' : '💵' }}</span>
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-end">
                                            <div class="flex items-baseline gap-2">
                                                <p class="text-3xl font-black tracking-tighter">{{ number_format($pm->balance, 0) }}</p>
                                                <span class="text-xs font-bold text-white/60">{{ $pm->currency }}</span>
                                            </div>
                                            <button @click="reconcilingId = {{ $pm->id }}; reconcilingName = '{{ $pm->name }}'; reconcilingBalance = {{ $pm->balance }}; showAccountModal = false;" class="text-white/40 hover:text-white transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10 border-2 border-dashed border-gray-100 rounded-[2.5rem]">
                                    <p class="text-xs font-bold text-gray-400">لا توجد حسابات مضافة</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="bg-white rounded-[3.5rem] border border-gray-100 shadow-sm p-10">
                        <div class="flex justify-between items-center mb-10">
                            <h3 class="text-2xl font-black text-gray-900 tracking-tight">العمليات الأخيرة</h3>
                            <a href="{{ route('funds.transactions', $fund->id) }}" class="text-[10px] font-black text-indigo-600 hover:underline">عرض الكل</a>
                        </div>
                        <div class="space-y-6">
                            @foreach($fund->recent_transactions->take(6) as $transaction)
                                <div class="flex items-center justify-between p-5 rounded-[1.8rem] hover:bg-gray-50 transition-all border border-transparent hover:border-gray-100">
                                    <div class="flex items-center gap-5">
                                        <div class="w-12 h-12 {{ $transaction->type == 'income' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} rounded-2xl flex items-center justify-center text-lg">
                                            {{ $transaction->type == 'income' ? '↓' : '↑' }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-gray-900 mb-1">{{ $transaction->description ?: $transaction->category }}</p>
                                            <p class="text-[10px] font-bold text-gray-400 tracking-tighter">{{ $transaction->transaction_date->format('Y/m/d') }} • {{ $transaction->category }}</p>
                                        </div>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-sm font-black {{ $transaction->type == 'income' ? 'text-emerald-700' : 'text-rose-700' }}">
                                            {{ $transaction->type == 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 0) }}
                                        </p>
                                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $transaction->paymentMethod->currency ?? $fund->currency }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right Column: Assets & Equity -->
                <div class="lg:col-span-8 space-y-10">
                    
                    <!-- Assets & Properties -->
                    <div class="bg-white rounded-[3.5rem] border border-gray-100 shadow-sm overflow-hidden">
                        <div class="p-10 border-b border-gray-50 flex justify-between items-center">
                            <div>
                                <h3 class="text-2xl font-black text-gray-900 tracking-tight">الأصول والممتلكات</h3>
                                <p class="text-xs font-bold text-gray-400 mt-1">إجمالي الأصول غير النقدية المضافة للصندوق</p>
                            </div>
                            <button @click="showAssetModal = true" class="px-6 py-3 bg-indigo-50 text-indigo-600 rounded-2xl text-[10px] font-black hover:bg-indigo-600 hover:text-white transition-all">إضافة أصل</button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-10">
                            @forelse($fund->assets as $asset)
                                <div class="p-8 bg-gray-50/50 rounded-[2.5rem] border border-gray-100 hover:border-indigo-100 transition-all group">
                                    <div class="flex justify-between items-start mb-6">
                                        <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-2xl shadow-sm group-hover:rotate-6 transition-transform">
                                            @if($asset->type == 'car') 🚗 @elseif($asset->type == 'furniture') 🪑 @elseif($asset->type == 'inventory') 📦 @else 🏢 @endif
                                        </div>
                                        <span class="text-[10px] font-black bg-indigo-100 text-indigo-700 px-4 py-1.5 rounded-full uppercase tracking-widest">{{ number_format($asset->value, 0) }} {{ $fund->currency }}</span>
                                    </div>
                                    <h4 class="text-xl font-black text-gray-900 mb-2">{{ $asset->name }}</h4>
                                    <p class="text-xs font-bold text-gray-400 tracking-widest mb-6 uppercase">تاريخ الشراء: {{ $asset->purchase_date->format('Y-m-d') }}</p>
                                    <div class="flex items-center gap-2">
                                        <div class="h-1.5 flex-1 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-indigo-500 rounded-full" style="width: 100%"></div>
                                        </div>
                                        <span class="text-[10px] font-black text-indigo-600">جديد</span>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-20 bg-gray-50/30 rounded-[3.5rem] border-2 border-dashed border-gray-100">
                                    <p class="text-gray-400 font-bold tracking-widest uppercase italic">لا توجد أصول مضافة حالياً</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Equities & Partners -->
                    <div class="bg-white rounded-[3.5rem] border border-gray-100 shadow-sm overflow-hidden">
                        <div class="p-10 border-b border-gray-50 flex justify-between items-center">
                            <div>
                                <h3 class="text-2xl font-black text-gray-900 tracking-tight">توزيع الحصص والشركاء</h3>
                                <p class="text-xs font-bold text-gray-400 mt-1">تقسيم ملكية الصندوق والأرباح المستحقة</p>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-right border-collapse">
                                <thead class="bg-gray-50/50 border-b border-gray-100">
                                    <tr>
                                        <th class="px-10 py-8 text-[10px] font-black text-gray-400 uppercase tracking-widest">الشريك</th>
                                        <th class="px-6 py-8 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">نوع الحصة</th>
                                        <th class="px-6 py-8 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">المساهمة</th>
                                        <th class="px-6 py-8 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">النسبة</th>
                                        <th class="px-6 py-8 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">القيمة الحالية</th>
                                        <th class="px-10 py-8 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($fund->equities as $equity)
                                        <tr class="hover:bg-indigo-50/20 transition-colors group">
                                            <td class="px-10 py-10">
                                                <div class="flex items-center gap-5">
                                                    <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-[1.5rem] flex items-center justify-center font-black text-xl shadow-sm border border-indigo-100">
                                                        {{ mb_substr($equity->partner->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <p class="font-black text-gray-900 text-lg mb-1">{{ $equity->partner->name }}</p>
                                                        @if($equity->partner->linked_user_id == auth()->id())
                                                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[9px] font-black rounded-full border border-emerald-100">أنت (المدير)</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-10 text-center">
                                                <span class="px-4 py-2 {{ $equity->equity_type == 'fixed' ? 'bg-amber-50 text-amber-600 border border-amber-100' : 'bg-gray-100 text-gray-600' }} rounded-xl text-[10px] font-black uppercase tracking-widest">
                                                    {{ $equity->equity_type == 'fixed' ? 'نسبة ثابتة' : 'رأس مال' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-10 text-center">
                                                <p class="font-black text-gray-900">{{ number_format($equity->amount, 0) }}</p>
                                                <p class="text-[9px] font-bold text-gray-400">{{ $fund->currency }}</p>
                                            </td>
                                            <td class="px-6 py-10 text-center">
                                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full border-4 border-indigo-50 bg-white shadow-sm">
                                                    <span class="text-sm font-black text-indigo-600">{{ number_format($equity->percentage, 1) }}%</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-10 text-center">
                                                <p class="text-xl font-black text-emerald-700 tracking-tighter">{{ number_format(($equity->percentage / 100) * $fund->current_value, 0) }}</p>
                                                <p class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest">{{ $fund->currency }}</p>
                                            </td>
                                            <td class="px-10 py-10 text-center">
                                                <div class="flex items-center justify-center gap-3">
                                                    <button @click="editingEquity = {{ $equity->id }}" class="p-3 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-2xl transition-all">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    </button>
                                                    <form action="{{ route('funds.removePartner', $equity->id) }}" method="POST" onsubmit="return confirm('استبعاد الشريك من الصندوق؟')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="p-3 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-2xl transition-all">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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
