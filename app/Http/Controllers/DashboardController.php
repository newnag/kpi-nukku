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
        $year = $request->input('year');

        // ====== ดึง indicators ของตารางหลัก (ของเดิม) ======
        $indicators = Indicator::with([
            'category:id,name,standard_id',
            'category.standard:id,name',
            'assignments.collectorUser' => fn($q) => $q->select('id', 'name', 'department_id'),
            'assignments.collectorUser.department:id,name',
            'evidences' => fn($q) => $q->select('evidence.id', 'evidence.criteria_id', 'evidence.name', 'evidence.created_at'),
        ])
            ->withCount(['criterias as criteria_count', 'evidences as evidence_count'])
            ->whereHas('assignments')
            ->when($year, fn($q) => $q->where('year', $year))
            ->get()
            ->map(function ($indicator) {
                $criteriaCount = (int)($indicator->criteria_count ?? 0);
                $evidenceCount = (int)($indicator->evidence_count ?? 0);
                $indicator->status_doc = $evidenceCount === 0
                    ? 'รอดำเนินการ'
                    : ($evidenceCount < $criteriaCount ? 'ไม่ครบ' : 'ครบ');
                return $indicator;
            });

        // ====== คะแนน/สรุป (ของเดิม) ======
        $maxScore    = Category::sum('max_score');
        $totalScore  = $indicators->sum('score_acc');
        $displayYear = $year ?? 'ทั้งหมด';

        $statusCounts = [
            'complete'   => $indicators->whereIn('status', [2, 3])->count(), // <- แก้เป็น whereIn
            'incomplete' => $indicators->where('status', 1)->count(),
            'pending'    => $indicators->where('status', 0)->count(),
        ];

        $legendConfig = [
            ['key' => 'complete', 'label' => 'ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรฐาน', 'color' => '#22c55e'],
            ['key' => 'incomplete', 'label' => 'ผลการดำเนินงานยังไม่ครบถ้วนตามเกณฑ์', 'color' => '#f59e0b'],
            ['key' => 'pending', 'label' => 'อยู่ระหว่างดำเนินการ', 'color' => '#ef4444'],
        ];

        // ====== (ใหม่) รายการ “มาตรฐาน/ด้าน” แบบครบทุกค่าเสมอ ======
        $allStandards   = Standard::orderBy('name')->get(['id', 'name']);
        $departments = Department::orderBy('name')->pluck('name');                  // มาตรฐานทั้งหมด
        $dimensionNames = Category::query()
            ->select('name')          // หรือ ->selectRaw('DISTINCT TRIM(name) AS name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name');          // Collection ของชื่อด้านไม่ซ้ำ
        $dimensionCount = $dimensionNames->count();
        $departmentCount = $departments->count();
        $standardCount  = $allStandards->count();                                           // จำนวนมาตรฐาน


        // (ถ้าต้องการตัวเลขจำนวนรายการต่อ "ด้าน" โดยนับ 0 ได้ ใช้ withCount + เงื่อนไขเดียวกับตารางหลัก)
        $dimensionStats = Category::select('id', 'name', 'standard_id')
            ->with('standard:id,name')
            ->withCount(['indicators as total_items' => function ($q) use ($year) {
                $q->when($year, fn($qq) => $qq->where('year', $year))
                    ->whereHas('assignments');
            }])
            ->orderBy('name')
            ->get();

        return view('dashboard.app', compact(
            'indicators',
            'totalScore',
            'maxScore',
            'year',
            'displayYear',
            'statusCounts',
            'legendConfig',
            // ส่งของใหม่ไปให้ Blade
            'allStandards',
            'standardCount',
            'dimensionNames',
            'dimensionCount',
            'departments',
            'departmentCount',
        ));
    }


    public function getData()
    {
        // เอาเฉพาะหัวข้อมาตรฐานไว้ loop ส่วนหน้า
        $standards = Standard::select('id', 'name')->get();

        // รวมข้อมูลที่ต้องใช้กรอง: standard, category (ชื่อ/ID), indicator (code/type), year, total_score
        $rows = Indicator::query()
            ->join('categories', 'categories.id', '=', 'indicators.categorie_id')
            ->join('standards',  'standards.id',  '=', 'categories.standard_id')
            ->whereHas('assignments') // ต้องมี assignments
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
            SUM(indicators.score_acc) as total_score
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
            ->orderBy('indicator_name')
            ->orderBy('indicators.year')
            ->get();

        // จัดกลุ่ม: มาตรฐาน → [กราฟของแต่ละตัวชี้วัด]
        $chartsByStandard = [];
        $allYears = [];
        $allCodes = [];
        $allStandards = [];
        $allDimensions = []; // ใช้ชื่อ category เป็น "ด้าน"
        $allTypes = [];

        foreach ($rows as $r) {
            $sid = $r->standard_id;
            $iid = $r->indicator_id;

            if (!isset($chartsByStandard[$sid])) {
                $chartsByStandard[$sid] = [
                    'standard_id'   => $sid,
                    'standard_name' => $r->standard_name,
                    'indicators'    => []
                ];
                $allStandards[$sid] = $r->standard_name;
            }

            if (!isset($chartsByStandard[$sid]['indicators'][$iid])) {
                $chartsByStandard[$sid]['indicators'][$iid] = [
                    'indicator_id'   => $iid,
                    'indicator_name' => $r->indicator_name,
                    'indicator_code' => $r->indicator_code,
                    'indicator_type' => $r->indicator_type,
                    'category_id'    => $r->category_id,
                    'category_name'  => $r->category_name, // = dimension
                    'years'          => [],
                    'values'         => [],
                ];
            }

            $chartsByStandard[$sid]['indicators'][$iid]['years'][]  = (string) $r->year;
            $chartsByStandard[$sid]['indicators'][$iid]['values'][] = (float) $r->total_score;

            // เก็บ option สำหรับตัวกรอง
            if ($r->year) {
                $allYears[$r->year] = (string) $r->year;
            }
            if ($r->indicator_code) {
                $allCodes[$r->indicator_code] = $r->indicator_code;
            }
            if ($r->category_name) {
                $allDimensions[$r->category_name] = $r->category_name;
            }
            if ($r->indicator_type) {
                $allTypes[$r->indicator_type] = $r->indicator_type;
            }
        }

        // ทำให้ indicators เป็น array ธรรมดา
        foreach ($chartsByStandard as $sid => $bucket) {
            $chartsByStandard[$sid]['indicators'] = array_values($bucket['indicators']);
        }

        // เตรียม option list
        $filters = [
            'years'     => array_values($allYears),
            'codes'     => array_values($allCodes),
            'standards' => array_map(fn($id, $name) => ['id' => $id, 'name' => $name], array_keys($allStandards), $allStandards),
            'dimensions' => array_values($allDimensions),
            'types'     => array_values($allTypes),
        ];

        // ถ้าขอ JSON (เช่น fetch/Ajax) ก็ส่ง JSON
        if (request()->wantsJson()) {
            return response()->json([
                'chartsByStandard' => $chartsByStandard,
                'filters'          => $filters,
            ]);
        }

        // ส่งให้ Blade
        return view('dashboard.result', [
            'standards'         => $standards,
            'chartsByStandard'  => $chartsByStandard,
            'filters'           => $filters,
        ]);
    }
}
