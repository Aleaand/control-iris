<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $user = Auth::user();
        if ($user && $user->role === 'super_admin') {
            $this->redirectIntended(default: route('admin.dashboard', absolute: false), navigate: true);
        } else {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        }
    }
}; ?>

<div class="animate-tech" style="opacity: 0;">
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-6">
        <div>
            <label for="email"
                class="block text-[10px] font-black text-[var(--neon-cyan)] mb-2 uppercase tracking-widest pl-1">Correo
                Electrónico</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <input wire:model="form.email" id="email" type="email"
                    class="tech-input block w-full pl-10 pr-4 py-3 text-xs focus:outline-none transition-all rounded-xl border-[var(--border-glass)] focus:border-[var(--neon-cyan)]"
                    required autofocus autocomplete="name" />
            </div>
            <x-input-error :messages="$errors->get('form.email')"
                class="mt-2 text-red-500 text-[10px] font-black uppercase" />
        </div>

        <!-- Password -->
        <div x-data="{ show: false }">
            <label for="password"
                class="block text-[10px] font-black text-[var(--neon-cyan)] mb-2 uppercase tracking-widest pl-1">Contraseña</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input wire:model="form.password" id="password" :type="show ? 'text' : 'password'"
                    class="tech-input block w-full pl-10 pr-10 py-3 text-xs focus:outline-none transition-all rounded-xl border-[var(--border-glass)] focus:border-[var(--neon-cyan)]"
                    placeholder="••••••••" required autocomplete="current-password" />
                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-3 flex items-center text-[var(--text-secondary)] hover:text-[var(--neon-cyan)] transition-colors">
                    <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.882 9.882L5.99 5.99m10.01 10.01l3.99 3.99M10.733 5.076A10.013 10.013 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.059 10.059 0 01-1.293 2.768M3 3l18 18" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('form.password')"
                class="mt-2 text-red-500 text-[10px] font-black uppercase" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember" class="inline-flex items-center cursor-pointer group">
                <div class="relative flex items-center">
                    <input wire:model="form.remember" id="remember" type="checkbox" class="peer sr-only"
                        name="remember">
                    <div
                        class="w-4 h-4 bg-[var(--tech-input-bg)] border border-[var(--border-glass)] rounded peer-checked:bg-[var(--neon-cyan)] peer-checked:border-[var(--neon-cyan)] transition-all">
                    </div>
                    <svg class="absolute w-3 h-3 text-black left-0.5 top-0.5 opacity-0 peer-checked:opacity-100 transition-opacity"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span
                    class="ms-2 font-mono-tech text-[9px] text-[var(--text-secondary)] uppercase tracking-widest group-hover:text-[var(--text-primary)] transition-colors">Recordarme</span>
            </label>

            @if (Route::has('password.request'))
                <a class="font-mono-tech text-[9px] text-[var(--neon-cyan)] hover:text-white uppercase tracking-widest transition-colors focus:outline-none"
                    href="{{ route('password.request') }}" wire:navigate>
                    Recuperar contraseña
                </a>
            @endif
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-full py-4 bg-[var(--neon-cyan)] hover:bg-[var(--neon-cyan)]/90 text-black font-black text-[10px] uppercase tracking-[0.3em] rounded-xl transition-all shadow-[0_0_20px_rgba(14,165,233,0.3)] border border-[var(--neon-cyan)] flex justify-center items-center gap-2 group">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                INICIAR SESIÓN
            </button>
        </div>
    </form>
</div>