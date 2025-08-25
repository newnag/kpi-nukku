@props([
  'name',
  'label' => '',
  'options' => [],     // ['value' => 'Label'] or [['id'=>..,'name'=>..]]
  'placeholder' => 'กรุณาเลือก',
  'required' => false,
  'optionValue' => 'id',
  'optionLabel' => 'name',
])

<label class="block">
  @if($label)
    <span class="text-sm font-medium text-slate-700">{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</span>
  @endif
  <select name="{{ $name }}" {{ $required ? 'required' : '' }}
          {{ $attributes->merge(['class' => 'mt-1 w-full rounded-lg border border-slate-300 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500']) }}>
    <option value="">{{ $placeholder }}</option>

    @foreach($options as $k => $v)
      @if(is_array($v))
        <option value="{{ $v[$optionValue] }}" @selected(old($name) == $v[$optionValue])>{{ $v[$optionLabel] }}</option>
      @else
        <option value="{{ $k }}" @selected(old($name) == $k)>{{ $v }}</option>
      @endif
    @endforeach
  </select>
</label>
