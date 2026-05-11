<x-app-layout>
    <div class="py-12" x-data="{ showModal: false }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="flex justify-between items-center px-4">
                <div>
                    <h2 class="text-3xl font-black text-gray-900">العمليات المالية</h2>
                    <p class="text-gray-500 text-sm mt-1">سجل كامل بجميع التحركات المالية الصادرة والواردة.</p>
                </div>
                <button @click="showModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl text-sm font-black shadow-lg shadow-indigo-500/20 flex items-center">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    تسجيل عملية
                </button>
            </div>

            <!-- Integrations Section -->
            <div class="spendee-card p-6 bg-indigo-50 border-indigo-100 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-xl ml-4">🔌</div>
                    <div>
                        <h4 class="text-sm font-black text-indigo-900">ربط الأنظمة الخارجية</h4>
                        <p class="text-[10px] text-indigo-600 font-bold uppercase">Whmcs, Shopify, Stripe</p>
                    </div>
                </div>
                <button class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-xs font-black shadow-sm">إعداد الربط</button>
            </div>

            <!-- Transactions List -->
            <div class="spendee-card overflow-hidden">
                <table class="w-full text-right">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">العملية</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">المصدر</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">التاريخ</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">المبلغ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($transactions as $transaction)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-5">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 {{ $transaction->type == 'income' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} rounded-xl flex items-center justify-center text-lg ml-3">
                                            {{ $transaction->type == 'income' ? '📈' : '📉' }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ $transaction->description ?: $transaction->category }}</p>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $transaction->category }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="text-xs font-bold text-gray-500">
                                        {{ $transaction->transactionable->name ?? 'غير محدد' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-xs font-bold text-gray-400">{{ $transaction->transaction_date->format('Y/m/d') }}</td>
                                <td class="px-6 py-5 font-black {{ $transaction->type == 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $transaction->type == 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    {{ $transactions->links() }}
                </div>
            </div>

        </div>

        <!-- Add Transaction Modal -->
        <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-[2.5rem] w-full max-w-md p-10 shadow-2xl relative" @click.away="showModal = false">
                <button @click="showModal = false" class="absolute top-8 left-8 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                
                <h3 class="text-2xl font-black text-gray-900 mb-8">تسجيل عملية مالية</h3>
                
                <form action="{{ route('transactions.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">نوع العملية</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="income" class="hidden peer" checked>
                                <div class="p-4 text-center border-2 border-gray-100 rounded-2xl font-black text-sm peer-checked:border-emerald-500 peer-checked:text-emerald-600 peer-checked:bg-emerald-50 transition-all">دخل / أرباح</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="expense" class="hidden peer">
                                <div class="p-4 text-center border-2 border-gray-100 rounded-2xl font-black text-sm peer-checked:border-rose-500 peer-checked:text-rose-600 peer-checked:bg-rose-50 transition-all">مصروف / التزام</div>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">المصدر</label>
                            <select name="source_type" class="w-full bg-gray-50 border-0 rounded-xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-600">
                                <option value="InvestmentFund">صندوق استثمار</option>
                                <option value="Business">مشروع تجاري</option>
                                <option value="Wallet">محفظة شخصية</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">تحديد المصدر</label>
                            <select name="source_id" class="w-full bg-gray-50 border-0 rounded-xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-600">
                                @foreach($funds as $f) <option value="{{ $f->id }}">{{ $f->name }}</option> @endforeach
                                @foreach($businesses as $b) <option value="{{ $b->id }}">{{ $b->name }}</option> @endforeach
                                @foreach($wallets as $w) <option value="{{ $w->id }}">{{ $w->name }}</option> @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">المبلغ ($)</label>
                        <input type="number" step="0.01" name="amount" required class="w-full bg-gray-50 border-0 rounded-xl p-4 font-black text-2xl focus:ring-2 focus:ring-indigo-600" placeholder="0.00">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">التصنيف</label>
                        <input type="text" name="category" required class="w-full bg-gray-50 border-0 rounded-xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-600" placeholder="مثلاً: أرباح عقارية، فاتورة كهرباء...">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">التاريخ</label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full bg-gray-50 border-0 rounded-xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-600">
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 text-white py-5 rounded-2xl font-black shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 transition-all">تأكيد العملية</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
