@extends('layouts.app')
@section('title', 'เอกสารและหลักฐาน')
@section('content')

    <div class="evidence-container">
        <h1>เอกสารและหลักฐาน</h1>

        <!-- Controls -->
        <div class="controls">
            <!-- Search -->
            <div class="search-box" style="width:100%; max-width:420px;">
                <div class="icon">
                    <!-- search icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" style="color:#9ca3af;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" id="custom-search" class="search-input" placeholder="ค้นหาเอกสารและหลักฐาน">
            </div>

            <!-- Sort -->
            <div class="dropdown" id="sort-dropdown-container">
                <button id="sort-button" class="btn">
                    <span>เรียงลำดับ</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                    </svg>
                </button>
                <div id="sort-dropdown" class="dropdown-menu hidden" role="menu" aria-orientation="vertical">
                    <button class="dropdown-item sort-option" data-column="0" data-order="asc" role="menuitem">ลำดับ
                        (น้อยไปมาก)</button>
                    <button class="dropdown-item sort-option" data-column="0" data-order="desc" role="menuitem">ลำดับ
                        (มากไปน้อย)</button>
                    <button class="dropdown-item sort-option" data-column="1" data-order="asc" role="menuitem">ชื่อไฟล์
                        (A-Z)</button>
                    <button class="dropdown-item sort-option" data-column="1" data-order="desc" role="menuitem">ชื่อไฟล์
                        (Z-A)</button>
                    <button class="dropdown-item sort-option" data-column="4" data-order="desc"
                        role="menuitem">วันที่อัปโหลด
                        (ใหม่ล่าสุด)</button>
                    <button class="dropdown-item sort-option" data-column="4" data-order="asc" role="menuitem">วันที่อัปโหลด
                        (เก่าล่าสุด)</button>
                    <div class="dropdown-divider"></div>
                    <button id="clear-sort" type="button" class="dropdown-item"
                        style="color:#4b5563;">ล้างตัวเรียงลำดับ</button>
                </div>
            </div>

            <!-- Filter -->
            <div class="dropdown" id="filter-dropdown-container">
                <button id="filter-button" class="btn">
                    <span>กรองข้อมูล</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                </button>

                <div id="filter-dropdown" class="dropdown-menu hidden">
                    <div style="padding:12px 12px;">

                        {{-- Section: ประเภทไฟล์ --}}
                        <h3 class="dropdown-title">ประเภทไฟล์</h3>
                        <div class="dropdown-multiselect" id="typeDropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('typeDropdown')">
                                <span id="type-label">เลือกประเภทไฟล์</span>
                                <i style="font-size:12px;">▼</i>
                            </div>
                            <div class="dropdown-content">
                                @foreach ($fileTypes as $type)
                                    <label style="display:flex; align-items:center; margin-bottom:4px;">
                                        <input type="checkbox" class="filter-option type-option" data-column="3"
                                            data-value="{{ $type }}">
                                        <span
                                            style="margin-left:6px; font-size:14px; color:#374151;">{{ $type }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        {{-- Section: ผู้ใช้งาน --}}
                        <h3 class="dropdown-title">ผู้ใช้งาน</h3>
                        <div class="dropdown-multiselect" id="userDropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('userDropdown')">
                                <span id="user-label">เลือกผู้ใช้งาน</span>
                                <i style="font-size:12px;">▼</i>
                            </div>
                            <div class="dropdown-content">
                                @foreach ($fileUsers as $user)
                                    <label style="display:flex; align-items:center; margin-bottom:4px;">
                                        <input type="checkbox" class="filter-option user-option" data-column="5"
                                            data-value="{{ $user }}">
                                        <span
                                            style="margin-left:6px; font-size:14px; color:#374151;">{{ $user }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        <h3 class="dropdown-title">ตัวชี้วัด</h3>
                        <div class="dropdown-multiselect" id="indicatorDropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('indicatorDropdown')">
                                <span id="indicator-label">เลือกตัวชี้วัด</span>
                                <i style="font-size:12px;">▼</i>
                            </div>
                            <div class="dropdown-content">
                                @foreach ($indicators as $ind)
                                    <label style="display:flex; align-items:center; margin-bottom:4px;">
                                        <input type="checkbox" class="filter-option indicator-option" data-column="6"
                                            data-value="{{ $ind->code }}">
                                        <span style="margin-left:6px; font-size:14px; color:#374151;">
                                            {{ $ind->code }}
                                            {{-- {{ $ind->code }} - {{ $ind->name }} --}}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        {{-- Buttons --}}
                        <div style="display:flex; justify-content:space-between; gap:12px;">
                            <button id="clear-filters" class="btn" style="padding:6px 10px;">ล้างตัวกรอง</button>
                            <button id="apply-filters" class="btn btn-primary"
                                style="padding:6px 10px;">ใช้ตัวกรอง</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ตารางเอกสารและหลักฐาน -->
        <div class="evidence-containers">
            <div class="evidence-list">
                <table class="table" id="evidenceTable">
                    <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>ชื่อไฟล์</th>
                            <th>ขนาดไฟล์</th>
                            <th>ประเภทไฟล์</th>
                            <th>วันที่อัปโหลด</th>
                            <th>ชื่อผู้อัปโหลด</th>
                            <th>ตัวชี้วัด</th>

                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($evidences as $index => $evidence)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="file-info">
                                        <div class="file-icon">
                                            @if (str_ends_with($evidence->type, 'pdf'))
                                                <i data-lucide="file-text" style="color:#dc2626;"></i>
                                            @elseif (str_ends_with($evidence->type, 'docx') || str_ends_with($evidence->type, '.docx'))
                                                <i data-lucide="file-text" style="color:#2563eb;"></i>
                                            @elseif (str_ends_with($evidence->type, 'pptx') || str_ends_with($evidence->type, '.pptx'))
                                                <i data-lucide="file-text" style="color:#eb7e25;"></i>
                                            @elseif (str_ends_with($evidence->type, 'image') ||
                                                    str_ends_with($evidence->type, 'jpg') ||
                                                    str_ends_with($evidence->type, 'png') ||
                                                    str_ends_with($evidence->type, 'jpeg'))
                                                <i data-lucide="image" style="color:#16a34a;"></i>
                                            @elseif (str_ends_with($evidence->type, 'excel') || str_ends_with($evidence->name, '.xls'))
                                                <i data-lucide="file-spreadsheet" style="color:#059669;"></i>
                                            @elseif ($evidence->type === 'url')
                                                <i data-lucide="link" style="color:#9333ea;"></i>
                                            @else
                                                <i data-lucide="file" style="color:#6b7280;"></i>
                                            @endif
                                        </div>

                                        <div class="file-details">
                                            <div class="file-name">{{ $evidence->name }}</div>
                                            @if ($evidence->detail)
                                                <div class="file-description">
                                                    {{ Str::limit(strip_tags($evidence->detail), 50) }}</div>
                                            @endif
                                        </div>

                                    </div>
                                </td>
                                <td>{{ $evidence->total_size_human ?? '-' }}</td>
                                <td data-search="{{ $evidence->type }}">{{ $evidence->type }}</td>
                                <td data-order="{{ optional($evidence->created_at)->timestamp }}">
                                    {{ $evidence->created_at ? $evidence->created_at->format('M d, Y') : 'Dec 13, 2022' }}
                                </td>
                                <td data-search="{{ optional($evidence->user)->name ?? '' }}">
                                    {{ $evidence->user->name ?? '-' }}
                                </td>
                                <td data-search="{{ optional($evidence->criteria->indicator)->code ?? '' }}">
                                    {{ optional($evidence->criteria->indicator)->name ?? '-' }}
                                </td>

                                <td>
                                    <div class="evidence-actions">
                                        @if ($evidence->type === 'url' && !empty($evidence->path['urls'][0]))
                                            <button type="button" class="btn-link" style="width: 110px;"
                                                onclick="window.open('{{ $evidence->path['urls'][0] }}', '_blank')"
                                                title="เปิดลิงก์">
                                                <i data-lucide="external-link" style="margin-right:4px;"></i> เปิดลิงก์
                                            </button>
                                        @else
                                            <button type="button" class="btn-download"
                                                onclick="window.location.href='{{ route('evidences.download', $evidence->id) }}'"
                                                title="ดาวน์โหลด">
                                                <i data-lucide="download" style="margin-right:4px;"></i> ดาวน์โหลด
                                            </button>
                                        @endif


                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.469.0/dist/umd/lucide.min.js"></script>

    <script>
        let table;

        // ฟังก์ชันดาวน์โหลดไฟล์
        function downloadFile(evidenceId) {
            window.location.href = "{{ route('evidences.download', ':id') }}".replace(':id', evidenceId);
        }

        $(function() {
            // --- DataTable init ---
            table = $('#evidenceTable').DataTable({
                searching: true,
                lengthChange: false,
                dom: 'rtip',
                order: [], // ไม่มี default sort
                stateSave: false, // ปิดจำสถานะ (กัน order เด้งกลับ)
                language: {
                    paginate: {
                        previous: 'ก่อนหน้า',
                        next: 'ถัดไป'
                    },
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    emptyTable: "ไม่พบข้อมูล",
                    zeroRecords: "ไม่พบข้อมูลที่ตรงกับการค้นหา"
                }
            });
            table.on('draw', function() {
                if (window.lucide?.createIcons) lucide.createIcons();
            });
            // --- Custom search ---
            let timer;
            $('#custom-search')
                .on('input', function() {
                    clearTimeout(timer);
                    const val = this.value;
                    timer = setTimeout(() => table.search(val).draw(), 150);
                })
                .on('search', function() {
                    if (this.value === '') table.search('').draw();
                });

            // --- Dropdown toggles ---
            $('#sort-button').on('click', function(e) {
                e.stopPropagation();
                $('#sort-dropdown').toggleClass('hidden');
                $('#filter-dropdown').addClass('hidden');
            });

            $('#filter-button').on('click', function(e) {
                e.stopPropagation();
                $('#filter-dropdown').toggleClass('hidden');
                $('#sort-dropdown').addClass('hidden');
            });

            // ปิด dropdown เมื่อคลิกข้างนอก
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#sort-dropdown-container, #filter-dropdown-container').length) {
                    $('#sort-dropdown, #filter-dropdown').addClass('hidden');
                }
            });

            // --- Sorting ---
            $('#sort-dropdown').on('click', '.sort-option', function(e) {
                e.stopPropagation();
                const col = Number($(this).data('column'));
                const order = String($(this).data('order'));
                table.order([
                    [col, order]
                ]).draw(false);

                $('#sort-button span').text('เรียงลำดับ: ' + $(this).text().trim());
                $('#sort-dropdown').addClass('hidden');
            });

            // ล้างการเรียงลำดับ
            $('#clear-sort').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                table.order([]).draw(false);
                table.order([
                    [0, 'asc']
                ]).draw(false);

                $('#sort-button span').text('เรียงลำดับ');
                $('#sort-dropdown').addClass('hidden');
            });

            // --- Filtering ---
            let activeFilters = {};

            $('.filter-option').on('change', function() {
                const column = String($(this).data('column'));
                const value = String($(this).data('value'));

                if (!activeFilters[column]) activeFilters[column] = [];
                if (this.checked) {
                    if (!activeFilters[column].includes(value)) activeFilters[column].push(value);
                } else {
                    activeFilters[column] = activeFilters[column].filter(v => v !== value);
                    if (activeFilters[column].length === 0) delete activeFilters[column];
                }
            });

            // ใช้ตัวกรอง
            $('#apply-filters').on('click', function() {
                table.columns().every(function() {
                    this.search('');
                });

                let filterCount = 0;

                for (const column in activeFilters) {
                    if (activeFilters[column].length > 0) {
                        filterCount += activeFilters[column].length;

                        const regex = activeFilters[column]
                            .map(v => v.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'))
                            .join('|');

                        table.column(Number(column)).search(regex, true, false);
                    }
                }

                $('#filter-button span').text(filterCount > 0 ? `กรองข้อมูล (${filterCount})` :
                    'กรองข้อมูล');
                table.draw();
                $('#filter-dropdown').addClass('hidden');
            });

            // ล้างตัวกรอง
            $('#clear-filters').on('click', function(e) {
                e.preventDefault();

                $('.filter-option').prop('checked', false);
                activeFilters = {};
                $('#filter-button span').text('กรองข้อมูล');

                // reset label ทั้ง 2 dropdown
                $('#type-label').text('เลือกประเภทไฟล์');
                $('#user-label').text('เลือกผู้ใช้งาน');
                $('#indicator-label').text('เลือกตัวชี้วัด');

                table.columns().search('').draw();
            });


        });
    </script>
    <script>
        function toggleDropdown(id) {
            document.querySelectorAll('.dropdown-multiselect').forEach(el => {
                if (el.id !== id) el.classList.remove("open");
            });
            document.getElementById(id).classList.toggle("open");
        }

        // อัปเดต label เมื่อเลือก
        function setupDropdownLabel(dropdownId, labelId) {
            const checkboxes = document.querySelectorAll(`#${dropdownId} .filter-option`);
            const label = document.getElementById(labelId);

            checkboxes.forEach(cb => {
                cb.addEventListener('change', () => {
                    const selected = Array.from(checkboxes)
                        .filter(x => x.checked)
                        .map(x => x.getAttribute('data-value'));

                    label.textContent = selected.length ?
                        selected.join(', ') :
                        (dropdownId === 'typeDropdown' ?
                            'เลือกประเภทไฟล์' :
                            (dropdownId === 'userDropdown' ?
                                'เลือกผู้ใช้งาน' :
                                'เลือกตัวชี้วัด'));
                });
            });
        }

        setupDropdownLabel('typeDropdown', 'type-label');
        setupDropdownLabel('userDropdown', 'user-label');
        setupDropdownLabel('indicatorDropdown', 'indicator-label');

        // checkboxes.forEach(cb => {
        //     cb.addEventListener('change', () => {
        //         const selected = Array.from(checkboxes)
        //             .filter(x => x.checked)
        //             .map(x => x.getAttribute('data-value'));

        //         label.textContent = selected.length ?
        //             selected.join(', ') :
        //             'เลือกประเภทไฟล์';
        //     });
        // });

        // ปิด dropdown ถ้าคลิกข้างนอก
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById("typeDropdown");
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove("open");
            }
        });
    </script>
    <script>
       ห
    </script>

    <!-- ========== CSS ========== -->
    <style>
        :root {
            --blue-600: #2563eb;
            --blue-700: #1d4ed8;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-700: #374151;
            --ring: #3b82f6;
            --white: #fff;
            --shadow: 0 1px 2px rgba(0, 0, 0, .06), 0 1px 3px rgba(0, 0, 0, .1);
            --radius: 8px;
            --gap-2: 8px;
            --gap-3: 12px;
            --pad-2: 8px;
            --pad-3: 12px;
            --pad-4: 16px;
        }

        .hidden {
            display: none !important;
        }

        .evidence-container {
            max-width: 1500px;
            margin: 0 auto;
            padding: 20px;
        }

        .evidence-container h1 {
            margin: 0 0 8px;
            font-size: 24px;
            color: #111827;
        }

        .controls {
            display: flex;
            flex-wrap: wrap;
            gap: var(--gap-2);
            align-items: center;
            margin: 16px 0;
        }

        /* Search box */
        .search-box {
            position: relative;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .search-box .icon {
            position: absolute;
            inset: 0 auto 0 12px;
            display: flex;
            align-items: center;
            pointer-events: none;
        }

        .search-input {
            padding: 8px 16px 8px 40px;
            width: 100%;
            outline: 0;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius);
        }

        .search-input:focus {
            border-color: var(--ring);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .2);
        }

        /* ปุ่ม */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: var(--radius);
            font: inherit;
            cursor: pointer;
            border: 0;
            background: var(--white);
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
            transition: .15s background-color ease;
        }

        .btn:hover {
            background: var(--gray-100);
        }

        .btn-primary {
            background: var(--blue-600);
            color: var(--white);
            border-color: transparent;
        }

        .btn-primary:hover {
            background: var(--blue-700);
        }

        /* Dropdown */
        .dropdown {
            position: relative;
            display: inline-block;
            text-align: left;
        }

        .dropdown-menu {
            position: absolute;
            left: 0;
            top: 100%;
            margin-top: 8px;
            width: 192px;
            background: var(--white);
            border-radius: 6px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, .1), 0 4px 6px -2px rgba(0, 0, 0, .05);
            border: 1px solid rgba(0, 0, 0, .05);
            z-index: 9999;
            padding: 4px 0;
            display: block;
        }

        .dropdown-menu.hidden {
            display: none !important;
        }

        .dropdown-item {
            display: block;
            width: 100%;
            text-align: left;
            padding: 8px 16px;
            font-size: 14px;
            color: #374151;
            background: transparent;
            border: 0;
        }

        .dropdown-item:hover {
            background: var(--gray-100);
        }

        .dropdown-divider {
            height: 1px;
            background: var(--gray-200);
            margin: 12px 0;
        }

        .dropdown-title {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 8px;
        }

        .filter-option {
            accent-color: var(--blue-600);
        }

        /* ตาราง */
        .evidence-containers {
            width: 100%;
            max-width: 1500px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .evidence-list {
            background: white;
            border-radius: 10px;
            padding: 30px;
            border: 2px solid #C2D9EB;
            margin-top: 40px;
            margin-bottom: 40px;
            margin-left: 60px;
            margin-right: 60px;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            text-align: left;
            font-weight: 600;
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
            padding: 12px;
        }

        .table tbody td {
            padding: 12px;
            border-bottom: 1px solid var(--gray-200);
        }

        /* File info styling */
        .file-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .file-icon {
            flex-shrink: 0;
        }

        .file-details {
            min-width: 0;
        }

        .file-name {
            font-weight: 500;
            color: #111827;
            word-break: break-word;
        }

        .file-description {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* Status badges */
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Action buttons */
        .evidence-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-download,
        .btn-link,
        .btn-edit,
        .btn-delete {
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid var(--gray-300);
            background: var(--white);
            cursor: pointer;
            font-size: 12px;
            white-space: nowrap;
        }

        .btn-link {
            color: #398ECA;
            border-color: #398ECA;
        }

        .btn-link:hover {
            background: #ecfdf5;
        }

        .btn-download {
            color: #059669;
            border-color: #059669;
        }

        .btn-download:hover {
            background: #ecfdf5;
        }

        .btn-edit {
            color: #398ECA;
        }

        .btn-edit:hover {
            background: var(--gray-100);
        }

        .btn-delete {
            color: #dc2626;
            border-color: #dc2626;
        }

        .btn-delete:hover {
            background: #fee2e2;
            border-color: #fecaca;
        }

        .dataTables_length,
        .dataTables_filter {
            display: none;
        }

        /* Responsive */
        @media (min-width: 768px) {
            .controls {
                flex-wrap: nowrap;
            }

            .controls .search-box {
                flex: 1 1 420px;
                max-width: none;
            }

            #add-evidence-button {
                margin-left: auto;
            }
        }

        .controls>* {
            flex-shrink: 0;
        }

        /* Table responsive */
        @media (max-width: 768px) {
            .evidence-actions {
                flex-direction: column;
            }

            .table {
                font-size: 12px;
            }

            .evidence-list {
                margin-left: 20px;
                margin-right: 20px;
                padding: 20px;
            }
        }

        i[data-lucide] {
            display: inline-block;
            vertical-align: middle;
        }

        .dropdown-multiselect {
            position: relative;
            display: inline-block;
            width: 220px;
        }

        .dropdown-multiselect .dropdown-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 6px 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: #fff;
            cursor: pointer;
            font-size: 14px;
        }

        .dropdown-multiselect .dropdown-content {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            max-height: 220px;
            overflow-y: auto;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            z-index: 20;
            padding: 8px;
        }

        .dropdown-multiselect.open .dropdown-content {
            display: block;
        }
    </style>

@endsection
