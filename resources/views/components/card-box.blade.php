@props(['title' => '', 'icon' => ''])

<div {{ $attributes->merge(['class' => 'bg-gray-100 rounded-2xl border border-slate-200 shadow-sm p-5 md:p-6']) }}>
    @if ($title)
        <div class="flex items-center gap-3 mb-4">
            @if ($icon)
                <div class="w-8 h-8 flex items-center justify-center font-semibold">
                    {{ $icon }}
                </div>
            @endif
            <h2 class="text-base font-semibold text-slate-900">{{ $title }}</h2>
        </div>
    @endif
    {{ $slot }}
</div>
