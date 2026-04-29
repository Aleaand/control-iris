<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
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
            <div class="w-2 h-2 bg-violet-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(139,92,246,0.5)]"></div>
            <h2 class="text-xl font-black tech-text-primary uppercase tracking-[0.2em]">Cifrado de Acceso</h2>
        </div>
        <p class="font-mono-tech text-[10px] tech-text-secondary uppercase tracking-widest">Protocolo de rotación de credenciales criptográficas</p>
    </header>

    <form wire:submit="updatePassword" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-2" x-data="{ show: false }">
                <label for="update_password_current_password" class="font-mono-tech text-[10px] tech-text-secondary uppercase tracking-widest">Contraseña Actual</label>
                <div class="relative">
                    <input wire:model="current_password" id="update_password_current_password" :type="show ? 'text' : 'password'" class="tech-input w-full p-4 pr-10" autocomplete="current-password" />
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-zinc-500 hover:text-violet-500 transition-colors">
                        <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.882 9.882L5.99 5.99m10.01 10.01l3.99 3.99M10.733 5.076A10.013 10.013 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.059 10.059 0 01-1.293 2.768M3 3l18 18" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
            </div>

            <div class="space-y-2" x-data="{ show: false }">
                <label for="update_password_password" class="font-mono-tech text-[10px] tech-text-secondary uppercase tracking-widest">Nueva Contraseña</label>
                <div class="relative">
                    <input wire:model="password" id="update_password_password" :type="show ? 'text' : 'password'" class="tech-input w-full p-4 pr-10" autocomplete="new-password" />
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-zinc-500 hover:text-violet-500 transition-colors">
                        <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.882 9.882L5.99 5.99m10.01 10.01l3.99 3.99M10.733 5.076A10.013 10.013 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.059 10.059 0 01-1.293 2.768M3 3l18 18" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="space-y-2" x-data="{ show: false }">
                <label for="update_password_password_confirmation" class="font-mono-tech text-[10px] tech-text-secondary uppercase tracking-widest">Confirmar Nueva</label>
                <div class="relative">
                    <input wire:model="password_confirmation" id="update_password_password_confirmation" :type="show ? 'text' : 'password'" class="tech-input w-full p-4 pr-10" autocomplete="new-password" />
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-zinc-500 hover:text-violet-500 transition-colors">
                        <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.882 9.882L5.99 5.99m10.01 10.01l3.99 3.99M10.733 5.076A10.013 10.013 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.059 10.059 0 01-1.293 2.768M3 3l18 18" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-between pt-6 border-t border-white/5">
            <p class="font-mono-tech text-[9px] text-zinc-600 uppercase">La nueva contraseña debe cumplir con los estándares de seguridad de nivel 5</p>
            <div class="flex items-center gap-4">
                <x-action-message class="font-mono-tech text-[10px] text-emerald-400 uppercase" on="password-updated">
                    Cifrado Actualizado
                </x-action-message>
                
                <button type="submit" class="px-6 py-2 bg-violet-600 hover:bg-violet-500 text-white font-black text-[10px] uppercase tracking-widest rounded-lg transition-all shadow-[0_0_15px_rgba(139,92,246,0.3)]">
                    Rotar Credenciales
                </button>
            </div>
        </div>
    </form>
</section>
