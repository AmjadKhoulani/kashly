<x-app-layout>
    <div class="py-12 bg-[#F8FAFC] min-h-screen" x-data="{ showModal: false, editingCategory: null }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <div class="flex justify-between items-center mb-10">
                <div>
                    <h2 class="text-5xl font-black text-slate-900 tracking-tight">إدارة التصنيفات المالية</h2>
                    <p class="text-lg font-bold text-slate-400 mt-3">خصص تصنيفاتك بالألوان والأيقونات لتحليل مالي أدق وأكثر وضوحاً.</p>
                </div>
                <button @click="showModal = true; editingCategory = null" class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-5 rounded-[2.5rem] font-black text-lg shadow-2xl shadow-indigo-500/30 transition-all hover:scale-105 active:scale-95">
                    إضافة تصنيف جديد
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($categories as $category)
                    <div class="premium-card p-8 flex items-center justify-between group border-2 border-slate-100 bg-white hover:border-indigo-200 transition-all duration-500 shadow-xl">
                        <div class="flex items-center gap-6">
                            <div class="w-16 h-16 rounded-[1.8rem] flex items-center justify-center text-3xl shadow-inner border-2 border-white transition-transform group-hover:rotate-6 duration-500" style="background-color: {{ $category->color }}20; color: {{ $category->color }};">
                                <span>{{ $category->icon ?: '📁' }}</span>
                            </div>
                            <div>
                                <h4 class="text-xl font-black text-slate-900 mb-1 group-hover:text-indigo-600 transition-colors">{{ $category->name }}</h4>
                                <span class="px-4 py-1.5 rounded-xl text-xs font-black uppercase tracking-widest border {{ $category->type == 'income' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }} shadow-sm">
                                    {{ $category->type == 'income' ? 'إيراد' : 'مصروف' }}
                                </span>
                            </div>
                        </div>
                        
                        @if(!$category->is_default)
                            <div class="flex items-center gap-3 opacity-0 group-hover:opacity-100 transition-all transform translate-x-4 group-hover:translate-x-0">
                                <button @click="editingCategory = {{ json_encode($category) }}; showModal = true" class="p-3 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-2xl transition-all border border-transparent hover:border-indigo-100">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-3 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-2xl transition-all border border-transparent hover:border-rose-100">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Category Modal -->
        <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" x-cloak x-transition>
            <div class="bg-white rounded-[3.5rem] w-full max-w-lg p-10 shadow-2xl relative text-right" @click.away="showModal = false">
                <h3 class="text-2xl font-black text-gray-900 mb-8" x-text="editingCategory ? 'تعديل التصنيف' : 'إضافة تصنيف جديد'"></h3>
                
                <form :action="editingCategory ? '{{ route('categories.update', 'ID') }}'.replace('ID', editingCategory.id) : '{{ route('categories.store') }}'" method="POST" class="space-y-6">
                    @csrf
                    <template x-if="editingCategory">
                        @method('PUT')
                    </template>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 mr-2">اسم التصنيف</label>
                        <input type="text" name="name" x-model="editingCategory ? editingCategory.name : ''" required class="w-full premium-input" placeholder="مثلاً: طعام، مواصلات، راتب...">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 mr-2">النوع</label>
                            <select name="type" x-model="editingCategory ? editingCategory.type : 'expense'" class="w-full premium-input">
                                <option value="expense">مصروف</option>
                                <option value="income">إيراد</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 mr-2">اللون</label>
                            <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-2xl border border-gray-100">
                                <input type="color" name="color" x-model="editingCategory ? editingCategory.color : '#4F46E5'" class="w-10 h-10 border-none bg-transparent cursor-pointer">
                                <span class="text-xs font-bold text-gray-500" x-text="editingCategory ? editingCategory.color : '#4F46E5'"></span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 mr-2">الأيقونة (إيموجي)</label>
                        <input type="text" name="icon" x-model="editingCategory ? editingCategory.icon : ''" class="w-full premium-input text-center text-2xl" placeholder="📁">
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 bg-indigo-600 text-white py-5 rounded-2xl font-black text-sm shadow-xl shadow-indigo-500/20" x-text="editingCategory ? 'تحديث' : 'حفظ'"></button>
                        <button type="button" @click="showModal = false" class="px-8 py-5 bg-gray-100 text-gray-500 rounded-2xl font-black text-sm">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
