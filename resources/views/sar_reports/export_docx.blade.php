<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>SAR Report {{ $report->year }}</title>
    <style>
        body {
            font-family: "TH Sarabun New", "Sarabun", sans-serif;
            font-size: 16pt;
            line-height: 1.4;
        }

        h2, h3, h4, h5 {
            margin: 12px 0 6px 0;
            font-weight: bold;
        }

        h2 { text-align: center; font-size: 20pt; }
        h3 { font-size: 18pt; border-bottom: 1px solid #000; padding-bottom: 4px; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background: #f0f0f0;
            text-align: center;
        }

        .criteria-preview {
            font-size: 14pt;
            color: #444;
        }

        .score {
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h2>รายงานการประเมินตนเอง (SAR) ปี {{ $report->year }}</h2>

    {{-- ส่วนที่ 1 --}}
    <h3>ส่วนที่ 1: ข้อมูลทั่วไปคณะพยาบาลศาสตร์</h3>
    <div>{!! $report->section1 !!}</div>

    {{-- ส่วนที่ 2 --}}
    <h3>ส่วนที่ 2: ข้อมูลด้านคุณภาพ</h3>
    <div>{!! $report->section2 !!}</div>

    {{-- ส่วนที่ 3 --}}
    <h3>ส่วนที่ 3: การประเมินตนเองตามตัวบ่งชี้</h3>

    @php
        // กำหนด indicators collection (รองรับทั้ง singular และ many)
        $indicatorCollection = collect(
            ($report && method_exists($report, 'relationLoaded') && $report->relationLoaded('indicators') && $report->indicators && $report->indicators->isNotEmpty())
                ? $report->indicators
                : (($report && isset($report->indicator) && $report->indicator) ? [$report->indicator] : [])
        );
    @endphp

    @foreach ($indicatorCollection->groupBy(fn($ind) => optional(optional($ind->category)->standard)->name ?? 'ไม่ระบุมาตรฐาน') as $stdName => $indsByStd)
        <h4>มาตรฐาน: {{ $stdName }}</h4>

        @foreach ($indsByStd->groupBy('category.name') as $catName => $inds)
            <h5>ด้าน: {{ $catName }}</h5>

            @foreach ($inds as $ind)
                <p><strong>[{{ $ind->code }}] {{ $ind->name }}</strong></p>

                {{-- ตารางเกณฑ์ --}}
                <table>
                    <thead>
                        <tr>
                            <th style="width:40px">ข้อ</th>
                            <th>เกณฑ์มาตรฐาน</th>
                            <th style="width:80px">ผลการดำเนินงาน</th>
                            <th style="width:200px">รายงานผลการดำเนินงาน</th>
                            <th style="width:150px">เอกสารหลักฐาน</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ind->criterias as $i => $cri)
                            <tr>
                                <td class="score">{{ $i + 1 }}</td>
                                <td>{{ $cri->name }}</td>
                                <td class="score">{{ $cri->status ? '✓' : '-' }}</td>
                                <td>{!! $cri->report ?? '-' !!}</td>
                                <td>
                                    @if ($cri->evidences->isNotEmpty())
                                        <ul>
                                            @foreach ($cri->evidences as $ev)
                                                <li>{{ $ev->name }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="score">ไม่มีข้อมูล</td></tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- ตารางคะแนน --}}
                @php
                    $lines = [];
                    if (!empty($ind->comment)) {
                        $plain = preg_replace('/<\/(p|div|li|br)>/i', "\n", $ind->comment);
                        $plain = strip_tags($plain);
                        $plain = html_entity_decode($plain, ENT_QUOTES, 'UTF-8');
                        $lines = preg_split('/\r\n|\r|\n/', $plain);
                        $lines = array_filter(array_map('trim', $lines));
                    }
                    $score = $ind->self_score ?? ($ind->score_acc ?? null);
                @endphp

                <table>
                    <thead>
                        <tr>
                            <th>เกณฑ์การให้คะแนน</th>
                            <th style="width:100px">คะแนน</th>
                            <th style="width:120px">การประเมินตนเอง</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lines as $line)
                            @php
                                $scoreFromLine = null;
                                if (preg_match('/([0-9]+(?:\.[0-9]+)?)\s*คะแนน/u', $line, $mm)) {
                                    $scoreFromLine = (float) $mm[1];
                                }
                                $match = $score !== null && $scoreFromLine !== null && abs($scoreFromLine - (float) $score) < 0.001;
                            @endphp
                            <tr>
                                <td>{{ $line }}</td>
                                <td class="score">{{ $scoreFromLine ? $scoreFromLine . ' คะแนน' : '...... คะแนน' }}</td>
                                <td class="score">{{ $match ? '✓' : '' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td>............................</td>
                                <td class="score">........... คะแนน</td>
                                <td></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endforeach
        @endforeach
    @endforeach

    {{-- ส่วนที่ 4 --}}
    <h3>ส่วนที่ 4: อื่นๆ</h3>
    <div>{!! $report->section4 !!}</div>

</body>
</html>
