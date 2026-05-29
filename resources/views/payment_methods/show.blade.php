<x-app-layout>
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/20"
     x-data="{ showModal: false, showDeleteModal: false }">

    {{-- ===================== STICKY HEADER ===================== --}}
    @php
        $backUrl = route('wallets.index');
        if ($method->wallet_id) {
            $backUrl = route('wallets.show', $method->wallet_id);
        } elseif ($method->fund_id) {
            $backUrl = route('funds.show', $method->fund_id);
        }
    @endphp
    <div class="bg-white border-b border-slate-100 sticky top-0 z-40 backdrop-blur-xl bg-white/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ $backUrl }}" 
                   class="w-10 h-10 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center hover:bg-slate-200 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h1 class="text-base font-black text-slate-900 leading-none">{{ $method->name }}</h1>
                    <p class="text-[10px] font-bold text-slate-400 mt-0.5">تفاصيل الحساب الفرعي والحركات الداخلية</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button @click="showModal = true"
                    class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black text-sm shadow-lg shadow-indigo-500/20 transition-all hover:scale-105">
                    + تسجيل حركة
                </button>
                <button @click="showDeleteModal = true"
                    class="w-10 h-10 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center hover:bg-rose-100 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-16v1a3 3 0 003 3h10M4 7h16"/></svg>
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-6">

        {{-- ===================== HERO CARD ===================== --}}
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950 rounded-3xl p-8 relative overflow-hidden shadow-xl text-right text-white">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>
            <div class="absolute -left-5 -bottom-5 w-28 h-28 bg-white/5 rounded-full blur-xl"></div>

            <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                <div class="space-y-2 md:col-span-2">
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 bg-white/10 text-white text-[9px] font-black uppercase tracking-widest rounded-xl border border-white/10">
                            @switch($method->type)
                                @case('bank') 🏛️ حساب بنكي فرعي @break
                                @case('credit_card') 💳 بطاقة ائتمانية @break
                                @case('debit_card') 💳 بطاقة دفع فرعية @break
                                @case('cash') 💵 كاش / صندوق فرعي @break
                                @default 🪙 حساب فرعي
                            @endswitch
                        </span>
                        @if($method->wallet)
                            <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 text-[9px] font-black rounded-xl border border-indigo-500/10">
                                🔗 تابع للمحفظة: {{ $method->wallet->name }}
                            </span>
                        @elseif($method->fund)
                            <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 text-[9px] font-black rounded-xl border border-emerald-500/10">
                                🔗 تابع للاستثمار: {{ $method->fund->name }}
                            </span>
                        @endif
                    </div>
                    <p class="text-white/50 text-[10px] font-black uppercase tracking-widest mt-4">الرصيد الحالي للحساب</p>
                    <p class="text-5xl font-black text-white tracking-tighter leading-none">
                        {{ number_format($method->balance, 2) }}
                        <span class="text-lg opacity-60">{{ $method->currency }}</span>
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-4 border-t md:border-t-0 md:border-r border-white/10 pt-6 md:pt-0 md:pr-8">
                    <div>
                        <p class="text-white/40 text-[9px] font-black uppercase tracking-widest mb-1">إجمالي الإيداعات</p>
                        <p class="text-xl font-black text-emerald-400 tracking-tighter">{{ number_format($totalIncome, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-white/40 text-[9px] font-black uppercase tracking-widest mb-1">إجمالي السحوبات</p>
                        <p class="text-xl font-black text-rose-400 tracking-tighter">{{ number_format($totalExpense, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== TRANSACTIONS LOG ===================== --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-50">
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center text-sm">📊</span>
                    العمليات والحركات الداخلية للحساب
                </h3>
                <span class="text-xs font-black text-slate-400 bg-slate-50 px-3 py-1.5 rounded-lg">{{ $transactions->total() }} عملية</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">البيان / التصنيف</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">التاريخ</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">المبلغ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($transactions as $t)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg" 
                                         style="background-color: {{ $t->categoryRelation?->color ?? '#6366f1' }}20; color: {{ $t->categoryRelation?->color ?? '#6366f1' }}">
                                        {{ $t->categoryRelation?->icon ?? '🪙' }}
                                    </div>
                                    <div>
                                        <p class="font-black text-slate-900 text-sm">{{ $t->description ?? ($t->categoryRelation?->name ?? 'بدون بيان') }}</p>
                                        <p class="text-[10px] font-bold text-slate-400">{{ $t->categoryRelation?->name ?? 'بدون تصنيف' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <p class="font-black text-slate-700 text-xs">{{ \Carbon\Carbon::parse($t->transaction_date)->format('Y-m-d') }}</p>
                                <p class="text-[9px] font-bold text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($t->transaction_date)->diffForHumans() }}</p>
                            </td>
                            <td class="px-6 py-4 text-left">
                                <p class="text-sm font-black {{ $t->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $t->type === 'income' ? '+' : '-' }}{{ number_format($t->amount, 2) }}
                                    <span class="text-xs opacity-60">{{ $t->currency }}</span>
                                </p>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-20 text-center text-slate-400 font-bold text-sm">لا توجد حركات داخلية مسجلة بعد لهذا الحساب.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-slate-50">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- ===================== ADD TRANSACTION MODAL ===================== --}}
    <div x-show="showModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-white rounded-3xl w-full max-w-md p-8 shadow-2xl relative text-right" @click.away="showModal = false">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-black text-gray-900">تسجيل حركة جديدة</h3>
                <button @click="showModal = false" class="w-8 h-8 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-600 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="payment_method_id" value="{{ $method->id }}">
                <input type="hidden" name="transactionable_id" value="{{ $method->wallet_id ?? ($method->fund_id ?? '') }}">
                <input type="hidden" name="transactionable_type" value="{{ $method->wallet_id ? 'App\\Models\\Wallet' : 'App\\Models\\InvestmentFund' }}">

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">نوع المعاملة</label>
                    <select name="type" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-base focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="expense">مصروف / سحب</option>
                        <option value="income">إيراد / إيداع</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">المبلغ</label>
                    <input type="number" name="amount" required step="0.01" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-black text-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">التصنيف</label>
                    <select name="category" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}">{{ $cat->icon }} {{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">التاريخ</label>
                    <input type="date" name="transaction_date" required value="{{ date('Y-m-d') }}" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-base focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">البيان / الوصف</label>
                    <input type="text" name="description" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-base focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="تفاصيل المعاملة...">
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-black text-base shadow-lg shadow-indigo-500/20 transition-all">
                    ✓ حفظ الحركة
                </button>
            </form>
        </div>
    </div>

    {{-- ===================== DELETE CONFIRMATION MODAL ===================== --}}
    <div x-show="showDeleteModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4 sm:p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-white rounded-3xl w-full max-w-sm p-8 shadow-2xl relative text-right" @click.away="showDeleteModal = false">
            <h3 class="text-lg font-black text-gray-900 mb-4">حذف الحساب الفرعي</h3>
            <p class="text-sm font-bold text-slate-500 mb-6 leading-relaxed">هل أنت متأكد من حذف هذا الحساب الفرعي نهائياً؟ سيؤدي ذلك لحذف كافة الحركات المسجلة عليه ولا يمكن استعادتها.</p>
            <div class="flex gap-2">
                <form action="{{ route('payment-methods.destroy', $method->id) }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white py-3 rounded-2xl font-black text-sm shadow-lg shadow-rose-500/20 transition-all">حذف نهائي</button>
                </form>
                <button @click="showDeleteModal = false" class="flex-1 bg-slate-100 text-slate-600 hover:bg-slate-200 py-3 rounded-2xl font-black text-sm transition-all">إلغاء</button>
            </div>
        </div>
    </div>

</div>
</x-app-layout>
