<x-app-layout>
    <div class="py-12 px-6">
        <div class="max-w-7xl mx-auto space-y-12">
            
            <!-- Breadcrumbs & Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">
                        <a href="{{ route('funds.index') }}" class="hover:text-indigo-600 transition-colors">صناديق الاستثمار</a>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                        <span class="text-gray-900">{{ $fund->name }}</span>
                    </nav>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-indigo-600 text-white rounded-3xl flex items-center justify-center text-3xl shadow-xl shadow-indigo-500/20">
                            {{ $fund->icon ?? '🏘️' }}
                        </div>
                        <h2 class="text-4xl font-black text-gray-900 tracking-tight">{{ $fund->name }}</h2>
                    </div>
                </div>
                
                <div class="flex items-center gap-3" x-data="{ showModal: false }">
                    <button class="bg-white border border-gray-100 text-gray-900 px-8 py-4 rounded-[2rem] text-sm font-black shadow-sm hover:bg-gray-50 transition-all">تعديل الصندوق</button>
                    <button @click="showModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-[2rem] text-sm font-black shadow-xl shadow-indigo-500/20 transition-all hover:scale-105">إضافة عملية مالية</button>

                    <!-- Transaction Modal -->
                    <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
                        <div class="bg-white rounded-[4rem] w-full max-w-lg p-12 shadow-2xl relative text-right" @click.away="showModal = false">
                            <div class="flex justify-between items-center mb-10">
                                <h3 class="text-3xl font-black text-gray-900">تسجيل عملية</h3>
                                <button @click="showModal = false" class="text-gray-400 hover:text-gray-900 transition-colors">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <form action="{{ route('transactions.store') }}" method="POST" class="space-y-8">
                                @csrf
                                <input type="hidden" name="source_type" value="InvestmentFund">
                                <input type="hidden" name="source_id" value="{{ $fund->id }}">
                                
                                <div class="grid grid-cols-2 gap-4 p-2 bg-gray-50 rounded-[2rem]">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="type" value="income" class="hidden peer" checked>
                                        <div class="py-4 text-center rounded-[1.5rem] font-black text-xs peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm transition-all">إيراد / أرباح</div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="type" value="expense" class="hidden peer">
                                        <div class="py-4 text-center rounded-[1.5rem] font-black text-xs peer-checked:bg-white peer-checked:text-rose-600 peer-checked:shadow-sm transition-all">مصروف / تكلفة</div>
                                    </label>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">المبلغ بالدولار</label>
                                    <input type="number" step="0.01" name="amount" required class="w-full bg-gray-50 border-0 rounded-[2rem] p-6 font-black text-3xl focus:ring-4 focus:ring-indigo-600/10 transition-all" placeholder="0.00">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">وصف العملية</label>
                                    <input type="text" name="category" required class="w-full bg-gray-50 border-0 rounded-[2rem] p-6 font-bold text-lg focus:ring-4 focus:ring-indigo-600/10 transition-all" placeholder="مثلاً: توزيع أرباح ربع سنوية">
                                </div>

                                <input type="hidden" name="transaction_date" value="{{ date('Y-m-d') }}">
                                <button type="submit" class="w-full bg-indigo-600 text-white py-6 rounded-[2.5rem] font-black text-xl shadow-xl shadow-indigo-500/20 hover:bg-indigo-700 transition-all">تأكيد العملية</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-gray-100">
                    <p class="text-[10px] text-gray-400 font-black uppercase mb-2 tracking-widest">رأس المال</p>
                    <p class="text-3xl font-black text-gray-900">${{ number_format($fund->capital, 0) }}</p>
                </div>
                <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-gray-100">
                    <p class="text-[10px] text-gray-400 font-black uppercase mb-2 tracking-widest">القيمة الحالية</p>
                    <p class="text-3xl font-black text-indigo-600">${{ number_format($fund->current_value, 0) }}</p>
                </div>
                <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-gray-100">
                    <p class="text-[10px] text-gray-400 font-black uppercase mb-2 tracking-widest">صافي الربح</p>
                    <p class="text-3xl font-black text-emerald-600">${{ number_format($fund->current_value - $fund->capital, 0) }}</p>
                </div>
                <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-gray-100">
                    <p class="text-[10px] text-gray-400 font-black uppercase mb-2 tracking-widest">معدل النمو</p>
                    @php $growth = (($fund->current_value - $fund->capital) / max($fund->capital, 1)) * 100; @endphp
                    <p class="text-3xl font-black text-emerald-600">+{{ number_format($growth, 1) }}%</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <!-- Main Content (Chart & Partners) -->
                <div class="lg:col-span-2 space-y-10">
                    <!-- Performance Chart -->
                    <div class="bg-white p-10 rounded-[4rem] border border-gray-50 shadow-sm">
                        <div class="flex justify-between items-center mb-10">
                            <h3 class="text-2xl font-black text-gray-900">أداء الصندوق</h3>
                            <div class="flex gap-2">
                                <span class="w-3 h-3 bg-indigo-600 rounded-full"></span>
                                <span class="text-[10px] font-black text-gray-400 uppercase">القيمة السوقية</span>
                            </div>
                        </div>
                        <div id="fundChart" class="w-full h-80"></div>
                    </div>

                    <!-- Partners Table -->
                    <div class="bg-white rounded-[4rem] border border-gray-50 shadow-sm overflow-hidden">
                        <div class="px-10 py-8 border-b border-gray-50 flex justify-between items-center">
                            <h3 class="text-2xl font-black text-gray-900">توزيع الحصص والشركاء</h3>
                            <button class="bg-indigo-50 text-indigo-600 px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-tighter hover:bg-indigo-100 transition-colors">+ إضافة شريك جديد</button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-right">
                                <thead class="bg-gray-50/50">
                                    <tr>
                                        <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">الشريك</th>
                                        <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">المساهمة الأصلية</th>
                                        <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">النسبة</th>
                                        <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">القيمة الحالية</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($equities as $equity)
                                        <tr class="hover:bg-gray-50/50 transition-colors group">
                                            <td class="px-10 py-8">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center text-lg font-black text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                                        {{ mb_substr($equity->partner->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <p class="font-black text-gray-900">{{ $equity->partner->name }}</p>
                                                        <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $equity->partner->phone ?? 'مساهم نشط' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-10 py-8 font-bold text-gray-600">${{ number_format($equity->amount, 0) }}</td>
                                            <td class="px-10 py-8">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-16 h-2 bg-gray-100 rounded-full overflow-hidden">
                                                        <div class="h-full bg-indigo-600 rounded-full" style="width: {{ $equity->percentage }}%"></div>
                                                    </div>
                                                    <span class="text-xs font-black text-indigo-600">{{ number_format($equity->percentage, 1) }}%</span>
                                                </div>
                                            </td>
                                            <td class="px-10 py-8">
                                                <p class="font-black text-gray-900">${{ number_format(($equity->percentage / 100) * $fund->current_value, 0) }}</p>
                                                <p class="text-[10px] text-emerald-500 font-black uppercase">ربح: ${{ number_format((($equity->percentage / 100) * $fund->current_value) - $equity->amount, 0) }}</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sidebar (Activity) -->
                <div class="space-y-10">
                    <div class="bg-white p-10 rounded-[4rem] border border-gray-50 shadow-sm relative overflow-hidden">
                        <div class="absolute -right-20 -bottom-20 w-40 h-40 bg-emerald-500/5 rounded-full blur-3xl"></div>
                        <h3 class="text-2xl font-black text-gray-900 mb-8 relative z-10">العمليات الأخيرة</h3>
                        <div class="space-y-8 relative z-10">
                            @forelse($transactions as $transaction)
                                <div class="flex items-center justify-between group cursor-pointer">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 {{ $transaction->type == 'income' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} rounded-[1.2rem] flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                                            {{ $transaction->type == 'income' ? '📈' : '📉' }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-gray-900">{{ $transaction->description }}</p>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $transaction->transaction_date->format('Y/m/d') }}</p>
                                        </div>
                                    </div>
                                    <p class="text-sm font-black {{ $transaction->type == 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $transaction->type == 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 0) }}
                                    </p>
                                </div>
                            @empty
                                <div class="text-center py-10">
                                    <div class="text-4xl mb-4">🌫️</div>
                                    <p class="text-gray-400 font-bold">لا توجد عمليات مسجلة</p>
                                </div>
                            @endforelse
                        </div>
                        <button class="w-full mt-10 py-4 bg-gray-50 rounded-2xl text-[10px] font-black text-gray-400 uppercase tracking-widest hover:bg-gray-100 transition-colors">عرض كافة العمليات</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var options = {
                series: [{
                    name: 'قيمة الصندوق',
                    data: [{{ $fund->capital }}, {{ ($fund->capital + $fund->current_value) / 2 }}, {{ $fund->current_value }}]
                }],
                chart: {
                    type: 'area',
                    height: 320,
                    toolbar: { show: false },
                    fontFamily: 'Noto Sans Arabic, sans-serif',
                },
                colors: ['#4f46e5'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 4 },
                xaxis: {
                    categories: ['التأسيس', 'نصف المدة', 'الحالي'],
                    labels: { style: { colors: '#9ca3af', fontWeight: 900, fontSize: '10px' } }
                },
                yaxis: { show: false },
                grid: { borderColor: '#f9fafb' },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.3,
                        opacityTo: 0.05,
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#fundChart"), options);
            chart.render();
        });
    </script>
</x-app-layout>
