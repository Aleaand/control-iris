<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<div x-data="{ 
    openMobile: false, 
    openEspacial: false, 
    openTerrestre: false, 
    openFinanzas: false, 
    openUser: false, 
    openUsuarios: false,
    isCollapsed: localStorage.getItem('sidebar-collapsed') === null ? true : localStorage.getItem('sidebar-collapsed') === 'true',
    localTime: '',
    serverTime: '',
    diff: 0,
    updateClocks() {
        const now = new Date();
        this.localTime = now.toLocaleDateString('sv-SE') + ' ' + now.toLocaleTimeString('sv-SE');
        
        const sTime = new Date(now.getTime() + this.diff);
        const year = sTime.getUTCFullYear();
        const month = String(sTime.getUTCMonth() + 1).padStart(2, '0');
        const day = String(sTime.getUTCDate()).padStart(2, '0');
        const hours = String(sTime.getUTCHours()).padStart(2, '0');
        const minutes = String(sTime.getUTCMinutes()).padStart(2, '0');
        const seconds = String(sTime.getUTCSeconds()).padStart(2, '0');
        
        this.serverTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    }
}" x-init="
    const serverInitial = new Date('{{ now()->toIso8601String() }}');
    const localInitial = new Date();
    diff = serverInitial.getTime() - localInitial.getTime();
    
    updateClocks();
    setInterval(() => updateClocks(), 1000);
    $watch('isCollapsed', value => { 
        localStorage.setItem('sidebar-collapsed', value);
        $dispatch('sidebar-toggle', value);
        openUser = false;
    })
