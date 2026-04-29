<?php
 
 use App\Livewire\Actions\Logout;
 use App\Models\User;
 use Illuminate\Support\Facades\Auth;
 use Livewire\Volt\Component;
 
 new class extends Component
 {
     public string $password = '';
     public string $confirmation = '';
     public bool $isLastAdmin = false;
 
     public function mount(): void
     {
         $this->isLastAdmin = User::where('role', 'admin')->count() <= 1 && Auth::user()->role === 'admin';
     }
 
     /**
      * Delete the currently authenticated user.
      */
     public function deleteUser(Logout $logout): void
     {
         if ($this->isLastAdmin) {
             $this->addError('confirmation', 'No se puede eliminar el único administrador del sistema.');
             return;
         }
 
         if ($this->confirmation !== 'CONFIRMAR_BORRAR_CUENTA') {
             $this->addError('confirmation', 'La frase de confirmación es incorrecta.');
             return;
         }
 
         $this->validate([
             'password' => ['required', 'string', 'current_password'],
         ]);
 
         tap(Auth::user(), $logout(...))->delete();
 
         $this->redirect('/', navigate: true);
     }
 }; ?>
 
 <section class="p-8">
     <header class="mb-8">
         <div class="flex items-center gap-3 mb-2">
             <div class="w-2 h-2 bg-rose-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(244,63,94,0.5)]"></div>
             <h2 class="text-xl font-black text-rose-500 uppercase tracking-[0.2em]">Protocolo de Purga</h2>
         </div>
         <p class="font-mono-tech text-[10px] tech-text-secondary uppercase tracking-widest">Eliminación permanente de registros y activos del operador</p>
     </header>
 
     @if($isLastAdmin)
         <div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl mb-6">
             <p class="font-mono-tech text-[9px] text-rose-400 uppercase leading-relaxed">
                 <span class="font-bold">[BLOQUEO CRÍTICO]:</span> Eres el único administrador activo. El sistema requiere al menos un operador de nivel 5 para mantenerse funcional. La purga está deshabilitada.
             </p>
         </div>
     @endif
 
     <button
         x-data=""
         x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
         @if($isLastAdmin) disabled @endif
         class="px-6 py-2 @if($isLastAdmin) bg-zinc-800 text-zinc-500 cursor-not-allowed @else bg-rose-600 hover:bg-rose-500 text-white shadow-[0_0_15px_rgba(244,63,94,0.3)] @endif font-black text-[10px] uppercase tracking-widest rounded-lg transition-all"
     >
         Iniciar Secuencia de Purga
     </button>
 
     <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
         <form wire:submit="deleteUser" class="p-8 bg-[#0a0a0f] border border-rose-500/20 rounded-3xl overflow-hidden relative">
             <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-rose-500 to-transparent animate-pulse"></div>
             
             <h2 class="text-xl font-black text-rose-500 uppercase tracking-[0.2em] mb-4">Confirmar Eliminación</h2>
 
             <p class="font-mono-tech text-[10px] text-zinc-400 uppercase leading-relaxed mb-8">
                 Esta acción es irreversible. Se eliminarán todos los registros de vuelo, reservaciones y credenciales asociadas a esta unidad de identidad.
             </p>
 
             <div class="space-y-6">
                 <!-- Nivel 1: Frase -->
                 <div class="space-y-2">
                     <label class="font-mono-tech text-[10px] text-rose-400/70 uppercase tracking-widest">Nivel 1: Frase de Seguridad</label>
                     <p class="font-mono-tech text-[8px] text-zinc-600 mb-2 italic">Escribe: CONFIRMAR_BORRAR_CUENTA</p>
                     <input wire:model="confirmation" type="text" class="tech-input w-full p-4 border-rose-500/20 focus:border-rose-500" placeholder="Escribe la frase aquí..." />
                     <x-input-error :messages="$errors->get('confirmation')" class="mt-2" />
                 </div>
 
                 <!-- Nivel 2: Password -->
                 <div class="space-y-2" x-data="{ show: false }">
                     <label for="password" class="font-mono-tech text-[10px] text-rose-400/70 uppercase tracking-widest">Nivel 2: Autorización del Operador</label>
                     <div class="relative">
                        <input wire:model="password" id="password" :type="show ? 'text' : 'password'" class="tech-input w-full p-4 pr-12 border-rose-500/20 focus:border-rose-500" placeholder="Contraseña de confirmación" />
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-4 flex items-center text-rose-400/50 hover:text-rose-500 transition-colors">
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
             </div>
 
             <div class="mt-10 flex justify-end gap-4">
                 <button type="button" x-on:click="$dispatch('close')" class="px-6 py-2 bg-zinc-900 text-zinc-400 font-bold text-[10px] uppercase tracking-widest rounded-lg border border-white/5 hover:bg-zinc-800 transition-all">
                     Abortar
                 </button>
 
                 <button type="submit" class="px-6 py-2 bg-rose-600 hover:bg-rose-500 text-white font-black text-[10px] uppercase tracking-widest rounded-lg transition-all shadow-[0_0_20px_rgba(244,63,94,0.4)]">
                     Confirmar Purga
                 </button>
             </div>
         </form>
     </x-modal>
 </section>
