<x-guest-layout>
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-black text-gray-900 mb-2">تسجيل الدخول</h2>
        <p class="text-sm font-bold text-gray-400">مرحباً بك مجدداً في كاشلي</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 mr-1">البريد الإلكتروني</label>
            <input id="email" type="email" name="email" :value="old('email')" required autofocus 
                class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-600 transition-all"
                placeholder="name@company.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-2 mr-1">
                <label class="block text-[10px] font-black text-gray-400 uppercase">كلمة المرور</label>
                @if (Route::has('password.request'))
                    <a class="text-[10px] font-black text-indigo-600 hover:text-indigo-700 uppercase" href="{{ route('password.request') }}">
                        نسيت كلمة المرور؟
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required 
                class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-indigo-600 transition-all"
                placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <div class="relative">
                    <input id="remember_me" type="checkbox" name="remember" class="sr-only">
                    <div class="w-10 h-6 bg-gray-200 rounded-full shadow-inner transition-colors group-has-[:checked]:bg-indigo-600"></div>
                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition-transform group-has-[:checked]:translate-x-4"></div>
                </div>
                <span class="ms-3 text-xs font-bold text-gray-500">تذكرني على هذا الجهاز</span>
            </label>
        </div>

        <button type="submit" class="w-full bg-indigo-600 text-white py-5 rounded-3xl font-black text-lg shadow-xl shadow-indigo-500/20 hover:bg-indigo-700 transition-all hover:scale-[1.02]">
            دخول للمنصة
        </button>

        <div class="text-center mt-8">
            <p class="text-sm font-bold text-gray-400">
                ليس لديك حساب؟ 
                <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-700 font-black mr-1">أنشئ حساباً جديداً</a>
            </p>
        </div>
    </form>
</x-guest-layout>
