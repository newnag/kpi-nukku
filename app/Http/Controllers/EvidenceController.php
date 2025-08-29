<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use App\Models\Evidence;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

class EvidenceController extends Controller
{
    /**
     * Display a listing of the evidence.
     */
    public function index(Request $request)
    {
        // เริ่มต้น query
        $query = Evidence::with(['criteria', 'user']);

        // กรองแบบใช้ filled() เพื่อกันค่าว่าง ''
        if ($request->filled('criteria_id')) {
            $query->where('criteria_id', (int) $request->input('criteria_id'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->input('user_id'));
        }

        // กรอง status ให้ชัดเจน: รับเฉพาะ '0' หรือ '1'
        if ($request->has('status') && $request->input('status') !== '') {
            // รองรับฟอร์มที่ส่ง '0' / '1' หรือ true/false
            $status = $request->input('status');
            if (in_array($status, ['0', '1', 0, 1, true, false], true)) {
                $query->where('status', (int) $status);
            }
            // ถ้าส่งค่าอื่นมา เช่น 'all' จะไม่ใส่ where
        }

        // กรองตามประเภทไฟล์ (หากมี)
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // สร้างรายการประเภทไฟล์จาก query ที่ "กรองแล้ว" (ก่อน paginate) เพื่อให้ dropdown/ตัวเลือกไม่หลุด
        $fileTypes = (clone $query)
            ->select('type')
            ->whereNotNull('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');

        // หน้าเพจ + คง query string เวลาคลิกเปลี่ยนหน้า
        $perPage   = (int) $request->input('per_page', 15);
        $evidences = $query->paginate($perPage)->withQueryString();

        return view('evidences.app', compact('evidences', 'fileTypes'));
    }

    /**
     * Store a newly created evidence in storage.
     */
    public function create()
    {
        // 1) ตรวจสิทธิ์
        // Gate::authorize('create',Evidence::class);

        // 2) ดึงรายการเกณฑ์ที่ใช้งานอยู่
        $criterias = Criteria::orderBy('name')->get(['id', 'name']);

        // 3) เคสไม่มีเกณฑ์ให้เลือก
        if ($criterias->isEmpty()) {
            return redirect()
                ->route('criterias.index') // หรือ evidences.index ตาม UX
                ->with('warning', 'ยังไม่มีเกณฑ์ที่ใช้งานอยู่ กรุณาเพิ่ม/เปิดใช้งานเกณฑ์ก่อน');
        }

        // 4) คืน view (ไม่ต้องส่ง user ไปก็ได้)
        return view('evidences.create', [
            'criterias' => $criterias,
        ]);
    }


    public function store(Request $request)
    {
        // 1) Validate
        $request->validate([
            'files.*'           => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240', // 10MB ต่อไฟล์
            'additional_urls'   => 'nullable|array',
            'additional_urls.*' => 'nullable|url|max:2048',
            'detail'            => 'nullable|string|max:65535',
            'criteria_id'       => 'nullable|integer',
        ], [
            'files.*.mimes'         => 'ไฟล์ต้องเป็นประเภท: pdf, jpg, jpeg, png, doc, docx เท่านั้น',
            'files.*.max'           => 'ไฟล์ต้องมีขนาดไม่เกิน 10MB',
            'additional_urls.*.url' => 'รูปแบบ URL ไม่ถูกต้อง',
            'detail.max'            => 'รายละเอียดต้องมีความยาวไม่เกิน 65,535 ตัวอักษร',
        ]);

        try {
            // 2) อย่างน้อยต้องมีอย่างใดอย่างหนึ่ง
            $urls = collect($request->input('additional_urls', []))
                ->filter(fn($u) => filled($u))
                ->values();

            $hasFiles = $request->hasFile('files') && count($request->file('files')) > 0;
            $hasUrls  = $urls->isNotEmpty();
            $hasDetail = filled($request->input('detail'));

            if (!$hasFiles && !$hasUrls && !$hasDetail) {
                return back()->withInput()->withErrors([
                    'general' => 'กรุณาระบุข้อมูลอย่างน้อย 1 รายการ (ไฟล์, URL หรือรายละเอียด)',
                ]);
            }

            // 3) เตรียม payload สำหรับเก็บใน column "path" (เป็น JSON)
            $payload = [];
            $uploadedFiles = [];

            if ($hasFiles) {
                foreach ($request->file('files') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $extension    = $file->getClientOriginalExtension();
                    $filename     = time() . '_' . Str::random(10) . '.' . $extension;

                    // เก็บไฟล์ที่ storage/app/public/evidences
                    $path = $file->storeAs('evidences', $filename, 'public');

                    $uploadedFiles[] = [
                        'original_name' => $originalName,
                        'stored_name'   => $filename,
                        'path'          => $path,
                        'size'          => $file->getSize(),
                        'mime_type'     => $file->getMimeType(),
                    ];
                }
                $payload['files'] = $uploadedFiles;
            }

            if ($hasUrls) {
                $payload['urls'] = $urls->all();
            }

            // 4) บันทึก Evidence
            $evidence = new Evidence();
            $evidence->path        = $payload;   // แนะนำ cast เป็น array ใน Model (ดูด้านล่าง)
            $evidence->detail      = $request->input('detail');
            $evidence->status      = true;
            $evidence->criteria_id = $request->input('criteria_id');
            $evidence->user_id     = auth()->id();

            // ตั้งชื่อ/ชนิดอัตโนมัติ
            if ($hasFiles && $hasUrls) {
                $evidence->type = 'mixed';
                $evidence->name = "หลักฐานรวม - " . count($uploadedFiles) . " ไฟล์, " . $urls->count() . " ลิงก์";
            } elseif ($hasFiles) {
                $evidence->type = 'file';
                $evidence->name = "หลักฐานไฟล์ - " . count($uploadedFiles) . " ไฟล์";
            } elseif ($hasUrls) {
                $evidence->type = 'url';
                $evidence->name = "หลักฐาน URL - " . $urls->count() . " ลิงก์";
            } else {
                $evidence->type = 'note';
                $evidence->name = "รายละเอียดเพิ่มเติม";
            }

            $evidence->save();

            return redirect()->route('evidences.index')->with('success', 'บันทึกหลักฐานเรียบร้อยแล้ว');
        } catch (\Throwable $e) {
            \Log::error('Evidence store error: ' . $e->getMessage());

            // ลบไฟล์ที่อัปโหลดไว้แล้ว หากบันทึก DB ล้มเหลว
            if (!empty($uploadedFiles)) {
                foreach ($uploadedFiles as $f) {
                    Storage::disk('public')->delete($f['path'] ?? null);
                }
            }

            return back()->withInput()->withErrors([
                'general' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง',
            ]);
        }
    }


    /**
     * Get file type icon class for display
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
     * Format file size for human readable format
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
    public function destroy($id): JsonResponse
    {
        try {
            $evidence = Evidence::findOrFail($id);

            // Delete file from storage
            if ($evidence->path && Storage::disk('public')->exists($evidence->path)) {
                Storage::disk('public')->delete($evidence->path);
            }

            $evidence->delete();

            return response()->json([
                'success' => true,
                'message' => 'Evidence deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete evidence',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download evidence file.
     */


    public function download($id)
    {
        $e = \App\Models\Evidence::findOrFail($id);
        $raw = (string) $e->getOriginal('path');

        // ====== เคส JSON (multi-file หรือมี URL) ======
        $json = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
            $files = $json['files'] ?? [];
            $urls  = $json['urls'] ?? [];

            // --- มีไฟล์เดียว ---
            if (count($files) === 1) {
                $file = $files[0];
                $rel  = ltrim(str_replace('\\', '/', $file['path'] ?? ''), '/');
                if ($rel && Storage::disk('public')->exists($rel)) {
                    return response()->download(
                        Storage::disk('public')->path($rel),
                        $file['original_name'] ?? basename($rel)
                    );
                }
                return $this->fileNotFound();
            }

            // --- หลายไฟล์ -> zip ---
            if (count($files) > 1) {
                $zipBase = Str::slug($e->name ?: "evidence-{$e->id}", '-') . "-{$e->id}";
                $tempDir = storage_path('app/temp');
                if (!is_dir($tempDir)) @mkdir($tempDir, 0775, true);
                $zipFull = $tempDir . DIRECTORY_SEPARATOR . $zipBase . '.zip';

                $zip = new \ZipArchive();
                if ($zip->open($zipFull, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                    return response()->json(['success' => false, 'message' => 'ไม่สามารถสร้างไฟล์ ZIP ได้'], 500);
                }

                foreach ($files as $f) {
                    $rel = ltrim(str_replace('\\', '/', $f['path'] ?? ''), '/');
                    if ($rel && Storage::disk('public')->exists($rel)) {
                        $zip->addFile(Storage::disk('public')->path($rel), $f['original_name'] ?? basename($rel));
                    }
                }
                $zip->close();

                return response()->download($zipFull, $zipBase . '.zip')->deleteFileAfterSend(true);
            }

            // --- URL อย่างเดียว ---
            if (!empty($urls)) {
                return redirect()->away($urls[0]);
            }

            return $this->fileNotFound();
        }

        // ====== เคส string เดี่ยว ======
        $path = ltrim(str_replace('\\', '/', $raw), '/');
        $path = preg_replace('#^(storage(?:/app)?/public/|public/|storage/)+#i', '', $path);

        if ($path && Storage::disk('public')->exists($path)) {
            $full = Storage::disk('public')->path($path);
            $name = $e->name ?: basename($path);
            if (!str_contains(strtolower($name), '.') && !empty($e->type)) {
                $name .= '.' . ltrim($e->type, '.');
            }
            return response()->download($full, $name);
        }

        // เผื่อ absolute path
        if ($raw && file_exists($raw)) {
            return response()->download($raw, $e->name ?: basename($raw));
        }

        return $this->fileNotFound();
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
