<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col rounded-xl border border-zinc-200/80 shadow-2xl gap-6 bg-white p-8 text-zinc-900 max-w-md mx-auto relative overflow-hidden">
        
        <div class="absolute top-0 left-0 right-0 h-1.5 flex">
            <div class="w-1/2 bg-amber-400"></div>
            <div class="w-1/2 bg-emerald-500"></div>
        </div>

        <x-auth-header :title="__('Masuk ke Akun Anda')" :description="__('Masukkan email dan kata sandi di bawah ini untuk masuk')" />

        <x-auth-session-status class="text-center text-emerald-600 font-medium" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
            @csrf

            <flux:input
                name="email"
                :label="__('Alamat Email')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
                class="focus:border-emerald-500 focus:ring-emerald-500"
            />

            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Kata Sandi')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Kata Sandi')"
                    viewable
                    class="focus:border-emerald-500 focus:ring-emerald-500"
                />

                {{-- @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-xs font-medium insert-e-0 text-emerald-600 hover:text-emerald-700 transition-colors" :href="route('password.request')" wire:navigate>
                        {{ __('Lupa Password?') }}
                    </flux:link>
                @endif --}}
            </div>

            <flux:checkbox name="remember" :label="__('Ingat Saya')" :checked="old('remember')" class="text-emerald-600 focus:ring-emerald-500" />

            <div class="flex items-center justify-end mt-2">
                <flux:button type="submit" class="w-full bg-amber-400 hover:bg-amber-500 text-zinc-950 font-semibold shadow-md shadow-amber-400/20 py-2.5 rounded-lg transition-all border-none" data-test="login-button">
                    {{ __('Masuk') }}
                </flux:button>
            </div>
        </form>

        <div class="text-center text-xs text-zinc-500 mt-2">
            Dengan login, Anda menyetujui <span class="text-zinc-900 font-medium underline cursor-pointer">S&K</span> kami.
        </div>
    </div>
</x-layouts::auth>