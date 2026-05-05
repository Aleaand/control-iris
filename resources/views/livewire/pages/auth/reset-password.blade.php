<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div class="animate-tech" style="opacity: 0;">
    <div class="mb-6 text-xs text-[var(--text-secondary)] font-mono-tech leading-relaxed uppercase tracking-wider">
        {{ __('Restablezca su contraseña para recuperar el acceso a su cuenta.') }}
    </div>

    <form wire:submit="resetPassword" class="space-y-6">
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
                    placeholder="correo@iris.aerospace" required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')"
                class="mt-2 text-red-500 text-[10px] font-black uppercase" />
        </div>

        <!-- Password -->
        <div x-data="{ show: false }">
            <label for="password"
                class="block text-[10px] font-black text-[var(--neon-cyan)] mb-2 uppercase tracking-widest pl-1">Nueva
                Contraseña</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input wire:model="password" id="password" :type="show ? 'text' : 'password'"
                    class="tech-input block w-full pl-10 pr-10 py-3 text-xs focus:outline-none transition-all rounded-xl border-[var(--border-glass)] focus:border-[var(--neon-cyan)]"
                    placeholder="••••••••" required autocomplete="new-password" />
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
            <x-input-error :messages="$errors->get('password')"
                class="mt-2 text-red-500 text-[10px] font-black uppercase" />
        </div>

        <!-- Confirm Password -->
        <div x-data="{ show: false }">
            <label for="password_confirmation"
                class="block text-[10px] font-black text-[var(--neon-cyan)] mb-2 uppercase tracking-widest pl-1">Confirmar
                Nueva Contraseña</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <input wire:model="password_confirmation" id="password_confirmation" :type="show ? 'text' : 'password'"
                    class="tech-input block w-full pl-10 pr-10 py-3 text-xs focus:outline-none transition-all rounded-xl border-[var(--border-glass)] focus:border-[var(--neon-cyan)]"
                    placeholder="••••••••" required autocomplete="new-password" />
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
            <x-input-error :messages="$errors->get('password_confirmation')"
                class="mt-2 text-red-500 text-[10px] font-black uppercase" />
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-full py-4 bg-[var(--neon-cyan)] hover:bg-[var(--neon-cyan)]/90 text-black font-black text-[10px] uppercase tracking-[0.3em] rounded-xl transition-all shadow-[0_0_20px_rgba(14,165,233,0.3)] border border-[var(--neon-cyan)] flex justify-center items-center gap-2 group">
                ACTUALIZAR CONTRASEÑA
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </button>
        </div>
    </form>
</div>