<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/20 pb-16" x-data="{ showEditModal: false, editingTransaction: {}, type: 'income' }">
        
        {{-- Sticky Header --}}
        <div class="bg-white/95 border-b border-slate-100 sticky top-0 z-40 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('funds.show', $fund->id) }}" class="w-8 h-8 bg-slate-50 hover:bg-slate-100 border border-slate-200/50 rounded-xl flex items-center justify-center transition-all text-slate-500">
                            <svg class="w-4 h-4 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <div>
                            <h1 class="text-base font-black text-slate-900 tracking-tight">سجل العمليات المالية</h1>
                            <p class="text-xs text-slate-400 font-semibold">{{ $fund->name }}</p>
                        </div>
                    </div>
                    <a href="{{ route('funds.show', $fund->id) }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl font-black text-xs transition-all shadow-sm flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        العودة للكيان الاستثماري
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-8 text-right" dir="rtl">

            {{-- Mini Stats Bar --}}
            @php $profit = $income - $expense; @endphp
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Income Card --}}
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50/30 rounded-2xl p-4 border border-emerald-100/70 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-1">إجمالي الإيرادات</p>
                        <p class="text-xl font-black text-emerald-700 tracking-tighter">{{ number_format($income, 0) }} <span class="text-xs font-bold text-emerald-500/80">{{ $fund->currency }}</span></p>
                    </div>
                    <div class="w-10 h-10 bg-white/70 rounded-xl flex items-center justify-center text-xl shadow-sm border border-emerald-100/30">📈</div>
                </div>

                {{-- Expense Card --}}
                <div class="bg-gradient-to-br from-rose-50 to-red-50/30 rounded-2xl p-4 border border-rose-100/70 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-1">إجمالي المصاريف</p>
                        <p class="text-xl font-black text-rose-700 tracking-tighter">{{ number_format($expense, 0) }} <span class="text-xs font-bold text-rose-500/80">{{ $fund->currency }}</span></p>
                    </div>
                    <div class="w-10 h-10 bg-white/70 rounded-xl flex items-center justify-center text-xl shadow-sm border border-rose-100/30">📉</div>
                </div>

                {{-- Capital Card --}}
                <div class="bg-gradient-to-br from-violet-50 to-indigo-50/30 rounded-2xl p-4 border border-violet-100/70 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-violet-500 uppercase tracking-widest mb-1">حركات رأس المال</p>
                        <p class="text-xl font-black text-violet-700 tracking-tighter">{{ number_format($capital, 0) }} <span class="text-xs font-bold text-violet-500/80">{{ $fund->currency }}</span></p>
                    </div>
                    <div class="w-10 h-10 bg-white/70 rounded-xl flex items-center justify-center text-xl shadow-sm border border-violet-100/30">💼</div>
                </div>

                {{-- Profit/Loss Card --}}
                <div class="bg-gradient-to-br {{ $profit >= 0 ? 'from-indigo-50 to-blue-50/30 border-indigo-100/70' : 'from-amber-50 to-orange-50/30 border-amber-100/70' }} rounded-2xl p-4 border shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black {{ $profit >= 0 ? 'text-indigo-500' : 'text-amber-500' }} uppercase tracking-widest mb-1">صافي الربح/الخسارة</p>
                        <p class="text-xl font-black {{ $profit >= 0 ? 'text-indigo-700' : 'text-amber-700' }} tracking-tighter">
                            {{ $profit >= 0 ? '+' : '' }}{{ number_format($profit, 0) }} 
                            <span class="text-xs font-bold {{ $profit >= 0 ? 'text-indigo-500/80' : 'text-amber-500/80' }}">{{ $fund->currency }}</span>
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-white/70 rounded-xl flex items-center justify-center text-xl shadow-sm border border-indigo-100/30">⚖️</div>
                </div>
            </div>

            {{-- Transactions Table Card --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">التاريخ</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">التفاصيل / البيان</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">التصنيف</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">الوسيلة / الحساب</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">المبلغ</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">المرفقات</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($transactions as $transaction)
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-black text-slate-400 group-hover:text-indigo-600 transition-colors">{{ $transaction->transaction_date->format('Y-m-d') }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-black text-slate-900 text-sm leading-tight">{{ $transaction->description ?: $transaction->category }}</p>
                                        @if($transaction->currency !== 'USD')
                                            <p class="text-[9px] font-black text-indigo-400 uppercase mt-1" dir="ltr" class="text-right">
                                                {{ number_format($transaction->amount_in_currency, 2) }} {{ $transaction->currency }} (سعر: {{ number_format($transaction->exchange_rate, 4) }})
                                            </p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2.5 py-1 bg-slate-50 text-slate-600 border border-slate-100 rounded-lg text-[9px] font-black uppercase tracking-tight shadow-xs">
                                            {{ $transaction->category }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-xs font-black text-slate-700 leading-none">{{ $transaction->paymentMethod->name ?? '—' }}</span>
                                            @if(isset($transaction->paymentMethod->type))
                                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mt-1">{{ $transaction->paymentMethod->type }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-sm font-black tracking-tight
                                            @if($transaction->type == 'income') text-emerald-600
                                            @elseif($transaction->type == 'capital') text-violet-600
                                            @else text-rose-600 @endif">
                                            {{ $transaction->type == 'income' ? '+' : ($transaction->type == 'capital' ? '●' : '−') }}
                                            {{ number_format($transaction->original_amount ?? $transaction->amount, 0) }}
                                            <span class="text-[10px] opacity-75 font-black">{{ $transaction->paymentMethod->currency ?? $fund->currency }}</span>
                                        </span>
                                        @if($transaction->type == 'capital')
                                            <p class="text-[8px] text-violet-400 font-black uppercase tracking-widest mt-0.5">رأس مال</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($transaction->invoice_path)
                                            <a href="{{ asset('storage/' . $transaction->invoice_path) }}" target="_blank" class="inline-flex w-8 h-8 bg-indigo-50 text-indigo-600 rounded-xl items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-xs border border-indigo-100/30">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                            </a>
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-left">
                                        <div class="flex items-center gap-2 justify-end">
                                            <button @click="editingTransaction = {
                                                id: '{{ $transaction->id }}',
                                                amount: '{{ $transaction->original_amount ?? $transaction->amount }}',
                                                type: '{{ $transaction->type }}',
                                                category: '{{ $transaction->category }}',
                                                description: '{{ addslashes($transaction->description) }}',
                                                transaction_date: '{{ $transaction->transaction_date->format('Y-m-d') }}',
                                                payment_method_id: '{{ $transaction->payment_method_id }}'
                                            }; type = editingTransaction.type; showEditModal = true"
                                            class="w-8 h-8 bg-slate-50 text-amber-600 rounded-lg flex items-center justify-center hover:bg-amber-600 hover:text-white transition-all border border-slate-200/50 shadow-xs"
                                            title="تعديل الحركة">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </button>
                                            <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الحركة نهائياً وتحديث أرصدة الصندوق؟')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-8 h-8 bg-slate-50 text-rose-600 rounded-lg flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all border border-slate-200/50 shadow-xs" title="حذف الحركة">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center text-slate-400 font-bold">لا توجد عمليات مسجلة حالياً لهذا الصندوق</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($transactions->hasPages())
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>

        </div>

        {{-- Edit Transaction Modal (Popup Modal) --}}
        <div x-show="showEditModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
            <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl relative text-right overflow-hidden animate-fade-in" @click.away="showEditModal = false">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-8 py-5 border-b border-slate-100">
                    <button @click="showEditModal = false" class="w-8 h-8 bg-slate-100 hover:bg-slate-200 rounded-xl flex items-center justify-center transition-all text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    <h3 class="text-lg font-black text-slate-900">تعديل الحركة المالية</h3>
                </div>
                
                {{-- Modal Form --}}
                <form :action="`/transactions/${editingTransaction.id}`" method="POST" class="p-8 space-y-5">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 mr-1">نوع العملية</label>
                        <div class="p-1 bg-slate-100 rounded-2xl grid grid-cols-3 gap-1">
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="income" x-model="type" class="hidden peer">
                                <div class="py-2 text-center rounded-xl font-black text-xs peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm transition-all text-slate-500">إيراد / أرباح</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="expense" x-model="type" class="hidden peer">
                                <div class="py-2 text-center rounded-xl font-black text-xs peer-checked:bg-white peer-checked:text-rose-600 peer-checked:shadow-sm transition-all text-slate-500">مصروف / التزام</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="capital" x-model="type" class="hidden peer">
                                <div class="py-2 text-center rounded-xl font-black text-xs peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm transition-all text-slate-500">رأس مال</div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 mr-1">المبلغ</label>
                        <div class="bg-slate-50 rounded-2xl p-4">
                            <input type="number" step="0.01" name="amount" x-model="editingTransaction.amount" required 
                                   class="w-full bg-transparent border-0 font-black text-3xl text-center outline-none focus:ring-0"
                                   :class="{
                                       'text-emerald-600': type === 'income',
                                       'text-rose-600': type === 'expense',
                                       'text-indigo-600': type === 'capital'
                                   }"
                                   placeholder="0.00">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 mr-1">البيان / الوصف</label>
                        <input type="text" name="description" x-model="editingTransaction.description" 
                               class="w-full bg-gray-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none" 
                               placeholder="بيان الحركة...">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 mr-1">التصنيف</label>
                            <select name="category" x-model="editingTransaction.category" required 
                                    class="w-full bg-gray-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
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
                                <template x-if="type == 'capital'">
                                    <optgroup label="تصنيفات رأس المال">
                                        <option value="رأس مال مساهم">رأس مال مساهم</option>
                                        <option value="أخرى">أخرى</option>
                                    </optgroup>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 mr-1">التاريخ</label>
                            <input type="date" name="transaction_date" x-model="editingTransaction.transaction_date" required 
                                   class="w-full bg-gray-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 mr-1">وسيلة الدفع / الحساب</label>
                        <select name="payment_method_id" x-model="editingTransaction.payment_method_id" 
                                class="w-full bg-gray-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
                            <option value="">-- اختر الحساب --</option>
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }} ({{ number_format($pm->balance, 0) }} {{ $pm->currency }})</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" 
                        class="w-full px-5 py-3.5 bg-indigo-600 text-white rounded-xl font-black text-sm shadow-lg shadow-indigo-500/20 hover:scale-105 active:scale-95 transition-all">
                        تأكيد التعديل وحفظ البيانات
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
