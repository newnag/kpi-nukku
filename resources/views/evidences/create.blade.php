@extends('layouts.app')
@section('title', 'เพิ่มใหม่หลักฐาน')
@section('content')

    <div class="evidence-container">
        <div class="evidence-containers">
            <div class="header-containers">เพิ่มใหม่หลักฐาน</div>

            <!-- ฟอร์มเพิ่มหลักฐาน -->
            <div class="evidence-form">
                <form action="{{ route('evidences.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- ================= Upload Section ================= -->
                    <div class="upload-section">
                        <div class="upload-area" id="uploadArea">
                            <div class="upload-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" style="color:#9ca3af;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <p class="upload-text">วางไฟล์ของคุณที่นี่ หรือ คลิกเพื่อเลือกไฟล์</p>
                            <input type="file" id="fileInput" name="files[]" multiple
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display:none;">
                        </div>
                        <div id="filesList" class="files-list"></div>
                    </div>

                    <!-- ================= URL Section ================= -->
                    <div class="url-section" id="urlSection">
                        <div class="section-divider"></div>

                        {{-- แสดงค่าที่เคยกรอก (old) ทั้งหมดแบบล็อกแก้ไข + ปุ่มลบ --}}
                        @foreach (collect(old('additional_urls', [])) as $u)
                            @if ($u !== null && $u !== '')
                                <div class="form-group url-row">
                                    <input type="url" name="additional_urls[]" class="form-input url-input locked"
                                        value="{{ $u }}" readonly tabindex="-1">
                                    <button type="button" class="remove-url-btn" aria-label="ลบ URL">
                                        <i data-lucide="x" style="width:16px;height:16px;"></i>
                                    </button>
                                </div>
                            @endif
                        @endforeach

                        {{-- แถวสุดท้าย: ว่าง + ปุ่มเพิ่ม (แก้ได้เพียงช่องเดียวในหน้า) --}}
                        <div class="form-group url-row">
                            <input type="url" name="additional_urls[]" class="form-input url-input"
                                placeholder="วาง URL เพิ่มเติม">
                            <button type="button" class="add-url-btn" aria-label="เพิ่ม URL">
                                <i data-lucide="plus" style="width:16px;height:16px;"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ================= Details Section ================= -->
                    <div class="details-section">
                        <div class="section-title">รายละเอียดเพิ่มเติม</div>

                        {{-- ใช้ Trumbowyg บน textarea นี้ --}}
                        <textarea id="detailEditor" name="detail" rows="6">
    {!! old('detail') !!}
  </textarea>
                    </div>
                    <!-- ================= Action Buttons ================= -->
                    <div class="action-buttons">
                        <button type="button" class="btn-secondary" onclick="window.history.back()">
                            <i data-lucide="undo-2" style="margin-right:6px;"></i> กลับ
                        </button>
                        <button type="submit" class="btn-primary">
                            <i data-lucide="save" style="margin-right:6px;"></i> บันทึก
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

    <!-- Trumbowyg core -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/ui/trumbowyg.min.css">
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/trumbowyg.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/langs/th.min.js"></script>

    <!-- Plugins ที่ใช้: colors, fontsize, fontfamily -->
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


    <!-- ================= Script ================= -->
    <script>
        // กัน error ถ้าไม่ได้โหลด lucide
        if (window.lucide && typeof lucide.createIcons === 'function') {
            lucide.createIcons();
        }

        document.addEventListener('DOMContentLoaded', function() {
            /*** ---------- File Upload ---------- ***/
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('fileInput');
            const filesList = document.getElementById('filesList');
            let selectedFiles = [];

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(evt => {
                window.addEventListener(evt, e => {
                    e.preventDefault();
                    e.stopPropagation();
                });
            });

            if (uploadArea) {
                uploadArea.addEventListener('click', () => fileInput?.click());
                uploadArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    uploadArea.classList.add('drag-over');
                });
                uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('drag-over'));
                uploadArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    uploadArea.classList.remove('drag-over');
                    handleFiles(Array.from(e.dataTransfer.files || []));
                });
            }
            fileInput?.addEventListener('change', (e) => handleFiles(Array.from(e.target.files || [])));

            function handleFiles(files) {
                files.forEach(file => {
                    const exists = selectedFiles.find(f => f.name === file.name && f.size === file.size && f
                        .type === file.type);
                    if (!exists) {
                        selectedFiles.push(file);
                        displayFile(file);
                    }
                });
                syncInputFiles();
            }

            function displayFile(file) {
                const el = document.createElement('div');
                el.className = 'file-item';
                el.innerHTML = `
        <div class="file-icon"><i data-lucide="file-text" style="width:20px;height:20px;color:#ef4444;"></i></div>
        <span class="file-name" title="${file.name}">${file.name}</span>
        <span class="file-size">${formatFileSize(file.size)}</span>
        <button type="button" class="remove-file" aria-label="ลบไฟล์"
                data-name="${encodeURIComponent(file.name)}" data-size="${file.size}">
          <i data-lucide="x" style="width:16px;height:16px;"></i>
        </button>`;
                filesList?.appendChild(el);

                el.querySelector('.remove-file')?.addEventListener('click', () => {
                    const n = decodeURIComponent(el.querySelector('.remove-file').getAttribute(
                        'data-name') || '');
                    const s = Number(el.querySelector('.remove-file').getAttribute('data-size') || 0);
                    selectedFiles = selectedFiles.filter(f => !(f.name === n && f.size === s));
                    el.remove();
                    syncInputFiles();
                });

                if (window.lucide?.createIcons) lucide.createIcons();
            }

            function syncInputFiles() {
                if (!fileInput) return;
                const dt = new DataTransfer();
                selectedFiles.forEach(file => dt.items.add(file));
                fileInput.files = dt.files;
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024,
                    sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            /*** ---------- URL Add / Remove (แถวว่างมี +, แถวที่มีค่า = ล็อก + x) ---------- ***/
            const urlSection = document.getElementById('urlSection');

            // ถ้าไม่มีแถวที่แก้ได้เลย (เช่น old ทั้งหมด) ให้เพิ่มแถวว่างพร้อม +
            if (urlSection && !urlSection.querySelector('input[type="url"]:not([readonly])')) {
                appendNewEditableRow();
            }

            // click ปุ่ม +
            urlSection?.addEventListener('click', (e) => {
                const addBtn = e.target.closest?.('.add-url-btn');
                if (addBtn) {
                    const row = addBtn.closest('.url-row');
                    lockRowAndSwapButton(row);
                    appendNewEditableRow();
                    return;
                }
                // click ปุ่ม x
                const removeBtn = e.target.closest?.('.remove-url-btn');
                if (removeBtn) {
                    const row = removeBtn.closest('.url-row');
                    row?.remove();
                    // ถ้าไม่มีช่องที่แก้ได้แล้ว ให้สร้างใหม่ 1 ช่อง
                    if (!urlSection.querySelector('input[type="url"]:not([readonly])')) {
                        appendNewEditableRow();
                    }
                }
            });

            // กด Enter ในช่อง = ทำงานเหมือนกด +
            urlSection?.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && e.target.matches('input[type="url"]')) {
                    e.preventDefault();
                    const row = e.target.closest('.url-row');
                    lockRowAndSwapButton(row);
                    appendNewEditableRow();
                }
            });

            function appendNewEditableRow() {
                if (!urlSection) return;
                const row = document.createElement('div');
                row.className = 'form-group url-row';
                row.innerHTML = `
        <input type="url" name="additional_urls[]" class="form-input url-input" placeholder="วาง URL เพิ่มเติม">
        <button type="button" class="add-url-btn" aria-label="เพิ่ม URL">
          <i data-lucide="plus" style="width:16px;height:16px;"></i>
        </button>`;
                urlSection.appendChild(row);
                if (window.lucide?.createIcons) lucide.createIcons();
                row.querySelector('input[type="url"]')?.focus();
            }

            function lockRowAndSwapButton(row) {
                if (!row) return;
                const input = row.querySelector('input[type="url"]');
                if (!input) return;
                if (!input.value.trim()) return; // ไม่ล็อกช่องว่าง

                input.readOnly = true;
                input.classList.add('locked');
                input.setAttribute('tabindex', '-1');

                const addBtn = row.querySelector('.add-url-btn');
                if (addBtn) {
                    addBtn.classList.remove('add-url-btn');
                    addBtn.classList.add('remove-url-btn');
                    addBtn.setAttribute('aria-label', 'ลบ URL');
                    addBtn.innerHTML = `<i data-lucide="x" style="width:16px;height:16px;"></i>`;
                    if (window.lucide?.createIcons) lucide.createIcons();
                }
            }
        });
    </script>
    <script>
        (function($) {
            $(function() {
                if (!$.fn.trumbowyg) return;

                $('#detailEditor').trumbowyg({
                    lang: 'th',
                    autogrow: true,
                    minimalLinks: true,
                    removeformatPasted: true,
                    btns: [
                        ['viewHTML'],
                        ['undo', 'redo'],
                        ['formatting'],
                        ['strong', 'em', 'underline', 'del'],
                        ['link'],
                        ['foreColor', 'backColor'],
                        ['fontfamily', 'fontsize'], // ← ใช้ plugin fontfamily + fontsize
                        ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                        ['unorderedList', 'orderedList'],
                        ['horizontalRule'],
                        ['removeformat']
                    ],
                    plugins: {
                        fontsize: {
                            sizeList: ['12px', '14px', '16px', '18px', '20px', '24px']
                        },
                        fontfamily: {
                            fontList: [{
                                    name: 'Arial',
                                    family: 'Arial, Helvetica, sans-serif'
                                },
                                {
                                    name: 'Times New Roman',
                                    family: '"Times New Roman", Times, serif'
                                },
                                {
                                    name: 'Helvetica',
                                    family: 'Helvetica, Arial, sans-serif'
                                },
                                {
                                    name: 'Tahoma',
                                    family: 'Tahoma, Geneva, sans-serif'
                                },
                                {
                                    name: 'Prompt',
                                    family: '"Prompt", sans-serif'
                                }, // ไทยสวย
                                {
                                    name: 'Kanit',
                                    family: '"Kanit", sans-serif'
                                }, // ไทยสวย
                                {
                                    name: 'Sarabun',
                                    family: '"Sarabun", sans-serif'
                                } // ไทยราชการ
                            ]
                        }
                    }
                });
            });
        })(window.jQuery);
    </script>

    <!-- ================= Styles ================= -->
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
