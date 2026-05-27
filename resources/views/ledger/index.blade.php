<x-app-layout>
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-amber-50/20"
     x-data="{ showAdd: false, addType: 'receivable', isInstallment: false }">

    {{-- ===================== HEADER ===================== --}}
    <div class="bg-white border-b border-slate-100 sticky top-0 z-40 backdrop-blur-xl bg-white/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-black text-slate-900">الديوان 📒</h1>
                <p class="text-xs font-bold text-slate-400">تتبع ديونك ومدائنك وأقساطك وقروضك</p>
            </div>
            <button @click="showAdd = true"
                class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black text-sm shadow-lg shadow-indigo-500/20 transition-all hover:scale-105">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                إضافة قيد
            </button>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-8">

        {{-- ===================== STATS ===================== --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- مديني = ناس مدينين لي --}}
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-5 border border-emerald-100 shadow-sm">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center text-base">💸</div>
                    <p class="text-[10px] font-black text-emerald-700 uppercase tracking-widest">مديني (لي عندهم)</p>
                </div>
                <p class="text-2xl font-black text-emerald-700 tracking-tighter">{{ number_format($totalReceivable, 2) }}</p>
                <p class="text-[10px] font-bold text-emerald-500 mt-1">متبقي للتحصيل</p>
            </div>

            {{-- أنا المدين --}}
            <div class="bg-gradient-to-br from-rose-50 to-pink-50 rounded-2xl p-5 border border-rose-100 shadow-sm">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 bg-rose-100 rounded-xl flex items-center justify-center text-base">🏦</div>
                    <p class="text-[10px] font-black text-rose-700 uppercase tracking-widest">أنا المدين (عليّ)</p>
                </div>
                <p class="text-2xl font-black text-rose-700 tracking-tighter">{{ number_format($totalPayable, 2) }}</p>
                <p class="text-[10px] font-bold text-rose-500 mt-1">متبقي للسداد</p>
            </div>

            {{-- تقسيط --}}
            <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-2xl p-5 border border-amber-100 shadow-sm">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 bg-amber-100 rounded-xl flex items-center justify-center text-base">🛒</div>
                    <p class="text-[10px] font-black text-amber-700 uppercase tracking-widest">أقساط شراء</p>
                </div>
                <p class="text-2xl font-black text-amber-700 tracking-tighter">{{ number_format($totalInstallment, 2) }}</p>
                <p class="text-[10px] font-bold text-amber-500 mt-1">متبقي من الأقساط</p>
            </div>

            {{-- قروض --}}
            <div class="bg-gradient-to-br from-violet-50 to-purple-50 rounded-2xl p-5 border border-violet-100 shadow-sm">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 bg-violet-100 rounded-xl flex items-center justify-center text-base">📋</div>
                    <p class="text-[10px] font-black text-violet-700 uppercase tracking-widest">قروض</p>
                </div>
                <p class="text-2xl font-black text-violet-700 tracking-tighter">{{ number_format($totalLoan, 2) }}</p>
                <p class="text-[10px] font-bold text-violet-500 mt-1">متبقي من القروض</p>
            </div>
        </div>

        {{-- ===================== ENTRIES LIST ===================== --}}
        @if($entries->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-100 py-24 text-center shadow-sm">
                <div class="text-6xl mb-4 opacity-20">📒</div>
                <p class="text-slate-400 font-black text-lg">الديوان فارغ</p>
                <p class="text-slate-300 text-sm font-bold mt-2">أضف أول قيد لمتابعة ديونك ومدائنك</p>
                <button @click="showAdd = true" class="mt-6 text-sm font-black text-indigo-600 hover:underline">+ إضافة قيد جديد</button>
            </div>
        @else
            {{-- Overdue first --}}
            @foreach(['overdue' => ['label' => 'متأخرة ⚠️', 'bg' => 'bg-rose-50', 'border' => 'border-rose-200'], 'active' => ['label' => 'نشطة', 'bg' => 'bg-white', 'border' => 'border-slate-100'], 'partial' => ['label' => 'مدفوعة جزئياً', 'bg' => 'bg-amber-50/50', 'border' => 'border-amber-100'], 'settled' => ['label' => 'مسدّدة ✅', 'bg' => 'bg-slate-50/50', 'border' => 'border-slate-100']] as $status => $config)
                @php $group = $entries->where('status', $status); @endphp
                @if($group->isNotEmpty())
                    <div>
                        <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-3">{{ $config['label'] }} ({{ $group->count() }})</h3>
                        <div class="space-y-3">
                            @foreach($group as $entry)
                            <div class="{{ $config['bg'] }} rounded-2xl border {{ $config['border'] }} p-5 shadow-sm hover:shadow-md transition-all">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-4 flex-1 min-w-0">
                                        {{-- Type Badge --}}
                                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0
                                            @if($entry->type === 'receivable') bg-emerald-100
                                            @elseif($entry->type === 'payable') bg-rose-100
                                            @elseif($entry->type === 'installment') bg-amber-100
                                            @else bg-violet-100 @endif">
                                            {{ $entry->type_icon }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                                <h4 class="font-black text-slate-900 text-base truncate">{{ $entry->party_name }}</h4>
                                                <span class="text-[10px] font-black px-2 py-0.5 rounded-lg
                                                    @if($entry->type === 'receivable') bg-emerald-100 text-emerald-700
                                                    @elseif($entry->type === 'payable') bg-rose-100 text-rose-700
                                                    @elseif($entry->type === 'installment') bg-amber-100 text-amber-700
                                                    @else bg-violet-100 text-violet-700 @endif">
                                                    {{ $entry->type_label }}
                                                </span>
                                                @if($entry->type === 'receivable')
                                                    <span class="text-[10px] font-black text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg">مديني لي</span>
                                                @elseif($entry->type === 'payable')
                                                    <span class="text-[10px] font-black text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg">أنا مدين له</span>
                                                @endif
                                            </div>
                                            @if($entry->description)
                                                <p class="text-xs font-bold text-slate-500 truncate">{{ $entry->description }}</p>
                                            @endif
                                            <div class="flex flex-wrap items-center gap-3 mt-2">
                                                @if($entry->due_date)
                                                    <span class="text-[10px] font-bold text-slate-400">
                                                        📅 الاستحقاق: {{ $entry->due_date->format('d/m/Y') }}
                                                        @if($entry->due_date->isPast() && $entry->status !== 'settled')
                                                            <span class="text-rose-500 font-black">(متأخر {{ $entry->due_date->diffForHumans() }})</span>
                                                        @endif
                                                    </span>
                                                @endif
                                                @if($entry->installment_count)
                                                    <span class="text-[10px] font-bold text-slate-400">🗓️ {{ $entry->installment_count }} قسط × {{ number_format($entry->installment_amount, 0) }}</span>
                                                @endif
                                                @if($entry->party_phone)
                                                    <a href="tel:{{ $entry->party_phone }}" class="text-[10px] font-bold text-indigo-500 hover:underline">📞 {{ $entry->party_phone }}</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Amount & Actions --}}
                                    <div class="flex flex-col items-end gap-3 flex-shrink-0">
                                        <div class="text-left">
                                            <p class="text-xl font-black tracking-tighter
                                                @if($entry->status === 'settled') text-slate-400 line-through
                                                @elseif($entry->type === 'receivable') text-emerald-600
                                                @else text-rose-600 @endif">
                                                {{ number_format($entry->remaining_amount, 2) }}
                                                <span class="text-xs opacity-60">{{ $entry->currency }}</span>
                                            </p>
                                            @if($entry->paid_amount > 0)
                                                <p class="text-[10px] font-bold text-slate-400">من {{ number_format($entry->total_amount, 2) }}</p>
                                            @endif
                                        </div>

                                        {{-- Progress Bar --}}
                                        @if($entry->total_amount > 0)
                                            <div class="w-28">
                                                <div class="h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                                    <div class="h-full rounded-full transition-all
                                                        @if($entry->status === 'settled') bg-emerald-500
                                                        @elseif($entry->type === 'receivable') bg-emerald-400
                                                        @else bg-rose-400 @endif"
                                                        style="width: {{ $entry->progress_percent }}%">
                                                    </div>
                                                </div>
                                                <p class="text-[9px] font-black text-slate-400 mt-0.5 text-left">{{ $entry->progress_percent }}%</p>
                                            </div>
                                        @endif

                                        <div class="flex items-center gap-1">
                                            <a href="{{ route('ledger.show', $entry->id) }}"
                                               class="px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg text-[11px] font-black hover:bg-indigo-100 transition-all">
                                                تفاصيل
                                            </a>
                                            @if($entry->status !== 'settled')
                                                <a href="{{ route('ledger.show', $entry->id) }}"
                                                   class="px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-lg text-[11px] font-black hover:bg-emerald-100 transition-all">
                                                    + دفعة
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    </div>

    {{-- ===================== ADD MODAL ===================== --}}
    <div x-show="showAdd" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl text-right overflow-y-auto max-h-[95vh]" @click.away="showAdd = false">
            <div class="sticky top-0 bg-white border-b border-slate-100 px-8 py-5 flex items-center justify-between rounded-t-3xl">
                <h3 class="text-xl font-black text-gray-900">إضافة قيد جديد</h3>
                <button @click="showAdd = false" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-600 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('ledger.store') }}" method="POST" class="p-8 space-y-5">
                @csrf

                {{-- Type Selection --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">نوع القيد</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="receivable" x-model="addType" class="hidden peer">
                            <div class="py-3 px-4 rounded-2xl border-2 border-transparent bg-slate-50 text-center transition-all peer-checked:border-emerald-400 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 font-black text-sm text-slate-600">
                                💸 مديني (لي عندهم)
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="payable" x-model="addType" class="hidden peer">
                            <div class="py-3 px-4 rounded-2xl border-2 border-transparent bg-slate-50 text-center transition-all peer-checked:border-rose-400 peer-checked:bg-rose-50 peer-checked:text-rose-700 font-black text-sm text-slate-600">
                                🏦 أنا المدين (عليّ)
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="installment" x-model="addType" class="hidden peer">
                            <div class="py-3 px-4 rounded-2xl border-2 border-transparent bg-slate-50 text-center transition-all peer-checked:border-amber-400 peer-checked:bg-amber-50 peer-checked:text-amber-700 font-black text-sm text-slate-600">
                                🛒 تقسيط شراء
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="loan" x-model="addType" class="hidden peer">
                            <div class="py-3 px-4 rounded-2xl border-2 border-transparent bg-slate-50 text-center transition-all peer-checked:border-violet-400 peer-checked:bg-violet-50 peer-checked:text-violet-700 font-black text-sm text-slate-600">
                                📋 قرض
                            </div>
                        </label>
                    </div>
                    {{-- Context hint --}}
                    <p class="text-[11px] font-bold mt-2 px-1"
                       :class="{
                           'text-emerald-600': addType === 'receivable',
                           'text-rose-600': addType === 'payable',
                           'text-amber-600': addType === 'installment',
                           'text-violet-600': addType === 'loan'
                       }"
                       x-text="{
                           receivable: '✅ أنت الدائن — الطرف الآخر مدين لك بهذا المبلغ',
                           payable: '⚠️ أنت المدين — عليك سداد هذا المبلغ للطرف الآخر',
                           installment: '🛒 شراء بالتقسيط — حدد المبلغ الإجمالي وعدد الأقساط',
                           loan: '📋 قرض — حدد إذا كنت المقرِض أو المقترِض في الملاحظات'
                       }[addType]">
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">
                            <span x-text="addType === 'receivable' ? 'اسم المدين (من عنده الدين)' : (addType === 'payable' ? 'اسم الدائن (من أنا مدين له)' : 'الجهة / الشركة / الشخص')"></span>
                        </label>
                        <input type="text" name="party_name" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="الاسم الكامل...">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">رقم الهاتف (اختياري)</label>
                        <input type="text" name="party_phone" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="+963...">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">الوصف / السبب</label>
                    <input type="text" name="description" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="مثلاً: قرض للمشروع، شراء سيارة، قرض شخصي...">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">المبلغ الإجمالي</label>
                        <input type="number" name="total_amount" step="0.01" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-xl focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">العملة</label>
                        <select name="currency" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="USD">USD - دولار</option>
                            <option value="SYP">SYP - ليرة سورية</option>
                            <option value="TRY">TRY - ليرة تركية</option>
                            <option value="SAR">SAR - ريال</option>
                            <option value="EUR">EUR - يورو</option>
                        </select>
                    </div>
                </div>

                {{-- Installment fields --}}
                <div x-show="addType === 'installment'" class="bg-amber-50 rounded-2xl p-4 border border-amber-100 space-y-4" x-cloak>
                    <p class="text-[10px] font-black text-amber-700 uppercase tracking-widest">تفاصيل التقسيط</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">عدد الأقساط</label>
                            <input type="number" name="installment_count" min="1" class="w-full bg-white border-0 rounded-xl p-3 font-bold text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="مثلاً: 12">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">قيمة القسط الواحد</label>
                            <input type="number" name="installment_amount" step="0.01" class="w-full bg-white border-0 rounded-xl p-3 font-bold text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="0.00">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">تاريخ البدء</label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">تاريخ الاستحقاق</label>
                        <input type="date" name="due_date" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">ملاحظات إضافية</label>
                    <textarea name="notes" rows="2" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none resize-none" placeholder="أي تفاصيل إضافية..."></textarea>
                </div>

                <button type="submit"
                    class="w-full py-4 rounded-2xl font-black text-base text-white shadow-lg transition-all"
                    :class="{
                        'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-500/20': addType === 'receivable',
                        'bg-rose-600 hover:bg-rose-700 shadow-rose-500/20': addType === 'payable',
                        'bg-amber-500 hover:bg-amber-600 shadow-amber-500/20': addType === 'installment',
                        'bg-violet-600 hover:bg-violet-700 shadow-violet-500/20': addType === 'loan'
                    }">
                    ✓ حفظ القيد
                </button>
            </form>
        </div>
    </div>

</div>
</x-app-layout>
