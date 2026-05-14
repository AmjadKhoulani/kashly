<x-app-layout>
    <div class="py-12 px-6" x-data="{ showModal: false, filter: 'all' }">
        <div class="max-w-7xl mx-auto space-y-10">
            
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h2 class="text-4xl font-black text-gray-900 tracking-tight">العمليات المالية</h2>
                    <p class="text-gray-500 font-bold mt-2">سجل ذكي لجميع التدفقات المالية وتوزيعات الأرباح.</p>
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
                <div class="lg:col-span-3 bg-white p-4 rounded-[2.5rem] border border-gray-50 shadow-sm flex items-center gap-2 overflow-x-auto">
                    <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'bg-gray-50 text-gray-500'" class="px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">الكل</button>
                    <button @click="filter = 'income'" :class="filter === 'income' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'bg-gray-50 text-gray-500'" class="px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">الإيرادات</button>
                    <button @click="filter = 'expense'" :class="filter === 'expense' ? 'bg-rose-500 text-white shadow-lg shadow-rose-500/20' : 'bg-gray-50 text-gray-500'" class="px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">المصاريف</button>
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
            <div class="space-y-4">
                @forelse($transactions as $transaction)
                    <div x-show="filter === 'all' || filter === '{{ $transaction->type }}'" class="bg-white p-8 rounded-[3rem] border border-gray-50 shadow-sm hover:shadow-xl hover:shadow-gray-500/5 transition-all group">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <div class="flex items-center gap-6">
                                <div class="w-16 h-16 {{ $transaction->type == 'income' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} rounded-[1.5rem] flex items-center justify-center text-3xl transition-transform group-hover:scale-110">
                                    {{ $transaction->type == 'income' ? '📈' : '📉' }}
                                </div>
                                <div>
                                    <p class="text-xl font-black text-gray-900">{{ $transaction->description ?: $transaction->category }}</p>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $transaction->transaction_date->format('Y/m/d') }}</span>
                                        <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                                        <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">{{ $transaction->category }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex flex-col md:items-end gap-2 w-full md:w-auto border-t md:border-t-0 border-gray-50 pt-4 md:pt-0">
                                <div class="flex items-center gap-3">
                                    <p class="text-2xl font-black {{ $transaction->type == 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $transaction->type == 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                    </p>
                                    <div class="px-3 py-1 bg-gray-50 rounded-lg text-[10px] font-black text-gray-400 uppercase">
                                        {{ $transaction->transactionable->name ?? 'مصدر خارجي' }}
                                    </div>
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
            <div class="bg-white rounded-[4rem] w-full max-w-xl p-12 shadow-2xl relative text-right" @click.away="showModal = false">
                <div class="flex justify-between items-center mb-10">
                    <h3 class="text-3xl font-black text-gray-900">تسجيل عملية مالية</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-900 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form action="{{ route('transactions.store') }}" method="POST" class="space-y-8">
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
                            <input type="text" name="category" required class="w-full bg-gray-50 border-0 rounded-[2rem] p-6 font-bold text-lg focus:ring-4 focus:ring-indigo-600/10 transition-all" placeholder="أرباح، مصاريف...">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest mr-2">التاريخ</label>
                            <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full bg-gray-50 border-0 rounded-[2rem] p-6 font-bold text-lg focus:ring-4 focus:ring-indigo-600/10 transition-all">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 text-white py-6 rounded-[2.5rem] font-black text-xl shadow-xl shadow-indigo-500/20 hover:bg-indigo-700 transition-all">تأكيد العملية</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
