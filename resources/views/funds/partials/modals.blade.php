<!-- Account/Payment Method Modal -->
<div x-show="showAccountModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl text-right" @click.away="showAccountModal = false">
        <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-black text-gray-900">إضافة حساب للصندوق</h3>
            <button @click="showAccountModal = false" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-500 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('funds.addPaymentMethod', $fund->id) }}" method="POST" class="p-8 space-y-5">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">اسم الحساب</label>
                <input type="text" name="name" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="مثلاً: بنك بيمو، خزينة المكتب...">
            </div>
            
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">تابع لحساب (اختياري)</label>
                <select name="parent_id" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="">-- حساب رئيسي جديد --</option>
                    @foreach($allPaymentMethods->whereNull('parent_id') as $rootPm)
                        <option value="{{ $rootPm->id }}">{{ $rootPm->name }}</option>
                    @endforeach
                </select>
                <p class="text-[9px] font-bold text-gray-400 mt-2 pr-1">* اختر حساباً رئيسياً إذا كنت تريد إضافة عملة فرعية له</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">النوع</label>
                    <select name="type" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="bank">حساب بنكي</option>
                        <option value="cash">نقد / كاش</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">العملة</label>
                    <select name="currency" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="USD">USD - دولار</option>
                        <option value="TRY">TRY - ليرة تركية</option>
                        <option value="SYP">SYP - ليرة سورية</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">الرصيد الافتتاحي</label>
                <input type="number" name="balance" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-xl focus:ring-2 focus:ring-indigo-500 outline-none text-center" placeholder="0.00">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-black text-base shadow-lg shadow-indigo-500/20 transition-all hover:scale-105">حفظ الحساب</button>
        </form>
    </div>
</div>

<!-- Partner Modal -->
<div x-show="showPartnerModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl text-right" @click.away="showPartnerModal = false">
        <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-black text-gray-900">إضافة شريك للصندوق</h3>
            <button @click="showPartnerModal = false" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-500 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('funds.addPartner', $fund->id) }}" method="POST" class="p-8 space-y-5">
            @csrf
            
            <div class="grid grid-cols-2 gap-2 p-1.5 bg-gray-100 rounded-2xl border border-gray-200">
                <label class="cursor-pointer">
                    <input type="radio" name="partner_source" value="existing" x-model="partnerSource" class="sr-only peer">
                    <div class="py-2.5 text-center rounded-xl font-black text-[10px] peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm transition-all text-slate-500">شريك موجود</div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="partner_source" value="new" x-model="partnerSource" class="sr-only peer">
                    <div class="py-2.5 text-center rounded-xl font-black text-[10px] peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm transition-all text-slate-500">شريك جديد</div>
                </label>
            </div>

            <div x-show="partnerSource === 'existing'">
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">اختيار الشريك</label>
                <select name="partner_id" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="">-- اختر شريك --</option>
                    @foreach(App\Models\Partner::where('user_id', auth()->id())->get() as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div x-show="partnerSource === 'new'" class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">اسم الشريك الجديد</label>
                    <input type="text" name="new_partner_name" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="الاسم الكامل">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">البريد الإلكتروني</label>
                    <input type="email" name="new_partner_email" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="example@mail.com">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">نوع الحصة</label>
                <select name="equity_type" x-model="partnerEquityType" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="contribution">بناءً على مبلغ مساهمة (رأس مال)</option>
                    <option value="fixed">نسبة مئوية ثابتة</option>
                </select>
            </div>
            <div x-show="partnerEquityType === 'contribution'">
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">مبلغ المساهمة</label>
                <input type="number" name="amount" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-xl focus:ring-2 focus:ring-indigo-500 outline-none text-center">
            </div>
            <div x-show="partnerEquityType === 'fixed'">
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">النسبة المئوية (%)</label>
                <input type="number" name="percentage" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-xl focus:ring-2 focus:ring-indigo-500 outline-none text-center" placeholder="مثلاً: 25">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-black text-base shadow-lg shadow-indigo-500/20 transition-all hover:scale-105">تأكيد الإضافة</button>
        </form>
    </div>
</div>

<!-- Asset Modal -->
<div x-show="showAssetModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl text-right" @click.away="showAssetModal = false">
        <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-black text-gray-900">إضافة أصل للصندوق</h3>
            <button @click="showAssetModal = false" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-500 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('funds.addAsset', $fund->id) }}" method="POST" class="p-8 space-y-5">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">اسم الأصل</label>
                <input type="text" name="name" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="مثلاً: سيارة، مكتب...">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">النوع</label>
                <select name="type" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="car">سيارة</option>
                    <option value="furniture">أثاث</option>
                    <option value="inventory">بضاعة</option>
                    <option value="other">أخرى</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">القيمة التقديرية</label>
                <input type="number" name="value" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-xl focus:ring-2 focus:ring-indigo-500 outline-none text-center">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">تاريخ الشراء</label>
                <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-black text-base shadow-lg shadow-indigo-500/20 transition-all hover:scale-105">حفظ الأصل</button>
        </form>
    </div>
