<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-full mx-auto px-4 sm:px-12 lg:px-16">
        <div class="flex justify-between h-20">
            <div class="flex items-center">

                <div class="-ms-2 me-4 flex items-center sm:hidden">
                    <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2.5 transition-all duration-200 focus:outline-none border-2 rounded-md"
                        :class="open ? 'text-purple-600 border-purple-200 bg-purple-50' : 'text-zinc-500 border-transparent hover:text-zinc-900 hover:bg-zinc-100'">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <x-application-logo class="block h-10 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <div class="hidden space-x-10 sm:-my-px sm:ms-[84px] sm:flex items-center">
                    <x-nav-link :href="auth()->user()->role === 'super_admin' ? route('admin.dashboard') : route('dashboard')" 
                        :active="request()->routeIs('admin.dashboard') || request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Inicio') }}
                    </x-nav-link>

                    @if(auth()->user()->role === 'super_admin')
                        <div class="hidden sm:flex items-center px-4">
                            <span class="w-px h-6 bg-gray-200"></span>
                        </div>

                        <div class="flex items-center gap-6">
                            <div class="relative" x-data="{ openEspacial: false }" @mouseenter="openEspacial = true"
                                @mouseleave="openEspacial = false" @click.away="openEspacial = false">
                                <button @click="openEspacial = !openEspacial"
                                    class="flex items-center gap-3 px-6 py-2.5 bg-blue-950 text-blue-400 text-[11px] uppercase tracking-[0.15em] font-bold border border-blue-900 hover:bg-blue-900 hover:text-white transition-all min-w-max">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                    <span>{{ __('Gestión Espacial') }}</span>
                                    <svg class="w-3 h-3 transition-transform" :class="openEspacial ? 'rotate-180' : ''"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="openEspacial" x-transition
                                    class="absolute top-full mt-0 left-0 w-64 bg-zinc-950 border border-blue-900 shadow-2xl z-50">
                                    <div class="flex flex-col">
                                        <a href="{{ route('admin.destinations') }}"
                                            class="px-5 py-4 text-xs tracking-wider text-blue-700 hover:text-blue-400 hover:bg-blue-950/30 border-b border-zinc-800 transition-colors">Destinos
                                            →</a>
                                        <a href="{{ route('admin.starships') }}"
                                            class="px-5 py-4 text-xs tracking-wider text-blue-300 hover:text-white hover:bg-blue-950/30 border-b border-zinc-800 transition-colors">Naves
                                            →</a>
                                        <a href="{{ route('admin.flights') }}"
                                            class="px-5 py-4 text-xs tracking-wider text-cyan-400 hover:text-white hover:bg-cyan-950/30 transition-colors">Vuelos
                                            →</a>
                                    </div>
                                </div>
                            </div>

                            <div class="relative" x-data="{ openTerrestre: false }" @mouseenter="openTerrestre = true"
                                @mouseleave="openTerrestre = false" @click.away="openTerrestre = false">
                                <button @click="openTerrestre = !openTerrestre"
                                    class="flex items-center gap-3 px-6 py-2.5 bg-rose-950 text-rose-400 text-[11px] uppercase tracking-[0.15em] font-bold border border-rose-900 hover:bg-rose-900 hover:text-white transition-all min-w-max">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    <span>{{ __('Gestión Terrestre') }}</span>
                                    <svg class="w-3 h-3 transition-transform" :class="openTerrestre ? 'rotate-180' : ''"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="openTerrestre" x-transition
                                    class="absolute top-full mt-0 left-0 w-64 bg-zinc-950 border border-rose-900 shadow-2xl z-50">
                                    <div class="flex flex-col">
                                        <a href="{{ route('admin.hotels') }}"
                                            class="px-5 py-4 text-xs tracking-wider text-rose-300 hover:text-white hover:bg-rose-950/30 border-b border-zinc-800 transition-colors">Hoteles
                                            →</a>
                                        <a href="{{ route('admin.terrestrial-flights') }}"
                                            class="px-5 py-4 text-xs tracking-wider text-pink-400 hover:text-white hover:bg-pink-950/30 transition-colors">Vuelos
                                            Terrestres →</a>
                                    </div>
                                </div>
                            </div>

                            <div class="min-w-max">
                                <a href="{{ route('admin.reservations') }}"
                                    class="flex items-center gap-3 px-6 py-2.5 bg-purple-950 text-purple-300 text-[11px] uppercase tracking-[0.15em] font-bold border border-purple-800 hover:bg-purple-800 hover:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                    </svg>
                                    <span>{{ __('Reservas') }}</span>
                                </a>
                            </div>

                            <div class="relative" x-data="{ openFin: false }" @mouseenter="openFin = true"
                                @mouseleave="openFin = false" @click.away="openFin = false">
                                <button @click="openFin = !openFin"
                                    class="flex items-center gap-3 px-6 py-2.5 bg-emerald-950 text-emerald-400 text-[11px] uppercase tracking-[0.15em] font-bold border border-emerald-900 hover:bg-emerald-900 hover:text-white transition-all min-w-max">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    <span>Finanzas</span>
                                    <svg class="w-3 h-3 transition-transform" :class="openFin ? 'rotate-180' : ''"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="openFin" x-transition
                                    class="absolute left-0 top-full mt-0 z-50 w-64 bg-zinc-950 border border-emerald-800 shadow-2xl overflow-hidden">
                                    <a href="{{ route('admin.finances') }}"
                                        class="block px-5 py-4 text-xs tracking-wider text-emerald-400 hover:text-white hover:bg-emerald-950/30 transition-colors border-b border-zinc-800">Finanzas
                                        →</a>
                                    <a href="{{ route('admin.tariffs') }}"
                                        class="block px-5 py-4 text-xs tracking-wider text-emerald-300 hover:text-white hover:bg-emerald-900/30 transition-colors">Tarifas
                                        →</a>
                                </div>
                            </div>

                            <div class="relative" x-data="{ openAdmin: false }" @mouseenter="openAdmin = true"
                                @mouseleave="openAdmin = false" @click.away="openAdmin = false">
                                <button @click="openAdmin = !openAdmin"
                                    class="flex items-center gap-3 px-6 py-2.5 bg-amber-950 text-amber-500 text-[11px] uppercase tracking-[0.15em] font-bold border border-amber-900 hover:bg-amber-800 hover:text-white transition-all min-w-max">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <span>{{ __('Usuarios') }}</span>
                                    <svg class="w-3 h-3 transition-transform" :class="openAdmin ? 'rotate-180' : ''"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="openAdmin" x-transition
                                    class="absolute top-full mt-0 left-0 w-56 bg-zinc-950 border border-amber-900 shadow-xl z-50">
                                    <div class="flex flex-col">
                                        <a href="{{ route('admin.users.role', 'cliente') }}"
                                            class="px-5 py-4 text-xs tracking-wider text-amber-600 hover:text-white hover:bg-amber-950/30 border-b border-zinc-800 transition-colors">Clientes</a>
                                        <a href="{{ route('admin.passengers') }}"
                                            class="px-5 py-4 text-xs tracking-wider text-orange-500 hover:text-white hover:bg-orange-950/30 border-b border-zinc-800 transition-colors">Pasajeros</a>
                                        <a href="{{ route('admin.users.role', 'gestor') }}"
                                            class="px-5 py-4 text-xs tracking-wider text-amber-400 hover:text-white hover:bg-amber-950/30 transition-colors">Gestores</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="relative" x-data="{ openUser: false }" @mouseenter="openUser = true"
                    @mouseleave="openUser = false" @click.away="openUser = false">
                    <button @click="openUser = !openUser"
                        class="flex items-center justify-center w-10 h-10 text-zinc-400 hover:text-black transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </button>
                    <div x-show="openUser" x-transition
                        class="absolute right-0 top-full w-56 bg-zinc-950 border border-zinc-800 shadow-2xl z-50">
                        <div class="px-5 py-3 border-b border-zinc-800 text-left">
                            <p class="text-[10px] uppercase text-zinc-500 font-bold tracking-widest">Modo Administrador
                            </p>
                            <p class="text-xs text-white truncate font-medium">{{ auth()->user()->name }}</p>
                        </div>
                        <a href="{{ route('profile') }}"
                            class="block px-5 py-4 text-xs text-zinc-400 hover:text-white hover:bg-zinc-900 border-b border-zinc-800">Mi
                            Perfil</a>
                        <button wire:click="logout"
                            class="w-full text-left px-5 py-4 text-xs text-rose-400 hover:bg-zinc-900 transition-colors">Cerrar
                            Sesión</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}"
        class="hidden sm:hidden bg-white border-t border-gray-100 shadow-inner">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="auth()->user()->role === 'super_admin' ? route('admin.dashboard') : route('dashboard')" 
                :active="request()->routeIs('admin.dashboard') || request()->routeIs('dashboard')" wire:navigate>
                {{ __('Inicio') }}
            </x-responsive-nav-link>

            @if(auth()->user()->role === 'super_admin')
                <div class="border-t border-gray-100 my-2 mx-4"></div>

                <div x-data="{ openEsp: false }">
                    <button @click="openEsp = !openEsp"
                        class="flex items-center justify-between w-full px-4 py-3 text-xs uppercase tracking-widest font-bold transition-all border-l-4"
                        :class="openEsp ? 'text-blue-600 bg-blue-50 border-blue-500' : 'text-gray-500 border-transparent'">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            <span>Gestión Espacial</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="openEsp ? 'rotate-180' : ''" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openEsp" class="bg-blue-50/30 pb-2">
                        <a href="{{ route('admin.destinations') }}"
                            class="block ps-12 py-2 text-xs text-blue-800 font-medium">Destinos</a>
                        <a href="{{ route('admin.starships') }}"
                            class="block ps-12 py-2 text-xs text-blue-600 font-medium">Naves</a>
                        <a href="{{ route('admin.flights') }}"
                            class="block ps-12 py-2 text-xs text-cyan-600 font-medium">Vuelos</a>
                    </div>
                </div>

                <div x-data="{ openTerr: false }" class="mt-1">
                    <button @click="openTerr = !openTerr"
                        class="flex items-center justify-between w-full px-4 py-3 text-xs uppercase tracking-widest font-bold border-l-4"
                        :class="openTerr ? 'text-rose-600 bg-rose-50 border-rose-500' : 'text-gray-500 border-transparent'">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>Gestión Terrestre</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="openTerr ? 'rotate-180' : ''" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openTerr" class="bg-rose-50/30 pb-2">
                        <a href="{{ route('admin.hotels') }}"
                            class="block ps-12 py-2 text-xs text-rose-700 font-medium">Hoteles</a>
                        <a href="{{ route('admin.terrestrial-flights') }}"
                            class="block ps-12 py-2 text-xs text-pink-600 font-medium">Vuelos Terrestres</a>
                    </div>
                </div>

                <a href="{{ route('admin.reservations') }}"
                    class="block px-4 py-3 text-xs uppercase tracking-widest font-bold text-purple-600 hover:bg-purple-50 transition-colors border-l-4 border-transparent">Reservas</a>
                <a href="{{ route('admin.finances') }}"
                    class="block px-4 py-3 text-xs uppercase tracking-widest font-bold text-emerald-600 hover:bg-emerald-50 transition-colors border-l-4 border-transparent">Finanzas</a>
                <a href="{{ route('admin.users.role', 'cliente') }}"
                    class="block px-4 py-3 text-xs uppercase tracking-widest font-bold text-amber-600 hover:bg-amber-50 transition-colors border-l-4 border-transparent">Usuarios</a>
                <a href="{{ route('admin.passengers') }}"
                    class="block px-4 py-3 text-xs uppercase tracking-widest font-bold text-cyan-600 hover:bg-cyan-50 transition-colors border-l-4 border-transparent">Pasajeros</a>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-100 bg-gray-50/50">
            <div class="px-4">
                <div class="font-bold text-sm text-zinc-800 uppercase tracking-widest">{{ auth()->user()->name }}</div>
                <div class="text-xs text-zinc-400">{{ auth()->user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')">Mi Perfil</x-responsive-nav-link>
                <button wire:click="logout"
                    class="w-full text-left px-4 py-2 text-rose-600 text-xs font-bold uppercase hover:bg-rose-50 transition-all">Cerrar
                    Sesión</button>
            </div>
        </div>
    </div>
</nav>