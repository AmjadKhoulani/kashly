<x-app-layout>
    <div class="py-12 px-6" x-data="{ showModal: false, showReconcile: false, type: 'expense' }">
        <div class="max-w-7xl mx-auto space-y-12">
            
            <!-- Breadcrumbs & Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
                <div>
                    <nav class="flex items-center gap-3 text-xs font-black text-slate-400 uppercase tracking-widest mb-6">
                        <a href="{{ route('wallets.index') }}" class="hover:text-indigo-600 transition-colors">المحافظ الشخصية</a>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                        <span class="text-slate-900">{{ $wallet->name }}</span>
                    </nav>
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 bg-white border-2 border-slate-100 rounded-[2rem] flex items-center justify-center text-4xl shadow-sm">
                            💰
                        </div>
                        <div>
                            <h2 class="text-5xl font-black text-slate-900 tracking-tight">{{ $wallet->name }}</h2>
                            @if($wallet->custodian_name)
                                <p class="text-sm font-black text-amber-700 bg-amber-50 px-4 py-2 rounded-xl inline-block mt-3 border border-amber-100 shadow-sm">بعهدة: {{ $wallet->custodian_name }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button @click="showReconcile = true" class="bg-white border-2 border-amber-200 text-amber-600 px-10 py-5 rounded-[2.5rem] font-black text-base shadow-sm hover:bg-amber-50 transition-all flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        مطابقة الرصيد
                    </button>
                    <button @click="showModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-12 py-5 rounded-[2.5rem] font-black text-xl shadow-xl shadow-indigo-500/20 transition-all hover:scale-105">إضافة عملية</button>
                    <form action="{{ route('wallets.destroy', $wallet->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه المحفظة؟ لا يمكن التراجع عن هذه الخطوة.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-16 h-16 bg-rose-50 text-rose-600 rounded-3xl flex items-center justify-center shadow-sm hover:bg-rose-600 hover:text-white transition-all border-2 border-rose-100">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Stats & Chart Area -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <div class="lg:col-span-2 premium-card p-14 bg-white flex flex-col justify-between overflow-hidden relative border-2 border-slate-100">
                    <div class="absolute -right-20 -top-20 w-80 h-80 bg-indigo-500/5 rounded-full blur-3xl"></div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-xs text-slate-400 font-black uppercase tracking-widest mb-6">الرصيد الحالي للمحفظة</p>
                            <p class="text-8xl font-black text-slate-900 tracking-tighter">{{ number_format($wallet->balance, 0) }} <span class="text-3xl opacity-40">{{ $wallet->currency }}</span></p>
                        </div>
                        <div class="text-left">
                            <span class="px-8 py-3 bg-emerald-50 text-emerald-600 text-sm font-black rounded-2xl shadow-sm border border-emerald-100">نشط الآن ✅</span>
                        </div>
                    </div>
                    <div class="mt-20 h-56 flex items-end gap-3">
                         @foreach(range(1, 15) as $i)
                            <div class="flex-1 bg-slate-50 rounded-2xl hover:bg-indigo-600 transition-all duration-500 cursor-help shadow-inner" style="height: {{ rand(30, 100) }}%" title="نشاط اليوم {{ $i }}"></div>
                         @endforeach
                    </div>
                </div>

                <div class="space-y-10">
                    <div class="premium-card p-12 bg-slate-900 text-white shadow-2xl shadow-slate-900/40 border-4 border-slate-800">
                        <h3 class="text-3xl font-black mb-10 flex items-center gap-4">
                            <span class="w-2 h-8 bg-indigo-500 rounded-full"></span>
                            ملخص مالي
                        </h3>
                        <div class="space-y-8">
                            <div class="flex justify-between items-center border-b border-white/10 pb-6">
                                <span class="text-sm font-black text-slate-400 uppercase tracking-widest">إجمالي الإيداعات</span>
                                <span class="text-2xl font-black text-emerald-400">+{{ number_format($transactions->where('type', 'income')->sum('amount'), 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-black text-slate-400 uppercase tracking-widest">إجمالي المصاريف</span>
                                <span class="text-2xl font-black text-rose-400">-{{ number_format($transactions->where('type', 'expense')->sum('amount'), 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="premium-card overflow-hidden bg-white shadow-2xl border-2 border-slate-100">
                <div class="px-12 py-10 border-b-2 border-slate-50 flex justify-between items-center bg-slate-50/30">
                    <h3 class="text-3xl font-black text-slate-900">سجل عمليات المحفظة التاريخي</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse">
                        <thead class="bg-slate-50/80 border-b-2 border-slate-100">
                            <tr>
                                <th class="px-12 py-8 text-xs font-black text-slate-400 uppercase tracking-widest">التاريخ</th>
                                <th class="px-8 py-8 text-xs font-black text-slate-400 uppercase tracking-widest">البيان والتفاصيل</th>
                                <th class="px-8 py-8 text-xs font-black text-slate-400 uppercase tracking-widest text-center">التصنيف</th>
                                <th class="px-8 py-8 text-xs font-black text-slate-400 uppercase tracking-widest text-center">المبلغ</th>
                                <th class="px-12 py-8 text-xs font-black text-slate-400 uppercase tracking-widest text-left">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-slate-50">
                            @forelse($transactions as $transaction)
                                <tr class="hover:bg-slate-50/80 transition-all group">
                                    <td class="px-12 py-8 whitespace-nowrap">
                                        <span class="text-sm font-black text-slate-400 group-hover:text-indigo-600 transition-colors tracking-widest">{{ $transaction->transaction_date->format('Y-m-d') }}</span>
                                    </td>
                                    <td class="px-8 py-8">
                                        <p class="font-black text-slate-900 text-lg group-hover:text-indigo-600 transition-colors">{{ $transaction->description ?: $transaction->category->name }}</p>
                                    </td>
                                    <td class="px-8 py-8 text-center">
                                        <span class="px-5 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest shadow-sm">
                                            {{ $transaction->category->icon ?? '📦' }} {{ $transaction->category->name }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-8 text-center">
                                        <span class="text-2xl font-black {{ $transaction->type == 'income' ? 'text-emerald-600' : 'text-rose-600' }} tracking-tighter">
                                            {{ $transaction->type == 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 0) }} <span class="text-sm opacity-40">{{ $wallet->currency }}</span>
                                        </span>
                                    </td>
                                    <td class="px-12 py-8 text-left">
                                        <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" onsubmit="return confirm('حذف العملية نهائياً؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-3 bg-white border border-slate-100 text-slate-300 hover:text-rose-600 hover:border-rose-100 hover:shadow-lg rounded-2xl transition-all">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-12 py-32 text-center text-slate-300 text-lg font-black uppercase tracking-widest italic">لا توجد عمليات مسجلة لهذه المحفظة حالياً 🏝️</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
        </div>
    </div>

    <!-- Reconcile Modal -->
    <div x-show="showReconcile" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-white rounded-[4rem] w-full max-w-md p-12 shadow-2xl relative text-right" @click.away="showReconcile = false">
            <h3 class="text-3xl font-black text-gray-900 mb-8">مطابقة رصيد</h3>
            <p class="text-gray-500 font-bold mb-8 text-sm leading-relaxed">أدخل المبلغ الحقيقي الموجود في هذه المحفظة حالياً. سيقوم النظام تلقائياً بإنشاء عملية تسوية بالفرق إذا وجد.</p>
            
            <form action="{{ route('wallets.reconcile', $wallet->id) }}" method="POST" class="space-y-8">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-4 tracking-widest mr-2">المبلغ الحقيقي الحالي ({{ $wallet->currency }})</label>
                    <input type="number" name="actual_balance" required step="0.01" class="w-full premium-input" placeholder="مثلاً: 500.00">
                </div>

                <button type="submit" class="w-full bg-amber-500 text-white py-6 rounded-[2.5rem] font-black text-xl shadow-xl shadow-amber-500/20 hover:bg-amber-600 transition-all">تأكيد المطابقة</button>
            </form>
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
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-4 tracking-widest mr-2">المبلغ</label>
                        <input type="number" name="amount" required step="0.01" class="w-full premium-input">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-4 tracking-widest mr-2 text-right">نوع العملية</label>
                        <div class="grid grid-cols-2 gap-2 p-1 bg-gray-50 rounded-2xl">
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="income" x-model="type" class="hidden peer">
                                <div class="py-2 text-center rounded-xl text-[10px] font-black peer-checked:bg-white peer-checked:text-emerald-600 shadow-sm transition-all">إيداع</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="expense" x-model="type" class="hidden peer">
                                <div class="py-2 text-center rounded-xl text-[10px] font-black peer-checked:bg-white peer-checked:text-rose-600 shadow-sm transition-all">سحب</div>
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-4 tracking-widest mr-2">التصنيف</label>
                    <select name="category" required class="w-full premium-input">
                        <template x-if="type == 'income'">
                            <optgroup label="تصنيفات الإيداع">
                                <option value="راتب">راتب</option>
                                <option value="هدية">هدية</option>
                                <option value="أرباح">أرباح</option>
                                <option value="أخرى">أخرى</option>
                            </optgroup>
                        </template>
                        <template x-if="type == 'expense'">
                            <optgroup label="تصنيفات السحب">
                                <option value="طعام">طعام</option>
                                <option value="فواتير">فواتير</option>
                                <option value="إيجار">إيجار</option>
                                <option value="صحة">صحة</option>
                                <option value="أخرى">أخرى</option>
                            </optgroup>
                        </template>
                    </select>
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
