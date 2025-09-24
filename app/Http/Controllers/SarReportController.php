<?php

namespace App\Http\Controllers;

use App\Models\SarReport;
use App\Models\Standard;
use App\Models\Indicator;
use App\Models\Criteria;
use App\Models\Evidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SarReportController extends Controller
{
    public function index()
    {
        $reports = SarReport::with(['standard', 'indicator', 'criteria'])->paginate(10);
         $years = SarReport::selectRaw('DISTINCT year')->orderBy('year', 'desc')->pluck('year');
        return view('sar_reports.index', compact('reports'  , 'years'));
    }
    public function part3()
    {
        $standards = \App\Models\Indicator::with([
            'category.standard',   // ดึง category + standard
            'criterias.evidences'  // ดึง criteria + evidences
        ])->get()
            ->groupBy(fn($ind) => $ind->category->standard->name ?? 'ไม่ทราบมาตรฐาน');

        return view('sar_reports.part3', compact('standards'));
    }

    public function create()
    {
        // Build data structure expected by the view: indicators grouped by standard name
        $standards = Indicator::with([
            'category.standard',
            'criterias' => function ($q) {
                $q->orderBy('sequence');
            },
            'criterias.evidences',
        ])
            // Order by code group: NCS -> NCO -> NCP -> others
            ->orderByRaw("CASE LEFT(code, 3) WHEN 'NCS' THEN 1 WHEN 'NCO' THEN 2 WHEN 'NCP' THEN 3 ELSE 4 END")
            // Then order by numeric part after dash, e.g. NCS-2, NCS-10
            ->orderByRaw("CASE WHEN split_part(code, '-', 2) ~ '^[0-9]+' THEN CAST(split_part(code, '-', 2) AS INTEGER) ELSE 999999 END")
            ->get()
            ->groupBy(function ($ind) {
                return optional(optional($ind->category)->standard)->name ?? 'ไม่ระบุมาตรฐาน';
            });

        return view('sar_reports.create', compact('standards'));
    }

    public function store(Request $request)
    {
        // Accept section fields from the form and optional meta fields
        $data = $request->validate([
            'section1' => 'nullable|string',
            'section2' => 'nullable|string',
            'section4' => 'nullable|string',
            'year' => 'nullable|integer',
            'standard_id' => 'nullable|exists:standards,id',
            'indicator_id' => 'nullable|exists:indicators,id',
            'criteria_id' => 'nullable|exists:criterias,id',
            'performance_result' => 'nullable|string',
            'performance_report' => 'nullable|string',
            'self_score' => 'nullable|numeric',
            'comment' => 'nullable|string',
        ]);

        // If required relational fields are missing, derive sensible defaults
        $indicatorId = $data['indicator_id'] ?? null;
        $criteriaId  = $data['criteria_id'] ?? null;
        $standardId  = $data['standard_id'] ?? null;

        if (!$indicatorId || !$criteriaId || !$standardId) {
            $indicator = Indicator::with(['category.standard', 'criterias' => function ($q) {
                $q->orderBy('sequence');
            }])
                ->orderBy('code')
                ->first();

            if (!$indicator || $indicator->criterias->isEmpty() || !$indicator->category || !$indicator->category->standard) {
                return back()->withInput()->withErrors([
                    'general' => 'ไม่พบตัวชี้วัด/เกณฑ์/มาตรฐานสำหรับบันทึกข้อมูล',
                ]);
            }

            $indicatorId = $indicatorId ?: $indicator->id;
            $criteriaId  = $criteriaId  ?: $indicator->criterias->first()->id;
            $standardId  = $standardId  ?: $indicator->category->standard->id;
        }

        $year = $data['year'] ?? null;
        if (!$year) {
            // Use indicator year if available, otherwise current year
            $year = optional(Indicator::find($indicatorId))->year ?? (int) now()->format('Y');
        }

        $payload = array_merge($data, [
            'year' => $year,
            'standard_id' => $standardId,
            'indicator_id' => $indicatorId,
            'criteria_id' => $criteriaId,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        $report = SarReport::create($payload);

        if ($request->filled('evidence_ids')) {
            $report->evidences()->sync($request->input('evidence_ids'));
        }

        return redirect()->route('sar_reports.index')->with('success', 'บันทึก SAR เรียบร้อยแล้ว');
    }
    public function edit($id)
    {
        $report = SarReport::findOrFail($id);

        $standards = Indicator::with([
            'category.standard',
            'criterias' => function ($q) {
                $q->orderBy('sequence');
            },
            'criterias.evidences',
        ])
            ->where('year', $report->year)
            ->orderByRaw("CASE LEFT(code, 3) WHEN 'NCS' THEN 1 WHEN 'NCO' THEN 2 WHEN 'NCP' THEN 3 ELSE 4 END")
            ->orderByRaw("CASE WHEN split_part(code, '-', 2) ~ '^[0-9]+' THEN CAST(split_part(code, '-', 2) AS INTEGER) ELSE 999999 END")
            ->get()
            ->groupBy(function ($ind) {
                return optional(optional($ind->category)->standard)->name ?? '�1,�,��1^�,��,��,s�,,�,��,��,\u0007�,��,?�,��,T';
            });

        return view('sar_reports.edit', compact('report', 'standards'));
    }

    public function update($id, Request $request)
    {
        $report = SarReport::findOrFail($id);

        $data = $request->validate([
            'section1' => 'nullable|string',
            'section2' => 'nullable|string',
            'section4' => 'nullable|string',
            'year' => 'nullable|integer',
            'standard_id' => 'nullable|exists:standards,id',
            'indicator_id' => 'nullable|exists:indicators,id',
            'criteria_id' => 'nullable|exists:criterias,id',
            'performance_result' => 'nullable|string',
            'performance_report' => 'nullable|string',
            'self_score' => 'nullable|numeric',
            'comment' => 'nullable|string',
        ]);

        $payload = [
            'section1' => $data['section1'] ?? $report->section1,
            'section2' => $data['section2'] ?? $report->section2,
            'section4' => $data['section4'] ?? $report->section4,
            'year' => $data['year'] ?? $report->year,
            'standard_id' => $data['standard_id'] ?? $report->standard_id,
            'indicator_id' => $data['indicator_id'] ?? $report->indicator_id,
            'criteria_id' => $data['criteria_id'] ?? $report->criteria_id,
            'performance_result' => $data['performance_result'] ?? $report->performance_result,
            'performance_report' => $data['performance_report'] ?? $report->performance_report,
            'self_score' => $data['self_score'] ?? $report->self_score,
            'comment' => $data['comment'] ?? $report->comment,
            'updated_by' => Auth::id(),
        ];

        $report->update($payload);

        if ($request->filled('evidence_ids')) {
            $report->evidences()->sync($request->input('evidence_ids'));
        }

        return redirect()->route('sar_reports.index')->with('success', 'อัปเดตรายงาน SAR สำเร็จ');
    }
    public function updateReport($id, Request $request)
    {
        $request->validate([
            'report' => 'nullable|string',
        ]);

        $criteria = Criteria::findOrFail($id);
        $criteria->report = $request->input('report');
        $criteria->save();

        return response()->json([
            'success' => true,
            'message' => 'บันทึกสำเร็จ',
            'report'  => $criteria->report,
        ]);
    }
}
