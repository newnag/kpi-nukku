<?php

namespace App\Http\Controllers;

use App\Models\Indicator;
use App\Models\Criteria;
use App\Models\Evidence;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

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

        return view('evidences.app', compact('evidences', 'fileTypes'));
    }

    /**
     * Store a newly created evidence in storage.
     */
    public function create(Criteria $criteria)
    {
        // Log ตอนเข้าหน้า create
        Log::info('=== EvidenceController@create ===', [
            'criteria_param' => $criteria->id ?? null,
        ]);

        $criterias = Criteria::orderBy('name')->get(['id', 'name']);

        if ($criterias->isEmpty()) {
            Log::warning('No criterias found when trying to create evidence.');
            return redirect()
                ->route('criterias.index')
                ->with('warning', 'ยังไม่มีเกณฑ์ที่ใช้งานอยู่ กรุณาเพิ่ม/เปิดใช้งานเกณฑ์ก่อน');
        }

        Log::info('Render evidences.create view', [
            'criterias_count' => $criterias->count(),
        ]);

        return view('evidences.create', [
            'criterias' => $criterias,
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
            'detail'            => 'nullable|string|max:65535',
        ]);

        try {
            $urls = collect($request->input('additional_urls', []))
                ->filter(fn($u) => filled($u))
                ->values();

            $hasFiles  = $request->hasFile('files');
            $hasUrls   = $urls->isNotEmpty();
            $hasDetail = filled($request->input('detail'));

            Log::info('Evidence input check', [
                'hasFiles'  => $hasFiles,
                'hasUrls'   => $hasUrls,
                'hasDetail' => $hasDetail,
            ]);

            if (!$hasFiles && !$hasUrls && !$hasDetail) {
                return back()->withInput()->withErrors([
                    'general' => 'กรุณาระบุข้อมูลอย่างน้อย 1 รายการ (ไฟล์, URL หรือรายละเอียด)',
                ]);
            }

            // ✅ ใช้ criteria_id จาก form ดึง indicator โดยตรง
            $criteria = Criteria::with('indicator')->findOrFail($request->criteria_id);
            $indicator = $criteria->indicator;

            if (!$indicator) {
                Log::error('Indicator not found for criteria', [
                    'criteria_id' => $criteria->id,
                ]);
                return back()->withInput()->withErrors([
                    'general' => 'ไม่พบตัวชี้วัดที่สอดคล้อง กรุณาตรวจสอบเกณฑ์',
                ]);
            }

            $year = $indicator->year;
            $code = $indicator->code;

            Log::info('Indicator resolved', [
                'indicator_id' => $indicator->id,
                'year'         => $year,
                'code'         => $code,
            ]);

            $payload = [];
            $uploadedFiles = [];
            $evidenceName = null;
            $evidenceType = null;

            if ($hasFiles) {
                foreach ($request->file('files') as $index => $file) {
                    $originalName = $file->getClientOriginalName();
                    $extension    = strtolower($file->getClientOriginalExtension());
                    $filename     = uniqid() . '_' . Str::random(10) . '.' . $extension;

                    $folder = "evidences/year/{$year}/{$code}";
                    $path   = $file->storeAs($folder, $filename, 'public');

                    $uploadedFiles[] = [
                        'original_name' => $originalName,
                        'stored_name'   => $filename,
                        'path'          => $path,
                        'size'          => $file->getSize(),
                        'mime_type'     => $file->getMimeType(),
                        'icon'          => $this->getFileTypeIcon($file->getMimeType()),
                        'size_human'    => $this->formatFileSize($file->getSize()),
                    ];

                    // ✅ ใช้ไฟล์แรกตั้ง name/type ของ evidence
                    if ($index === 0) {
                        $evidenceName = $originalName;
                        $evidenceType = $extension;
                    }
                }
                $payload['files'] = $uploadedFiles;

                Log::info('Files uploaded', [
                    'count' => count($uploadedFiles),
                ]);
            }

            if ($hasUrls) {
                $payload['urls'] = $urls->all();
                Log::info('URLs added', [
                    'urls' => $urls->all(),
                ]);
            }

            $evidence = new Evidence();
            $evidence->path        = $payload;
            $evidence->detail      = $request->input('detail');
            $evidence->status      = true;
            $evidence->criteria_id = $criteria->id;
            $evidence->user_id     = auth()->id();

            // ✅ กำหนดชื่อ/ประเภทจากข้อมูลจริง
            if ($hasFiles) {
                $evidence->name = $evidenceName ?? 'ไม่ทราบชื่อไฟล์';
                $evidence->type = $evidenceType ?? 'unknown';
            } elseif ($hasUrls) {
                $evidence->name = "หลักฐาน URL";
                $evidence->type = "url";
            } else {
                $evidence->name = "รายละเอียดเพิ่มเติม";
                $evidence->type = "note";
            }

            $evidence->save();

            Log::info('Evidence saved', [
                'evidence_id' => $evidence->id,
                'criteria_id' => $evidence->criteria_id,
                'name'        => $evidence->name,
                'type'        => $evidence->type,
            ]);

            return redirect()->route('evidences.index')
                ->with('success', 'บันทึกหลักฐานเรียบร้อยแล้ว');
        } catch (\Throwable $e) {
            Log::error('Evidence store error', [
                'exception' => $e->getMessage(),
            ]);
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
