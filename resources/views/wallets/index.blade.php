<x-app-layout>
    <div class="py-12 px-6" x-data="{ showCreateModal: false }">
        <div class="max-w-7xl mx-auto space-y-12">
            
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h2 class="text-4xl font-black text-gray-900 tracking-tight">المحافظ الشخصية 💰</h2>
                    <p class="text-gray-500 font-bold mt-2">أدر أموالك الشخصية، مدخراتك، ومحافظك الرقمية بخصوصية تامة.</p>
                </div>
                <button @click="showCreateModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-5 rounded-[2rem] font-black text-lg shadow-xl shadow-indigo-500/20 transition-all hover:scale-105 flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    إنشاء محفظة جديدة
                </button>
            </div>

            <!-- Wallets Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($wallets as $wallet)
                    <a href="{{ route('wallets.show', $wallet->id) }}" class="premium-card p-10 bg-white hover:border-indigo-100 transition-all group relative overflow-hidden">
                        <div class="absolute -right-20 -top-20 w-40 h-40 bg-indigo-50 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        
                        <div class="flex justify-between items-start mb-10 relative z-10">
                            <div class="w-16 h-16 bg-gray-50 rounded-3xl flex items-center justify-center text-3xl group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm">
                                💳
                            </div>
                            <span class="px-4 py-2 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-xl">{{ $wallet->currency }}</span>
                        </div>

                        <div class="relative z-10">
                            <h3 class="text-xl font-black text-gray-900 mb-1">{{ $wallet->name }}</h3>
                            @if($wallet->custodian_name)
                                <p class="text-[10px] font-black text-amber-600 bg-amber-50 px-3 py-1 rounded-lg inline-block mb-4">بعهدة: {{ $wallet->custodian_name }}</p>
                            @endif
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-6">الرصيد المتاح</p>
                            <p class="text-4xl font-black text-gray-900 tracking-tighter group-hover:text-indigo-600 transition-colors">
                                ${{ number_format($wallet->balance, 2) }}
                            </p>
                        </div>

                        <div class="mt-10 pt-8 border-t border-gray-50 flex justify-between items-center relative z-10">
                            <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest flex items-center gap-2">
                                عرض التفاصيل
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </span>
                            <div class="flex -space-x-2">
                                <div class="w-8 h-8 rounded-full border-2 border-white bg-emerald-100 flex items-center justify-center text-[10px]">📈</div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-32 text-center bg-gray-50 rounded-[4rem] border-2 border-dashed border-gray-200">
                        <div class="text-6xl mb-6 text-gray-300">🕳️</div>
                        <h3 class="text-2xl font-black text-gray-400">لا توجد محافظ شخصية مضافة بعد</h3>
                        <p class="text-gray-400 mt-2 font-bold italic">ابدأ بإنشاء محفظتك الأولى لمتابعة أموالك الخاصة.</p>
                    </div>
                @endforelse
            </div>

            <!-- Create Wallet Modal -->
            <div x-show="showCreateModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
                <div class="bg-white rounded-[4rem] w-full max-w-md p-12 shadow-2xl relative text-right" @click.away="showCreateModal = false">
                    <div class="flex justify-between items-center mb-10">
                        <h3 class="text-3xl font-black text-gray-900">محفظة جديدة</h3>
                        <button @click="showCreateModal = false" class="w-12 h-12 bg-gray-50 text-gray-400 rounded-2xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-600 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form action="{{ route('wallets.store') }}" method="POST" class="space-y-8">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-4 mr-2 tracking-widest">اسم المحفظة (مثلاً: كاش شخصي)</label>
                            <input type="text" name="name" required class="w-full premium-input" placeholder="مثلاً: محفظة الطوارئ">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-4 mr-2 tracking-widest">اسم الشخص المسؤول (اختياري)</label>
                            <input type="text" name="custodian_name" class="w-full premium-input" placeholder="مثلاً: أحمد المندوب">
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-4 mr-2 tracking-widest">الرصيد الافتتاحي</label>
                                <input type="number" name="balance" value="0" required class="w-full premium-input">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-4 mr-2 tracking-widest text-right">العملة</label>
                                <select name="currency" class="w-full premium-input">
                                    <option value="USD">USD - دولار</option>
                                    <option value="SYP">SYP - ليرة</option>
                                    <option value="AED">AED - درهم</option>
                                    <option value="SAR">SAR - ريال</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 text-white py-6 rounded-[2.5rem] font-black text-xl shadow-xl shadow-indigo-500/20 hover:bg-indigo-700 transition-all">إنشاء المحفظة</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
