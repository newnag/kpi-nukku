<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use App\Models\Evidence;
use App\Models\Indicator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class EvidenceController extends Controller
{
    public function index(Request $request)
    {
        // preload ความสัมพันธ์ครบ
        $query = Evidence::with([
            'criteria.indicator.category.standard',
            'criteria.indicator.assignments.collectorUser.department',
            'user'
        ]);

        // 🟢 ถ้า role = user → แสดงเฉพาะของตัวเอง
        if (auth()->user()->hasRole('user')) {
            $query->where('user_id', auth()->id());
        }

        // filter ต่างๆ (optionally)
        if ($request->filled('criteria_id')) {
            $query->where('criteria_id', (int) $request->input('criteria_id'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->input('user_id'));
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('status', (int) $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('indicator_id')) {
            $query->whereHas('criteria.indicator', function ($q) use ($request) {
                $q->where('id', (int) $request->input('indicator_id'));
            });
        }

        // ✅ Dropdown data
        $years = Indicator::whereNotNull('year')->distinct()->orderByDesc('year')->pluck('year');
        $standards = \App\Models\Standard::select('name')->distinct()->orderBy('name')->pluck('name');
        $dimensions = \App\Models\Category::select('name')->distinct()->orderBy('name')->pluck('name');
        $departments = \App\Models\Department::select('name')->distinct()->orderBy('name')->pluck('name');
        $collectors = \App\Models\User::whereHas('assignments')
            ->select('name')->distinct()->orderBy('name')->pluck('name');
        $fileTypes = Evidence::whereNotNull('type')->distinct()->orderBy('type')->pluck('type');
        $statusMap = [
            0 => 'รอดำเนินการ',
            1 => 'รอดำเนินการ / บันทึกร่าง',
            2 => 'รอดำเนินการ / บันทึกจริง',
            3 => 'ครบถ้วนตามเกณฑ์',
            4 => 'ยังไม่ครบถ้วนตามเกณฑ์',
        ];
        $statusList = Indicator::pluck('status')->unique()->map(fn($s) => $statusMap[$s] ?? 'ไม่ทราบ');
        // Pagination
        $perPage = (int) $request->input('per_page', 1000);
        $evidences = $query->paginate($perPage)->withQueryString();

        // คำนวณขนาดไฟล์รวม
        $evidences->getCollection()->transform(function ($evidence) {
            $totalSize = 0;
            if (!empty($evidence->path['files'])) {
                foreach ($evidence->path['files'] as $f) {
                    $totalSize += $f['size'] ?? 0;
                }
            }
            $evidence->total_size = $totalSize;
            $evidence->total_size_human = $this->formatFileSize($totalSize);
            return $evidence;
        });

        return view('evidences.app', compact(
            'evidences',
            'years',
            'standards',
            'dimensions',
            'departments',
            'collectors',
            'fileTypes',
            'statusList',
        ));
    }

    // public function index(Request $request)
    // {
    //     // เริ่มต้น query + preload ความสัมพันธ์
    //     $query = Evidence::with(['criteria.indicator', 'user']);

    //     // 🟢 ถ้า role = user → แสดงเฉพาะของตัวเอง
    //     if (auth()->user()->hasRole('user')) {
    //         $query->where('user_id', auth()->id());
    //     }

    //     // กรอง criteria_id
    //     if ($request->filled('criteria_id')) {
    //         $query->where('criteria_id', (int) $request->input('criteria_id'));
    //     }

    //     // กรอง user_id (แต่ user ปกติไม่ควรเลือก user_id อื่นได้อยู่แล้ว)
    //     if ($request->filled('user_id')) {
    //         $query->where('user_id', (int) $request->input('user_id'));
    //     }

    //     // กรอง status
    //     if ($request->has('status') && $request->input('status') !== '') {
    //         $status = $request->input('status');
    //         if (in_array($status, ['0', '1', 0, 1, true, false], true)) {
    //             $query->where('status', (int) $status);
    //         }
    //     }

    //     // กรอง type
    //     if ($request->filled('type')) {
    //         $query->where('type', $request->input('type'));
    //     }

    //     // ✅ กรอง indicator
    //     if ($request->filled('indicator_id')) {
    //         $query->whereHas('criteria.indicator', function ($q) use ($request) {
    //             $q->where('id', (int) $request->input('indicator_id'));
    //         });
    //     }

    //     // 🔹 รายการประเภทไฟล์
    //     $fileTypes = (clone $query)
    //         ->select('type')
    //         ->whereNotNull('type')
    //         ->distinct()
    //         ->orderBy('type')
    //         ->pluck('type');

    //     // 🔹 รายการผู้ใช้ (เฉพาะ role อื่น ๆ เท่านั้นถึงจะเห็น user list)
    //     $fileUsers = collect();
    //     if (!auth()->user()->hasRole('user')) {
    //         $fileUsers = (clone $query)
    //             ->join('users', 'evidence.user_id', '=', 'users.id')
    //             ->select('users.name')
    //             ->whereNotNull('users.name')
    //             ->distinct()
    //             ->orderBy('users.name')
    //             ->pluck('users.name');
    //     }

    //     // 🔹 รายการตัวชี้วัด
    //     $indicators = Indicator::select('code', 'name')
    //         ->groupBy('code', 'name')
    //         ->orderByRaw("split_part(code, '-', 1)")            // prefix ก่อน '-'
    //         ->orderByRaw("(split_part(code, '-', 2))::int")     // ตัวเลขหลัง '-'
    //         ->get();

    //     // Pagination
    //     $perPage   = (int) $request->input('per_page', 5000);
    //     $evidences = $query->paginate($perPage)->withQueryString();

    //     // คำนวณ total_size
    //     $evidences->getCollection()->transform(function ($evidence) {
    //         $totalSize = 0;
    //         if (!empty($evidence->path['files'])) {
    //             foreach ($evidence->path['files'] as $f) {
    //                 $totalSize += $f['size'] ?? 0;
    //             }
    //         }
    //         $evidence->total_size = $totalSize;
    //         $evidence->total_size_human = $this->formatFileSize($totalSize);
    //         return $evidence;
    //     });

    //     return view('evidences.app', compact('evidences', 'fileTypes', 'fileUsers', 'indicators'));
    // }


    /**
     * Store a newly created evidence in storage.
     */
    public function create(Criteria $criteria)
    {
        // Log ตอนเข้าหน้า create
        // Log::info('=== EvidenceController@create ===', [
        //     'criteria_param' => $criteria->id ?? null,
        // ]);

        $criterias = Criteria::orderBy('name')->get(['id', 'name']);

        if ($criterias->isEmpty()) {
            Log::warning('No criterias found when trying to create evidence.');
            return redirect()
                ->route('dashboard.index')
                ->with('warning', 'ยังไม่มีเกณฑ์ที่ใช้งานอยู่ กรุณาเพิ่ม/เปิดใช้งานเกณฑ์ก่อน');
        }

        // Log::info('Render evidences.create view', [
        //     'criterias_count' => $criterias->count(),
        // ]);

        return view('evidences.create', [
            'criterias'   => $criterias,
            'criteria_id' => $criteria->id, // ✅ ส่งไปด้วย
            'criteria'    => $criteria      // ถ้าอยากใช้ทั้ง object
        ]);
    }



    public function store(Request $request)
    {
        Log::info('=== EvidenceController@store ===', [
            'criteria_id' => $request->input('criteria_id'),
        ]);

        $request->validate([
            'criteria_id'       => 'required|integer|exists:criterias,id',
            'files.*'           => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'additional_urls'   => 'nullable|array',
            'additional_urls.*' => 'nullable|url|max:2048',
            'url_names'         => 'nullable|array',
            'url_names.*'       => 'nullable|string|max:255',
            'detail'            => 'nullable|string|max:65535',
        ]);

        $uploadedFiles = [];
        $savedEvidences = []; // ✅ เก็บ evidences หลายรายการ

        try {
            $urls     = collect($request->input('additional_urls', []));
            $urlNames = collect($request->input('url_names', []));

            $urlEntries = $urls
                ->filter(fn($u) => filled($u))
                ->values()
                ->map(function ($url, $i) use ($urlNames) {
                    return [
                        'url'  => $url,
                        'name' => $urlNames->get($i) ?: 'หลักฐาน URL',
                    ];
                });

            $hasFiles  = $request->hasFile('files');
            $hasUrls   = $urlEntries->isNotEmpty();
            $hasDetail = filled($request->input('detail'));

            if (!$hasFiles && !$hasUrls && !$hasDetail) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'กรุณาระบุข้อมูลอย่างน้อย 1 รายการ (ไฟล์, URL หรือรายละเอียด)',
                    ], 422);
                }
                return back()->withInput()->withErrors([
                    'general' => 'กรุณาระบุข้อมูลอย่างน้อย 1 รายการ (ไฟล์, URL หรือรายละเอียด)',
                ]);
            }

            // ✅ chain: criteria -> indicator -> category -> standard
            $criteria  = Criteria::with('indicator.category.standard')->findOrFail($request->criteria_id);
            $indicator = $criteria->indicator;
            $category  = $indicator->category;
            $standard  = $category->standard;

            if (!$indicator || !$category || !$standard) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'กรุณาตรวจสอบว่าตัวชี้วัดนี้มีการผูกกับหมวดหมู่และมาตรฐานแล้ว',
                    ], 422);
                }
                return back()->withErrors([
                    'general' => 'กรุณาตรวจสอบว่าตัวชี้วัดนี้มีการผูกกับหมวดหมู่และมาตรฐานแล้ว',
                ]);
            }

            // ใช้ slug กันชื่อไทย/ช่องว่าง
            $standardSegment = $this->safeFolderSegment($standard->name ?? '', 'standard-' . $standard->name);
            $categorySegment = $this->safeFolderSegment($category->name ?? '', 'category-' . $category->name);

            $folder = implode('/', [
                'evidences',
                'year',
                $indicator->year,
                $standardSegment,
                $categorySegment,
                $indicator->code,
            ]);

            // ========== 1) ถ้ามีไฟล์ → loop แล้วบันทึก ==========
            if ($hasFiles) {
                foreach ($request->file('files') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $extension    = strtolower($file->getClientOriginalExtension());
                    $filename     = uniqid() . '_' . Str::random(10) . '.' . $extension;

                    $path = $file->storeAs($folder, $filename, 'public');

                    $payload = [
                        'files' => [[
                            'original_name' => $originalName,
                            'stored_name'   => $filename,
                            'path'          => $path,
                            'size'          => $file->getSize(),
                            'mime_type'     => $file->getMimeType(),
                            'icon'          => $this->getFileTypeIcon($file->getMimeType()),
                            'size_human'    => $this->formatFileSize($file->getSize()),
                        ]]
                    ];

                    $evidence = new Evidence();
                    $evidence->path        = $payload;
                    $evidence->detail      = null;
                    $evidence->status      = true;
                    $evidence->criteria_id = $criteria->id;
                    $evidence->user_id     = Auth::id();
                    $evidence->name        = $originalName;
                    $evidence->type        = $extension;
                    $evidence->save();

                    $uploadedFiles[] = ['path' => $path];

                    // Log::info('Evidence saved (file)', [
                    //     'evidence_id' => $evidence->id,
                    //     'name'        => $evidence->name,
                    //     'type'        => $evidence->type,
                    // ]);
                }
            }

            // ========== 2) ถ้ามี URL → บันทึก ==========
            if ($hasUrls) {
                foreach ($urlEntries as $entry) {
                    $payload = ['urls' => [$entry['url']]];

                    $evidence = new Evidence();
                    $evidence->path        = $payload;
                    $evidence->detail      = $request->input('detail');
                    $evidence->status      = true;
                    $evidence->criteria_id = $criteria->id;
                    $evidence->user_id     = Auth::id();
                    $evidence->name        = $entry['name'];
                    $evidence->type        = "url";
                    $evidence->save();

                    // Log::info('Evidence saved (url)', [
                    //     'evidence_id' => $evidence->id,
                    //     'name'        => $evidence->name,
                    //     'url'         => $entry['url'],
                    // ]);
                }
            }

            // ========== 3) ถ้ามีแค่ Detail ==========
            if (!$hasUrls && $hasDetail) {
                $evidence = new Evidence();
                $evidence->path        = [];
                $evidence->detail      = $request->input('detail');
                $evidence->status      = true;
                $evidence->criteria_id = $criteria->id;
                $evidence->user_id     = Auth::id();
                $evidence->name        = "รายละเอียดเพิ่มเติม";
                $evidence->type        = "note";
                $evidence->save();

                // Log::info('Evidence saved (note)', [
                //     'evidence_id' => $evidence->id,
                //     'name'        => $evidence->name,
                // ]);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'บันทึกหลักฐานเรียบร้อยแล้ว',
                ]);
            }

            return redirect()->route('dashboardKpiUser.show', $indicator->id)
                ->with('success', 'บันทึกหลักฐานเรียบร้อยแล้ว');
        } catch (\Throwable $e) {
            Log::error('Evidence store error', [
                'exception' => $e->getMessage(),
            ]);

            // rollback ลบไฟล์ที่อัปโหลดแล้วถ้าเกิด error
            if (!empty($uploadedFiles)) {
                foreach ($uploadedFiles as $f) {
                    Storage::disk('public')->delete($f['path'] ?? null);
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง',
                ], 500);
            }

            return back()->withInput()->withErrors([
                'general' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง',
            ]);
        }
    }



    /**
     * คืน icon type ตาม MIME type
     */
    private function getFileTypeIcon($mimeType)
    {
        switch ($mimeType) {
            case 'application/pdf':
                return 'file-pdf';
            case 'image/jpeg':
            case 'image/jpg':
            case 'image/png':
                return 'image';
            case 'application/msword':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                return 'file-text';
            default:
                return 'file';
        }
    }

    /**
     * Build a filesystem-safe slug for folder names with a reliable fallback.
     */
    private function safeFolderSegment(?string $text, string $fallback): string
    {
        $base = (string) ($text ?? '');
        $slug = trim(Str::slug($base, '-'));
        if ($slug === '' || $slug === '-') {
            return $fallback;
        }
        // Collapse duplicate separators just in case
        return preg_replace('/-+/', '-', $slug);
    }

    /**
     * แปลงขนาดไฟล์เป็น human-readable
     */
    private function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }




    /**
     * Display the specified evidence.
     */
    public function show($id): JsonResponse
    {
        try {
            $evidence = Evidence::with(['criteria', 'user'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $evidence
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Evidence not found'
            ], 404);
        }
    }

    /**
     * Update the specified evidence in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'file' => 'sometimes|file|max:10240', // 10MB max
            'type' => 'sometimes|required|string|max:100',
            'detail' => 'nullable|string',
            'status' => 'boolean',
            'criteria_id' => 'sometimes|required|exists:criteria,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $evidence = Evidence::findOrFail($id);

            $updateData = $request->only(['name', 'type', 'detail', 'status', 'criteria_id']);

            // Handle file upload if provided
            if ($request->hasFile('file')) {
                // Delete old file
                if ($evidence->path && Storage::disk('public')->exists($evidence->path)) {
                    Storage::disk('public')->delete($evidence->path);
                }

                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('evidence', $filename, 'public');
                $updateData['path'] = $path;
            }

            $evidence->update($updateData);
            $evidence->load(['criteria', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Evidence updated successfully',
                'data' => $evidence
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update evidence',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified evidence from storage.
     */
    public function destroy($id)
    {
        $evidence = Evidence::findOrFail($id);

        // ถ้ามีไฟล์จริงใน storage → ลบด้วย
        if (is_array($evidence->path) && isset($evidence->path['files'])) {
            foreach ($evidence->path['files'] as $file) {
                if (!empty($file['path'])) {
                    Storage::disk('public')->delete($file['path']);
                }
            }
        }

        $evidence->delete();

        return response()->json([
            'success' => true,
            'message' => 'ลบหลักฐานเรียบร้อยแล้ว'
        ]);
    }


    /**
     * Download evidence file.
     */


    public function download($id)
    {
        $e = Evidence::findOrFail($id);

        // ดึง JSON raw string โดยตรง
        $raw  = $e->getRawOriginal('path');
        $json = json_decode((string) $raw, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
            $files = $json['files'] ?? [];
            $urls  = $json['urls'] ?? [];
            // --- มีไฟล์เดียว ---
            if (count($files) === 1) {
                $file = $files[0] ?? [];
                $rel  = $this->normalizePath($file['path'] ?? null);

                if ($rel && Storage::disk('public')->exists($rel)) {
                    $downloadName = $file['original_name'] ?? basename($rel);
                    return response()->download(Storage::disk('public')->path($rel), $downloadName);
                }
                return $this->fileNotFound();
            }

            // --- หลายไฟล์ -> ZIP ---
            if (count($files) > 1) {
                $zipBase = Str::slug($e->name ?: "evidence-{$e->id}", '-') . "-{$e->id}";
                $tempDir = storage_path('app/temp');
                if (!is_dir($tempDir)) {
                    @mkdir($tempDir, 0775, true);
                }
                $zipFull = $tempDir . DIRECTORY_SEPARATOR . $zipBase . '.zip';

                $zip = new \ZipArchive();
                if ($zip->open($zipFull, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                    return response()->json(['success' => false, 'message' => 'ไม่สามารถสร้างไฟล์ ZIP ได้'], 500);
                }

                foreach ($files as $f) {
                    $rel = $this->normalizePath($f['path'] ?? null);
                    if ($rel && Storage::disk('public')->exists($rel)) {
                        $zip->addFile(Storage::disk('public')->path($rel), $f['original_name'] ?? basename($rel));
                    }
                }
                $zip->close();

                return response()->download($zipFull, $zipBase . '.zip')->deleteFileAfterSend(true);
            }

            // --- มีแต่ URL ---
            if (!empty($urls)) {
                return redirect()->away($urls[0]);
            }

            return $this->fileNotFound();
        }

        // ====== เคส path string ======
        $path = $this->normalizePath((string) $raw);
        if ($path && Storage::disk('public')->exists($path)) {
            $full = Storage::disk('public')->path($path);
            $name = $e->name ?: basename($path);
            if (!str_contains($name, '.') && !empty($e->type)) {
                $name .= '.' . ltrim($e->type, '.');
            }
            return response()->download($full, $name);
        }

        // ====== absolute path ======
        if ($raw && file_exists($raw)) {
            return response()->download($raw, $e->name ?: basename($raw));
        }

        return $this->fileNotFound();
    }

    /**
     * Normalize relative path ให้สะอาด
     */
    private function normalizePath(?string $path): ?string
    {
        if (!$path) return null;

        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');

        // ลบ prefix ที่ไม่จำเป็น เช่น storage/app/public/
        return preg_replace('#^(storage(?:/app)?/public/|public/|storage/)+#i', '', $path);
    }

    private function fileNotFound()
    {
        return response()->json(['success' => false, 'message' => 'File not found'], 404);
    }
    public function getByCriteria($criteriaId): JsonResponse
    {
        try {
            $evidences = Evidence::with(['user'])
                ->where('criteria_id', $criteriaId)
                ->where('status', true)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $evidences
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get evidence',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle evidence status.
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $evidence = Evidence::findOrFail($id);
            $evidence->update(['status' => !$evidence->status]);
            $evidence->load(['criteria', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Evidence status updated successfully',
                'data' => $evidence
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update evidence status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
