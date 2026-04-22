<div class="min-h-screen bg-gradient-to-b from-[#050505] to-[#19191c] text-zinc-300 p-4 md:p-8"
    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
    <div class="max-w-[1400px] mx-auto space-y-6">

        <!-- Header & Flash Message -->
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-end border-b border-amber-700/50 pb-4">
            <div>
                <h2 class="text-3xl font-bold text-amber-400 tracking-tight uppercase flex items-center gap-3">
                    Gestión de {{ ucfirst($roleFilter) }}
                </h2>
                <p class="text-zinc-400 text-sm mt-1 uppercase tracking-widest">
                    @if($filterManagerName)
                        Clientes del Gestor: <span class="text-amber-400 font-bold">{{ $filterManagerName }}</span>
                    @else
                        Gestión de los tipo de usuarios.
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-3 mt-4 md:mt-0">
                @if($filterManagerId)
                    <a href="{{ route('admin.users.role', 'cliente') }}"
                        class="bg-zinc-800/80 hover:bg-zinc-700 border border-zinc-700/50 text-zinc-300 px-4 py-2 text-[10px] font-bold uppercase tracking-widest rounded-[10px] flex items-center gap-2 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Ver Todos los Clientes
                    </a>
                @endif

                @if (session()->has('message'))
                    <div
                        class="bg-amber-900/40 border border-amber-700/50 text-amber-400 px-4 py-2 text-sm font-medium uppercase tracking-wider rounded-[10px] flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('message') }}
                    </div>
                @endif
            </div>

            @if (session()->has('error'))
                <div
                    class="mt-4 md:mt-0 bg-red-900/40 border border-red-700/50 text-red-400 px-4 py-2 text-sm font-medium uppercase tracking-wider rounded-[10px] flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Columna Izquierda: Lista y Filtros -->
            <div class="lg:col-span-8 flex flex-col space-y-4 order-2 lg:order-1">

                <div
                    class="border border-zinc-700/50 bg-[#0f0f0f]/80 backdrop-blur-md p-4 flex flex-col sm:flex-row gap-4 justify-between items-center rounded-[10px] shadow-lg">
                    <div class="relative w-full sm:w-2/3">
                        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                            <svg class="h-4 w-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" wire:model.live="search" placeholder="Buscar por Nombre, Email, Teléfono..."
                            class="block w-full pl-10 bg-[#050505] border border-zinc-700/50 text-white placeholder-zinc-600 py-2 focus:outline-none focus:border-zinc-400 sm:text-sm transition-colors rounded-[10px]">
                    </div>

                    <div class="w-full sm:w-1/3 flex justify-end">
                        <button wire:click="toggleSort"
                            class="bg-zinc-800/80 hover:bg-zinc-700 border border-zinc-700/50 text-white px-4 py-2 sm:text-sm font-medium flex items-center gap-2 transition-colors w-full sm:w-auto justify-center rounded-[10px] tracking-widest uppercase text-xs">
                            <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($sortDir === 'asc')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"></path>
                                @endif
                            </svg>
                            Orden: {{ $sortDir === 'asc' ? 'A-Z' : 'Z-A' }}
                        </button>
                    </div>
                </div>

                <div
                    class="border border-zinc-700/50 bg-[#0f0f0f]/80 backdrop-blur-md rounded-[10px] shadow-lg overflow-hidden">
                    <ul class="divide-y divide-zinc-800/80">
                        @forelse($users as $u)
                            <li
                                class="p-4 hover:bg-zinc-800/50 transition-colors flex flex-col sm:flex-row justify-between sm:items-center gap-4 group">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span
                                            class="text-xs font-mono text-amber-400 bg-amber-950/30 px-2 py-0.5 rounded-[5px] border border-amber-900/50">ID:{{ str_pad($u->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        <h4
                                            class="text-lg font-bold text-white uppercase tracking-wide flex items-center gap-2">
                                            {{ $u->name }}
                                        </h4>
                                    </div>
                                    <div class="flex flex-wrap gap-2 text-xs font-mono text-zinc-400">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3 h-3 text-zinc-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            {{ $u->email }}
                                        </span>
                                        @if($u->phone)
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-3 h-3 text-zinc-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                    </path>
                                                </svg>
                                                {{ $u->phone }}
                                            </span>
                                        @endif
                                        @if($u->birth_date)
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-3 h-3 text-zinc-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                                {{ $u->birth_date->format('d/M/Y') }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($roleFilter === 'cliente')
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            {{-- Passenger Count Badge --}}
                                            @php $paxCount = $u->passengers->count(); @endphp
                                            <div
                                                class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest px-2 py-1 border rounded-[5px] {{ $paxCount > 0 ? 'text-orange-400 bg-orange-950/30 border-orange-900/50' : 'text-zinc-500 bg-zinc-900 border-zinc-700/50' }}">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z">
                                                    </path>
                                                </svg>
                                                PASAJEROS: {{ $paxCount }}
                                            </div>

                                            @if($u->manager)
                                                <div
                                                    class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest px-2 py-1 border rounded-[5px] text-amber-300 bg-amber-950/30 border-amber-900/50">
                                                    GESTOR: {{ $u->manager->name }}
                                                </div>
                                            @endif
                                        </div>
                                    @elseif($roleFilter === 'gestor')
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <a href="{{ route('admin.users.role', ['role' => 'cliente', 'manager' => $u->id]) }}"
                                                class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest px-2 py-1 border rounded-[5px] 
                                                                                  text-amber-400 bg-amber-950/30 border-amber-900/50 
                                                                                  hover:bg-amber-900/50 hover:border-amber-500 hover:text-amber-300 
                                                                                  transition-all duration-300 ease-in-out">
                                                CLIENTES ASIGNADOS: {{ $u->clients->count() }}
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                <div
                                    class="flex sm:flex-col gap-2 shrink-0 border-t border-zinc-800/80 sm:border-0 pt-3 sm:pt-0">
                                    @if($roleFilter === 'cliente')
                                        <a href="{{ route('admin.passengers', ['userId' => $u->id]) }}"
                                            class="flex-1 sm:flex-none px-4 py-1.5 bg-orange-950/30 hover:bg-orange-900/50 hover:text-orange-400 text-orange-500/80 text-xs font-bold uppercase tracking-wider transition-colors border border-orange-900/30 hover:border-orange-500 rounded-[10px] flex items-center justify-center gap-2">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z">
                                                </path>
                                            </svg>
                                            Pasajeros
                                        </a>
                                    @endif
                                    <button type="button" wire:click="edit({{ $u->id }})"
                                        class="flex-1 sm:flex-none px-4 py-1.5 bg-zinc-800 hover:bg-amber-900/50 hover:text-amber-400 text-zinc-300 text-xs font-bold uppercase tracking-wider transition-colors border border-zinc-700/50 hover:border-amber-400 rounded-[10px] flex items-center justify-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                            </path>
                                        </svg>
                                        Editar
                                    </button>
                                    <button type="button" wire:click="confirmDelete({{ $u->id }})"
                                        class="flex-1 sm:flex-none px-4 py-1.5 bg-black/50 hover:bg-red-950/50 text-red-500/80 hover:text-red-400 text-xs font-bold uppercase tracking-wider transition-colors border border-red-900/30 hover:border-red-900/80 rounded-[10px] flex items-center justify-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                        Eliminar
                                    </button>
                                </div>
                            </li>
                        @empty
                            <div class="p-12 text-center text-zinc-500">
                                <svg class="w-10 h-10 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                <p class="font-medium uppercase tracking-widest text-sm">No se han encontrado registros</p>
                            </div>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Columna Derecha: Formulario Tabulado -->
            <div class="lg:col-span-4 sticky top-6 order-1 lg:order-2">
                <div class="border border-zinc-700/50 bg-[#0f0f0f]/80 backdrop-blur-md rounded-[10px] shadow-lg overflow-hidden transition-colors duration-500 {{ $isEditing ? 'border-2 border-amber-500/80 shadow-[0_0_20px_rgba(168,85,247,0.05)]' : 'border-2 border-zinc-500' }}"
                    x-data="{ tab: 'general' }">

                    <div class="p-4 border-b border-zinc-800 flex justify-between items-center bg-black/40">
                        <h3
                            class="text-sm font-bold uppercase tracking-widest flex items-center gap-2 {{ $isEditing ? 'text-amber-400' : 'text-white' }}">
                            @if($isEditing)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                Modo Edición
                            @else
                                <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Nuevo Cliente
                            @endif
                        </h3>

                        @if($isEditing)
                            <button type="button" wire:click="setCreateMode" @click="tab = 'general'"
                                class="text-[9px] uppercase font-bold tracking-widest bg-zinc-800 hover:bg-white hover:text-black text-zinc-300 px-2 py-1 transition-colors border border-zinc-700/50 rounded-[5px]">
                                CREAR NUEVO
                            </button>
                        @endif
                    </div>

                    <div class="flex bg-black">
                        <button type="button" @click="tab = 'general'"
                            :class="tab === 'general' ? 'bg-[#0f0f0f] border-t-2 border-amber-500 text-white' : 'bg-black text-zinc-500 hover:bg-zinc-900 hover:text-zinc-300 border-t-2 border-transparent'"
                            class="flex-1 py-6 text-[10px] font-bold uppercase tracking-widest transition-colors">
                            RESPONSABLE DE LAS RESERVAS
                        </button>
                        @if($roleFilter === 'gestor')
                            <button type="button" @click="tab = 'clients'"
                                :class="tab === 'clients' ? 'bg-[#0f0f0f] border-t-2 border-amber-500 text-white' : 'bg-black text-zinc-500 hover:bg-zinc-900 hover:text-zinc-300 border-t-2 border-transparent'"
                                class="flex-1 py-3 text-[10px] font-bold uppercase tracking-widest transition-colors border-l border-zinc-800">
                                Clientes Asignados
                            </button>
                        @endif
                    </div>

                    <form wire:submit.prevent="confirmSave" class="p-6 space-y-4">

                        <div x-show="tab === 'general'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                            @if($isEditing)
                                <div>
                                    <label
                                        class="block text-[10px] font-bold text-zinc-500 mb-1 uppercase tracking-widest flex items-center gap-1.5">
                                        ID Lógico
                                    </label>
                                    <input type="text" value="{{ str_pad($userId, 5, '0', STR_PAD_LEFT) }}" readonly
                                        class="w-full bg-black border border-zinc-800 px-3 py-2 text-zinc-600 font-mono text-sm cursor-not-allowed outline-none rounded-[10px]">
                                </div>
                            @endif

                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-400' : 'text-zinc-400' }} mb-1 uppercase tracking-widest">
                                    Nombre Completo
                                </label>
                                <input type="text" wire:model.live.debounce.300ms="name"
                                    class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500' : 'border-zinc-700/50 focus:border-zinc-400' }} text-white px-3 py-2 placeholder-zinc-700 focus:outline-none transition-colors text-sm rounded-[10px]">
                                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-400' : 'text-zinc-400' }} mb-1 uppercase tracking-widest">
                                    Email Válido
                                    @if($roleFilter === 'gestor')
                                        <span
                                            class="text-zinc-500 font-normal">({{ $isEditing ? 'Inmutable' : 'Autogenerado' }})</span>
                                    @endif
                                </label>
                                <input type="email" wire:model="email" {{ $roleFilter === 'gestor' ? 'readonly' : '' }}
                                    class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500' : 'border-zinc-700/50 focus:border-zinc-400' }} text-white px-3 py-2 placeholder-zinc-700 focus:outline-none transition-colors text-sm rounded-[10px] {{ $roleFilter === 'gestor' ? 'opacity-60 cursor-not-allowed' : '' }}">
                                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            @if($isEditing && $roleFilter === 'gestor')
                                <div class="bg-purple-950/20 border border-purple-900/40 rounded-[10px] p-3 mb-4">
                                    <p
                                        class="text-[10px] text-purple-500 uppercase tracking-widest font-bold mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                            </path>
                                        </svg>
                                        Recuperación de Acceso
                                    </p>
                                    <button type="button" wire:click="resetPassword"
                                        class="w-full block text-center py-2 px-4 bg-purple-900/30 hover:bg-purple-800/50 text-purple-400 text-[10px] font-bold uppercase tracking-widest rounded-[8px] border border-purple-800/50 hover:border-purple-500 transition-all">
                                        Generar Nueva Clave Temporal
                                    </button>
                                </div>
                            @endif

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-[10px] font-bold text-zinc-400 mb-1 uppercase tracking-widest">
                                        Teléfono
                                    </label>
                                    <input type="text" wire:model="phone"
                                        class="w-full bg-[#050505] border border-zinc-700/50 focus:border-zinc-400 text-white px-3 py-2 placeholder-zinc-700 focus:outline-none transition-colors text-sm rounded-[10px]"
                                        placeholder="+0000000000">
                                    @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-bold text-zinc-400 mb-1 uppercase tracking-widest">
                                        Nacimiento
                                    </label>
                                    <input type="date" wire:model="birth_date"
                                        class="w-full bg-[#050505] border border-zinc-700/50 focus:border-zinc-400 text-white font-mono px-3 py-2 focus:outline-none transition-colors text-sm rounded-[10px]">
                                    @error('birth_date') <span
                                    class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            @if($roleFilter === 'cliente')
                                <div>
                                    <label
                                        class="block text-[10px] font-bold text-amber-400 mb-1 uppercase tracking-widest">
                                        Vincular a Gestor Logístico
                                    </label>
                                    <select required wire:model="assigned_manager_id"
                                        class="w-full bg-[#050505] border border-amber-900/40 focus:border-amber-500 text-white px-3 py-2 focus:outline-none transition-colors text-sm rounded-[10px]">
                                        <option value="">-- Sin Gestor Asignado --</option>
                                        @foreach($managers as $manager)
                                            <option value="{{ $manager->id }}">{{ $manager->name }} ({{ $manager->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_manager_id') <span
                                    class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                @if($isEditing && $userId)
                                    <div class="bg-cyan-950/20 border border-cyan-900/40 rounded-[10px] p-3 mt-2">
                                        <p
                                            class="text-[10px] text-cyan-500 uppercase tracking-widest font-bold mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z">
                                                </path>
                                            </svg>
                                            Módulo de Pasajeros
                                        </p>
                                        <a href="{{ route('admin.passengers', ['userId' => $userId]) }}"
                                            class="block text-center py-2 px-4 bg-cyan-900/30 hover:bg-cyan-800/50 text-cyan-400 text-[10px] font-bold uppercase tracking-widest rounded-[8px] border border-cyan-800/50 hover:border-cyan-500 transition-all">
                                            Gestionar Pasajeros de este Cliente →
                                        </a>
                                    </div>
                                @endif
                            @endif
                        </div>
                        @if($roleFilter === 'gestor')
                            <div x-show="tab === 'clients'" style="display: none;"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">

                                <div class="bg-black border border-zinc-800 rounded-[10px] p-4">
                                    <label
                                        class="block text-[10px] font-bold text-amber-400 mb-2 uppercase tracking-widest">
                                        Vincular Cliente (ID o Email)
                                    </label>
                                    <div class="relative">
                                        <input type="text" wire:model.live.debounce.300ms="clientSearch"
                                            class="w-full bg-[#050505] border border-amber-900/50 focus:border-amber-500 text-white px-3 py-2 text-sm rounded-[10px]">
                                        <div wire:loading wire:target="clientSearch" class="absolute right-3 top-2.5">
                                            <svg class="animate-spin h-4 w-4 text-amber-500"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>

                                    @if(!empty($clientSearchResults))
                                        <ul class="mt-2 border border-zinc-800 rounded-[10px] overflow-hidden bg-[#090909]">
                                            @foreach($clientSearchResults as $res)
                                                <li
                                                    class="flex justify-between items-center p-2 border-b border-zinc-800 last:border-0 hover:bg-zinc-900/50">
                                                    <div>
                                                        <p class="text-xs font-bold text-white">{{ $res['name'] }} <span
                                                                class="text-zinc-500">(#{{ str_pad($res['id'], 5, '0', STR_PAD_LEFT) }})</span>
                                                        </p>
                                                        <p class="text-[10px] font-mono text-zinc-400">{{ $res['email'] }}</p>
                                                    </div>
                                                    <button type="button" wire:click="requestAddClient({{ $res['id'] }})"
                                                        class="text-[10px] px-2 py-1 bg-amber-900/50 hover:bg-amber-500 hover:text-black text-amber-500 border border-amber-900 rounded-[5px] uppercase font-bold transition-colors">
                                                        Añadir
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @elseif(strlen($clientSearch) > 1)
                                        <p class="text-[10px] text-zinc-500 mt-2 font-mono">No se encontraron clientes
                                            coincidiendo con "{{ $clientSearch }}".</p>
                                    @endif
                                </div>

                                <hr class="border-zinc-800">

                                <!-- Assigned List -->
                                <div>
                                    <h4 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-3">
                                        Clientes Actuales ({{ count($assignedClients) }})
                                    </h4>
                                    @if(count($assignedClients) > 0)
                                        <ul class="space-y-2">
                                            @foreach($assignedClients as $ac)
                                                <li
                                                    class="flex justify-between items-center p-3 bg-[#050505] border border-zinc-800 rounded-[10px]">
                                                    <div>
                                                        <p class="text-xs font-bold text-white">{{ $ac['name'] }} <span
                                                                class="text-zinc-500">(#{{ str_pad($ac['id'], 5, '0', STR_PAD_LEFT) }})</span>
                                                        </p>
                                                        <p
                                                            class="text-[9px] font-mono text-amber-600/80 uppercase tracking-widest mt-0.5">
                                                            Gestor Anterior: <span
                                                                class="text-zinc-400">{{ $ac['old_manager'] ?? 'Ninguno' }}</span>
                                                        </p>
                                                    </div>
                                                    <button type="button" wire:click="removeClient({{ $ac['id'] }})"
                                                        class="text-red-500 hover:text-red-400 bg-red-950/30 p-1.5 rounded-[5px] border border-red-900/30 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p
                                            class="text-xs text-zinc-600 bg-zinc-900/30 p-4 text-center rounded-[10px] border border-zinc-800/50">
                                            Este gestor no administra actualmente a ningún cliente.</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Footer Acción Guardar -->
                        <div class="pt-6 mt-4 border-t border-zinc-800">
                            <button type="submit"
                                class="w-full {{ $isEditing ? 'bg-amber-600 hover:bg-amber-500 text-white border-amber-500' : 'bg-white hover:bg-zinc-200 text-black border-white' }} font-bold uppercase tracking-widest py-3 px-4 transition-colors text-xs rounded-[10px] border">
                                @if($isEditing)
                                    Actualizar {{ ucfirst($roleFilter) }}
                                @else
                                    Guardar Nuevo {{ ucfirst($roleFilter) }}
                                @endif
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Modals de Livewire -->
    @if($showSaveModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4" x-data
            x-init="document.body.style.overflow='hidden'" @destroyed="document.body.style.overflow=''">
            <div class="bg-[#0f0f0f] border border-zinc-700/50 rounded-[15px] max-w-sm w-full overflow-hidden shadow-2xl"
                @click.away="$wire.set('showSaveModal', false)">
                <div class="p-6 border-b border-zinc-800 flex items-start gap-4">
                    <div
                        class="w-10 h-10 rounded-full {{ $isEditing ? 'bg-amber-500/10 border-amber-500/30 text-amber-400' : 'bg-zinc-800 border-zinc-600 text-white' }} flex items-center justify-center border shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white uppercase tracking-widest mb-1">Confirmación</h3>
                        <p class="text-zinc-500 text-xs leading-relaxed">
                            {{ $isEditing ? "Se actualizarán los datos modificados del usuario." : "Se dará de alta un nuevo usuario." }}
                        </p>
                    </div>
                </div>
                <div class="flex bg-[#050505] p-3 gap-3">
                    <button type="button" wire:click="$set('showSaveModal', false)"
                        class="flex-1 py-2.5 px-4 text-xs font-bold text-zinc-400 hover:text-white bg-zinc-900 hover:bg-zinc-800 rounded-[10px] border border-zinc-800 transition-colors">
                        Cancelar
                    </button>
                    <button type="button" wire:click="executeSave"
                        class="flex-1 py-2.5 px-4 text-xs font-bold text-white {{ $isEditing ? 'bg-amber-600 hover:bg-amber-500' : 'bg-black border hover:bg-white hover:text-black' }} rounded-[10px] transition-colors">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4" x-data
            x-init="document.body.style.overflow='hidden'" @destroyed="document.body.style.overflow=''">
            <div class="bg-[#0f0f0f] border border-red-900/50 rounded-[15px] max-w-sm w-full overflow-hidden shadow-[0_0_30px_rgba(220,38,38,0.1)]"
                @click.away="$wire.set('showDeleteModal', false)">
                <div class="p-6 border-b border-red-900/30 flex items-start gap-4">
                    <div
                        class="w-10 h-10 rounded-full bg-red-950/50 border border-red-900/50 text-red-500 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-red-500 uppercase tracking-widest mb-1">
                            {{ ($deleteImpactInfo['type'] ?? '') === 'client_soft' ? 'Desactivación de Cuenta' : 'Eliminación de Usuario' }}
                        </h3>
                        @if(($deleteImpactInfo['type'] ?? '') === 'client_soft')
                            <p class="text-zinc-500 text-xs leading-relaxed">
                                El cliente <strong class="text-white">{{ $deleteImpactInfo['name'] ?? '' }}</strong> tiene
                                reservas pagadas.
                                Se eliminará el usuario, sus reservas activas y se conservará el registro fiscal.
                            </p>
                            <div class="mt-2 flex gap-2 flex-wrap">
                                <span
                                    class="text-[9px] bg-red-950/50 border border-red-900/50 text-red-400 px-2 py-0.5 rounded font-bold uppercase">{{ $deleteImpactInfo['passenger_count'] ?? 0 }}
                                    pasajeros</span>
                                <span
                                    class="text-[9px] bg-red-950/50 border border-red-900/50 text-red-400 px-2 py-0.5 rounded font-bold uppercase">{{ $deleteImpactInfo['reservation_count'] ?? 0 }}
                                    reservas</span>
                            </div>
                        @elseif(($deleteImpactInfo['type'] ?? '') === 'client_hard')
                            <p class="text-zinc-500 text-xs leading-relaxed">
                                El cliente <strong class="text-white">{{ $deleteImpactInfo['name'] ?? '' }}</strong> no tiene
                                reservass.
                                Se eliminará el usuario y todos sus datos relacionados del servidor.
                            </p>
                        @else
                            <p class="text-zinc-500 text-xs leading-relaxed">
                                Este usuario y todos sus datos relacionados serán eliminados.
                            </p>
                        @endif
                    </div>
                </div>
                <div class="flex bg-[#050505] p-3 gap-3">
                    <button type="button" wire:click="$set('showDeleteModal', false)"
                        class="flex-1 py-2.5 px-4 text-xs font-bold text-zinc-400 hover:text-white bg-zinc-900 hover:bg-zinc-800 rounded-[10px] border border-zinc-800 transition-colors">
                        Cancelar
                    </button>
                    <button type="button" wire:click="executeDelete"
                        class="flex-1 py-2.5 px-4 text-xs font-bold text-white bg-red-900 hover:bg-red-800 rounded-[10px] transition-colors border border-red-900/50">
                        {{ ($deleteImpactInfo['type'] ?? '') === 'client_soft' ? 'Desactivar' : 'Eliminar' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showMigrationModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4" x-data
            x-init="document.body.style.overflow='hidden'" @destroyed="document.body.style.overflow=''">
            <div
                class="bg-[#0f0f0f] border border-amber-900/50 rounded-[15px] max-w-md w-full overflow-hidden shadow-[0_0_30px_rgba(245,158,11,0.1)]">
                <div class="p-6 border-b border-amber-900/30 flex items-start gap-4">
                    <div
                        class="w-10 h-10 rounded-full bg-amber-950/50 border border-amber-900/50 text-amber-500 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-amber-500 uppercase tracking-widest mb-1">Migración de gestor
                            Obligatoria</h3>
                        <p class="text-zinc-500 text-xs leading-relaxed">
                            El gestor <strong class="text-white">{{ $deleteImpactInfo['name'] ?? '' }}</strong> administra
                            <strong class="text-amber-400">{{ $deleteImpactInfo['client_count'] ?? 0 }} clientes</strong>.
                            Debe reasignarlos a otro gestor antes de poder eliminar esta cuenta.
                        </p>
                    </div>
                </div>
                <div class="p-4 border-b border-zinc-800">
                    <label class="block text-[10px] font-bold text-amber-400 mb-2 uppercase tracking-widest">Migrar clientes
                        al Gestor:</label>
                    <select wire:model.live="migrationTargetGestorId"
                        class="w-full bg-[#050505] border border-amber-900/40 focus:border-amber-500 text-white px-3 py-2 text-sm rounded-[10px]">
                        <option value="">-- Seleccionar Gestor Destino --</option>
                        @foreach($availableGestors as $g)
                            @if($g->id !== $deleteId)
                                <option value="{{ $g->id }}">{{ $g->name }} ({{ $g->email }})</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="flex bg-[#050505] p-3 gap-3">
                    <button type="button" wire:click="$set('showMigrationModal', false)"
                        class="flex-1 py-2.5 px-4 text-xs font-bold text-zinc-400 hover:text-white bg-zinc-900 hover:bg-zinc-800 rounded-[10px] border border-zinc-800 transition-colors">
                        Cancelar
                    </button>
                    <button type="button" wire:click="executeMigrationAndDelete" {{ !$migrationTargetGestorId ? 'disabled' : '' }}
                        class="flex-1 py-2.5 px-4 text-xs font-bold text-black bg-amber-500 hover:bg-amber-400 rounded-[10px] transition-colors border border-amber-600 disabled:opacity-30 disabled:cursor-not-allowed">
                        Migrar y Eliminar
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showOverrideModal && $pendingClientOverride !== null)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4" x-data
            x-init="document.body.style.overflow='hidden'" @destroyed="document.body.style.overflow=''">
            <div
                class="bg-[#0f0f0f] border border-amber-900/50 rounded-[15px] max-w-sm w-full overflow-hidden shadow-[0_0_30px_rgba(245,158,11,0.1)]">
                <div class="p-6 border-b border-amber-900/30 flex items-start gap-4">
                    <div
                        class="w-10 h-10 rounded-full bg-amber-950/50 border border-amber-900/50 text-amber-500 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-amber-500 uppercase tracking-widest mb-1">Conflicto de Gestor</h3>
                        <p class="text-zinc-500 text-xs leading-relaxed">
                            El cliente
                            <strong>{{ is_array($pendingClientOverride) ? $pendingClientOverride['name'] : $pendingClientOverride->name }}</strong>
                            ya está supervisado por
                            <strong>{{ is_array($pendingClientOverride) ? ($pendingClientOverride['manager']['name'] ?? 'N/A') : ($pendingClientOverride->manager ? $pendingClientOverride->manager->name : 'N/A') }}</strong>.
                        </p>
                        <p class="text-zinc-400 text-[10px] mt-2 font-bold uppercase tracking-widest">¿Sobrescribir Gestor
                            Anterior?</p>
                    </div>
                </div>
                <div class="flex bg-[#050505] p-3 gap-3">
                    <button type="button" wire:click="cancelOverrideClient"
                        class="flex-1 py-2.5 px-4 text-xs font-bold text-zinc-400 hover:text-white bg-zinc-900 hover:bg-zinc-800 rounded-[10px] border border-zinc-800 transition-colors">
                        Cancelar
                    </button>
                    <button type="button" wire:click="confirmOverrideClient"
                        class="flex-1 py-2.5 px-4 text-xs font-bold text-black bg-amber-500 hover:bg-amber-400 rounded-[10px] transition-colors border border-amber-600">
                        Sobrescribir
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showPasswordModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4" x-data
            x-init="document.body.style.overflow='hidden'" @destroyed="document.body.style.overflow=''">
            <div
                class="bg-[#0b0b0b] border-2 border-purple-900/50 rounded-[20px] max-w-sm w-full overflow-hidden shadow-[0_0_50px_rgba(168,85,247,0.15)] animate-in zoom-in duration-300">
                <div class="p-8 text-center">
                    <div
                        class="w-16 h-16 rounded-full bg-purple-950/30 border border-purple-500/50 text-purple-400 flex items-center justify-center mx-auto mb-6 shadow-[0_0_20px_rgba(168,85,247,0.2)]">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>

                    <h3 class="text-lg font-black text-white uppercase tracking-[0.2em] mb-2">Credenciales Iris</h3>
                    <p class="text-zinc-500 text-xs leading-relaxed mb-6 uppercase tracking-wider">
                        Se ha enviado un correo con las llaves de acceso al correo electrónico registrado.
                    </p>

                    <div class="bg-black/50 border border-zinc-800 rounded-[12px] p-4 mb-6">
                        <label class="block text-[10px] font-bold text-zinc-600 uppercase tracking-widest mb-1.5">Llave
                            Maestra Temporal</label>
                        <div class="text-xl font-mono text-purple-400 font-black tracking-widest">{{ $tempPassword }}</div>
                    </div>

                    <button type="button" wire:click="$set('showPasswordModal', false)"
                        class="w-full py-3.5 px-6 bg-purple-600 hover:bg-purple-500 text-white text-[10px] font-black uppercase tracking-[0.3em] rounded-[10px] transition-all shadow-[0_4px_15px_rgba(147,51,234,0.3)] hover:shadow-[0_6px_25px_rgba(147,51,234,0.5)] active:scale-[0.98]">
                        Confirmar y Cerrar
                    </button>

                    <p class="text-[9px] text-zinc-700 uppercase font-bold mt-6 tracking-widest italic">
                        &copy; Iris Aerospace
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>