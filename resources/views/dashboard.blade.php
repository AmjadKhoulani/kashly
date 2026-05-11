<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-10 pb-20">
        
        <!-- Spendee-style Wallet Slider (Top Section) -->
        <div class="relative group">
            <div class="flex overflow-x-auto pb-6 hide-scrollbar snap-x snap-mandatory gap-6" style="scrollbar-width: none; -ms-overflow-style: none;">
                <!-- Main Balance Card -->
                <div class="min-w-full md:min-w-[400px] snap-center bg-gradient-to-br from-indigo-600 to-violet-700 p-8 rounded-[2.5rem] shadow-2xl shadow-indigo-500/20 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-40 h-40 bg-white/10 rounded-full -ml-20 -mt-20 blur-3xl"></div>
                    <p class="text-indigo-100 text-[10px] font-black uppercase tracking-widest mb-2">إجمالي الثروة</p>
                    <h2 class="text-4xl font-black text-white mb-8">${{ number_format($netWorth, 2) }}</h2>
                    <div class="flex justify-between items-end">
                        <div class="flex -space-x-3 space-x-reverse">
                            <div class="w-10 h-10 rounded-full border-2 border-indigo-600 bg-white/20 flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="w-10 h-10 rounded-full border-2 border-indigo-600 bg-emerald-500 flex items-center justify-center shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            </div>
                        </div>
                        <span class="px-4 py-2 bg-black/20 rounded-2xl text-white text-xs font-bold backdrop-blur-md">حالة التحديث: مباشر</span>
                    </div>
                </div>

                <!-- Secondary Stats Card (Investment Funds) -->
                <div class="min-w-full md:min-w-[400px] snap-center bg-slate-900 p-8 rounded-[2.5rem] border border-white/5 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-40 h-40 bg-indigo-500/5 rounded-full -ml-20 -mt-20 blur-3xl"></div>
                    <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">صناديق الاستثمار</p>
                    <h2 class="text-4xl font-black text-white mb-8">{{ $activeFunds }} <span class="text-lg font-normal text-slate-500 italic">محفظة</span></h2>
                    <div class="flex items-center space-x-2 space-x-reverse text-emerald-400 font-bold text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                        <span>إدارة نشطة</span>
                    </div>
                </div>

                <!-- Debt Card -->
                <div class="min-w-full md:min-w-[400px] snap-center bg-rose-600 p-8 rounded-[2.5rem] shadow-2xl shadow-rose-500/20 relative overflow-hidden">
                    <p class="text-rose-100 text-[10px] font-black uppercase tracking-widest mb-2">الديون المستحقة</p>
                    <h2 class="text-4xl font-black text-white mb-8">${{ number_format($totalDebts, 2) }}</h2>
                    <div class="flex items-center space-x-2 space-x-reverse text-rose-200 font-bold text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>متابعة السداد</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions (Spendee Icons) -->
        <div class="grid grid-cols-4 gap-4 px-2">
            <button class="flex flex-col items-center space-y-2 group">
                <div class="w-16 h-16 bg-emerald-500 rounded-3xl flex items-center justify-center shadow-lg shadow-emerald-500/20 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                </div>
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">دخل</span>
            </button>
            <button class="flex flex-col items-center space-y-2 group">
                <div class="w-16 h-16 bg-rose-500 rounded-3xl flex items-center justify-center shadow-lg shadow-rose-500/20 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                </div>
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">مصروف</span>
            </button>
            <button class="flex flex-col items-center space-y-2 group">
                <div class="w-16 h-16 bg-amber-500 rounded-3xl flex items-center justify-center shadow-lg shadow-amber-500/20 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                </div>
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">تحويل</span>
            </button>
            <button class="flex flex-col items-center space-y-2 group">
                <div class="w-16 h-16 bg-indigo-500 rounded-3xl flex items-center justify-center shadow-lg shadow-indigo-500/20 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">إحصائيات</span>
            </button>
        </div>

        <!-- Spendee-style Feed (Timeline) -->
        <div class="space-y-6">
            <div class="flex items-center justify-between px-4">
                <h3 class="text-xl font-bold text-white tracking-tight">الجدول الزمني</h3>
                <button class="text-indigo-400 text-sm font-bold">عرض الكل</button>
            </div>

            <div class="space-y-3 px-2">
                @forelse($recentTransactions as $transaction)
                    <div class="glass p-5 rounded-[2.5rem] border border-white/5 flex items-center justify-between hover:bg-white/[0.03] transition-all group cursor-pointer shadow-sm">
                        <div class="flex items-center">
                            <!-- Category Icon -->
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center ml-4 relative">
                                @php
                                    $iconColor = $transaction->type == 'income' ? 'bg-emerald-500' : 'bg-rose-500';
                                    $shadowColor = $transaction->type == 'income' ? 'shadow-emerald-500/20' : 'shadow-rose-500/20';
                                @endphp
                                <div class="w-12 h-12 {{ $iconColor }} rounded-2xl flex items-center justify-center text-white shadow-lg {{ $shadowColor }} relative z-10 transition-transform group-hover:-rotate-6">
                                    @if($transaction->type == 'income')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    @else
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <p class="text-white font-bold">{{ $transaction->category }}</p>
                                <p class="text-slate-500 text-[11px] font-bold uppercase tracking-widest">{{ $transaction->wallet->name ?? 'صندوق' }} • {{ $transaction->transaction_date->format('d M, H:i') }}</p>
                            </div>
                        </div>
                        <div class="text-left">
                            <p class="text-lg font-black {{ $transaction->type == 'income' ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $transaction->type == 'income' ? '+' : '-' }}${{ number_format(abs($transaction->amount), 2) }}
                            </p>
                            <p class="text-[10px] text-slate-600 font-black uppercase tracking-widest">{{ $transaction->transaction_date->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 bg-slate-900/30 rounded-[3rem] border-dashed border-2 border-slate-800">
                        <p class="text-slate-500 font-medium">الجدول الزمني المالي فارغ حالياً.</p>
                        <p class="text-slate-600 text-xs">ابدأ بإضافة العمليات لتراها هنا.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Cash Flow Chart (Spendee Style) -->
        <div class="glass p-8 rounded-[3rem] border border-white/5 mx-2">
            <h3 class="text-xl font-bold text-white mb-8">النبض المالي</h3>
            <div id="cashFlowChart" class="w-full h-64"></div>
        </div>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var options = {
                series: [{
                    name: 'دخل',
                    data: [4500, 5200, 3800, 2400, 3300, 2600, 4800]
                }, {
                    name: 'مصروف',
                    data: [1200, 3400, 5100, 2100, 4200, 3800, 2900]
                }],
                chart: {
                    type: 'area',
                    height: 250,
                    toolbar: { show: false },
                    fontFamily: 'Cairo, sans-serif'
                },
                colors: ['#10b981', '#f43f5e'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 4 },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.3,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                xaxis: {
                    categories: ['إثن', 'ثلا', 'أرب', 'خمي', 'جمع', 'سبت', 'أحد'],
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { style: { colors: '#64748b', fontWeight: 600, fontFamily: 'Cairo' } }
                },
                yaxis: { show: false },
                grid: { show: false },
                legend: { show: false },
                theme: { mode: 'dark' }
            };

            var chart = new ApexCharts(document.querySelector("#cashFlowChart"), options);
            chart.render();
        });
    </script>
    @endpush
</x-app-layout>
