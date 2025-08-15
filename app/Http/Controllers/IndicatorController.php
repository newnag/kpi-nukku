<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Indicator as IndicatorModel;
use App\Models\User;

class IndicatorController extends Controller
{
    public function index()
    {
        $indicators = IndicatorModel::with([
            'category.standard',
            'assignments.collector.department',
            'evidences'
        ])->get()->map(function ($indicator) {
            // Add custom properties or modify existing ones
            // $indicator->custom_property = 'Custom Value';
            return [
                'id' => $indicator->id,
                'name' => $indicator->name,
                'year' => $indicator->year,
                'code' => $indicator->code,
                // 'description' => $indicator->description,
                // 'condition' => $indicator->condition,
                // 'annotation' => $indicator->annotation,
                'deadline' => $indicator->deadline ? $indicator->deadline->format('Y-m-d') : null,
                'status' => $indicator->status,
                // 'comment' => $indicator->comment,
                'score_acc' => $indicator->score_acc,
                'max_score' => $indicator->max_score,
                'type' => $indicator->type,
                // 'category_id' => $indicator->categorie_id,
                'category' => $indicator->category ? [
                    'id' => $indicator->category->id,
                    'name' => $indicator->category->name,
                ] : null,
                'standard' => $indicator->category->standard ? [
                    'id' => $indicator->category->standard->id,
                    'name' => $indicator->category->standard->name,
                ] : null,
                'assignments' => $indicator->assignments->map(function ($assignment) {
                    $user = User::find($assignment->collector);
                    return [
                        'user' => $user ? [
                            'id' => $user->id,
                            'name' => $user->name,
                            // 'employee_id' => $user->employee_id,
                            // 'phone' => $user->phone,
                            // 'email' => $user->email,
                            'department_id' => $user->department_id,
                            'department_name' => $user->department ? $user->department->name : null,
                        ] : null,
                    ];
                }),
                'evidences' => $indicator->evidences->map(function ($evidence) {
                    return [
                        'id' => $evidence->id,
                        'name' => $evidence->name,
                        'file_path' => $evidence->file_path,
                        'created_at' => $evidence->created_at ? $evidence->created_at->format('Y-m-d H:i:s') : null,
                    ];
                }),
            ];
        });


        return view('indicator.dashboard', compact('indicators'));

        // return response()->json([
        //     'indicators' => $indicators,
        // ]);
    }

    public function show($id)
    {
        $indicator = IndicatorModel::with([
            'category.standard',
            'assignments.collector.department',
            'evidences'
        ])->findOrFail($id);

        return response()->json([
            'indicator' => $indicator,
        ]);
    }

    public function create()
    {
        
        return view('indicator.create');
    }
}
