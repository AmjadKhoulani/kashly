<x-app-layout>
    <div class="py-12 px-6" x-data="{ showEditModal: false, editingTransaction: {}, type: 'income' }">
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
                                <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">مرفق</th>
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-left">الإجراءات</th>
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
                                    <td class="px-6 py-6 text-center">
                                        @if($transaction->invoice_path)
                                            <a href="{{ asset('storage/' . $transaction->invoice_path) }}" target="_blank" class="inline-flex w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                            </a>
                                        @else
                                            <span class="text-gray-300">--</span>
                                        @endif
                                    </td>
                                    <td class="px-10 py-6 text-left">
                                        <div class="flex items-center gap-3 justify-end">
                                            <button @click="editingTransaction = {
                                                id: '{{ $transaction->id }}',
                                                amount: '{{ $transaction->original_amount ?? $transaction->amount }}',
                                                type: '{{ $transaction->type }}',
                                                category: '{{ $transaction->category }}',
                                                description: '{{ $transaction->description }}',
                                                transaction_date: '{{ $transaction->transaction_date->format('Y-m-d') }}',
                                                payment_method_id: '{{ $transaction->payment_method_id }}'
                                            }; type = editingTransaction.type; showEditModal = true"
                                            class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center hover:bg-amber-500 hover:text-white transition-all shadow-sm"
                                            title="تعديل الحركة">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </button>
                                            <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الحركة نهائياً وتحديث أرصدة الصندوق؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-10 h-10 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-sm" title="حذف الحركة">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-10 py-20 text-center text-gray-400 font-bold">لا توجد عمليات مسجلة حالياً لهذا الصندوق</td>
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

        <!-- Edit Transaction Modal -->
        <div x-show="showEditModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
            <div class="bg-white rounded-[4rem] w-full max-w-xl p-12 shadow-2xl relative text-right overflow-y-auto max-h-[90vh]" @click.away="showEditModal = false">
                <div class="flex justify-between items-center mb-10">
                    <h3 class="text-3xl font-black text-gray-900">تعديل الحركة المالية</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-900 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form :action="`/transactions/${editingTransaction.id}`" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">نوع العملية</label>
                        <div class="grid grid-cols-2 gap-4 p-2 bg-gray-50 rounded-[2rem]">
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="income" x-model="type" class="hidden peer">
                                <div class="py-4 text-center rounded-[1.5rem] font-black text-xs peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm transition-all">إيراد / أرباح</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="expense" x-model="type" class="hidden peer">
                                <div class="py-4 text-center rounded-[1.5rem] font-black text-xs peer-checked:bg-white peer-checked:text-rose-600 peer-checked:shadow-sm transition-all">مصروف / التزام</div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">المبلغ</label>
                        <input type="number" step="0.01" name="amount" x-model="editingTransaction.amount" required class="w-full premium-input text-3xl" placeholder="0.00">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">التفاصيل / البيان</label>
                        <input type="text" name="description" x-model="editingTransaction.description" class="w-full premium-input font-bold" placeholder="بيان الحركة...">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">التصنيف</label>
                            <select name="category" x-model="editingTransaction.category" required class="w-full premium-input">
                                <template x-if="type == 'income'">
                                    <optgroup label="تصنيفات الأرباح">
                                        <option value="أرباح">أرباح</option>
                                        <option value="إيداع">إيداع</option>
                                        <option value="تحويل واصل">تحويل واصل</option>
                                        <option value="بيع أصول">بيع أصول</option>
                                        <option value="أخرى">أخرى</option>
                                    </optgroup>
                                </template>
                                <template x-if="type == 'expense'">
                                    <optgroup label="تصنيفات المصاريف">
                                        <option value="مصاريف تشغيل">مصاريف تشغيل</option>
                                        <option value="رواتب">رواتب</option>
                                        <option value="إيجار">إيجار</option>
                                        <option value="صيانة">صيانة</option>
                                        <option value="خسارة تداول">خسارة تداول</option>
                                        <option value="أخرى">أخرى</option>
                                    </optgroup>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">التاريخ</label>
                            <input type="date" name="transaction_date" x-model="editingTransaction.transaction_date" required class="w-full premium-input">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">وسيلة الدفع / الحساب</label>
                        <select name="payment_method_id" x-model="editingTransaction.payment_method_id" class="w-full premium-input">
                            <option value="">-- اختر الحساب --</option>
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }} ({{ number_format($pm->balance, 0) }} {{ $pm->currency }})</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 text-white py-6 rounded-[2.5rem] font-black text-xl shadow-xl shadow-indigo-500/20 hover:bg-indigo-700 transition-all">تحديث الحركة</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
