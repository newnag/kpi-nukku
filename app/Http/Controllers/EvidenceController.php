<?php

namespace App\Http\Controllers;

use App\Models\Evidence;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // 10MB max
            'type' => 'required|string|max:100',
            'detail' => 'nullable|string',
            'status' => 'boolean',
            'criteria_id' => 'required|exists:criteria,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('evidence', $filename, 'public');

            $evidence = Evidence::create([
                'name' => $request->name,
                'path' => $path,
                'type' => $request->type,
                'detail' => $request->detail,
                'status' => $request->boolean('status', true),
                'criteria_id' => $request->criteria_id,
                'user_id' => Auth::id(),
            ]);

            $evidence->load(['criteria', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Evidence created successfully',
                'data' => $evidence
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create evidence',
                'error' => $e->getMessage()
            ], 500);
        }
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
