@props([
    'criteria', // Criteria model (expects ->id)
    'indicator', // Indicator model (expects ->status)
    'action' => route('evidences.store'), // POST target
])

@php
    $cid = $criteria->id;
    $disabled = (int) $indicator->status === 2; // ปิดเมื่อบันทึกจริงแล้ว
@endphp

<div class="criteria-actions" x-data="{ open{{ $cid }}: false }">
    {{-- Trigger --}}
    <button type="button" class="btn-add ev-modal-open-btn" data-criteria-id="{{ $cid }}"
        @click="open{{ $cid }} = true" {{ $disabled ? 'disabled' : '' }}>
        เพิ่มหลักฐาน <i class="fa fa-upload"></i>
    </button>

    {{-- Modal --}}
    <div x-show="open{{ $cid }} && {{ (int) $indicator->status }} != 2"
        class="fixed inset-0 bg-opacity-50 flex items-center justify-center z-50" x-cloak style="display:none;">
        <!-- ปิด -->
        <button type="button" @click="open{{ $cid }} = false"
            class="absolute top-3 right-3 text-gray-500 hover:text-red-500">
            ✕
        </button>

        <form action="{{ $action }}" method="POST" enctype="multipart/form-data"
            id="evidence-form-{{ $cid }}">
            @csrf
            <input type="hidden" name="criteria_id" value="{{ $cid }}">

            <div class="evidence-containers">
                <div class="header-containers">เพิ่มใหม่หลักฐาน</div>

                <div class="evidence-form">
                    {{-- ================= Upload Section ================= --}}
                    <div class="upload-section">
                        <div class="upload-area upload-area-{{ $cid }}" data-criteria="{{ $cid }}">
                            <div class="upload-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" style="color:#9ca3af;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <p class="upload-text">วางไฟล์ของคุณที่นี่ หรือ คลิกเพื่อเลือกไฟล์</p>
                            <input type="file" id="fileInput-{{ $cid }}" name="files[]" multiple
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.ppt,.pptx,.xls,.xlsx"
                                class="file-input-{{ $cid }}" style="display:none;">
                        </div>

                        <div id="filesList-{{ $cid }}" class="files-list files-list-{{ $cid }}">
                        </div>
                    </div>

                    {{-- ================= URL Section ================= --}}
                    <div class="url-section url-section-{{ $cid }}" data-criteria="{{ $cid }}">
                        <div class="section-divider"></div>

                        {{-- แถวใหม่สำหรับเพิ่ม URL --}}
                        <div class="form-group url-row url-row-new-{{ $cid }}">
                            <input type="text" name="url_names[]"
                                class="form-input url-name url-name-{{ $cid }}" placeholder="ชื่อหลักฐาน URL">
                            <input type="url" name="additional_urls[]"
                                class="form-input url-input url-input-{{ $cid }}"
                                placeholder="วาง URL เพิ่มเติม">
                            <button type="button" class="add-url-btn add-url-btn-{{ $cid }}"
                                aria-label="เพิ่ม URL">
                                <i data-lucide="plus" style="width:16px;height:16px;"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ================= Details Section ================= --}}
                    <div class="details-section">
                        <div class="section-title">รายละเอียดเพิ่มเติม</div>
                        <textarea id="detailEditor-{{ $cid }}" name="detail" class="detail-editor-{{ $cid }}"
                            rows="6">{!! old('detail') !!}</textarea>
                    </div>

                    {{-- ================= Action Buttons ================= --}}
                    <div class="action-buttons">
                        <button type="button" class="btn-secondary" @click="open{{ $cid }} = false">
                            <i data-lucide="undo-2" style="margin-right:6px;"></i> กลับ
                        </button>
                        <button type="submit" class="btn-primary">
                            <i data-lucide="save" style="margin-right:6px;"></i> บันทึก
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
