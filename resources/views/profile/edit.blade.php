<x-app-layout>
    <div class="py-12 px-6">
        <div class="max-w-7xl mx-auto space-y-12">
            
            <!-- Profile Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h2 class="text-4xl font-black text-gray-900 tracking-tight">إعدادات الحساب الشخصي 👤</h2>
                    <p class="text-gray-500 font-bold mt-1">إدارة معلوماتك الشخصية، الأمان، وإعدادات الخصوصية.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <!-- Sidebar info -->
                <div class="lg:col-span-4 space-y-6">
                    <div class="premium-card p-8 bg-indigo-600 text-white relative overflow-hidden">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
                        <div class="w-20 h-20 bg-white/20 rounded-3xl flex items-center justify-center text-4xl mb-6 relative z-10">
                            {{ mb_substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <h3 class="text-2xl font-black relative z-10">{{ Auth::user()->name }}</h3>
                        <p class="text-indigo-100 font-bold relative z-10 opacity-80">{{ Auth::user()->email }}</p>
                        <div class="mt-8 pt-8 border-t border-white/10 relative z-10">
                            <div class="flex justify-between items-center text-sm font-bold opacity-80">
                                <span>عضو منذ</span>
                                <span>{{ Auth::user()->created_at->format('Y/m/d') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="premium-card p-8 space-y-4">
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">إحصائيات سريعة</h4>
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-sm font-bold text-gray-500">صناديق الاستثمار</span>
                            <span class="font-black text-gray-900">{{ \App\Models\InvestmentFund::where('user_id', Auth::id())->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm font-bold text-gray-500">إجمالي العمليات</span>
                            <span class="font-black text-gray-900">{{ \App\Models\Transaction::where('user_id', Auth::id())->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Forms Area -->
                <div class="lg:col-span-8 space-y-10">
                    <div class="premium-card p-10 border-t-4 border-t-indigo-500">
                        <h3 class="text-xl font-black text-gray-900 mb-8">المعلومات الشخصية</h3>
                        <div class="max-w-2xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <div class="premium-card p-10 border-t-4 border-t-amber-500">
                        <h3 class="text-xl font-black text-gray-900 mb-8">تغيير كلمة المرور</h3>
                        <div class="max-w-2xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <div class="premium-card p-10 border-t-4 border-t-rose-500">
                        <h3 class="text-xl font-black text-gray-900 mb-8">حذف الحساب</h3>
                        <div class="max-w-2xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
