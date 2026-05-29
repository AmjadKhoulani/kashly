<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/20">
        
        <!-- Sticky Header -->
        <div class="bg-white/95 border-b border-slate-100 sticky top-0 z-40 backdrop-blur-xl py-6 px-6">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight">إدارة الشركاء والمساهمين 🤝</h2>
                    <p class="text-slate-500 font-bold mt-2 text-sm">إدارة قائمة الشركاء، حساباتهم، وتوزيع حصصهم المالية في الصناديق المختلفة.</p>
                </div>
                <button class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-sm shadow-lg shadow-indigo-500/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                    إضافة شريك جديد
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-6 py-10 space-y-10">

            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl font-black shadow-sm flex items-center gap-3">
                    <span class="text-xl">✅</span>
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($partners as $partner)
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 hover:shadow-md transition-all flex flex-col justify-between group relative overflow-hidden">
                        <div class="absolute -right-6 -top-6 w-32 h-32 bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/10 transition-all duration-700"></div>
                        
                        <div class="relative">
                            <div class="flex justify-between items-start mb-6">
                                <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-indigo-600 text-white rounded-2xl flex items-center justify-center text-2xl font-black shadow-lg shadow-indigo-500/20 transform group-hover:rotate-6 transition-transform">
                                    {{ mb_substr($partner->name, 0, 1) }}
                                </div>
                                <form action="{{ route('partners.destroy', $partner) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الشريك؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-10 h-10 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-sm border border-rose-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>

                            <h4 class="text-2xl font-black text-slate-900 mb-1 tracking-tight group-hover:text-indigo-600 transition-colors">{{ $partner->name }}</h4>
                            <p class="text-xs font-black text-slate-400 mb-6 uppercase tracking-widest">{{ $partner->email }}</p>

                            <div class="space-y-6 mb-6">
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full shadow-lg shadow-indigo-500/40"></span>
                                    المساهمات النشطة
                                </p>
                                <div class="space-y-3">
                                    @foreach($partner->equities as $equity)
                                        <div class="flex justify-between items-center bg-slate-50/50 p-4 rounded-xl border border-slate-100 hover:bg-white hover:border-slate-200 transition-all shadow-sm">
                                            <span class="text-xs font-black text-slate-600">{{ $equity->equitable?->name ?? 'كيان محذوف' }}</span>
                                            <span class="text-[10px] font-black text-indigo-700 bg-white border border-indigo-100 px-3 py-1 rounded-lg shadow-sm">{{ number_format($equity->percentage, 1) }}%</span>
                                        </div>
                                    @endforeach
                                    @if($partner->equities->isEmpty())
                                        <div class="text-center py-6 border border-dashed border-slate-200 rounded-2xl">
                                            <p class="text-[10px] text-slate-300 font-black uppercase tracking-widest italic">لا توجد مساهمات حالية</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Account Status -->
                        <div class="pt-6 border-t border-slate-100 mt-auto">
                            @if($partner->linkedUser)
                                <div class="flex items-center justify-between bg-emerald-50/50 p-4 rounded-2xl border border-emerald-100">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-white text-emerald-600 rounded-lg flex items-center justify-center text-lg shadow-sm border border-emerald-50">👤</div>
                                        <div>
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">حساب نشط ✅</p>
                                            <p class="text-xs font-black text-slate-700 mt-0.5">{{ $partner->linkedUser->email }}</p>
                                        </div>
                                    </div>
                                    <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full shadow-lg shadow-emerald-500/40 animate-pulse"></span>
                                </div>
                            @else
                                <div class="bg-amber-50/50 p-4 rounded-2xl border border-amber-100">
                                    <p class="text-[10px] text-amber-700 font-black uppercase tracking-widest mb-3 flex items-center gap-1.5">
                                        🔗 ربط حساب دخول
                                    </p>
                                    <form action="{{ route('partners.link', $partner) }}" method="POST" class="flex gap-2">
                                        @csrf
                                        <input type="email" name="email" required placeholder="example@mail.com" class="flex-1 bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-sm shadow-lg shadow-indigo-500/20 hover:scale-105 active:scale-95 transition-all">ربط</button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <div class="mt-6 flex justify-between items-end bg-slate-50 p-4 rounded-2xl border border-slate-100 group-hover:bg-white group-hover:border-slate-200 transition-all duration-500">
                            <div>
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">إجمالي الاستثمار</p>
                                <p class="text-2xl font-black text-slate-900 tracking-tighter">${{ number_format($partner->equities->sum('amount'), 0) }}</p>
                            </div>
                            <div class="text-left">
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">القيمة الحالية</p>
                                @php
                                    $currentVal = $partner->equities->sum(function($eq) {
                                        return $eq->equitable ? ($eq->percentage / 100) * $eq->equitable->current_value : 0;
                                    });
                                @endphp
                                <p class="text-xl font-black text-emerald-600 tracking-tighter">${{ number_format($currentVal, 0) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-2xl border border-slate-100 shadow-sm p-16 text-center">
                        <div class="text-6xl mb-6">🏜️</div>
                        <h4 class="text-2xl font-black text-slate-900 mb-2">لا يوجد شركاء بعد</h4>
                        <p class="text-slate-400 font-bold text-sm">ابدأ بإضافة أول شريك خارجي لإدارة مساهماته وحصصه الاستثمارية.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
