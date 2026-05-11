<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="flex justify-between items-center px-4">
                <div>
                    <h2 class="text-3xl font-black text-gray-900">صناديق الاستثمار</h2>
                    <p class="text-gray-500 text-sm mt-1">إدارة المحافظ الاستثمارية الكبيرة والشركاء.</p>
                </div>
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl text-sm font-black shadow-lg shadow-indigo-500/20 flex items-center">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    صندوق جديد
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($funds as $fund)
                    <a href="{{ route('funds.show', $fund->id) }}" class="spendee-card p-8 group hover:border-indigo-200 transition-all block">
                        <div class="flex justify-between items-start mb-10">
                            <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">🏢</div>
                            <span class="px-3 py-1 {{ $fund->status == 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-50 text-gray-600' }} text-[10px] font-black uppercase rounded-lg">
                                {{ $fund->status == 'active' ? 'نشط' : 'مغلق' }}
                            </span>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900 mb-2">{{ $fund->name }}</h3>
                        <p class="text-gray-500 text-sm mb-10 line-clamp-2">إدارة الأصول الرأسمالية وتوزيع الأرباح بناءً على الحصص.</p>
                        
                        <div class="grid grid-cols-2 gap-4 border-t border-gray-50 pt-6">
                            <div>
                                <p class="text-[10px] text-gray-400 font-black uppercase mb-1">رأس المال</p>
                                <p class="text-xl font-black text-gray-900">${{ number_format($fund->capital, 0) }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 font-black uppercase mb-1">القيمة الحالية</p>
                                <p class="text-xl font-black text-indigo-600">${{ number_format($fund->current_value, 0) }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
