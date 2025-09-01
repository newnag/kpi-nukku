<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

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
            'assignments.user',
            'criterias',
            // 'evidences',
        ])
            ->get()
            ->map(fn($i) => $this->serializeIndicatorForList($i));

        return view('indicator.dashboard', compact('indicators'));

        // return response()->json(['indicators' => $indicators]);
    }

    public function create()
    {
        $data = $this->formSelections();
        $data['criteriaOptions'] = [];

        // ใช้สำหรับ multi-select + filter
        $data['usersForAssign'] = User::select('id', 'name', 'department_id')
            ->orderBy('name')
            ->get()
            ->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'department_id' => $u->department_id,
            ]);

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

            return response()->json(['indicator' => $indicator]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to retrieve indicator'], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Basic
            'year'         => 'required|integer|digits:4',
            'name'         => 'required|string|max:255',
            'code'         => 'required|string|max:100',
            'max_score'    => 'required|numeric|min:0',
            'standard_id'  => 'required|exists:standards,id',
            'category_id'  => 'required|exists:categories,id',
            'type'         => 'nullable|string',
            'deadline'     => 'required|date',

            // Responsible
            'department_ids'   => 'nullable|array',
            'department_ids.*' => 'exists:departments,id',

            'user_ids'         => 'required|array|min:1',
            'user_ids.*'       => 'exists:users,id',

            // Rich text
            'description'  => 'nullable|string',
            'condition'    => 'nullable|string',
            'comment'      => 'nullable|string',
            'annotation'   => 'nullable|string',

            // Criteria
            'criteria'                   => 'nullable|array',
            'criteria.*.sequence'        => 'required|integer',
            'criteria.*.name'            => 'required|string|max:255',
            'criteria.*.description'     => 'nullable|string',

            // Multi-choice (count-based)
            'multiCounts'                => 'nullable|array',
            'multiCounts.*.count'        => 'required|integer|min:0',
            'multiCounts.*.score'        => 'required|numeric',

            // Multi-choice (selected-based)
            'multiSelected'                    => 'nullable|array',
            'multiSelected.*.sequence'         => 'required|integer',
            'multiSelected.*.required_items'   => 'nullable|array',
            'multiSelected.*.required_items.*' => 'integer',
            'multiSelected.*.score'            => 'required|numeric',

            // Custom scoring
            'scoring.variables'                       => 'nullable|array',
            'scoring.variables.*.variable_name'      => 'required|string|max:100',
            'scoring.variables.*.type'               => 'required|in:defined,input,output',
            'scoring.variables.*.value'              => 'nullable|numeric',
            'scoring.condition'                      => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $indicator = IndicatorModel::create([
                'year'         => $validated['year'],
                'name'         => $validated['name'],
                'code'         => $validated['code'],
                'max_score'    => $validated['max_score'],
                'score_acc'    => 0,
                'categorie_id' => $validated['category_id'], // คอลัมน์สะกดตามนี้
                'type'         => $validated['type'] ?? null,
                'deadline'     => $validated['deadline'],
                'status'       => 2,
                'description'  => $validated['description'] ?? null,
                'condition'    => $validated['condition'] ?? null,
                'comment'      => $validated['comment'] ?? null,
                'annotation'   => $validated['annotation'] ?? null,
            ]);

            // สร้าง assignments หลายรายการ
            $indicator->assignments()->createMany(
                collect($validated['user_ids'])->unique()->values()->map(fn($uid) => ['collector' => $uid])->all()
            );

            $criteriaCount = $this->syncCriterias($indicator, $validated['criteria'] ?? []);
            $this->syncVariablesAndFormula($indicator, $validated['scoring'] ?? []);
            $this->syncChecklistFromSelected($indicator, $validated['multiSelected'] ?? []);
            $this->syncChecklistFromCounts($indicator, $validated['multiCounts'] ?? [], $criteriaCount);

            DB::commit();

            return redirect()
                ->route('indicator.dashboard')
                ->with('success', 'ตัวชี้วัดถูกสร้างเรียบร้อยแล้ว');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            // Basic
            'year'         => 'required|integer|digits:4',
            'name'         => 'required|string|max:255',
            'code'         => 'required|string|max:100',
            'max_score'    => 'required|numeric|min:0',
            'standard_id'  => 'required|exists:standards,id',
            'category_id'  => 'required|exists:categories,id',
            'type'         => 'nullable|string',
            'deadline'     => 'required|date',

            // Responsible
            'department_ids'   => 'nullable|array',
            'department_ids.*' => 'exists:departments,id',

            'user_ids'         => 'required|array|min:1',
            'user_ids.*'       => 'exists:users,id',

            // Rich text
            'description'  => 'nullable|string',
            'condition'    => 'nullable|string',
            'comment'      => 'nullable|string',
            'annotation'   => 'nullable|string',

            // Criteria
            'criteria'                   => 'nullable|array',
            'criteria.*.sequence'        => 'required|integer',
            'criteria.*.name'            => 'required|string|max:255',
            'criteria.*.description'     => 'nullable|string',

            // Multi-choice
            'multiCounts'                => 'nullable|array',
            'multiCounts.*.count'        => 'required|integer|min:0',
            'multiCounts.*.score'        => 'required|numeric',

            'multiSelected'                     => 'nullable|array',
            'multiSelected.*.sequence'          => 'required|integer',
            'multiSelected.*.required_items'    => 'nullable|array',
            'multiSelected.*.required_items.*'  => 'integer',
            'multiSelected.*.score'             => 'required|numeric',

            // Scoring
            'scoring.variables'                       => 'nullable|array',
            'scoring.variables.*.variable_name'      => 'required|string|max:100',
            'scoring.variables.*.type'               => 'required|in:defined,input,output',
            'scoring.variables.*.value'              => 'nullable|numeric',
            'scoring.condition'                      => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

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
            ]);

            // อัปเดต assignment (ลบของเดิมแล้วสร้างใหม่)
            $indicator->assignments()->delete();
            $indicator->assignments()->createMany(
                collect($validated['user_ids'])->unique()->values()->map(fn($uid) => ['collector' => $uid])->all()
            );

            // แทนที่ criterias ทั้งชุด
            $indicator->criterias()->delete();
            $criteriaCount = $this->syncCriterias($indicator, $validated['criteria'] ?? []);

            // แทนที่ variables + formula
            $this->syncVariablesAndFormula($indicator, $validated['scoring'] ?? []);

            // แทนที่ checklist items
            $indicator->checklistItems()->delete();
            $this->syncChecklistFromSelected($indicator, $validated['multiSelected'] ?? []);
            $this->syncChecklistFromCounts($indicator, $validated['multiCounts'] ?? [], $criteriaCount);

            DB::commit();

            return redirect()
                ->route('indicator.dashboard')
                ->with('success', 'ตัวชี้วัดถูกอัปเดตเรียบร้อยแล้ว');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการอัปเดต: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $indicator = IndicatorModel::findOrFail($id);
            // อาศัย FK cascade ในตารางลูก (ถ้าตั้งค่าไว้แล้ว)
            $indicator->delete();

            DB::commit();
            return redirect()
                ->route('indicator.dashboard')
                ->with('success', 'ตัวชี้วัดถูกลบเรียบร้อยแล้ว');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการลบ: ' . $e->getMessage()]);
        }
    }

    public function getIndicatorsByCategory($categoryId)
    {
        // หมายเหตุ: คอลัมน์คือ categorie_id
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
        ];
    }

    private function serializeIndicatorForList(IndicatorModel $i): array
    {
        // รองรับทั้ง cast ที่เป็น Carbon และสตริงธรรมดา
        $deadline = null;
        if ($i->deadline instanceof Carbon) {
            $deadline = $i->deadline->format('Y-m-d');
        } elseif (!empty($i->deadline)) {
            try {
                $deadline = Carbon::parse($i->deadline)->format('Y-m-d');
            } catch (\Throwable $e) {
                $deadline = (string) $i->deadline;
            }
        }

        return [
            'id'        => $i->id,
            'name'      => $i->name,
            'year'      => $i->year,
            'code'      => $i->code,
            'deadline'  => $deadline,
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

            // ทำให้ dashboard.blade เอาไป map หา department ได้
            'assignments' => $i->assignments->map(function ($a) {
                // ถ้าในความสัมพันธ์ Assignment มี ->user ก็ใช้เลย ไม่งั้น fallback หาเอง
                $user = $a->user ?? \App\Models\User::find($a->collector);
                return [
                    'user' => $user ? [
                        'id'              => $user->id,
                        'name'            => $user->name,
                        'department_id'   => $user->department_id,
                        'department_name' => optional($user->department)->name,
                    ] : null,
                ];
            }),

            'criteria' => $i->criterias->map(function ($c) {
                return [
                    'id'          => $c->id,
                    'name'        => $c->name,
                    // 'description' => $c->description,
                    // 'sequence'    => $c->sequence,
                    'status'      => $c->status,
                ];
            }),

            // 'evidences' => $i->evidences->map(function ($e) {
            //     return [
            //         'id'         => $e->id,
            //         'name'       => $e->name,
            //         'path'       => $e->path,
            //         'created_at' => $e->created_at instanceof Carbon
            //             ? $e->created_at->format('Y-m-d H:i:s')
            //             : (string) $e->created_at,
            //     ];
            // }),
        ];
    }

    /**
     * Insert criteria rows; return count for combination generation.
     */
    private function syncCriterias(IndicatorModel $indicator, array $criteria): int
    {
        $rows = [];
        foreach ($criteria as $c) {
            $rows[] = [
                'name'         => (string) ($c['name'] ?? ''),
                'description'  => $c['description'] ?? null,
                'sequence'     => (int) ($c['sequence'] ?? 0),
                'indicator_id' => $indicator->id,
            ];
        }
        if (!empty($rows)) {
            // ถ้าต้องการ timestamps ให้เพิ่ม created_at/updated_at เอง หรือใช้ createMany ผ่าน relation
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

        $vars        = (array) ($scoring['variables'] ?? []);
        $formulaText = trim((string) ($scoring['condition'] ?? ''));

        $createdVars = [];
        foreach ($vars as $v) {
            $name  = trim((string) ($v['variable_name'] ?? ''));
            $type  = (string) ($v['type'] ?? 'defined'); // defined|input|output
            $value = array_key_exists('value', $v) ? $v['value'] : null;

            if ($name === '') {
                continue;
            }

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
            $req      = array_values(array_filter((array) ($row['required_items'] ?? []), 'is_numeric'));
            $score    = (float) ($row['score'] ?? 0);
            $sequence = (int) ($row['sequence'] ?? 1);

            if (!$req) continue;

            sort($req);

            $indicator->checklistItems()->create([
                'required_items' => $req,
                'score'          => $score,
                'sequence'       => $sequence,
            ]);
        }
    }

    /**
     * From count rules like [{count:1,score:5},{count:2,score:15}],
     * generate all k-combinations of [1..criteriaCount], skipping duplicates.
     */
    private function syncChecklistFromCounts(IndicatorModel $indicator, array $multiCounts, int $criteriaCount): void
    {
        if ($criteriaCount <= 0 || empty($multiCounts)) {
            return;
        }

        $existingKeys = $indicator->checklistItems()
            ->get(['required_items'])
            ->pluck('required_items')
            ->map(function ($arr) {
                $a = array_map('intval', (array) $arr);
                sort($a);
                return implode(',', $a);
            })->flip();

        $universe = range(1, $criteriaCount);

        foreach ($multiCounts as $rule) {
            $k     = (int) ($rule['count'] ?? 0);
            $score = (float) ($rule['score'] ?? 0);

            if ($k <= 0 || $k > $criteriaCount) continue;

            foreach ($this->kCombinations($universe, $k) as $combo) {
                $key = implode(',', $combo);
                if (isset($existingKeys[$key])) continue;

                $indicator->checklistItems()->create([
                    'required_items' => $combo,
                    'score'          => $score,
                    'sequence'       => 1,
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
            $curr[] = (int) $arr[$i];
            $this->kCombDfs($arr, $k, $i + 1, $curr, $out);
            array_pop($curr);
        }
    }
}
