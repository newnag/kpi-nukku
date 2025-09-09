<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Department;
use App\Models\Evidence;
use App\Models\Indicator;
use App\Models\Standard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        // ====== 1) ปีทั้งหมดสำหรับ Filter ======
        $yearsForFilter = Indicator::query()
            ->whereHas('assignments')
            ->whereNotNull('year')
            ->selectRaw('DISTINCT CAST(year AS INTEGER) AS y')
            ->orderBy('y')
            ->pluck('y');   // [2020,2021,...] เป็น int (Collection)

        // ====== 2) Summary รวมคะแนนตามปี ======
        $yearlyTotals = Indicator::query()
            ->whereHas('assignments')
            ->whereNotNull('year')
            ->selectRaw('CAST(year AS INTEGER) as year, SUM(score_acc) AS total_score, SUM(max_score) AS max_score')
            ->groupBy('year')
            ->orderBy('year')
            ->get();
        // ====== X) นับจำนวนตัวชี้วัด แยกตามปี ======



        // ปีล่าสุด: ใช้จาก summary ถ้ามี ไม่งั้น fallback ไปที่ปีทั้งหมด
        $latestFromTotals = $yearlyTotals->max('year');
        $latestFromAll = $yearsForFilter->max();
        $latestYear = $latestFromTotals ?? $latestFromAll;

        // ====== 3) ปีที่จะแสดงผล ======
        $displayYear = $request->filled('year')
            ? (int) $request->input('year')
            : (int) $latestYear;
        $indicatorCount = Indicator::query()
            ->whereHas('assignments')
            ->whereNotNull('year')
            ->where('year', $displayYear)   // ✅ นับเฉพาะปีที่เลือก
            ->count();
        // หาข้อมูลคะแนนของปีที่เลือก
        $currentYearData = $displayYear !== null
            ? $yearlyTotals->firstWhere('year', (int) $displayYear)
            : null;

        $totalScore = (float) ($currentYearData->total_score ?? 0);
        $maxScore = (float) ($currentYearData->max_score ?? 0);
        $displayYearText = $displayYear !== null ? (string) $displayYear : 'ไม่มีข้อมูล';

        // ====== 4) Indicators สำหรับแสดงในตาราง (โหลดทั้งหมด ให้กรองที่ฝั่ง client)
        $indicators = Indicator::query()
            ->whereHas('assignments')
            ->with([
                'category:id,name,standard_id',
                'category.standard:id,name',
                'assignments.collectorUser' => fn($q) => $q->select('id', 'name', 'department_id'),
                'assignments.collectorUser.department:id,name',
                'evidences' => fn($q) => $q->select('evidence.id', 'evidence.criteria_id', 'evidence.name', 'evidence.created_at'),
            ])
            ->withCount(['criterias as criteria_count', 'evidences as evidence_count'])

            // 1) แยกตามปี
            ->orderBy('indicators.year', 'asc')   // ถ้าอยากปีล่าสุดก่อน → 'desc'

            // 2) จัดกลุ่ม prefix: NCS -> NCP -> NCO
            ->orderByRaw("
        CASE
            WHEN indicators.code LIKE 'NCS-%' THEN 1
            WHEN indicators.code LIKE 'NCP-%' THEN 2
            WHEN indicators.code LIKE 'NCO-%' THEN 3
            ELSE 99
        END
            ")

            // 3) เลขหลังขีด (เอาตัวท้ายสุด)
            ->orderByRaw("
              COALESCE(NULLIF(SPLIT_PART(indicators.code, '-', 2), ''), '0')::int ASC
                ")


            // กันกรณีเลขเท่ากัน → เรียง code เต็ม
            ->orderBy('indicators.code', 'asc')

            ->get()
            ->map(function ($indicator) {
                $criteriaCount = (int) ($indicator->criteria_count ?? 0);
                $evidenceCount = (int) ($indicator->evidence_count ?? 0);
                $indicator->status_doc = $evidenceCount === 0
                    ? 'รอดำเนินการ'
                    : ($evidenceCount < $criteriaCount ? 'ไม่ครบ' : 'ครบ');
                return $indicator;
            });


        // ====== 5) นับสถานะตัวชี้วัด (รวมทั้งหมด ให้ client เป็นคนกรอง)
        $indicatorsForStatus = Indicator::query()
            ->whereHas('assignments')
            ->whereNotNull('year')
            ->get();

        $statusCounts = [
            'complete' => $indicatorsForStatus->whereIn('status', 3)->count(),
            'incomplete' => $indicatorsForStatus->where('status', 4)->count(),
            'pending' => $indicatorsForStatus->where('status', 0)->count(),
        ];

        // ====== 6) Config ของ Chart/Legend ======
        $legendConfig = [
            [
                'key'   => 'complete',
                'label' => 'ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรการ',
                'color' => '#22c55e', // เขียวสด
            ],
            [
                'key'   => 'incomplete',
                'label' => 'ผลการดำเนินงานยังไม่ครบถ้วนตามเกณฑ์',
                'color' => '#facc15', // เหลือง
            ],
            [
                'key'   => 'pending',
                'label' => 'อยู่ระหว่างดำเนินการ',
                'color' => '#ef4444', // แดงสด
            ],
        ];


        // ====== 7) Dropdown filters อื่น ๆ ======
        $allStandards = Standard::orderBy('name')->get(['id', 'name']);
        $departments = Department::orderBy('name')->pluck('name');
        $collectors = User::query()
            ->whereIn('id', function ($q) {
                $q->select('collector')->from('assignments');
            })
            ->orderBy('name')
            ->pluck('name');
        $dimensionNames = Category::query()
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name');

        $standardCount = $allStandards->count();
        $departmentCount = $departments->count();
        $collectorCount = $collectors->count();
        $dimensionCount = $dimensionNames->count();

        $dimensionStats = Category::select('id', 'name', 'standard_id')
            ->with('standard:id,name')
            ->withCount([
                'indicators as total_items' => function ($q) {
                    $q->whereHas('assignments');
                }
            ])
            ->orderBy('name')
            ->get();


        $allYears = Indicator::query()
            ->whereHas('assignments')
            ->whereNotNull('year')
            ->selectRaw('DISTINCT CAST(year AS INTEGER) AS y')
            ->orderBy('y')
            ->pluck('y')
            ->toArray();

        $last5Years = array_slice($allYears, max(0, count($allYears) - 5));

        // เตรียมค่า default กันตัวแปรหาย
        $chart = ['labels' => [], 'datasets' => []]; // กราฟ 3 มาตรฐาน (ของเดิม)
        $chartYearXDimension = ['labels' => [], 'datasets' => []]; // กราฟปีละ 7 แท่ง (รวม 3 มาตรฐานต่อด้าน)

        if (!empty($last5Years)) {

            // ====== (A) กราฟ 3 มาตรฐาน (ถ้าคุณยังใช้ส่วนนี้ในหน้าเดิม) ======
            $standards = DB::table('standards')->select('id', 'name')->orderBy('id')->get();
            $threeStandardIds = $standards->pluck('id')->take(3)->values()->all();

            $rowsStd = Indicator::query()
                ->join('categories', 'categories.id', '=', 'indicators.categorie_id')
                ->join('standards', 'standards.id', '=', 'categories.standard_id')
                ->whereHas('assignments')
                ->whereNotNull('indicators.year')
                ->whereIn(DB::raw('CAST(indicators.year AS INTEGER)'), $last5Years)
                ->whereIn('standards.id', $threeStandardIds)
                ->selectRaw('
                CAST(indicators.year AS INTEGER) AS year,
                standards.id AS standard_id,
                SUM(indicators.score_acc) AS total_score
            ')
                ->groupByRaw('CAST(indicators.year AS INTEGER), standards.id')
                ->orderByRaw('CAST(indicators.year AS INTEGER)')
                ->get();

            $series = [];
            foreach ($threeStandardIds as $sid) {
                foreach ($last5Years as $y) {
                    $series[$sid][$y] = 0.0;
                }
            }
            foreach ($rowsStd as $r) {
                $series[$r->standard_id][$r->year] = (float) $r->total_score;
            }

            $paletteById = [
                $threeStandardIds[0] ?? 0 => ['label' => 'มาตราฐานโครงสร้าง', 'color' => '#8979FF'],
                $threeStandardIds[1] ?? 0 => ['label' => 'มาตรฐานกระบวนการ', 'color' => '#FF928A'],
                $threeStandardIds[2] ?? 0 => ['label' => 'มาตรฐานผลลัพธ์', 'color' => '#3CC3DF'],
            ];

            $chart = [
                'labels' => array_map(fn($y) => "ปี {$y}", $last5Years),
                'datasets' => [],
            ];
            foreach ($threeStandardIds as $sid) {
                if (!isset($paletteById[$sid]))
                    continue;
                $conf = $paletteById[$sid];
                $chart['datasets'][] = [
                    'label' => $conf['label'],
                    'data' => array_values(array_intersect_key($series[$sid], array_flip($last5Years))),
                    'backgroundColor' => $conf['color'],
                ];
            }


            // ====== (B) กราฟ "ปีละ 7 แท่ง": รวม 3 มาตรฐานต่อ "ด้าน" ในปีนั้น ======

            // 0) ล็อกชื่อ 7 ด้าน (เรียงตามที่อยากแสดง)
            $rowsDim = Indicator::query()
                ->join('categories', 'categories.id', '=', 'indicators.categorie_id')
                ->whereHas('assignments')
                ->whereNotNull('indicators.year')
                ->whereIn(DB::raw('CAST(indicators.year AS INTEGER)'), $last5Years)
                ->selectRaw('
        CAST(indicators.year AS INTEGER) AS year,
        categories.name AS dim_name,
        SUM(indicators.score_acc) AS total_score
     ')
                ->groupByRaw('CAST(indicators.year AS INTEGER), categories.name')
                ->orderByRaw('CAST(indicators.year AS INTEGER)')
                ->get();

            // 2) กำหนดลำดับชื่อ “7 ด้าน” ตามที่ต้องการแสดง (แก้ให้ตรงชื่อในฐานข้อมูลของคุณ)
            $dimOrder = [
                'ด้านองค์กรและการบริหารองค์กร',
                'ด้านบุคลากร',
                'ด้านการจัดการศึกษา',
                'ด้านการวิจัยและนวัตกรรมและผลผลิตทางวิชาการ',
                'ด้านการบริการวิชาการ/วิชาชีพแก่สังคม',
                'ด้านการทำนุบำรุงศิลปะและวัฒนธรรม',
                'ด้านนิสิตและนักศึกษา',
            ];

            // 3) สร้าง matrix [ด้าน][ปี] = คะแนน
            $matrix = [];
            foreach ($dimOrder as $d) {
                foreach ($last5Years as $y) {
                    $matrix[$d][$y] = 0.0;
                }
            }
            foreach ($rowsDim as $r) {
                if (isset($matrix[$r->dim_name][$r->year])) {
                    $matrix[$r->dim_name][$r->year] = (float) $r->total_score;
                }
            }

            // 4) สี 7 สีสำหรับ 7 ด้าน (ปรับได้)
            $dimColors = ['#6366F1', '#F59E0B', '#10B981', '#EF4444', '#3B82F6', '#A855F7', '#14B8A6'];

            // 5) โครงสำหรับ Chart.js: labels = ปี, datasets = 7 ด้าน
            $chartYearAsX = [
                'labels' => array_values($last5Years),  // เช่น [2020,2021,...]
                'datasets' => [],
            ];
            foreach ($dimOrder as $i => $name) {
                $chartYearAsX['datasets'][] = [
                    'label' => $name,
                    'data' => array_map(fn($y) => $matrix[$name][$y], $last5Years),
                    'backgroundColor' => $dimColors[$i % count($dimColors)],
                    'borderColor' => $dimColors[$i % count($dimColors)],
                    'borderWidth' => 1,
                    'barPercentage' => 0.75,
                    'categoryPercentage' => 0.8,
                ];
            }
        } // end if last5Years
        // ====== 9) DEBUG LOG ======
        // Log::info('==== DASHBOARD DEBUG ====');
        // Log::info('Selected Year param: ' . ($request->input('year') ?? 'NULL'));
        // Log::info('Latest Year: ' . ($latestYear ?? 'NULL'));
        // Log::info('Display Year (int): ' . ($displayYear ?? 'NULL'));
        // Log::info('Indicators Count (filtered): ' . $indicators->count());
        // Log::info('Total Score: ' . $totalScore . ' / Max: ' . $maxScore);
        // Log::info('Status Counts: ' . json_encode($statusCounts));
        // Log::info('==========================');

        // ====== 10) RETURN VIEW ======
        return view('dashboard.app', compact(
            'indicators',
            'totalScore',
            'maxScore',
            'displayYear',
            'displayYearText',
            'statusCounts',
            'legendConfig',
            'allStandards',
            'standardCount',
            'dimensionNames',
            'dimensionCount',
            'departments',
            'departmentCount',
            'collectors',
            'collectorCount',
            'dimensionStats',
            'yearlyTotals',
            'yearsForFilter',
            'last5Years',          // ถ้าจะใช้บน Blade
            'chart',               // กราฟ 3 มาตรฐาน (ของเดิม)
            'chartYearAsX',
            'indicatorCount' // ✅ ปีละ 7 แท่ง = รวม 3 มาตรฐานต่อด้าน

        ));
    }

    public function getData()
    {
        // -------- 0) รับปีจากหลายชื่อฟิลด์ เผื่อฟอร์มตั้งชื่อไม่ตรง --------
        $req = request();
        $pickedYearRaw = $req->input(
            'year',
            $req->input(
                'filter-year',
                $req->input('assessment_year', '')
            )
        );

        $pickedYear = null;
        if ($pickedYearRaw !== null && trim((string) $pickedYearRaw) !== '') {
            $s = trim((string) $pickedYearRaw);
            if (!in_array(strtolower($s), ['ทั้งหมด', 'all', '*', '0'], true)) {
                $pickedYear = (int) $s;
            }
        }

        // -------- 1) ช่วงปีที่ต้องแสดง (ดึงจาก DB จริง) --------
        if (is_null($pickedYear)) {
            $yearRange = Indicator::whereNotNull('year')
                ->distinct()
                ->orderBy('year')
                ->pluck('year')
                ->map(fn($y) => (int) $y)
                ->toArray();
        } else {
            $yearRange = [$pickedYear];
        }
        $yearRangeStr = array_map('strval', $yearRange);

        // -------- 2) รายชื่อมาตรฐาน --------
        $standards = Standard::select('id', 'name')->get();

        // -------- 3) ดึงข้อมูลตามช่วงปี --------
        $rows = Indicator::query()
            ->join('categories', 'categories.id', '=', 'indicators.categorie_id')
            ->join('standards', 'standards.id', '=', 'categories.standard_id')
            ->whereHas('assignments')
            ->whereNotNull('indicators.year')
            ->when(
                !is_null($pickedYear),
                fn($q) => $q->where('indicators.year', $pickedYear),
                fn($q) => $q->whereIn('indicators.year', $yearRange)
            )
            ->selectRaw('
        standards.id   as standard_id,
        standards.name as standard_name,
        categories.id  as category_id,
        categories.name as category_name,
        indicators.id  as indicator_id,
        indicators.name as indicator_name,
        indicators.code as indicator_code,
        indicators.type as indicator_type,
        indicators.year,
        SUM(indicators.score_acc) as total_score,
        SUM(indicators.max_score) as max_score
    ')
            ->groupBy(
                'standards.id',
                'standards.name',
                'categories.id',
                'categories.name',
                'indicators.id',
                'indicators.name',
                'indicators.code',
                'indicators.type',
                'indicators.year'
            )
            ->orderBy('standards.id')
            ->orderBy('indicators.code')
            ->orderBy('indicators.year')
            ->get();


        // -------- 4) ฟังก์ชัน normalize รหัส indicator --------
        $normalize = function ($c) {
            $c = strtoupper(trim((string) $c));
            $c = preg_replace('/\s+/', '', $c);
            if (preg_match('/^([A-Z]+)[\s_\-]?0*(\d+)$/', $c, $m))
                return $m[1] . '-' . (int) $m[2];
            return $c;
        };

        $prefixOrder = ['NCS' => 0, 'NCP' => 1, 'NCO' => 2];

        // -------- 5) รวมกราฟด้วย indicator_code --------
        $chartsByStandard = [];
        foreach ($rows as $r) {
            $sid = $r->standard_id;
            $code = $normalize($r->indicator_code);

            $chartsByStandard[$sid] ??= [
                'standard_id'   => $sid,
                'standard_name' => $r->standard_name,
                'indicators'    => [],
            ];
            $chartsByStandard[$sid]['indicators'][$code] ??= [
                'indicator_id'   => $r->indicator_id,
                'indicator_key'  => $code,
                'indicator_code' => $code,
                'indicator_name' => $r->indicator_name,
                'indicator_type' => $r->indicator_type,
                'category_id'    => $r->category_id,
                'category_name'  => $r->category_name,
                'years'          => [],
                'values'         => [],
                'max_values'     => [], // ✅ เพิ่มตรงนี้
            ];

            $cb = &$chartsByStandard[$sid]['indicators'][$code];
            $cb['years'][]      = (string) $r->year;
            $cb['values'][]     = (float) $r->total_score;
            $cb['max_values'][] = (float) $r->max_score; // ✅ เก็บ max

        }

        // -------- 6) บังคับ labels/data และ sort indicators --------
        foreach ($chartsByStandard as $sid => &$bucket) {
            foreach ($bucket['indicators'] as &$ind) {
                $mapScore = [];
                $mapMax   = [];
                foreach ($ind['years'] as $i => $y) {
                    $yy = (string) $y;
                    $mapScore[$yy] = ($mapScore[$yy] ?? 0) + (float) $ind['values'][$i];
                    $mapMax[$yy]   = ($mapMax[$yy] ?? 0) + (float) $ind['max_values'][$i];
                }
                $ind['years']      = array_values($yearRangeStr);
                $ind['values']     = array_map(fn($y) => (float) ($mapScore[$y] ?? 0), $yearRangeStr);
                $ind['max_values'] = array_map(fn($y) => (float) ($mapMax[$y] ?? 0), $yearRangeStr);
            }


            // ✅ sort indicators ตาม indicator_code
            usort($bucket['indicators'], function ($a, $b) use ($prefixOrder) {
                preg_match('/^([A-Z]+)-(\d+)$/', $a['indicator_code'], $ma);
                preg_match('/^([A-Z]+)-(\d+)$/', $b['indicator_code'], $mb);
                $pa = $ma[1] ?? $a['indicator_code'];
                $pb = $mb[1] ?? $b['indicator_code'];
                $ra = $prefixOrder[$pa] ?? 999;
                $rb = $prefixOrder[$pb] ?? 999;
                if ($ra !== $rb) return $ra <=> $rb;
                if ($pa !== $pb) return strcmp($pa, $pb);
                $na = isset($ma[2]) ? (int) $ma[2] : PHP_INT_MAX;
                $nb = isset($mb[2]) ? (int) $mb[2] : PHP_INT_MAX;
                return $na <=> $nb;
            });
        }
        unset($bucket);

        // -------- 7) Filters สำหรับดรอปดาวน์ --------
        $filterYears = Indicator::whereNotNull('year')
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->map(fn($y) => (string) $y)
            ->toArray();

        if (!is_null($pickedYear) && !in_array((string) $pickedYear, $filterYears, true)) {
            $filterYears[] = (string) $pickedYear;
            sort($filterYears);
        }

        $allStandards = Standard::query()
            ->select('id', 'name')->orderBy('name')->get()
            ->map(fn($s) => ['id' => $s->id, 'name' => trim($s->name)])->toArray();

        $allDimensions = Category::query()->whereNotNull('name')->pluck('name')
            ->map(fn($n) => trim($n))->filter()->unique()->sort()->values()->toArray();

        $allTypes = Indicator::query()->whereNotNull('type')->pluck('type')
            ->map(fn($t) => trim($t))->filter()->unique()->sort()->values()->toArray();

        $codesRaw = Indicator::query()->whereNotNull('code')->pluck('code')->toArray();
        $codesNormalized = array_map($normalize, $codesRaw);
        $codesUnique = array_values(array_unique(array_filter($codesNormalized)));
        usort($codesUnique, function ($a, $b) use ($prefixOrder) {
            preg_match('/^([A-Z]+)-(\d+)$/', $a, $ma);
            preg_match('/^([A-Z]+)-(\d+)$/', $b, $mb);
            $pa = $ma[1] ?? $a;
            $pb = $mb[1] ?? $b;
            $ra = $prefixOrder[$pa] ?? 999;
            $rb = $prefixOrder[$pb] ?? 999;
            if ($ra !== $rb) return $ra <=> $rb;
            if ($pa !== $pb) return strcmp($pa, $pb);
            $na = isset($ma[2]) ? (int) $ma[2] : PHP_INT_MAX;
            $nb = isset($mb[2]) ? (int) $mb[2] : PHP_INT_MAX;
            return $na <=> $nb;
        });

        $filters = [
            'years'      => $filterYears,
            'codes'      => $codesUnique,
            'standards'  => $allStandards,
            'dimensions' => $allDimensions,
            'types'      => $allTypes,
            'pickedYear' => $pickedYear,
            'range'      => ['start' => reset($yearRange), 'end' => end($yearRange)],
        ];
        $yearlyTotals = Indicator::query()
            ->whereHas('assignments')
            ->whereNotNull('year')
            ->selectRaw('CAST(year AS INTEGER) as year, SUM(score_acc) as total_score, SUM(max_score) as max_score')
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->map(fn($r) => [
                'year'  => (int) $r->year,
                'score' => (float) $r->total_score,
                'max'   => (float) $r->max_score,
            ])
            ->toArray();


        return view('dashboard.result', [
            'standards'        => $standards,
            'chartsByStandard' => $chartsByStandard,
            'filters'          => $filters,
            'yearlyTotals'     => $yearlyTotals,
        ]);
    }
}
