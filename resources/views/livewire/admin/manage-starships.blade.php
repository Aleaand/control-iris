<div class="min-h-screen bg-gradient-to-b from-[#050505] to-[#19191c] text-zinc-300 p-4 md:p-8"
    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
    <div class="max-w-[1400px] mx-auto space-y-6">

        <!-- Header & Flash Message -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end border-b border-blue-300 pb-4">
            <div>
                <h2 class="text-3xl font-bold text-blue-300 tracking-tight uppercase flex items-center gap-3">
                    Naves
                </h2>
                <p class="text-zinc-400 text-sm mt-1 uppercase tracking-widest">
                    Gestión de Naves Espaciales
                </p>
            </div>

            @if (session()->has('message'))
                <div
                    class="mt-4 md:mt-0 bg-green-900/40 border border-green-700/50 text-green-400 px-4 py-2 text-sm font-medium uppercase tracking-wider rounded-[10px] flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('message') }}
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Columna Izquierda: Lista y Filtros -->
            <div class="lg:col-span-8 flex flex-col space-y-4 order-2 lg:order-1">

                <!-- Buscador y Filtro -->
                <div
                    class="border border-zinc-700/50 bg-[#0f0f0f]/80 backdrop-blur-md p-4 flex flex-col sm:flex-row gap-4 justify-between items-center rounded-[10px] shadow-lg">
                    <div class="relative w-full sm:w-2/3">
                        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                            <svg class="h-4 w-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" wire:model.live="search" placeholder="Buscar por Nombre o ID"
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

                <!-- Lista de Resultados -->
                <div
                    class="border border-zinc-700/50 bg-[#0f0f0f]/80 backdrop-blur-md rounded-[10px] shadow-lg overflow-hidden">
                    <ul class="divide-y divide-zinc-800/80">
                        @forelse($starships as $ship)
                            <li
                                class="p-4 hover:bg-zinc-800/50 transition-colors flex flex-col sm:flex-row justify-between sm:items-center gap-4 group">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span
                                            class="text-xs font-mono text-zinc-500 bg-black px-2 py-0.5 rounded-[5px] border border-zinc-800">ID:{{ str_pad($ship->id, 4, '0', STR_PAD_LEFT) }}</span>
                                        <h4
                                            class="text-lg font-bold text-blue-300 uppercase tracking-wide flex items-center gap-2">
                                            {{ $ship->name }}
                                        </h4>
                                        @if($ship->status === 'active')
                                            <span
                                                class="px-2 py-0.5 text-[10px] uppercase tracking-widest bg-green-900/30 text-green-400 border border-green-800/50 rounded-[5px]">Active</span>
                                        @elseif($ship->status === 'maintenance')
                                            <span
                                                class="px-2 py-0.5 text-[10px] uppercase tracking-widest bg-amber-900/30 text-amber-400 border border-amber-800/50 rounded-[5px]">Maintenance</span>
                                        @else
                                            <span
                                                class="px-2 py-0.5 text-[10px] uppercase tracking-widest bg-red-900/30 text-red-400 border border-red-800/50 rounded-[5px]">Retired</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <div
                                            class="inline-flex items-center gap-1.5 text-xs font-mono text-zinc-300 bg-zinc-900/80 px-2 py-1 border border-zinc-700/50 rounded-[5px]">
                                            <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                </path>
                                            </svg>
                                            GENERAL: {{ $ship->general_capacity }}
                                        </div>
                                        <div
                                            class="inline-flex items-center gap-1.5 text-xs font-mono text-amber-300/80 bg-zinc-900/80 px-2 py-1 border border-amber-900/30 rounded-[5px]">
                                            <svg class="w-3.5 h-3.5 text-amber-600/50" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                                                </path>
                                            </svg>
                                            VIP: {{ $ship->vip_capacity }}
                                        </div>
                                        <div
                                            class="inline-flex items-center gap-1.5 text-xs font-mono text-cyan-300/80 bg-zinc-900/80 px-2 py-1 border border-cyan-900/30 rounded-[5px]">
                                            <svg class="w-3.5 h-3.5 text-cyan-600/50" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                </path>
                                            </svg>
                                            CREW: {{ $ship->crew_capacity }}
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex sm:flex-col gap-2 shrink-0 border-t border-zinc-800/80 sm:border-0 pt-3 sm:pt-0">
                                    <button type="button" wire:click="edit({{ $ship->id }})"
                                        class="flex-1 sm:flex-none px-4 py-1.5 bg-zinc-800 hover:bg-yellow-900/50 hover:text-yellow-400 text-zinc-300 text-xs font-bold uppercase tracking-wider transition-colors border border-zinc-700/50 hover:border-yellow-400 rounded-[10px] flex items-center justify-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                            </path>
                                        </svg>
                                        Editar
                                    </button>
                                    <button type="button" wire:click="confirmDelete({{ $ship->id }})"
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
                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                                    </path>
                                </svg>
                                <p class="font-medium uppercase tracking-widest text-sm">No se han encontrado registros</p>
                            </div>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Columna Derecha: Formulario -->
            <div class="lg:col-span-4 sticky top-6 order-1 lg:order-2">
                <div
                    class="border border-zinc-700/50 bg-[#0f0f0f]/80 backdrop-blur-md p-6 rounded-[10px] shadow-lg transition-colors duration-500 {{ $isEditing ? 'border-2 border-amber-500/80 shadow-[0_0_20px_rgba(245,158,11,0.05)]' : 'border-2 border-zinc-500' }}">

                    <div class="flex justify-between items-center mb-6 border-b border-zinc-800 pb-4">
                        <h3
                            class="text-sm font-bold uppercase tracking-widest flex items-center gap-2 {{ $isEditing ? 'text-amber-400' : 'text-white' }}">
                            @if($isEditing)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                Modo Edición
                            @else
                                <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Nueva Nave
                            @endif
                        </h3>

                        @if($isEditing)
                            <button type="button" wire:click="setCreateMode"
                                class="text-[10px] uppercase font-bold tracking-widest bg-zinc-800/80 hover:bg-white hover:text-black text-zinc-300 px-2.5 py-1.5 transition-colors border border-zinc-700/50 rounded-[5px] flex items-center gap-1.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Crear Nuevo
                            </button>
                        @endif
                    </div>

                    <form wire:submit.prevent="confirmSave" class="space-y-4" x-data="{ status: @entangle('status') }">
                        @if($isEditing)
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-zinc-500 mb-1 uppercase tracking-widest flex items-center gap-1.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                        </path>
                                    </svg>
                                    ID de Nave
                                </label>
                                <input type="text" value="{{ str_pad($starshipId, 4, '0', STR_PAD_LEFT) }}" readonly
                                    class="w-full bg-[#050505] border border-zinc-800 px-3 py-2 text-zinc-600 font-mono text-sm cursor-not-allowed outline-none rounded-[10px]">
                            </div>
                        @endif

                        <div>
                            <label
                                class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-500/80' : 'text-zinc-400' }} mb-1 uppercase tracking-widest pl-2">
                                Nombre de la Nave
                            </label>
                            <input type="text" wire:model="name"
                                class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-zinc-700/50 focus:border-zinc-400 text-white' }} px-3 py-2 placeholder-zinc-700 focus:outline-none transition-colors text-sm rounded-[10px]">
                            @error('name') <span
                                class="text-red-500 text-[10px] font-bold mt-1 block uppercase italic">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-500/80' : 'text-zinc-400' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                    <span>Cap.<br>Nova</span>
                                    <div x-data="{ open: false }" class="relative flex items-center">
                                        <button type="button" @mouseenter="open = true" @mouseleave="open = false"
                                            class="text-zinc-600 hover:text-blue-400 transition-colors focus:outline-none">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                        </button>
                                        <div x-show="open" x-transition
                                            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-2 bg-zinc-900/95 border border-zinc-700 text-white text-[9px] leading-tight text-center rounded-md shadow-2xl backdrop-blur-sm z-50 pointer-events-none uppercase">
                                            Capacidad Estandar para pasajeros.
                                            <div
                                                class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-zinc-700">
                                            </div>
                                        </div>
                                    </div>
                                </label>
                                <input type="number" min="0" wire:model="general_capacity"
                                    class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-zinc-700/50 focus:border-zinc-400 text-white' }} px-3 py-2 placeholder-zinc-700 font-mono focus:outline-none transition-colors text-sm rounded-[10px]">
                                @error('general_capacity') <span
                                    class="text-red-500 text-[10px] font-bold mt-1 block uppercase italic">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-500/80' : 'text-zinc-400' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                    <span>Cap.<br> SuperNova</span>
                                    <div x-data="{ open: false }" class="relative flex items-center">
                                        <button type="button" @mouseenter="open = true" @mouseleave="open = false"
                                            class="text-zinc-600 hover:text-red-400 transition-colors focus:outline-none">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                        </button>
                                        <div x-show="open" x-transition
                                            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-2 bg-zinc-900/95 border border-red-900/30 text-white text-[9px] leading-tight text-center rounded-md shadow-2xl backdrop-blur-sm z-50 pointer-events-none uppercase">
                                            Capacidad exclusiva para pasajeros.
                                            <div
                                                class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-red-900/30">
                                            </div>
                                        </div>
                                    </div>
                                </label>
                                <input type="number" min="0" wire:model="vip_capacity"
                                    class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-zinc-700/50 focus:border-zinc-400 text-white' }} px-3 py-2 placeholder-zinc-700 font-mono focus:outline-none transition-colors text-sm rounded-[10px]">
                                @error('vip_capacity') <span
                                    class="text-red-500 text-[10px] font-bold mt-1 block uppercase italic">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-500/80' : 'text-zinc-400' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                    <span>Cap.<br> Tripulación</span>
                                    <div x-data="{ open: false }" class="relative flex items-center">
                                        <button type="button" @mouseenter="open = true" @mouseleave="open = false"
                                            class="text-zinc-600 hover:text-cyan-400 transition-colors focus:outline-none">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                        </button>
                                        <div x-show="open" x-transition
                                            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-2 bg-zinc-900/95 border border-cyan-900/30 text-white text-[9px] leading-tight text-center rounded-md shadow-2xl backdrop-blur-sm z-50 pointer-events-none uppercase">
                                            Capacidad para empleados.
                                            <div
                                                class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-cyan-900/30">
                                            </div>
                                        </div>
                                    </div>
                                </label>
                                <input type="number" min="0" wire:model="crew_capacity"
                                    class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-zinc-700/50 focus:border-zinc-400 text-white' }} px-3 py-2 placeholder-zinc-700 font-mono focus:outline-none transition-colors text-sm rounded-[10px]">
                                @error('crew_capacity') <span
                                    class="text-red-500 text-[10px] font-bold mt-1 block uppercase italic">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-500/80' : 'text-zinc-400' }} mb-1 uppercase tracking-widest pl-2">
                                    Coste Base*AU(€)
                                </label>
                                <input type="number" min="0" step="0.01" wire:model="operational_cost_per_au"
                                    class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-zinc-700/50 focus:border-zinc-400 text-white' }} px-3 py-2 placeholder-zinc-700 font-mono focus:outline-none transition-colors text-sm rounded-[10px]">
                                @error('operational_cost_per_au') <span
                                    class="text-red-500 text-[10px] font-bold mt-1 block uppercase italic">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-500/80' : 'text-zinc-400' }} mb-1 uppercase tracking-widest pl-2">
                                    Estado Operativo
                                </label>
                                <select wire:model.live="status"
                                    class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-zinc-700/50 focus:border-zinc-400 text-white' }} px-3 py-2 focus:outline-none transition-colors text-sm rounded-[10px] appearance-none cursor-pointer">
                                    <option value="active">Activa</option>
                                    <option value="maintenance">Mantenimiento</option>
                                    <option value="retired">Retirada</option>
                                </select>
                            </div>
                        </div>
                        <div class="border-t border-zinc-800/80 pt-4 mt-2">
                            <p
                                class="text-[9px] font-bold text-cyan-500/70 uppercase tracking-[0.2em] mb-3 flex items-center gap-1.5">
                                Parámetros de Vuelos
                            </p>
                            <div class="mb-3">
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-500/80' : 'text-zinc-400' }} mb-1 uppercase tracking-widest pl-2 flex items-center gap-1.5">
                                    Velocidad
                                    <div x-data="{ open: false }" class="relative flex items-center">
                                        <span @mouseenter="open = true" @mouseleave="open = false"
                                            class="cursor-help border-b border-dotted border-zinc-500 pb-0.5 text-zinc-500 hover:text-white transition-colors">
                                            (Horas/AU)
                                        </span>

                                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 translate-y-1"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-40 p-2 bg-zinc-900/95 border border-zinc-700 text-white text-[9px] leading-tight text-center rounded-md shadow-2xl backdrop-blur-sm z-50 pointer-events-none">
                                            <span class="font-bold text-cyan-400">EFICIENCIA TEMPORAL</span><br>
                                            Horas necesarias para recorrer 1 AU.
                                            <diV
                                                class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-zinc-700">
                                            </div>
                                        </div>
                                    </div>
                                </label>
                                <input type="number" min="0.0001" step="0.0001" wire:model="cruise_speed_au"
                                    placeholder="24.0000"
                                    class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-cyan-900/30 ring-1 ring-cyan-900/50 focus:border-cyan-400 text-cyan-100' }} px-3 py-2 placeholder-zinc-700 font-mono focus:outline-none transition-colors text-sm rounded-[10px]">
                                @error('cruise_speed_au') <span
                                class="text-red-500 text-[10px] font-bold mt-1 block uppercase italic">{{ $message }}</span>@enderror
                            </div>

                            {{-- Tarifas de Tripulación --}}
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                {{-- Columna 1 --}}
                                <div>
                                    <label
                                        class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-500/80' : 'text-zinc-400' }} mb-1 uppercase tracking-widest pl-2">
                                        Tripulación €/hora
                                    </label>
                                    <input type="number" min="0" step="0.01" wire:model="crew_hourly_rate"
                                        placeholder="10.00"
                                        class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-zinc-700/50 focus:border-zinc-400 text-white' }} px-3 py-2 placeholder-zinc-700 font-mono focus:outline-none transition-colors text-sm rounded-[10px]">
                                    @error('crew_hourly_rate') <span
                                    class="text-red-500 text-[10px] font-bold mt-1 block uppercase italic">{{ $message }}</span>@enderror
                                </div>

                                {{-- Columna 2 --}}
                                <div>
                                    <label
                                        class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-500/80' : 'text-zinc-400' }} mb-1 uppercase tracking-widest pl-2">
                                        Espera tripulación €/día
                                    </label>
                                    <input type="number" min="0" step="0.01" wire:model="crew_daily_rate"
                                        placeholder="100.00"
                                        class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-zinc-700/50 focus:border-zinc-400 text-white' }} px-3 py-2 placeholder-zinc-700 font-mono focus:outline-none transition-colors text-sm rounded-[10px]">
                                </div>

                                {{-- Bloque de Mantenimiento: AÑADIDO col-span-2 --}}
                                <div x-show="status === 'maintenance'" x-transition
                                    class="col-span-2 mt-4 grid grid-cols-2 gap-4"> {{-- Este div ahora ocupa las dos
                                    columnas --}}
                                    <div>
                                        <label
                                            class="block text-[10px] font-bold text-amber-500/80 mb-1 uppercase tracking-widest pl-2 flex items-center gap-2">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            Inicio del Mantenimiento
                                        </label>
                                        <input type="date" wire:model="maintenance_start_date"
                                            class="w-full bg-[#050505] border border-amber-900/40 text-amber-100 px-3 py-2 focus:outline-none focus:border-amber-500 transition-colors text-sm rounded-[10px] [color-scheme:dark]">
                                    </div>

                                    <div>
                                        <label
                                            class="block text-[10px] font-bold text-red-500/80 mb-1 uppercase tracking-widest pl-2 flex items-center gap-2">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            Fin del Mantenimiento
                                        </label>
                                        <input type="date" wire:model="maintenance_end_date"
                                            class="w-full bg-[#050505] border border-red-900/30 text-red-100 px-3 py-2 focus:outline-none focus:border-red-500 transition-colors text-sm rounded-[10px] [color-scheme:dark]">
                                    </div>
                                </div>

                                <div class="col-span-2 pt-4 mt-2 border-t border-zinc-800">
                                    <button type="submit"
                                        class="w-full {{ $isEditing ? 'bg-amber-600 hover:bg-amber-500 text-black border-amber-500' : 'bg-white hover:bg-zinc-200 text-black border-white' }} font-bold uppercase tracking-widest py-3 px-4 transition-colors text-xs rounded-[10px] border flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ $isEditing ? 'Actualizar Nave' : 'Registrar Nave' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Confirmar Guardar -->
    @if($showSaveModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
            <div class="bg-[#0f0f0f] border border-zinc-700/50 rounded-[15px] max-w-sm w-full overflow-hidden shadow-2xl"
                @click.away="$wire.set('showSaveModal', false)">
                <div class="p-6 border-b border-zinc-800 flex items-start gap-4">
                    <div
                        class="w-10 h-10 rounded-full {{ $isEditing ? 'bg-amber-500/10 border-amber-500/30 text-amber-500' : 'bg-zinc-800 border-zinc-600 text-white' }} flex items-center justify-center border shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white uppercase tracking-widest mb-1">Confirmación</h3>
                        <p class="text-zinc-500 text-xs leading-relaxed">
                            {{ $isEditing ? 'Se sobrescribirán los datos de esta nave. ¿Deseas proceder con la actualización?' : 'Se añadirá una nueva nave. ¿Deseas proceder?' }}
                        </p>
                    </div>
                </div>
                <div class="flex bg-[#050505] p-3 gap-3">
                    <button type="button" wire:click="$set('showSaveModal', false)"
                        class="flex-1 py-2.5 px-4 text-xs font-bold text-zinc-400 hover:text-white bg-zinc-900 hover:bg-zinc-800 rounded-[10px] border border-zinc-800 transition-colors">
                        Cancelar
                    </button>
                    <button type="button" wire:click="executeSave"
                        class="flex-1 py-2.5 px-4 text-xs font-bold text-black {{ $isEditing ? 'bg-amber-500 hover:bg-amber-400' : 'bg-white hover:bg-zinc-200' }} rounded-[10px] transition-colors">
                        Ejecutar
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal: Confirmar Eliminar -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
            <div
                class="bg-[#0f0f0f] border border-red-900/30 rounded-[15px] max-w-sm w-full overflow-hidden shadow-[0_0_20px_rgba(220,38,38,0.05)]">
                <div class="p-6 border-b border-red-900/10 flex items-start gap-4">
                    <div
                        class="w-10 h-10 rounded-full bg-red-950/30 border border-red-900/50 text-red-500 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-red-500 uppercase tracking-widest mb-1">Confirmar Eliminación</h3>
                        <p class="text-zinc-500 text-xs leading-relaxed">
                            ¿Estás seguro de borrar esta nave definitivamente? El sistema no detecta vuelos
                            asignados.
                        </p>
                        <div class="mt-3 p-2 bg-blue-900/10 border border-blue-900/20 rounded-[8px]">
                            <p class="text-[9px] text-blue-400 uppercase tracking-tighter leading-tight">
                                <span class="font-bold underline">Sugerencia:</span> Antes considere cambiar el
                                Estado Operativo de la nave.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex bg-[#050505] p-3 gap-3">
                    <button type="button" wire:click="$set('showDeleteModal', false)"
                        class="flex-1 py-2 px-4 text-xs font-bold text-zinc-400 bg-zinc-900 border border-zinc-800 rounded-[10px]">Cancelar</button>
                    <button type="button" wire:click="executeDelete"
                        class="flex-1 py-2 px-4 text-xs font-bold text-white bg-red-900/80 hover:bg-red-600 rounded-[10px] border border-red-800/50 transition-all">Eliminar</button>
                </div>
            </div>
        </div>
    @endif

    @if($showCascadeDeleteModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/90 backdrop-blur-xl p-4">
            <div
                class="bg-[#0f0f0f] border-2 border-red-600 rounded-[15px] max-w-md w-full overflow-hidden shadow-[0_0_50px_rgba(220,38,38,0.2)] animate-pulse-subtle">
                <div class="p-6 border-b border-red-900/30 flex items-start gap-4">
                    <div
                        class="w-12 h-12 rounded-full bg-red-600 text-white flex items-center justify-center shrink-0 shadow-[0_0_20px_rgba(220,38,38,0.5)]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-red-500 uppercase tracking-tighter mb-1">ELIMINACIÓN EN CASCADA
                        </h3>
                        <p class="text-zinc-300 text-sm leading-relaxed">
                            Se han detectado <span class="text-white font-bold underline">{{ $flightsCount }} vuelos
                                programados</span> vinculados a esta nave.
                        </p>
                        <p class="text-red-400/80 text-xs mt-2 italic font-medium">
                            Si confirmas, la nave será eliminada y todos estos vuelos pasarán automáticamente a estado
                            "Cancelado". Se notificará a los Gestores para reubicar a los pasajeros.
                        </p>
                    </div>
                </div>
                <div class="flex bg-[#050505] p-4 gap-3">
                    <button type="button" wire:click="$set('showCascadeDeleteModal', false)"
                        class="flex-1 py-3 px-4 text-xs font-bold text-zinc-400 bg-zinc-950 border border-zinc-800 rounded-[10px] uppercase tracking-widest">Cancelar</button>
                    <button type="button" wire:click="executeDelete"
                        class="flex-1 py-3 px-4 text-xs font-bold text-white bg-red-600 hover:bg-red-500 rounded-[10px] uppercase tracking-widest shadow-lg shadow-red-900/40 transition-all">Confirmar</button>
                </div>
            </div>
        </div>
    @endif

    <!--Modal: Conlictos de estados-->
    @if($showConflictModal)
        <div class="fixed inset-0 z-[70] flex items-center justify-center bg-black/90 backdrop-blur-xl p-4">
            <div
                class="bg-[#0f0f0f] border-2 border-amber-500/50 rounded-[20px] max-w-lg w-full overflow-hidden shadow-[0_0_50px_rgba(245,158,11,0.2)]">
                <div class="p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-14 h-14 rounded-full bg-amber-500/10 border border-amber-500/50 text-amber-500 flex items-center justify-center shrink-0 animate-pulse">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white uppercase tracking-tighter">Colisión de Calendario
                                Detectada</h3>
                        </div>
                    </div>

                    <p class="text-zinc-300 text-sm leading-relaxed mb-6">
                        Se han identificado <span class="text-white font-bold underline">{{ $flightsCount }} vuelos</span>
                        programados durante el periodo de inactividad de la nave selecionada. ¿Cómo desea proceder con la
                        logística?
                    </p>

                    <div class="grid grid-cols-1 gap-3">
                        <button wire:click="handleRedirectToFlights"
                            class="group flex items-center gap-4 p-4 bg-zinc-900 border border-zinc-800 hover:border-blue-500 rounded-[15px] transition-all text-left">
                            <div
                                class="w-10 h-10 rounded-lg bg-blue-500/10 text-blue-500 flex items-center justify-center group-hover:bg-blue-500 group-hover:text-black transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white uppercase">Reasignar naves Ahora</p>
                                <p class="text-[10px] text-zinc-500">Redirigir al panel de vuelos para reasignar las naves.
                                </p>
                            </div>
                        </button>

                        <button wire:click="handleDelegateToGestor"
                            class="group flex items-center gap-4 p-4 bg-zinc-900 border border-zinc-800 hover:border-amber-500 rounded-[15px] transition-all text-left">
                            <div
                                class="w-10 h-10 rounded-lg bg-amber-500/10 text-amber-500 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-black transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white uppercase">Notificar a Gestores</p>
                                <p class="text-[10px] text-zinc-500">Notificar a los gestores para que gestionen la
                                    incidencia con los clientes.</p>
                            </div>
                        </button>
                    </div>

                    <button wire:click="$set('showConflictModal', false)"
                        class="w-full mt-6 py-3 text-[10px] font-bold text-zinc-500 hover:text-white uppercase tracking-widest transition-colors">
                        Cancelar Cambio de Estado
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>