<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Iris') }} — Panel del Gestor</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script>
        function applyTheme() {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'light') {
                document.documentElement.classList.add('light-mode');
            } else {
                document.documentElement.classList.remove('light-mode');
            }
        }
        applyTheme();
        document.addEventListener('livewire:navigated', applyTheme);
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased gestor-theme obsidian-bg-gestor overflow-x-hidden" style="color: var(--text-primary)"
    x-data="{ isSidebarCollapsed: localStorage.getItem('sidebar-collapsed') === 'true' }"
    @sidebar-toggle.window="isSidebarCollapsed = $event.detail">
    <div class="flex min-h-screen">
        <livewire:gestor.layout.navigation />
        <div class="flex-1 flex flex-col min-w-0 transition-all duration-300"
            :class="isSidebarCollapsed ? 'md:pl-[80px]' : 'md:pl-[260px]'">
            <div class="pt-16 md:pt-0">
                @if (isset($header))
                    <header class="bg-white/5 border-b border-white/5 backdrop-blur-sm">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>
</body>

</html>