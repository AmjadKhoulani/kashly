<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/20 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            
            {{-- Header --}}
            <div class="text-right mb-10 space-y-2" dir="rtl">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5 justify-start">
                    ⚙️
                    <span>إدارة منظومات وأقسام المنصة</span>
                </h1>
                <p class="text-slate-500 text-sm font-semibold">
                    قم بتخصيص حسابك وتفعيل الميزات الإضافية التي تحتاجها فقط للحصول على واجهة عمل مبسطة وخالية من التعقيد.
                </p>
            </div>

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold text-sm rounded-2xl px-5 py-4 flex items-center gap-2 mb-6" dir="rtl">
                    ✅ {{ session('success') }}
                </div>
            @endif

            {{-- Modules Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" dir="rtl">
                @foreach($modules as $module)
                    @php 
                        $isActive = in_array($module->id, $userActiveModules);
                    @endphp
                    <div class="bg-white rounded-3xl border {{ $isActive ? 'border-indigo-500 ring-2 ring-indigo-500/10 shadow-indigo-500/5' : 'border-slate-100 shadow-sm' }} hover:shadow-md transition-all duration-300 p-6 flex flex-col justify-between group">
                        <div>
                            {{-- Icon & Name --}}
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shadow-sm border border-slate-100/80 group-hover:scale-105 group-hover:rotate-3 transition-transform 
                                    {{ $isActive ? 'bg-indigo-50 text-indigo-600 border-indigo-100/50' : 'bg-slate-50 text-slate-400' }}">
                                    {{ $module->icon }}
                                </div>
                                <span class="px-2.5 py-1 text-[9px] font-black rounded-lg uppercase tracking-wider 
                                    {{ $isActive ? 'bg-indigo-100/70 text-indigo-700' : 'bg-slate-100 text-slate-400' }}">
                                    {{ $isActive ? 'مفعّل' : 'غير نشط' }}
                                </span>
                            </div>

                            <h3 class="text-base font-black text-slate-900 mb-2">{{ $module->name_ar }}</h3>
                            <p class="text-xs text-slate-400 font-bold leading-relaxed mb-6">{{ $module->description_ar }}</p>
                        </div>

                        {{-- Toggle Button --}}
                        <form action="{{ route('settings.modules.toggle', $module->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-full py-3 rounded-2xl font-black text-xs transition-all duration-200 border flex items-center justify-center gap-2 
                                    {{ $isActive 
                                        ? 'bg-slate-50 text-rose-600 border-slate-200/50 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-100' 
                                        : 'bg-indigo-600 text-white border-transparent hover:bg-indigo-700 shadow-sm hover:shadow' }}">
                                @if($isActive)
                                    <span>إلغاء التفعيل</span>
                                @else
                                    <span>تفعيل المنظومة الآن</span>
                                @endif
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
