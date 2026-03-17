<x-guest-layout>
    <div class="mb-6 text-center">
        @if(optional($activeBrand)->logo_path)
            <img src="{{ asset('storage/'.optional($activeBrand)->logo_path) }}" alt="logo" class="mx-auto mb-3 h-16 w-48 rounded-2xl bg-white/80 object-contain p-2 shadow-sm">
        @endif
        <h1 class="text-2xl font-semibold text-[#0f2d5c]">Iniciar sesión</h1>
        <p class="mt-1 text-sm text-slate-600">{{ optional($activeBrand)->name ?? 'Portal SYSO' }}</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 block w-full rounded-xl border-slate-200" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-1 block w-full rounded-xl border-slate-200" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#2563eb] shadow-sm focus:ring-[#2563eb]" name="remember">
                <span class="ms-2 text-sm text-gray-600">Recordarme</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-[#2563eb] hover:underline" href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <button class="cmm-btn-primary w-full">
            Entrar
        </button>
    </form>
</x-guest-layout>
