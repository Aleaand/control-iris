<div class="min-h-screen bg-gradient-to-b from-[#050505] to-[#19191c] text-zinc-300 p-4 md:p-8"
    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
    <div class="max-w-[1400px] mx-auto space-y-6">

        <!-- Header & Flash Message -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end border-b border-pink-400 pb-4">
            <div>
                <h2 class="text-3xl font-bold text-pink-400 tracking-tight uppercase flex items-center gap-3">
                    Vuelos Terrestres
                </h2>
                <p class="text-zinc-400 text-sm mt-1 uppercase tracking-widest">
                    Gestión de Vuelos Terrestres.
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
                        <input type="text" wire:model.live="search" placeholder="Buscar por Aerolínea, Origen o Destino"
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
                            Fecha: {{ $sortDir === 'asc' ? 'Recientes' : 'Antiguos' }}
                        </button>
                    </div>
                </div>

                <!-- Lista de Resultados -->
                <div
                    class="border border-zinc-700/50 bg-[#0f0f0f]/80 backdrop-blur-md rounded-[10px] shadow-lg overflow-hidden">
                    <ul class="divide-y divide-zinc-800/80">
                        @forelse($flights as $flight)
                            <li
                                class="p-4 hover:bg-zinc-800/50 transition-colors flex flex-col sm:flex-row justify-between sm:items-center gap-4 group">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span
                                            class="text-xs font-mono text-zinc-500 bg-black px-2 py-0.5 rounded-[5px] border border-zinc-800">#{{ $flight->flight_number }}</span>
                                        <h4
                                            class="text-lg font-bold text-white uppercase tracking-wide flex items-center gap-2">
                                            {{ $flight->airline }}
                                        </h4>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <div
                                            class="inline-flex items-center gap-1.5 text-xs font-mono text-pink-300 bg-pink-950/30 px-2 py-1 border border-pink-900/50 rounded-[5px]">
                                            {{ optional($flight->originLocation)->code ?? '???' }} →
                                            {{ optional($flight->destinationLocation)->code ?? '???' }}
                                        </div>
                                        <div
                                            class="inline-flex items-center gap-1.5 text-xs font-mono text-zinc-300 bg-zinc-900/80 px-2 py-1 border border-zinc-700/50 rounded-[5px]">
                                            <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            {{ $flight->departure_datetime->format('d/M H:i') }} -
                                            {{ $flight->arrival_datetime ? $flight->arrival_datetime->format('H:i') : '' }}
                                        </div>
                                        <div
                                            class="inline-flex items-center gap-1.5 text-xs font-mono text-zinc-300 bg-zinc-900/80 px-2 py-1 border border-zinc-700/50 rounded-[5px]">
                                            TARIFA: ${{ number_format($flight->price, 2) }}
                                        </div>

                                        <!-- Disponibilidades -->
                                        <div
                                            class="inline-flex items-center gap-1.5 text-xs font-mono {{ $flight->status === 'Cancelado' ? 'text-red-400 bg-red-950/30 border-red-900/50' : 'text-zinc-300 bg-zinc-900/80 border-zinc-700/50' }} px-2 py-1 border rounded-[5px]">
                                            ESTADO: {{ strtoupper($flight->status ?? 'PROGRAMADO') }}
                                        </div>
                                        <div
                                            class="inline-flex items-center gap-1.5 text-xs font-mono text-zinc-300 bg-zinc-900/80 px-2 py-1 border border-zinc-700/50 rounded-[5px]">
                                            EQUIPAJE: ${{ number_format($flight->baggage_price, 2) }}
                                        </div>
                                        <div
                                            class="inline-flex items-center gap-1.5 text-xs font-mono text-zinc-300/80 bg-zinc-900/80 px-2 py-1 border border-zinc-700/50 rounded-[5px]">
                                            CAPACIDAD: {{ $flight->executive_capacity }}
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex sm:flex-col gap-2 shrink-0 border-t border-zinc-800/80 sm:border-0 pt-3 sm:pt-0">
                                    <button type="button" wire:click="edit({{ $flight->id }})"
                                        class="flex-1 sm:flex-none px-4 py-1.5 bg-zinc-800 hover:bg-amber-900/50 hover:text-amber-400 text-zinc-300 text-xs font-bold uppercase tracking-wider transition-colors border border-zinc-700/50 hover:border-amber-400 rounded-[10px] flex items-center justify-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                            </path>
                                        </svg>
                                        Editar
                                    </button>
                                    <button type="button" wire:click="confirmDelete({{ $flight->id }})"
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
                    class="border border-zinc-700/50 bg-[#0f0f0f]/80 backdrop-blur-md p-6 rounded-[10px] shadow-lg transition-colors duration-500 {{ $isEditing ? 'border-2 border-amber-500/80 shadow-[0_0_20px_rgba(16,185,129,0.05)]' : 'border-2 border-zinc-500' }}">
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
                                Nuevo vuelo Terrestre
                            @endif
                        </h3>

                        @if($isEditing)
                            <button type="button" wire:click="setCreateMode"
                                class="text-[10px] uppercase font-bold tracking-widest bg-zinc-800/80 hover:bg-white hover:text-black text-zinc-300 px-2.5 py-1.5 transition-colors border border-zinc-700/50 rounded-[5px] flex items-center gap-1.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Nuevo Vuelo
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
                                    ID
                                </label>
                                <input type="text" value="{{ str_pad($flightId, 4, '0', STR_PAD_LEFT) }}" readonly
                                    class="w-full bg-[#050505] border border-zinc-800 px-3 py-2 text-zinc-600 font-mono text-sm cursor-not-allowed outline-none rounded-[10px]">
                            </div>
                        @endif

                        <div>
                            <label
                                class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-400' : 'text-zinc-400' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                Aerolínea
                            </label>
                            <input type="text" wire:model="airline"
                                class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-zinc-700/50 focus:border-zinc-400 text-white' }} px-3 py-2 placeholder-zinc-700 focus:outline-none transition-colors text-sm rounded-[10px]">
                            @error('airline') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-400' : 'text-zinc-400' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                    Base Origen
                                </label>
                                <select wire:model="origin_id"
                                    class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-zinc-700/50 focus:border-zinc-400 text-white' }} px-3 py-2 focus:outline-none transition-colors text-sm rounded-[10px]">
                                    <option value="">-- SELECCIONAR --</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->code }})</option>
                                    @endforeach
                                </select>
                                @error('origin_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-400' : 'text-zinc-400' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                    Base Destino
                                </label>
                                <select wire:model="destination_id"
                                    class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-zinc-700/50 focus:border-zinc-400 text-white' }} px-3 py-2 focus:outline-none transition-colors text-sm rounded-[10px]">
                                    <option value="">-- SELECCIONAR --</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->code }})</option>
                                    @endforeach
                                </select>
                                @error('destination_id') <span
                                class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-400' : 'text-zinc-400' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                    Salida
                                </label>
                                <input type="datetime-local" wire:model="departure_datetime"
                                    class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-zinc-700/50 focus:border-zinc-400 text-white' }} px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-[10px]">
                                @error('departure_datetime') <span
                                class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-400' : 'text-zinc-400' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                    Llegada
                                </label>
                                <input type="datetime-local" wire:model="arrival_datetime"
                                    class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-zinc-700/50 focus:border-zinc-400 text-white' }} px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-[10px]">
                                @error('arrival_datetime') <span
                                class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-400' : 'text-zinc-400' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                    Precio P.P($)
                                </label>
                                <input type="number" step="0.01" min="0.01" wire:model="price"
                                    class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-zinc-700/50 focus:border-zinc-400 text-white' }} px-3 py-2 placeholder-zinc-700 font-mono focus:outline-none transition-colors text-sm rounded-[10px]"
                                    placeholder="1200.00">
                                @error('price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-400' : 'text-zinc-400' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                    Precio por Equipaje ($)
                                </label>
                                <input type="number" step="0.01" min="0.01" wire:model="baggage_price"
                                    class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-zinc-700/50 focus:border-zinc-400 text-white' }} px-3 py-2 placeholder-zinc-700 font-mono focus:outline-none transition-colors text-sm rounded-[10px]"
                                    placeholder="50.00">
                                @error('baggage_price') <span
                                class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-amber-500' : 'text-zinc-400' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                    Capacidad Ejecutiva
                                </label>
                                <input type="number" min="1" wire:model="executive_capacity"
                                    class="w-full bg-[#050505] border {{ $isEditing ? 'border-amber-900/40 focus:border-amber-500 text-amber-50' : 'border-zinc-700/50 focus:border-zinc-400 text-white' }} px-3 py-2 placeholder-zinc-700 font-mono focus:outline-none transition-colors text-sm rounded-[10px]"
                                    placeholder="20">
                                @error('executive_capacity') <span
                                class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="pt-4 mt-2 border-t border-zinc-800">
                            <button type="submit"
                                class="w-full {{ $isEditing ? 'bg-amber-600 hover:bg-amber-500 text-white border-amber-500' : 'bg-white hover:bg-zinc-200 text-black border-white' }} font-bold uppercase tracking-widest py-3 px-4 transition-colors text-xs rounded-[10px] border flex items-center justify-center gap-2">
                                @if($isEditing)
                                    Actualizar Vuelo
                                @else
                                    Programar Vuelo
                                @endif
                            </button>
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
                            @if($isEditing)
                                Se actualizará este vuelo terrestre.
                            @else
                                Se añadirá un nuevo vuelo terrestre.
                            @endif

                            @if($flightDurationHours > 0 && $airline)
                                <br><br>
                                <span class="text-amber-400 font-bold block mt-2 text-sm border-l-2 border-amber-500 pl-2">
                                    ¿Está seguro de añadir el vuelo de {{ strtoupper($origin_id) }} a
                                    {{ strtoupper($destination_id) }}, que durará aprox.
                                    {{ $flightDurationHours }} hora(s)?
                                </span>
                            @endif
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

    <!-- Modal: Confirmar Eliminar -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
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
                        <h3 class="text-sm font-bold text-red-500 uppercase tracking-widest mb-1">Confirmar Eliminación</h3>
                        <p class="text-zinc-500 text-xs leading-relaxed">
                            ¿Seguro que quieres eliminar este vuelo terrestre? La acción es irreversible.
                        </p>
                    </div>
                </div>
                <div class="flex bg-[#050505] p-3 gap-3">
                    <button type="button" wire:click="$set('showDeleteModal', false)"
                        class="flex-1 py-2.5 px-4 text-xs font-bold text-zinc-400 hover:text-white bg-zinc-900 hover:bg-zinc-800 rounded-[10px] border border-zinc-800 transition-colors">
                        Cancelar
                    </button>
                    <button type="button" wire:click="executeDelete"
                        class="flex-1 py-2.5 px-4 text-xs font-bold text-white bg-red-900 hover:bg-red-800 rounded-[10px] transition-colors border border-red-900/50">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal: Eliminar en Cascada -->
    @if($showConflictDeleteModal)
        <div class="fixed inset-0 z-[70] flex items-center justify-center bg-black/90 backdrop-blur-xl p-4">
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
                        <h3 class="text-lg font-black text-red-500 uppercase tracking-tighter mb-1">Conflicto de Traslado
                            detectado</h3>
                        <p class="text-zinc-300 text-sm leading-relaxed">
                            Se han detectado <span class="text-white font-bold underline">{{ $reservationsCount }} pasajeros
                                ejecutivos</span> en este trayecto terrestre.
                        </p>
                        <p class="text-red-400/80 text-xs mt-2 italic font-medium leading-tight">
                            Al eliminar, se cancelarán los traslados y se notificará a los Gestores.
                        </p>
                    </div>
                </div>
                <div class="flex bg-[#050505] p-4 gap-3 flex-col sm:flex-row">
                    <button type="button" wire:click="$set('showConflictDeleteModal', false)"
                        class="flex-1 py-3 px-4 text-xs font-bold text-zinc-400 bg-zinc-950 border border-zinc-800 rounded-[10px] uppercase tracking-widest">
                        Cancelar
                    </button>

                    <button type="button" wire:click="redirectToEdit"
                        class="flex-1 py-3 px-4 text-xs font-bold text-amber-500 hover:text-amber-400 bg-amber-950/20 border border-amber-900/50 rounded-[10px] uppercase tracking-widest shadow-lg transition-all">
                        Editar
                    </button>

                    <button type="button" wire:click="cancelTerrestrialFlightAndNotify"
                        class="flex-1 py-3 px-4 text-xs font-bold text-white bg-red-600 hover:bg-red-500 rounded-[10px] uppercase tracking-widest shadow-lg shadow-red-900/40 transition-all">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>