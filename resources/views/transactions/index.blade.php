<x-app-layout>
    <div class="py-12 px-6" x-data="{ showModal: false, filter: 'all' }">
        <div class="max-w-7xl mx-auto space-y-10">
            
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h2 class="text-4xl font-black text-gray-900 tracking-tight">العمليات المالية</h2>
                    @if(request()->has('source_id'))
                        <div class="flex items-center gap-2 mt-2">
                            <p class="text-indigo-600 font-bold">عرض العمليات الخاصة بـ: {{ $transactions->first()->transactionable->name ?? 'مصدر محدد' }}</p>
                            <a href="{{ route('transactions.index') }}" class="text-[10px] font-black text-rose-500 bg-rose-50 px-2 py-1 rounded-md uppercase">إلغاء الفلترة ×</a>
                        </div>
                    @else
                        <p class="text-gray-500 font-bold mt-2">سجل ذكي لجميع التدفقات المالية وتوزيعات الأرباح.</p>
                    @endif
                </div>
                <div class="flex items-center gap-4">
                    <button @click="showModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-[2rem] text-sm font-black shadow-xl shadow-indigo-500/20 flex items-center transition-all hover:scale-105">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                        تسجيل عملية جديدة
                    </button>
                </div>
            </div>

            <!-- Filters & Integration Card -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <div class="flex items-center gap-4 bg-white/60 backdrop-blur-md p-2 rounded-[2.5rem] border border-white shadow-sm">
                    <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'text-gray-500 hover:bg-gray-50'" class="px-8 py-3 rounded-[2rem] text-sm font-black transition-all">الكل</button>
                    <button @click="filter = 'income'" :class="filter === 'income' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-200' : 'text-gray-500 hover:bg-gray-50'" class="px-8 py-3 rounded-[2rem] text-sm font-black transition-all">الإيرادات</button>
                    <button @click="filter = 'expense'" :class="filter === 'expense' ? 'bg-rose-500 text-white shadow-lg shadow-rose-200' : 'text-gray-500 hover:bg-gray-50'" class="px-8 py-3 rounded-[2rem] text-sm font-black transition-all">المصاريف</button>
                </div>
                <div class="bg-indigo-50 p-4 rounded-[2.5rem] border border-indigo-100 flex items-center justify-between group cursor-pointer hover:bg-indigo-100 transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-lg">🔌</div>
                        <span class="text-[10px] font-black text-indigo-900 uppercase">ربط الأنظمة</span>
                    </div>
                    <svg class="w-5 h-5 text-indigo-400 group-hover:translate-x-[-4px] transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                </div>
            </div>

            <!-- Transactions List -->
            <div class="space-y-6">
                @forelse($transactions as $transaction)
                    <div x-show="filter === 'all' || filter === '{{ $transaction->type }}'" class="premium-card p-8 group">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <div class="flex items-center gap-6">
                                <div class="w-16 h-16 {{ $transaction->type == 'income' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} rounded-[1.5rem] flex items-center justify-center text-3xl transition-transform group-hover:scale-110">
                                    {{ $transaction->type == 'income' ? '📈' : '📉' }}
                                </div>
                                <div>
                                    <p class="text-xl font-black text-gray-900 flex items-center gap-2">
                                        {{ $transaction->description ?: $transaction->category }}
                                        @if($transaction->invoice_path)
                                            <a href="{{ asset('storage/' . $transaction->invoice_path) }}" target="_blank" class="w-6 h-6 bg-indigo-50 text-indigo-600 rounded-md flex items-center justify-center text-[10px]" title="عرض الفاتورة">📄</a>
                                        @endif
                                    </p>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $transaction->transaction_date->format('Y/m/d') }}</span>
                                        <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                                        <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">{{ $transaction->category }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex flex-col md:items-end gap-2 w-full md:w-auto border-t md:border-t-0 border-gray-50 pt-4 md:pt-0" x-data="{ openMenu: false }">
                                <div class="flex items-center gap-3">
                                    <p class="text-2xl font-black {{ $transaction->type == 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $transaction->type == 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                    </p>
                                    <div class="relative">
                                        <button @click="openMenu = !openMenu" class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 5v.01M12 12v.01M12 19v.01"></path></svg>
                                        </button>
                                        <div x-show="openMenu" @click.away="openMenu = false" class="absolute left-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-gray-50 z-50 overflow-hidden" x-cloak>
                                            <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full text-right px-6 py-4 text-xs font-black text-rose-600 hover:bg-rose-50 transition-colors">حذف العملية</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="px-3 py-1 bg-gray-50 rounded-lg text-[10px] font-black text-gray-400 uppercase">
                                        {{ $transaction->transactionable->name ?? 'مصدر خارجي' }}
                                    </div>
                                    @if($transaction->paymentMethod)
                                        <div class="px-3 py-1 bg-indigo-50 rounded-lg text-[10px] font-black text-indigo-400 uppercase">
                                            🏦 {{ $transaction->paymentMethod->name }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-[4rem] p-24 text-center border border-dashed border-gray-200">
                        <div class="text-6xl mb-6">🏜️</div>
                        <h3 class="text-2xl font-black text-gray-900 mb-2">لا توجد عمليات حالياً</h3>
                        <p class="text-gray-400 font-bold">ابدأ بإضافة أول عملية مالية لمتابعة تدفقاتك النقدية.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="pt-6">
                {{ $transactions->links() }}
            </div>

        </div>

        <!-- Add Transaction Modal -->
        <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
            <div class="bg-white rounded-[4rem] w-full max-w-xl p-12 shadow-2xl relative text-right overflow-y-auto max-h-[90vh]" @click.away="showModal = false">
                <div class="flex justify-between items-center mb-10">
                    <h3 class="text-3xl font-black text-gray-900">تسجيل عملية مالية</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-900 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">نوع العملية</label>
                        <div class="grid grid-cols-2 gap-4 p-2 bg-gray-50 rounded-[2rem]">
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="income" class="hidden peer" checked>
                                <div class="py-4 text-center rounded-[1.5rem] font-black text-xs peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm transition-all">إيراد / أرباح</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="expense" class="hidden peer">
                                <div class="py-4 text-center rounded-[1.5rem] font-black text-xs peer-checked:bg-white peer-checked:text-rose-600 peer-checked:shadow-sm transition-all">مصروف / التزام</div>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">المصدر</label>
                            <select name="source_type" class="w-full bg-gray-50 border-0 rounded-[2rem] p-6 font-bold text-lg focus:ring-4 focus:ring-indigo-600/10 transition-all">
                                <option value="InvestmentFund">صندوق استثمار</option>
                                <option value="Business">مشروع تجاري</option>
                                <option value="Wallet">محفظة شخصية</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">تحديد المصدر</label>
                            <select name="source_id" class="w-full bg-gray-50 border-0 rounded-[2rem] p-6 font-bold text-lg focus:ring-4 focus:ring-indigo-600/10 transition-all">
                                @foreach($funds as $f) <option value="{{ $f->id }}">{{ $f->name }}</option> @endforeach
                                @foreach($businesses as $b) <option value="{{ $b->id }}">{{ $b->name }}</option> @endforeach
                                @foreach($wallets as $w) <option value="{{ $w->id }}">{{ $w->name }}</option> @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">المبلغ ($)</label>
                        <input type="number" step="0.01" name="amount" required class="w-full bg-gray-50 border-0 rounded-[2rem] p-6 font-black text-3xl focus:ring-4 focus:ring-indigo-600/10 transition-all" placeholder="0.00">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">التصنيف</label>
                            <select name="category" required class="w-full bg-gray-50 border-0 rounded-[2rem] p-6 font-bold text-lg focus:ring-4 focus:ring-indigo-600/10 transition-all">
                                <option value="أرباح">أرباح</option>
                                <option value="رواتب">رواتب</option>
                                <option value="إيجار">إيجار</option>
                                <option value="تسويق">تسويق</option>
                                <option value="صيانة">صيانة</option>
                                <option value="تأسيس">تأسيس</option>
                                <option value="أخرى">أخرى</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">التاريخ</label>
                            <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full bg-gray-50 border-0 rounded-[2rem] p-6 font-bold text-lg focus:ring-4 focus:ring-indigo-600/10 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">وسيلة الدفع / الحساب</label>
                        <select name="payment_method_id" class="w-full bg-gray-50 border-0 rounded-[2rem] p-6 font-bold text-lg focus:ring-4 focus:ring-indigo-600/10 transition-all">
                            <option value="">-- اختر الحساب --</option>
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }} ({{ number_format($pm->balance, 0) }} {{ $pm->currency }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">توثيق العملية (فاتورة/إيصال)</label>
                        <input type="file" name="invoice" class="w-full bg-gray-50 border-0 rounded-[2rem] p-6 font-bold text-sm focus:ring-4 focus:ring-indigo-600/10 transition-all">
                        <p class="text-[10px] text-gray-400 mt-2 mr-2">يمكنك رفع ملف PDF أو صورة (الحد الأقصى 4MB)</p>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 text-white py-6 rounded-[2.5rem] font-black text-xl shadow-xl shadow-indigo-500/20 hover:bg-indigo-700 transition-all">تأكيد العملية</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
