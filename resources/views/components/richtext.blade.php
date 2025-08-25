@props([
    'name',
    'label' => '',
    'placeholder' => '',
    'value' => '',
    'preview' => true, // toggle preview on/off per usage if you want
])

@php
    $inputId = $name . '_trix';
@endphp

<div x-data="{
    id: '{{ $inputId }}',
    html: @js(old($name, $value)),
    showPreview: false
}" class="block">
    @if ($label)
        <span class="text-sm font-medium text-slate-700 mb-1 block">{{ $label }}</span>
    @endif

    <!-- Hidden input that Trix syncs to -->
    <input id="{{ $inputId }}" type="hidden" name="{{ $name }}" value="{{ old($name, $value) }}">

    <!-- Editor -->
    <trix-editor x-ref="editor" input="{{ $inputId }}"
        class="trix-content bg-white rounded-lg border border-slate-300 px-3 py-2"
        @trix-initialize="
      // load placeholder only if empty
      @if ($placeholder) if ($refs.editor.editor.getDocument().toString().trim() === '') {
        $refs.editor.editor.loadHTML(@js($placeholder));
      } @endif
      // set initial preview from hidden input value
      html = document.getElementById(id)?.value || '';
    "
        @trix-change="
      // update preview on each change
      html = document.getElementById(id)?.value || '';
    "></trix-editor>

    @if ($preview)
        <!-- Live Preview -->
        <div class="mt-3">
            <div class="flex items-center justify-between mb-1">
                <span class="text-sm font-medium text-slate-700">ตัวอย่าง (Preview)</span>
                <label class="text-xs text-slate-600 flex items-center gap-2">
                    <input type="checkbox" x-model="showPreview" class="rounded border-slate-300">
                    แสดงตัวอย่าง
                </label>
            </div>

            <div x-show="showPreview"
                class="trix-content rounded-lg border border-slate-200 bg-slate-50 p-3 min-h-[2.5rem]"
                x-html="html || '<span class=&quot;text-slate-400&quot;>พิมพ์เพื่อดูตัวอย่าง…</span>'"></div>
        </div>
    @endif
</div>
