@props(['title' => '', 'number' => null])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white shadow-sm p-5 md:p-6']) }}>
  @if($title)
    <div class="flex items-center gap-3 mb-4">
      @if($number !== null)
        <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold">
          {{ $number }}
        </div>
      @endif
      <h2 class="text-lg font-semibold text-slate-900">{{ $title }}</h2>
    </div>
  @endif

  {{ $slot }}
</div>