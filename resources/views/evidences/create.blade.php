@extends('layouts.app')
@section('title', 'เพิ่มใหม่หลักฐาน')
@section('content')

    <div class="evidence-container">
        <div class="evidence-containers">
            <div class="header-containers">
                เพิ่มใหม่หลักฐาน
            </div>

            <!-- ฟอร์มเพิ่มหลักฐาน -->
            <div class="evidence-form">
                <form action="{{ route('evidences.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- File Upload Section -->
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
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display: none;">
                        </div>

                        <!-- Selected Files List -->
                        <div id="filesList" class="files-list"></div>
                    </div>

                    <!-- URL Section -->
                    <div class="url-section" id="urlSection">
                        <div class="section-divider"></div>

                        <div class="form-group url-row">
                            <input type="url" name="additional_urls[]" class="form-input"
                                placeholder="ท่านสามารถกรอก URL" value="{{ old('additional_url') }}">
                            <button type="button" class="add-url-btn" aria-label="เพิ่ม URL">
                                <i data-lucide="plus" style="width:16px;height:16px;"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Details Section -->
                    <div class="details-section">
                        <div class="section-title">รายละเอียดเพิ่มเติม</div>

                        <div class="form-group">
                            <div class="editor-toolbar">
                                <select class="font-select">
                                    <option>Arial</option>
                                    <option>Times New Roman</option>
                                    <option>Helvetica</option>
                                </select>

                                <select class="size-select">
                                    <option>14</option>
                                    <option>12</option>
                                    <option>16</option>
                                    <option>18</option>
                                </select>

                                <button type="button" class="toolbar-btn" id="boldBtn">
                                    <i data-lucide="bold" style="width: 16px; height: 16px;"></i>
                                </button>

                                <button type="button" class="toolbar-btn" id="italicBtn">
                                    <i data-lucide="italic" style="width: 16px; height: 16px;"></i>
                                </button>

                                <button type="button" class="toolbar-btn" id="underlineBtn">
                                    <i data-lucide="underline" style="width: 16px; height: 16px;"></i>
                                </button>

                                <button type="button" class="toolbar-btn" id="strikeBtn">
                                    <i data-lucide="strikethrough" style="width: 16px; height: 16px;"></i>
                                </button>

                                <div class="color-picker">
                                    <input type="color" id="textColor" value="#0066cc">
                                </div>

                                <button type="button" class="toolbar-btn" id="linkBtn">
                                    <i data-lucide="link" style="width: 16px; height: 16px;"></i>
                                </button>
                            </div>

                            <textarea name="detail" class="form-textarea" placeholder="กรอกรายการหัวข้อ" rows="6">{{ old('detail') }}</textarea>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button type="button" class="btn-secondary" onclick="window.history.back()">
                            <i data-lucide="undo-2" style="margin-right: 6px;"></i>
                            กลับ
                        </button>
                        <button type="submit" class="btn-primary">
                            <i data-lucide="save" style="margin-right: 6px;"></i>
                            บันทึก
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

  <script>
  // กัน error ถ้าไม่ได้โหลด lucide
  if (window.lucide && typeof lucide.createIcons === 'function') {
    lucide.createIcons();
  }

  document.addEventListener('DOMContentLoaded', function () {
    /*** ------------------- File Upload ------------------- ***/
    const uploadArea = document.getElementById('uploadArea');
    const fileInput  = document.getElementById('fileInput');
    const filesList  = document.getElementById('filesList');
    let selectedFiles = [];

    // กันไฟล์ถูกเปิดแทนการอัปโหลดทั้งหน้า
    ['dragenter','dragover','dragleave','drop'].forEach(evt => {
      window.addEventListener(evt, e => {
        e.preventDefault();
        e.stopPropagation();
      });
    });

    // คลิกเพื่อเลือกไฟล์ (ถ้า input ใช้ display:none บางเบราว์เซอร์จะบล็อก แนะนำซ่อนด้วยคลาสแทน)
    if (uploadArea) {
      uploadArea.addEventListener('click', () => fileInput && fileInput.click());

      uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('drag-over');
      });

      uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('drag-over');
      });

      uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
        const files = Array.from(e.dataTransfer.files || []);
        handleFiles(files);
      });
    }

    if (fileInput) {
      fileInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files || []);
        handleFiles(files);
      });
    }

    function handleFiles(files) {
      files.forEach(file => {
        const exists = selectedFiles.find(f => f.name === file.name && f.size === file.size && f.type === file.type);
        if (!exists) {
          selectedFiles.push(file);
          displayFile(file);
        }
      });
      syncInputFiles();
    }

    function displayFile(file) {
      const fileItem = document.createElement('div');
      fileItem.className = 'file-item';
      fileItem.innerHTML = `
        <div class="file-icon">
          <i data-lucide="file-text" style="width:20px;height:20px;color:#ef4444;"></i>
        </div>
        <span class="file-name" title="${file.name}">${file.name}</span>
        <span class="file-size">${formatFileSize(file.size)}</span>
        <button type="button" class="remove-file" aria-label="ลบไฟล์"
          data-name="${encodeURIComponent(file.name)}" data-size="${file.size}">
          <i data-lucide="x" style="width:16px;height:16px;"></i>
        </button>
      `;
      filesList && filesList.appendChild(fileItem);

      // bind ปุ่มลบของรายการที่เพิ่งเพิ่ม
      const removeBtn = fileItem.querySelector('.remove-file');
      if (removeBtn) {
        removeBtn.addEventListener('click', () => {
          const n = decodeURIComponent(removeBtn.getAttribute('data-name') || '');
          const s = Number(removeBtn.getAttribute('data-size') || 0);
          selectedFiles = selectedFiles.filter(f => !(f.name === n && f.size === s));
          fileItem.remove();
          syncInputFiles();
        });
      }

      // init icons
      if (window.lucide && typeof lucide.createIcons === 'function') {
        lucide.createIcons();
      }
    }

    function syncInputFiles() {
      if (!fileInput) return;
      const dt = new DataTransfer();
      selectedFiles.forEach(file => dt.items.add(file));
      fileInput.files = dt.files;
    }

    function formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    /*** ------------------- URL Add/Remove ------------------- ***/
    // โครงสร้างที่คาดหวัง:
    // <div class="url-section" id="urlSection">
    //   <div class="section-divider"></div>
    //   <div class="form-group url-row">
    //     <input type="url" name="additional_urls[]" class="form-input" ...>
    //     <button type="button" class="add-url-btn"><i data-lucide="plus"></i></button>
    //   </div>
    // </div>

    const urlSection   = document.getElementById('urlSection') || document.querySelector('.url-section');
    const firstAddBtn  = urlSection ? urlSection.querySelector('.add-url-btn') : null;

    // ป้องกัน divider ขวางการคลิก
    const divider = urlSection ? urlSection.querySelector('.section-divider') : null;
    if (divider) divider.style.pointerEvents = 'none';

    if (firstAddBtn) {
      firstAddBtn.addEventListener('click', () => addUrlRow());
      // ให้ปุ่มรับคลิกแน่ ๆ
      firstAddBtn.style.pointerEvents = 'auto';
    }

    // ใช้ event delegation สำหรับปุ่มลบในแถวที่ถูกเพิ่มภายหลัง
    if (urlSection) {
      urlSection.addEventListener('click', (e) => {
        const removeBtn = e.target.closest('.remove-url-btn');
        if (removeBtn) {
          const row = removeBtn.closest('.url-row');
          if (row) row.remove();
        }

        const addBtn = e.target.closest('.add-url-second'); // เผื่อมีปุ่ม + เพิ่มที่แถวล่าง ๆ
        if (addBtn) addUrlRow();
      });

      // ถ้าผู้ใช้กด Enter ในช่อง URL → เพิ่มแถวใหม่ทันที
      urlSection.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && e.target.matches('input[type="url"]')) {
          e.preventDefault();
          addUrlRow();
        }
      });
    }

    function addUrlRow() {
      if (!urlSection) return;
      const row = document.createElement('div');
      row.className = 'form-group url-row';
      row.innerHTML = `
        <input type="url" name="additional_urls[]" class="form-input" placeholder="วาง URL เพิ่มเติม">
        <button type="button" class="remove-url-btn" aria-label="ลบ URL">
          <i data-lucide="x" style="width:16px;height:16px;"></i>
        </button>
      `;
      urlSection.appendChild(row);

      // init icons
      if (window.lucide && typeof lucide.createIcons === 'function') {
        lucide.createIcons();
      }

      // โฟกัสช่องใหม่
      const input = row.querySelector('input[type="url"]');
      if (input) input.focus();
    }
  });
