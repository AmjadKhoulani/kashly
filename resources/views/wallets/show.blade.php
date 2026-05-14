<x-app-layout>
    <div class="py-12 px-6" x-data="{ showModal: false }">
        <div class="max-w-7xl mx-auto space-y-12">
            
            <!-- Breadcrumbs & Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">
                        <a href="{{ route('wallets.index') }}" class="hover:text-indigo-600 transition-colors">المحافظ الشخصية</a>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                        <span class="text-gray-900">{{ $wallet->name }}</span>
                    </nav>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-white border border-gray-100 rounded-3xl flex items-center justify-center text-3xl shadow-sm">
                            💰
                        </div>
                        <div>
                            <h2 class="text-4xl font-black text-gray-900 tracking-tight">{{ $wallet->name }}</h2>
                            @if($wallet->custodian_name)
                                <p class="text-xs font-black text-amber-600 bg-amber-50 px-3 py-1 rounded-lg inline-block mt-2">بعهدة: {{ $wallet->custodian_name }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button @click="showModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-5 rounded-[2rem] font-black text-lg shadow-xl shadow-indigo-500/20 transition-all hover:scale-105">إضافة عملية</button>
                    <form action="{{ route('wallets.destroy', $wallet->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه المحفظة؟ لا يمكن التراجع عن هذه الخطوة.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-14 h-14 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center shadow-sm hover:bg-rose-600 hover:text-white transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Stats & Chart Area -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <div class="lg:col-span-2 premium-card p-12 bg-white flex flex-col justify-between overflow-hidden relative">
                    <div class="absolute -right-20 -top-20 w-80 h-80 bg-indigo-500/5 rounded-full blur-3xl"></div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-4">الرصيد الحالي للمحفظة</p>
                            <p class="text-7xl font-black text-gray-900 tracking-tighter">${{ number_format($wallet->balance, 2) }}</p>
                        </div>
                        <div class="text-left">
                            <span class="px-6 py-2 bg-emerald-50 text-emerald-600 text-xs font-black rounded-full shadow-sm">نشط</span>
                        </div>
                    </div>
                    <div class="mt-16 h-48 flex items-end gap-2">
                         <!-- Placeholder for mini-chart or activity indicator -->
                         @foreach(range(1, 12) as $i)
                            <div class="flex-1 bg-indigo-50 rounded-full hover:bg-indigo-600 transition-colors cursor-help" style="height: {{ rand(20, 100) }}%" title="نشاط اليوم {{ $i }}"></div>
                         @endforeach
                    </div>
                </div>

                <div class="space-y-8">
                    <div class="premium-card p-10 bg-indigo-900 text-white shadow-2xl shadow-indigo-900/20">
                        <h3 class="text-xl font-black mb-8">ملخص سريع 📊</h3>
                        <div class="space-y-6">
                            <div class="flex justify-between items-center border-b border-white/5 pb-4">
                                <span class="text-xs font-bold text-gray-400 tracking-widest">إجمالي الإيداعات</span>
                                <span class="text-lg font-black text-emerald-400">+${{ number_format($transactions->where('type', 'income')->sum('amount'), 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-gray-400 tracking-widest">إجمالي المصاريف</span>
                                <span class="text-lg font-black text-rose-400">-${{ number_format($transactions->where('type', 'expense')->sum('amount'), 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="premium-card overflow-hidden bg-white shadow-sm border border-gray-50">
                <div class="px-10 py-8 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="text-2xl font-black text-gray-900">سجل عمليات المحفظة</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse">
                        <thead class="bg-gray-50/50 border-b border-gray-100">
                            <tr>
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">التاريخ</th>
                                <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">البيان</th>
                                <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">التصنيف</th>
                                <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">المبلغ</th>
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-left">إجراء</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($transactions as $transaction)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-10 py-6 whitespace-nowrap">
                                        <span class="text-xs font-black text-gray-400 group-hover:text-indigo-600 transition-colors">{{ $transaction->transaction_date->format('Y-m-d') }}</span>
                                    </td>
                                    <td class="px-6 py-6">
                                        <p class="font-black text-gray-900 text-sm">{{ $transaction->description ?: $transaction->category }}</p>
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <span class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg text-[10px] font-black uppercase tracking-tighter">
                                            {{ $transaction->category }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <span class="text-lg font-black {{ $transaction->type == 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $transaction->type == 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 0) }}
                                        </span>
                                    </td>
                                    <td class="px-10 py-6 text-left">
                                        <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" onsubmit="return confirm('حذف العملية؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-300 hover:text-rose-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-10 py-20 text-center text-gray-400 font-bold uppercase tracking-widest italic">لا توجد عمليات مسجلة لهذه المحفظة</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Transaction Modal -->
    <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-white rounded-[4rem] w-full max-w-xl p-12 shadow-2xl relative text-right overflow-y-auto max-h-[90vh]" @click.away="showModal = false">
            <h3 class="text-3xl font-black text-gray-900 mb-10">تسجيل عملية محفظة</h3>
            <form action="{{ route('transactions.store') }}" method="POST" class="space-y-8">
                @csrf
                <input type="hidden" name="source_type" value="Wallet">
                <input type="hidden" name="source_id" value="{{ $wallet->id }}">

                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-4 tracking-widest mr-2">المبلغ ($)</label>
                        <input type="number" name="amount" required step="0.01" class="w-full premium-input">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-4 tracking-widest mr-2 text-right">نوع العملية</label>
                        <select name="type" required class="w-full premium-input">
                            <option value="income">إيداع / إيراد (+)</option>
                            <option value="expense">سحب / مصروف (-)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-4 tracking-widest mr-2">التصنيف</label>
                    <input type="text" name="category" required class="w-full premium-input" placeholder="مثلاً: هدايا، مصاريف بيت، راتب...">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-4 tracking-widest mr-2">وصف إضافي</label>
                    <textarea name="description" rows="3" class="w-full premium-input"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-4 tracking-widest mr-2">التاريخ</label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full premium-input">
                    </div>
                    <div>
                         <label class="block text-[10px] font-black text-gray-400 uppercase mb-4 tracking-widest mr-2">العملة</label>
                         <input type="text" name="currency" value="USD" class="w-full premium-input uppercase" maxlength="3">
                    </div>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white py-6 rounded-[2.5rem] font-black text-xl shadow-xl shadow-indigo-500/20 hover:bg-indigo-700 transition-all">تأكيد العملية</button>
            </form>
        </div>
    </div>
</x-app-layout>
