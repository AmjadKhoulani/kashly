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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                @php $colors = ['card-blue', 'card-green', 'card-yellow', 'card-red']; @endphp
                @forelse($wallets as $index => $wallet)
                    <a href="{{ route('wallets.show', $wallet->id) }}" class="premium-card {{ $colors[$index % 4] }} p-10 hover:scale-[1.03] transition-all group relative overflow-hidden block">
                        <div class="absolute -right-20 -top-20 w-48 h-48 bg-white/30 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                        
                        <div class="flex justify-between items-start mb-12 relative z-10">
                            <div class="w-20 h-20 bg-white/60 backdrop-blur-md rounded-3xl flex items-center justify-center text-4xl group-hover:scale-110 group-hover:rotate-6 transition-all duration-500 shadow-sm border-2 border-white/80">
                                💳
                            </div>
                            <span class="px-5 py-2 bg-white/60 backdrop-blur-md text-slate-700 text-[10px] font-black uppercase tracking-widest rounded-2xl border-2 border-white/80 shadow-sm">{{ $wallet->currency }}</span>
                        </div>

                        <div class="relative z-10">
                            <h3 class="text-2xl font-black text-slate-900 mb-2">{{ $wallet->name }}</h3>
                            @if($wallet->custodian_name)
                                <p class="text-[10px] font-black text-amber-700 bg-white/60 px-4 py-2 rounded-xl inline-block mb-6 border border-white shadow-sm">بعهدة: {{ $wallet->custodian_name }}</p>
                            @endif
                            <div class="space-y-1">
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">الرصيد المتاح حالياً</p>
                                <p class="text-5xl font-black text-slate-900 tracking-tighter group-hover:text-indigo-600 transition-colors">
                                    {{ number_format($wallet->balance, 0) }} <span class="text-xl opacity-60">{{ $wallet->currency }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="mt-12 pt-8 border-t border-white/50 flex justify-between items-center relative z-10">
                            <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest flex items-center gap-3">
                                <span class="w-8 h-8 bg-indigo-50 rounded-full flex items-center justify-center shadow-inner">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </span>
                                عرض التفاصيل
                            </span>
                            <div class="flex -space-x-2">
                                <div class="w-10 h-10 rounded-full border-4 border-white bg-emerald-100 flex items-center justify-center text-sm shadow-md">📈</div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-32 text-center bg-slate-100/50 rounded-[4rem] border-4 border-dashed border-slate-200">
                        <div class="text-8xl mb-8 opacity-20">🕳️</div>
                        <h3 class="text-3xl font-black text-slate-400">لا توجد محافظ شخصية</h3>
                        <p class="text-slate-400 mt-4 font-black italic uppercase tracking-widest text-xs">ابدأ بإنشاء محفظتك الأولى لمتابعة أموالك الخاصة.</p>
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
