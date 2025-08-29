<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Indicator as IndicatorModel;
use App\Models\User;
use App\Models\Standard;
use App\Models\Department;
use App\Models\Category;
use App\Models\Criteria;
use App\Models\Variable;
use App\Models\Formula;
use App\Models\Checklist_item;

class IndicatorController extends Controller
{
    /* ===========================
     * Views
     * =========================== */

    public function index()
    {
        $indicators = IndicatorModel::with([
            'category.standard',
            'assignments.collector.department',
            'evidences',
        ])->get()->map(fn($i) => $this->serializeIndicatorForList($i));

        return view('indicator.dashboard', compact('indicators'));
    }

    public function create()
    {
        $data = $this->formSelections();
        return view('indicator.create', $data);
    }

    /* ===========================
     * APIs
     * =========================== */

    public function show($id)
    {
        try {
            $indicator = IndicatorModel::with([
                'category.standard',
                'assignments',
                'criterias',
                'variables',
                'formulas',
                'checklistItems',
            ])->findOrFail($id);

            // return response()->json(['indicator' => $this->serializeIndicator($indicator)]);
            return response()->json(['indicator' => $indicator]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve indicator'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validation with detailed error handling
            $validated = $request->validate([
                // Basic
                'year'      => 'required|integer|digits:4',
                'name'         => 'required|string|max:255',
                'code'         => 'required|string|max:100',
                'max_score'    => 'required|numeric|min:0',
                'standard_id'  => 'required|exists:standards,id',
                'category_id'  => 'required|exists:categories,id',
                'type'         => 'nullable|string',
                'deadline'     => 'required|date',

                // Responsible
                'department_id' => 'required|exists:departments,id',
                'user_id'      => 'required|exists:users,id',

                // Rich text
                'description'  => 'nullable|string',
                'condition'    => 'nullable|string',
                'comment'      => 'nullable|string',
                'annotation'   => 'nullable|string',

                // Criteria
                'criteria'                 => 'nullable|array',
                'criteria.*.sequence'      => 'required|integer',
                'criteria.*.name'          => 'required|string|max:255',
                'criteria.*.description'   => 'nullable|string',

                // Multi-choice (count-based)
                'multiCounts'                 => 'nullable|array',
                'multiCounts.*.count'         => 'required|integer|min:0',
                'multiCounts.*.score'         => 'required|numeric',

                // Multi-choice (selected-based)
                'multiSelected'                  => 'nullable|array',
                'multiSelected.*.sequence'       => 'required|integer',
                'multiSelected.*.required_items' => 'nullable|array',
                'multiSelected.*.required_items.*' => 'integer',
                'multiSelected.*.score'          => 'required|numeric',

                // Custom scoring
                'scoring.variables'                  => 'nullable|array',
                'scoring.variables.*.variable_name' => 'required|string|max:100',
                'scoring.variables.*.type'           => 'required|in:defined,input,output',
                'scoring.variables.*.value'          => 'nullable|numeric',
                'scoring.condition'                  => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'error' => 'ข้อมูลไม่ถูกต้อง',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create indicator
            try {
                $indicator = IndicatorModel::create([
                    'year'         => $validated['year'],
                    'name'         => $validated['name'],
                    'code'         => $validated['code'],
                    'max_score'    => $validated['max_score'],
                    'score_acc'    => 0,
                    'categorie_id' => $validated['category_id'],
                    'type'         => $validated['type'] ?? null,
                    'deadline'     => $validated['deadline'],
                    'status'       => 2, // อยู่ระหว่างดำเนินการ
                    'description'  => $validated['description'] ?? null,
                    'condition'    => $validated['condition'] ?? null,
                    'comment'      => $validated['comment'] ?? null,
                    'annotation'   => $validated['annotation'] ?? null,
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                return response()->json([
                    'error' => 'เกิดข้อผิดพลาดในการสร้างตัวชี้วัด',
                    'message' => 'Failed to create indicator',
                    'details' => $e->getMessage()
                ], 500);
            }

            // Create assignment
            try {
                $indicator->assignments()->create([
                    'collector' => $validated['user_id'],
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'error' => 'เกิดข้อผิดพลาดในการกำหนดผู้รับผิดชอบ',
                    'message' => 'Failed to create assignment',
                    'details' => $e->getMessage()
                ], 500);
            }

            // Sync criteria with error handling
            try {
                $criteriaCount = $this->syncCriterias($indicator, $validated['criteria'] ?? []);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'error' => 'เกิดข้อผิดพลาดในการบันทึกเกณฑ์',
                    'message' => 'Failed to sync criteria',
                    'details' => $e->getMessage()
                ], 500);
            }

            // Sync variables and formula with error handling
            try {
                $this->syncVariablesAndFormula($indicator, $validated['scoring'] ?? []);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'error' => 'เกิดข้อผิดพลาดในการบันทึกตัวแปรและสูตร',
                    'message' => 'Failed to sync variables and formula',
                    'details' => $e->getMessage()
                ], 500);
            }

            // Sync checklist from selected with error handling
            try {
                $this->syncChecklistFromSelected($indicator, $validated['multiSelected'] ?? []);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'error' => 'เกิดข้อผิดพลาดในการบันทึกรายการตรวจสอบ (แบบเลือก)',
                    'message' => 'Failed to sync checklist from selected',
                    'details' => $e->getMessage()
                ], 500);
            }

            // Sync checklist from counts with error handling
            try {
                $this->syncChecklistFromCounts($indicator, $validated['multiCounts'] ?? [], $criteriaCount);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'error' => 'เกิดข้อผิดพลาดในการบันทึกรายการตรวจสอบ (แบบนับ)',
                    'message' => 'Failed to sync checklist from counts',
                    'details' => $e->getMessage()
                ], 500);
            }

            DB::commit();

            return response()->json([
                'message' => 'ตัวชี้วัดถูกสร้างเรียบร้อยแล้ว',
                'success' => 'Indicator created successfully',
                'indicator' => $indicator->load(['category', 'assignments'])
            ], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            // Handle specific database errors
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json([
                    'error' => 'ข้อมูลซ้ำ: รหัสตัวชี้วัดนี้มีอยู่แล้ว',
                    'message' => 'Duplicate entry error',
                    'details' => $e->getMessage()
                ], 409);
            }

            if (str_contains($e->getMessage(), 'Column not found')) {
                return response()->json([
                    'error' => 'โครงสร้างฐานข้อมูลไม่ถูกต้อง',
                    'message' => 'Database schema error',
                    'details' => $e->getMessage()
                ], 500);
            }

            return response()->json([
                'error' => 'เกิดข้อผิดพลาดในฐานข้อมูล',
                'message' => 'Database error',
                'details' => $e->getMessage()
            ], 500);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'เกิดข้อผิดพลาด',
                'message' => 'Unexpected error occurred',
                'details' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $validated = $request->validate([
                // Basic
                'year'      => 'required|integer|digits:4',
                'name'         => 'required|string|max:255',
                'code'         => 'required|string|max:100',
                'max_score'    => 'required|numeric|min:0',
                'standard_id'  => 'required|exists:standards,id',
                'category_id'  => 'required|exists:categories,id',
                'type'         => 'nullable|string',
                'deadline'     => 'required|date',

                // Responsible
                'department_id' => 'required|exists:departments,id',
                'user_id'      => 'required|exists:users,id',

                // Rich text
                'description'  => 'nullable|string',
                'condition'    => 'nullable|string',
                'comment'      => 'nullable|string',
                'annotation'   => 'nullable|string',

                // Criteria
                'criteria'                 => 'nullable|array',
                'criteria.*.sequence'      => 'required|integer',
                'criteria.*.name'          => 'required|string|max:255',
                'criteria.*.description'   => 'nullable|string',

                // Multi-choice
                'multiCounts'                 => 'nullable|array',
                'multiCounts.*.count'         => 'required|integer|min:0',
                'multiCounts.*.score'         => 'required|numeric',

                'multiSelected'                  => 'nullable|array',
                'multiSelected.*.sequence'       => 'required|integer',
                'multiSelected.*.required_items' => 'nullable|array',
                'multiSelected.*.required_items.*' => 'integer',
                'multiSelected.*.score'          => 'required|numeric',

                // Scoring
                'scoring.variables'                  => 'nullable|array',
                'scoring.variables.*.variable_name' => 'required|string|max:100',
                'scoring.variables.*.type'           => 'required|in:defined,input,output',
                'scoring.variables.*.value'          => 'nullable|numeric',
                'scoring.condition'                  => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'error' => 'ข้อมูลไม่ถูกต้อง',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $indicator = IndicatorModel::findOrFail($id);

            $indicator->update([
                'year'         => $validated['year'],
                'name'         => $validated['name'],
                'code'         => $validated['code'],
                'max_score'    => $validated['max_score'],
                'categorie_id' => $validated['category_id'],
                'type'         => $validated['type'] ?? null,
                'deadline'     => $validated['deadline'],
                'description'  => $validated['description'] ?? null,
                'condition'    => $validated['condition'] ?? null,
                'comment'      => $validated['comment'] ?? null,
                'annotation'   => $validated['annotation'] ?? null,
                // keep score_acc as-is, or recompute somewhere else
            ]);

            // Update assignment (collector only)
            $indicator->assignments()->delete();
            $indicator->assignments()->create([
                'collector' => $validated['user_id'],
            ]);

            // Replace related blocks
            $indicator->criterias()->delete();
            $criteriaCount = $this->syncCriterias($indicator, $validated['criteria'] ?? []);

            $this->syncVariablesAndFormula($indicator, $validated['scoring'] ?? []);

            $indicator->checklistItems()->delete();
            $this->syncChecklistFromSelected($indicator, $validated['multiSelected'] ?? []);
            $this->syncChecklistFromCounts($indicator, $validated['multiCounts'] ?? [], $criteriaCount);

            DB::commit();
            return redirect()->route('indicator.dashboard')->with('success', 'ตัวชี้วัดถูกอัปเดตเรียบร้อยแล้ว');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาดในการอัปเดต: ' . $e->getMessage()])->withInput();
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $indicator = IndicatorModel::findOrFail($id);
            // Thanks to FK cascades (criterias→evidence, indicator→criterias, indicator→assignments),
            // a single delete is enough:
            $indicator->delete();

            DB::commit();
            return redirect()->route('indicator.dashboard')->with('success', 'ตัวชี้วัดถูกลบเรียบร้อยแล้ว');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาดในการลบ: ' . $e->getMessage()]);
        }
    }

    public function getIndicatorsByCategory($categoryId)
    {
        // column name is categorie_id per migration
        $indicators = IndicatorModel::where('categorie_id', $categoryId)->get();
        return response()->json(['indicators' => $indicators]);
    }

    public function getIndicatorsByStandard($standardId)
    {
        $indicators = IndicatorModel::whereHas('category.standard', function ($q) use ($standardId) {
            $q->where('id', $standardId);
        })->get();

        return response()->json(['indicators' => $indicators]);
    }

    /* ===========================
     * Private helpers
     * =========================== */

    private function formSelections(): array
    {
        return [
            'standards'   => Standard::query()->pluck('name', 'id')->toArray(),
            'categories'  => Category::query()->pluck('name', 'id')->toArray(),
            'departments' => Department::query()->pluck('name', 'id')->toArray(),
            'users'       => User::query()->pluck('name', 'id', 'department_id')->toArray(),
        ];
    }

    private function serializeIndicatorForList(IndicatorModel $i): array
    {
        return [
            'id'        => $i->id,
            'name'      => $i->name,
            'year'      => $i->year,
            'code'      => $i->code,
            'deadline'  => $i->deadline ? $i->deadline->format('Y-m-d') : null,
            'status'    => $i->status,
            'score_acc' => $i->score_acc,
            'max_score' => $i->max_score,
            'type'      => $i->type,
            'category'  => $i->category ? [
                'id'   => $i->category->id,
                'name' => $i->category->name,
            ] : null,
            'standard'  => ($i->category && $i->category->standard) ? [
                'id'   => $i->category->standard->id,
                'name' => $i->category->standard->name,
            ] : null,
            'assignments' => $i->assignments->map(function ($a) {
                // $a->collector is an integer (user_id), so we need to fetch the user
                $user = \App\Models\User::find($a->collector);
                return [
                    'user' => $user ? [
                        'id'               => $user->id,
                        'name'             => $user->name,
                        'department_id'    => $user->department_id,
                        'department_name'  => optional($user->department)->name,
                    ] : null,
                ];
            }),
            'evidences' => $i->evidences->map(function ($e) {
                return [
                    'id'         => $e->id,
                    'name'       => $e->name,
                    'path'       => $e->path,
                    'created_at' => $e->created_at ? $e->created_at->format('Y-m-d H:i:s') : null,
                ];
            }),
        ];
    }

    // private function serializeIndicator(IndicatorModel $i): array
    // {
    //     return [
    //         'id'          => $i->id,
    //         'name'        => $i->name,
    //         'year'        => $i->year,
    //         'code'        => $i->code,
    //         'score_acc'   => $i->score_acc,
    //         'max_score'   => $i->max_score,
    //         'type'        => $i->type,
    //         'deadline'    => $i->deadline ? $i->deadline->format('Y-m-d') : null,
    //         'description' => $i->description,
    //         'condition'   => $i->condition,
    //         'comment'     => $i->comment,
    //         'annotation'  => $i->annotation,

    //         'category'    => $i->category ? [
    //             'id'       => $i->category->id,
    //             'name'     => $i->category->name,
    //             'standard' => $i->category->standard ? [
    //                 'id'   => $i->category->standard->id,
    //                 'name' => $i->category->standard->name,
    //             ] : null,
    //         ] : null,

    //         // Fix: collector is already an integer (user_id), not a User model
    //         'assignee'    => optional($i->assignments->first())->collector,

    //         // Or if you want the full user data:
    //         'assignments' => $i->assignments->map(function ($a) {
    //             $user = \App\Models\User::find($a->collector);
    //             return [
    //                 'user' => $user ? [
    //                     'id'               => $user->id,
    //                     'name'             => $user->name,
    //                     'department_id'    => $user->department_id,
    //                     'department_name'  => optional($user->department)->name,
    //                 ] : null,
    //             ];
    //         }),

    //         'criterias'   => $i->criterias()->orderBy('sequence')->get(['id', 'sequence', 'name', 'description'])->toArray(),

    //         // checklist items are the unified representation (explicit combos + generated count rules)
    //         'checklist'   => $i->checklistItems()->get(['required_items', 'score', 'description'])->toArray(),

    //         'scoring'     => [
    //             'variables' => $i->variables()->get()->map(fn($v) => [
    //                 'name'  => $v->variable_name,
    //                 'type'  => $v->type,
    //                 'value' => $v->value,
    //             ])->toArray(),
    //             'formula'   => optional($i->formulas()->first())->condition,
    //         ],
    //     ];
    // }

    /**
     * Insert criteria rows; return count for combination generation.
     */
    private function syncCriterias(IndicatorModel $indicator, array $criteria): int
    {
        $rows = [];
        foreach ($criteria as $c) {
            $rows[] = [
                'name'         => (string)($c['name'] ?? ''),
                'description'  => $c['description'] ?? null,
                'sequence'     => (int)($c['sequence'] ?? 0),
                'indicator_id' => $indicator->id,
            ];
        }
        if ($rows) {
            Criteria::insert($rows);
        }
        return count($rows);
    }

    /**
     * Variables + a single Formula (text in formulas.condition); link via pivot.
     */
    private function syncVariablesAndFormula(IndicatorModel $indicator, array $scoring): void
    {
        $indicator->variables()->delete();
        $indicator->formulas()->delete();

        $vars = (array)($scoring['variables'] ?? []);
        $formulaText = trim((string)($scoring['condition'] ?? ''));

        $createdVars = [];
        foreach ($vars as $v) {
            $name  = trim((string)($v['variable_name'] ?? ''));
            $type  = (string)($v['type'] ?? 'defined'); // defined|input|output
            $value = array_key_exists('value', $v) ? $v['value'] : null;
            if ($name === '') continue;

            $createdVars[] = $indicator->variables()->create([
                'variable_name' => $name,
                'type'          => $type,
                'value'         => $type === 'defined' ? $value : null,
            ]);
        }

        if ($formulaText !== '') {
            $formula = $indicator->formulas()->create([
                'condition' => $formulaText,
            ]);
            foreach ($createdVars as $var) {
                $var->formulas()->attach($formula->id);
            }
        }
    }

    /**
     * Persist explicit checklist combos.
     */
    private function syncChecklistFromSelected(IndicatorModel $indicator, array $multiSelected): void
    {
        foreach ($multiSelected as $row) {
            $req   = array_values(array_filter((array)($row['required_items'] ?? []), 'is_numeric'));
            $score = (float)($row['score'] ?? 0);
            $sequence = (int)($row['sequence'] ?? 1);
            if (!$req) continue;

            sort($req);
            $indicator->checklistItems()->create([
                'required_items' => $req,
                'score'          => $score,
                'sequence'       => $sequence, // Add this
                // Remove 'description' => null, since the column doesn't exist
            ]);
        }
    }

    /**
     * From count rules like [{count:1,score:5},{count:2,score:15}],
     * generate all k-combinations of [1..criteriaCount], skipping duplicates.
     */
    private function syncChecklistFromCounts(IndicatorModel $indicator, array $multiCounts, int $criteriaCount): void
    {
        if ($criteriaCount <= 0 || empty($multiCounts)) return;

        $existingKeys = $indicator->checklistItems()
            ->get(['required_items'])
            ->pluck('required_items')
            ->map(function ($arr) {
                $a = array_map('intval', (array)$arr);
                sort($a);
                return implode(',', $a);
            })->flip();

        $universe = range(1, $criteriaCount);

        foreach ($multiCounts as $rule) {
            $k     = (int)($rule['count'] ?? 0);
            $score = (float)($rule['score'] ?? 0);
            if ($k <= 0 || $k > $criteriaCount) continue;

            foreach ($this->kCombinations($universe, $k) as $combo) {
                $key = implode(',', $combo);
                if (isset($existingKeys[$key])) continue;

                $indicator->checklistItems()->create([
                    'required_items' => $combo,
                    'score'          => $score,
                    'sequence'       => 1, // Add default sequence
                    // Remove 'description' => null, since the column doesn't exist
                ]);
                $existingKeys[$key] = true;
            }
        }
    }

    /** Generate all k-combinations of an integer array */
    private function kCombinations(array $arr, int $k): array
    {
        $n = count($arr);
        if ($k < 0 || $k > $n) return [];
        if ($k === 0) return [[]];

        $out = [];
        $this->kCombDfs($arr, $k, 0, [], $out);
        return $out;
    }

    private function kCombDfs(array $arr, int $k, int $start, array $curr, array &$out): void
    {
        if (count($curr) === $k) {
            $tmp = $curr;
            sort($tmp);
            $out[] = $tmp;
            return;
        }
        for ($i = $start; $i < count($arr); $i++) {
            $curr[] = (int)$arr[$i];
            $this->kCombDfs($arr, $k, $i + 1, $curr, $out);
            array_pop($curr);
        }
    }
}
