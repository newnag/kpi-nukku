<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Indicator;
use App\Models\Variable;
use App\Models\Criteria;
use App\Models\Evidence;
use Illuminate\Database\Eloquent\Casts\Json;
use Laravel\Pail\ValueObjects\Origin\Console;

class DashboardKpiAdminController extends Controller
{

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

        return view('kpi_dashboard_assigned.vrf_show', compact('indicator', 'criteria_id'));

        // return response()->json([
        //     'indicator'  => $indicator,
        //     'criteria_id' => $criteria_id,
        // ]);
    }

    public function saveVariables(Request $request, $id)
    {
        $indicator = Indicator::findOrFail($id);

        // variables
        if ($request->has('variables')) {
            foreach ($request->variables as $varId => $value) {
                Variable::updateOrCreate(
                    ['id' => $varId, 'indicator_id' => $indicator->id],
                    ['value' => $value]
                );
            }
        }

        // criterias
        if ($request->has('criterias')) {
            foreach ($request->criterias as $criteriaId => $criteriaData) {
                if (isset($criteriaData['status'])) {
                    Criteria::where('id', $criteriaId)->update(['status' => $criteriaData['status']]);
                }
            }
        }

        // evidences
        if ($request->has('evidences')) {
            foreach ($request->evidences as $evidenceId => $evidenceData) {
                if (isset($evidenceData['status'])) {
                    Evidence::where('id', $evidenceId)->update([
                        'status' => $evidenceData['status'] === 'true'
                    ]);
                }
            }
        }

        if ($request->has('status')) {
            $indicator->status = $request->status;
        }

        $indicator->save();

        // ✅ ส่ง JSON กลับไป
        return redirect()->route('dashboardkpi.admin.show', $indicator->id)
            ->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');

        // return response()->json($request->all());
    }

    public function updateStatus(Request $request, $id)
    {
        $indicator = Indicator::findOrFail($id);

        if ($request->has('status')) {
            $indicator->status = $request->status;
            $indicator->save();

            return redirect()->route('dashboardkpi.admin.show', $indicator->id)
                ->with('success', 'เปลี่ยนสถานะตัวชี้วัดเรียบร้อยแล้ว');
        }

        return redirect()->route('dashboardkpi.admin.show', $indicator->id)
            ->with('error', 'ไม่พบสถานะที่ต้องการเปลี่ยนแปลง');
    }
}
