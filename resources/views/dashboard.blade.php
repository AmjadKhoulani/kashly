<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Welcome Header -->
            <div class="flex justify-between items-center px-4">
                <div>
                    <h2 class="text-3xl font-black text-gray-900">أهلاً بك، {{ Auth::user()->name }} 👋</h2>
                    <p class="text-gray-500 text-sm mt-1">إليك ملخص سريع لوضعك المالي اليوم.</p>
                </div>
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl text-sm font-black shadow-lg shadow-indigo-500/20 flex items-center">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    عملية جديدة
                </button>
            </div>

            <!-- Main Stats Card -->
            <div class="spendee-card p-10 text-center relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-500/5 rounded-full blur-3xl"></div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-2">إجمالي النقد المتاح (شخصي)</p>
                <h2 class="text-5xl font-black text-gray-900 mb-8">${{ number_format($totalPersonalCash, 2) }}</h2>
                
                <div class="grid grid-cols-2 gap-4 border-t border-gray-50 pt-8">
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">القطاع التجاري (أصول)</p>
                        <p class="text-xl font-black text-indigo-600">${{ number_format($totalBusinessValue, 2) }}</p>
                    </div>
                    <div class="text-left border-r border-gray-50 pr-4">
                        <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">عدد الصناديق</p>
                        <p class="text-xl font-black text-gray-900">{{ $funds->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="spendee-card p-6 flex items-center group cursor-pointer hover:bg-indigo-50 transition-colors">
                    <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center text-xl ml-4 group-hover:scale-110 transition-transform">💼</div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase">إدارة الاستثمارات</p>
                        <h4 class="text-lg font-black text-gray-900">صناديق الاستثمار والشركاء</h4>
                    </div>
                </div>
                <div class="spendee-card p-6 flex items-center group cursor-pointer hover:bg-emerald-50 transition-colors">
                    <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center text-xl ml-4 group-hover:scale-110 transition-transform">💰</div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase">إدارة الدخل الشخصي</p>
                        <h4 class="text-lg font-black text-gray-900">المحافظ والمصاريف الخاصة</h4>
                    </div>
                </div>
            </div>

            <!-- Charts & Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 spendee-card p-8">
                    <h4 class="font-bold text-gray-900 mb-6">النشاط المالي (تجاري vs شخصي)</h4>
                    <div id="dashboardChart"></div>
                </div>
                <div class="spendee-card p-8">
                    <h4 class="font-bold text-gray-900 mb-6">أحدث العمليات</h4>
                    <div class="space-y-6">
                        @forelse($recentTransactions as $transaction)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-lg">
                                        {{ $transaction->type == 'income' ? '📈' : '📉' }}
                                    </div>
                                    <div class="mr-3">
                                        <p class="text-sm font-black text-gray-900">{{ $transaction->description }}</p>
                                        <p class="text-[10px] text-gray-400 uppercase font-black">{{ $transaction->transaction_date->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <span class="text-sm font-black {{ $transaction->type == 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $transaction->type == 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-gray-400 text-xs font-bold">لا يوجد عمليات مسجلة حالياً</p>
                            </div>
                        @endforelse
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
                    data: [31, 40, 28, 51, 42, 109, 100]
                }, {
                    name: 'شخصي',
                    data: [11, 32, 45, 32, 34, 52, 41]
                }],
                chart: {
                    height: 300,
                    type: 'area',
                    toolbar: { show: false },
                    fontFamily: 'Noto Sans Arabic, sans-serif',
                },
                colors: ['#4f46e5', '#10b981'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                xaxis: {
                    categories: ["سبت", "أحد", "إثن", "ثلا", "أرب", "خمي", "جمع"],
                    labels: { style: { colors: '#828282', fontFamily: 'Noto Sans Arabic' } }
                },
                yaxis: { show: false },
                grid: { borderColor: '#F2F2F2' },
                legend: { position: 'top', horizontalAlign: 'right', fontFamily: 'Noto Sans Arabic' },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.2,
                        opacityTo: 0.02,
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#dashboardChart"), options);
            chart.render();
        });
    </script>
</x-app-layout>
