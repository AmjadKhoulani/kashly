<!-- Account/Payment Method Modal -->
<div x-show="showAccountModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
    <div class="bg-white rounded-[4rem] w-full max-w-lg p-12 shadow-2xl relative text-right" @click.away="showAccountModal = false">
        <h3 class="text-3xl font-black text-gray-900 mb-8">إضافة حساب للصندوق</h3>
        <form action="{{ route('funds.addPaymentMethod', $fund->id) }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">اسم الحساب</label>
                <input type="text" name="name" required class="w-full premium-input" placeholder="مثلاً: خزينة الصندوق، حساب بنكي...">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">النوع</label>
                    <select name="type" class="w-full premium-input">
                        <option value="bank">حساب بنكي</option>
                        <option value="cash">نقد / كاش</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">العملة</label>
                    <select name="currency" class="w-full premium-input">
                        <option value="USD">USD</option>
                        <option value="TRY">TRY</option>
                        <option value="SYP">SYP</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">الرصيد الافتتاحي</label>
                <input type="number" name="balance" required class="w-full premium-input text-2xl" placeholder="0.00">
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white py-6 rounded-[2.5rem] font-black text-xl shadow-xl shadow-indigo-500/20">حفظ الحساب</button>
        </form>
    </div>
</div>

<!-- Partner Modal -->
<div x-show="showPartnerModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
    <div class="bg-white rounded-[4rem] w-full max-w-lg p-12 shadow-2xl relative text-right" @click.away="showPartnerModal = false">
        <h3 class="text-3xl font-black text-gray-900 mb-8">إضافة شريك للصندوق</h3>
        <form action="{{ route('funds.addPartner', $fund->id) }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-2 gap-4 p-2 bg-gray-50 rounded-[2rem] border border-gray-100 mb-6">
                <label class="cursor-pointer">
                    <input type="radio" name="partner_source" value="existing" x-model="partnerSource" class="hidden peer">
                    <div class="py-3 text-center rounded-[1.5rem] font-black text-[10px] peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-md transition-all">شريك موجود</div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="partner_source" value="new" x-model="partnerSource" class="hidden peer">
                    <div class="py-3 text-center rounded-[1.5rem] font-black text-[10px] peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-md transition-all">شريك جديد</div>
                </label>
            </div>

            <div x-show="partnerSource === 'existing'">
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">اختيار الشريك</label>
                <select name="partner_id" class="w-full premium-input">
                    <option value="">-- اختر شريك --</option>
                    @foreach(App\Models\Partner::where('user_id', auth()->id())->get() as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div x-show="partnerSource === 'new'" class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">اسم الشريك الجديد</label>
                    <input type="text" name="new_partner_name" class="w-full premium-input" placeholder="الاسم الكامل">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">البريد الإلكتروني</label>
                    <input type="email" name="new_partner_email" class="w-full premium-input" placeholder="example@mail.com">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">نوع الحصة</label>
                <select name="equity_type" x-model="type" class="w-full premium-input">
                    <option value="contribution">بناءً على مبلغ مساهمة (رأس مال)</option>
                    <option value="fixed">نسبة مئوية ثابتة</option>
                </select>
            </div>
            <div x-show="type === 'contribution'">
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">مبلغ المساهمة</label>
                <input type="number" name="amount" class="w-full premium-input text-2xl">
            </div>
            <div x-show="type === 'fixed'">
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">النسبة المئوية (%)</label>
                <input type="number" name="percentage" class="w-full premium-input text-2xl" placeholder="مثلاً: 25">
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white py-6 rounded-[2.5rem] font-black text-xl shadow-xl shadow-indigo-500/20">تأكيد الإضافة</button>
        </form>
    </div>
</div>

<!-- Asset Modal -->
<div x-show="showAssetModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
    <div class="bg-white rounded-[4rem] w-full max-w-lg p-12 shadow-2xl relative text-right" @click.away="showAssetModal = false">
        <h3 class="text-3xl font-black text-gray-900 mb-8">إضافة أصل للصندوق</h3>
        <form action="{{ route('funds.addAsset', $fund->id) }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">اسم الأصل</label>
                <input type="text" name="name" required class="w-full premium-input" placeholder="مثلاً: سيارة، مكتب...">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">النوع</label>
                <select name="type" class="w-full premium-input">
                    <option value="car">سيارة</option>
                    <option value="furniture">أثاث</option>
                    <option value="inventory">بضاعة</option>
                    <option value="other">أخرى</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">القيمة التقديرية</label>
                <input type="number" name="value" required class="w-full premium-input text-2xl">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">تاريخ الشراء</label>
                <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" required class="w-full premium-input">
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white py-6 rounded-[2.5rem] font-black text-xl shadow-xl shadow-indigo-500/20">حفظ الأصل</button>
        </form>
    </div>
</div>

<!-- Transaction Modal -->
<div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
    <div class="bg-white rounded-[4rem] w-full max-w-lg p-12 shadow-2xl relative text-right overflow-y-auto max-h-[90vh]" @click.away="showModal = false">
        <h3 class="text-3xl font-black text-gray-900 mb-10 text-center">تسجيل عملية جديدة</h3>
        <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{ multiCurrency: false, txType: 'expense' }">
            @csrf
            <input type="hidden" name="source_type" value="InvestmentFund">
            <input type="hidden" name="source_id" value="{{ $fund->id }}">
            
            <div class="grid grid-cols-2 gap-4 p-2 bg-gray-50 rounded-[2rem] border border-gray-100 shadow-inner">
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="income" x-model="txType" class="hidden peer">
                    <div class="py-4 text-center rounded-[1.5rem] font-black text-xs peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-md transition-all">إيراد / أرباح</div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="expense" x-model="txType" class="hidden peer">
                    <div class="py-4 text-center rounded-[1.5rem] font-black text-xs peer-checked:bg-white peer-checked:text-rose-600 peer-checked:shadow-md transition-all">مصروف / تكلفة</div>
                </label>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">المبلغ</label>
                <input type="number" step="0.01" name="amount" required class="w-full premium-input text-3xl text-center" placeholder="0.00">
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">التصنيف</label>
                <select name="category" required class="w-full premium-input">
                    <template x-if="txType == 'income'">
                        <optgroup label="تصنيفات الأرباح">
                            <option value="أرباح">أرباح</option>
                            <option value="إيداع">إيداع</option>
                            <option value="أخرى">أخرى</option>
                        </optgroup>
                    </template>
                    <template x-if="txType == 'expense'">
                        <optgroup label="تصنيفات المصاريف">
                            <option value="مصاريف تشغيل">مصاريف تشغيل</option>
                            <option value="رواتب">رواتب</option>
                            <option value="إيجار">إيجار</option>
                            <option value="أخرى">أخرى</option>
                        </optgroup>
                    </template>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">الحساب المستخدم</label>
                <select name="payment_method_id" required class="w-full premium-input">
                    @foreach($paymentMethods as $pm)
                        <option value="{{ $pm->id }}">{{ $pm->name }} ({{ number_format($pm->balance, 0) }} {{ $pm->currency }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">العنوان / البيان</label>
                <input type="text" name="description" class="w-full premium-input" placeholder="وصف موجز للعملية...">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">التاريخ</label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full premium-input">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">المرفقات</label>
                    <input type="file" name="invoice" class="w-full premium-input text-[10px]">
                </div>
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white py-6 rounded-[2.5rem] font-black text-xl shadow-xl shadow-indigo-500/20">تأكيد العملية</button>
        </form>
    </div>
</div>

<!-- Transfer Modal -->
<div x-show="showTransferModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
    <div class="bg-white rounded-[4rem] w-full max-w-lg p-12 shadow-2xl relative text-right" @click.away="showTransferModal = false">
        <h3 class="text-3xl font-black text-gray-900 mb-10 text-center">تحويل داخلي</h3>
        <form action="{{ route('transactions.transfer') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="source_type" value="InvestmentFund">
            <input type="hidden" name="source_id" value="{{ $fund->id }}">

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">من حساب</label>
                <select name="from_payment_method_id" required class="w-full premium-input">
                    @foreach($paymentMethods as $pm)
                        <option value="{{ $pm->id }}">{{ $pm->name }} ({{ number_format($pm->balance, 0) }} {{ $pm->currency }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">إلى حساب</label>
                <select name="to_payment_method_id" required class="w-full premium-input">
                    @foreach($paymentMethods as $pm)
                        <option value="{{ $pm->id }}">{{ $pm->name }} ({{ number_format($pm->balance, 0) }} {{ $pm->currency }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">المبلغ</label>
                <input type="number" step="0.01" name="amount" required class="w-full premium-input text-3xl text-center" placeholder="0.00">
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">التاريخ</label>
                <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full premium-input">
            </div>

            <button type="submit" class="w-full bg-amber-500 text-white py-6 rounded-[2.5rem] font-black text-xl shadow-xl shadow-amber-500/20">تأكيد التحويل</button>
        </form>
    </div>
</div>

<!-- Reconcile Modal -->
<div x-show="reconcilingId" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
    <div class="bg-white rounded-[4rem] w-full max-w-lg p-12 shadow-2xl relative text-right" @click.away="reconcilingId = null">
        <h3 class="text-2xl font-black text-gray-900 mb-6">مطابقة حساب: <span x-text="reconcilingName"></span></h3>
        <p class="text-xs font-bold text-gray-400 mb-8 leading-relaxed">أدخل الرصيد الفعلي المتوفر حالياً في هذا الحساب. سيقوم النظام بإنشاء عملية تسوية تلقائية بالفرق.</p>
        <form :action="'{{ route('funds.reconcileAccount', [$fund->id, 'ID']) }}'.replace('ID', reconcilingId)" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">الرصيد الفعلي الحالي</label>
                <input type="number" step="0.01" name="actual_balance" required class="w-full premium-input text-3xl text-center" :placeholder="reconcilingBalance">
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white py-6 rounded-[2.5rem] font-black text-xl shadow-xl shadow-indigo-500/20">تأكيد المطابقة</button>
        </form>
    </div>
</div>

<!-- Edit Equity Modal -->
<div x-show="editingEquity" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
    <div class="bg-white rounded-[4rem] w-full max-w-lg p-12 shadow-2xl relative text-right" @click.away="editingEquity = null">
        <h3 class="text-2xl font-black text-gray-900 mb-8 text-center">تعديل حصة الشريك</h3>
        <form :action="'{{ route('funds.updateEquity', 'ID') }}'.replace('ID', editingEquity)" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2 text-right">المبلغ (رأس المال)</label>
                <input type="number" name="amount" class="w-full premium-input">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2 text-right">النسبة المئوية (%)</label>
                <input type="number" step="0.1" name="percentage" class="w-full premium-input">
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white py-6 rounded-[2.5rem] font-black text-xl">تحديث البيانات</button>
        </form>
    </div>
</div>
