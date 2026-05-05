<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="animate-tech" style="opacity: 0;">
    <div class="mb-6 text-xs text-[var(--text-secondary)] font-mono-tech leading-relaxed uppercase tracking-wider">
        {{ __('¿Ha olvidado su contraseña? Introduzca su correo electrónico y el sistema le enviará un enlace de recuperación.') }}
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="space-y-6">
        <!-- Email Address -->
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
                <input wire:model="email" id="email" type="email"
                    class="tech-input block w-full pl-10 pr-4 py-3 text-xs focus:outline-none transition-all rounded-xl border-[var(--border-glass)] focus:border-[var(--neon-cyan)]"
                    required autofocus />
            </div>
            <x-input-error :messages="$errors->get('email')"
                class="mt-2 text-red-500 text-[10px] font-black uppercase" />
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-full py-4 bg-[var(--neon-cyan)] hover:bg-[var(--neon-cyan)]/90 text-black font-black text-[10px] uppercase tracking-[0.3em] rounded-xl transition-all shadow-[0_0_20px_rgba(14,165,233,0.3)] border border-[var(--neon-cyan)] flex justify-center items-center gap-2 group">
                RECUPERAR CONTRASEÑA
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('login') }}" wire:navigate
                class="font-mono-tech text-[9px] text-[var(--text-secondary)] hover:text-[var(--neon-cyan)] uppercase tracking-widest transition-colors">
                Iniciar Sesión
            </a>
        </div>
    </form>
</div>