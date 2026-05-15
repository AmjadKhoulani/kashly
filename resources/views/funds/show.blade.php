<x-app-layout>
    <div class="py-12 px-6" x-data="{ 
        editingEquity: null, 
        showModal: false, 
        showAssetModal: false, 
        showPartnerModal: false, 
        showAccountModal: false,
        showTransferModal: false,
        reconcilingId: null, 
        reconcilingName: '', 
        reconcilingBalance: 0
    }">
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
                
                <div class="flex flex-wrap items-center gap-3">
                    <button @click="showAccountModal = true" class="bg-white border border-gray-100 text-gray-900 px-6 py-4 rounded-[2rem] text-sm font-black shadow-sm hover:bg-gray-50 transition-all">حسابات الصندوق</button>
                    <button @click="showPartnerModal = true" class="bg-white border border-gray-100 text-gray-900 px-6 py-4 rounded-[2rem] text-sm font-black shadow-sm hover:bg-gray-50 transition-all">إضافة شريك</button>
                    <button @click="showAssetModal = true" class="bg-white border border-gray-100 text-gray-900 px-6 py-4 rounded-[2rem] text-sm font-black shadow-sm hover:bg-gray-50 transition-all">إضافة أصل</button>
                    <a href="{{ route('funds.distributions', $fund->id) }}" class="bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-4 rounded-[2rem] text-sm font-black shadow-xl shadow-emerald-500/20 transition-all hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        توزيع أرباح
                    </a>
                    <button @click="showTransferModal = true" class="bg-amber-100 hover:bg-amber-200 text-amber-700 px-8 py-4 rounded-[2rem] text-sm font-black transition-all flex items-center gap-2 shadow-sm border border-amber-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        تحويل داخلي
                    </button>
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
                    <div x-show="showPartnerModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition x-init="@if($errors->has('new_partner_email') || $errors->has('new_partner_name') || $errors->has('partner_id')) showPartnerModal = true @endif">
                        <div class="bg-white rounded-[4rem] w-full max-w-lg p-12 shadow-2xl relative text-right" @click.away="showPartnerModal = false">
                            <h3 class="text-3xl font-black text-gray-900 mb-8">إضافة شريك للصندوق</h3>
                            
                            @if ($errors->any())
                                <div class="bg-rose-50 border border-rose-100 text-rose-600 p-6 rounded-3xl mb-8 text-sm font-bold">
                                    <ul class="list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('funds.addPartner', $fund->id) }}" method="POST" class="space-y-6" x-data="{ type: 'contribution', partnerSource: '{{ old('partner_source', 'existing') }}' }">
                                @csrf
                                
                                <div class="grid grid-cols-2 gap-4 p-2 bg-gray-50 rounded-[2rem] border border-gray-100 shadow-inner mb-6">
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
                                        @foreach(App\Models\Partner::where('user_id', auth()->id())->where(function($q) { $q->whereNull('linked_user_id')->orWhere('linked_user_id', '!=', auth()->id()); })->get() as $p)
                                            <option value="{{ $p->id }}" {{ old('partner_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div x-show="partnerSource === 'new'" class="space-y-4">
                                    <input type="hidden" name="is_new" :value="partnerSource === 'new' ? 'true' : 'false'">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">اسم الشريك الجديد</label>
                                        <input type="text" name="new_partner_name" value="{{ old('new_partner_name') }}" class="w-full premium-input" placeholder="الاسم الكامل">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">البريد الإلكتروني</label>
                                        <input type="email" name="new_partner_email" value="{{ old('new_partner_email') }}" class="w-full premium-input" placeholder="example@mail.com">
                                        <p class="text-[10px] text-gray-400 mt-2 mr-2">سيتم إنشاء حساب له وإرسال كلمة المرور.</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">نوع الحصة</label>
                                    <select name="equity_type" x-model="type" class="w-full premium-input">
                                        <option value="contribution" {{ old('equity_type') == 'contribution' ? 'selected' : '' }}>بناءً على مبلغ مساهمة (رأس مال)</option>
                                        <option value="fixed" {{ old('equity_type') == 'fixed' ? 'selected' : '' }}>نسبة مئوية ثابتة</option>
                                    </select>
                                </div>
                                <div x-show="type === 'contribution'">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">مبلغ المساهمة ($)</label>
                                    <input type="number" name="amount" value="{{ old('amount') }}" class="w-full premium-input text-2xl">
                                </div>
                                <div x-show="type === 'fixed'">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">الالنسبة المئوية (%)</label>
                                    <input type="number" name="percentage" value="{{ old('percentage') }}" class="w-full premium-input text-2xl" placeholder="مثلاً: 25">
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

                            <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{ multiCurrency: false, type: 'income' }">
                                @csrf
                                <input type="hidden" name="source_type" value="InvestmentFund">
                                <input type="hidden" name="source_id" value="{{ $fund->id }}">
                                
                                <div class="grid grid-cols-2 gap-4 p-2 bg-gray-50 rounded-[2rem] border border-gray-100 shadow-inner">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="type" value="income" x-model="type" class="hidden peer">
                                        <div class="py-4 text-center rounded-[1.5rem] font-black text-xs peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-md transition-all">إيراد / أرباح</div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="type" value="expense" x-model="type" class="hidden peer">
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
                                        <template x-if="type == 'income'">
                                            <optgroup label="تصنيفات الأرباح">
                                                <option value="أرباح">أرباح</option>
                                                <option value="إيداع">إيداع</option>
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
                                                <option value="أخرى">أخرى</option>
                                            </optgroup>
                                        </template>
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

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2 text-right">تاريخ العملية</label>
                                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full premium-input text-right">
                                </div>
                                <button type="submit" class="w-full bg-indigo-600 text-white py-6 rounded-[2.5rem] font-black text-xl shadow-xl shadow-indigo-500/20 hover:bg-indigo-700 transition-all">تأكيد العملية</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transfer Modal -->
            <div x-show="showTransferModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
                <div class="bg-white rounded-[4rem] w-full max-w-lg p-12 shadow-2xl relative text-right overflow-y-auto max-h-[90vh]" @click.away="showTransferModal = false">
                    <div class="flex justify-between items-center mb-10">
                        <h3 class="text-3xl font-black text-gray-900">تحويل بين الحسابات</h3>
                        <button @click="showTransferModal = false" class="text-gray-400 hover:text-gray-900 transition-colors">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form action="{{ route('transactions.transfer') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="source_type" value="InvestmentFund">
                        <input type="hidden" name="source_id" value="{{ $fund->id }}">

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2 text-right">من حساب</label>
                            <select name="from_payment_method_id" required class="w-full premium-input text-right">
                                <option value="">-- اختر الحساب المرسل --</option>
                                @foreach($paymentMethods as $pm)
                                    <option value="{{ $pm->id }}">{{ $pm->name }} ({{ number_format($pm->balance, 0) }} {{ $pm->currency }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex justify-center -my-3 relative z-10">
                            <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center border-4 border-white shadow-md">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2 text-right">إلى حساب</label>
                            <select name="to_payment_method_id" required class="w-full premium-input text-right">
                                <option value="">-- اختر الحساب المستلم --</option>
                                @foreach($paymentMethods as $pm)
                                    <option value="{{ $pm->id }}">{{ $pm->name }} ({{ number_format($pm->balance, 0) }} {{ $pm->currency }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2 text-right">المبلغ</label>
                            <input type="number" step="0.01" name="amount" required class="w-full premium-input text-3xl text-right" placeholder="0.00">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2 text-right">تاريخ التحويل</label>
                            <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full premium-input text-right">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2 text-right">ملاحظات التحويل</label>
                            <input type="text" name="description" class="w-full premium-input text-right" placeholder="مثلاً: تغذية صندوق النثرية، تحويل للبنك...">
                        </div>

                        <button type="submit" class="w-full bg-amber-500 text-white py-6 rounded-[2.5rem] font-black text-xl shadow-xl shadow-amber-500/20 hover:bg-amber-600 transition-all">تأكيد التحويل</button>
                    </form>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="premium-card p-8 border border-gray-200 border-t-4 border-t-blue-500 shadow-sm">
                    <p class="text-[10px] text-gray-500 font-black uppercase mb-4 tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                        إجمالي رأس المال المستثمر
                    </p>
                    <p class="text-3xl font-black text-gray-900">{{ number_format($fund->total_invested_capital, 0) }} <span class="text-xs">{{ $fund->currency }}</span></p>
                </div>
                <div class="premium-card p-8 border border-gray-200 border-t-4 border-t-indigo-600 shadow-sm">
                    <p class="text-[10px] text-gray-500 font-black uppercase mb-4 tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                        القيمة الحالية
                    </p>
                    <p class="text-3xl font-black text-indigo-700">{{ number_format($fund->current_value, 0) }} <span class="text-xs">{{ $fund->currency }}</span></p>
                </div>
                <div class="premium-card p-8 border border-gray-200 border-t-4 border-t-amber-500 shadow-sm">
                    <p class="text-[10px] text-gray-500 font-black uppercase mb-4 tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                        قيمة الأصول
                    </p>
                    <p class="text-3xl font-black text-gray-900">{{ number_format($fund->assets->sum('value'), 0) }} <span class="text-xs">{{ $fund->currency }}</span></p>
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
                <div class="premium-card p-8 border border-gray-200 border-t-4 {{ $profit >= 0 ? 'border-t-emerald-500' : 'border-t-rose-500' }} shadow-sm">
                    <p class="text-[10px] text-gray-500 font-black uppercase mb-4 tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 {{ $profit >= 0 ? 'bg-emerald-500' : 'bg-rose-500' }} rounded-full"></span>
                        صافي الأرباح/الخسائر
                    </p>
                    <p class="text-3xl font-black {{ $profit >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ $profit >= 0 ? '+' : '' }}{{ number_format($profit, 0) }} <span class="text-xs">{{ $fund->currency }}</span>
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
                            <span class="px-4 py-2 bg-amber-100 text-amber-800 text-[10px] font-black rounded-full uppercase tracking-widest border border-amber-200">إجمالي الأصول: {{ number_format($fund->assets->sum('value'), 0) }} {{ $fund->currency }}</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-10">
                            @foreach($fund->assets as $asset)
                                <div class="bg-white p-6 rounded-[2rem] border border-gray-100 flex items-center gap-6 shadow-sm hover:shadow-md transition-all">
                                    <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner">
                                        {{ $asset->type == 'car' ? '🚗' : ($asset->type == 'furniture' ? '🪑' : '📦') }}
                                    </div>
                                    <div>
                                        <p class="font-black text-gray-900">{{ $asset->name }}</p>
                                        <p class="text-xs font-black text-amber-600">{{ number_format($asset->value, 0) }} {{ $fund->currency }}</p>
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
                                <thead class="bg-gray-50/80">
                                    <tr>
                                        <th class="px-10 py-6 text-right">
                                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">الشريك</span>
                                            <span class="block text-[8px] font-bold text-gray-300 mt-0.5">الاسم المسجل للنظام</span>
                                        </th>
                                        <th class="px-6 py-6 text-center">
                                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">نوع الحصة</span>
                                            <span class="block text-[8px] font-bold text-gray-300 mt-0.5">آلية حساب الملكية</span>
                                        </th>
                                        <th class="px-6 py-6 text-center">
                                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">المساهمة</span>
                                            <span class="block text-[8px] font-bold text-gray-300 mt-0.5">رأس المال المدفوع</span>
                                        </th>
                                        <th class="px-6 py-6 text-center">
                                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">النسبة</span>
                                            <span class="block text-[8px] font-bold text-gray-300 mt-0.5">الحصة من الإجمالي</span>
                                        </th>
                                        <th class="px-6 py-6 text-center">
                                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest text-emerald-600/60">القيمة الحالية</span>
                                            <span class="block text-[8px] font-bold text-emerald-300/60 mt-0.5">قيمة الحصة اليوم</span>
                                        </th>
                                        <th class="px-10 py-6 text-center">
                                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">إجراءات</span>
                                            <span class="block text-[8px] font-bold text-gray-300 mt-0.5">تعديل أو استبعاد</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($equities as $equity)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-10 py-6">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-black text-sm">
                                                        {{ mb_substr($equity->partner->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <span class="font-black text-gray-900 block">{{ $equity->partner->name }}</span>
                                                        @if($equity->partner->linked_user_id == auth()->id())
                                                            <span class="text-[8px] font-black text-indigo-500 uppercase">أنت (المدير)</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-6 text-center">
                                                <span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-3 py-1 rounded-lg">
                                                    {{ $equity->equity_type == 'fixed' ? 'نسبة ثابتة' : 'رأس مال' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-6 text-center font-black text-gray-700">{{ number_format($equity->amount, 0) }} {{ $fund->currency }}</td>
                                            <td class="px-6 py-6 text-center font-black text-indigo-600 text-lg">{{ number_format($equity->percentage, 1) }}%</td>
                                            <td class="px-6 py-6 text-center font-black text-emerald-700 text-lg">{{ number_format(($equity->percentage / 100) * $fund->current_value, 0) }} {{ $fund->currency }}</td>
                                            <td class="px-10 py-6 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button @click="editingEquity = {{ $equity->id }}" class="p-2 text-gray-400 hover:text-indigo-600 transition-colors">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    </button>
                                                    @if($equity->partner->linked_user_id !== auth()->id())
                                                        <form action="{{ route('funds.removePartner', $equity->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف الشريك؟')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="p-2 text-gray-400 hover:text-rose-600 transition-colors">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @foreach($equities as $equity)
                        <!-- Edit Modal Moved Outside -->
                        <div x-show="editingEquity === {{ $equity->id }}" 
                             class="fixed inset-0 z-[500] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-sm" 
                             x-cloak 
                             x-transition>
                            <div class="bg-white rounded-[3rem] w-full max-w-md p-10 shadow-2xl relative text-right" @click.away="editingEquity = null">
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-2xl font-black text-gray-900">تعديل الحصة</h3>
                                    <button @click="editingEquity = null" class="text-gray-400 hover:text-gray-900">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                                <form action="{{ route('funds.updateEquity', $equity->id) }}" method="POST" class="space-y-6">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">المبلغ</label>
                                        <input type="number" name="amount" value="{{ $equity->amount }}" class="w-full premium-input">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">النسبة (%)</label>
                                        <input type="number" step="0.1" name="percentage" value="{{ $equity->percentage }}" class="w-full premium-input">
                                    </div>
                                    <div class="flex gap-4 pt-4">
                                        <button type="submit" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl font-black shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 transition-all">حفظ</button>
                                        <button type="button" @click="editingEquity = null" class="px-6 bg-gray-50 text-gray-400 py-4 rounded-2xl font-black">إلغاء</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Sidebar -->
                <div class="space-y-10">
                    <!-- Accounts Widget -->
                    <div class="premium-card p-10 relative overflow-hidden bg-indigo-900 text-white shadow-2xl shadow-indigo-900/20">
                        <div class="absolute -right-20 -top-20 w-60 h-60 bg-white/10 rounded-full blur-3xl"></div>
                        <div class="flex justify-between items-center mb-8 relative z-10">
                            <h3 class="text-2xl font-black">حسابات الصندوق 💳</h3>
                            <button @click="showAccountModal = true" class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center hover:bg-white/30 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        </div>
                        <div class="space-y-6 relative z-10">
                            @forelse($paymentMethods as $pm)
                                <div class="bg-white/10 p-5 rounded-3xl border border-white/10 backdrop-blur-sm hover:bg-white/15 transition-all">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-lg">
                                                {{ $pm->type == 'bank' ? '🏦' : ($pm->type == 'wallet' ? '📱' : '💵') }}
                                            </div>
                                            <div>
                                                <p class="text-xs font-black opacity-60 uppercase tracking-widest">{{ $pm->name }}</p>
                                                <p class="text-[10px] font-bold opacity-40">{{ $pm->type == 'bank' ? 'حساب بنكي' : 'محفظة رقمية' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button @click="reconcilingId = {{ $pm->id }}; reconcilingName = `{{ $pm->name }}`; reconcilingBalance = {{ $pm->balance }};" class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center hover:bg-white/20 transition-all text-[10px]" title="مطابقة الرصيد">
                                                ⚖️
                                            </button>
                                            <span class="text-[10px] font-black bg-white/20 px-2 py-1 rounded-lg">{{ $pm->currency }}</span>
                                        </div>
                                    </div>
                                    <p class="text-2xl font-black tracking-tighter">{{ number_format($pm->balance, 0) }} <span class="text-xs opacity-60">{{ $pm->currency }}</span></p>
                                </div>
                            @empty
                                <div class="text-center py-10 border-2 border-dashed border-white/20 rounded-[2.5rem]">
                                    <p class="text-xs font-black opacity-40 uppercase tracking-widest italic">لا توجد حسابات مضافة</p>
                                </div>
                            @endforelse

                        </div>
                    </div>

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
                                    <p class="text-sm font-black {{ $transaction->type == 'income' ? 'text-emerald-700' : 'text-rose-700' }}">
                                        {{ $transaction->type == 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 0) }} <span class="text-[10px]">{{ $transaction->paymentMethod->currency ?? $fund->currency }}</span>
                                    </p>
                                </div>
                            @empty
                                <div class="text-center py-10">
                                    <div class="text-4xl mb-4">🌫️</div>
                                    <p class="text-gray-400 font-bold">لا توجد عمليات مسجلة</p>
                                </div>
                            @endforelse
                        </div>
                        <a href="{{ route('funds.transactions', $fund->id) }}" class="block w-full mt-10 py-4 bg-gray-50 rounded-2xl text-[10px] font-black text-gray-400 uppercase tracking-widest text-center hover:bg-gray-100 transition-colors">عرض كافة العمليات</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fund Account Reconcile Modal -->
    <div x-show="reconcilingId" class="fixed inset-0 z-[150] flex items-center justify-center p-6 bg-gray-900/80 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-white rounded-[3rem] w-full max-w-sm p-12 shadow-2xl relative text-right text-gray-900" @click.away="reconcilingId = null">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-3xl font-black">مطابقة رصيد</h3>
                <button @click="reconcilingId = null" class="text-gray-400 hover:text-rose-600 transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <p class="text-xs font-bold text-gray-500 mb-10 leading-relaxed italic">مطابقة حساب: <span class="text-indigo-600" x-text="reconcilingName"></span></p>
            
            <form :action="'/funds/{{ $fund->id }}/accounts/' + reconcilingId + '/reconcile'" method="POST" class="space-y-8">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-4 mr-2 tracking-widest">المبلغ الحقيقي الحالي ($)</label>
                    <input type="number" name="actual_balance" required step="0.01" class="w-full premium-input border-gray-100 text-gray-900" :placeholder="'الرصيد الحالي في النظام: ' + reconcilingBalance">
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white py-6 rounded-[2rem] font-black text-xl shadow-xl shadow-indigo-500/20 hover:bg-indigo-700 transition-all">تأكيد المطابقة</button>
            </form>
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
