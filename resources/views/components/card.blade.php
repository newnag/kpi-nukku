@props(['title' => '', 'number' => null])

<div
    {{ $attributes->merge([
        'class' => 'bg-white rounded-2xl border border-slate-200 shadow-sm 
       p-4 sm:p-5 md:p-6 hover:shadow-md transition',
    ]) }}>
    @if ($title)
        <div class="flex items-center gap-3 mb-3 md:mb-4">
            @if ($number !== null)
                <div
                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold text-sm sm:text-base">
                    {{ $number }}
                </div>
            @endif
            <h2 class="text-base md:text-lg font-semibold text-slate-900">{{ $title }}</h2>
        </div>
    @endif

    {{ $slot }}
</div>
