<?php
 
 use App\Models\User;
 use Illuminate\Support\Facades\Auth;
 use Illuminate\Support\Facades\Session;
 use Illuminate\Validation\Rule;
 use Livewire\Volt\Component;
 
 new class extends Component
 {
     public string $name = '';
     public string $email = '';
 
     /**
      * Mount the component.
      */
     public function mount(): void
     {
         $this->name = Auth::user()->name;
         $this->email = Auth::user()->email;
     }
 
     /**
      * Update the profile information for the currently authenticated user.
      */
     public function updateProfileInformation(): void
     {
         $user = Auth::user();
 
         $validated = $this->validate([
             'name' => ['required', 'string', 'max:255'],
             'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
         ]);
 
         $user->fill($validated);
 
         if ($user->isDirty('email')) {
             $user->email_verified_at = null;
         }
 
         $user->save();
 
         $this->dispatch('profile-updated', name: $user->name);
     }
 
     /**
      * Send an email verification notification to the current user.
      */
     public function sendVerification(): void
     {
         $user = Auth::user();
 
         if ($user->hasVerifiedEmail()) {
             $this->redirectIntended(default: route('dashboard', absolute: false));
 
             return;
         }
 
         $user->sendEmailVerificationNotification();
 
         Session::flash('status', 'verification-link-sent');
     }
 }; ?>
 
 <section class="tech-card p-8">
     <!-- Header de Dossier -->
     <div class="flex justify-between items-start mb-8">
         <div>
             <h2 class="text-xl font-black tech-text-primary uppercase tracking-[0.2em]">Ficha Técnica de Personal</h2>
             <p class="font-mono-tech text-[10px] tech-text-secondary uppercase tracking-widest mt-1">Identificador Único: #{{ str_pad(auth()->id(), 6, '0', STR_PAD_LEFT) }}</p>
         </div>
         <div class="flex flex-col items-end">
             <span class="px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 font-mono-tech text-[9px] uppercase tracking-widest shadow-[0_0_15px_rgba(6,182,212,0.2)]">
                 SUPER_ADMIN
             </span>
             <span class="font-mono-tech text-[8px] text-zinc-500 mt-2 uppercase">Protocolo Nivel 5</span>
         </div>
     </div>
 
     <!-- Banner de Advertencia si no está verificado -->
     @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
         <div class="mb-8 p-6 bg-amber-500/5 border border-amber-500/20 rounded-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6 overflow-hidden relative group">
             <div class="absolute top-0 left-0 w-1 h-full bg-amber-500/50 animate-pulse"></div>
             
             <div class="flex items-center gap-4">
                 <div class="w-12 h-12 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500 shadow-[0_0_15px_rgba(245,158,11,0.1)]">
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                     </svg>
                 </div>
                 <div>
                     <h4 class="font-black text-amber-400 text-xs uppercase tracking-widest mb-1">Comunicación No Encriptada</h4>
                     <p class="font-mono-tech text-[9px] text-amber-200/60 uppercase max-w-md leading-relaxed">
                         Tu enlace de comunicación (email) no ha sido verificado. El acceso a protocolos avanzados podría estar restringido.
                     </p>
                 </div>
             </div>
 
             <div class="flex flex-col items-end gap-2">
                 @if (session('status') === 'verification-link-sent')
                     <span class="font-mono-tech text-[10px] text-emerald-400 uppercase font-bold flex items-center gap-2">
                         <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                         Protocolo Enviado
                     </span>
                 @else
                     <button wire:click.prevent="sendVerification" class="px-6 py-2 bg-amber-500/20 hover:bg-amber-500/30 border border-amber-500/40 text-amber-400 font-black text-[9px] uppercase tracking-[0.2em] rounded-lg transition-all">
                         Verificar Protocolo
                     </button>
                 @endif
             </div>
         </div>
     @endif
 
     <!-- Formulario de Dossier -->
     <form wire:submit="updateProfileInformation" class="space-y-8">
         <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
             <!-- Nombre -->
             <div class="space-y-2">
                 <label for="name" class="font-mono-tech text-[10px] tech-text-secondary uppercase tracking-widest">Nombre Completo de Operador</label>
                 <input wire:model="name" id="name" type="text" class="tech-input w-full p-4" required autofocus autocomplete="name" />
                 <x-input-error class="mt-2" :messages="$errors->get('name')" />
             </div>
 
             <!-- Email -->
             <div class="space-y-2">
                 <div class="flex justify-between">
                     <label for="email" class="font-mono-tech text-[10px] tech-text-secondary uppercase tracking-widest">Enlace de Comunicación (Email)</label>
                     @if(auth()->user()->hasVerifiedEmail())
                         <span class="font-mono-tech text-[8px] text-emerald-400 uppercase flex items-center gap-1">
                             <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                             Verificado
                         </span>
                     @endif
                 </div>
                 <input wire:model="email" id="email" type="email" class="tech-input w-full p-4" required autocomplete="username" />
                 <x-input-error class="mt-2" :messages="$errors->get('email')" />
             </div>
         </div>
 
         <div class="flex items-center justify-between pt-6 border-t border-white/5">
             <p class="font-mono-tech text-[9px] text-zinc-600 uppercase">Confirmación de cambios requerida para actualización</p>
             <div class="flex items-center gap-4">
                 <x-action-message class="font-mono-tech text-[10px] text-emerald-400 uppercase" on="profile-updated">
                     Dossier Actualizado
                 </x-action-message>
                 
                 <button type="submit" class="px-6 py-2 bg-cyan-500 hover:bg-cyan-400 text-black font-black text-[10px] uppercase tracking-widest rounded-lg transition-all shadow-[0_0_15px_rgba(6,182,212,0.3)]">
                     Guardar Cambios
                 </button>
             </div>
         </div>
     </form>
 </section>
