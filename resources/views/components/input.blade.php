@props(['name', 'label' => '', 'type' => 'text', 'placeholder' => '', 'required' => false, 'value' => null])

<label class="block">
    @if ($label)
        <span class="text-sm font-medium text-slate-700">{{ $label }} 
          @if ($required)
            <span class="text-red-500">*</span>
          @endif
        </span>
    @endif
    <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}"
    placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}
    {{ $attributes->merge(['class' =>
        'p-2 mt-1 w-full bg-white rounded-xl border border-slate-300 
        placeholder-slate-400 text-sm md:text-base 
        hover:shadow-md hover:border-blue-400 transition
        ']) }}>
</label>
