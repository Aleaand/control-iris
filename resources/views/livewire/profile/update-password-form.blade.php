<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section class="tech-card p-8">
    <header class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-2 h-2 bg-[var(--neon-cyan)] rounded-full animate-pulse shadow-[0_0_8px_rgba(14,165,233,0.5)]">
            </div>
            <h2 class="text-xl font-black tech-text-primary uppercase tracking-[0.2em]">Cambiar Contraseña</h2>
        </div>
        <p class="font-mono-tech text-[10px] tech-text-secondary uppercase tracking-widest">Introduce tus credenciales
            para actualizar tu contraseña.</p>
    </header>

    <form wire:submit="updatePassword" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-2" x-data="{ show: false }">
                <label for="update_password_current_password"
                    class="font-mono-tech text-[10px] tech-text-secondary uppercase tracking-widest pl-1">Contraseña
                    Actual</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input wire:model="current_password" id="update_password_current_password"
                        :type="show ? 'text' : 'password'"
                        class="tech-input w-full pl-10 p-4 pr-10 text-xs focus:outline-none transition-all rounded-xl border-[var(--border-glass)] focus:border-[var(--neon-cyan)]"
                        autocomplete="current-password" />
                    <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-3 flex items-center text-zinc-500 hover:text-[var(--neon-cyan)] transition-colors">
                        <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.882 9.882L5.99 5.99m10.01 10.01l3.99 3.99M10.733 5.076A10.013 10.013 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.059 10.059 0 01-1.293 2.768M3 3l18 18" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('current_password')"
                    class="mt-2 text-red-500 text-[10px] font-black uppercase" />
            </div>

            <div class="space-y-2" x-data="{ show: false }">
                <label for="update_password_password"
                    class="font-mono-tech text-[10px] tech-text-secondary uppercase tracking-widest pl-1">Nueva
                    Contraseña</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <input wire:model="password" id="update_password_password" :type="show ? 'text' : 'password'"
                        class="tech-input w-full pl-10 p-4 pr-10 text-xs focus:outline-none transition-all rounded-xl border-[var(--border-glass)] focus:border-[var(--neon-cyan)]"
                        autocomplete="new-password" />
                    <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-3 flex items-center text-zinc-500 hover:text-[var(--neon-cyan)] transition-colors">
                        <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.882 9.882L5.99m10.01 10.01l3.99 3.99M10.733 5.076A10.013 10.013 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.059 10.059 0 01-1.293 2.768M3 3l18 18" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')"
                    class="mt-2 text-red-500 text-[10px] font-black uppercase" />
            </div>

            <div class="space-y-2" x-data="{ show: false }">
                <label for="update_password_password_confirmation"
                    class="font-mono-tech text-[10px] tech-text-secondary uppercase tracking-widest pl-1">Confirmar
                    Nueva</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <input wire:model="password_confirmation" id="update_password_password_confirmation"
                        :type="show ? 'text' : 'password'"
                        class="tech-input w-full pl-10 p-4 pr-10 text-xs focus:outline-none transition-all rounded-xl border-[var(--border-glass)] focus:border-[var(--neon-cyan)]"
                        autocomplete="new-password" />
                    <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-3 flex items-center text-zinc-500 hover:text-[var(--neon-cyan)] transition-colors">
                        <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.882 9.882L5.99m10.01 10.01l3.99 3.99M10.733 5.076A10.013 10.013 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.059 10.059 0 01-1.293 2.768M3 3l18 18" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')"
                    class="mt-2 text-red-500 text-[10px] font-black uppercase" />
            </div>
        </div>

        <div class="flex items-center justify-between pt-6 border-t border-white/5">
            <p class="font-mono-tech text-[9px] text-zinc-600 uppercase">La nueva contraseña debe cumplir con los
                estándares de seguridad</p>
            <div class="flex items-center gap-4">
                <x-action-message class="font-mono-tech text-[10px] text-[var(--neon-cyan)] uppercase"
                    on="password-updated">
                    Actualizado
                </x-action-message>

                <button type="submit"
                    class="px-6 py-2 bg-[var(--neon-cyan)] hover:bg-[var(--neon-cyan)]/90 text-black font-black text-[10px] uppercase tracking-widest rounded-xl transition-all shadow-[0_0_20px_rgba(14,165,233,0.3)] border border-[var(--neon-cyan)] flex items-center gap-2 group">
                    <svg class="w-3 h-3 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Actualizar Contraseña
                </button>
            </div>
        </div>
    </form>
</section>