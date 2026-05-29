<x-app-layout>
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/20"
     x-data="{ showPayment: false, showEdit: false, showCharge: false, payAlt: false, chargeAlt: false }">

    {{-- ===================== HEADER ===================== --}}
    <div class="bg-white border-b border-slate-100 sticky top-0 z-40 backdrop-blur-xl bg-white/90">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('ledger.index') }}"
                   class="w-9 h-9 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-500 rounded-xl flex items-center justify-center transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-base font-black text-slate-900 leading-none">{{ $entry->party_name }}</h1>
                    <p class="text-[10px] font-bold mt-0.5
                        @if($entry->type === 'receivable') text-emerald-600
                        @elseif($entry->type === 'payable') text-rose-600
                        @elseif($entry->type === 'installment') text-amber-600
                        @else text-violet-600 @endif">
                        {{ $entry->type_icon }} {{ $entry->type_label }}
                        @if($entry->type === 'receivable') · مديني لي
                        @elseif($entry->type === 'payable') · أنا مدين له
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if($entry->status !== 'settled')
                    <button @click="showPayment = true"
                        class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-black text-sm shadow-lg shadow-emerald-500/20 transition-all hover:scale-105">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        تسجيل دفعة
                    </button>
                    <button @click="showCharge = true"
                        class="flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-black text-sm shadow-lg shadow-amber-500/20 transition-all hover:scale-105">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        إضافة للذمة
                    </button>
                @endif
                <button @click="showEdit = true"
                    class="w-9 h-9 bg-slate-100 text-slate-500 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl flex items-center justify-center transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
                <form action="{{ route('ledger.destroy', $entry->id) }}" method="POST"
                      onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="w-9 h-9 bg-rose-50 text-rose-400 hover:bg-rose-600 hover:text-white rounded-xl flex items-center justify-center transition-all border border-rose-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 space-y-6">

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold text-sm rounded-2xl px-5 py-4 flex items-center gap-2">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 font-bold text-sm rounded-2xl px-5 py-4 space-y-1">
                <div class="flex items-center gap-2 font-black">
                    ❌ حدث خطأ أثناء الحفظ:
                </div>
                <ul class="list-disc list-inside mr-6 text-xs space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ===================== HERO CARD ===================== --}}
        @php
            $colorMap = [
                'receivable'  => ['from' => 'from-emerald-600', 'to' => 'to-teal-700',   'shadow' => 'shadow-emerald-500/30'],
                'payable'     => ['from' => 'from-rose-600',    'to' => 'to-pink-700',    'shadow' => 'shadow-rose-500/30'],
                'installment' => ['from' => 'from-amber-500',   'to' => 'to-orange-600',  'shadow' => 'shadow-amber-500/30'],
                'loan'        => ['from' => 'from-violet-600',  'to' => 'to-purple-700',  'shadow' => 'shadow-violet-500/30'],
            ][$entry->type];
        @endphp

        <div class="bg-gradient-to-br {{ $colorMap['from'] }} {{ $colorMap['to'] }} rounded-3xl p-8 text-white relative overflow-hidden shadow-2xl {{ $colorMap['shadow'] }}">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -left-8 -bottom-8 w-48 h-48 bg-black/10 rounded-full blur-2xl"></div>

            <div class="relative z-10">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <p class="text-white/60 text-xs font-black uppercase tracking-widest mb-1">المبلغ المتبقي</p>
                        <p class="text-5xl font-black tracking-tighter">
                            {{ number_format($entry->remaining_amount, 2) }}
                            <span class="text-xl opacity-70">{{ $entry->currency }}</span>
                        </p>
                        <p class="text-white/60 text-sm font-bold mt-1">من إجمالي {{ number_format($entry->total_amount, 2) }} {{ $entry->currency }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <span class="text-3xl">{{ $entry->type_icon }}</span>
                        <span class="px-3 py-1.5 rounded-xl text-[10px] font-black border border-white/20 backdrop-blur-sm
                            @if($entry->status === 'settled') bg-white/30 text-white
                            @elseif($entry->status === 'overdue') bg-rose-900/40 text-rose-200
                            @elseif($entry->status === 'partial') bg-amber-900/30 text-amber-200
                            @else bg-white/20 text-white @endif">
                            {{ $entry->status_label }}
                        </span>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <p class="text-white/60 text-[10px] font-black uppercase tracking-widest">نسبة السداد</p>
                        <p class="text-white font-black text-sm">{{ $entry->progress_percent }}%</p>
                    </div>
                    <div class="h-3 bg-white/20 rounded-full overflow-hidden">
                        <div class="h-full bg-white/80 rounded-full transition-all duration-700"
                             style="width: {{ $entry->progress_percent }}%"></div>
                    </div>
                </div>

                {{-- Stats Row --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-white/20">
                    <div>
                        <p class="text-white/50 text-[9px] font-black uppercase tracking-widest">المدفوع (السداد)</p>
                        <p class="text-white font-black text-lg tracking-tighter">
                            {{ number_format($entry->paid_amount, 2) }}
                            <span class="text-xs opacity-75">{{ $entry->currency }}</span>
                        </p>
                        <p class="text-[9px] text-white/40 font-bold mt-0.5">عدد الدفعات: {{ $entry->payments->count() }}</p>
                    </div>
                    <div>
                        <p class="text-white/50 text-[9px] font-black uppercase tracking-widest">المضاف للذمة</p>
                        <p class="text-white font-black text-lg tracking-tighter">
                            {{ number_format($entry->charges()->sum('amount'), 2) }}
                            <span class="text-xs opacity-75">{{ $entry->currency }}</span>
                        </p>
                        <p class="text-[9px] text-white/40 font-bold mt-0.5">عدد الإضافات: {{ $entry->charges()->count() }}</p>
                    </div>
                    <div>
                        <p class="text-white/50 text-[9px] font-black uppercase tracking-widest">إجمالي الحركات</p>
                        <p class="text-white font-black text-lg tracking-tighter">{{ $entry->movements->count() }} حركة</p>
                        <p class="text-[9px] text-white/40 font-bold mt-0.5">موثقة بالسجل أدناه</p>
                    </div>
                    <div>
                        <p class="text-white/50 text-[9px] font-black uppercase tracking-widest">تاريخ الاستحقاق</p>
                        <p class="text-white font-black text-sm mt-1">
                            {{ $entry->due_date ? $entry->due_date->format('d/m/Y') : 'غير محدد' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== DETAILS ===================== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">تفاصيل القيد</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400">النوع</span>
                        <span class="text-sm font-black text-slate-900">{{ $entry->type_icon }} {{ $entry->type_label }}</span>
                    </div>
                    @if($entry->type === 'receivable')
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-slate-400">العلاقة</span>
                            <span class="text-sm font-black text-emerald-600">{{ $entry->party_name }} مدين لك</span>
                        </div>
                    @elseif($entry->type === 'payable')
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-slate-400">العلاقة</span>
                            <span class="text-sm font-black text-rose-600">أنت مدين لـ {{ $entry->party_name }}</span>
                        </div>
                    @endif
                    @if($entry->description)
                        <div class="flex justify-between items-start gap-4">
                            <span class="text-xs font-bold text-slate-400 flex-shrink-0">الوصف</span>
                            <span class="text-sm font-bold text-slate-700 text-left">{{ $entry->description }}</span>
                        </div>
                    @endif
                    @if($entry->party_phone)
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-slate-400">الهاتف</span>
                            <a href="tel:{{ $entry->party_phone }}" class="text-sm font-black text-indigo-600 hover:underline">
                                📞 {{ $entry->party_phone }}
                            </a>
                        </div>
                    @endif
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400">العملة</span>
                        <span class="text-sm font-black text-slate-900">{{ $entry->currency }}</span>
                    </div>
                    @if($entry->start_date)
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-slate-400">تاريخ البدء</span>
                            <span class="text-sm font-black text-slate-900">{{ $entry->start_date->format('d/m/Y') }}</span>
                        </div>
                    @endif
                    @if($entry->due_date)
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-slate-400">الاستحقاق</span>
                            <span class="text-sm font-black {{ $entry->due_date->isPast() && $entry->status !== 'settled' ? 'text-rose-600' : 'text-slate-900' }}">
                                {{ $entry->due_date->format('d/m/Y') }}
                                @if($entry->due_date->isPast() && $entry->status !== 'settled')
                                    <span class="text-[10px]">(متأخر)</span>
                                @elseif(!$entry->due_date->isPast())
                                    <span class="text-[10px] text-slate-400">({{ $entry->due_date->diffForHumans() }})</span>
                                @endif
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Installment details --}}
            @if($entry->type === 'installment' && $entry->installment_count)
                <div class="bg-amber-50 rounded-2xl border border-amber-100 p-6 shadow-sm space-y-4">
                    <h3 class="text-xs font-black text-amber-700 uppercase tracking-widest">📦 تفاصيل التقسيط</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-amber-600">عدد الأقساط</span>
                            <span class="text-sm font-black text-amber-900">{{ $entry->installment_count }} قسط</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-amber-600">قيمة القسط</span>
                            <span class="text-sm font-black text-amber-900">{{ number_format($entry->installment_amount, 2) }} {{ $entry->currency }}</span>
                        </div>
                        @php
                            $paidInstallments = $entry->installment_amount > 0
                                ? floor($entry->paid_amount / $entry->installment_amount)
                                : 0;
                            $remainingInstallments = max(0, $entry->installment_count - $paidInstallments);
                        @endphp
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-amber-600">المدفوع</span>
                            <span class="text-sm font-black text-emerald-700">{{ $paidInstallments }} قسط</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-amber-600">المتبقي</span>
                            <span class="text-sm font-black text-rose-700">{{ $remainingInstallments }} قسط</span>
                        </div>
                    </div>
                </div>
            @else
                @if($entry->notes)
                    <div class="bg-slate-50 rounded-2xl border border-slate-100 p-6 shadow-sm">
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">ملاحظات</h3>
                        <p class="text-sm font-bold text-slate-700 leading-relaxed">{{ $entry->notes }}</p>
                    </div>
                @endif
            @endif
        </div>

        {{-- ===================== MOVEMENTS HISTORY ===================== --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-50 flex items-center justify-between">
                <h3 class="font-black text-slate-900 text-sm">سجل الحركات (الدفعات والإضافات)</h3>
                <span class="text-[10px] font-black text-slate-400 bg-slate-100 px-3 py-1 rounded-full">{{ $entry->movements->count() }} حركة</span>
            </div>

            @if($entry->movements->isEmpty())
                <div class="py-16 text-center">
                    <div class="text-4xl mb-3 opacity-20">📊</div>
                    <p class="text-slate-400 font-black text-sm">لا توجد حركات مسجلة بعد</p>
                    @if($entry->status !== 'settled')
                        <div class="flex justify-center gap-4 mt-4">
                            <button @click="showPayment = true" class="text-xs font-black text-emerald-600 hover:underline">
                                + سجّل دفعة
                            </button>
                            <button @click="showCharge = true" class="text-xs font-black text-amber-600 hover:underline">
                                + إضافة للذمة
                            </button>
                        </div>
                    @endif
                </div>
            @else
                <div class="divide-y divide-slate-50">
                    @foreach($entry->movements->sortByDesc(function($m) { return $m->payment_date->format('Y-m-d') . '_' . $m->id; }) as $movement)
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-slate-50/50 transition-all">
                            <div class="flex items-center gap-4">
                                @if($movement->type === 'charge')
                                    <div class="w-9 h-9 bg-amber-50 border border-amber-100 rounded-xl flex items-center justify-center text-base flex-shrink-0 text-amber-600 font-black">
                                        ➕
                                    </div>
                                @else
                                    <div class="w-9 h-9 bg-emerald-50 border border-emerald-100 rounded-xl flex items-center justify-center text-base flex-shrink-0 text-emerald-600">
                                        💳
                                    </div>
                                @endif
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="font-black text-slate-900 text-sm">
                                            @if($movement->type === 'charge')
                                                إضافة للذمة بتاريخ {{ $movement->payment_date->format('d/m/Y') }}
                                            @else
                                                دفعة بتاريخ {{ $movement->payment_date->format('d/m/Y') }}
                                            @endif
                                        </p>
                                        <span class="text-[9px] font-black px-2 py-0.5 rounded-full 
                                            @if($movement->type === 'charge') bg-amber-50 text-amber-700 border border-amber-100
                                            @else bg-emerald-50 text-emerald-700 border border-emerald-100 @endif">
                                            @if($movement->type === 'charge') زيادة دين @else سداد @endif
                                        </span>
                                    </div>
                                    @if($movement->original_amount && $movement->original_currency)
                                        <p class="text-[10px] font-bold text-indigo-500 mt-0.5">
                                            {{ number_format($movement->original_amount, 2) }} {{ $movement->original_currency }}
                                            <span class="text-slate-300 mx-1">×</span>
                                            <span class="text-slate-400">{{ $movement->exchange_rate }}</span>
                                        </p>
                                    @endif
                                    @if($movement->notes)
                                        <p class="text-xs font-bold text-slate-500 mt-1 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 inline-block">{{ $movement->notes }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-left">
                                <p class="font-black text-base tracking-tighter 
                                    @if($movement->type === 'charge') text-amber-600 @else text-emerald-600 @endif">
                                    @if($movement->type === 'charge') + @else - @endif{{ number_format($movement->amount, 2) }}
                                    <span class="text-xs opacity-60">{{ $movement->currency }}</span>
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ===================== ADD PAYMENT MODAL ===================== --}}
    <div x-show="showPayment" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl text-right" @click.away="showPayment = false">
            <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xl font-black text-gray-900">تسجيل دفعة</h3>
                <button @click="showPayment = false" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-500 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('ledger.payment', $entry->id) }}" method="POST" class="p-8 space-y-5">
                @csrf
                <input type="hidden" name="pay_in_alt" :value="payAlt ? '1' : '0'">

                {{-- Remaining context --}}
                <div class="bg-slate-50 rounded-2xl px-5 py-4 flex justify-between items-center">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">المتبقي</p>
                        <p class="text-2xl font-black text-slate-900 tracking-tighter">
                            {{ number_format($entry->remaining_amount, 2) }}
                            <span class="text-sm opacity-60">{{ $entry->currency }}</span>
                        </p>
                    </div>
                    <div class="text-3xl">{{ $entry->type_icon }}</div>
                </div>

                {{-- Alt currency toggle --}}
                <div class="bg-indigo-50 rounded-2xl p-4 border border-indigo-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs font-black text-indigo-700">دفع بعملة مختلفة</p>
                            <p class="text-[9px] font-bold text-indigo-400 mt-0.5">يتم التحويل تلقائياً بسعر الصرف الذي تحدده</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="payAlt" class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-5 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>

                    {{-- Alt fields --}}
                    <div x-show="payAlt" x-cloak class="mt-4 space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[9px] font-black text-indigo-600 uppercase mb-1.5">العملة المدفوعة بها</label>
                                <select name="original_currency" class="w-full bg-white border border-indigo-100 rounded-xl p-3 font-bold text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                                    <option value="SYP">SYP - ليرة سورية</option>
                                    <option value="TRY">TRY - ليرة تركية</option>
                                    <option value="SAR">SAR - ريال</option>
                                    <option value="EUR">EUR - يورو</option>
                                    <option value="USD">USD - دولار</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[9px] font-black text-indigo-600 uppercase mb-1.5">سعر الصرف</label>
                                <input type="number" name="exchange_rate" step="0.0001" placeholder="مثال: 13900"
                                       class="w-full bg-white border border-indigo-100 rounded-xl p-3 font-bold text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                                <p class="text-[9px] font-bold text-indigo-400 mt-1">1 {{ $entry->currency }} = X من العملة</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-indigo-600 uppercase mb-1.5">المبلغ بالعملة البديلة</label>
                            <input type="number" name="original_amount" step="0.01" placeholder="0.00"
                                   class="w-full bg-white border border-indigo-100 rounded-xl p-3 font-black text-lg focus:ring-2 focus:ring-indigo-400 outline-none text-center">
                        </div>
                    </div>
                </div>

                {{-- amount in base currency (hidden when alt) --}}
                <div x-show="!payAlt">
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">مبلغ الدفعة ({{ $entry->currency }})</label>
                    <input type="number" name="amount" step="0.01"
                           value="{{ $entry->installment_amount ?? '' }}"
                           :disabled="payAlt"
                           class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-2xl focus:ring-2 focus:ring-emerald-500 outline-none text-center"
                           placeholder="0.00">
                </div>
                <div x-show="payAlt" x-cloak>
                    <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-3 text-center">
                        <p class="text-xs font-black text-emerald-700">يتم حساب المبلغ المعادل تلقائياً</p>
                        <p class="text-[9px] font-bold text-emerald-500 mt-0.5">المبلغ البديل ÷ سعر الصرف = القيمة بالدولار</p>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">التاريخ</label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                           class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">ملاحظة (اختياري)</label>
                    <input type="text" name="notes"
                           class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-emerald-500 outline-none"
                           placeholder="مثلاً: قسط شهر مايو...">
                </div>
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-2xl font-black text-base shadow-lg shadow-emerald-500/20 transition-all">
                    ✓ تأكيد الدفعة
                </button>
            </form>
        </div>
    </div>

    {{-- ===================== EDIT MODAL ===================== --}}
    <div x-show="showEdit" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl text-right overflow-y-auto max-h-[90vh]" @click.away="showEdit = false">
            <div class="sticky top-0 bg-white border-b border-slate-100 px-8 py-5 flex items-center justify-between rounded-t-3xl">
                <h3 class="text-xl font-black text-gray-900">تعديل القيد</h3>
                <button @click="showEdit = false" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-500 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('ledger.update', $entry->id) }}" method="POST" class="p-8 space-y-5">
                @csrf @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">اسم الطرف</label>
                        <input type="text" name="party_name" value="{{ $entry->party_name }}" required
                               class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">الهاتف</label>
                        <input type="text" name="party_phone" value="{{ $entry->party_phone }}"
                               class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">الوصف</label>
                    <input type="text" name="description" value="{{ $entry->description }}"
                           class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">المبلغ الإجمالي</label>
                        <input type="number" name="total_amount" step="0.01" value="{{ $entry->total_amount }}" required
                               class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">العملة</label>
                        <select name="currency" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            @foreach(['USD','SYP','TRY','SAR','EUR'] as $cur)
                                <option value="{{ $cur }}" {{ $entry->currency === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">تاريخ البدء</label>
                        <input type="date" name="start_date" value="{{ $entry->start_date?->format('Y-m-d') }}"
                               class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">تاريخ الاستحقاق</label>
                        <input type="date" name="due_date" value="{{ $entry->due_date?->format('Y-m-d') }}"
                               class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>

                @if($entry->type === 'installment')
                    <div class="grid grid-cols-2 gap-4 bg-amber-50 rounded-2xl p-4 border border-amber-100">
                        <div>
                            <label class="block text-[10px] font-black text-amber-600 uppercase mb-2">عدد الأقساط</label>
                            <input type="number" name="installment_count" value="{{ $entry->installment_count }}"
                                   class="w-full bg-white border-0 rounded-xl p-3 font-bold text-sm focus:ring-2 focus:ring-amber-400 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-amber-600 uppercase mb-2">قيمة القسط</label>
                            <input type="number" name="installment_amount" step="0.01" value="{{ $entry->installment_amount }}"
                                   class="w-full bg-white border-0 rounded-xl p-3 font-bold text-sm focus:ring-2 focus:ring-amber-400 outline-none">
                        </div>
                    </div>
                @endif

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">ملاحظات</label>
                    <textarea name="notes" rows="2" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none resize-none">{{ $entry->notes }}</textarea>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-black text-base shadow-lg shadow-indigo-500/20 transition-all">
                    ✓ حفظ التعديلات
                </button>
            </form>
        </div>
    </div>

    {{-- ===================== ADD CHARGE MODAL ===================== --}}
    <div x-show="showCharge" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl text-right" @click.away="showCharge = false">
            <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-gray-900">إضافة للذمة</h3>
                    <p class="text-xs font-bold text-amber-600 mt-0.5">
                        @if($entry->type === 'receivable') أقرضت {{ $entry->party_name }} مبلغاً إضافياً
                        @elseif($entry->type === 'payable') اقترضت مبلغاً إضافياً من {{ $entry->party_name }}
                        @elseif($entry->type === 'installment') أضفت قسطاً أو تكلفة إضافية
                        @else أضفت التزاماً إضافياً
                        @endif
                    </p>
                </div>
                <button @click="showCharge = false" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-500 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('ledger.charge', $entry->id) }}" method="POST" class="p-8 space-y-5">
                @csrf
                <input type="hidden" name="pay_in_alt" :value="chargeAlt ? '1' : '0'">

                {{-- Current total context --}}
                <div class="bg-amber-50 border border-amber-100 rounded-2xl px-5 py-4 flex justify-between items-center">
                    <div>
                        <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest">الذمة الحالية</p>
                        <p class="text-2xl font-black text-amber-800 tracking-tighter">
                            {{ number_format($entry->total_amount, 2) }}
                            <span class="text-sm opacity-60">{{ $entry->currency }}</span>
                        </p>
                    </div>
                    <div class="text-3xl">📋</div>
                </div>

                {{-- Alt currency toggle --}}
                <div class="bg-amber-50/50 rounded-2xl p-4 border border-amber-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs font-black text-amber-700">إضافة بعملة مختلفة</p>
                            <p class="text-[9px] font-bold text-amber-500 mt-0.5">يتم التحويل وإضافته بالعملة الأساسية</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="chargeAlt" class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-5 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500"></div>
                        </label>
                    </div>

                    <div x-show="chargeAlt" x-cloak class="mt-4 space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[9px] font-black text-amber-700 uppercase mb-1.5">العملة</label>
                                <select name="original_currency" class="w-full bg-white border border-amber-100 rounded-xl p-3 font-bold text-sm focus:ring-2 focus:ring-amber-400 outline-none">
                                    <option value="SYP">SYP - ليرة سورية</option>
                                    <option value="TRY">TRY - ليرة تركية</option>
                                    <option value="SAR">SAR - ريال</option>
                                    <option value="EUR">EUR - يورو</option>
                                    <option value="USD">USD - دولار</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[9px] font-black text-amber-700 uppercase mb-1.5">سعر الصرف (1 {{ $entry->currency }} = ?)</label>
                                <input type="number" name="exchange_rate" step="0.0001" placeholder="مثال: 13900"
                                       class="w-full bg-white border border-amber-100 rounded-xl p-3 font-bold text-sm focus:ring-2 focus:ring-amber-400 outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-amber-700 uppercase mb-1.5">المبلغ بالعملة البديلة</label>
                            <input type="number" name="original_amount" step="0.01" placeholder="0.00"
                                   class="w-full bg-white border border-amber-100 rounded-xl p-3 font-black text-lg focus:ring-2 focus:ring-amber-400 outline-none text-center">
                        </div>
                    </div>
                </div>

                <div x-show="!chargeAlt">
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">المبلغ المضاف ({{ $entry->currency }})</label>
                    <input type="number" name="amount" step="0.01"
                           :disabled="chargeAlt"
                           class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-2xl focus:ring-2 focus:ring-amber-500 outline-none text-center"
                           placeholder="0.00">
                </div>
                <div x-show="chargeAlt" x-cloak>
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-3 text-center">
                        <p class="text-xs font-black text-amber-700">يتم حساب المبلغ المعادل تلقائياً</p>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">تاريخ الإضافة</label>
                    <input type="date" name="charge_date" value="{{ date('Y-m-d') }}"
                           class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-amber-500 outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">سبب الإضافة (اختياري)</label>
                    <input type="text" name="notes"
                           class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-amber-500 outline-none"
                           placeholder="@if($entry->type === 'receivable')مثلاً: أقرضته مزيداً...@else مثلاً: فائدة إضافية...@endif">
                </div>

                <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white py-4 rounded-2xl font-black text-base shadow-lg shadow-amber-500/20 transition-all">
                    ✓ إضافة للذمة
                </button>
            </form>
        </div>
    </div>

</div>
</x-app-layout>
