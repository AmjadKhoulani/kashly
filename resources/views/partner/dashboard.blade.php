<x-app-layout>
    <div class="py-12 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h1 class="text-3xl font-black text-gray-900">أهلاً بك، {{ $partner->name }}</h1>
                    <p class="text-sm font-bold text-gray-400 mt-1">نظرة عامة على حصصك واستثماراتك</p>
                </div>
                <div class="flex items-center gap-4">
                    <span class="px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl font-black text-sm border border-emerald-100">حساب شريك</span>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">إجمالي قيمة حصصك</div>
                    <div class="text-4xl font-black text-gray-900">${{ number_format($stats['total_equity_value'], 2) }}</div>
                </div>
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">عدد الصناديق المشارك بها</div>
                    <div class="text-4xl font-black text-gray-900">{{ $stats['fund_count'] }}</div>
                </div>
            </div>

            <!-- Equities Table -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden mb-12">
                <div class="px-8 py-6 border-b border-gray-50">
                    <h3 class="text-xl font-black text-gray-900">توزيع الحصص</h3>
                </div>
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">الاستثمار</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">النسبة</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">القيمة الحالية للحصة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($equities as $equity)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-6 font-bold text-gray-900">{{ $equity->equitable->name ?? 'N/A' }}</td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-2">
                                        <div class="w-12 h-2 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-indigo-600" style="width: {{ $equity->percentage }}%"></div>
                                        </div>
                                        <span class="font-black text-indigo-600">{{ $equity->percentage }}%</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 font-black text-gray-900">${{ number_format($equity->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50">
                    <h3 class="text-xl font-black text-gray-900">آخر العمليات ذات الصلة</h3>
                </div>
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">التاريخ</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">البيان</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">المبلغ الكلي</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($stats['recent_transactions'] as $transaction)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-6 text-sm font-bold text-gray-400">{{ $transaction->transaction_date->format('Y/m/d') }}</td>
                                <td class="px-8 py-6 font-bold text-gray-900">{{ $transaction->description }}</td>
                                <td class="px-8 py-6 font-black {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $transaction->type === 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-8 py-10 text-center text-gray-400 font-bold">لا توجد عمليات مؤخراً</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
