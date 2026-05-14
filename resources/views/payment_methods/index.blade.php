<x-app-layout>
    <div class="py-12 px-6" x-data="{ showModal: false }">
        <div class="max-w-7xl mx-auto space-y-12">
            
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h2 class="text-4xl font-black text-gray-900 tracking-tight">الحسابات ووسائل الدفع</h2>
                    <p class="text-gray-500 font-bold mt-2">إدارة حساباتك البنكية، الخزينة، والبطاقات الائتمانية في مكان واحد.</p>
                </div>
                <button @click="showModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-[2rem] text-sm font-black shadow-xl shadow-indigo-500/20 flex items-center transition-all hover:scale-105">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                    إضافة حساب جديد
                </button>
            </div>

            <!-- Payment Methods Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($methods as $method)
                    <div class="premium-card p-10 group relative overflow-hidden">
                        <div class="absolute -right-10 -top-10 w-32 h-32 bg-indigo-500/5 rounded-full blur-3xl"></div>
                        
                        <div class="flex justify-between items-start mb-8 relative z-10">
                            <div class="w-16 h-16 bg-gray-50 text-gray-900 rounded-2xl flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                                @switch($method->type)
                                    @case('bank') 🏛️ @break
                                    @case('cash') 💵 @break
                                    @case('credit_card') 💳 @break
                                    @case('debit_card') 💳 @break
                                    @default 💰
                                @endswitch
                            </div>
                            <form action="{{ route('payment-methods.destroy', $method->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الحساب؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-300 hover:text-rose-500 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>

                        <div class="relative z-10">
                            <h3 class="text-2xl font-black text-gray-900 mb-1">{{ $method->name }}</h3>
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-6">
                                @switch($method->type)
                                    @case('bank') حساب بنكي @break
                                    @case('cash') نقد / خزينة @break
                                    @case('credit_card') بطاقة ائتمان @break
                                    @case('debit_card') بطاقة صراف @break
                                    @default أخرى
                                @endswitch
                            </p>
                            
                            <div class="border-t border-gray-50 pt-6">
                                <p class="text-[10px] text-gray-400 font-black uppercase mb-1">الرصيد الحالي</p>
                                <p class="text-3xl font-black text-indigo-600">{{ number_format($method->balance, 2) }} <span class="text-sm">{{ $method->currency }}</span></p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="lg:col-span-3 premium-card p-24 text-center border-dashed">
                        <div class="text-6xl mb-6">🏦</div>
                        <h3 class="text-2xl font-black text-gray-900 mb-2">لا توجد حسابات مضافة</h3>
                        <p class="text-gray-400 font-bold">ابدأ بإضافة حساباتك البنكية أو محافظك المالية لتتبع مصادر أموالك.</p>
                    </div>
                @endforelse
            </div>

        </div>

        <!-- Add Modal -->
        <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
            <div class="bg-white rounded-[4rem] w-full max-w-lg p-12 shadow-2xl relative text-right" @click.away="showModal = false">
                <h3 class="text-3xl font-black text-gray-900 mb-8">إضافة وسيلة دفع</h3>
                <form action="{{ route('payment-methods.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">اسم الحساب / الوسيلة</label>
                        <input type="text" name="name" required class="w-full bg-gray-50 border-0 rounded-[2rem] p-6 font-bold text-lg" placeholder="مثلاً: بنك الراجحي، خزينة المحل...">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">نوع الحساب</label>
                        <select name="type" class="w-full bg-gray-50 border-0 rounded-[2rem] p-6 font-bold text-lg">
                            <option value="bank">حساب بنكي</option>
                            <option value="cash">نقد / كاش</option>
                            <option value="credit_card">بطاقة ائتمان (Credit)</option>
                            <option value="debit_card">بطاقة دفع (Debit)</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">الرصيد الافتتاحي</label>
                            <input type="number" step="0.01" name="balance" required class="w-full bg-gray-50 border-0 rounded-[2rem] p-6 font-black text-xl" placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">العملة</label>
                            <select name="currency" class="w-full bg-gray-50 border-0 rounded-[2rem] p-6 font-bold text-lg">
                                <option value="USD">USD</option>
                                <option value="TRY">TRY</option>
                                <option value="SAR">SAR</option>
                                <option value="EUR">EUR</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 text-white py-6 rounded-[2.5rem] font-black text-xl shadow-xl shadow-indigo-500/20 hover:bg-indigo-700 transition-all">حفظ الحساب</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
