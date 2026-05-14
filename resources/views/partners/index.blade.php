<x-app-layout>
    <div class="py-12 px-6">
        <div class="max-w-7xl mx-auto space-y-12">
            
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h2 class="text-4xl font-black text-gray-900 tracking-tight">إدارة الشركاء 🤝</h2>
                    <p class="text-gray-500 font-bold mt-1">إدارة قائمة الشركاء المساهمين، حساباتهم، وتوزيع حصصهم المالية.</p>
                </div>
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-[2rem] text-sm font-black shadow-xl shadow-indigo-500/20 flex items-center transition-all hover:scale-105">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                    إضافة شريك خارجي
                </button>
            </div>

            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-8 py-6 rounded-[2rem] font-bold shadow-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($partners as $partner)
                    <div class="premium-card p-10 flex flex-col justify-between group relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-500/5 rounded-full blur-2xl group-hover:bg-indigo-500/10 transition-all"></div>
                        
                        <div class="relative">
                            <div class="flex justify-between items-start mb-8">
                                <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 text-white rounded-3xl flex items-center justify-center text-2xl font-black shadow-lg shadow-indigo-500/20">
                                    {{ mb_substr($partner->name, 0, 1) }}
                                </div>
                                <form action="{{ route('partners.destroy', $partner) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الشريك؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-10 h-10 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>

                            <h4 class="text-2xl font-black text-gray-900 mb-1 tracking-tight">{{ $partner->name }}</h4>
                            <p class="text-sm font-bold text-gray-400 mb-8">{{ $partner->email }}</p>

                            <div class="space-y-6 mb-8">
                                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full"></span>
                                    المساهمات النشطة
                                </p>
                                <div class="space-y-3">
                                    @foreach($partner->equities as $equity)
                                        <div class="flex justify-between items-center bg-gray-50/50 p-4 rounded-2xl border border-gray-50/50">
                                            <span class="text-xs font-black text-gray-600">{{ $equity->equitable?->name ?? 'كيان محذوف' }}</span>
                                            <span class="text-xs font-black text-indigo-600 bg-white px-3 py-1 rounded-lg shadow-sm">{{ number_format($equity->percentage, 1) }}%</span>
                                        </div>
                                    @endforeach
                                    @if($partner->equities->isEmpty())
                                        <div class="text-center py-6 border-2 border-dashed border-gray-100 rounded-[2rem]">
                                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest italic">لا توجد مساهمات</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Account Status -->
                        <div class="pt-8 border-t border-gray-50 mt-auto">
                            @if($partner->linkedUser)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center text-sm shadow-inner">👤</div>
                                        <div>
                                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">حساب نشط</p>
                                            <p class="text-xs font-bold text-gray-700">{{ $partner->linkedUser->email }}</p>
                                        </div>
                                    </div>
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full shadow-lg shadow-emerald-500/40 animate-pulse"></span>
                                </div>
                            @else
                                <div class="bg-amber-50/50 p-4 rounded-2xl border border-amber-50">
                                    <p class="text-[10px] text-amber-600 font-black uppercase tracking-widest mb-3">ربط حساب خارجي</p>
                                    <form action="{{ route('partners.link', $partner) }}" method="POST" class="flex gap-2">
                                        @csrf
                                        <input type="email" name="email" required placeholder="example@mail.com" class="flex-1 bg-white border-0 rounded-xl px-4 py-3 text-xs font-bold shadow-sm focus:ring-2 focus:ring-indigo-600">
                                        <button type="submit" class="bg-indigo-600 text-white px-4 py-3 rounded-xl text-xs font-black shadow-md hover:bg-indigo-700 transition-all">ربط</button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <div class="mt-8 flex justify-between items-end">
                            <div>
                                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">إجمالي رأس المال</p>
                                <p class="text-3xl font-black text-gray-900 tracking-tighter">${{ number_format($partner->equities->sum('amount'), 0) }}</p>
                            </div>
                            <div class="text-left">
                                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">القيمة الحالية</p>
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
                    <div class="col-span-full premium-card p-24 text-center">
                        <div class="text-6xl mb-6">🏜️</div>
                        <h4 class="text-2xl font-black text-gray-900 mb-2">لا يوجد شركاء بعد</h4>
                        <p class="text-gray-400 font-bold">ابدأ بإضافة أول شريك خارجي لإدارة مساهماته وحصصه.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
