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
    showLogoutModal: false,
    isCollapsed: localStorage.getItem('sidebar-collapsed') === null ? true : localStorage.getItem('sidebar-collapsed') === 'true',
    isDarkMode: localStorage.getItem('theme') === 'dark',
    localTime: '',
    serverTime: '',
    diff: 0,
    toggleTheme() {
        this.isDarkMode = !this.isDarkMode;
        localStorage.setItem('theme', this.isDarkMode ? 'dark' : 'light');
        if (this.isDarkMode) {
            document.documentElement.classList.remove('light-mode');
        } else {
            document.documentElement.classList.add('light-mode');
        }
    },
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
    <header class="mobile-header md:hidden flex justify-between items-center px-4 py-3 border-b border-white/5"
        style="background: var(--bg-obsidian)">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" wire:navigate>
                <img :src="isDarkMode ? '{{ asset('assets/logo_iris.png') }}' : '{{ asset('assets/logo_iris_black.png') }}'"
                    alt="Iris" class="h-8 transition-transform duration-300" :class="openMobile ? 'scale-105' : ''">
            </a>
            <div x-show="openMobile" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0"
                class="flex flex-col justify-center">
                <span class="font-bold text-sm tracking-[0.2em] leading-none"
                    style="color: var(--text-primary)">IRIS</span>
                <span class="font-mono-tech text-[8px] text-cyan-500 mt-0.5">AEROSPACE</spanç>
            </div>
        </div>
        <button @click="openMobile = !openMobile" class="p-2" style="color: var(--text-primary)">
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
                <img :src="isDarkMode ? '{{ asset('assets/logo_iris.png') }}' : '{{ asset('assets/logo_iris_black.png') }}'"
                    alt="Iris" class="logo-img group-hover:scale-105 transition-transform duration-500">
                <div class="logo-text" style="line-height: 1.2;" x-show="!isCollapsed"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-x-4"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-700" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0">
                    <span class="font-bold text-sm tracking-[0.2em]" style="color: var(--text-primary)">IRIS</span><br>
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
                            x-transition:leave-end="opacity-0">Logistica Usuarios</span>
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
                        <span class="font-bold"
                            style="color: var(--text-primary)">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    @endif
                </div>
                <div class="min-w-0 user-info" x-show="!isCollapsed"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-700"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <p class="text-xs font-bold truncate" style="color: var(--text-primary)">{{ auth()->user()->name }}
                    </p>
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
                    class="block px-4 py-3 text-xs text-zinc-400 hover:text-[var(--text-primary)] hover:bg-white/5 rounded-lg mb-1 transition-colors">
                    Mi Perfil
                </a>
                <div @click="toggleTheme"
                    class="w-full flex items-center justify-between px-4 py-3 text-xs text-zinc-400 hover:text-[var(--text-primary)] hover:bg-white/5 rounded-lg mb-1 transition-all cursor-pointer group/theme">
                    <div class="flex items-center gap-3">
                        <span x-text="isDarkMode ? 'Modo Día' : 'Modo Noche'"
                            class="transition-colors group-hover/theme:text-[var(--text-primary)]"></span>
                    </div>
                    <div class="theme-switch" :class="{ 'active': !isDarkMode }">
                        <div class="theme-switch-dot">
                            <!-- Moon Icon (Dark Mode) -->
                            <svg x-show="isDarkMode" class="theme-switch-icon text-zinc-900" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                            </svg>
                            <!-- Sun Icon (Light Mode) -->
                            <svg x-show="!isDarkMode" class="theme-switch-icon text-white" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="h-px bg-white/5 my-1"></div>
                <button @click="showLogoutModal = true; openUser = false"
                    class="w-full text-left px-4 py-3 text-xs text-rose-400 hover:bg-rose-500/10 rounded-lg transition-colors">
                    Cerrar Sesión
                </button>
            </div>

            <div class="mt-6 pt-4 border-t border-white/5 flex justify-between items-center font-mono-tech text-[8px] text-zinc-600 sidebar-footer-details"
                x-show="!isCollapsed">
                <span>BD:<span x-text="serverTime"></span></span>
                <span>Local: <span x-text="localTime"></span></span>
            </div>
        </div>
    </aside>

    <div x-show="openMobile" @click="openMobile = false"
        class="md:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[90]"></div>

    <!-- Logout Modal Técnico -->
    <template x-teleport="body">
        <div x-show="showLogoutModal" x-cloak class="fixed inset-0 z-[300] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/80 backdrop-blur-md" @click="showLogoutModal = false"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"></div>

            <div class="tech-card p-8 w-full max-w-sm relative overflow-hidden" x-show="showLogoutModal"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100"
                style="background: #0a0a0f; border-color: rgba(244, 63, 94, 0.2);">

                <div
                    class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-rose-500 to-transparent animate-pulse">
                </div>

                <div class="flex flex-col items-center text-center">
                    <div
                        class="w-16 h-16 rounded-2xl bg-rose-500/10 flex items-center justify-center text-rose-500 mb-6 border border-rose-500/20 shadow-[0_0_20px_rgba(244,63,94,0.1)]">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>

                    <h3 class="text-xl font-black text-rose-500 uppercase tracking-[0.2em] mb-3">Cerrar Sesión</h3>
                    <p class="font-mono-tech text-[10px] text-zinc-400 uppercase leading-relaxed mb-8">
                        ¿Estás seguro de que deseas cerrar sesión?
                    </p>

                    <div class="flex gap-4 w-full">
                        <button @click="showLogoutModal = false"
                            class="flex-1 px-6 py-3 bg-zinc-900 text-zinc-400 font-bold text-[10px] uppercase tracking-widest rounded-xl border border-white/5 hover:bg-zinc-800 transition-all">
                            Cancelar
                        </button>
                        <button wire:click="logout"
                            class="flex-1 px-6 py-3 bg-rose-600 hover:bg-rose-500 text-white font-black text-[10px] uppercase tracking-widest rounded-xl transition-all shadow-[0_0_20px_rgba(244,63,94,0.4)]">
                            Cerrar Sesión
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>