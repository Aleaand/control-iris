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
    openUser: false,
    showLogoutModal: false,
    isCollapsed: localStorage.getItem('sidebar-collapsed') === null ? true : localStorage.getItem('sidebar-collapsed') === 'true',
    isDarkMode: localStorage.getItem('theme') !== 'light',
    localTime: '',
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
    }
}" x-init="
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
            <a href="{{ route('gestor.dashboard') }}" wire:navigate>
                <img :src="isDarkMode ? '{{ asset('assets/logo_iris.png') }}' : '{{ asset('assets/logo_iris_black.png') }}'"
                    alt="Iris" class="h-8 transition-transform duration-300" :class="openMobile ? 'scale-105' : ''">
            </a>
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

        <button @click.stop="isCollapsed = !isCollapsed; openUser = false" class="collapse-toggle hidden md:flex">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </button>

        <!-- Header -->
        <div class="sidebar-header hidden md:block">
            <a href="{{ route('gestor.dashboard') }}" wire:navigate class="logo-container group">
                <img :src="isDarkMode ? '{{ asset('assets/logo_iris.png') }}' : '{{ asset('assets/logo_iris_black.png') }}'"
                    alt="Iris" class="logo-img group-hover:scale-105 transition-transform duration-500">
                <div class="logo-text" style="line-height: 1.2;" x-show="!isCollapsed"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-x-4"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-700" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0">
                    <span class="font-bold text-sm tracking-[0.2em]" style="color: var(--text-primary)">IRIS</span><br>
                    <span class="font-mono-tech text-[8px] text-emerald-500">GESTOR</span>
                </div>
            </a>
        </div>

        <!-- Badge de rol -->
        <div x-show="!isCollapsed" class="px-4 mb-4"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100">
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></div>
                <span class="font-mono-tech text-[9px] text-emerald-400 uppercase tracking-widest">Gestor Activo</span>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="nav-menu custom-scrollbar overflow-y-auto mt-2 md:mt-0">

            <!-- Dashboard -->
            <a href="{{ route('gestor.dashboard') }}" wire:navigate class="nav-item hover-emerald"
                :class="'{{ request()->routeIs('gestor.dashboard') }}' === '1' ? 'active text-emerald-500' : ''">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                <span class="nav-text" x-show="!isCollapsed"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-x-2"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-700"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">Dashboard</span>
            </a>

            <!-- Mis Clientes -->
            <a href="{{ route('gestor.clients') }}" wire:navigate class="nav-item hover-cyan"
                :class="'{{ request()->routeIs('gestor.clients') }}' === '1' ? 'active text-cyan-500' : ''">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span class="nav-text" x-show="!isCollapsed"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-x-2"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-700"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">Mis Clientes</span>
            </a>

            <!-- Central de Reservas -->
            <a href="{{ route('gestor.reservations') }}" wire:navigate class="nav-item"
                style="--hover-color: #10b981"
                :class="'{{ request()->routeIs('gestor.reservations') }}' === '1' ? 'active' : ''"
                :style="'{{ request()->routeIs('gestor.reservations') }}' === '1' ? 'color: #10b981' : ''">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                </svg>
                <span class="nav-text" x-show="!isCollapsed"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-x-2"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-700"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">Central de Reservas</span>
            </a>

            <!-- Compliance -->
            <a href="{{ route('gestor.compliance') }}" wire:navigate class="nav-item hover-violet"
                :class="'{{ request()->routeIs('gestor.compliance') }}' === '1' ? 'active text-violet-500' : ''">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <span class="nav-text" x-show="!isCollapsed"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-x-2"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-700"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">Compliance</span>
            </a>

            <!-- Pagos y Reembolsos -->
            <a href="{{ route('gestor.payments') }}" wire:navigate class="nav-item hover-amber"
                :class="'{{ request()->routeIs('gestor.payments') }}' === '1' ? 'active text-amber-500' : ''">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                    <line x1="1" y1="10" x2="23" y2="10"></line>
                </svg>
                <span class="nav-text" x-show="!isCollapsed"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-x-2"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-700"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">Pagos & Reembolsos</span>
            </a>

            <!-- Radar -->
            <a href="{{ route('gestor.radar') }}" wire:navigate class="nav-item hover-rose"
                :class="'{{ request()->routeIs('gestor.radar') }}' === '1' ? 'active text-rose-500' : ''">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="2"></circle>
                    <path d="M16.24 7.76a6 6 0 0 1 0 8.49m-8.48-.01a6 6 0 0 1 0-8.49m11.31-2.82a10 10 0 0 1 0 14.14m-14.14 0a10 10 0 0 1 0-14.14"></path>
                </svg>
                <span class="nav-text" x-show="!isCollapsed"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-x-2"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-700"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">Radar de Recursos</span>
            </a>

            <!-- Misiones -->
            <a href="{{ route('gestor.missions') }}" wire:navigate class="nav-item hover-orange"
                :class="'{{ request()->routeIs('gestor.missions') }}' === '1' ? 'active text-orange-500' : ''">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 11 12 14 22 4"></polyline>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                </svg>
                <span class="nav-text" x-show="!isCollapsed"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-x-2"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-700"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">Misiones
                    @php $pendingTasks = \App\Models\Task::where('assigned_gestor_id', auth()->id())->where('status', 'Pendiente')->count(); @endphp
                    @if($pendingTasks > 0)
                        <span class="ml-auto bg-orange-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">{{ $pendingTasks }}</span>
                    @endif
                </span>
            </a>

            <!-- Comunicación -->
            <a href="{{ route('gestor.communication') }}" wire:navigate class="nav-item hover-blue"
                :class="'{{ request()->routeIs('gestor.communication') }}' === '1' ? 'active text-blue-400' : ''">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <span class="nav-text" x-show="!isCollapsed"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-x-2"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-700"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">Comunicación</span>
            </a>

        </nav>

        <!-- User Bubble Section -->
        <div class="user-bubble-container" x-cloak>
            <div class="flex items-center gap-4">
                <div class="user-bubble" @click.stop="openUser = !openUser">
                    <span class="font-bold" style="color: var(--text-primary)">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                <div class="min-w-0 user-info" x-show="!isCollapsed"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-700"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <p class="text-xs font-bold truncate" style="color: var(--text-primary)">{{ auth()->user()->name }}</p>
                    <p class="font-mono-tech text-[8px] text-emerald-500 uppercase">Gestor</p>
                </div>
            </div>

            <!-- User Dropdown Panel -->
            <div x-show="openUser" x-cloak @click.away="openUser = false"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-0"
                class="user-dropdown-panel z-50"
                style="left: 85px; bottom: 24px;">
                <div @click="toggleTheme"
                    class="w-full flex items-center justify-between px-4 py-3 text-xs text-zinc-400 hover:text-[var(--text-primary)] hover:bg-white/5 rounded-lg mb-1 transition-all cursor-pointer group/theme">
                    <span x-text="isDarkMode ? 'Modo Día' : 'Modo Noche'" class="transition-colors group-hover/theme:text-[var(--text-primary)]"></span>
                    <div class="theme-switch" :class="{ 'active': !isDarkMode }">
                        <div class="theme-switch-dot">
                            <svg x-show="isDarkMode" class="theme-switch-icon text-zinc-900" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                            </svg>
                            <svg x-show="!isDarkMode" class="theme-switch-icon text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
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
                <span x-text="localTime"></span>
            </div>
        </div>
    </aside>

    <div x-show="openMobile" @click="openMobile = false"
        class="md:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[90]"></div>

    <!-- Logout Modal -->
    <template x-teleport="body">
        <div x-show="showLogoutModal" x-cloak class="fixed inset-0 z-[300] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/80 backdrop-blur-md" @click="showLogoutModal = false"></div>
            <div class="tech-card p-8 w-full max-w-sm relative overflow-hidden"
                x-show="showLogoutModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100"
                style="background: #0a0a0f; border-color: rgba(244, 63, 94, 0.2);">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-rose-500 to-transparent animate-pulse"></div>
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-2xl bg-rose-500/10 flex items-center justify-center text-rose-500 mb-6 border border-rose-500/20">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-rose-500 uppercase tracking-[0.2em] mb-3">Finalizar Sesión</h3>
                    <p class="font-mono-tech text-[10px] text-zinc-400 uppercase leading-relaxed mb-8">
                        ¿Estás seguro de que deseas desconectarte del panel de gestión?
                    </p>
                    <div class="flex gap-4 w-full">
                        <button @click="showLogoutModal = false" class="flex-1 px-6 py-3 bg-zinc-900 text-zinc-400 font-bold text-[10px] uppercase tracking-widest rounded-xl border border-white/5 hover:bg-zinc-800 transition-all">
                            Abortar
                        </button>
                        <button wire:click="logout" class="flex-1 px-6 py-3 bg-rose-600 hover:bg-rose-500 text-white font-black text-[10px] uppercase tracking-widest rounded-xl transition-all shadow-[0_0_20px_rgba(244,63,94,0.4)]">
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
