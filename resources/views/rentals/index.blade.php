<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/20 py-8" x-data="{
        activeTab: 'properties',
        showPropertyModal: false,
        showUnitModal: false,
        showTenantModal: false,
        showContractModal: false,
        showCollectModal: false,
        selectedPaymentId: null,
        selectedPropertyId: null
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-6" dir="rtl">
                <div class="text-right">
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">🏢 منظومة إدارة الأملاك والعقارات الإيجارية</h1>
                    <p class="text-xs text-slate-400 font-bold mt-1">تتبع عقاراتك، وحداتك السكنية، عقود المستأجرين، وجدولة عمليات تحصيل الإيجارات بمرونة كاملة.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button @click="showPropertyModal = true" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black text-xs transition-all shadow-sm">
                        ➕ إضافة عقار
                    </button>
                    <button @click="showTenantModal = true" class="px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl font-black text-xs transition-all shadow-sm">
                        👤 إضافة مستأجر
                    </button>
                    <button @click="showContractModal = true" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-black text-xs transition-all shadow-sm">
                        📝 إنشاء عقد إيجار
                    </button>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold text-sm rounded-2xl px-5 py-4 flex items-center gap-2" dir="rtl">
                    ✅ {{ session('success') }}
                </div>
            @endif

            {{-- Stats Overview --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-right" dir="rtl">
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">إجمالي العقارات</p>
                        <p class="text-2xl font-black text-slate-900 tracking-tight">{{ $properties->count() }}</p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl shadow-xs">🏢</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">الوحدات المؤجرة</p>
                        <p class="text-2xl font-black text-emerald-600 tracking-tight">
                            {{ $properties->flatMap->units->where('status', 'occupied')->count() }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl shadow-xs">🔑</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">الوحدات الشاغرة</p>
                        <p class="text-2xl font-black text-amber-500 tracking-tight">
                            {{ $properties->flatMap->units->where('status', 'vacant')->count() }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center text-xl shadow-xs">🏝️</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">الدفعات المحصلة</p>
                        <p class="text-2xl font-black text-violet-600 tracking-tight">
                            {{ number_format($payments->where('status', 'paid')->sum('amount_paid'), 0) }} <span class="text-xs font-bold">USD</span>
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-violet-50 text-violet-600 rounded-xl flex items-center justify-center text-xl shadow-xs">💰</div>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="flex items-center gap-1.5 bg-slate-100 p-1.5 rounded-2xl w-fit mr-auto no-print" dir="rtl">
                <button @click="activeTab = 'properties'" :class="activeTab === 'properties' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-500 hover:text-slate-900'" class="px-5 py-2.5 rounded-xl font-black text-xs transition-all">
                    🏠 العقارات والوحدات
                </button>
                <button @click="activeTab = 'contracts'" :class="activeTab === 'contracts' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-500 hover:text-slate-900'" class="px-5 py-2.5 rounded-xl font-black text-xs transition-all">
                    📝 عقود الإيجار
                </button>
                <button @click="activeTab = 'tenants'" :class="activeTab === 'tenants' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-500 hover:text-slate-900'" class="px-5 py-2.5 rounded-xl font-black text-xs transition-all">
                    👥 المستأجرون
                </button>
                <button @click="activeTab = 'payments'" :class="activeTab === 'payments' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-500 hover:text-slate-900'" class="px-5 py-2.5 rounded-xl font-black text-xs transition-all">
                    🗓️ جدول الدفعات
                </button>
            </div>

            {{-- Content Panels --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden text-right" dir="rtl">
                
                {{-- Panel 1: Properties --}}
                <div x-show="activeTab === 'properties'" class="p-6 space-y-6">
                    @forelse($properties as $property)
                        <div class="border border-slate-100 rounded-2xl p-5 hover:border-indigo-100 transition-colors">
                            <div class="flex items-start justify-between flex-wrap gap-4 mb-4">
                                <div>
                                    <h3 class="text-base font-black text-slate-900">{{ $property->name }}</h3>
                                    <p class="text-xs text-slate-400 font-bold mt-1">📍 {{ $property->address ?? 'لا يوجد عنوان مسجل' }}</p>
                                    @if($property->description)
                                        <p class="text-xs text-slate-500 font-semibold mt-2 bg-slate-50 p-3 rounded-xl border border-slate-100/50">{{ $property->description }}</p>
                                    @endif
                                </div>
                                <button @click="selectedPropertyId = {{ $property->id }}; showUnitModal = true" class="px-3.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl font-black text-[11px] transition-all">
                                    ➕ إضافة وحدة
                                </button>
                            </div>

                            {{-- Units List --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @forelse($property->units as $unit)
                                    <div class="bg-slate-50/50 rounded-xl border border-slate-100/80 p-4 flex flex-col justify-between">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-xs font-black text-slate-900">{{ $unit->name }}</span>
                                            <span class="px-2 py-0.5 text-[9px] font-black rounded-lg 
                                                {{ $unit->status === 'occupied' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-amber-50 text-amber-600 border border-amber-100' }}">
                                                {{ $unit->status === 'occupied' ? 'مؤجرة' : 'شاغرة' }}
                                            </span>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="flex justify-between text-[11px] font-bold text-slate-400">
                                                <span>قيمة الإيجار:</span>
                                                <span class="text-slate-700">{{ number_format($unit->rent_amount, 0) }} USD</span>
                                            </div>
                                            @if($unit->status === 'occupied' && $unit->activeContract)
                                                <div class="flex justify-between text-[11px] font-bold text-slate-400">
                                                    <span>المستأجر:</span>
                                                    <span class="text-indigo-600">{{ $unit->activeContract->tenant->name }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 font-bold py-4 col-span-full text-center">لا توجد وحدات مضافة لهذا العقار بعد.</p>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-16">
                            <p class="text-slate-400 font-bold">لا يوجد أي عقارات مسجلة حالياً.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Panel 2: Contracts --}}
                <div x-show="activeTab === 'contracts'" class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-right border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100">
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase">الوحدة / العقار</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase">المستأجر</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase">الفترة</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase text-center">القيمة الإيجارية</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase text-center">دورية الدفع</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase text-center">الحالة</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs font-bold text-slate-700">
                                @forelse($contracts as $contract)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="font-black text-slate-900">{{ $contract->unit->name }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $contract->unit->property->name }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-indigo-600 font-black">{{ $contract->tenant->name }}</td>
                                        <td class="px-6 py-4 text-slate-400">{{ $contract->start_date->format('Y-m-d') }} إلى {{ $contract->end_date->format('Y-m-d') }}</td>
                                        <td class="px-6 py-4 text-center font-black text-slate-900">{{ number_format($contract->rent_amount, 2) }} USD</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-2 py-0.5 bg-slate-100 rounded-md text-[10px]">
                                                {{ $contract->billing_cycle === 'monthly' ? 'شهري' : ($contract->billing_cycle === 'quarterly' ? 'ربع سنوي' : 'سنوي') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded-lg text-[9px]">مستمر</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">لا توجد عقود إيجار مسجلة.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Panel 3: Tenants --}}
                <div x-show="activeTab === 'tenants'" class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-right border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100">
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase">اسم المستأجر</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase">رقم الهاتف</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase">البريد الإلكتروني</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase">رقم الهوية الوطنية</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs font-bold text-slate-700">
                                @forelse($tenants as $tenant)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4 font-black text-slate-900">{{ $tenant->name }}</td>
                                        <td class="px-6 py-4 text-slate-500">{{ $tenant->phone ?? '—' }}</td>
                                        <td class="px-6 py-4 text-slate-500">{{ $tenant->email ?? '—' }}</td>
                                        <td class="px-6 py-4 text-slate-500">{{ $tenant->national_id ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-slate-400">لا يوجد مستأجرون مسجلون.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Panel 4: Payments --}}
                <div x-show="activeTab === 'payments'" class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-right border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100">
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase">الوحدة / المستأجر</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase text-center">المبلغ المستحق</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase">تاريخ الاستحقاق</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase">الحساب المحصل إليه</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase text-center">الحالة</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase text-left">الإجراء</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs font-bold text-slate-700">
                                @forelse($payments as $payment)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="font-black text-slate-900">{{ $payment->leaseContract->unit->name }}</p>
                                            <p class="text-[10px] text-indigo-600">{{ $payment->leaseContract->tenant->name }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-center font-black text-slate-900">{{ number_format($payment->amount_due, 2) }} USD</td>
                                        <td class="px-6 py-4 text-slate-400">{{ $payment->due_date->format('Y-m-d') }}</td>
                                        <td class="px-6 py-4">
                                            @if($payment->paymentMethod)
                                                <span class="text-slate-600">{{ $payment->paymentMethod->name }}</span>
                                            @else
                                                <span class="text-slate-300">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-2 py-0.5 rounded-lg text-[9px] 
                                                {{ $payment->status === 'paid' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                                {{ $payment->status === 'paid' ? 'تم الدفع' : 'معلّقة' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-left">
                                            @if($payment->status === 'pending')
                                                <button @click="selectedPaymentId = {{ $payment->id }}; showCollectModal = true" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] transition-all">
                                                    💵 تسجيل تحصيل الدفعة
                                                </button>
                                            @else
                                                <span class="text-[10px] text-slate-400">📅 مدفوعة بتاريخ {{ $payment->paid_date->format('Y-m-d') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">لا توجد دفعات مجدولة.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        {{-- Modals --}}
        
        {{-- Add Property Modal --}}
        <div x-show="showPropertyModal" class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-xs" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md shadow-xl text-right overflow-hidden p-6" @click.away="showPropertyModal = false">
                <h3 class="text-lg font-black text-slate-900 mb-4 border-b border-slate-100 pb-2">➕ تسجيل عقار جديد</h3>
                <form action="{{ route('rentals.properties.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-black text-slate-400 mb-2">اسم العقار / البناية</label>
                        <input type="text" name="name" required class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-400 mb-2">📍 العنوان</label>
                        <input type="text" name="address" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-400 mb-2">تفاصيل إضافية</label>
                        <textarea name="description" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none" rows="3"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-4">
                        <button type="button" @click="showPropertyModal = false" class="px-4 py-2 text-slate-500 font-bold text-xs hover:bg-slate-100 rounded-xl transition-all">إلغاء</button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-xs transition-all shadow-sm">حفظ العقار</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Add Unit Modal --}}
        <div x-show="showUnitModal" class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-xs" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md shadow-xl text-right overflow-hidden p-6" @click.away="showUnitModal = false">
                <h3 class="text-lg font-black text-slate-900 mb-4 border-b border-slate-100 pb-2">➕ إضافة وحدة إيجارية للعقار</h3>
                <form :action="`/rentals/properties/${selectedPropertyId}/units`" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-black text-slate-400 mb-2">اسم أو رقم الوحدة</label>
                        <input type="text" name="name" placeholder="مثال: شقة 101" required class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-400 mb-2">نوع العقار</label>
                        <select name="type" required class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
                            <option value="residential">سكني</option>
                            <option value="commercial">تجاري / محل</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-400 mb-2">قيمة الإيجار المتوقعة (USD)</label>
                        <input type="number" step="0.01" name="rent_amount" required class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-4">
                        <button type="button" @click="showUnitModal = false" class="px-4 py-2 text-slate-500 font-bold text-xs hover:bg-slate-100 rounded-xl transition-all">إلغاء</button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-xs transition-all shadow-sm">إضافة الوحدة</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Add Tenant Modal --}}
        <div x-show="showTenantModal" class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-xs" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md shadow-xl text-right overflow-hidden p-6" @click.away="showTenantModal = false">
                <h3 class="text-lg font-black text-slate-900 mb-4 border-b border-slate-100 pb-2">👤 تسجيل مستأجر جديد</h3>
                <form action="{{ route('rentals.tenants.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-black text-slate-400 mb-2">اسم المستأجر</label>
                        <input type="text" name="name" required class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-400 mb-2">رقم الجوال / الهاتف</label>
                        <input type="text" name="phone" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-400 mb-2">البريد الإلكتروني</label>
                        <input type="email" name="email" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-400 mb-2">رقم الهوية الوطنية / جواز السفر</label>
                        <input type="text" name="national_id" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-4">
                        <button type="button" @click="showTenantModal = false" class="px-4 py-2 text-slate-500 font-bold text-xs hover:bg-slate-100 rounded-xl transition-all">إلغاء</button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-xs transition-all shadow-sm">حفظ المستأجر</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Add Lease Contract Modal --}}
        <div x-show="showContractModal" class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-xs" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md shadow-xl text-right overflow-hidden p-6" @click.away="showContractModal = false">
                <h3 class="text-lg font-black text-slate-900 mb-4 border-b border-slate-100 pb-2">📝 توثيق عقد إيجار وجدولة دفعات</h3>
                <form action="{{ route('rentals.contracts.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-black text-slate-400 mb-2">الوحدة السكنية / العقار</label>
                        <select name="unit_id" required class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
                            @foreach($properties as $property)
                                <optgroup label="{{ $property->name }}">
                                    @foreach($property->units as $unit)
                                        @if($unit->status === 'vacant')
                                            <option value="{{ $unit->id }}">{{ $unit->name }} (سعر مقدر: {{ $unit->rent_amount }} USD)</option>
                                        @endif
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-400 mb-2">المستأجر</label>
                        <select name="tenant_id" required class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
                            @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-slate-400 mb-2">تاريخ البدء</label>
                            <input type="date" name="start_date" required class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-400 mb-2">تاريخ الانتهاء</label>
                            <input type="date" name="end_date" required class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-400 mb-2">قيمة الإيجار المتفق عليها (USD)</label>
                        <input type="number" step="0.01" name="rent_amount" required class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-400 mb-2">دورية الدفع</label>
                        <select name="billing_cycle" required class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
                            <option value="monthly">شهرياً</option>
                            <option value="quarterly">كل 3 أشهر (ربع سنوي)</option>
                            <option value="annually">سنوياً</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-4">
                        <button type="button" @click="showContractModal = false" class="px-4 py-2 text-slate-500 font-bold text-xs hover:bg-slate-100 rounded-xl transition-all">إلغاء</button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-xs transition-all shadow-sm">تثبيت وتوليد الدفعات</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Collect Lease Payment Modal --}}
        <div x-show="showCollectModal" class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-xs" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md shadow-xl text-right overflow-hidden p-6" @click.away="showCollectModal = false">
                <h3 class="text-lg font-black text-slate-900 mb-4 border-b border-slate-100 pb-2">💵 تحصيل دفعة إيجار مالي</h3>
                <form :action="`/rentals/payments/${selectedPaymentId}/collect`" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-black text-slate-400 mb-2">الحساب / المحفظة التي استلمت المبلغ</label>
                        <select name="payment_method_id" required class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }} (رصيد: {{ $pm->balance }} {{ $pm->currency }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-4">
                        <button type="button" @click="showCollectModal = false" class="px-4 py-2 text-slate-500 font-bold text-xs hover:bg-slate-100 rounded-xl transition-all">إلغاء</button>
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white rounded-xl font-black text-xs transition-all shadow-sm">تسجيل التحصيل فوراً</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
