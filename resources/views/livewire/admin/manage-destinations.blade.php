<div class="min-h-screen bg-gradient-to-b from-[#050505] to-[#19191c] text-zinc-300 p-4 md:p-8"
    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
    <div class="max-w-[1400px] mx-auto space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end border-b border-blue-700 pb-4">
            <div>
                <h2 class="text-3xl font-bold text-blue-700 tracking-tight uppercase flex items-center gap-3">
                    Destinos
                </h2>
                <p class="text-zinc-400 text-sm mt-1 uppercase tracking-widest ">
                    Gestión de destinos espaciales (Planetas)
                </p>
            </div>

            @if (session()->has('message'))
                <div
                    class="mt-4 md:mt-0 bg-emerald-900/40 border border-emerald-700/50 text-emerald-400 px-4 py-2 text-sm font-medium uppercase tracking-wider rounded-[10px] flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('message') }}
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!--Lista y Filtros -->
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

                <!--Resultado -->
                <div
                    class="border border-zinc-700/50 bg-[#0f0f0f]/80 backdrop-blur-md rounded-[10px] shadow-lg overflow-hidden">
                    <ul class="divide-y divide-zinc-800/80">
                        @forelse($destinations as $dest)
                            <li
                                class="p-4 hover:bg-zinc-800/50 transition-colors flex flex-col sm:flex-row justify-between sm:items-center gap-4 group">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-1">
                                        <span
                                            class="text-xs font-mono text-zinc-500 bg-black px-2 py-0.5 rounded-[5px] border border-zinc-800">ID:{{ str_pad($dest->id, 4, '0', STR_PAD_LEFT) }}</span>
                                        <h4
                                            class="text-lg font-bold text-blue-700 uppercase tracking-wide flex items-center gap-2">
                                            {{ $dest->name }}
                                        </h4>
                                    </div>
                                    <p class="text-zinc-400 text-sm line-clamp-2 mb-3 leading-relaxed">
                                        {{ $dest->description }}
                                    </p>
                                    <div
                                        class="inline-flex items-center gap-1.5 text-xs font-mono text-zinc-300 bg-zinc-900/80 px-2 py-1 border border-zinc-700/50 rounded-[5px]">
                                        <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                        DISTANCIA: {{ number_format($dest->distance_au, 2) }} AU
                                        @if($dest->max_distance_au)
                                            <span class="text-zinc-600">—</span>
                                            <span class="text-cyan-500/70">{{ number_format($dest->max_distance_au, 2) }}
                                                AU</span>
                                        @endif
                                    </div>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <span
                                            class="text-[10px] font-mono text-blue-400 bg-blue-950/20 px-2 py-0.5 rounded-[5px] border border-blue-900/30">
                                            DESPEGUE: {{ number_format($dest->launch_fee, 2) }} €
                                        </span>
                                        <span
                                            class="text-[10px] font-mono text-purple-400 bg-purple-950/20 px-2 py-0.5 rounded-[5px] border border-purple-900/30">
                                            ATERRIZAJE: {{ number_format($dest->landing_fee, 2) }} €
                                        </span>
                                    </div>
                                </div>

                                <div
                                    class="flex sm:flex-col gap-2 shrink-0 border-t border-zinc-800/80 sm:border-0 pt-3 sm:pt-0">
                                    <button type="button" wire:click="edit({{ $dest->id }})"
                                        class="flex-1 sm:flex-none px-4 py-1.5 bg-zinc-800 hover:bg-yellow-900/50 hover:text-yellow-400 text-zinc-300 text-xs font-bold uppercase tracking-wider transition-colors border border-zinc-700/50 hover:border-yellow-400 rounded-[10px] flex items-center justify-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                            </path>
                                        </svg>
                                        Editar
                                    </button>
                                    <button type="button" wire:click="confirmDelete({{ $dest->id }})"
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
                                <p class="font-medium uppercase tracking-widest text-sm">No se han encontrado destinos con
                                    ese nombre o id</p>
                            </div>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Formulario -->
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
                                Editar Destino
                            @else
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Nuevo Destino
                            @endif
                        </h3>

                        @if($isEditing)
                            <button type="button" wire:click="setCreateMode"
                                class="text-[10px] uppercase font-bold tracking-widest hover:bg-zinc-800/80 bg-white text-black hover:text-zinc-300 px-2.5 py-1.5 transition-colors border border-zinc-700/50 rounded-[5px] flex items-center gap-1.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Nuevo Destino
                            </button>
                        @endif
                    </div>

                    <form wire:submit.prevent="confirmSave" class="space-y-4">
                        @if($isEditing)
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-zinc-500 mb-1 uppercase tracking-widest flex items-center gap-1.5 pl-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                        </path>
                                    </svg>
                                    ID DE PLANETA
                                </label>
                                <input type="text" value="{{ str_pad($destinationId, 4, '0', STR_PAD_LEFT) }}" readonly
                                    class="w-full bg-[#050505] border border-zinc-800 px-3 py-2 text-zinc-600 font-mono text-sm cursor-not-allowed outline-none rounded-[10px]">
                            </div>
                        @endif

                        <div>
                            <label
                                class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-500/80' : 'text-zinc-400' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                Nombre del Planeta
                            </label>
                            <input type="text" wire:model="name"
                                class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-zinc-700/50 focus:border-zinc-400 text-white' }} px-3 py-2 placeholder-zinc-700 focus:outline-none transition-colors text-sm rounded-[10px]">
                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-500/80' : 'text-zinc-400' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                Descripción
                            </label>
                            <textarea wire:model="description" rows="4"
                                class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-zinc-700/50 focus:border-zinc-400 text-white' }} px-3 py-2 placeholder-zinc-700 focus:outline-none transition-colors text-sm rounded-[10px] resize-y"></textarea>
                            @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-500/80' : 'text-zinc-400' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                <span>Distancia Máxima</span>
                                <div x-data="{ open: false }" class="relative flex items-center">
                                    <span @mouseenter="open = true" @mouseleave="open = false"
                                        class="cursor-help border-b border-dotted border-zinc-500 pb-0.5 text-zinc-500 hover:text-white transition-colors">
                                        (AU)
                                    </span>

                                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-y-1"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-40 p-2 bg-zinc-900/95 border border-zinc-700 text-white text-[9px] leading-tight text-center rounded-md shadow-2xl backdrop-blur-sm z-50 pointer-events-none">
                                        <span class="font-bold text-cyan-400">UNIDADES ASTRONÓMICAS</span><br>
                                        1 AU ≈ 149.6 millones de km
                                        <diV
                                            class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-zinc-700">
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <input type="number" step="0.01" min="0.01" wire:model="max_distance_au"
                                class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-cyan-900/30 focus:border-cyan-400 text-cyan-100' }} px-3 py-2 placeholder-zinc-700 font-mono focus:outline-none transition-colors text-sm rounded-[10px]"
                                placeholder="0.00">
                            @error('max_distance_au') <span
                            class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        {{-- Tasas de Despegue y Aterrizaje --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-500/80' : 'text-zinc-400' }} mb-1 uppercase tracking-widest pl-2">
                                    Tasa Despegue (€)
                                </label>
                                <input type="number" step="0.01" min="0" wire:model="launch_fee"
                                    class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-zinc-700/50 focus:border-zinc-400 text-white' }} px-3 py-2 placeholder-zinc-700 font-mono focus:outline-none transition-colors text-sm rounded-[10px]"
                                    placeholder="0.00">
                                @error('launch_fee') <span
                                class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-500/80' : 'text-zinc-400' }} mb-1 uppercase tracking-widest pl-2">
                                    Tasa Aterrizaje (€)
                                </label>
                                <input type="number" step="0.01" min="0" wire:model="landing_fee"
                                    class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-zinc-700/50 focus:border-zinc-400 text-white' }} px-3 py-2 placeholder-zinc-700 font-mono focus:outline-none transition-colors text-sm rounded-[10px]"
                                    placeholder="0.00">
                                @error('landing_fee') <span
                                class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="pt-4 mt-2 border-t border-zinc-800">
                            <button type="submit"
                                class="w-full {{ $isEditing ? 'bg-amber-600 hover:bg-amber-500 text-black border-amber-500' : 'bg-white hover:bg-zinc-200 text-black border-white' }} font-bold uppercase tracking-widest py-3 px-4 transition-colors text-xs rounded-[10px] border flex items-center justify-center gap-2">
                                @if($isEditing)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                        </path>
                                    </svg>
                                    Actualizar Datos
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Registrar
                                @endif
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Confirmar Guardar -->
    @if($showSaveModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
            <div class="bg-[#0f0f0f] border border-zinc-700/50 rounded-[15px] max-w-sm w-full overflow-hidden shadow-2xl"
                @click.away="$wire.set('showSaveModal', false)">
                <div class="p-6 border-b border-zinc-800 flex items-start gap-4">
                    <div
                        class="w-10 h-10 rounded-full {{ $isEditing ? 'bg-amber-500/10 border-amber-500/30 text-amber-500' : 'bg-blue-700/10 border-blue-700/30 text-blue-700' }} flex items-center justify-center border shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white uppercase tracking-widest mb-1">
                            {{ $isEditing ? 'Confirmar Actualización' : 'Confirmar Nuevo Destino' }}
                        </h3>
                        <p class="text-zinc-500 text-xs leading-relaxed">
                            {{ $isEditing ? '¿Está seguro de actualizar los datos de este destino?' : '¿Está seguro de añadir un nuevo destino?' }}
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
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Confirmar Eliminar -->
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
                            ¿Estás seguro de borrar este destino <span class="text-zinc-300">definitivamente</span>? El
                            sistema no detecta datos asociados.
                        </p>
                    </div>
                </div>
                <div class="flex bg-[#050505] p-3 gap-3">
                    <button type="button" wire:click="$set('showDeleteModal', false)"
                        class="flex-1 py-2 px-4 text-xs font-bold text-zinc-400 hover:text-white bg-zinc-900 border border-zinc-800 rounded-[10px] transition-colors">
                        Cancelar
                    </button>
                    <button type="button" wire:click="executeDelete"
                        class="flex-1 py-2 px-4 text-xs font-bold text-white bg-red-900/80 hover:bg-red-600 rounded-[10px] transition-all border border-red-800/50">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Confirmar Eliminar con Vuelos -->
    @if($showCascadeDeleteModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/90 backdrop-blur-md p-4">
            <div class="bg-[#0f0f0f] border-2 border-red-600 rounded-[15px] max-w-md w-full overflow-hidden">
                <div class="p-6 border-b border-red-900/30 flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-red-600 text-white flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-red-500 uppercase tracking-tighter mb-1">
                            Eliminación en Cascada
                        </h3>
                        <p class="text-zinc-300 text-sm leading-relaxed">
                            Se han detectado <span class="text-white font-bold underline">{{ $flightsCount }} vuelos
                                activos</span> vinculados a este destino.
                        </p>
                        <p class="text-red-400/80 text-xs mt-2 italic font-medium">
                            Si confirmas se eliminarán todos los vuelos, reservas y datos logísticos asociados al destino.
                            Esta acción es irreversible.
                        </p>
                    </div>
                </div>
                <div class="flex bg-[#050505] p-4 gap-3">
                    <button type="button" wire:click="$set('showCascadeDeleteModal', false)"
                        class="flex-1 py-3 px-4 text-xs font-bold text-zinc-400 hover:text-white bg-zinc-950 border border-zinc-800 rounded-[10px] uppercase tracking-widest transition-colors">
                        Cancelar
                    </button>
                    <button type="button" wire:click="executeDelete"
                        class="flex-1 py-3 px-4 text-xs font-bold text-white bg-red-600 hover:bg-red-500 rounded-[10px] uppercase tracking-widest shadow-lg shadow-red-900/40 transition-all">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>