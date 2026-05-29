<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/20 pb-16">
        
        {{-- Sticky Header --}}
        <div class="bg-white/95 border-b border-slate-100 sticky top-0 z-40 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                <div class="flex items-center justify-between h-16">
                    <div>
                        <h1 class="text-base font-black text-slate-900 tracking-tight">لوحة التحكم الرئيسية</h1>
                        <p class="text-xs text-slate-400 font-semibold">أهلاً {{ Auth::user()->name }}، نظرة شاملة على ثروتك وأنشطتك اليوم</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-xl text-xs font-black border border-emerald-100/70 shadow-sm">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                            USD/SYP: {{ number_format($sypRate, 0) }} ل.س
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-8 text-right" dir="rtl">

            {{-- Top KPI Cards Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Estimated Net Worth Card (Indigo Gradient) --}}
                <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-3xl p-6 text-white shadow-xl shadow-indigo-500/10 relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full blur-xl group-hover:scale-150 transition-all duration-700"></div>
                    <div class="flex justify-between items-start mb-4 relative z-10">
                        <div>
                            <p class="text-[10px] font-black text-indigo-200 uppercase tracking-widest">صافي الثروة المقدرة</p>
                            <h3 class="text-3xl font-black tracking-tighter mt-1">${{ number_format($estimatedTotalUSD, 0) }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center text-xl shadow-inner">🏛️</div>
                    </div>
                    <div class="flex flex-wrap gap-2 mt-4 relative z-10 pt-2 border-t border-white/10">
                        @if(isset($totalByCurrency['SYP']) && $totalByCurrency['SYP'] > 0)
                            <p class="text-[10px] font-bold text-indigo-100">
                                * تشمل {{ number_format($totalByCurrency['SYP'], 0) }} ل.س بسعر صرف {{ number_format($sypRate, 0) }} ل.س
                            </p>
                        @else
                            <p class="text-[10px] font-bold text-indigo-100">
                                تقدير فوري موحد لكافة حساباتك
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Personal Cash Card (White/Emerald) --}}
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between group">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">النقد المتوفر (المحافظ الشخصية)</p>
                            <h3 class="text-3xl font-black text-emerald-600 tracking-tighter mt-1">${{ number_format($estimatedPersonalCashUSD, 0) }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl border border-emerald-100 group-hover:scale-110 transition-transform">💵</div>
                    </div>
                    <div class="flex flex-wrap gap-1.5 mt-2 pt-2 border-t border-slate-50">
                        @foreach(collect($personalCashByCurrency)->forget('USD') as $curr => $val)
                            <span class="text-[10px] font-black text-slate-600 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100">{{ number_format($val, 0) }} {{ $curr }}</span>
                        @endforeach
                    </div>
                </div>

                {{-- Business & Investments Card (White/Amber) --}}
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between group">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">المشاريع والاستثمارات</p>
                            <h3 class="text-3xl font-black text-amber-600 tracking-tighter mt-1">${{ number_format($estimatedBusinessValueUSD, 0) }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl border border-amber-100 group-hover:scale-110 transition-transform">💼</div>
                    </div>
                    <div class="flex flex-wrap gap-1.5 mt-2 pt-2 border-t border-slate-50">
                        <span class="text-[10px] font-black text-slate-600 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100">
                            المشاريع: ${{ number_format($estimatedBusinessOnlyUSD, 0) }}
                        </span>
                        <span class="text-[10px] font-black text-slate-600 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100">
                            الصناديق: ${{ number_format($estimatedFundsOnlyUSD, 0) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Debts & Claims (Ledger Stats) Section --}}
            <div class="space-y-4 pt-2">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-black text-slate-900 tracking-tight">إحصائيات الديون والالتزامات بذمة الآخرين (الدفتر)</h2>
                    <a href="{{ route('ledger.index') }}" class="text-xs text-indigo-600 font-black hover:underline flex items-center gap-1">
                        إدارة دفتر الديون
                        <svg class="w-3.5 h-3.5 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Receivables Card (Emerald Theme) --}}
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50/30 rounded-3xl p-5 border border-emerald-100/70 shadow-sm hover:shadow-md transition-all duration-300">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">ديون لي (مستحقات بذمة الآخرين)</span>
                            <span class="text-xl">💸</span>
                        </div>
                        <h4 class="text-2xl font-black text-emerald-700 tracking-tighter">${{ number_format($totalReceivablesUSD, 2) }}</h4>
                        <p class="text-[10px] text-emerald-500 font-semibold mt-1">مبالغ مستحقة لك لم يتم تحصيلها بالكامل بعد</p>
                    </div>

                    {{-- Payables Card (Rose Theme) --}}
                    <div class="bg-gradient-to-br from-rose-50 to-red-50/30 rounded-3xl p-5 border border-rose-100/70 shadow-sm hover:shadow-md transition-all duration-300">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-[10px] font-black text-rose-600 uppercase tracking-widest">ديون عليّ (التزامات وقروض للآخرين)</span>
                            <span class="text-xl">🏦</span>
                        </div>
                        <h4 class="text-2xl font-black text-rose-700 tracking-tighter">${{ number_format($totalPayablesUSD, 2) }}</h4>
                        <p class="text-[10px] text-rose-500 font-semibold mt-1">قروض، أقساط، أو مبالغ يتوجب عليك سدادها</p>
                    </div>

                    {{-- Net Debt Card (Violet Theme) --}}
                    <div class="bg-gradient-to-br from-violet-50 to-indigo-50/30 rounded-3xl p-5 border border-violet-100/70 shadow-sm hover:shadow-md transition-all duration-300">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-[10px] font-black text-violet-600 uppercase tracking-widest">صافي الديون المستحقة</span>
                            <span class="text-xl">⚖️</span>
                        </div>
                        <h4 class="text-2xl font-black {{ $netDebtsUSD >= 0 ? 'text-emerald-700' : 'text-rose-700' }} tracking-tighter">
                            {{ $netDebtsUSD >= 0 ? '+' : '' }}${{ number_format($netDebtsUSD, 2) }}
                        </h4>
                        <p class="text-[10px] text-violet-500 font-semibold mt-1">الفارق المالي بين ما لك وما عليك من التزامات</p>
                    </div>
                </div>
            </div>

            {{-- Upcoming Due Reminders Grid --}}
            @if($upcomingDebts->count() > 0)
                <div class="space-y-4 pt-2">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-black text-slate-900 tracking-tight">مواعيد الاستحقاق القريبة (تنبيهات السداد)</h2>
                        <span class="text-[10px] bg-amber-50 text-amber-600 border border-amber-100 font-black px-2.5 py-1 rounded-lg">قيد الانتظار</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        @foreach($upcomingDebts as $debt)
                            @php
                                $daysLeft = now()->startOfDay()->diffInDays($debt->due_date->startOfDay(), false);
                                $remaining = $debt->remaining_amount;
                            @endphp
                            <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden flex flex-col justify-between group">
                                {{-- Top Details --}}
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="px-2 py-0.5 rounded-md text-[9px] font-black border uppercase tracking-wider
                                            {{ $debt->type == 'receivable' ? 'bg-emerald-50 text-emerald-600 border-emerald-100/50' : 'bg-rose-50 text-rose-600 border-rose-100/50' }}">
                                            {{ $debt->type_label }}
                                        </span>
                                        
                                        @if($daysLeft < 0)
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-black bg-red-100 text-red-700 animate-pulse border border-red-200">
                                                متأخر منذ {{ abs($daysLeft) }} يوم
                                            </span>
                                        @elseif($daysLeft == 0)
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-black bg-amber-100 text-amber-800 animate-bounce border border-amber-200">
                                                اليوم هو موعد السداد
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-black bg-slate-50 text-slate-500 border border-slate-100">
                                                متبقي {{ $daysLeft }} يوم
                                            </span>
                                        @endif
                                    </div>
                                    <h4 class="text-sm font-black text-slate-950 truncate group-hover:text-indigo-600 transition-colors">{{ $debt->party_name }}</h4>
                                    <p class="text-xs text-slate-400 font-semibold mt-0.5 line-clamp-1">{{ $debt->description ?: 'بدون تفاصيل إضافية' }}</p>
                                </div>

                                {{-- Bottom Metrics & Action --}}
                                <div class="mt-4 pt-3 border-t border-slate-50 flex items-center justify-between">
                                    <div>
                                        <p class="text-[9px] font-black text-slate-400">المبلغ المتبقي</p>
                                        <p class="text-sm font-black text-slate-900">{{ number_format($remaining, 2) }} {{ $debt->currency }}</p>
                                    </div>
                                    <a href="{{ route('ledger.show', $debt->id) }}" class="text-[10px] text-indigo-600 font-black hover:underline bg-indigo-50/50 hover:bg-indigo-50 px-2.5 py-1.5 rounded-lg border border-indigo-100/30 transition-all">
                                        تسجيل حركة ←
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Main Layout: Chart & Side Activity Columns --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pt-2">
                
                {{-- Chart & Investment Funds (Col Span 2) --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Chart Container --}}
                    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h4 class="text-base font-black text-slate-950 tracking-tight">تحليل التدفقات النقدية</h4>
                                <p class="text-xs text-slate-400 font-semibold">مقارنة بين النشاط التجاري الاستثماري والنشاط الشخصي اليومي</p>
                            </div>
                            <div class="flex gap-4 bg-slate-50 px-4 py-2 rounded-xl border border-slate-100">
                                <div class="flex items-center gap-2 border-l border-slate-200 pl-4">
                                    <span class="w-3 h-3 bg-amber-400 rounded-full"></span>
                                    <span class="text-[10px] font-black text-slate-600">تجاري</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 bg-indigo-500 rounded-full"></span>
                                    <span class="text-[10px] font-black text-slate-600">شخصي</span>
                                </div>
                            </div>
                        </div>
                        <div id="dashboardChart" class="min-h-[350px]"></div>
                    </div>

                    {{-- Investment Funds Showcase --}}
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm font-black text-slate-900 tracking-tight">أداء المحافظ والصناديق النشطة</h2>
                            <a href="{{ route('funds.index') }}" class="text-xs text-indigo-600 font-black hover:underline flex items-center gap-1">
                                جميع الصناديق
                                <svg class="w-3.5 h-3.5 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            @foreach($funds->take(2) as $fund)
                                @php
                                    $fundProfit = $fund->current_value - $fund->capital;
                                    $fundProfitPct = $fund->capital > 0 ? (($fund->current_value - $fund->capital) / $fund->capital) * 100 : 0;
                                    $barPercent = min(100, ($fund->current_value / max($fund->capital, 1)) * 100);
                                @endphp
                                <div class="p-5 bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-md hover:border-indigo-100 transition-all cursor-pointer group flex flex-col justify-between" onclick="window.location='{{ route('funds.show', $fund->id) }}'">
                                    <div>
                                        <div class="flex justify-between items-start mb-4">
                                            <div class="w-12 h-12 bg-indigo-50/50 border border-indigo-100/50 rounded-2xl flex items-center justify-center text-2xl group-hover:rotate-6 transition-all duration-300 shadow-inner">{{ $fund->icon ?? '📊' }}</div>
                                            <span class="text-[9px] font-black text-indigo-600 uppercase tracking-widest bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100/30">كيان استثماري</span>
                                        </div>
                                        <h5 class="text-sm font-black text-slate-950 mb-1 group-hover:text-indigo-600 transition-colors leading-tight">{{ $fund->name }}</h5>
                                        <div class="flex items-baseline gap-2 mb-4">
                                            <p class="text-2xl font-black text-slate-900 tracking-tighter">${{ number_format($fund->current_value, 0) }}</p>
                                            <span class="text-[9px] font-black {{ $fundProfitPct >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }} px-1.5 py-0.5 rounded-md">
                                                {{ $fundProfitPct >= 0 ? '+' : '' }}{{ number_format($fundProfitPct, 1) }}%
                                            </span>
                                        </div>
                                    </div>
                                    <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-l from-indigo-600 to-indigo-400 rounded-full transition-all duration-1000" style="width: {{ $barPercent }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Side Actions & Recent Activity --}}
                <div class="space-y-6">
                    {{-- Recent Transactions List --}}
                    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-center mb-6">
                                <div>
                                    <h4 class="text-base font-black text-slate-950 tracking-tight">آخر العمليات المضافة</h4>
                                    <p class="text-[10px] text-slate-400 font-semibold mt-0.5">سجل الحركات المالية المباشرة</p>
                                </div>
                                <a href="{{ route('transactions.index') }}" class="w-8 h-8 bg-slate-50 hover:bg-slate-100 rounded-xl flex items-center justify-center text-slate-600 border border-slate-100 transition-all shadow-sm">
                                    <svg class="w-3.5 h-3.5 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                            <div class="space-y-4">
                                @forelse($recentTransactions as $transaction)
                                    @php
                                        $category = $transaction->categoryRelation;
                                        $isIncome = $transaction->type === 'income';
                                        $icon = $category ? $category->icon : '💸';
                                        $bgColor = $isIncome ? 'bg-emerald-50 text-emerald-600 border-emerald-100/50' : 'bg-rose-50 text-rose-600 border-rose-100/50';
                                    @endphp
                                    <div class="flex items-center justify-between group bg-slate-50/40 hover:bg-slate-50 p-3 rounded-2xl border border-slate-100 transition-all shadow-sm">
                                        <div class="flex items-center gap-3">
                                            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xl group-hover:scale-105 transition-transform border {{ $bgColor }}">
                                                {{ $icon }}
                                            </div>
                                            <div>
                                                <p class="text-xs font-black text-slate-900 line-clamp-1">{{ $transaction->description ?: ($category ? $category->name : $transaction->category) }}</p>
                                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ $transaction->transaction_date->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <p class="text-sm font-black {{ $isIncome ? 'text-emerald-600' : 'text-rose-600' }} tracking-tighter">
                                            {{ $isIncome ? '+' : '-' }}${{ number_format($transaction->amount, 0) }}
                                        </p>
                                    </div>
                                @empty
                                    <div class="text-center py-10">
                                        <span class="text-3xl block mb-2 opacity-40">📭</span>
                                        <p class="text-xs text-slate-400 font-bold">لا توجد عمليات مسجلة حالياً</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Sync Banner / Premium Shortcut Callout --}}
                    <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-3xl p-6 text-white shadow-xl shadow-indigo-500/10 relative overflow-hidden group">
                        <div class="absolute -right-10 -top-10 w-36 h-36 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                        <h4 class="text-lg font-black mb-1 relative z-10 leading-none">مزامنة الحسابات</h4>
                        <p class="text-indigo-100 text-xs font-bold mb-6 leading-relaxed relative z-10">اربط حساباتك البنكية وبوابات الدفع للمزامنة التلقائية والتحليل المالي الموحد.</p>
                        <a href="{{ route('integrations.index') }}" class="w-full bg-white text-indigo-700 py-3 rounded-xl font-black text-xs hover:bg-indigo-50 transition-all shadow-md relative z-10 block text-center">الربط التلقائي والدمج</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Chart Logic --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var options = {
                series: [{
                    name: 'تجاري',
                    data: @json($chartData['commercial'])
                }, {
                    name: 'شخصي',
                    data: @json($chartData['personal'])
                }],
                chart: {
                    height: 350,
                    type: 'area',
                    toolbar: { show: false },
                    fontFamily: 'Almarai, sans-serif',
                    background: 'transparent'
                },
                colors: ['#f59e0b', '#4f46e5'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 4 },
                xaxis: {
                    categories: @json($chartData['days']),
                    labels: { style: { colors: '#94a3b8', fontWeight: 900 } }
                },
                yaxis: { 
                    labels: { style: { colors: '#94a3b8', fontWeight: 900 } }
                },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 5 },
                legend: { show: false },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.25,
                        opacityTo: 0.02,
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#dashboardChart"), options);
            chart.render();
        });
    </script>
</x-app-layout>
