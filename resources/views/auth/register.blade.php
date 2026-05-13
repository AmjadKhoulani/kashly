<x-guest-layout>
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-black text-gray-900 mb-2">إنشاء حساب جديد</h2>
        <p class="text-sm font-bold text-gray-400">ابدأ رحلتك المالية اليوم مع كاشلي</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 mr-1">الاسم الكامل</label>
            <input id="name" type="text" name="name" :value="old('name')" required autofocus 
                class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-600 transition-all"
                placeholder="مثلاً: أحمد علي">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 mr-1">البريد الإلكتروني</label>
            <input id="email" type="email" name="email" :value="old('email')" required 
                class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-600 transition-all"
                placeholder="name@company.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 mr-1">كلمة المرور</label>
            <input id="password" type="password" name="password" required 
                class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-600 transition-all"
                placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 mr-1">تأكيد كلمة المرور</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required 
                class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-600 transition-all"
                placeholder="••••••••">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="w-full bg-indigo-600 text-white py-5 rounded-3xl font-black text-lg shadow-xl shadow-indigo-500/20 hover:bg-indigo-700 transition-all hover:scale-[1.02]">
            تأكيد وإنشاء الحساب
        </button>

        <div class="text-center mt-8">
            <p class="text-sm font-bold text-gray-400">
                لديك حساب بالفعل؟ 
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-black mr-1">تسجيل الدخول</a>
            </p>
        </div>
    </form>
</x-guest-layout>
