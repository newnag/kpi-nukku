@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">
        <h2 class="text-2xl font-bold mb-6">สร้างรายงาน SAR</h2>
        @if (request('year'))
            <div class="text-gray-600 mb-6">ปีการประเมิน: <span class="font-semibold">{{ request('year') }}</span></div>
        @endif
        <form method="POST" action="{{ route('sar_reports.update', $report->id) }}" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="year" value="{{ $report->year }}">
            {{-- ชื่อเรื่อง --}}
            <div class="bg-white shadow rounded-lg p-6">
                <label for="title" class="block text-lg font-semibold mb-2">ชื่อเรื่อง (ถ้ามี)</label>
                <input type="text" name="title" id="title" class="w-full border rounded px-3 py-2"
                    value="{{ old('title', $report->title) }}" placeholder="เช่น รายงานการประเมินตนเอง ประจำปี 2566">
            </div>
            {{-- ส่วนที่ 1 --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold border-b pb-2 mb-4">ส่วนที่ 1: ข้อมูลทั่วไปคณะพยาบาลศาสตร์</h3>
                <textarea name="section1" id="section1" class="trumbowyg-textarea w-full">{{ old('section1', $report->section1 ?? '') }}</textarea>
            </div>

            {{-- ส่วนที่ 2 --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold border-b pb-2 mb-4">ส่วนที่ 2: ข้อมูลด้านคุณภาพ</h3>
                <textarea name="section2" id="section2" class="trumbowyg-textarea w-full">{{ old('section2', $report->section2 ?? '') }}</textarea>
            </div>

            {{-- ส่วนที่ 3 --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold border-b pb-2 mb-4">ส่วนที่ 3: การประเมินตนเองตามตัวบ่งชี้</h3>

                @foreach ($standards as $stdName => $indsByStd)
                    <div class="mt-6">
                        <h4 class="text-md font-bold text-blue-700 mb-2">มาตรฐาน: {{ $stdName }}</h4>

                        @foreach ($indsByStd->groupBy('category.name') as $catName => $inds)
                            <h5 class="text-md font-semibold text-gray-700 mt-4 mb-2">ด้าน: {{ $catName }}</h5>

                            @foreach ($inds as $ind)
                                <div class="bg-gray-50 p-3 rounded-md border mb-4">
                                    <div class="font-semibold text-gray-800 mb-2">[{{ $ind->code }}] {{ $ind->name }}
                                    </div>

                                    {{-- ตารางเกณฑ์ --}}
                                    <div class="overflow-x-auto">
                                        <table class="w-full border-collapse text-sm">
                                            <thead>
                                                <tr class="bg-gray-200 text-center text-gray-700">
                                                    <th class="border px-2 py-1 w-12">ข้อ</th>
                                                    <th class="border px-2 py-1">เกณฑ์มาตรฐาน</th>
                                                    <th class="border px-2 py-1">ผลการดำเนินงาน</th>
                                                    <th class="border px-2 py-1">รายงานผลการดำเนินงาน</th>
                                                    <th class="border px-2 py-1">เอกสารหลักฐาน</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y">
                                                @forelse($ind->criterias as $i => $cri)
                                                    <tr class="hover:bg-gray-100">
                                                        <td class="border text-center">{{ $i + 1 }}</td>
                                                        <td class="border px-2">{{ $cri->name }}</td>
                                                        <td class="border text-center">
                                                            @if ($cri->status)
                                                                <span class="text-green-600">✓</span>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="border px-2 text-center cursor-pointer hover:bg-blue-50"
                                                            x-data="{ open: false, text: @js($cri->report ?? ''), saving: false, saved: false, showPreview: false }" @click="open = true" tabindex="0"
                                                            role="button" @keydown.enter.prevent="open = true"
                                                            @keydown.space.prevent="open = true" x-init="// init Trumbowyg เมื่อ modal เปิด
                                                            $watch('open', value => {
                                                                if (value) {
                                                                    setTimeout(() => {
                                                                        if ($('#editor-{{ $cri->id }}').data('trumbowyg')) $('#editor-{{ $cri->id }}').trumbowyg('destroy');
                                                                        $('#editor-{{ $cri->id }}').trumbowyg({
                                                                            svgPath: 'https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/ui/icons.svg',
                                                                            btns: [
                                                                                ['viewHTML'],
                                                                                ['undo', 'redo'],
                                                                                ['formatting'],
                                                                                ['fontsize', 'fontfamily'],
                                                                                ['foreColor', 'backColor'],
                                                                                ['strong', 'em', 'underline', 'del'],
                                                                                ['unorderedList', 'orderedList'],
                                                                                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                                                                                ['horizontalRule'],
                                                                                ['removeformat'],
                                                                                ['fullscreen']
                                                                            ],
                                                                            plugins: {
                                                                                fontsize: {
                                                                                    sizeList: ['12px', '14px', '16px', '18px', '20px', '24px', '28px', '32px']
                                                                                }
                                                                            },
                                                                            autogrow: true,
                                                                        }).on('tbwchange', function() { text = $(this).trumbowyg('html'); });
                                                                    }, 200);
                                                                }
                                                            })">

                                                            <!-- สถานะบันทึก -->
                                                            <div class="mt-1 text-xs" x-show="saved">
                                                                <span class="text-green-600 font-semibold">✓
                                                                    บันทึกแล้ว</span>
                                                            </div>

                                                            <!-- ข้อความแนะนำเมื่อยังไม่มีข้อมูล -->
                                                            <template
                                                                x-if="!(text && text.replace(/<[^>]*>/g,'').trim().length)">
                                                                <div class="mt-1 text-xs text-gray-500 italic select-none">
                                                                    คลิกเพื่อกรอก
                                                                </div>
                                                            </template>

                                                            <!-- ตัวอย่างหากมีข้อมูล -->
                                                            <template
                                                                x-if="text && text.replace(/<[^>]*>/g,'').trim().length">
                                                                <div class="mt-2 text-left">
                                                                    <div class="rte-preview border rounded bg-gray-50 p-2 max-h-28 overflow-auto"
                                                                        x-html="text"></div>
                                                                    <div class="text-[10px] text-gray-500 mt-1">ตัวอย่าง
                                                                    </div>
                                                                </div>
                                                            </template>

                                                            <!-- Modal -->
                                                            <!-- Modal -->
                                                            <div x-show="open"
                                                                class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
                                                                x-cloak @click.self="open = false">
                                                                <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6"
                                                                    @click.stop>
                                                                    <!-- ✅ กันไม่ให้คลิกด้านในส่งต่อไปข้างนอก -->
                                                                    <h3 class="text-lg font-semibold mb-4">
                                                                        กรอกรายงานผลการดำเนินงาน
                                                                    </h3>

                                                                    <!-- Rich Text Editor -->
                                                                    <textarea id="editor-{{ $cri->id }}" x-ref="editor">{{ $cri->report }}</textarea>

                                                                    <div class="mt-4 flex justify-end space-x-2">
                                                                        <button type="button"
                                                                            class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
                                                                            @click.stop="open = false"> <!-- ✅ .stop -->
                                                                            ยกเลิก
                                                                        </button>
                                                                        <button type="button"
                                                                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                                                            @click.stop="
                    saving = true;
                    fetch('{{ route('sar_reports.criterias.updateReport', $cri->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ report: text })
                    })
                    .then(res => res.json())
                    .then(data => {
                        saving = false;
                        saved = true;
                        open = false;
                    })
                    .catch(() => { saving = false; });
                ">
                                                                            <span x-show="!saving">บันทึก</span>
                                                                            <span x-show="saving">กำลังบันทึก...</span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </td>
                                                        <td class="border px-2">
                                                            @if ($cri->evidences->isNotEmpty())
                                                                <ul class="list-disc list-inside text-blue-600 text-sm">
                                                                    @foreach ($cri->evidences as $ev)
                                                                        <li>
                                                                            <a href="{{ route('evidences.download', $ev->id) }}"
                                                                                class="hover:underline">
                                                                                {{ $ev->name }}
                                                                            </a>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <span class="text-gray-400">-</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="border text-center text-gray-500">
                                                            ไม่มีข้อมูล</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- เกณฑ์การให้คะแนน --}}
                                    <div class="mt-4 border rounded-md overflow-hidden">
                                        <table class="w-full text-sm">
                                            <thead>
                                                <tr class="bg-gray-100 text-center">
                                                    <th class="border px-2 py-1">เกณฑ์การให้คะแนน</th>
                                                    <th class="border px-2 py-1">คะแนน</th>
                                                    <th class="border px-2 py-1 w-32">การประเมินตนเอง</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $lines = [];
                                                    if (!empty($ind->comment)) {
                                                        $plain = preg_replace(
                                                            '/<\/(p|div|li|br)>/i',
                                                            "\n",
                                                            $ind->comment,
                                                        );
                                                        $plain = strip_tags($plain);
                                                        $plain = html_entity_decode($plain, ENT_QUOTES, 'UTF-8');
                                                        $lines = preg_split('/\r\n|\r|\n/', $plain);
                                                        $lines = array_filter(array_map('trim', $lines));
                                                    }
                                                    $score = $ind->self_score ?? ($ind->score_acc ?? null);
                                                @endphp

                                                @forelse ($lines as $line)
                                                    @php
                                                        $scoreFromLine = null;
                                                        if (
                                                            preg_match(
                                                                '/\(\s*(?:([0-9]+(?:\.[0-9]+)?)\s*คะแนน|คะแนน\s*([0-9]+(?:\.[0-9]+)?)|([0-9]+(?:\.[0-9]+)?))\s*\)/u',
                                                                $line,
                                                                $mm,
                                                            )
                                                        ) {
                                                            $scoreFromLine =
                                                                (float) (array_values(
                                                                    array_filter([
                                                                        $mm[1] ?? null,
                                                                        $mm[2] ?? null,
                                                                        $mm[3] ?? null,
                                                                    ]),
                                                                )[0] ?? null);
                                                        }

                                                        $match =
                                                            $score !== null &&
                                                            $scoreFromLine !== null &&
                                                            abs($scoreFromLine - (float) $score) < 0.001;
                                                    @endphp
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="border px-2 text-left">{{ $line }}</td>
                                                        <td class="border text-center">
                                                            @if ($scoreFromLine !== null)
                                                                <span
                                                                    class="{{ $match ? 'font-bold text-green-600' : '' }}">
                                                                    {{ $scoreFromLine }} คะแนน
                                                                </span>
                                                            @else
                                                                ........... คะแนน
                                                            @endif
                                                        </td>
                                                        <td class="border text-center">
                                                            @if ($match)
                                                                <span class="text-green-600 font-bold">✓</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td class="border px-2">............................</td>
                                                        <td class="border text-center">........... คะแนน</td>
                                                        <td class="border"></td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                @endforeach
            </div>

            {{-- ส่วนที่ 4 --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold border-b pb-2 mb-4">ส่วนที่ 4:
                    สรุปผลการประเมินตนเองตามเกณฑ์ของสภาการพยาบาล</h3>
                <textarea name="section4" id="section4" class="trumbowyg-textarea w-full">{{ old('section4', $report->section4 ?? '') }}</textarea>
            </div>

            <div class="flex justify-end space-x-2">

                <div x-data="{ open: false }" class="relative flex justify-end">
                    <button type="button" @click="open = !open"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md shadow inline-flex items-center">
                        <i data-lucide="download" class="w-4 h-4 mr-2"></i> Export
                    </button>

                    <div x-show="open" @click.outside="open = false"
                        class="absolute right-0 mt-2 w-40 bg-white border rounded-md shadow-lg z-50" x-cloak>
                        <a type="button" href="{{ route('sar_reports.export.docx', $report->id) }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> DOCX
                        </a>
                        <a type="button" href="{{ route('sar_reports.export.xlsx', $report->id) }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2"></i> Excel
                        </a>
                        {{-- <a type="button" href="{{ route('sar_reports.export.pdf', $report->id) }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i data-lucide="file-type" class="w-4 h-4 mr-2"></i> PDF
                        </a> --}}
                        <a href="{{ route('sar_reports.export.pdf', $report->id) }}" target="_blank"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Preview PDF
                        </a>

                    </div>
                </div>

                <!-- ปุ่ม Save -->
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md shadow inline-flex items-center">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i> บันทึก
                </button>
            </div>


        </form>
    </div>
@endsection


@push('styles')
    <!-- Trumbowyg core CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/ui/trumbowyg.min.css">
    <!-- Trumbowyg colors plugin CSS -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/plugins/colors/ui/trumbowyg.colors.min.css">
    <!-- Webfont (TH Sarabun via Google Fonts) -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap">

    <style>
        /* Ensure list markers (bullets/numbers) are visible inside editor */
        .trumbowyg-box .trumbowyg-editor ul {
            list-style-type: disc !important;
            list-style-position: inside !important;
            margin-left: 1.5em;
            padding-left: 0;
        }

        .trumbowyg-box .trumbowyg-editor ol {
            list-style-type: decimal !important;
            list-style-position: inside !important;
            margin-left: 1.5em;
            padding-left: 0;
        }

        .trumbowyg-box .trumbowyg-editor li {
            display: list-item !important;
        }

        /* Preview HTML list markers */
        .rte-preview ul {
            list-style-type: disc !important;
            list-style-position: inside !important;
            margin-left: 1.5em;
            padding-left: 0;
        }

        .rte-preview ol {
            list-style-type: decimal !important;
            list-style-position: inside !important;
            margin-left: 1.5em;
            padding-left: 0;
        }

        .rte-preview li {
            display: list-item !important;
        }
    </style>
@endpush

@push('scripts')
    <!-- jQuery + Trumbowyg core -->
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/trumbowyg.min.js"></script>

    <!-- Plugins -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/plugins/fontsize/trumbowyg.fontsize.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/plugins/colors/trumbowyg.colors.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/plugins/table/trumbowyg.table.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/plugins/fontfamily/trumbowyg.fontfamily.min.js">
    </script>

    <!-- Init Editor -->
    <script>
        $(function() {
            $('#section1, #section2, #section4').trumbowyg({
                svgPath: 'https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/ui/icons.svg',
                btns: [
                    ['viewHTML'],
                    ['undo', 'redo'],
                    ['formatting'],
                    ['fontsize', 'fontfamily'],
                    ['foreColor', 'backColor'],
                    ['strong', 'em', 'underline', 'del'],
                    ['superscript', 'subscript'],
                    ['link'],
                    ['insertImage'],
                    ['table'],
                    ['unorderedList', 'orderedList'],
                    ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                    ['horizontalRule'],
                    ['removeformat'],
                    ['fullscreen']
                ],
                plugins: {
                    fontsize: {
                        sizeList: ['10px', '12px', '14px', '16px', '18px', '20px', '24px', '28px', '32px',
                            '36px'
                        ]
                    },
                    fontfamily: {
                        fontList: [{
                                name: 'TH Sarabun',
                                family: "'Sarabun','TH Sarabun New', sans-serif"
                            },
                            {
                                name: 'Arial',
                                family: 'Arial, Helvetica, sans-serif'
                            },
                            {
                                name: 'Times New Roman',
                                family: '"Times New Roman", Times, serif'
                            },
                            {
                                name: 'Tahoma',
                                family: 'Tahoma, Geneva, sans-serif'
                            },
                            {
                                name: 'Courier New',
                                family: '"Courier New", Courier, monospace'
                            }
                        ]
                    }
                },
                autogrow: true,
                semantic: true,
                resetCss: true
            });
        });
    </script>
@endpush
