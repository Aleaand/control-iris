<div class="space-y-5">
    <form wire:submit="save">
        <!-- Nueva contraseña -->
        <div class="mb-4" x-data="{ show: false }">
            <label class="block font-mono-tech text-[10px] text-zinc-400 uppercase tracking-widest mb-2">
                Nueva Contraseña
            </label>
            <div class="relative">
                <input
                    :type="show ? 'text' : 'password'"
                    wire:model="new_password"
                    id="new_password"
                    autocomplete="new-password"
                    placeholder="Mínimo 8 caracteres"
                    class="w-full px-4 py-3 rounded-xl text-sm transition-all duration-200 focus:outline-none focus:ring-1 pr-10"
                    style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); color: var(--text-primary); focus: border-color: #10b981;"
                >
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-zinc-500 hover:text-emerald-500 transition-colors">
                    <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.882 9.882L5.99 5.99m10.01 10.01l3.99 3.99M10.733 5.076A10.013 10.013 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.059 10.059 0 01-1.293 2.768M3 3l18 18" />
                    </svg>
                </button>
            </div>
            @error('new_password')
                <p class="mt-1.5 text-rose-400 text-xs">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirmar contraseña -->
        <div class="mb-6" x-data="{ show: false }">
            <label class="block font-mono-tech text-[10px] text-zinc-400 uppercase tracking-widest mb-2">
                Confirmar Contraseña
            </label>
            <div class="relative">
                <input
                    :type="show ? 'text' : 'password'"
                    wire:model="new_password_confirmation"
                    id="new_password_confirmation"
                    autocomplete="new-password"
                    placeholder="Repite la contraseña"
                    class="w-full px-4 py-3 rounded-xl text-sm transition-all duration-200 focus:outline-none focus:ring-1 pr-10"
                    style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); color: var(--text-primary);"
                >
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-zinc-500 hover:text-emerald-500 transition-colors">
                    <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.882 9.882L5.99 5.99m10.01 10.01l3.99 3.99M10.733 5.076A10.013 10.013 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.059 10.059 0 01-1.293 2.768M3 3l18 18" />
                    </svg>
                </button>
            </div>
            @error('new_password_confirmation')
                <p class="mt-1.5 text-rose-400 text-xs">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="w-full py-3.5 rounded-xl font-black text-sm uppercase tracking-widest transition-all duration-200 flex items-center justify-center gap-2"
            style="background: linear-gradient(135deg, #059669, #10b981); color: white; box-shadow: 0 0 20px rgba(16,185,129,0.3);"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove>
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Activar Acceso
            </span>
            <span wire:loading class="flex items-center gap-2">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Procesando...
            </span>
        </button>
    </form>
</div>
