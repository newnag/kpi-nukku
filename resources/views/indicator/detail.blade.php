@extends('layouts.app')

@section('title', 'รายละเอียดตัวชี้วัด')

@push('styles')
    <style>
        /* Custom banner gradient with a modern, soft look */
        .banner {
            background: linear-gradient(90deg, #e0f2fe 0%, #fef3e0 100%);
            transition: all 0.3s ease-in-out;
        }

        /* Smooth hover effects for buttons */
        .action-btn {
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }
    </style>
@endpush

@section('content')
    @php
        // --- Normalize input (works with: ['data'=>...] JSON, or $indicator model/array) ---
        $ind = $data['data'] ?? ($data ?? ($indicator ?? null));

        // helper (works for array|object)
        $dg = fn($key, $default = null) => data_get($ind, $key, $default);

        // Basic fields
        $indicatorId = $dg('id') ?? null;
        $year = $dg('year') ?? '-';
        $name = $dg('name') ?? '-';
        $code = $dg('code') ?? '-';
        $type = $dg('type') ?? '-'; 
        $maxScore = $dg('max_score') ?? '-';
        $deadline = $dg('deadline') ?? '-';
        $status = $dg('status') ?? '-';
        $comment = $dg('comment') ?? '-';
        $descHtml = $dg('description') ?? '-';
        $condHtml = $dg('condition') ?? '-';
        $annoHtml = $dg('annotation') ?? '-';

        // Dates
        $deadlineDisplay = $deadline ? \Carbon\Carbon::parse($deadline)->format('d/m/Y') : '-';

        // Category / Standard (prefer nested, fallback to top-level)
        $categoryName = $dg('category.name') ?? '-';
        $standardName = $dg('category.standard.name') ?? ($dg('standard.name') ?? '-');

        // Criteria (ordered by sequence)
        $criteria = collect($dg('criterias', []))->sortBy('sequence')->values();
        $criteriaList = $criteria->pluck('name')->all();

        // Map: sequence => name (for checklist labels)
        $seqToName = $criteria->mapWithKeys(function ($c) {
            $seq = data_get($c, 'sequence');
            return $seq ? [$seq => data_get($c, 'name')] : [];
        });

        // Assignments -> collectors
        $assignments = collect($dg('assignments', []));
        $collectors = $assignments->map(fn($a) => data_get($a, 'user.name'))->filter()->unique()->values()->all();

        // Distinct departments (from API's departments array)
$departments = collect($dg('departments', []))->pluck('name')->unique()->values()->all();

// ---------- Checklist ----------
$checklist = collect($dg('checklistItems', []))
    ->map(function ($it) use ($seqToName) {
        $label = collect(data_get($it, 'required_items', []))
            ->map(fn($i) => $seqToName[$i] ?? "ข้อ {$i}")
            ->implode(', ');
        return [
            'label' => $label,
            'score' => (float) data_get($it, 'score', 0),
        ];
    })
    ->reject(fn($r) => ($r['label'] === '' || $r['label'] === '-') && $r['score'] <= 0)
    ->values();

// ---------- Variable & Formula ----------
$vf = (array) $dg('variable_formula', []);

// variables อนุญาตทั้งสตริง หรืออ็อบเจ็กต์ {variable_name, type, value}
$variablesVF = collect(data_get($vf, 'variables', []))
    ->map(function ($v) {
        $var = data_get($v, 'variable_name');
        $vtype = data_get($v, 'type');
        $value = data_get($v, 'value', null);

        return [
            'var' => trim((string) $var),
            'vtype' => trim((string) $vtype),
            'value' => $value,
        ];
    })
    // ->reject(fn($r) => $r['var'] === '' && $r['type'] === '' && is_null($r['value']))
    ->values();

// formulas อนุญาตทั้งสตริง หรืออ็อบเจ็กต์ {condition} / {expression}
$formulasVF = collect(data_get($vf, 'formulas', []))
    ->map(function ($f) {
        $raw = is_string($f) ? $f : data_get($f, 'condition') ?? data_get($f, 'expression', '');
        return trim((string) $raw);
    })
    ->filter()
    ->values();

// flags
$hasVFVars = $variablesVF->isNotEmpty();
$hasVFFx = $formulasVF->isNotEmpty();
$hasChecklist = $checklist->isNotEmpty();

// โชว์ตามโหมด (และซ่อนส่วนว่างอัตโนมัติ)
$showVFSection = $type === 'variable_formula' || ($type !== 'checklist' && ($hasVFVars || $hasVFFx));
$showChecklistSection = $type === 'checklist' || ($type !== 'variable_formula' && $hasChecklist);

// ---- Status mapping -> label + badge classes (shadcn-like) ----
$statusOptions = [
    0 => [
        'label' => 'รอดำเนินการ',
        'class' => 'bg-slate-100 text-slate-800 ring-slate-300',
        'dot' => 'bg-slate-500',
    ],
    1 => [
        'label' => 'บันทึกร่าง',
        'class' => 'bg-amber-100 text-amber-800 ring-amber-300',
        'dot' => 'bg-amber-600',
    ],
    2 => [
        'label' => 'บันทึกจริง',
        'class' => 'bg-blue-100 text-blue-800 ring-blue-300',
        'dot' => 'bg-blue-600',
    ],
    3 => [
        'label' => 'ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรฐาน',
        'class' => 'bg-emerald-100 text-emerald-800 ring-emerald-300',
        'dot' => 'bg-emerald-600',
    ],
    4 => [
        'label' => 'ผลการดำเนินงานไม่ครบถ้วนตามเกณฑ์มาตรฐาน',
        'class' => 'bg-rose-100 text-rose-800 ring-rose-300',
        'dot' => 'bg-rose-600',
    ],
];

$statusKey = is_numeric($status) ? (int) $status : null;
$opt = $statusKey !== null && array_key_exists($statusKey, $statusOptions) ? $statusOptions[$statusKey] : null;

$statusLabel = $opt['label'] ?? ($status !== null && $status !== '' ? (string) $status : '-');
$statusBadgeClass =
    'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset ' .
    ($opt['class'] ?? 'bg-slate-100 text-slate-700 ring-slate-300');
$statusDotClass = $opt['dot'] ?? 'bg-slate-500';
    @endphp

    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            {{-- Header --}}
            <div class="banner rounded-t-2xl border border-slate-200 p-5 ">
                <h1 class="text-2xl sm:text-3xl text-center font-bold">รายละเอียดตัวชี้วัด</h1>
            </div>
            <div class="mb-5 w-full px-4 sm:px-6 lg:px-8 py-6 bg-white rounded-b-2xl-2xl border border-slate-200 shadow-sm">
                <div class="space-y-6 sm:space-y-5">
                    {{-- Card 1: Basic --}}
                    <x-card number="1" title="ข้อมูลตัวชี้วัด">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <div class="text-sm text-slate-500">ปีการประเมิน</div>
                                <div class="font-medium text-slate-900">{{ $year ?: '-' }}</div>
                            </div>

                            <div>
                                <div class="text-sm text-slate-500">คะแนนตัวชี้วัด</div>
                                <div class="font-medium text-slate-900">
                                    {{ $maxScore !== null ? number_format((float) $maxScore, 2) : '-' }}
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <div class="text-sm text-slate-500">ชื่อตัวชี้วัด</div>
                                <div class="font-medium text-slate-900">{{ $name ?: '-' }}</div>
                            </div>

                            <div>
                                <div class="text-sm text-slate-500">รหัสตัวชี้วัด</div>
                                <div class="font-medium text-slate-900">{{ $code ?: '-' }}</div>
                            </div>

                            <div>
                                <div class="text-sm text-slate-500">มาตรฐานตัวชี้วัด</div>
                                <div class="font-medium text-slate-900">{{ $standardName }}</div>
                            </div>

                            <div>
                                <div class="text-sm text-slate-500">ด้านตัวชี้วัด</div>
                                <div class="font-medium text-slate-900">{{ $categoryName }}</div>
                            </div>

                            <div>
                                <div class="text-sm text-slate-500 ">ประเภทตัวชี้วัด</div>
                                <div class="font-medium text-slate-900">{{ $type ?: '-' }}</div>
                            </div>

                            <div>
                                <div class="text-sm text-slate-500">สถานะตัวชี้วัด</div>
                                <div class="mt-1">
                                    <x-status-badge :status="$status" size="sm" />
                                </div>
                            </div>

                            <div>
                                <div class="text-sm text-slate-500">วันสิ้นสุดการประเมิน</div>
                                <div class="font-medium text-slate-900">{{ $deadlineDisplay }}</div>
                            </div>
                        </div>
                    </x-card>

                    {{-- Card 2: Responsible --}}
                    <x-card number="2" title="ผู้รับผิดชอบ">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <div class="text-sm text-slate-500 mb-1">หน่วยงานที่รับผิดชอบ</div>
                                @if (count($departments))
                                    <ol class="list-decimal list-inside space-y-1 text-slate-900">
                                        @foreach ($departments as $department)
                                            <li>{{ $department }}</li>
                                        @endforeach
                                    </ol>
                                @else
                                    <div class="text-slate-400">-</div>
                                @endif
                            </div>
                            <div>
                                <div class="text-sm text-slate-500 mb-1">ผู้รับผิดชอบในการรวบรวมข้อมูล</div>
                                @if (count($collectors))
                                    <ol class="list-decimal list-inside space-y-1 text-slate-900">
                                        @foreach ($collectors as $collector)
                                            <li>{{ $collector }}</li>
                                        @endforeach
                                    </ol>
                                @else
                                    <div class="text-slate-400">-</div>
                                @endif
                            </div>
                        </div>
                    </x-card>


                    {{-- Card 3: Description (richtext) --}}
                    <x-card number="3" title="คำอธิบายตัวชี้วัด">
                        <x-richtext-content :html="$descHtml" empty="-" />
                    </x-card>

                    {{-- Card 4: Criteria --}}
                    <x-card number="4" title="เกณฑ์การพิจารณา">
                        @if (count($criteriaList))
                            <ol class="list-decimal pl-5 space-y-1 text-slate-800">
                                @foreach ($criteriaList as $text)
                                    <li>{{ $text }}</li>
                                @endforeach
                            </ol>
                        @else
                            <div class="text-slate-400">-</div>
                        @endif

                        <x-card-box title="วิธีการคำนวณ" icon="📋">
                            <x-richtext-content :html="$condHtml" empty="-" />
                        </x-card-box>
                    </x-card>

                    {{-- Card 5: Scoring (comment richtext + variable/formula + checklist rules) --}}
                    <x-card number="5" title="เกณฑ์การให้คะแนน" class="space-y-5">
                        {{-- คำอธิบาย --}}
                        <x-richtext-content :html="$comment" empty="ไม่มีคำอธิบายเกณฑ์" />

                        {{-- ตัวแปร/สูตร --}}
                        @if ($showVFSection && ($hasVFVars || $hasVFFx))
                            <div class="space-y-3">
                                <div class="text-sm font-medium text-slate-700">ตัวแปรและสูตรคำนวณ</div>

                                @if ($hasVFVars)
                                    <div class="overflow-x-auto">
                                        <table
                                            class="min-w-full text-sm text-slate-800 border border-slate-200 rounded-xl overflow-hidden">
                                            <thead class="bg-slate-50">
                                                <tr>
                                                    <th class="px-3 py-2 text-left font-semibold border-b border-slate-200">
                                                        ตัวแปร</th>
                                                    <th class="px-3 py-2 text-left font-semibold border-b border-slate-200">
                                                        ประเภท</th>
                                                    <th class="px-3 py-2 text-left font-semibold border-b border-slate-200">
                                                        ค่าเริ่มต้น</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($variablesVF as $v)
                                                    <tr class="odd:bg-white even:bg-slate-50">
                                                        <td class="px-3 py-2 font-medium">{{ $v['var'] ?: '-' }}</td>
                                                        <td class="px-3 py-2">{{ $v['vtype'] ?: '-' }}</td>
                                                        <td class="px-3 py-2">
                                                            {{ is_null($v['value']) ? '-' : (is_bool($v['value']) ? ($v['value'] ? 'true' : 'false') : $v['value']) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                                @if ($hasVFFx)
                                    <div>
                                        <div class="text-sm text-slate-500 mb-1">สูตร/เงื่อนไข</div>
                                        <div class="space-y-2">
                                            @foreach ($formulasVF as $fx)
                                                <pre class="whitespace-pre-wrap bg-slate-50 rounded px-2 py-1 border border-slate-200">{{ $fx }}</pre>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- เช็กลิสต์ --}}
                        @if ($showChecklistSection && $hasChecklist)
                            <div class="space-y-2">
                                <div class="text-sm font-medium text-slate-700">เกณฑ์ให้คะแนนแบบเช็กลิสต์</div>
                                <ul class="list-disc pl-5 space-y-1 text-slate-800">
                                    @foreach ($checklist as $r)
                                        <li>{{ $r['label'] }} = {{ number_format($r['score'], 2) }} คะแนน</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- ว่างทั้งสองฝั่ง --}}
                        @if (!($showVFSection && ($hasVFVars || $hasVFFx)) && !($showChecklistSection && $hasChecklist))
                            <div class="text-slate-400">-</div>
                        @endif
                    </x-card>

                    {{-- Card 7: Annotation/Note (richtext) --}}
                    <x-card number="6" title="หมายเหตุ">
                        <x-richtext-content :html="$annoHtml" empty="-" />
                    </x-card>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row justify-between gap-4 pt-2">
                        <a href="{{ route('indicator.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-200 text-gray-700 px-6 py-3 hover:bg-gray-300 text-sm md:text-base transition-colors order-2 sm:order-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span>กลับ</span>
                        </a>

                        <div class="flex flex-col sm:flex-row gap-3 order-1 sm:order-2">
                            <form action="{{ route('indicator.delete', $dg('id')) }}" method="POST"
                                onsubmit="return confirm('ต้องการลบตัวชี้วัดนี้ใช่หรือไม่?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 text-white px-6 py-3 hover:bg-rose-700 text-sm md:text-base transition-colors">
                                    ลบตัวชี้วัด
                                </button>
                            </form>

                            {{-- Enable when edit route is ready --}}
                            <a href="{{ route('indicator.edit', $indicatorId) }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 text-white px-6 py-3 hover:bg-amber-600 text-sm md:text-base transition-colors">
                                แก้ไข
                            </a>

                            <button type="button"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 text-white px-6 py-3 hover:bg-blue-700 text-sm md:text-base transition-colors">
                                แจ้งเตือนผู้รับผิดชอบ
                            </button>
                        </div>
                    </div>
                </div> {{-- /space-y --}}
            </div>
        </div>
    </div>
@endsection
