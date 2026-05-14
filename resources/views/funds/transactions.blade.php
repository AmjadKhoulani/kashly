<x-app-layout>
    <div class="py-12 px-6">
        <div class="max-w-7xl mx-auto space-y-12">
            
            <!-- Breadcrumbs & Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">
                        <a href="{{ route('funds.index') }}" class="hover:text-indigo-600 transition-colors">صناديق الاستثمار</a>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                        <a href="{{ route('funds.show', $fund->id) }}" class="hover:text-indigo-600 transition-colors">{{ $fund->name }}</a>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                        <span class="text-gray-900">سجل العمليات المالية</span>
                    </nav>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-white border border-gray-100 rounded-3xl flex items-center justify-center text-3xl shadow-sm">
                            💸
                        </div>
                        <h2 class="text-4xl font-black text-gray-900 tracking-tight">سجل العمليات المالية</h2>
                    </div>
                </div>
                <a href="{{ route('funds.show', $fund->id) }}" class="bg-white border border-gray-100 text-gray-900 px-8 py-4 rounded-[2rem] text-sm font-black shadow-sm hover:bg-gray-50 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l7-7-7-7"></path></svg>
                    العودة للصندوق
                </a>
            </div>

            <!-- Mini Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="premium-card p-8 bg-emerald-50 border-emerald-100 border">
                    <p class="text-[10px] text-emerald-600 font-black uppercase mb-2 tracking-widest">إجمالي الإيرادات</p>
                    <p class="text-3xl font-black text-emerald-700 tracking-tighter">${{ number_format($income, 0) }}</p>
                </div>
                <div class="premium-card p-8 bg-rose-50 border-rose-100 border">
                    <p class="text-[10px] text-rose-600 font-black uppercase mb-2 tracking-widest">إجمالي المصاريف</p>
                    <p class="text-3xl font-black text-rose-700 tracking-tighter">${{ number_format($expense, 0) }}</p>
                </div>
                @php $profit = $income - $expense; @endphp
                <div class="premium-card p-8 {{ $profit >= 0 ? 'bg-indigo-50 border-indigo-100' : 'bg-amber-50 border-amber-100' }} border">
                    <p class="text-[10px] {{ $profit >= 0 ? 'text-indigo-600' : 'text-amber-600' }} font-black uppercase mb-2 tracking-widest">صافي الربح/الخسارة</p>
                    <p class="text-3xl font-black {{ $profit >= 0 ? 'text-indigo-700' : 'text-amber-700' }} tracking-tighter">${{ number_format($profit, 0) }}</p>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="premium-card overflow-hidden bg-white">
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse">
                        <thead class="bg-gray-50/80 border-b border-gray-100">
                            <tr>
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">التاريخ</th>
                                <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">التفاصيل / البيان</th>
                                <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">التصنيف</th>
                                <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">الوسيلة</th>
                                <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">المبلغ</th>
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-left">مرفق</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($transactions as $transaction)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-10 py-6">
                                        <span class="text-xs font-black text-gray-400 group-hover:text-indigo-600 transition-colors">{{ $transaction->transaction_date->format('Y-m-d') }}</span>
                                    </td>
                                    <td class="px-6 py-6">
                                        <p class="font-black text-gray-900 text-sm">{{ $transaction->description ?: $transaction->category }}</p>
                                        @if($transaction->currency !== 'USD')
                                            <p class="text-[10px] font-bold text-indigo-400 uppercase mt-0.5">{{ $transaction->amount_in_currency }} {{ $transaction->currency }} ({{ $transaction->exchange_rate }})</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <span class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg text-[10px] font-black uppercase tracking-tighter shadow-sm">
                                            {{ $transaction->category }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-xs font-black text-gray-700">{{ $transaction->paymentMethod->name ?? '--' }}</span>
                                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">{{ $transaction->paymentMethod->type ?? '' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <span class="text-lg font-black {{ $transaction->type == 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $transaction->type == 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 0) }}
                                        </span>
                                    </td>
                                    <td class="px-10 py-6 text-left">
                                        @if($transaction->invoice_path)
                                            <a href="{{ asset('storage/' . $transaction->invoice_path) }}" target="_blank" class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                            </a>
                                        @else
                                            <span class="text-gray-300">--</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-10 py-20 text-center text-gray-400 font-bold">لا توجد عمليات مسجلة حالياً لهذا الصندوق</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($transactions->hasPages())
                    <div class="px-10 py-8 bg-gray-50 border-t border-gray-100">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
