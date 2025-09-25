<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>SAR Report {{ $report->year }}</title>
    <style>
        /* Use a Unicode font that DomPDF supports; Sarabun if available */
        body {
            font-family: "TH Sarabun New", "Sarabun", DejaVu Sans, sans-serif;
            font-size: 16px;
            line-height: 1.5;
        }
        h2 { text-align: center; margin: 0 0 12px 0; }
        h3 { font-size: 18px; margin: 18px 0 8px 0; }
        h4 { font-size: 16px; margin: 12px 0 6px 0; }
        h5 { font-size: 15px; margin: 10px 0 6px 0; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0 16px 0; }
        th, td { border: 1px solid #000; padding: 6px; vertical-align: top; }
        th { background: #f0f0f0; text-align: center; }
        .score { text-align: center; font-weight: bold; }
        .muted { color: #666; }
        ul { margin: 0; padding-left: 18px; }
        p { margin: 0 0 8px 0; }
    </style>
    @php
        // Build a safe collection of indicators from either the many-to-many or the single relation
        $indicatorCollection = collect(
            ($report && method_exists($report, 'relationLoaded') && $report->relationLoaded('indicators') && $report->indicators && $report->indicators->isNotEmpty())
                ? $report->indicators
                : (($report && isset($report->indicator) && $report->indicator) ? [$report->indicator] : [])
        );
    @endphp
    </head>
<body>
    <h2>รายงานการประเมินตนเอง (SAR) ปี {{ $report->year }}</h2>

    <h3>ส่วนที่ 1: ข้อมูลทั่วไปตามมาตรฐาน</h3>
    <div>{!! $report->section1 ?? '' !!}</div>

    <h3>ส่วนที่ 2: ข้อมูลด้านคุณภาพ</h3>
    <div>{!! $report->section2 ?? '' !!}</div>

    <h3>ส่วนที่ 3: ผลการดำเนินงานตามตัวบ่งชี้</h3>
    @foreach ($indicatorCollection->groupBy(fn($ind) => optional(optional($ind->category)->standard)->name ?? 'ไม่ระบุมาตรฐาน') as $stdName => $indsByStd)
        <h4>มาตรฐาน: {{ $stdName }}</h4>
        @foreach ($indsByStd->groupBy('category.name') as $catName => $inds)
            <h5>หมวด: {{ $catName }}</h5>

            @foreach ($inds as $ind)
                <p><strong>[{{ $ind->code }}] {{ $ind->name }}</strong></p>

                <table>
                    <thead>
                        <tr>
                            <th style="width:40px">ข้อ</th>
                            <th>ตัวชี้วัด/เกณฑ์</th>
                            <th style="width:80px">ผ่านเกณฑ์</th>
                            <th style="width:220px">รายงานผลการดำเนินงาน</th>
                            <th style="width:150px">หลักฐาน</th>
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
                            <tr><td colspan="5" class="score muted">ไม่มีข้อมูล</td></tr>
                        @endforelse
                    </tbody>
                </table>

                @php
                    // Extract summary/comment lines and self score
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
                            <th>สรุปผล/ข้อค้นพบสำคัญ</th>
                            <th style="width:100px">คะแนน</th>
                            <th style="width:120px">ตรวจสอบ</th>
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
                                <td class="score">{{ $scoreFromLine ? ($scoreFromLine . ' คะแนน') : '...... คะแนน' }}</td>
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

    <h3>ส่วนที่ 4: สรุปภาพรวม</h3>
    <div>{!! $report->section4 ?? '' !!}</div>

</body>
</html>

