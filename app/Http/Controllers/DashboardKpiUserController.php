<?php

namespace App\Http\Controllers;

use App\Models\Assignment;

use App\Models\Indicator;
use App\Models\Variable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardKpiUserController extends Controller
{
    public function index(Request $request)
    {
        $user   = Auth::user();
        $userId = $user->id;

        $query = Indicator::query();

        // ✅ ใช้ Spatie เช็ค role
        if ($user->hasRole('user')) {
            $query->whereHas('assignments', fn($q) => $q->where('collector', $userId));
        }

        $indicators = $query
            ->with([
                'category:id,name,standard_id',
                'category.standard:id,name',
                'assignments' => function ($q) use ($user) {
                    if ($user->hasRole('user')) {
                        $q->where('collector', $user->id);
                    }
                    $q->with(['collectorUser:id,name,department_id', 'collectorUser.department:id,name']);
                },
                'criterias:id,indicator_id,status'
            ])
            ->orderByRaw("
            CASE LEFT(code, 3)
                WHEN 'NCS' THEN 1
                WHEN 'NCO' THEN 2
                WHEN 'NCP' THEN 3
                ELSE 4
            END
        ")
            ->orderByRaw("
            CASE 
                WHEN split_part(code, '-', 2) ~ '^[0-9]+$' 
                THEN CAST(split_part(code, '-', 2) AS INTEGER)
                ELSE 999999
            END
        ")
            ->get()
            ->map(function ($indicator) {
                $totalCriteria = $indicator->criterias->count();
                $completed     = $indicator->criterias->where('status', 1)->count();

                if ($totalCriteria === 0) {
                    $indicator->doc_status = 'รอดำเนินการ';
                } elseif ($completed < $totalCriteria) {
                    $indicator->doc_status = 'ไม่ครบ';
                } else {
                    $indicator->doc_status = 'ครบ';
                }

                $indicator->status_key = match ((int) $indicator->status) {
                    2, 3 => 'complete',
                    4    => 'incomplete',
                    0    => 'pending',
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
            'assignments.collectorUser.department',
            'criterias.evidences.user.department',
            'variables',
            'formulas.variables',
            'checklistItems',
        ])->findOrFail($id);

        $criteria_id = optional($indicator->criterias->first())->id;

        return view('kpi_dashboard_assigned.show', compact('indicator', 'criteria_id'));
    }
    public function saveVariables(Request $request, $id)
    {
        $indicator = Indicator::findOrFail($id);

        if ($request->has('variables')) {
            foreach ($request->variables as $varId => $value) {
                Variable::updateOrCreate(
                    ['id' => $varId, 'indicator_id' => $indicator->id],
                    ['value' => $value]
                );
            }
        }

        if ($request->has('status')) {
            $indicator->status = $request->status;
        }

        $indicator->save();

        // $userId = Auth::user();

        return redirect()->route('dashboardkpi.user.show', $indicator->id)
                ->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');

        // if ($userId->hasRole('user')) {
        //     // ส่งข้อมูลกลับไปยังหน้าแสดงผล
        //     return redirect()->route('dashboardkpi.user.show', $indicator->id)
        //         ->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
        // } else {
        //     // สำหรับผู้ดูแลระบบหรือบทบาทอื่น ๆ
        //     return redirect()->route('dashboardkpi.admin.show', $indicator->id)
        //         ->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
        // }
    }


    // public function saveVariables(Request $request, $id)
    // {
    //     $indicator = Indicator::findOrFail($id);

    //     if ($request->has('variables')) {
    //         foreach ($request->variables as $varId => $value) {
    //             Variable::updateOrCreate(
    //                 ['id' => $varId, 'indicator_id' => $indicator->id],
    //                 ['value' => $value]
    //             );
    //         }
    //     }

    //     // ✅ update status
    //     if ($request->has('status')) {
    //         $indicator->status = $request->status;
    //     }

    //     $indicator->save();
    //     // return response()->json([
    //     //     'success' => true,
    //     //     'message' => $request->status == 1
    //     //         ? 'บันทึกฉบับร่างเรียบร้อย ✅'
    //     //         : 'บันทึกจริงสำเร็จ 🚀',
    //     // ]);

    //     return redirect()
    //         ->route('dashboardKpiUser.index')
    //         ->with('success', $request->status == 1
    //             ? 'บันทึกฉบับร่างเรียบร้อย '
    //             : 'บันทึกจริงสำเร็จ ');
    // }
}
