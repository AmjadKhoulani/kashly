<x-app-layout>
    <div class="py-12 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <h1 class="text-5xl font-black text-slate-900 tracking-tight">أهلاً بك، {{ $partner->name }} 👋</h1>
                    <p class="text-lg font-bold text-slate-400 mt-3">نظرة عامة دقيقة على حصصك وأدائك الاستثماري في كاشلي.</p>
                </div>
                <div class="flex items-center gap-4">
                    <span class="px-6 py-2.5 bg-emerald-50 text-emerald-600 rounded-xl font-black text-xs border-2 border-emerald-100 shadow-sm uppercase tracking-widest">حساب شريك نشط</span>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-16">
                <div class="premium-card p-12 bg-white border-2 border-slate-100 shadow-xl group">
                    <div class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 bg-indigo-600 rounded-full shadow-lg shadow-indigo-500/40"></span>
                        إجمالي قيمة حصصك الحالية
                    </div>
                    <div class="text-6xl font-black text-slate-900 tracking-tighter group-hover:text-indigo-600 transition-colors">${{ number_format($stats['total_equity_value'], 2) }}</div>
                </div>
                <div class="premium-card p-12 bg-white border-2 border-slate-100 shadow-xl group">
                    <div class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-600 rounded-full shadow-lg shadow-emerald-500/40"></span>
                        عدد الصناديق المشارك بها
                    </div>
                    <div class="text-6xl font-black text-slate-900 tracking-tighter group-hover:text-emerald-600 transition-colors">{{ $stats['fund_count'] }}</div>
                </div>
            </div>

            <!-- Equities Table -->
            <div class="premium-card bg-white border-2 border-slate-100 overflow-hidden mb-16 shadow-xl">
                <div class="px-12 py-10 border-b-2 border-slate-50 bg-slate-50/30">
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight">توزيع الحصص والمساهمات</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b-2 border-slate-100">
                                <th class="px-12 py-8 text-xs font-black text-slate-400 uppercase tracking-widest">الاستثمار / الكيان</th>
                                <th class="px-12 py-8 text-xs font-black text-slate-400 uppercase tracking-widest text-center">النسبة المئوية</th>
                                <th class="px-12 py-8 text-xs font-black text-slate-400 uppercase tracking-widest text-center">القيمة الحالية للحصة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-slate-50">
                            @foreach($equities as $equity)
                                <tr class="hover:bg-indigo-50/30 transition-all group">
                                    <td class="px-12 py-10">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 bg-white border border-slate-100 rounded-lg flex items-center justify-center text-xl shadow-sm group-hover:rotate-6 transition-transform">🏢</div>
                                            <span class="text-xl font-black text-slate-900">{{ $equity->equitable->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-12 py-10">
                                        <div class="flex items-center justify-center gap-5">
                                            <div class="w-32 h-3 bg-slate-100 rounded-full overflow-hidden shadow-inner border border-slate-50">
                                                <div class="h-full bg-gradient-to-l from-indigo-600 to-indigo-400 shadow-lg shadow-indigo-500/20" style="width: {{ $equity->percentage }}%"></div>
                                            </div>
                                            <span class="text-lg font-black text-indigo-600">{{ $equity->percentage }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-12 py-10 text-center">
                                        <p class="text-2xl font-black text-slate-900 tracking-tighter">${{ number_format($equity->amount, 2) }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="premium-card bg-white border-2 border-slate-100 overflow-hidden shadow-xl">
                <div class="px-12 py-10 border-b-2 border-slate-50 bg-slate-50/30">
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight">آخر التحركات والعمليات</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b-2 border-slate-100">
                                <th class="px-12 py-8 text-xs font-black text-slate-400 uppercase tracking-widest">التاريخ</th>
                                <th class="px-12 py-8 text-xs font-black text-slate-400 uppercase tracking-widest">البيان والوصف</th>
                                <th class="px-12 py-8 text-xs font-black text-slate-400 uppercase tracking-widest text-left">المبلغ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-slate-50">
                            @forelse($stats['recent_transactions'] as $transaction)
                                <tr class="hover:bg-slate-50/50 transition-all group">
                                    <td class="px-12 py-10 text-base font-black text-slate-400 uppercase tracking-widest">{{ $transaction->transaction_date->format('Y/m/d') }}</td>
                                    <td class="px-12 py-10 font-black text-slate-900 text-lg">{{ $transaction->description }}</td>
                                    <td class="px-12 py-10 text-left font-black text-2xl tracking-tighter {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $transaction->type === 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-12 py-20 text-center text-slate-300 font-black text-xl uppercase tracking-widest italic">لا توجد عمليات مؤخراً 🏝️</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
