<x-app-layout>
    <div class="py-12 px-6">
        <div class="max-w-7xl mx-auto space-y-12">
            
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">
                        <a href="{{ route('funds.index') }}" class="hover:text-indigo-600">صناديق الاستثمار</a>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                        <a href="{{ route('funds.show', $fund->id) }}" class="hover:text-indigo-600">{{ $fund->name }}</a>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                        <span class="text-gray-900">توزيعات الأرباح</span>
                    </nav>
                    <h2 class="text-4xl font-black text-gray-900 tracking-tight">إدارة وتوزيع الأرباح</h2>
                    <p class="text-gray-500 font-bold mt-2">احتساب الأرباح الصافية وتوزيعها على الشركاء حسب الحصص المعتمدة.</p>
                </div>
            </div>

            <!-- Current Period Summary -->
            <div class="bg-gray-900 p-12 rounded-[4rem] relative overflow-hidden shadow-2xl">
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl"></div>
                <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-12 items-center">
                    <div>
                        <p class="text-[10px] text-indigo-300 font-black uppercase tracking-widest mb-4">الأرباح الصافية للفترة الحالية</p>
                        <p class="text-6xl font-black text-white tracking-tighter">${{ number_format($netProfit, 0) }}</p>
                        <div class="mt-4 flex items-center gap-3">
                            <span class="text-xs font-bold text-emerald-400 bg-emerald-400/10 px-3 py-1 rounded-full">+{{ number_format($income > 0 ? ($netProfit / $income) * 100 : 0, 1) }}% هامش الربح</span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-6">
                        <div class="flex justify-between items-center border-b border-white/5 pb-4">
                            <span class="text-xs font-bold text-gray-400">إجمالي الإيرادات</span>
                            <span class="text-lg font-black text-white">${{ number_format($income, 0) }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-white/5 pb-4">
                            <span class="text-xs font-bold text-gray-400">إجمالي المصاريف</span>
                            <span class="text-lg font-black text-rose-400">${{ number_format($expense, 0) }}</span>
                        </div>
                    </div>
                    <div class="text-left">
                        <button class="bg-emerald-500 hover:bg-emerald-600 text-white px-10 py-5 rounded-[2rem] font-black text-lg shadow-xl shadow-emerald-500/20 transition-all hover:scale-105">تنفيذ التوزيع الآن</button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Partner Distribution Preview -->
                <div class="lg:col-span-2 space-y-8">
                    <h3 class="text-2xl font-black text-gray-900 px-4">توقعات التوزيع للشركاء</h3>
                    <div class="bg-white rounded-[4rem] border border-gray-50 shadow-sm overflow-hidden">
                        <table class="w-full text-right">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">الشريك</th>
                                    <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">النسبة</th>
                                    <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">المبلغ المستحق</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($equities as $equity)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-10 py-8">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center text-lg font-black text-indigo-600">
                                                    {{ mb_substr($equity->partner->name, 0, 1) }}
                                                </div>
                                                <p class="font-black text-gray-900">{{ $equity->partner->name }}</p>
                                            </div>
                                        </td>
                                        <td class="px-10 py-8">
                                            <span class="text-xs font-black text-indigo-600 bg-indigo-50 px-3 py-1 rounded-lg">{{ number_format($equity->percentage, 1) }}%</span>
                                        </td>
                                        <td class="px-10 py-8">
                                            <p class="font-black text-xl text-gray-900">${{ number_format(($equity->percentage / 100) * $netProfit, 2) }}</p>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Historical Distributions -->
                <div class="space-y-8">
                    <h3 class="text-2xl font-black text-gray-900 px-4">التوزيعات السابقة</h3>
                    <div class="bg-white p-10 rounded-[4rem] border border-gray-50 shadow-sm space-y-8">
                        @forelse($fund->distributions as $dist)
                            <div class="flex items-center justify-between group">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-xl">✅</div>
                                    <div>
                                        <p class="text-sm font-black text-gray-900">توزيع أرباح {{ $dist->distribution_date->format('M Y') }}</p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $dist->distribution_date->format('Y/m/d') }}</p>
                                    </div>
                                </div>
                                <p class="font-black text-gray-900">${{ number_format($dist->net_amount, 0) }}</p>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <p class="text-gray-400 font-bold">لا توجد توزيعات سابقة</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
