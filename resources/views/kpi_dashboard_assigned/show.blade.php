@extends('layouts.app')
@section('title', 'แดshboard')
@section('content')
    <div class="dashboard-container">
        <div class="card indicator-card">
            <!-- ชื่อหัวข้อ -->
            <h1 class="indicator-title">
                {{ $indicator->name }} ({{ $indicator->code }})
            </h1>

            <!-- Tabs -->
            <div class="indicator-tabs">
                <span class="tab ">{{ $indicator->category->standard->name ?? '-' }}</span>
                <span class="tab-divider">|</span>
                <span class="tab">{{ $indicator->category->name ?? '-' }}</span>
            </div>
            <hr class="tab-divider">

            <!-- ข้อมูล -->
            <div class="info-block">
                <div class="info-row">
                    <span class="label">หน่วยงานที่รับผิดชอบ:</span>
                    <span class="value">
                        @forelse($indicator->assignments as $assignment)
                            @if ($assignment->collectorUser)
                                <span class="chip">
                                    {{ $assignment->collectorUser->department->name }}
                                </span>
                            @endif
                        @empty
                            <span class="value">-</span>
                        @endforelse
                    </span>
                </div>

                <div class="info-row">
                    <span class="label">ผู้รับผิดชอบในการรวบรวม:</span>
                    @forelse($indicator->assignments as $assignment)
                        @if ($assignment->collectorUser)
                            <span class="chip">
                                {{ $assignment->collectorUser->name }}
                            </span>
                        @endif
                    @empty
                        <span class="value">-</span>
                    @endforelse
                </div>

                <div class="info-row">
                    <span class="label">สถานะตัวชี้วัด:</span>
                    @if ($indicator->status == 0)
                        <span class="status-chip orange">รอดำเนินการ</span>
                    @elseif ($indicator->status == 1)
                        <span class="status-chip gray">บันทึกร่าง</span>
                    @elseif ($indicator->status == 2)
                        <span class="status-chip blue">บันทึกจริง</span>
                    @elseif ($indicator->status == 3)
                        <span class="status-chip green">ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรการ</span>
                    @elseif ($indicator->status == 4)
                        <span class="status-chip red">ผลการดำเนินงานไม่ครบถ้วนตามเกณฑ์มาตรการ</span>
                    @else
                        <span class="status-chip">ไม่ทราบสถานะ</span>
                    @endif
                </div>
            </div>
            <hr class="section-divider">

            <div class="card ">
                <h2 class="card-title">คำอธิบายตัวชี้วัด</h2>
                <div class="description-box">
                    {!! $indicator->description ?? '-' !!}
                </div>
            </div>

            <div class="card">
                <h2 class="card-title">เกณฑ์การพิจารณา</h2>

                @forelse($indicator->criterias as $criteriaIndex => $criteria)
                    <div class="criteria-box" id="criteria-{{ $criteria->id }}">
                        <!-- ชื่อเกณฑ์ -->
                        <div class="criteria-header">
                            <div class="criteria-title">
                                {{ $criteria->sequence }}. {!! $criteria->name !!}
                            </div>
                            <div class="criteria-actions" x-data="{ open{{ $criteria->id }}: false }">
                                <!-- ปุ่มเปิด popup -->
                                <button @click="open{{ $criteria->id }} = true" class="btn-add">
                                    เพิ่มหลักฐาน <i class="fa fa-upload"></i>
                                </button>

                                <!-- Popup -->
                                <div x-show="open{{ $criteria->id }}"
                                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                                    x-cloak>
                                    <div class="bg-white w-full max-w-3xl rounded-lg shadow-lg p-6 relative">
                                        <!-- ปุ่มปิด -->
                                        <button @click="open{{ $criteria->id }} = false"
                                            class="absolute top-3 right-3 text-gray-500 hover:text-red-500">
                                            ✕
                                        </button>

                                        <form action="{{ route('evidences.store') }}" method="POST"
                                            enctype="multipart/form-data" id="evidence-form-{{ $criteria->id }}">
                                            @csrf
                                            <input type="hidden" name="criteria_id" value="{{ $criteria->id }}">

                                            <div class="evidence-container">
                                                <div class="evidence-containers">
                                                    <div class="header-containers">เพิ่มใหม่หลักฐาน</div>

                                                    <!-- ฟอร์มเพิ่มหลักฐาน -->
                                                    <div class="evidence-form">
                                                        <!-- ================= Upload Section ================= -->
                                                        <div class="upload-section">
                                                            <div class="upload-area upload-area-{{ $criteria->id }}"
                                                                data-criteria="{{ $criteria->id }}">
                                                                <div class="upload-icon">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="48"
                                                                        height="48" fill="none" viewBox="0 0 24 24"
                                                                        stroke="currentColor" style="color:#9ca3af;">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="1"
                                                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                                    </svg>
                                                                </div>
                                                                <p class="upload-text">วางไฟล์ของคุณที่นี่ หรือ
                                                                    คลิกเพื่อเลือกไฟล์</p>
                                                                <input type="file" id="fileInput-{{ $criteria->id }}"
                                                                    name="files[]" multiple
                                                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                                                    class="file-input-{{ $criteria->id }}"
                                                                    style="display:none;">
                                                            </div>
                                                            <div id="filesList-{{ $criteria->id }}"
                                                                class="files-list files-list-{{ $criteria->id }}"></div>
                                                        </div>

                                                        <!-- ================= URL Section ================= -->
                                                        <div class="url-section url-section-{{ $criteria->id }}"
                                                            data-criteria="{{ $criteria->id }}">
                                                            <div class="section-divider"></div>

                                                            {{-- แสดงค่าที่เคยกรอก (old) ทั้งหมดแบบล็อคแก้ไข + ปุ่มลบ --}}
                                                            @foreach (collect(old('additional_urls', [])) as $u)
                                                                @if ($u !== null && $u !== '')
                                                                    <div
                                                                        class="form-group url-row url-row-{{ $criteria->id }}-{{ $loop->index }}">
                                                                        <input type="url" name="additional_urls[]"
                                                                            class="form-input url-input locked"
                                                                            value="{{ $u }}" readonly
                                                                            tabindex="-1">
                                                                        <button type="button" class="remove-url-btn"
                                                                            aria-label="ลบ URL">
                                                                            <i data-lucide="x"
                                                                                style="width:16px;height:16px;"></i>
                                                                        </button>
                                                                    </div>
                                                                @endif
                                                            @endforeach

                                                            {{-- แถวสุดท้าย: ว่าง + ปุ่มเพิ่ม (แก้ได้เพียงช่องเดียวในหน้า) --}}
                                                            <div
                                                                class="form-group url-row url-row-new-{{ $criteria->id }}">
                                                                <input type="text" name="url_names[]"
                                                                    class="form-input url-name url-name-{{ $criteria->id }}"
                                                                    placeholder="ชื่อหลักฐาน URL">
                                                                <input type="url" name="additional_urls[]"
                                                                    class="form-input url-input url-input-{{ $criteria->id }}"
                                                                    placeholder="วาง URL เพิ่มเติม">
                                                                <button type="button"
                                                                    class="add-url-btn add-url-btn-{{ $criteria->id }}"
                                                                    aria-label="เพิ่ม URL">
                                                                    <i data-lucide="plus"
                                                                        style="width:16px;height:16px;"></i>
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <!-- ================= Details Section ================= -->
                                                        <div class="details-section">
                                                            <div class="section-title">รายละเอียดเพิ่มเติม</div>

                                                            {{-- ใช้ Trumbowyg บน textarea นี้ --}}
                                                            <textarea id="detailEditor-{{ $criteria->id }}" name="detail" class="detail-editor-{{ $criteria->id }}"
                                                                rows="6">{!! old('detail') !!}</textarea>
                                                        </div>

                                                        <!-- ================= Action Buttons ================= -->
                                                        <div class="action-buttons">
                                                            <button type="button" class="btn-secondary"
                                                                @click="open{{ $criteria->id }} = false">
                                                                <i data-lucide="undo-2" style="margin-right:6px;"></i>
                                                                กลับ
                                                            </button>
                                                            <button type="submit" class="btn-primary">
                                                                <i data-lucide="save" style="margin-right:6px;"></i>
                                                                บันทึก
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- คำอธิบายเกณฑ์ -->
                        @if ($criteria->description)
                            <div class="criteria-description">
                                {!! $criteria->description !!}
                            </div>
                        @endif

                        <!-- หลักฐานของเกณฑ์นี้ -->
                        <div class="evidence-list evidence-list-{{ $criteria->id }}">
                            @forelse($criteria->evidences as $evidence)
                                @php
                                    $type = strtolower($evidence->type ?? '');
                                    $name = strtolower($evidence->name ?? '');
                                @endphp

                                <div class="evidence-item" id="evidence-{{ $evidence->id }}">
                                    <span class="evidence-icon">
                                        @if (Str::endsWith($type, 'pdf'))
                                            <i data-lucide="file-text" style="color:#dc2626;"></i>
                                        @elseif (Str::endsWith($type, 'doc') || Str::endsWith($type, 'docx') || Str::endsWith($name, '.docx'))
                                            <i data-lucide="file-text" style="color:#2563eb;"></i>
                                        @elseif (Str::endsWith($type, 'ppt') || Str::endsWith($type, 'pptx') || Str::endsWith($name, '.pptx'))
                                            <i data-lucide="presentation" style="color:#eb7e25;"></i>
                                        @elseif (in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'image']))
                                            <i data-lucide="image" style="color:#16a34a;"></i>
                                        @elseif (Str::endsWith($type, 'xls') || Str::endsWith($type, 'xlsx') || Str::contains($name, '.xls'))
                                            <i data-lucide="file-spreadsheet" style="color:#059669;"></i>
                                        @elseif ($type === 'url')
                                            <i data-lucide="link" style="color:#9333ea;"></i>
                                        @elseif ($type === 'note')
                                            <i data-lucide="sticky-note" style="color:#f59e0b;"></i>
                                        @else
                                            <i data-lucide="file" style="color:#6b7280;"></i>
                                        @endif
                                    </span>

                                    <span class="evidence-name">
                                        @if ($evidence->type === 'url')
                                            @php
                                                // ถ้า path เก็บเป็น JSON หรือ array
                                                $urls = is_array($evidence->path)
                                                    ? $evidence->path
                                                    : json_decode($evidence->path, true);
                                                $firstUrl = $urls['urls'][0] ?? '#';
                                            @endphp
                                            <a href="{{ $firstUrl }}" target="_blank"
                                                class="text-blue-600 underline hover:text-blue-800">
                                                {{ $evidence->name }}
                                            </a>
                                        @else
                                            {{ $evidence->name }}
                                        @endif
                                    </span>

                                    <button class="btn-delete" data-id="{{ $evidence->id }}" title="ลบหลักฐาน">
                                        x
                                    </button>
                                </div>
                            @empty
                                <div class="evidence-empty">ยังไม่มีหลักฐานแนบ</div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">ยังไม่มีเกณฑ์การพิจารณา</p>
                @endforelse
            </div>

            <div class="card">
                <h2 class="card-title">วิธีการคำนวน</h2>
                <div class="criteria-box">
                    {!! $indicator->condition ?? '-' !!}
                </div>
            </div>

            @if ($indicator->variables->where('type', 'input')->isNotEmpty())
                <div class="card">
                    <h2 class="card-title">กรอกค่าตัวแปร</h2>

                    @php
                        // กรองเฉพาะ type = input (ตัดช่องว่าง/คอมม่าออก)
                        $inputVariables = $indicator->variables->filter(function ($v) {
                            return trim($v->type) === 'input';
                        });
                    @endphp

                    @forelse($inputVariables as $variable)
                        <div class="variable-row">
                            <label class="variable-label">
                                {{ $variable->label_name ?? $variable->variable_name }}
                            </label>
                            <input type="number" name="variables[{{ $variable->id }}]"
                                value="{{ old('variables.' . $variable->id, $variable->value) }}"
                                placeholder="กรุณากรอกร้อยละเป็นตัวเลข" class="variable-input">
                        </div>
                    @empty
                        <p class="text-gray-500">ยังไม่มีตัวแปรที่ต้องกรอกเอง</p>
                    @endforelse
                </div>
            @endif

            @if ($indicator->status == 3 && (isset($indicator->score_acc) || isset($indicator->max_score)))
                <div class="card_total total-score-card">
                    <h2 class="card-title">คะแนนรวม</h2>

                    <div class="total-score-row">
                        <span class="label">คะแนนที่ได้:</span>
                        <span class="score-value">
                            {{ number_format($indicator->score_acc ?? 0, 2) }}
                        </span>
                    </div>

                    <div class="total-score-row">
                        <span class="label">คะแนนเต็ม:</span>
                        <span class="score-max">
                            {{ number_format($indicator->max_score ?? 0, 2) }}
                        </span>
                    </div>
                </div>
            @endif

            @if (!empty($indicator->annotation))
                <div class="card annotation-card">
                    <div class="annotation-header">
                        <i class="fa fa-exclamation-triangle"></i>
                        หมายเหตุ
                    </div>
                    <div class="annotation-body">
                        {!! nl2br(e($indicator->annotation)) !!}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

    <!-- Trumbowyg core -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/ui/trumbowyg.min.css">
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/trumbowyg.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/langs/th.min.js"></script>

    <!-- Plugins -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/colors/ui/trumbowyg.colors.min.css">
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/colors/trumbowyg.colors.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/fontsize/trumbowyg.fontsize.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/fontfamily/trumbowyg.fontfamily.min.js">
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600;700&family=Kanit:wght@400;600&family=Sarabun:wght@400;600&display=swap"
        rel="stylesheet">

    <script>
        // กัน error ถ้าไม่ได้โหลด lucide
        if (window.lucide && typeof lucide.createIcons === 'function') {
            lucide.createIcons();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // เก็บ selectedFiles สำหรับแต่ละ criteria แยกจากกัน
            const fileHandlers = {};
            const editorInitialized = {}; // Track initialized editors

            /*** ---------- File Upload Handler Class ---------- ***/
            class FileUploadHandler {
                constructor(criteriaId) {
                    this.criteriaId = criteriaId;
                    this.uploadArea = document.querySelector(`.upload-area-${criteriaId}`);
                    this.fileInput = document.getElementById(`fileInput-${criteriaId}`);
                    this.filesList = document.getElementById(`filesList-${criteriaId}`);
                    this.selectedFiles = [];

                    // เก็บ instance ไว้ใน global object
                    fileHandlers[criteriaId] = this;

                    this.init();
                }

                init() {
                    if (!this.uploadArea || !this.fileInput || !this.filesList) {
                        console.warn(`FileUploadHandler: Missing elements for criteria ${this.criteriaId}`);
                        return;
                    }

                    // Drag & Drop events
                    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(evt => {
                        this.uploadArea.addEventListener(evt, e => {
                            e.preventDefault();
                            e.stopPropagation();
                        });
                    });

                    // Click to upload
                    this.uploadArea.addEventListener('click', () => {
                        this.fileInput.click();
                    });

                    // Drag over effect
                    this.uploadArea.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        this.uploadArea.classList.add('drag-over');
                    });

                    // Drag leave effect
                    this.uploadArea.addEventListener('dragleave', () => {
                        this.uploadArea.classList.remove('drag-over');
                    });

                    // Drop files
                    this.uploadArea.addEventListener('drop', (e) => {
                        e.preventDefault();
                        this.uploadArea.classList.remove('drag-over');
                        this.handleFiles(Array.from(e.dataTransfer.files || []));
                    });

                    // File input change
                    this.fileInput.addEventListener('change', (e) => {
                        this.handleFiles(Array.from(e.target.files || []));
                    });
                }

                handleFiles(files) {
                    files.forEach(file => {
                        const exists = this.selectedFiles.find(f =>
                            f.name === file.name && f.size === file.size && f.type === file.type
                        );
                        if (!exists) {
                            this.selectedFiles.push(file);
                            this.displayFile(file);
                        }
                    });
                    this.syncInputFiles();
                }

                displayFile(file) {
                    const el = document.createElement('div');
                    el.className = 'file-item';
                    el.innerHTML = `
                    <div class="file-icon">
                        <i data-lucide="file-text" style="width:20px;height:20px;color:#ef4444;"></i>
                    </div>
                    <span class="file-name" title="${file.name}">${file.name}</span>
                    <span class="file-size">${this.formatFileSize(file.size)}</span>
                    <button type="button" class="remove-file" aria-label="ลบไฟล์"
                            data-name="${encodeURIComponent(file.name)}" 
                            data-size="${file.size}"
                            data-criteria="${this.criteriaId}">
                        <i data-lucide="x" style="width:16px;height:16px;"></i>
                    </button>`;

                    this.filesList.appendChild(el);

                    // Add remove event listener
                    el.querySelector('.remove-file').addEventListener('click', (e) => {
                        const btn = e.currentTarget;
                        const name = decodeURIComponent(btn.getAttribute('data-name') || '');
                        const size = Number(btn.getAttribute('data-size') || 0);
                        const criteriaId = btn.getAttribute('data-criteria');

                        // ใช้ handler ที่ถูกต้องตาม criteriaId
                        const handler = fileHandlers[criteriaId];
                        if (handler) {
                            handler.selectedFiles = handler.selectedFiles.filter(f =>
                                !(f.name === name && f.size === size)
                            );
                            handler.syncInputFiles();
                        }

                        el.remove();
                    });

                    // Create icons
                    if (window.lucide?.createIcons) {
                        lucide.createIcons();
                    }
                }

                syncInputFiles() {
                    if (!this.fileInput) return;
                    const dt = new DataTransfer();
                    this.selectedFiles.forEach(file => dt.items.add(file));
                    this.fileInput.files = dt.files;
                }

                formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                }
            }

            /*** ---------- URL Handler Class ---------- ***/
            class URLHandler {
                constructor(criteriaId) {
                    this.criteriaId = criteriaId;
                    this.urlSection = document.querySelector(`.url-section-${criteriaId}`);
                    this.init();
                }

                init() {
                    if (!this.urlSection) {
                        console.warn(`URLHandler: Missing url-section for criteria ${this.criteriaId}`);
                        return;
                    }

                    // ถ้าไม่มีแถวที่แก้ไขได้เลย ให้เพิ่มแถวว่างพร้อม +
                    if (!this.urlSection.querySelector('input[type="url"]:not([readonly])')) {
                        this.appendNewEditableRow();
                    }

                    // Event delegation
                    this.urlSection.addEventListener('click', (e) => this.handleClick(e));
                    this.urlSection.addEventListener('keydown', (e) => this.handleKeydown(e));
                }

                handleClick(e) {
                    const addBtn = e.target.closest(`.add-url-btn-${this.criteriaId}`);
                    if (addBtn) {
                        const row = addBtn.closest('.url-row');
                        this.lockRowAndSwapButton(row);
                        this.appendNewEditableRow();
                        return;
                    }

                    const removeBtn = e.target.closest('.remove-url-btn');
                    if (removeBtn && removeBtn.closest(`.url-section-${this.criteriaId}`)) {
                        const row = removeBtn.closest('.url-row');
                        row?.remove();

                        // ถ้าไม่มีช่องที่แก้ไขได้แล้ว ให้สร้างใหม่
                        if (!this.urlSection.querySelector('input[type="url"]:not([readonly])')) {
                            this.appendNewEditableRow();
                        }
                    }
                }

                handleKeydown(e) {
                    if (e.key === 'Enter' && e.target.matches(`.url-input-${this.criteriaId}`)) {
                        e.preventDefault();
                        const row = e.target.closest('.url-row');
                        this.lockRowAndSwapButton(row);
                        this.appendNewEditableRow();
                    }
                }

                appendNewEditableRow() {
                    if (!this.urlSection) return;

                    const row = document.createElement('div');
                    const rowId = `url-row-${this.criteriaId}-${Date.now()}`;
                    row.className = `form-group url-row ${rowId}`;
                    row.innerHTML = `
                    <input type="text" name="url_names[]" 
                        class="form-input url-name url-name-${this.criteriaId}" 
                        placeholder="ชื่อหลักฐาน URL">
                    <input type="url" name="additional_urls[]" 
                        class="form-input url-input url-input-${this.criteriaId}" 
                        placeholder="วาง URL เพิ่มเติม">
                    <button type="button" 
                        class="add-url-btn add-url-btn-${this.criteriaId}" 
                        aria-label="เพิ่ม URL">
                        <i data-lucide="plus" style="width:16px;height:16px;"></i>
                    </button>`;

                    this.urlSection.appendChild(row);

                    if (window.lucide?.createIcons) {
                        lucide.createIcons();
                    }

                    row.querySelector(`input.url-input-${this.criteriaId}`)?.focus();
                }

                lockRowAndSwapButton(row) {
                    if (!row) return;

                    const input = row.querySelector(`input.url-input-${this.criteriaId}`);
                    if (!input || !input.value.trim()) return;

                    input.readOnly = true;
                    input.classList.add('locked');
                    input.setAttribute('tabindex', '-1');

                    const addBtn = row.querySelector(`.add-url-btn-${this.criteriaId}`);
                    if (addBtn) {
                        addBtn.classList.remove(`add-url-btn-${this.criteriaId}`);
                        addBtn.classList.add('remove-url-btn');
                        addBtn.setAttribute('aria-label', 'ลบ URL');
                        addBtn.innerHTML = `<i data-lucide="x" style="width:16px;height:16px;"></i>`;

                        if (window.lucide?.createIcons) {
                            lucide.createIcons();
                        }
                    }
                }
            }

            /*** ---------- Initialize Trumbowyg Editor Function ---------- ***/
            function initTrumbowyg(criteriaId) {
                // ตรวจสอบว่ามี jQuery และ Trumbowyg หรือไม่
                if (typeof $ === 'undefined' || typeof $.fn.trumbowyg === 'undefined') {
                    console.error('jQuery or Trumbowyg not loaded');
                    return false;
                }

                // ตรวจสอบว่า editor นี้ถูก initialize แล้วหรือไม่
                if (editorInitialized[criteriaId]) {
                    console.log(`Editor ${criteriaId} already initialized`);
                    return true;
                }

                const selector = `#detailEditor-${criteriaId}`;
                const $editor = $(selector);

                console.log(`Attempting to initialize editor for criteria ${criteriaId}`, $editor.length);

                if ($editor.length === 0) {
                    console.error(`Element ${selector} not found`);
                    return false;
                }

                // ตรวจสอบว่า element มองเห็นได้หรือไม่
                if ($editor.is(':hidden')) {
                    console.warn(`Element ${selector} is hidden`);
                    return false;
                }

                try {
                    // ถ้าเคย initialize แล้ว ให้ destroy ก่อน
                    if ($editor.data('trumbowyg')) {
                        $editor.trumbowyg('destroy');
                    }

                    $editor.trumbowyg({
                        lang: 'th',
                        btnsDef: {
                            fontfamily: {
                                dropdown: ['Prompt', 'Kanit', 'Sarabun', 'Arial', 'Times New Roman'],
                                hasIcon: false,
                                text: 'Font'
                            }
                        },
                        btns: [
                            ['viewHTML'],
                            ['undo', 'redo'],
                            ['formatting'],
                            ['fontfamily'],
                            ['fontsize'],
                            ['foreColor', 'backColor'],
                            ['strong', 'em', 'del', 'underline'],
                            ['link'],
                            ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                            ['unorderedList', 'orderedList'],
                            ['horizontalRule'],
                            ['removeformat'],
                            ['fullscreen']
                        ],
                        plugins: {
                            fontfamily: {},
                            fontsize: {},
                            colors: {
                                foreColorList: [
                                    '000000', 'FFFFFF', 'FF0000', '00FF00', '0000FF',
                                    '333333', '666666', '999999', 'CCCCCC',
                                    'FF6B6B', '4ECDC4', '45B7D1', 'FFA07A', '98D8C8'
                                ],
                                backColorList: [
                                    'FFFFFF', '000000', 'EFEFEF', 'F3F3F3',
                                    'FFF2CC', 'FFF2F2', 'F2F2FF', 'F2FFF2'
                                ]
                            }
                        },
                        autogrow: true
                    });

                    // ทำเครื่องหมายว่า editor นี้ถูก initialize แล้ว
                    editorInitialized[criteriaId] = true;
                    console.log(`✅ Trumbowyg initialized successfully for criteria ${criteriaId}`);
                    return true;

                } catch (error) {
                    console.error(`❌ Error initializing Trumbowyg for criteria ${criteriaId}:`, error);
                    return false;
                }
            }

            /*** ---------- Setup Event Listeners for Popup Open ---------- ***/
            function setupPopupEventListeners() {
                @foreach ($indicator->criterias as $criteria)
                    // หา button ที่เปิด popup
                    const openButton{{ $criteria->id }} = document.querySelector(
                        '[\\@click="open{{ $criteria->id }} = true"]');
                    if (openButton{{ $criteria->id }}) {
                        openButton{{ $criteria->id }}.addEventListener('click', function() {
                            console.log('Opening popup for criteria {{ $criteria->id }}');

                            // รอให้ Alpine.js แสดง popup ก่อน
                            setTimeout(() => {
                                const popup = document.querySelector(
                                    '[x-show="open{{ $criteria->id }}"]');
                                if (popup && popup.style.display !== 'none') {
                                    console.log('Popup is visible, initializing editor...');
                                    initTrumbowyg({{ $criteria->id }});
                                } else {
                                    // ลองอีกครั้งหลัง 500ms
                                    setTimeout(() => {
                                        console.log(
                                            'Second attempt to initialize editor...');
                                        initTrumbowyg({{ $criteria->id }});
                                    }, 500);
                                }
                            }, 100);
                        });
                    } else {
                        console.warn('Open button not found for criteria {{ $criteria->id }}');
                    }
                @endforeach
            }

            /*** ---------- Initialize handlers for each criteria ---------- ***/
            @foreach ($indicator->criterias as $criteria)
                // Initialize file upload handler
                new FileUploadHandler({{ $criteria->id }});
                // Initialize URL handler
                new URLHandler({{ $criteria->id }});
            @endforeach

            // Setup popup event listeners
            setupPopupEventListeners();

            /*** ---------- Delete Evidence Handler ---------- ***/
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute("content") : "";

            document.querySelectorAll(".btn-delete").forEach(btn => {
                btn.addEventListener("click", function() {
                    const id = this.getAttribute("data-id");
                    if (!confirm("คุณแน่ใจว่าต้องการลบหลักฐานนี้หรือไม่?")) return;

                    fetch(`/evidences/${id}`, {
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": csrfToken,
                                "Content-Type": "application/json",
                                "Accept": "application/json"
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById(`evidence-${id}`)?.remove();
                                alert("ลบหลักฐานเรียบร้อยแล้ว");
                            } else {
                                alert("เกิดข้อผิดพลาดในการลบหลักฐาน");
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            alert("เกิดข้อผิดพลาดในการลบหลักฐาน");
                        });
                });
            });

            /*** ---------- Debug Information ---------- ***/
            console.log('Available criteria IDs:', [
                @foreach ($indicator->criterias as $criteria)
                    {{ $criteria->id }},
                @endforeach
            ]);
            console.log('jQuery loaded:', typeof $ !== 'undefined');
            console.log('Trumbowyg loaded:', typeof $.fn.trumbowyg !== 'undefined');
        });
    </script>


    <style>
        /* ---- Base ---- */
        :root {
            --blue: #398ECA;
            --blue-600: #2f7db2;
            --text: #1f2937;
            /* gray-800 */
            --muted: #6b7280;
            /* gray-500 */
            --border: #e5e7eb;
            /* gray-200 */
            --bg: #ffffff;
            --shadow: 0 8px 24px rgba(0, 0, 0, .08);
            --radius: 16px;
            --radius-sm: 10px;
        }

        .card {
            background: var(--bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 24px;
            /* max-width: 920px; */
            /* ปรับตามหน้า */
            margin: 16px;
            border: 1px solid #f3f4f6;
        }

        .card_total {
            background: var(--bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 24px;
            /* max-width: 920px; */
            /* ปรับตามหน้า */
            margin: 16px;
            border: 1px solid #f3f4f6;
        }

        .card-title {
            font-size: 18px;
            /* font-weight: 700; */
            color: var(--blue);
            margin: 0 0 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
            padding-left: 10px;
        }

        .card-title::before {
            content: "";
            width: 4px;
            height: 20px;
            border-radius: 8px;
            background: var(--blue);
            position: absolute;
            left: 0;
            top: 2px;
            opacity: .25;
        }

        .section-divider {
            position: relative;
            left: -29px;
            width: calc(100% + 57px);
            /* right: 30px; */
            border: none;
            border-bottom: 3px solid #C3D8E8;
            /* เทาอ่อน */
            margin: 24px 0;
        }

        .description-box {
            background: #f9fafb;
            /* gray-50 */
            border: 1px solid #e5e7eb;
            /* gray-200 */
            border-radius: 12px;
            padding: 16px 20px;
            font-size: 14px;
            line-height: 1.7;
            color: #374151;
            /* gray-800 */
        }

        .description-box p {
            margin-bottom: 12px;
        }

        .description-box ul {
            margin: 8px 0 8px 20px;
            list-style: disc;
        }

        .criteria-box {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .criteria-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .criteria-title {
            font-weight: 600;
            font-size: 14px;
            color: #1f2937;
        }

        .criteria-description {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 12px;
        }

        .criteria-actions {
            display: flex;
            align-items: stretch;
            gap: 8px;
            flex-direction: column;
        }

        .btn-add {
            background: #EBF7FF;
            border: 1px solid #398ECA;
            border-radius: 20px;
            font-size: 12px;
            padding: 6px 12px;
            cursor: pointer;
            color: #398ECA;
            text-decoration: none;
            /* กันไม่ให้มีขีดเส้นใต้ */
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }

        .btn-add:hover {
            background: #dbeafe;
        }

        .btn-delete {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #dc2626;
            /* แดงอ่อน */
            transition: color 0.2s, transform 0.1s;
        }

        .btn-delete:hover {
            color: #b91c1c;
            /* แดงเข้ม */
            transform: scale(1.1);
        }

        .evidence-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .evidence-item {
            background: #f0f9ff;
            border-radius: 8px;
            padding: 6px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            color: #374151;
        }

        .evidence-icon {
            margin-right: 6px;
        }

        .dropdown {
            position: relative;
            display: inline-block;

        }

        .dropdown-toggle {
            background: #ffffff;
            border: 1.5px solid #398ECA;
            border-radius: 20px;
            padding: 6px 28px 6px 12px;
            font-size: 13px;
            color: #398ECA;
            cursor: pointer;
            outline: none;
            text-align: left;
            position: relative;
        }

        .dropdown-toggle .arrow {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 6px solid #398ECA;
            /* ลูกศรลง */
            pointer-events: none;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            list-style: none;
            padding: 6px 0;
            margin-top: 4px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            z-index: 9999;
            /* ป้องกันถูกบัง */
        }

        .dropdown-menu li {
            padding: 8px 12px;
            cursor: pointer;
            font-size: 13px;
            color: #374151;
            transition: background 0.2s;
        }

        .dropdown-menu li:hover {
            background: #EBF7FF;
            color: #398ECA;
        }

        .dropdown-menu.show {
            display: block;
        }

        .file-icon {
            flex-shrink: 0;
        }
    </style>
    <style>
        :root {
            --blue: #398ECA;
            --green: #22c55e;
            --orange: #fbbf24;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-600: #4b5563;
            --gray-800: #1f2937;
            --radius: 14px;
            --shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .annotation-card {
            margin-top: 20px;
            padding: 16px 20px;
            background: #fffbea;
            /* เหลืองอ่อน */
            border: 1px solid #fde68a;
            /* เส้นกรอบเหลือง */
            border-radius: 12px;
            color: #92400e;
            /* น้ำตาลเข้ม */
        }

        .annotation-header {
            font-weight: 600;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            color: #b45309;
            /* เหลือง-น้ำตาล */
        }

        .annotation-body {
            font-size: 14px;
            line-height: 1.6;
            color: #78350f;
        }


        .total-score-card {
            margin-top: 20px;
            padding: 20px;
            background: #deedfb;
            border-radius: 16px;
            /* box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); */
            /* border: 1px solid #e5e7eb; */
            text-align: center;
        }

        .total-score-row {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 8px 0;
            font-size: 16px;
        }

        .total-score-row .label {
            font-weight: 600;
            color: #374151;
        }

        .score-value {
            color: #2563eb;
            font-weight: 700;
            font-size: 20px;
        }

        .score-max {
            color: #10b981;
            font-weight: 700;
            font-size: 20px;
        }

        .variable-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f9f9f9;
            /* เทาอ่อน */
            padding: 10px 16px;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .variable-label {
            font-weight: 600;
            font-size: 14px;
            color: #374151;
            /* gray-700 */
        }

        .variable-input {
            border: 1px solid #e5e7eb;
            /* gray-200 */
            border-radius: 6px;
            padding: 6px 10px;
            width: 300px;
            text-align: center;
            font-size: 14px;
            background: #fff;
        }


        .dashboard-container {
            max-width: 960px;
            margin: 0 auto;
            padding: 24px;
        }

        /* Card */
        .card.indicator-card {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 28px;
            border: 1px solid var(--gray-100);
        }

        /* Title */
        .indicator-title {
            font-size: 26px;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 16px;
        }

        /* Tabs */
        .indicator-tabs {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .tab {
            color: #6b7280;
            /* gray-500 */
            cursor: default;
        }



        .tab-divider {
            color: #d1d5db;
            /* gray-300 */
        }

        hr.tab-divider {
            border: none;
            border-bottom: 2px solid #bbc8e0;
            margin: 0 0 16px 0;
        }


        /* Info block */
        .info-block {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .info-row {
            font-size: 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .info-row .label {
            font-weight: 600;
            color: #717d83;
            margin-right: 6px;
        }

        .info-row .value {
            color: var(--gray-600);
            display: inline-block;
            background: #EBF7FF;
            border-radius: 16px;
            font-size: 13px;
        }

        /* Chips */
        .chip {
            display: inline-block;
            background: #EBF7FF;

            border-radius: 16px;
            padding: 4px 12px;
            font-size: 13px;
            color: #858e95;
        }

        /* Status Chips */
        .status-chip {
            display: inline-block;
            border-radius: 16px;
            padding: 4px 12px;
            font-size: 13px;
            color: #fff;
        }

        /* โทนส้มพาสเทล */
        .status-chip.orange {
            background: #FFD1A8;
            color: #1f2937;
        }

        /* โทนเขียวพาสเทล */
        .status-chip.green {
            background: #A8FFBD;
            color: #1f2937;
        }

        /* โทนน้ำเงินพาสเทล */
        .status-chip.blue {
            background: #A8D4FF;
            color: #1f2937;
        }

        /* โทนเทาพาสเทล */
        .status-chip.gray {
            background: #E5E7EB;
            /* gray-200 */
            color: #1f2937;
        }

        .status-chip.red {
            background: #FFA8A8;
            /* red-500 */
            color: #858e95;
        }
    </style>
    <style>
        .trumbowyg-editor ol,
        .trumbowyg-editor ul {
            list-style-position: inside;
            padding-left: 0;
        }

        .trumbowyg-editor ol,
        .trumbowyg-editor ul {
            list-style-position: inside;
            /* สำคัญ */
            padding-left: 0;
            /* ตัดระยะเว้นซ้ายของลิสต์เดิม */
        }

        /* ให้กล่อง Trumbowyg กลมกลืนกับธีมเดิม */
        .trumbowyg-box {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }

        .trumbowyg-editor,
        .trumbowyg-textarea {
            font-size: 16px;
            min-height: 160px;
        }

        .trumbowyg-box.trumbowyg-editor-visible .trumbowyg-editor:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .1);
            border-color: #3b82f6;
        }

        .evidence-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .evidence-containers {
            width: 100%;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .1);
            overflow: hidden;
        }

        .header-containers {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px 20px;
            font-weight: 700;
            font-size: 30px;
            background: linear-gradient(90deg, #a9c6ff 0%, #fff3d4 100%);
            color: #222;
        }

        .evidence-form {
            padding: 40px;
        }

        /* Upload */
        .upload-section {
            margin-bottom: 30px;
        }

        .upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            background: #f9fafb;
            cursor: pointer;
            transition: .3s;
            margin-bottom: 20px;
        }

        .upload-area:hover,
        .upload-area.drag-over {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .upload-icon {
            margin-bottom: 16px;
        }

        .upload-text {
            color: #6b7280;
            margin: 0;
            font-size: 16px;
        }

        .files-list {
            max-height: 200px;
            overflow-y: auto;
        }

        .file-item {
            display: flex;
            align-items: center;
            padding: 12px;
            background: #f3f4f6;
            border-radius: 6px;
            margin-bottom: 8px;
            gap: 12px;
        }

        .file-name {
            flex: 1;
            font-size: 14px;
            color: #374151;
            word-break: break-all;
        }

        .file-size {
            font-size: 12px;
            color: #6b7280;
            flex-shrink: 0;
        }

        .remove-file {
            background: none;
            border: none;
            cursor: pointer;
            color: #6b7280;
            padding: 4px;
            border-radius: 4px;
            flex-shrink: 0;
        }

        .remove-file:hover {
            background: #e5e7eb;
            color: #ef4444;
        }

        /* URL */
        .url-section {
            margin-bottom: 30px;
        }

        .section-divider {
            position: relative;
            text-align: center;
            color: #6b7280;
            margin: 20px 0;
            font-size: 14px;
            pointer-events: none;
        }

        .section-divider:before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e5e7eb;
            z-index: 1;
        }

        .section-divider:after {
            content: 'หรือ';
            background: #fff;
            padding: 0 15px;
            position: relative;
            z-index: 2;
        }

        .form-group {
            margin-bottom: 16px;
            display: flex;
            gap: 8px;
        }

        .form-input {
            flex: 1;
            padding: 12px 16px;
            border: 2px solid #d1d5db;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color .3s;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .1);
        }

        .url-input {
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1'/%3E%3C/svg%3E") no-repeat 16px center;
            background-size: 20px;
            padding-left: 48px;
        }

        .url-row {
            display: flex;
            gap: 8px;
            align-items: stretch;
            flex-wrap: nowrap;
        }

        .url-row .form-input {
            flex: 1;
            min-width: 0;
        }

        .add-url-btn,
        .remove-url-btn {
            width: 44px;
            min-width: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #d1d5db;
            border-radius: 8px;
            background: #f3f4f6;
            cursor: pointer;
            transition: .2s;
        }

        .add-url-btn:hover {
            background: #e5e7eb;
            border-color: #9ca3af;
        }

        .remove-url-btn {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .remove-url-btn:hover {
            background: #fee2e2;
            border-color: #fca5a5;
        }

        .form-input.locked {
            background: #f3f4f6;
            color: #6b7280;
            pointer-events: none;
        }

        /* Details / Buttons */
        .details-section {
            margin-bottom: 30px;
        }

        .section-title {
            color: #374151;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .editor-toolbar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-bottom: none;
            border-radius: 8px 8px 0 0;
            flex-wrap: wrap;
        }

        .font-select,
        .size-select {
            padding: 4px 8px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            background: #fff;
            font-size: 14px;
        }

        .toolbar-btn {
            padding: 6px 8px;
            background: none;
            border: 1px solid transparent;
            border-radius: 4px;
            cursor: pointer;
            transition: .2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toolbar-btn:hover {
            background: #e5e7eb;
        }

        .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 8px 8px;
            font-size: 16px;
            font-family: Arial, sans-serif;
            resize: vertical;
            min-height: 120px;
        }

        .form-textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .1);
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .btn-primary,
        .btn-secondary {
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: .3s;
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #3b82f6;
            color: #fff;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-secondary {
            background: #fff;
            color: #374151;
            border: 2px solid #d1d5db;
        }

        .btn-secondary:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }

        @media (max-width:768px) {
            .evidence-form {
                padding: 20px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .form-group {
                flex-direction: column;
            }

            .add-url-btn,
            .remove-url-btn {
                align-self: flex-start;
            }
        }
    </style>
@endsection
