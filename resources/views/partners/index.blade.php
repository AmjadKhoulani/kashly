<x-app-layout>
    <div class="py-12 px-6">
        <div class="max-w-7xl mx-auto space-y-12">
            
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
                <div>
                    <h2 class="text-5xl font-black text-slate-900 tracking-tight">إدارة الشركاء والمساهمين 🤝</h2>
                    <p class="text-slate-500 font-bold mt-3 text-lg">إدارة قائمة الشركاء، حساباتهم، وتوزيع حصصهم المالية في الصناديق المختلفة.</p>
                </div>
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-5 rounded-[2.5rem] text-lg font-black shadow-2xl shadow-indigo-500/30 flex items-center transition-all hover:scale-105 active:scale-95">
                    <svg class="w-6 h-6 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                    إضافة شريك جديد
                </button>
            </div>

            @if (session('status'))
                <div class="bg-emerald-50 border-2 border-emerald-100 text-emerald-700 px-10 py-6 rounded-[2.5rem] font-black shadow-sm flex items-center gap-4">
                    <span class="text-2xl">✅</span>
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                @forelse($partners as $partner)
                    <div class="premium-card p-12 flex flex-col justify-between group relative overflow-hidden bg-white border-2 border-slate-100 hover:border-indigo-200 transition-all duration-500">
                        <div class="absolute -right-6 -top-6 w-32 h-32 bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/10 transition-all duration-700"></div>
                        
                        <div class="relative">
                            <div class="flex justify-between items-start mb-10">
                                <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-indigo-600 text-white rounded-[2rem] flex items-center justify-center text-3xl font-black shadow-2xl shadow-indigo-500/30 transform group-hover:rotate-6 transition-transform">
                                    {{ mb_substr($partner->name, 0, 1) }}
                                </div>
                                <form action="{{ route('partners.destroy', $partner) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الشريك؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-sm border border-rose-100">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>

                            <h4 class="text-3xl font-black text-slate-900 mb-2 tracking-tight group-hover:text-indigo-600 transition-colors">{{ $partner->name }}</h4>
                            <p class="text-sm font-black text-slate-400 mb-10 uppercase tracking-widest">{{ $partner->email }}</p>

                            <div class="space-y-8 mb-10">
                                <p class="text-xs text-slate-400 font-black uppercase tracking-widest flex items-center gap-3">
                                    <span class="w-2 h-2 bg-indigo-600 rounded-full shadow-lg shadow-indigo-500/40"></span>
                                    المساهمات النشطة
                                </p>
                                <div class="space-y-4">
                                    @foreach($partner->equities as $equity)
                                        <div class="flex justify-between items-center bg-slate-50/50 p-6 rounded-[1.5rem] border-2 border-slate-50 hover:bg-white hover:border-slate-100 transition-all shadow-sm">
                                            <span class="text-sm font-black text-slate-600">{{ $equity->equitable?->name ?? 'كيان محذوف' }}</span>
                                            <span class="text-xs font-black text-indigo-700 bg-white border border-indigo-100 px-4 py-1.5 rounded-xl shadow-sm">{{ number_format($equity->percentage, 1) }}%</span>
                                        </div>
                                    @endforeach
                                    @if($partner->equities->isEmpty())
                                        <div class="text-center py-10 border-4 border-dashed border-slate-50 rounded-[2.5rem]">
                                            <p class="text-xs text-slate-300 font-black uppercase tracking-widest italic">لا توجد مساهمات حالية</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Account Status -->
                        <div class="pt-10 border-t-2 border-slate-50 mt-auto">
                            @if($partner->linkedUser)
                                <div class="flex items-center justify-between bg-emerald-50/50 p-6 rounded-[2rem] border border-emerald-100">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-white text-emerald-600 rounded-xl flex items-center justify-center text-xl shadow-sm border border-emerald-50">👤</div>
                                        <div>
                                            <p class="text-xs font-black text-slate-400 uppercase tracking-widest">حساب نشط ✅</p>
                                            <p class="text-sm font-black text-slate-700 mt-1">{{ $partner->linkedUser->email }}</p>
                                        </div>
                                    </div>
                                    <span class="w-3 h-3 bg-emerald-500 rounded-full shadow-lg shadow-emerald-500/40 animate-pulse"></span>
                                </div>
                            @else
                                <div class="bg-amber-50/50 p-6 rounded-[2.5rem] border-2 border-amber-50">
                                    <p class="text-xs text-amber-700 font-black uppercase tracking-widest mb-5 flex items-center gap-2">
                                        🔗 ربط حساب دخول
                                    </p>
                                    <form action="{{ route('partners.link', $partner) }}" method="POST" class="flex gap-3">
                                        @csrf
                                        <input type="email" name="email" required placeholder="example@mail.com" class="flex-1 bg-white border-2 border-amber-100 rounded-2xl px-5 py-4 text-sm font-black shadow-inner focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none transition-all">
                                        <button type="submit" class="bg-indigo-600 text-white px-6 py-4 rounded-2xl text-xs font-black shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 transition-all uppercase tracking-widest">ربط</button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <div class="mt-10 flex justify-between items-end bg-slate-50 p-8 rounded-[2.5rem] border-2 border-slate-50 group-hover:bg-white group-hover:border-slate-100 transition-all duration-500">
                            <div>
                                <p class="text-xs text-slate-400 font-black uppercase tracking-widest mb-2">إجمالي الاستثمار</p>
                                <p class="text-4xl font-black text-slate-900 tracking-tighter">${{ number_format($partner->equities->sum('amount'), 0) }}</p>
                            </div>
                            <div class="text-left">
                                <p class="text-xs text-slate-400 font-black uppercase tracking-widest mb-2">القيمة الحالية</p>
                                @php
                                    $currentVal = $partner->equities->sum(function($eq) {
                                        return $eq->equitable ? ($eq->percentage / 100) * $eq->equitable->current_value : 0;
                                    });
                                @endphp
                                <p class="text-2xl font-black text-emerald-600 tracking-tighter">${{ number_format($currentVal, 0) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full premium-card p-32 text-center bg-white border-4 border-dashed border-slate-100">
                        <div class="text-8xl mb-8">🏜️</div>
                        <h4 class="text-4xl font-black text-slate-900 mb-4">لا يوجد شركاء بعد</h4>
                        <p class="text-slate-400 font-black text-xl">ابدأ بإضافة أول شريك خارجي لإدارة مساهماته وحصصه الاستثمارية.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
