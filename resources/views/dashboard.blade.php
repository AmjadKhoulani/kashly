<x-app-layout>
    <div class="py-12 px-6">
        <div class="max-w-7xl mx-auto space-y-10">
            
            <!-- Welcome Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h2 class="text-4xl font-black text-gray-900 tracking-tight">أهلاً بك، {{ Auth::user()->name }} 👋</h2>
                    <p class="text-gray-500 font-bold mt-1">إليك نظرة شاملة على إمبراطوريتك المالية اليوم.</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden md:flex flex-col items-end">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">الثروة الإجمالية</span>
                        <div class="flex items-center gap-3">
                            <span class="text-2xl font-black text-emerald-600">${{ number_format($totalByCurrency['USD'] ?? 0, 0) }}</span>
                            @foreach($totalByCurrency as $curr => $val)
                                @if($curr !== 'USD')
                                    <span class="text-sm font-black text-gray-400">+ {{ number_format($val, 0) }} {{ $curr }}</span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-[2rem] text-sm font-black shadow-xl shadow-indigo-500/20 flex items-center transition-all hover:scale-105">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                        تسجيل عملية
                    </button>
                </div>
            </div>

            <!-- Stats Overview Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Cash Card -->
                <div class="premium-card p-10 bg-gradient-to-br from-white to-indigo-50/30 border-t-4 border-t-indigo-600">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner">💵</div>
                        <span class="text-[10px] font-black text-indigo-400 bg-indigo-50 px-3 py-1 rounded-full uppercase tracking-widest">نقد متاح</span>
                    </div>
                    <h2 class="text-4xl font-black text-gray-900 mb-2 tracking-tighter">${{ number_format($wallets->where('currency', 'USD')->sum('balance'), 0) }}</h2>
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($totalByCurrency as $curr => $val)
                            @if($curr !== 'USD')
                                <span class="text-[10px] font-black text-indigo-600 bg-white/50 px-2 py-1 rounded border border-indigo-100">{{ number_format($val, 0) }} {{ $curr }}</span>
                            @endif
                        @endforeach
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">إجمالي رصيد المحافظ الشخصية</p>
                </div>

                <!-- Business Assets Card -->
                <div class="premium-card p-10 bg-gradient-to-br from-white to-amber-50/30 border-t-4 border-t-amber-500">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner">🏢</div>
                        <span class="text-[10px] font-black text-amber-400 bg-amber-50 px-3 py-1 rounded-full uppercase tracking-widest">قطاع الأعمال</span>
                    </div>
                    <h2 class="text-4xl font-black text-gray-900 mb-2 tracking-tighter">${{ number_format($totalBusinessValue, 0) }}</h2>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">قيمة المشاريع والأعمال</p>
                </div>

                <!-- Funds Performance Card -->
                <div class="premium-card p-10 bg-gradient-to-br from-white to-emerald-50/30 border-t-4 border-t-emerald-500">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner">📈</div>
                        <span class="text-[10px] font-black text-emerald-400 bg-emerald-50 px-3 py-1 rounded-full uppercase tracking-widest">الاستثمارات</span>
                    </div>
                    <h2 class="text-4xl font-black text-gray-900 mb-2 tracking-tighter">${{ number_format($funds->sum('current_value'), 0) }}</h2>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">إجمالي قيمة صناديق الاستثمار</p>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                
                <!-- Chart & Funds List -->
                <div class="lg:col-span-2 space-y-10">
                    <div class="premium-card p-8">
                        <div class="flex justify-between items-center mb-8">
                            <h4 class="font-black text-xl text-gray-900 flex items-center gap-3">
                                <span class="w-1.5 h-6 bg-indigo-600 rounded-full"></span>
                                النشاط المالي الأسبوعي
                            </h4>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 bg-indigo-600 rounded-full"></span>
                                    <span class="text-[10px] font-black text-gray-400 uppercase">تجاري</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                                    <span class="text-[10px] font-black text-gray-400 uppercase">شخصي</span>
                                </div>
                            </div>
                        </div>
                        <div id="dashboardChart"></div>
                    </div>

                    <!-- Investment Funds Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($funds->take(2) as $fund)
                            <div class="premium-card p-8 flex flex-col justify-between hover:border-indigo-100 transition-all cursor-pointer" onclick="window.location='{{ route('funds.show', $fund->id) }}'">
                                <div class="flex justify-between items-start">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-2xl shadow-inner border border-gray-100">
                                            {{ $fund->icon ?? '🏙️' }}
                                        </div>
                                        <div>
                                            <h5 class="font-black text-gray-900">{{ $fund->name }}</h5>
                                            <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">صندوق استثماري</p>
                                        </div>
                                    </div>
                                    <span class="text-xs font-black text-emerald-600">+12%</span>
                                </div>
                                <div class="mt-8">
                                    <div class="flex justify-between items-end mb-2">
                                        <span class="text-[10px] font-black text-gray-400 uppercase">نمو الصندوق</span>
                                        <span class="text-sm font-black text-gray-900">${{ number_format($fund->current_value, 0) }}</span>
                                    </div>
                                    <div class="w-full h-2 bg-gray-50 rounded-full overflow-hidden">
                                        <div class="h-full bg-indigo-600 rounded-full" style="width: 65%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Activity & Sidebar -->
                <div class="space-y-10">
                    <div class="premium-card p-8">
                        <div class="flex justify-between items-center mb-8">
                            <h4 class="font-black text-xl text-gray-900">آخر العمليات</h4>
                            <a href="{{ route('transactions.index') }}" class="text-[10px] font-black text-indigo-600 hover:underline uppercase tracking-widest">الكل ←</a>
                        </div>
                        <div class="space-y-8">
                            @forelse($recentTransactions as $transaction)
                                <div class="flex items-center justify-between group">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform shadow-inner border border-gray-50">
                                            {{ $transaction->type == 'income' ? '📈' : '📉' }}
                                        </div>
                                        <div class="mr-4">
                                            <p class="text-sm font-black text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $transaction->description ?: $transaction->category }}</p>
                                            <p class="text-[10px] text-gray-400 uppercase font-black tracking-tighter">{{ $transaction->transaction_date->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-sm font-black {{ $transaction->type == 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $transaction->type == 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 0) }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10">
                                    <div class="text-4xl mb-4">🏜️</div>
                                    <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">لا توجد عمليات مسجلة</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Quick Tools -->
                    <div class="bg-indigo-600 rounded-[3rem] p-10 text-white relative overflow-hidden shadow-2xl shadow-indigo-500/40">
                        <div class="absolute -right-20 -bottom-20 w-60 h-60 bg-white/10 rounded-full blur-3xl"></div>
                        <h4 class="text-2xl font-black mb-4 relative z-10">الربط الآلي</h4>
                        <p class="text-indigo-100 text-sm font-bold mb-8 relative z-10 leading-relaxed">اربط حساباتك المصرفية والمحافظ الرقمية للحصول على تحديثات فورية.</p>
                        <button class="w-full bg-white text-indigo-600 py-4 rounded-2xl font-black text-sm shadow-xl hover:bg-indigo-50 transition-all relative z-10">ابدأ الربط الآن 🔌</button>
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
                },
                colors: ['#4f46e5', '#10b981'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 4 },
                xaxis: {
                    categories: @json($chartData['days']),
                    labels: { style: { colors: '#9ca3af', fontFamily: 'Almarai', fontWeight: 700 } }
                },
                yaxis: { 
                    labels: { style: { colors: '#9ca3af', fontFamily: 'Almarai', fontWeight: 700 } }
                },
                grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
                legend: { show: false },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#dashboardChart"), options);
            chart.render();
        });
    </script>
</x-app-layout>
