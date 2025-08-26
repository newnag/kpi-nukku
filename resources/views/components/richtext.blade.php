@props([
    'name',
    'label' => '',
    'placeholder' => '',
    'value' => '',
    'preview' => true,
    'height' => 260, // px
])

@php
    $inputId = $name . '_trix'; // keep same hidden input id
    $editorId = $name . '_trumbowyg'; // Trumbowyg host
@endphp

<div x-data="{ html: @js(old($name, $value)), showPreview: false }" class="block">
    @if ($label)
        <span class="text-sm font-medium text-slate-700 mb-1 block">{{ $label }}</span>
    @endif

    <input id="{{ $inputId }}" type="hidden" name="{{ $name }}" value="{{ old($name, $value) }}">

    {{-- Host: ensure rounded + border present (you were missing `border`) --}}
    <div id="{{ $editorId }}" class="rounded-b-2xl w-full h-full border border-slate-200 bg-white shadow-sm overflow-hidden"
        style="--rte-min-h: {{ (int) $height }}px"></div>

    @if ($preview)
        <div class="mt-3">
            <div class="flex items-center justify-between mb-1">
                <span class="text-sm font-medium text-slate-700">ตัวอย่าง (Preview)</span>
                <label class="text-xs text-slate-600 flex items-center gap-2">
                    <input type="checkbox" x-model="showPreview" class="rounded border-slate-300">
                    แสดงตัวอย่าง
                </label>
            </div>
            <div x-show="showPreview"
                class="rounded-xl border border-slate-200 bg-slate-50 p-3 min-h-[2.5rem] prose max-w-none"
                x-html="html || '<span class=&quot;text-slate-400&quot;>พิมพ์เพื่อดูตัวอย่าง…</span>'"></div>
        </div>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const $host = $('#{{ $editorId }}');

                // initial content (use placeholder if empty)
                let initialHtml = @json(old($name, $value));
                if (!initialHtml && @json($placeholder)) initialHtml = @json($placeholder);
                $host.html(initialHtml || '');

                $host.trumbowyg({
                    autogrow: true,
                    btns: [
                        ['undo', 'redo'],
                        ['strong', 'em', 'del'],
                        ['superscript', 'subscript'],
                        ['link'],
                        ['table'],
                        ['unorderedList', 'orderedList'],
                        ['justifyLeft', 'justifyCenter', 'justifyRight'],
                        ['horizontalRule'],
                        ['removeformat'],
                        ['viewHTML'],
                    ],
                    svgPath: 'https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/ui/icons.svg',
                });

                // keep hidden input + preview in sync
                const sync = () => {
                    const html = $host.trumbowyg('html');
                    document.getElementById('{{ $inputId }}').value = html;
                    const root = document.getElementById('{{ $editorId }}').closest('[x-data]');
                    if (root && root.__x) root.__x.$data.html = html;
                };
                $host.on('tbwchange tbwblur tbwfocus', sync);
                sync();

                // ensure sync on submit
                const form = $host.closest('form')[0];
                form?.addEventListener('submit', sync);
            });
        </script>
    @endpush
</div>
