@props([
    'title' => null, // header text
    'size' => 'md', // sm | md | lg | xl
    'closeOnBg' => true, // click backdrop to close
    'context' => null, // string to identify which modal opened
    'fullOnMobile' => true, // fullscreen on small screens
])

@php
    // Width at >= sm breakpoint (Tailwind utilities)
    $panelWidth =
        [
            'sm' => 'sm:max-w-md',
            'md' => 'sm:max-w-lg',
            'lg' => 'sm:max-w-2xl',
            'xl' => 'sm:max-w-4xl',
        ][$size] ?? 'sm:max-w-lg';

    // Mobile behavior classes
    $mobileShell = $fullOnMobile ? 'w-screen h-screen sm:w-full sm:h-auto' : 'w-full';

    // Height & overflow management
    // On mobile: full screen; On larger screens: constrained height
    $panelFrame = 'bg-white border border-slate-200 shadow-xl flex flex-col overflow-hidden modal-panel';
@endphp

<div x-data="{ open: false }" x-init="$watch('open', v => {
    // lock page scroll while open
    document.documentElement.classList.toggle('modal-open', v);
    if (v) { $dispatch('modal:opened', { context: @js($context) }); }
})" @modal:close.window="open = false" class="inline">
    {{-- Trigger --}}
    <div @click="open = true">
        {{ $trigger ?? '' }}
    </div>

    {{-- Teleport to body to avoid parent overflow clipping --}}
    <template x-teleport="body">
        <div x-show="open" x-cloak class="fixed inset-0 z-[1000] flex items-center justify-center p-3 sm:p-6"
            @keydown.escape.window="open = false" role="dialog" aria-modal="true" aria-label="{{ $title ?? 'Modal' }}">
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/40" @if ($closeOnBg) @click="open = false" @endif>
            </div>

            {{-- Panel wrapper (centering + width) --}}
            <div class="relative mx-auto w-full {{ $panelWidth }} {{ $mobileShell }}">
                {{-- Panel --}}
                <div class="{{ $panelFrame }} sm:rounded-xl rounded-none sm:max-h-[85vh] max-h-screen">
                    {{-- Header (sticky) --}}
                    <div
                        class="flex items-center justify-between px-5 py-3 border-b border-slate-200 bg-white sticky top-0 z-10">
                        <h3 class="text-base font-semibold text-slate-800 truncate">
                            {{ $title }}
                        </h3>
                        <button type="button" class="text-slate-500 hover:text-red-500 btn-ghost-close"
                            @click="open = false" aria-label="Close">✕</button>
                    </div>

                    {{-- Body (scrollable) --}}
                    <div class="px-5 py-4 overflow-auto modal-body grow">
                        {{ $slot }}
                    </div>

                    {{-- Footer (optional, sticky) --}}
                    @isset($footer)
                        <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 sticky bottom-0">
                            {{ $footer }}
                        </div>
                    @endisset
                </div>
            </div>
        </div>
    </template>
</div>