">

    <!-- Mobile Header -->
    <header
        class="mobile-header md:hidden flex justify-between items-center px-4 py-3 bg-[#09090b] border-b border-white/5">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" wire:navigate>
                <img src="{{ asset('assets/logo_iris.png') }}" alt="Iris" class="h-8 transition-transform duration-300"
                    :class="openMobile ? 'scale-105' : ''">
            </a>
            <div x-show="openMobile" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0"
                class="flex flex-col justify-center">
                <span class="font-bold text-sm tracking-[0.2em] text-white leading-none">IRIS</span>
                <span class="font-mono-tech text-[8px] text-cyan-500 mt-0.5">AEROSPACE</spanç>
            </div>
        </div>
        <button @click="openMobile = !openMobile" class="text-white p-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path x-show="!openMobile" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16m-7 6h7"></path>
                <path x-show="openMobile" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </header>

    <aside class="sidebar" :class="{ 'open': openMobile, 'collapsed': isCollapsed }">

        <button @click.stop="isCollapsed = !isCollapsed;openUser = false" class="collapse-toggle hidden md:flex">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </button>

        <!-- Header-->
        <div class="sidebar-header hidden md:block">
            <a href="{{ route('admin.dashboard') }}" wire:navigate class="logo-container group">
                <img src="{{ asset('assets/logo_iris.png') }}" alt="Iris"
                    class="logo-img group-hover:scale-105 transition-transform duration-500">
                <div class="logo-text" style="line-height: 1.2;" x-show="!isCollapsed"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-x-4"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-700" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0">
                    <span class="font-bold text-sm tracking-[0.2em] text-white">IRIS</span><br>
                    <span class="font-mono-tech text-[8px] text-cyan-500">AEROSPACE</span>
                </div>
            </a>
        </div>

        <!-- Navigation Menu -->
        <nav class="nav-menu custom-scrollbar overflow-y-auto mt-12 md:mt-0">

            <!-- Logística Espacial -->
            <div class="nav-dropdown" @mouseenter="if(!isCollapsed) openEspacial = true"
                @mouseleave="openEspacial = false">
                <div class="nav-item nav-dropdown-trigger" :class="openEspacial ? 'active text-cyan-500' : ''"
                    @click="if(isCollapsed) isCollapsed = false; openEspacial = !openEspacial">
                    <div class="flex items-center">
                        <svg class="nav-icon" :class="openEspacial ? 'text-cyan-500' : ''" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path
                                d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z">
                            </path>
                        </svg>
                        <span class="nav-text" x-show="!isCollapsed"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 -translate-x-2"
                            x-transition:enter-end="opacity-100 translate-x-0"
                            x-transition:leave="transition ease-in duration-700" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0">Logística Espacial</span>
                    </div>
                    <svg class="w-3 h-3 transition-transform nav-dropdown-arrow"
                        :class="openEspacial ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        x-show="!isCollapsed">
                        <path d="M19 9l-7 7-7-7" stroke-width="2"></path>
                    </svg>
                </div>
                <div x-show="openEspacial && !isCollapsed" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0" class="nav-submenu">
                    <a href="{{ route('admin.destinations') }}" wire:navigate
                        class="nav-submenu-item hover-blue-2">Destinos</a>
                    <a href="{{ route('admin.starships') }}" wire:navigate
                        class="nav-submenu-item hover-blue-3">Naves</a>
                    <a href="{{ route('admin.flights') }}" wire:navigate class="nav-submenu-item hover-blue-4">Vuelos
                        Espaciales</a>
                </div>
            </div>

            <!-- Logística Terrestre -->
            <div class="nav-dropdown" @mouseenter="if(!isCollapsed) openTerrestre = true"
                @mouseleave="openTerrestre = false">
                <div class="nav-item nav-dropdown-trigger" :class="openTerrestre ? 'active text-rose-500' : ''"
                    @click="if(isCollapsed) isCollapsed = false; openTerrestre = !openTerrestre">
                    <div class="flex items-center">
                        <svg class="nav-icon" :class="openTerrestre ? 'text-rose-500' : ''" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        </svg>
                        <span class="nav-text" x-show="!isCollapsed"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 -translate-x-2"
                            x-transition:enter-end="opacity-100 translate-x-0"
                            x-transition:leave="transition ease-in duration-700" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0">Logística Terrestre</span>
                    </div>
                    <svg class="w-3 h-3 transition-transform nav-dropdown-arrow"
                        :class="openTerrestre ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        x-show="!isCollapsed">
                        <path d="M19 9l-7 7-7-7" stroke-width="2"></path>
                    </svg>
                </div>
                <div x-show="openTerrestre && !isCollapsed" x-transition.opacity class="nav-submenu">
                    <a href="{{ route('admin.hotels') }}" wire:navigate
                        class="nav-submenu-item hover-rose-2">Hoteles</a>
                    <a href="{{ route('admin.terrestrial-flights') }}" wire:navigate
                        class="nav-submenu-item hover-rose-4">Vuelos Terrestres</a>
                </div>
            </div>

            <!-- Finanzas -->
            <div class="nav-dropdown" @mouseenter="if(!isCollapsed) openFinanzas = true"
                @mouseleave="openFinanzas = false">
                <div class="nav-item nav-dropdown-trigger" :class="openFinanzas ? 'active text-violet-500' : ''"
                    @click="if(isCollapsed) isCollapsed = false; openFinanzas = !openFinanzas">
                    <div class="flex items-center">
                        <svg class="nav-icon" :class="openFinanzas ? 'text-violet-500' : ''" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                        <span class="nav-text" x-show="!isCollapsed"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 -translate-x-2"
                            x-transition:enter-end="opacity-100 translate-x-0"
                            x-transition:leave="transition ease-in duration-700" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0">Finanzas</span>
                    </div>
                    <svg class="w-3 h-3 transition-transform nav-dropdown-arrow"
                        :class="openFinanzas ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        x-show="!isCollapsed">
                        <path d="M19 9l-7 7-7-7" stroke-width="2"></path>
                    </svg>
                </div>
                <div x-show="openFinanzas && !isCollapsed" x-transition.opacity class="nav-submenu">
                    <a href="{{ route('admin.finances') }}" wire:navigate
                        class="nav-submenu-item hover-violet-2">Gestión
                        Financiera</a>
                    <a href="{{ route('admin.tariffs') }}" wire:navigate
                        class="nav-submenu-item hover-violet-4">Tarifario</a>
                </div>
            </div>

            <!-- Usuarios -->
            <div class="nav-dropdown" @mouseenter="if(!isCollapsed) openUsuarios = true"
                @mouseleave="openUsuarios = false">
                <div class="nav-item nav-dropdown-trigger" :class="openUsuarios ? 'active text-orange-500' : ''"
                    @click="if(isCollapsed) isCollapsed = false; openUsuarios = !openUsuarios">
                    <div class="flex items-center">
                        <svg class="nav-icon" :class="openUsuarios ? 'text-orange-500' : ''" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                        <span class="nav-text" x-show="!isCollapsed"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 -translate-x-2"
                            x-transition:enter-end="opacity-100 translate-x-0"
                            x-transition:leave="transition ease-in duration-700" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0">Gobernanza Usuarios</span>
                    </div>
                    <svg class="w-3 h-3 transition-transform nav-dropdown-arrow"
                        :class="openUsuarios ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        x-show="!isCollapsed">
                        <path d="M19 9l-7 7-7-7" stroke-width="2"></path>
                    </svg>
                </div>
                <div x-show="openUsuarios && !isCollapsed" x-transition.opacity class="nav-submenu">
                    <a href="{{ route('admin.users.role', 'gestor') }}" wire:navigate
                        class="nav-submenu-item hover-orange-2">Gestores</a>
                    <a href="{{ route('admin.users.role', 'cliente') }}" wire:navigate
                        class="nav-submenu-item hover-orange-4">Clientes</a>
                    <a href="{{ route('admin.passengers') }}" wire:navigate
                        class="nav-submenu-item hover-orange-6">Pasajeros</a>
                </div>
            </div>

            <!-- Central de Reservas -->
            <a href="{{ route('admin.reservations') }}" wire:navigate class="nav-item hover-emerald"
                :class="request()->routeIs('admin.reservations') ? 'active text-emerald-500' : ''">
                <svg class="nav-icon" :class="request()->routeIs('admin.reservations') ? 'text-emerald-500' : ''"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path
                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                    </path>
                </svg>
                <span class="nav-text" x-show="!isCollapsed" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-x-2"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-700" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0">Central de Reservas</span>
            </a>

        </nav>

        <!-- User Bubble Section -->
        <div class="user-bubble-container" x-cloak>
            <div class="flex items-center gap-4">
                <div class="user-bubble" @click.stop="openUser = !openUser">
                    @if(auth()->user()->profile_photo_url)
                        <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}"
                            class="user-img">
                    @else
                        <span class="text-white font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    @endif
                </div>
                <div class="min-w-0 user-info" x-show="!isCollapsed"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-700"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="font-mono-tech text-[8px] text-zinc-500 uppercase">Rol Admin</p>
                </div>
            </div>

            <!-- User Dropdown Panel -->
            <div x-show="openUser" x-cloak @click.away="openUser = false"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-0"
                class="user-dropdown-panel z-50"
                :style="openUser ? (isCollapsed ? 'left: 85px; bottom: 24px;' : 'left: 85px; bottom: 24px;') : 'display: none;'">
                <a href="{{ route('profile') }}" wire:navigate
                    class="block px-4 py-3 text-xs text-zinc-400 hover:text-white hover:bg-white/5 rounded-lg mb-1">
                    Mi Perfil
                </a>
                <div class="h-px bg-white/5 my-1"></div>
                <button wire:click="logout"
                    class="w-full text-left px-4 py-3 text-xs text-rose-400 hover:bg-rose-500/10 rounded-lg transition-colors">
                    Cerrar Sesión
                </button>
            </div>

            <div class="mt-6 pt-4 border-t border-white/5 flex justify-between items-center font-mono-tech text-[8px] text-zinc-600 sidebar-footer-details"
                x-show="!isCollapsed">
                <span>BD:<span x-text="serverTime"></span></span>
                <span class="w-1 h-1 bg-zinc-800 rounded-full"></span>
                <span>Local: <span x-text="localTime"></span></span>
            </div>
        </div>
    </aside>

    <div x-show="openMobile" @click="openMobile = false"
        class="md:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[90]"></div>
</div>