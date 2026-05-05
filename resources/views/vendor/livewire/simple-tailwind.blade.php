@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

@if ($paginator->hasPages())
    <div class="py-6 flex justify-center">
        <nav role="navigation" aria-label="Pagination Navigation" 
            class="inline-flex items-center p-1 bg-white/40 dark:bg-black/30 backdrop-blur-xl rounded-2xl border border-zinc-200/50 dark:border-white/5 shadow-lg shadow-black/[0.03] dark:shadow-black/20">
            
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="w-9 h-9 flex items-center justify-center text-zinc-300 dark:text-zinc-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </span>
            @else
                <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" 
                    class="w-9 h-9 flex items-center justify-center rounded-xl text-zinc-500 dark:text-zinc-400 hover:bg-white dark:hover:bg-zinc-800 hover:text-black dark:hover:text-white transition-all duration-300 active:scale-90">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </button>
            @endif

            {{-- Pagination Elements --}}
            <div class="flex items-center gap-0.5">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="px-1 text-zinc-400 dark:text-zinc-600 font-mono text-xs select-none">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="relative z-10 w-9 h-9 flex items-center justify-center text-xs font-black text-black dark:text-white rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-white/10 shadow-sm animate-tech-pop">
                                    {{ $page }}
                                    {{-- Subtle Dreamcore Blur --}}
                                    <div class="absolute inset-0 -z-10 bg-zinc-400/10 dark:bg-white/5 blur-sm rounded-xl"></div>
                                </span>
                            @else
                                <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled"
                                    class="w-9 h-9 flex items-center justify-center text-xs font-bold text-zinc-400 dark:text-zinc-500 rounded-xl hover:text-zinc-900 dark:hover:text-zinc-200 transition-all duration-300">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled"
                    class="w-9 h-9 flex items-center justify-center rounded-xl text-zinc-500 dark:text-zinc-400 hover:bg-white dark:hover:bg-zinc-800 hover:text-black dark:hover:text-white transition-all duration-300 active:scale-90">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </button>
            @else
                <span class="w-9 h-9 flex items-center justify-center text-zinc-300 dark:text-zinc-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </span>
            @endif
        </nav>
    </div>
@endif

<style>
    @keyframes tech-pop {
        0% { transform: scale(0.8); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    .animate-tech-pop {
        animation: tech-pop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }
</style>
