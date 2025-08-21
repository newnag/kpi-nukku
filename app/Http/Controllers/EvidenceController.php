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
    /**
     * Store a newly created evidence in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation rules
        $request->validate([
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240', // 10MB max per file
            'url' => 'nullable|url|max:2048',
            'additional_url' => 'nullable|url|max:2048',
            'detail' => 'nullable|string|max:65535',
        ], [
            'files.*.mimes' => 'ไฟล์ต้องเป็นประเภท: pdf, jpg, jpeg, png, doc, docx เท่านั้น',
            'files.*.max' => 'ไฟล์ต้องมีขนาดไม่เกิน 10MB',
            'url.url' => 'รูปแบบ URL ไม่ถูกต้อง',
            'additional_url.url' => 'รูปแบบ URL เพิ่มเติมไม่ถูกต้อง',
            'detail.max' => 'รายละเอียดต้องมีความยาวไม่เกิน 65,535 ตัวอักษร'
        ]);

        try {
            // Check if at least one input is provided
            if (!$request->hasFile('files') && !$request->filled('url') && !$request->filled('additional_url') && !$request->filled('detail')) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['general' => 'กรุณาระบุข้อมูลอย่างน้อย 1 รายการ (ไฟล์, URL, หรือรายละเอียด)']);
            }

            // Create new evidence record
            $evidence = new Evidence();

            // Handle file uploads
            if ($request->hasFile('files')) {
                $uploadedFiles = [];
                foreach ($request->file('files') as $file) {
                    // Generate unique filename
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '_' . Str::random(10) . '.' . $extension;

                    // Store file in storage/app/public/evidences
                    $path = $file->storeAs('evidences', $filename, 'public');

                    $uploadedFiles[] = [
                        'original_name' => $originalName,
                        'stored_name' => $filename,
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType()
                    ];
                }

                // Store file information as JSON
                $evidence->path = json_encode($uploadedFiles);
                $evidence->type = 'file';
                $evidence->name = 'หลักฐานไฟล์ - ' . count($uploadedFiles) . ' ไฟล์';
            }

            // Handle URL input
            $urls = [];
            if ($request->filled('url')) {
                $urls[] = $request->url;
            }
            if ($request->filled('additional_url')) {
                $urls[] = $request->additional_url;
            }

            if (!empty($urls)) {
                if ($request->hasFile('files')) {
                    // If files exist, store URLs separately or combine
                    $evidence->path = json_encode(array_merge(
                        json_decode($evidence->path, true),
                        ['urls' => $urls]
                    ));
                } else {
                    $evidence->path = json_encode(['urls' => $urls]);
                    $evidence->type = 'url';
                    $evidence->name = 'หลักฐาน URL - ' . count($urls) . ' ลิงก์';
                }
            }

            // Handle mixed content name
            if ($request->hasFile('files') && !empty($urls)) {
                $fileCount = count($request->file('files'));
                $urlCount = count($urls);
                $evidence->name = "หลักฐานรวม - {$fileCount} ไฟล์, {$urlCount} ลิงก์";
                $evidence->type = 'mixed';
            }

            // Set other fields
            $evidence->detail = $request->detail;
            $evidence->status = true; // Default status as active

            // Set foreign keys (adjust based on your requirements)
            $evidence->criteria_id = $request->criteria_id ?? null;
            $evidence->user_id = auth()->id(); // Current authenticated user

            // Save to database
            $evidence->save();

            // Redirect with success message
            return redirect()->route('evidences.index')
                ->with('success', 'บันทึกหลักฐานเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            // Handle errors
            \Log::error('Evidence store error: ' . $e->getMessage());

            // Clean up uploaded files if database save failed
            if (isset($uploadedFiles)) {
                foreach ($uploadedFiles as $fileInfo) {
                    Storage::disk('public')->delete($fileInfo['path']);
                }
            }

            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง']);
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
    public function download($id): \Symfony\Component\HttpFoundation\BinaryFileResponse|JsonResponse
    {
        try {
            $evidence = Evidence::findOrFail($id);

            if (!$evidence->path || !Storage::disk('public')->exists($evidence->path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            $filePath = Storage::disk('public')->path($evidence->path);
            return response()->download($filePath, $evidence->name);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get evidence by criteria.
     */
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
