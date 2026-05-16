<x-app-layout>
    <style>
        @media print {
            .no-print { display: none !important; }
            .premium-card { 
                box-shadow: none !important; 
                border: 1px solid #eee !important;
                break-inside: avoid;
            }
            body { background: white !important; }
            .py-12 { padding-top: 0 !important; padding-bottom: 0 !important; }
        }
    </style>
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

            <!-- Advanced Financial Filters & Reporting Hub -->
            <form action="{{ route('transactions.index') }}" method="GET" class="premium-card p-10 bg-white shadow-2xl space-y-8 no-print">
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-8">
                    <!-- Date Filter Group -->
                    <div class="flex gap-3">
                        <select name="month" class="flex-1 premium-input text-sm h-14">
                            <option value="">كل الأشهر</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>شهر {{ $m }}</option>
                            @endforeach
                        </select>
                        <select name="year" class="flex-1 premium-input text-sm h-14">
                            <option value="">كل السنوات</option>
                            @foreach(range(date('Y'), date('Y')-5) as $y)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Source & Type -->
                    <select name="source_type" class="premium-input text-sm h-14">
                        <option value="">جميع المصادر</option>
                        <option value="InvestmentFund" {{ request('source_type') == 'InvestmentFund' ? 'selected' : '' }}>صناديق الاستثمار</option>
                        <option value="Wallet" {{ request('source_type') == 'Wallet' ? 'selected' : '' }}>المحافظ الشخصية</option>
                    </select>

                    <select name="type" class="premium-input text-sm h-14">
                        <option value="">جميع الأنواع</option>
                        <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>الإيرادات</option>
                        <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>المصاريف</option>
                    </select>

                    <!-- Category & Currency -->
                    <select name="category" class="premium-input text-sm h-14">
                        <option value="">كل التصنيفات</option>
                        @foreach(['أرباح', 'رواتب', 'إيجار', 'تسويق', 'صيانة', 'تأسيس', 'أخرى'] as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>

                    <select name="currency" class="premium-input text-sm h-14">
                        <option value="">كل العملات</option>
                        <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                        <option value="SYP" {{ request('currency') == 'SYP' ? 'selected' : '' }}>SYP (ل.س)</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-2">
                    <select name="payment_method_id" class="premium-input text-sm h-14">
                        <option value="">جميع الحسابات البنكية / الكاش</option>
                        @foreach($paymentMethods as $pm)
                            <option value="{{ $pm->id }}" {{ request('payment_method_id') == $pm->id ? 'selected' : '' }}>{{ $pm->name }} ({{ $pm->currency }})</option>
                        @endforeach
                    </select>

                    <div class="md:col-span-2 flex gap-4">
                        <button type="submit" class="flex-1 bg-indigo-600 text-white rounded-3xl font-black text-xs uppercase shadow-xl shadow-indigo-200 hover:scale-105 transition-all">تطبيق الفلاتر الذكية</button>
                        <a href="{{ route('transactions.index') }}" class="w-40 bg-slate-50 text-slate-400 rounded-3xl font-black text-xs uppercase flex items-center justify-center border-2 border-slate-100 hover:bg-slate-100 transition-all text-center">تصفير الكل</a>
                    </div>
                </div>
                
                <div class="flex flex-col md:flex-row justify-between items-center pt-10 border-t-2 border-slate-50 gap-6">
                    <div class="flex items-center gap-10">
                        <div class="flex flex-col">
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">إجمالي الإيرادات</span>
                            <span class="text-2xl font-black text-emerald-600">${{ number_format($transactions->where('type', 'income')->sum('amount'), 0) }}</span>
                        </div>
                        <div class="w-px h-12 bg-slate-100"></div>
                        <div class="flex flex-col">
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">إجمالي المصاريف</span>
                            <span class="text-2xl font-black text-rose-600">${{ number_format($transactions->where('type', 'expense')->sum('amount'), 2) }}</span>
                        </div>
                    </div>
                    <button type="button" onclick="window.print()" class="w-full md:w-auto bg-amber-500 text-white px-12 py-5 rounded-[2.5rem] text-base font-black shadow-2xl shadow-amber-200 hover:bg-amber-600 transition-all flex items-center justify-center gap-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002-2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        توليد تقرير مالي مطبوع
                    </button>
                </div>
            </form>

            <!-- Transactions List -->
            <div class="space-y-8">
                @forelse($transactions as $transaction)
                    <div x-show="filter === 'all' || filter === '{{ $transaction->type }}'" class="premium-card p-10 group bg-white border-2 border-slate-100">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
                            <div class="flex items-center gap-8">
                                <div class="w-20 h-20 {{ $transaction->type == 'income' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }} rounded-[2rem] flex items-center justify-center text-4xl transition-all duration-500 group-hover:scale-110 group-hover:rotate-3 shadow-inner border-2 border-white">
                                    {{ $transaction->category->icon ?? ($transaction->type == 'income' ? '📈' : '📉') }}
                                </div>
                                <div>
                                    <p class="text-2xl font-black text-slate-900 flex items-center gap-4 group-hover:text-indigo-600 transition-colors">
                                        {{ $transaction->description ?: $transaction->category->name }}
                                        @if($transaction->invoice_path)
                                            <a href="{{ asset('storage/' . $transaction->invoice_path) }}" target="_blank" class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xs border border-indigo-100 hover:bg-indigo-600 hover:text-white transition-all shadow-sm" title="عرض الفاتورة">📄</a>
                                        @endif
                                    </p>
                                    <div class="flex items-center gap-5 mt-3">
                                        <span class="text-xs font-black text-slate-400 uppercase tracking-widest">{{ $transaction->transaction_date->format('Y/m/d') }}</span>
                                        <span class="w-1.5 h-1.5 bg-slate-200 rounded-full"></span>
                                        <span class="text-xs font-black px-4 py-1.5 rounded-xl uppercase tracking-widest bg-slate-50 border border-slate-100 text-slate-600">
                                            {{ $transaction->category->name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex flex-col md:items-end gap-3 w-full md:w-auto border-t md:border-t-0 border-slate-50 pt-6 md:pt-0" x-data="{ openMenu: false }">
                                <div class="flex items-center gap-5">
                                    <p class="text-3xl font-black {{ $transaction->type == 'income' ? 'text-emerald-600' : 'text-rose-600' }} tracking-tighter">
                                        {{ $transaction->type == 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 0) }}
                                    </p>
                                    <div class="relative">
                                        <button @click="openMenu = !openMenu" class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 hover:bg-indigo-600 hover:text-white transition-all border border-slate-100 shadow-sm">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 5v.01M12 12v.01M12 19v.01"></path></svg>
                                        </button>
                                        <div x-show="openMenu" @click.away="openMenu = false" class="absolute left-0 mt-3 w-56 bg-white rounded-3xl shadow-2xl border-2 border-slate-100 z-50 overflow-hidden" x-cloak x-transition>
                                            <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full text-right px-8 py-5 text-sm font-black text-rose-600 hover:bg-rose-50 transition-colors flex items-center gap-3">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    حذف العملية نهائياً
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="px-4 py-1.5 bg-slate-50 rounded-xl text-xs font-black text-slate-400 uppercase border border-slate-100 shadow-sm">
                                        {{ $transaction->transactionable->name ?? 'مصدر خارجي' }}
                                    </div>
                                    @if($transaction->paymentMethod)
                                        <div class="px-4 py-1.5 bg-indigo-50 rounded-xl text-xs font-black text-indigo-400 uppercase border border-indigo-100 shadow-sm">
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
                                <input type="radio" name="type" value="income" x-model="type" class="hidden peer">
                                <div class="py-4 text-center rounded-[1.5rem] font-black text-xs peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm transition-all">إيراد / أرباح</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="expense" x-model="type" class="hidden peer">
                                <div class="py-4 text-center rounded-[1.5rem] font-black text-xs peer-checked:bg-white peer-checked:text-rose-600 peer-checked:shadow-sm transition-all">مصروف / التزام</div>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">المصدر</label>
                            <select name="source_type" class="w-full premium-input">
                                <option value="InvestmentFund">صندوق استثمار</option>
                                <option value="Business">مشروع تجاري</option>
                                <option value="Wallet">محفظة شخصية</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">تحديد المصدر</label>
                            <select name="source_id" class="w-full premium-input">
                                @foreach($funds as $f) <option value="{{ $f->id }}">{{ $f->name }}</option> @endforeach
                                @foreach($businesses as $b) <option value="{{ $b->id }}">{{ $b->name }}</option> @endforeach
                                @foreach($wallets as $w) <option value="{{ $w->id }}">{{ $w->name }}</option> @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">المبلغ</label>
                        <input type="number" step="0.01" name="amount" required class="w-full premium-input text-3xl" placeholder="0.00">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">التصنيف</label>
                            <select name="category" required class="w-full premium-input">
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
                            <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full premium-input">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">وسيلة الدفع / الحساب</label>
                        <select name="payment_method_id" class="w-full premium-input">
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
