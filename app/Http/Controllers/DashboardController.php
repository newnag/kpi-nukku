<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Evidence;
use App\Models\Indicator;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $year = $request->input('year');

        // ดึง Indicators เฉพาะปี (ถ้ามี year ส่งมา)
        $indicators = Indicator::with([
            'category:id,name,standard_id',
            'category.standard:id,name',
            'assignments.collectorUser' => function ($q) {
                $q->select('id', 'name', 'department_id');
            },
            'assignments.collectorUser.department:id,name',
            'evidences' => function ($q) {
                $q->select('evidence.id', 'evidence.criteria_id', 'evidence.name', 'evidence.created_at');
            },
        ])
            ->withCount([
                'criterias as criteria_count',
                'evidences as evidence_count',
            ])
            ->whereHas('assignments')
            ->when($year, function ($q) use ($year) {
                $q->where('year', $year);
            })
            ->get()
            ->map(function ($indicator) {
                $criteriaCount = (int) ($indicator->criteria_count ?? 0);
                $evidenceCount = (int) ($indicator->evidence_count ?? 0);

                $indicator->status_doc = $evidenceCount === 0
                    ? 'รอดำเนินการ'
                    : ($evidenceCount < $criteriaCount ? 'ไม่ครบ' : 'ครบ');

                return $indicator;
            });

        // ✅ คะแนนเต็ม fix จาก categories
        $maxScore = Category::sum('max_score');

        // ✅ คะแนนที่ได้รวมจาก indicators.score_acc
        $totalScore = $indicators->sum('score_acc');  // ใช้ collection ที่ดึงมาแล้ว จะได้ไม่ต้อง query DB ซ้ำ
        $displayYear = $year ?? 'ทั้งหมด';
        return view('dashboard.app', compact('indicators', 'totalScore', 'maxScore', 'year', 'displayYear'));
    }
}
