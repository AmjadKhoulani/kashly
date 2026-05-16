<x-app-layout>
    <div class="py-16 px-8 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto space-y-16">
            
            <!-- Colorful Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-8 pb-10">
                <div>
                    <h2 class="text-6xl font-black text-slate-800 tracking-tighter leading-none mb-4">كاشلي.</h2>
                    <p class="text-xl font-bold text-slate-500">أهلاً {{ Auth::user()->name }}، نظرة ملونة ومشرقة لأصولك اليوم.</p>
                </div>
                <div class="flex items-center gap-10 bg-indigo-50 px-8 py-5 rounded-3xl border-2 border-indigo-100 shadow-lg shadow-indigo-100/50">
                    <div class="text-left border-l-2 border-indigo-200 pl-8 ml-8">
                        <p class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-1">صافي الثروة</p>
                        <p class="text-4xl font-black text-indigo-700 tracking-tighter">${{ number_format($totalByCurrency['USD'] ?? 0, 0) }}</p>
                    </div>
                    <button class="bg-indigo-600 text-white px-8 py-4 rounded-2xl text-lg font-black shadow-xl shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-1 transition-all">
                        تسجيل حركة +
                    </button>
                </div>
            </div>

            <!-- Colorful Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Cash Card (Light Blue) -->
                <div class="p-10 rounded-[3rem] bg-sky-50 border-2 border-sky-200 shadow-2xl shadow-sky-200/50 group hover:bg-sky-100 transition-all duration-300">
                    <div class="w-20 h-20 bg-sky-200 text-sky-700 rounded-3xl flex items-center justify-center text-4xl mb-8 border-2 border-sky-300 shadow-inner group-hover:scale-110 transition-transform">💵</div>
                    <p class="text-xs font-black text-sky-600 uppercase tracking-widest mb-2">النقد الشخصي</p>
                    <h3 class="text-5xl font-black text-sky-900 tracking-tighter mb-8">${{ number_format($wallets->where('currency', 'USD')->sum('balance'), 0) }}</h3>
                    <div class="flex flex-wrap gap-3">
                        @foreach(collect($totalByCurrency)->forget('USD')->take(2) as $curr => $val)
                            <span class="text-xs font-black text-sky-800 bg-white px-4 py-2 rounded-xl border border-sky-200 shadow-sm">{{ number_format($val, 0) }} {{ $curr }}</span>
                        @endforeach
                    </div>
                </div>

                <!-- Business Assets Card (Light Yellow/Amber) -->
                <div class="p-10 rounded-[3rem] bg-amber-50 border-2 border-amber-200 shadow-2xl shadow-amber-200/50 group hover:bg-amber-100 transition-all duration-300">
                    <div class="w-20 h-20 bg-amber-200 text-amber-700 rounded-3xl flex items-center justify-center text-4xl mb-8 border-2 border-amber-300 shadow-inner group-hover:scale-110 transition-transform">🏢</div>
                    <p class="text-xs font-black text-amber-600 uppercase tracking-widest mb-2">قطاع الأعمال</p>
                    <h3 class="text-5xl font-black text-amber-900 tracking-tighter mb-8">${{ number_format($totalBusinessValue, 0) }}</h3>
                    <div class="flex items-center gap-3 bg-white w-max px-4 py-2 rounded-xl border border-amber-200 shadow-sm">
                        <span class="w-3 h-3 bg-amber-400 rounded-full animate-pulse shadow-sm shadow-amber-400"></span>
                        <span class="text-xs font-black text-amber-700 uppercase tracking-widest">أصول عاملة</span>
                    </div>
                </div>

                <!-- Investments Card (Light Green) -->
                <div class="p-10 rounded-[3rem] bg-emerald-50 border-2 border-emerald-200 shadow-2xl shadow-emerald-200/50 group hover:bg-emerald-100 transition-all duration-300">
                    <div class="w-20 h-20 bg-emerald-200 text-emerald-700 rounded-3xl flex items-center justify-center text-4xl mb-8 border-2 border-emerald-300 shadow-inner group-hover:scale-110 transition-transform">📈</div>
                    <p class="text-xs font-black text-emerald-600 uppercase tracking-widest mb-2">الاستثمارات</p>
                    <h3 class="text-5xl font-black text-emerald-900 tracking-tighter mb-8">${{ number_format($funds->sum('current_value'), 0) }}</h3>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-black text-emerald-800 bg-white px-4 py-2 rounded-xl border border-emerald-200 shadow-sm">+ نمو مستقر</span>
                    </div>
                </div>
            </div>

            <!-- Main Content Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 pt-6">
                
                <!-- Chart Section -->
                <div class="lg:col-span-2 space-y-10">
                    <div class="bg-indigo-50 rounded-[3.5rem] border-2 border-indigo-100 p-10 shadow-xl shadow-indigo-100/50">
                        <div class="flex justify-between items-center mb-10">
                            <h4 class="text-3xl font-black text-indigo-900 tracking-tight">التدفق النقدي</h4>
                            <div class="flex gap-6 bg-white px-6 py-3 rounded-2xl border border-indigo-100 shadow-sm">
                                <div class="flex items-center gap-3 border-l-2 border-indigo-50 pl-6">
                                    <span class="w-4 h-4 bg-amber-400 rounded-full"></span>
                                    <span class="text-xs font-black text-slate-600 uppercase">تجاري</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="w-4 h-4 bg-indigo-500 rounded-full"></span>
                                    <span class="text-xs font-black text-slate-600 uppercase">شخصي</span>
                                </div>
                            </div>
                        </div>
                        <div id="dashboardChart" class="min-h-[350px]"></div>
                    </div>

                    <!-- Funds Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        @foreach($funds->take(2) as $fund)
                            <div class="p-8 rounded-[3rem] border-2 border-violet-100 bg-violet-50 hover:bg-violet-100 transition-all cursor-pointer group shadow-xl shadow-violet-100/50" onclick="window.location='{{ route('funds.show', $fund->id) }}'">
                                <div class="flex justify-between items-start mb-8">
                                    <div class="w-16 h-16 bg-white border-2 border-violet-200 rounded-2xl flex items-center justify-center text-3xl group-hover:rotate-6 transition-transform shadow-sm">{{ $fund->icon ?? '📊' }}</div>
                                    <span class="text-[10px] font-black text-violet-600 uppercase tracking-widest bg-white px-3 py-1.5 rounded-xl border border-violet-200">صندوق</span>
                                </div>
                                <h5 class="text-2xl font-black text-violet-900 mb-2">{{ $fund->name }}</h5>
                                <p class="text-4xl font-black text-violet-900 tracking-tighter mb-6">${{ number_format($fund->current_value, 0) }}</p>
                                <div class="h-2 bg-white rounded-full overflow-hidden border border-violet-200">
                                    <div class="h-full bg-violet-500 rounded-full w-2/3 shadow-inner"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Side Activity Section -->
                <div class="space-y-10">
                    <!-- Transactions List (Light Rose) -->
                    <div class="bg-rose-50 rounded-[3.5rem] border-2 border-rose-100 p-10 shadow-xl shadow-rose-100/50">
                        <div class="flex justify-between items-center mb-10">
                            <h4 class="text-2xl font-black text-rose-900 tracking-tight">آخر الحركات</h4>
                            <a href="{{ route('transactions.index') }}" class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-rose-600 border border-rose-200 hover:bg-rose-600 hover:text-white transition-colors shadow-sm">←</a>
                        </div>
                        <div class="space-y-8">
                            @forelse($recentTransactions as $transaction)
                                <div class="flex items-center justify-between group bg-white p-4 rounded-3xl border border-rose-100 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform border border-rose-100">
                                            {{ $transaction->category->icon ?? '💸' }}
                                        </div>
                                        <div>
                                            <p class="text-base font-black text-rose-900">{{ $transaction->description ?: $transaction->category->name }}</p>
                                            <p class="text-[10px] font-bold text-rose-400 uppercase tracking-widest mt-1">{{ $transaction->transaction_date->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <p class="text-xl font-black {{ $transaction->type == 'income' ? 'text-emerald-600' : 'text-rose-600' }} tracking-tighter">
                                        {{ $transaction->type == 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 0) }}
                                    </p>
                                </div>
                            @empty
                                <p class="text-center text-rose-300 font-bold py-10">لا توجد عمليات</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Integration Callout (Vibrant Gradient) -->
                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-[3.5rem] p-10 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden group border-2 border-indigo-400">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/20 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                        <h4 class="text-3xl font-black mb-4 leading-tight relative z-10">المزامنة التلقائية</h4>
                        <p class="text-indigo-100 text-base font-bold mb-8 leading-relaxed relative z-10">اربط حساباتك وتتبع أموالك بمكان واحد.</p>
                        <button class="w-full bg-white text-indigo-700 py-4 rounded-2xl font-black text-lg hover:bg-indigo-50 transition-all shadow-xl relative z-10">ربط الآن</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

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
                colors: ['#fbbf24', '#6366f1'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 4 },
                xaxis: {
                    categories: @json($chartData['days']),
                    labels: { style: { colors: '#818cf8', fontWeight: 900 } }
                },
                yaxis: { 
                    labels: { style: { colors: '#818cf8', fontWeight: 900 } }
                },
                grid: { borderColor: '#e0e7ff', strokeDashArray: 5 },
                legend: { show: false },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.3,
                        opacityTo: 0.05,
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#dashboardChart"), options);
            chart.render();
        });
    </script>
</x-app-layout>
