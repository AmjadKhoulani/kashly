<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/20" x-data="{ showModal: false, editingCategory: null }">
        
        <!-- Sticky Header -->
        <div class="bg-white/95 border-b border-slate-100 sticky top-0 z-40 backdrop-blur-xl py-6 px-6">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight">إدارة التصنيفات المالية</h2>
                    <p class="text-slate-500 font-bold mt-2 text-sm">خصص تصنيفاتك بالألوان والأيقونات لتحليل مالي أدق وأكثر وضوحاً.</p>
                </div>
                <button @click="showModal = true; editingCategory = null" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-sm shadow-lg shadow-indigo-500/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                    إضافة تصنيف جديد
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-6 py-10 space-y-10">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($categories as $category)
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 hover:shadow-md transition-all flex items-center justify-between group relative overflow-hidden">
                        <div class="flex items-center gap-4 relative z-10">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shadow-inner border border-white transition-transform group-hover:rotate-6 duration-500" style="background-color: {{ $category->color }}20; color: {{ $category->color }};">
                                <span>{{ $category->icon ?: '📁' }}</span>
                            </div>
                            <div>
                                <h4 class="text-md font-black text-slate-900 mb-1 group-hover:text-indigo-600 transition-colors">{{ $category->name }}</h4>
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest border {{ $category->type == 'income' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }} shadow-sm">
                                    {{ $category->type == 'income' ? 'إيراد' : 'مصروف' }}
                                </span>
                            </div>
                        </div>
                        
                        @if(!$category->is_default)
                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0 relative z-10">
                                <button @click="editingCategory = {{ json_encode($category) }}; showModal = true" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all border border-transparent hover:border-indigo-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all border border-transparent hover:border-rose-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Category Modal -->
        <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-md" x-cloak x-transition>
            <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl text-right relative overflow-hidden border border-slate-100/50" @click.away="showModal = false">
                
                <!-- Sticky Header inside Modal -->
                <div class="sticky top-0 bg-white/95 border-b border-slate-100 px-6 py-4 flex justify-between items-center z-10 backdrop-blur-md">
                    <h3 class="text-lg font-black text-slate-900" x-text="editingCategory ? 'تعديل التصنيف' : 'إضافة تصنيف جديد'"></h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 transition-all p-1.5 hover:bg-slate-50 rounded-xl border border-transparent hover:border-slate-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Content / Form -->
                <form :action="editingCategory ? '{{ route('categories.update', 'ID') }}'.replace('ID', editingCategory.id) : '{{ route('categories.store') }}'" method="POST" class="p-6 space-y-4">
                    @csrf
                    <template x-if="editingCategory">
                        @method('PUT')
                    </template>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 mr-1">اسم التصنيف</label>
                        <input type="text" name="name" x-model="editingCategory ? editingCategory.name : ''" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="مثلاً: طعام، مواصلات، راتب...">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 mr-1">النوع</label>
                            <select name="type" x-model="editingCategory ? editingCategory.type : 'expense'" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                                <option value="expense">مصروف</option>
                                <option value="income">إيراد</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 mr-1">اللون</label>
                            <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-2xl border border-slate-100">
                                <input type="color" name="color" x-model="editingCategory ? editingCategory.color : '#4F46E5'" class="w-8 h-8 border-none bg-transparent cursor-pointer rounded-lg">
                                <span class="text-xs font-bold text-slate-500" x-text="editingCategory ? editingCategory.color : '#4F46E5'"></span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 mr-1">الأيقونة (إيموجي)</label>
                        <input type="text" name="icon" x-model="editingCategory ? editingCategory.icon : ''" class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 outline-none text-center text-2xl" placeholder="📁">
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="flex-1 px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-sm shadow-lg shadow-indigo-500/20 hover:scale-105 active:scale-95 transition-all" x-text="editingCategory ? 'تحديث' : 'حفظ'"></button>
                        <button type="button" @click="showModal = false" class="px-5 py-2.5 bg-slate-150 text-slate-750 hover:bg-slate-200 rounded-xl font-black text-sm transition-all">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