</div>

<!-- Transaction Modal -->
<div x-show="showModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl text-right overflow-y-auto max-h-[90vh]" @click.away="showModal = false">
        <div class="sticky top-0 bg-white border-b border-slate-100 px-8 py-5 flex items-center justify-between rounded-t-3xl z-10">
            <h3 class="text-lg font-black text-gray-900">تسجيل عملية جديدة</h3>
            <button @click="showModal = false" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-500 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-5" x-data="{ 
            txType: 'expense', 
            categories: {{ \App\Models\Category::where('is_default', true)->orWhere('user_id', auth()->id())->get()->toJson() }} 
        }">
            @csrf
            <input type="hidden" name="source_type" value="InvestmentFund">
            <input type="hidden" name="source_id" value="{{ $fund->id }}">
            
            <div class="grid grid-cols-3 gap-2 p-1.5 bg-gray-100 rounded-2xl border border-gray-200">
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="income" x-model="txType" class="sr-only peer">
                    <div class="py-2.5 text-center rounded-xl font-black text-[10px] peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm transition-all text-slate-500">إيراد</div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="expense" x-model="txType" class="sr-only peer">
                    <div class="py-2.5 text-center rounded-xl font-black text-[10px] peer-checked:bg-white peer-checked:text-rose-600 peer-checked:shadow-sm transition-all text-slate-500">مصروف</div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="capital" x-model="txType" class="sr-only peer">
                    <div class="py-2.5 text-center rounded-xl font-black text-[10px] peer-checked:bg-white peer-checked:text-amber-600 peer-checked:shadow-sm transition-all text-slate-500">رأس مال</div>
                </label>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">المبلغ</label>
                <input type="number" step="0.01" name="amount" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-center" placeholder="0.00">
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">التصنيف</label>
                <select name="category_id" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    <template x-for="cat in categories.filter(c => c.type === txType)" :key="cat.id">
                        <option :value="cat.id" x-text="cat.icon + ' ' + cat.name"></option>
                    </template>
                    <template x-if="txType === 'capital' && !categories.some(c => c.type === 'capital')">
                        <option value="" selected>🏢 رأس مال مساهم</option>
                    </template>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">الحساب المستخدم</label>
                <select name="payment_method_id" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    @foreach($allPaymentMethods as $pm)
                        <option value="{{ $pm->id }}">{{ $pm->parent ? $pm->parent->name . ' - ' : '' }}{{ $pm->name }} ({{ number_format($pm->balance, 0) }} {{ $pm->currency }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">العنوان / البيان</label>
                <input type="text" name="description" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="وصف موجز للعملية...">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">التاريخ</label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">المرفقات</label>
                    <input type="file" name="invoice" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-xs focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-black text-base shadow-lg shadow-indigo-500/20 transition-all hover:scale-105">تأكيد العملية</button>
        </form>
    </div>
</div>

<!-- Transfer Modal -->
<div x-show="showTransferModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl text-right" @click.away="showTransferModal = false">
        <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-black text-gray-900">تحويل داخلي</h3>
            <button @click="showTransferModal = false" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-500 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('transactions.transfer') }}" method="POST" class="p-8 space-y-5">
            @csrf
            <input type="hidden" name="source_type" value="InvestmentFund">
            <input type="hidden" name="source_id" value="{{ $fund->id }}">

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">من حساب</label>
                <select name="from_payment_method_id" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    @foreach($allPaymentMethods as $pm)
                        <option value="{{ $pm->id }}">{{ $pm->parent ? $pm->parent->name . ' - ' : '' }}{{ $pm->name }} ({{ number_format($pm->balance, 0) }} {{ $pm->currency }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">إلى حساب</label>
                <select name="to_payment_method_id" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    @foreach($allPaymentMethods as $pm)
                        <option value="{{ $pm->id }}">{{ $pm->parent ? $pm->parent->name . ' - ' : '' }}{{ $pm->name }} ({{ number_format($pm->balance, 0) }} {{ $pm->currency }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">المبلغ</label>
                <input type="number" step="0.01" name="amount" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-center" placeholder="0.00">
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">التاريخ</label>
                <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>

            <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white py-4 rounded-2xl font-black text-base shadow-lg shadow-amber-500/20 transition-all hover:scale-105">تأكيد التحويل</button>
        </form>
    </div>
</div>

<!-- Reconcile Modal -->
<div x-show="reconcilingId" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl text-right" @click.away="reconcilingId = null">
        <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-black text-gray-900">مطابقة حساب: <span x-text="reconcilingName"></span></h3>
            <button @click="reconcilingId = null" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-500 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form :action="'{{ route('funds.reconcileAccount', [$fund->id, 'ID']) }}'.replace('ID', reconcilingId)" method="POST" class="p-8 space-y-5">
            @csrf
            <p class="text-xs font-bold text-gray-400 leading-relaxed pr-1">أدخل الرصيد الفعلي المتوفر حالياً في هذا الحساب. سيقوم النظام بإنشاء عملية تسوية تلقائية بالفرق.</p>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">الرصيد الفعلي الحالي</label>
                <input type="number" step="0.01" name="actual_balance" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-center" :placeholder="reconcilingBalance">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-black text-base shadow-lg shadow-indigo-500/20 transition-all hover:scale-105">تأكيد المطابقة</button>
        </form>
    </div>
</div>

<!-- Edit Equity Modal -->
<div x-show="editingEquity" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl text-right" @click.away="editingEquity = null">
        <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-black text-gray-900">تعديل حصة الشريك</h3>
            <button @click="editingEquity = null" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-500 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form :action="'{{ route('funds.updateEquity', 'ID') }}'.replace('ID', editingEquity)" method="POST" class="p-8 space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">المبلغ (رأس المال)</label>
                <input type="number" name="amount" x-model="editingEquityAmount" :readonly="editingEquityType === 'fixed'" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-lg focus:ring-2 focus:ring-indigo-500 outline-none text-center" :class="editingEquityType === 'fixed' ? 'opacity-60 cursor-not-allowed' : ''">
                <template x-if="editingEquityType === 'fixed'">
                    <p class="text-[9px] font-bold text-amber-600 mt-1 pr-1">* الشريك ذو النسبة الثابتة لا يملك مساهمة رأس مالية مباشرة.</p>
                </template>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">النسبة المئوية (%)</label>
                <input type="number" step="0.1" name="percentage" x-model="editingEquityPercentage" :readonly="editingEquityType === 'contribution'" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-lg focus:ring-2 focus:ring-indigo-500 outline-none text-center" :class="editingEquityType === 'contribution' ? 'opacity-60 cursor-not-allowed' : ''">
                <template x-if="editingEquityType === 'contribution'">
                    <p class="text-[9px] font-bold text-indigo-600 mt-1 pr-1">* يتم احتساب النسبة تلقائياً بناءً على قيمة المساهمة المتبقية.</p>
                </template>
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-black text-base transition-all hover:scale-105">تحديث البيانات</button>
        </form>
    </div>
</div>
