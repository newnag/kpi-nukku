<?php

namespace App\Http\Controllers;

use App\Models\Assignment;

use App\Models\Indicator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardKpiUserController extends Controller
{
    public function index(Request $request)
    {
        // สมมติว่าลองใช้ id ปลอมก่อน (เวลาใช้จริงเปลี่ยนเป็น Auth::id())
        $userId = 1;

        $indicators = Indicator::query()
            ->whereHas('assignments', fn($q) => $q->where('collector', $userId))
            ->with([
                'category:id,name,standard_id',
                'category.standard:id,name',
                'assignments' => fn($q) => $q->where('collector', $userId)
                    ->with(['collectorUser:id,name,department_id', 'collectorUser.department:id,name']),
                'criterias:id,indicator_id,status'
            ])
            ->get()
            ->map(function ($indicator) {
                $totalCriteria = $indicator->criterias->count();
                $completed = $indicator->criterias->where('status', 1)->count(); // สมมติว่า 1 = ผ่าน/ครบ

                if ($totalCriteria === 0) {
                    $indicator->doc_status = 'รอดำเนินการ';
                } elseif ($completed < $totalCriteria) {
                    $indicator->doc_status = 'ไม่ครบ';
                } else {
                    $indicator->doc_status = 'ครบ';
                }

                $indicator->status_key = match ((int) $indicator->status) {
                    2, 3 => 'complete',
                    4 => 'incomplete',
                    0 => 'pending',
                    default => 'pending',
                };
                return $indicator;
            });
        return view('kpi_dashboard_assigned.app', compact('indicators'));
    }



    public function show($id)
    {
        $indicator = Indicator::with([
            'category.standard',
            'assignments.collectorUser.department', // 👈 เพิ่มบรรทัดนี้

            'criterias.evidences.user.department'
        ])->findOrFail($id);

        // return response()->json([
        //     'success'   => true,
        //     'indicator' => $indicator
        // ]);
        return view('kpi_dashboard_assigned.show', compact('indicator'));
    }
}
