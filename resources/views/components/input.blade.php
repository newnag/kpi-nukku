@props([
  'name',
  'label' => '',
  'type' => 'text',
  'placeholder' => '',
  'required' => false,
  'value' => null,
])

<label class="block">
  @if($label)
    <span class="text-sm font-medium text-slate-700">{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</span>
  @endif
  <input
    type="{{ $type }}"
    name="{{ $name }}"
    value="{{ old($name, $value) }}"
    placeholder="{{ $placeholder }}"
    {{ $required ? 'required' : '' }}
    {{ $attributes->merge(['class' => 'mt-1 w-full rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-slate-400']) }}>
</label>
