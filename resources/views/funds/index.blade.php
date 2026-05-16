<x-app-layout>
    <div class="py-12 px-6" x-data="{ showModal: false }">
        <div class="max-w-7xl mx-auto space-y-12">

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-100 p-6 rounded-[2rem] flex items-center gap-4 shadow-sm">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-xl shadow-sm">⚠️</div>
                    <div>
                        <h4 class="text-sm font-black text-rose-900">حدث خطأ أثناء المعالجة</h4>
                        <p class="text-xs font-bold text-rose-600 mt-1">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-100 p-6 rounded-[2rem] flex items-center gap-4 shadow-sm">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-xl shadow-sm">✅</div>
                    <div>
                        <h4 class="text-sm font-black text-emerald-900">تمت العملية بنجاح</h4>
                        <p class="text-xs font-bold text-emerald-600 mt-1">{{ session('success') }}</p>
                    </div>
                </div>
            @endif
            
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
                <div>
                    <h2 class="text-5xl font-black text-slate-900 tracking-tight">الكيانات الاستثمارية</h2>
                    <p class="text-slate-500 font-bold mt-3 text-lg">تتبع نمو محافظك العقارية والتجارية وإدارة حصص الشركاء باحترافية.</p>
                </div>
                <button @click="showModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-5 rounded-[2.5rem] text-lg font-black shadow-2xl shadow-indigo-500/30 flex items-center transition-all hover:scale-105 active:scale-95">
                    <svg class="w-6 h-6 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                    إنشاء كيان استثماري
                </button>
            </div>

            <!-- Global Stats Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="premium-card p-10 flex items-center justify-between border-2 border-slate-100 bg-white">
                    <div>
                        <p class="text-xs text-slate-400 font-black uppercase tracking-widest mb-2">إجمالي رأس المال</p>
                        <p class="text-4xl font-black text-slate-900 tracking-tighter">${{ number_format($funds->sum('capital'), 0) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-3xl shadow-inner border border-indigo-100">🏛️</div>
                </div>
                <div class="premium-card p-10 flex items-center justify-between border-2 border-slate-100 bg-white">
                    <div>
                        <p class="text-xs text-slate-400 font-black uppercase tracking-widest mb-2">القيمة السوقية الحالية</p>
                        <p class="text-4xl font-black text-indigo-600 tracking-tighter">${{ number_format($funds->sum('current_value'), 0) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center text-3xl shadow-inner border border-emerald-100">📈</div>
                </div>
                <div class="premium-card p-10 flex items-center justify-between border-2 border-slate-100 bg-white">
                    <div>
                        <p class="text-xs text-slate-400 font-black uppercase tracking-widest mb-2">العائد الإجمالي</p>
                        @php
                            $totalProfit = $funds->sum('current_value') - $funds->sum('capital');
                            $profitPercent = $funds->sum('capital') > 0 ? ($totalProfit / $funds->sum('capital')) * 100 : 0;
                        @endphp
                        <p class="text-4xl font-black text-emerald-600 tracking-tighter">+{{ number_format($profitPercent, 1) }}%</p>
                    </div>
                    <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center text-3xl shadow-inner border border-emerald-100">💰</div>
                </div>
            </div>

            <!-- Funds Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                @foreach($funds as $fund)
                    <a href="{{ route('funds.show', $fund->id) }}" class="premium-card p-12 group relative overflow-hidden block border-2 border-slate-100 bg-white hover:border-indigo-200 transition-all duration-500">
                        <div class="absolute -right-10 -top-10 w-48 h-48 bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/10 transition-all duration-700"></div>
                        
                        <div class="flex justify-between items-start mb-14 relative z-10">
                            <div class="w-24 h-24 bg-slate-50 text-slate-900 rounded-[2.5rem] flex items-center justify-center text-5xl group-hover:scale-110 group-hover:rotate-6 transition-all duration-500 border-2 border-white shadow-lg">
                                {{ $fund->icon ?? '🏘️' }}
                            </div>
                            <span class="px-6 py-2.5 {{ $fund->status == 'active' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-slate-50 text-slate-400 border-slate-100' }} text-xs font-black uppercase rounded-xl tracking-widest border shadow-sm">
                                {{ $fund->status == 'active' ? '● نشط حالياً' : 'مغلق' }}
                            </span>
                        </div>

                        <div class="relative z-10">
                            <h3 class="text-4xl font-black text-slate-900 mb-6 tracking-tight group-hover:text-indigo-600 transition-colors">{{ $fund->name }}</h3>
                            <div class="flex items-center gap-8 mb-12">
                                <div class="flex -space-x-4 space-x-reverse overflow-hidden">
                                    @for($i = 0; $i < 3; $i++)
                                        <div class="inline-block h-10 w-10 rounded-full ring-4 ring-white bg-slate-100 flex items-center justify-center text-xs font-black text-slate-400 border border-slate-200 shadow-sm">P</div>
                                    @endfor
                                </div>
                                <span class="text-sm font-black text-slate-400 uppercase tracking-widest">إدارة الشركاء والمساهمات</span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-10 border-t-2 border-slate-50 pt-12">
                                <div>
                                    <p class="text-xs text-slate-400 font-black uppercase mb-3 tracking-widest">رأس المال المستثمر</p>
                                    <p class="text-3xl font-black text-slate-900 tracking-tighter">${{ number_format($fund->capital, 0) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-black uppercase mb-3 tracking-widest">القيمة الحالية</p>
                                    <div class="flex items-center gap-3">
                                        <p class="text-3xl font-black text-indigo-600 tracking-tighter">${{ number_format($fund->current_value, 0) }}</p>
                                        <span class="text-xs font-black text-emerald-600 bg-emerald-50 px-3 py-1 rounded-lg border border-emerald-100 shadow-sm">
                                            +{{ number_format((($fund->current_value - $fund->capital) / max($fund->capital, 1)) * 100, 1) }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-12 h-4 bg-slate-50 rounded-full overflow-hidden shadow-inner border border-slate-100">
                            @php $percent = min(100, ($fund->current_value / max($fund->capital, 1)) * 100); @endphp
                            <div class="h-full bg-gradient-to-l from-indigo-600 to-indigo-400 rounded-full transition-all duration-1000 shadow-lg shadow-indigo-500/20" style="width: {{ $percent }}%"></div>
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
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">رأس المال التأسيسي (اختياري)</label>
                        <input type="number" name="capital" class="w-full premium-input text-2xl" placeholder="0.00">
                        <p class="text-[10px] text-gray-400 mt-2 mr-2">يمكنك تركه فارغاً إذا كان المشروع قيد التأسيس، وسيتم احتسابه من "مصاريف رأس المال".</p>
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
