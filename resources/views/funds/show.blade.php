<x-app-layout>
    <div class="py-12 px-6">
        <div class="max-w-7xl mx-auto space-y-12">
            
            <!-- Breadcrumbs & Header -->
            @if(session('new_partner_password'))
                <div class="bg-emerald-50 border border-emerald-100 p-8 rounded-[2rem] flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-6">
                        <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-2xl shadow-sm">🔐</div>
                        <div>
                            <h4 class="text-lg font-black text-emerald-900">تم إنشاء حساب الشريك بنجاح!</h4>
                            <p class="text-sm font-bold text-emerald-600 mt-1">البريد: {{ session('new_partner_email') }} | كلمة المرور: <span class="bg-white px-2 py-1 rounded border border-emerald-100 font-mono">{{ session('new_partner_password') }}</span></p>
                        </div>
                    </div>
                    <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest">يرجى تزويد الشريك بهذه البيانات للدخول</p>
                </div>
            @endif

            @if(session('mail_error'))
                <div class="bg-amber-50 border border-amber-100 p-6 rounded-[2rem] flex items-center gap-4 mt-6 shadow-sm">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-xl shadow-sm">⚠️</div>
                    <div>
                        <h4 class="text-sm font-black text-amber-900">تنبيه بخصوص البريد الإلكتروني</h4>
                        <p class="text-xs font-bold text-amber-600 mt-1">{{ session('mail_error') }}</p>
                    </div>
                </div>
            @endif

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">
                        <a href="{{ route('funds.index') }}" class="hover:text-indigo-600 transition-colors">صناديق الاستثمار</a>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                        <span class="text-gray-900">{{ $fund->name }}</span>
                    </nav>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-indigo-600 text-white rounded-3xl flex items-center justify-center text-3xl shadow-xl shadow-indigo-500/20">
                            {{ $fund->icon ?? '🏘️' }}
                        </div>
                        <h2 class="text-4xl font-black text-gray-900 tracking-tight">{{ $fund->name }}</h2>
                    </div>
                </div>
                
                <div class="flex items-center gap-3" x-data="{ showModal: false, showAssetModal: false, showPartnerModal: false, showAccountModal: false }">
                    <button @click="showAccountModal = true" class="bg-white border border-gray-100 text-gray-900 px-6 py-4 rounded-[2rem] text-sm font-black shadow-sm hover:bg-gray-50 transition-all">حسابات الصندوق</button>
                    <button @click="showPartnerModal = true" class="bg-white border border-gray-100 text-gray-900 px-6 py-4 rounded-[2rem] text-sm font-black shadow-sm hover:bg-gray-50 transition-all">إضافة شريك</button>
                    <button @click="showAssetModal = true" class="bg-white border border-gray-100 text-gray-900 px-6 py-4 rounded-[2rem] text-sm font-black shadow-sm hover:bg-gray-50 transition-all">إضافة أصل</button>
                    <button @click="showModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-[2rem] text-sm font-black shadow-xl shadow-indigo-500/20 transition-all hover:scale-105">إضافة عملية</button>
                    
                    <form action="{{ route('funds.destroy', $fund->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الكيان؟ لا يمكن التراجع عن هذه الخطوة وسيتم حذف كافة العمليات والبيانات المرتبطة به.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-14 h-14 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center shadow-sm hover:bg-rose-600 hover:text-white transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>

                    <!-- Fund Accounts Modal -->
                    <div x-show="showAccountModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
                        <div class="bg-white rounded-[4rem] w-full max-w-2xl p-12 shadow-2xl relative text-right overflow-y-auto max-h-[90vh]" @click.away="showAccountModal = false">
                            <h3 class="text-3xl font-black text-gray-900 mb-8">حسابات الصندوق</h3>
                            
                            <!-- Existing Accounts List -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10">
                                @foreach($fund->paymentMethods as $pm)
                                    <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100 flex items-center gap-4">
                                        <div class="text-2xl">
                                            @switch($pm->type)
                                                @case('bank') 🏛️ @break
                                                @case('cash') 💵 @break
                                                @case('credit_card') 💳 @break
                                                @default 💰
                                            @endswitch
                                        </div>
                                        <div>
                                            <p class="font-black text-gray-900">{{ $pm->name }}</p>
                                            <p class="text-xs font-bold text-indigo-600">{{ number_format($pm->balance, 0) }} {{ $pm->currency }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <hr class="border-gray-100 mb-10">

                            <h4 class="text-xl font-black text-gray-900 mb-6">إضافة حساب جديد للصندوق</h4>
                            <form action="{{ route('funds.addPaymentMethod', $fund->id) }}" method="POST" class="space-y-6">
                                @csrf
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">اسم الحساب</label>
                                    <input type="text" name="name" required class="w-full premium-input" placeholder="مثلاً: خزينة الصندوق، حساب بنكي للصندوق...">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">النوع</label>
                                        <select name="type" class="w-full premium-input">
                                            <option value="bank">حساب بنكي</option>
                                            <option value="cash">نقد / كاش</option>
                                            <option value="credit_card">بطاقة ائتمان</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">العملة</label>
                                        <select name="currency" class="w-full premium-input">
                                            <option value="USD">USD</option>
                                            <option value="TRY">TRY</option>
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
                            <form action="{{ route('funds.addPartner', $fund->id) }}" method="POST" class="space-y-6" x-data="{ type: 'contribution', isNew: false }">
                                @csrf
                                
                                <div class="grid grid-cols-2 gap-4 p-2 bg-gray-50 rounded-[2rem] border border-gray-100 shadow-inner mb-6">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="partner_source" value="existing" x-model="isNew" :value="false" class="hidden peer" checked>
                                        <div class="py-3 text-center rounded-[1.5rem] font-black text-[10px] peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-md transition-all">شريك موجود</div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="partner_source" value="new" x-model="isNew" :value="true" class="hidden peer">
                                        <div class="py-3 text-center rounded-[1.5rem] font-black text-[10px] peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-md transition-all">شريك جديد</div>
                                    </label>
                                </div>

                                <div x-show="!isNew">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">اختيار الشريك</label>
                                    <select name="partner_id" class="w-full premium-input">
                                        <option value="">-- اختر شريك --</option>
                                        @foreach(App\Models\Partner::where('user_id', auth()->id())->get() as $p)
                                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div x-show="isNew" class="space-y-4">
                                    <input type="hidden" name="is_new" :value="isNew ? 'true' : 'false'">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">اسم الشريك الجديد</label>
                                        <input type="text" name="new_partner_name" class="w-full premium-input" placeholder="الاسم الكامل">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">البريد الإلكتروني</label>
                                        <input type="email" name="new_partner_email" class="w-full premium-input" placeholder="example@mail.com">
                                        <p class="text-[10px] text-gray-400 mt-2 mr-2">سيتم إنشاء حساب له وإرسال كلمة المرور.</p>
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
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">مبلغ المساهمة ($)</label>
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
                                    <input type="text" name="name" required class="w-full premium-input" placeholder="مثلاً: سيارة توصيل، مكتب...">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">نوع الأصل</label>
                                    <select name="type" class="w-full premium-input">
                                        <option value="car">سيارة</option>
                                        <option value="furniture">أثاث</option>
                                        <option value="inventory">بضاعة / مخزون</option>
                                        <option value="other">أخرى</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">القيمة التقديرية ($)</label>
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
                            <div class="flex justify-between items-center mb-10">
                                <h3 class="text-3xl font-black text-gray-900">تسجيل عملية</h3>
                                <button @click="showModal = false" class="text-gray-400 hover:text-gray-900 transition-colors">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{ multiCurrency: false }">
                                @csrf
                                <input type="hidden" name="source_type" value="InvestmentFund">
                                <input type="hidden" name="source_id" value="{{ $fund->id }}">
                                
                                <div class="grid grid-cols-2 gap-4 p-2 bg-gray-50 rounded-[2rem] border border-gray-100 shadow-inner">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="type" value="income" class="hidden peer" checked>
                                        <div class="py-4 text-center rounded-[1.5rem] font-black text-xs peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-md transition-all">إيراد / أرباح</div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="type" value="expense" class="hidden peer">
                                        <div class="py-4 text-center rounded-[1.5rem] font-black text-xs peer-checked:bg-white peer-checked:text-rose-600 peer-checked:shadow-md transition-all">مصروف / تكلفة</div>
                                    </label>
                                </div>

                                <div class="flex items-center gap-4 mb-4">
                                    <input type="checkbox" x-model="multiCurrency" class="rounded-lg text-indigo-600 focus:ring-indigo-600">
                                    <span class="text-xs font-black text-gray-500 uppercase mr-1 text-right">دفع بغير الدولار؟</span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-show="multiCurrency">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 mr-2 text-right">العملة</label>
                                        <input type="text" name="currency" class="w-full premium-input" placeholder="مثلاً: TRY, EUR">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 mr-2 text-right">سعر الصرف</label>
                                        <input type="number" step="0.000001" name="exchange_rate" class="w-full premium-input" placeholder="1.00">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2 text-right">المبلغ</label>
                                    <input type="number" step="0.01" name="amount" required class="w-full premium-input text-3xl text-right" placeholder="0.00">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2 text-right">التصنيف</label>
                                    <select name="category" required class="w-full premium-input text-right">
                                        <option value="أرباح">أرباح</option>
                                        <option value="مصاريف رأس مال">مصاريف رأس مال (تأسيس/أصول)</option>
                                        <option value="رواتب">رواتب</option>
                                        <option value="إيجار">إيجار</option>
                                        <option value="تسويق">تسويق</option>
                                        <option value="صيانة">صيانة</option>
                                        <option value="أخرى">أخرى</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2 text-right">عنوان الحركة / التفاصيل</label>
                                    <input type="text" name="description" class="w-full premium-input text-right" placeholder="مثلاً: شراء أثاث مكتب، أرباح الربع الأول...">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2 text-right">وسيلة الدفع / الحساب</label>
                                    <select name="payment_method_id" class="w-full premium-input text-right">
                                        <option value="">-- اختر الحساب --</option>
                                        @foreach($paymentMethods as $pm)
                                            <option value="{{ $pm->id }}">{{ $pm->name }} ({{ number_format($pm->balance, 0) }} {{ $pm->currency }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2 text-right">توثيق العملية (فاتورة/إيصال)</label>
                                    <input type="file" name="invoice" class="w-full premium-input text-sm">
                                </div>

                                <input type="hidden" name="transaction_date" value="{{ date('Y-m-d') }}">
                                <button type="submit" class="w-full bg-indigo-600 text-white py-6 rounded-[2.5rem] font-black text-xl shadow-xl shadow-indigo-500/20 hover:bg-indigo-700 transition-all">تأكيد العملية</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="premium-card p-8 border-t-4 border-t-blue-500">
                    <p class="text-[10px] text-gray-400 font-black uppercase mb-4 tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                        إجمالي رأس المال المستثمر
                    </p>
                    <p class="text-3xl font-black text-gray-900">${{ number_format($fund->total_invested_capital, 0) }}</p>
                </div>
                <div class="premium-card p-8 border-t-4 border-t-indigo-500">
                    <p class="text-[10px] text-gray-400 font-black uppercase mb-4 tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                        القيمة الحالية
                    </p>
                    <p class="text-3xl font-black text-indigo-600">${{ number_format($fund->current_value, 0) }}</p>
                </div>
                <div class="premium-card p-8 border-t-4 border-t-amber-500">
                    <p class="text-[10px] text-gray-400 font-black uppercase mb-4 tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                        قيمة الأصول
                    </p>
                    <p class="text-3xl font-black text-gray-900">${{ number_format($fund->assets->sum('value'), 0) }}</p>
                </div>
                @php
                    $income = \App\Models\Transaction::where('transactionable_id', $fund->id)
                        ->where('transactionable_type', \App\Models\InvestmentFund::class)
                        ->where('type', 'income')->sum('amount');
                    $expense = \App\Models\Transaction::where('transactionable_id', $fund->id)
                        ->where('transactionable_type', \App\Models\InvestmentFund::class)
                        ->where('type', 'expense')->sum('amount');
                    $profit = $income - $expense;
                @endphp
                <div class="premium-card p-8 border-t-4 {{ $profit >= 0 ? 'border-t-emerald-500' : 'border-t-rose-500' }}">
                    <p class="text-[10px] text-gray-400 font-black uppercase mb-4 tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 {{ $profit >= 0 ? 'bg-emerald-500' : 'bg-rose-500' }} rounded-full"></span>
                        صافي الأرباح/الخسائر
                    </p>
                    <p class="text-3xl font-black {{ $profit >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $profit >= 0 ? '+' : '' }}${{ number_format($profit, 0) }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-10">
                    <!-- Assets Section -->
                    <div class="premium-card overflow-hidden">
                        <div class="px-10 py-8 border-b border-gray-50 bg-gray-50/30 flex justify-between items-center">
                            <h3 class="text-2xl font-black text-gray-900">الأصول والممتلكات</h3>
                            <span class="px-4 py-2 bg-amber-100 text-amber-700 text-[10px] font-black rounded-full uppercase tracking-widest">إجمالي الأصول: ${{ number_format($fund->assets->sum('value'), 0) }}</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-10">
                            @foreach($fund->assets as $asset)
                                <div class="bg-white p-6 rounded-[2rem] border border-gray-100 flex items-center gap-6 shadow-sm hover:shadow-md transition-all">
                                    <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner">
                                        {{ $asset->type == 'car' ? '🚗' : ($asset->type == 'furniture' ? '🪑' : '📦') }}
                                    </div>
                                    <div>
                                        <p class="font-black text-gray-900">{{ $asset->name }}</p>
                                        <p class="text-xs font-black text-amber-600">${{ number_format($asset->value, 0) }}</p>
                                    </div>
                                </div>
                            @endforeach
                            @if($fund->assets->isEmpty())
                                <p class="text-gray-400 font-bold col-span-full text-center py-4">لا توجد أصول مضافة حالياً</p>
                            @endif
                        </div>
                    </div>

                    <!-- Partners Table -->
                    <div class="premium-card overflow-hidden">
                        <div class="px-10 py-8 border-b border-gray-50 bg-gray-50/30">
                            <h3 class="text-2xl font-black text-gray-900">توزيع الحصص والشركاء</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-right">
                                <thead class="bg-indigo-50/50">
                                    <tr>
                                        <th class="px-10 py-5 text-[10px] font-black text-indigo-900 uppercase tracking-widest">الشريك</th>
                                        <th class="px-10 py-5 text-[10px] font-black text-indigo-900 uppercase tracking-widest">نوع الحصة</th>
                                        <th class="px-10 py-5 text-[10px] font-black text-indigo-900 uppercase tracking-widest">المساهمة</th>
                                        <th class="px-10 py-5 text-[10px] font-black text-indigo-900 uppercase tracking-widest">النسبة</th>
                                        <th class="px-10 py-5 text-[10px] font-black text-indigo-900 uppercase tracking-widest">القيمة الحالية</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($equities as $equity)
                                        <tr class="hover:bg-gray-50/30 transition-colors group">
                                            <td class="px-10 py-6">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center font-black">
                                                        {{ mb_substr($equity->partner->name, 0, 1) }}
                                                    </div>
                                                    <span class="font-black text-gray-900">{{ $equity->partner->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-10 py-6">
                                                <span class="px-3 py-1 {{ $equity->equity_type == 'fixed' ? 'bg-purple-50 text-purple-600' : 'bg-blue-50 text-blue-600' }} rounded-lg text-[10px] font-black">
                                                    {{ $equity->equity_type == 'fixed' ? 'نسبة ثابتة' : 'مساهمة مالية' }}
                                                </span>
                                            </td>
                                            <td class="px-10 py-6 font-bold text-gray-600">${{ number_format($equity->amount, 0) }}</td>
                                            <td class="px-10 py-6">
                                                <div class="flex items-center gap-3">
                                                <p class="font-black text-gray-900">${{ number_format(($equity->percentage / 100) * $fund->current_value, 0) }}</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sidebar (Activity) -->
                <div class="space-y-10">
                    <div class="premium-card p-10 relative overflow-hidden">
                        <div class="absolute -right-20 -bottom-20 w-40 h-40 bg-emerald-500/5 rounded-full blur-3xl"></div>
                        <h3 class="text-2xl font-black text-gray-900 mb-8 relative z-10">العمليات الأخيرة</h3>
                        <div class="space-y-8 relative z-10">
                            @forelse($transactions as $transaction)
                                <div class="flex items-center justify-between group cursor-pointer">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 {{ $transaction->type == 'income' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} rounded-[1.2rem] flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                                            {{ $transaction->type == 'income' ? '📈' : '📉' }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-gray-900 flex items-center gap-2">
                                                {{ $transaction->description ?: $transaction->category }}
                                                @if($transaction->invoice_path)
                                                    <a href="{{ asset('storage/' . $transaction->invoice_path) }}" target="_blank" class="w-5 h-5 bg-indigo-50 text-indigo-600 rounded flex items-center justify-center text-[8px]">📄</a>
                                                @endif
                                            </p>
                                            <div class="flex items-center gap-2">
                                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $transaction->transaction_date->format('Y/m/d') }}</p>
                                                @if($transaction->currency !== 'USD')
                                                    <span class="text-[10px] font-black text-indigo-400 bg-indigo-50 px-1.5 py-0.5 rounded-md">{{ $transaction->currency }} ({{ $transaction->exchange_rate }})</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-sm font-black {{ $transaction->type == 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $transaction->type == 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 0) }}
                                    </p>
                                </div>
                            @empty
                                <div class="text-center py-10">
                                    <div class="text-4xl mb-4">🌫️</div>
                                    <p class="text-gray-400 font-bold">لا توجد عمليات مسجلة</p>
                                </div>
                            @endforelse
                        </div>
                        <a href="{{ route('transactions.index', ['source_type' => 'InvestmentFund', 'source_id' => $fund->id]) }}" class="block w-full mt-10 py-4 bg-gray-50 rounded-2xl text-[10px] font-black text-gray-400 uppercase tracking-widest text-center hover:bg-gray-100 transition-colors">عرض كافة العمليات</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var options = {
            series: [{
                name: 'قيمة الصندوق',
                data: [{{ $fund->capital }}, {{ ($fund->capital + $fund->current_value) / 2 }}, {{ $fund->current_value }}]
            }],
            chart: {
                type: 'area',
                height: 320,
                toolbar: { show: false },
                fontFamily: 'Noto Sans Arabic, sans-serif',
            },
            colors: ['#4f46e5'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 4 },
            xaxis: {
                categories: ['التأسيس', 'نصف المدة', 'الحالي'],
                labels: { style: { colors: '#9ca3af', fontWeight: 900, fontSize: '10px' } }
            },
            yaxis: { show: false },
            grid: { borderColor: '#f9fafb' },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.3,
                    opacityTo: 0.05,
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#fundChart"), options);
        chart.render();
    });
</script>
