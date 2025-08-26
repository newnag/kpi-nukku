@props([
    'name',
    'label' => '',
    'placeholder' => '',
    'value' => '',
    'preview' => true,
    'height' => 260, // px
])

@php
    $inputId = $name . '_trix';
    $editorId = $name . '_trumbowyg';
@endphp

@once
    @push('styles')
        <style>
            /* สไตล์คอนเทนต์ในพรีวิว */
            .rte-content {
                font-size: .9375rem;
            }

            .rte-content p {
                margin: .5rem 0;
            }

            .rte-content ul {
                list-style: disc;
                padding-left: 1.25rem;
            }

            .rte-content ol {
                list-style: decimal;
                padding-left: 1.5rem;
            }

            .rte-content li {
                margin: .25rem 0;
            }

            .rte-content table {
                width: 100%;
                border-collapse: collapse;
                font-size: .95rem;
            }

            .rte-content table,
            .rte-content th,
            .rte-content td {
                border: 1px solid #e5e7eb;
                /* slate-200 */
            }

            .rte-content th,
            .rte-content td {
                padding: .5rem .625rem;
            }

            /* ป้องกัน FOUC เวลา x-cloak ยังไม่พร้อม */
            [x-cloak] {
                display: none !important;
            }
        </style>
    @endpush
@endonce


<div x-data="{ html: @js(old($name, $value)), showPreview: true }" class="block">
    @if ($label)
        <span class="text-sm font-medium text-slate-700 mb-1 block">{{ $label }}</span>
    @endif

    <input id="{{ $inputId }}" type="hidden" name="{{ $name }}" x-model="html" x-ref="input"
        value="{{ old($name, $value) }}">

    <div id="{{ $editorId }}"
        class="rounded-b-2xl w-full h-full border border-slate-200 bg-white shadow-sm overflow-hidden"
        style="--rte-min-h: {{ (int) $height }}px"></div>

    @if ($preview)
        <div class="mt-3" x-data="{ placeholder: 'พิมพ์เพื่อดูตัวอย่าง…' }">
            <div class="flex items-center justify-between mb-1">
                <span class="text-sm font-medium text-slate-700">ตัวอย่าง (Preview)</span>
                <label class="text-xs text-slate-600 flex items-center gap-2">
                    <input type="checkbox" x-model="showPreview" class="rounded border-slate-300">
                    แสดงตัวอย่าง
                </label>
            </div>

            <!-- ใช้ x-effect อัปเดต innerHTML ทุกครั้งที่ html เปลี่ยน -->
            <div x-show="showPreview" x-cloak
                class="rte-content rounded-xl border border-slate-200 bg-slate-50 p-3 min-h-[2.5rem] max-w-none"
                x-effect="
                $el.innerHTML = (html && html.trim().length)
                  ? html
                  : `<span class=&quot;text-slate-400&quot;>${placeholder}</span>`;
             ">
            </div>
        </div>
    @endif


    @push('scripts')
        <script>
            (function() {
                function run() {
                    var $host = $('#{{ $editorId }}');
                    if (!$host.length || typeof $host.trumbowyg !== 'function') return;

                    var initialHtml = @json(old($name, $value)) || '';
                    var placeholder = @json($placeholder);

                    function sync() {
                        var html = $host.trumbowyg('html');
                        var input = document.getElementById('{{ $inputId }}');
                        if (input) {
                            input.value = html;
                            input.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                        }
                    }

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

                    $host.on('tbwinit', function() {
                        if (initialHtml.trim().length) {
                            $host.trumbowyg('html', initialHtml);
                        } else if (placeholder) {
                            // show UI-only placeholder (won't be submitted)
                            $host.attr('placeholder', placeholder);
                        }
                        sync();

                        // ---- Robust live sync ----
                        var editorEl =
                            $host.closest('.trumbowyg-box').find('.trumbowyg-editor')[0] ||
                            $host.siblings('.trumbowyg-editor')[0] ||
                            $host.find('.trumbowyg-editor')[0];

                        if (editorEl) {
                            // Direct DOM events (catch toolbar actions that don't emit tbwchange)
                            $(editorEl).on('input keyup paste blur', sync);

                            // Observe any DOM mutations (e.g., bold/italic wraps)
                            var rafId = null;
                            var observer = new MutationObserver(function() {
                                if (rafId) cancelAnimationFrame(rafId);
                                rafId = requestAnimationFrame(sync);
                            });
                            observer.observe(editorEl, {
                                childList: true,
                                characterData: true,
                                attributes: true,
                                subtree: true
                            });

                            // Extra Trumbowyg events for good measure
                            $host.on('tbwchange tbwblur tbwfocus tbwkeyup tbwpaste', sync);
                        }
                    });

                    var form = $host.closest('form')[0];
                    if (form) form.addEventListener('submit', sync);
                }

                if (document.readyState !== 'loading') run();
                else document.addEventListener('DOMContentLoaded', run);
            })();
        </script>
    @endpush
</div>
