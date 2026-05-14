<x-app-layout>
    <div class="py-12 px-6" x-data="{ showModal: false }">
        <div class="max-w-7xl mx-auto space-y-12">
            
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h2 class="text-4xl font-black text-gray-900 tracking-tight">الكيانات الاستثمارية</h2>
                    <p class="text-gray-500 font-bold mt-2">تتبع نمو محافظك العقارية والتجارية وإدارة حصص الشركاء.</p>
                </div>
                <button @click="showModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-[2rem] text-sm font-black shadow-xl shadow-indigo-500/20 flex items-center transition-all hover:scale-105">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                    إنشاء كيان استثماري جديد
                </button>
            </div>

            <!-- Global Stats Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="premium-card p-8 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">إجمالي رأس المال</p>
                        <p class="text-3xl font-black text-gray-900">${{ number_format($funds->sum('capital'), 0) }}</p>
                    </div>
                    <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-2xl shadow-sm">🏛️</div>
                </div>
                <div class="premium-card p-8 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">القيمة السوقية الحالية</p>
                        <p class="text-3xl font-black text-indigo-600">${{ number_format($funds->sum('current_value'), 0) }}</p>
                    </div>
                    <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-2xl shadow-sm">📈</div>
                </div>
                <div class="premium-card p-8 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">العائد الإجمالي</p>
                        @php
                            $totalProfit = $funds->sum('current_value') - $funds->sum('capital');
                            $profitPercent = $funds->sum('capital') > 0 ? ($totalProfit / $funds->sum('capital')) * 100 : 0;
                        @endphp
                        <p class="text-3xl font-black text-emerald-600">+{{ number_format($profitPercent, 1) }}%</p>
                    </div>
                    <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-2xl shadow-sm">💰</div>
                </div>
            </div>

            <!-- Funds Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                @foreach($funds as $fund)
                    <a href="{{ route('funds.show', $fund->id) }}" class="premium-card p-10 group relative overflow-hidden block">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/10 transition-colors"></div>
                        
                        <div class="flex justify-between items-start mb-12 relative z-10">
                            <div class="w-20 h-20 bg-gray-50 text-gray-900 rounded-[2rem] flex items-center justify-center text-4xl group-hover:scale-110 transition-transform duration-500">
                                {{ $fund->icon ?? '🏘️' }}
                            </div>
                            <span class="px-4 py-2 {{ $fund->status == 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-50 text-gray-400' }} text-[10px] font-black uppercase rounded-xl tracking-tighter">
                                {{ $fund->status == 'active' ? '● نشط حالياً' : 'مغلق' }}
                            </span>
                        </div>

                        <div class="relative z-10">
                            <h3 class="text-3xl font-black text-gray-900 mb-4 tracking-tight group-hover:text-indigo-600 transition-colors">{{ $fund->name }}</h3>
                            <div class="flex items-center gap-6 mb-10">
                                <div class="flex -space-x-3 space-x-reverse overflow-hidden">
                                    @for($i = 0; $i < 3; $i++)
                                        <div class="inline-block h-8 w-8 rounded-full ring-4 ring-white bg-gray-100 flex items-center justify-center text-[10px] font-black text-gray-400">P</div>
                                    @endfor
                                </div>
                                <span class="text-xs font-bold text-gray-400">إدارة الشركاء والمساهمات</span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-8 border-t border-gray-50 pt-10">
                                <div>
                                    <p class="text-[10px] text-gray-400 font-black uppercase mb-2 tracking-widest">رأس المال المستثمر</p>
                                    <p class="text-2xl font-black text-gray-900">${{ number_format($fund->capital, 0) }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-black uppercase mb-2 tracking-widest">القيمة الحالية</p>
                                    <div class="flex items-center gap-2">
                                        <p class="text-2xl font-black text-indigo-600">${{ number_format($fund->current_value, 0) }}</p>
                                        <span class="text-[10px] font-black text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded-md">
                                            +{{ number_format((($fund->current_value - $fund->capital) / max($fund->capital, 1)) * 100, 1) }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-10 h-3 bg-gray-50 rounded-full overflow-hidden">
                            @php $percent = min(100, ($fund->current_value / max($fund->capital, 1)) * 100); @endphp
                            <div class="h-full bg-gradient-to-l from-indigo-600 to-indigo-400 rounded-full transition-all duration-1000" style="width: {{ $percent }}%"></div>
                        </div>
                    </a>
                @endforeach
            </div>

        </div>

        <!-- Create Entity Modal -->
        <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
            <div class="bg-white rounded-[4rem] w-full max-w-lg p-12 shadow-2xl relative text-right" @click.away="showModal = false">
                <h3 class="text-3xl font-black text-gray-900 mb-8">إنشاء كيان استثماري</h3>
                <form action="{{ route('funds.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">اسم الكيان</label>
                        <input type="text" name="name" required class="w-full premium-input" placeholder="مثلاً: عمارة الياسمين، محفظة الأسهم...">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">رأس المال التأسيسي ($)</label>
                        <input type="number" name="capital" required class="w-full premium-input text-2xl" placeholder="0.00">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">تكرار توزيع الأرباح</label>
                            <select name="distribution_frequency" class="w-full premium-input">
                                <option value="1">شهري</option>
                                <option value="3">كل 3 أشهر</option>
                                <option value="6">كل 6 أشهر</option>
                                <option value="12">سنوي</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">العملة الأساسية</label>
                            <select name="currency" class="w-full premium-input">
                                <option value="USD">USD</option>
                                <option value="TRY">TRY</option>
                                <option value="SAR">SAR</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 text-white py-6 rounded-[2.5rem] font-black text-xl shadow-xl shadow-indigo-500/20 hover:bg-indigo-700 transition-all">تأكيد الإنشاء</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
