<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Department;
use App\Models\Evidence;
use App\Models\Indicator;
use App\Models\Standard;
use App\Models\User;
use Illuminate\Http\Request;

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
}