</script>


    <style>
        .evidence-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .evidence-containers {
            width: 100%;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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

        /* Upload Section */
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
            transition: all 0.3s ease;
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

        .file-icon {
            flex-shrink: 0;
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

        /* URL Section */
        .url-section {
            margin-bottom: 30px;
        }

        .section-divider {
            text-align: center;
            color: #6b7280;
            margin: 20px 0;
            position: relative;
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
            background: white;
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
            transition: border-color 0.3s;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .url-input {
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1'/%3E%3C/svg%3E") no-repeat 16px center;
            background-size: 20px;
            padding-left: 48px;
        }

        .add-url-btn {
            padding: 12px;
            background: #f3f4f6;
            border: 2px solid #d1d5db;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .add-url-btn:hover {
            background: #e5e7eb;
            border-color: #9ca3af;
        }

        /* Details Section */
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
            background: white;
            font-size: 14px;
        }

        .toolbar-btn {
            padding: 6px 8px;
            background: none;
            border: 1px solid transparent;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toolbar-btn:hover {
            background: #e5e7eb;
        }

        .toolbar-btn.active {
            background: #dbeafe;
            border-color: #3b82f6;
        }

        .color-picker input[type="color"] {
            width: 32px;
            height: 32px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            cursor: pointer;
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
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Action Buttons */
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
            transition: all 0.3s;
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-secondary {
            background: white;
            color: #374151;
            border: 2px solid #d1d5db;
        }

        .btn-secondary:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .evidence-form {
                padding: 20px;
            }

            .editor-toolbar {
                gap: 4px;
                padding: 6px 8px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .form-group {
                flex-direction: column;
            }

            .add-url-btn {
                align-self: flex-start;
                width: fit-content;
            }
        }
    </style>

@endsection
