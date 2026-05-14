<x-app-layout>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="flex justify-between items-center px-4">
                <div>
                    <h2 class="text-3xl font-black text-gray-900">إدارة الشركاء</h2>
                    <p class="text-gray-500 text-sm mt-1">قائمة بجميع الشركاء المساهمين في الصناديق والمشاريع.</p>
                </div>
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl text-sm font-black shadow-lg shadow-indigo-500/20 flex items-center">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    شريك جديد
                </button>
            </div>

            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-6 py-4 rounded-2xl font-bold text-sm mx-4">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($partners as $partner)
                    <div class="spendee-card p-6 flex flex-col justify-between">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-xl font-black ml-4">
                                {{ mb_substr($partner->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-gray-900">{{ $partner->name }}</h4>
                                <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $partner->phone ?? 'بدون رقم هاتف' }}</p>
                            </div>
                        </div>

                        <div class="space-y-4 mb-6">
                            <p class="text-[10px] text-gray-400 font-black uppercase border-b border-gray-50 pb-2">المساهمات النشطة</p>
                            @foreach($partner->equities as $equity)
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-bold text-gray-600">{{ $equity->equitable?->name ?? 'كيان محذوف' }}</span>
                                    <span class="text-xs font-black text-indigo-600">{{ number_format($equity->percentage, 1) }}%</span>
                                </div>
                            @endforeach
                            @if($partner->equities->isEmpty())
                                <p class="text-center text-[10px] text-gray-400 py-2 italic">لا توجد مساهمات مسجلة</p>
                            @endif
                        </div>

                        <!-- Link Account Section -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-2xl">
                            <p class="text-[10px] text-gray-400 font-black uppercase mb-3">حساب الشريك للدخول</p>
                            @if($partner->linkedUser)
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                                    <span class="text-xs font-bold text-gray-700">{{ $partner->linkedUser->email }}</span>
                                </div>
                            @else
                                <form action="{{ route('partners.link', $partner) }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <input type="email" name="email" required placeholder="البريد الإلكتروني" class="flex-1 bg-white border-0 rounded-xl px-3 py-2 text-[10px] font-bold focus:ring-1 focus:ring-indigo-600">
                                    <button type="submit" class="bg-indigo-600 text-white px-3 py-2 rounded-xl text-[10px] font-black shadow-sm">ربط</button>
                                </form>
                            @endif
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-gray-50">
                            <div>
                                <p class="text-[10px] text-gray-400 font-black uppercase">إجمالي الاستثمار</p>
                                <p class="text-lg font-black text-gray-900">${{ number_format($partner->equities->sum('amount'), 0) }}</p>
                            </div>
                            <button class="text-gray-400 hover:text-indigo-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full spendee-card p-20 text-center">
                        <p class="text-gray-400 font-bold">لا يوجد شركاء مسجلين حالياً.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
