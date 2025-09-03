@extends('layouts.app')
@section('title', 'รายการตัวบ่งชี้')
@section('content')

    <div class="evidence-container">
        <h1>รายการตัวบ่งชี้</h1>

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
                <input type="text" id="custom-search" class="search-input" placeholder="ค้นหารายการตัวบ่งชี้">
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
                    <button class="dropdown-item sort-option" data-column="0" data-order="asc" role="menuitem">ปี
                        (น้อยไปมาก)</button>
                    <button class="dropdown-item sort-option" data-column="0" data-order="desc" role="menuitem">ปี
                        (มากไปน้อย)</button>
                    <button class="dropdown-item sort-option" data-column="1" data-order="asc" role="menuitem">ชื่อตัวบ่งชี้
                        (A-Z)</button>
                    <button class="dropdown-item sort-option" data-column="1" data-order="desc"
                        role="menuitem">ชื่อตัวบ่งชี้
                        (Z-A)</button>
                    <button class="dropdown-item sort-option" data-column="4" data-order="desc" role="menuitem">คะแนนเต็ม
                        (มากไปน้อย)</button>
                    <button class="dropdown-item sort-option" data-column="4" data-order="asc" role="menuitem">คะแนนเต็ม
                        (น้อยไปมาก)</button>
                    <div class="dropdown-divider"></div>
                    <button id="clear-sort" type="button" class="dropdown-item"
                        style="color:#4b5563;">ล้างการเรียงลำดับ</button>
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
                        {{-- Section: ปี --}}
                        <h3 class="dropdown-title">ปี</h3>
                        <div class="dropdown-multiselect" id="yearDropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('yearDropdown')">
                                <span id="year-label">เลือกปี</span>
                                <i style="font-size:12px;">▼</i>
                            </div>
                            <div class="dropdown-content">
                                <label style="display:flex; align-items:center; margin-bottom:4px;">
                                    <input type="checkbox" class="filter-option year-option" data-column="0"
                                        data-value="2568">
                                    <span style="margin-left:6px; font-size:14px; color:#374151;">2568</span>
                                </label>
                                <label style="display:flex; align-items:center; margin-bottom:4px;">
                                    <input type="checkbox" class="filter-option year-option" data-column="0"
                                        data-value="2567">
                                    <span style="margin-left:6px; font-size:14px; color:#374151;">2567</span>
                                </label>
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        {{-- Section: สถานะ --}}
                        <h3 class="dropdown-title">สถานะตัวบ่งชี้</h3>
                        <div class="dropdown-multiselect" id="statusDropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('statusDropdown')">
                                <span id="status-label">เลือกสถานะ</span>
                                <i style="font-size:12px;">▼</i>
                            </div>
                            <div class="dropdown-content">
                                <label style="display:flex; align-items:center; margin-bottom:4px;">
                                    <input type="checkbox" class="filter-option status-option" data-column="6"
                                        data-value="รอดำเนินการ">
                                    <span style="margin-left:6px; font-size:14px; color:#374151;">รอดำเนินการ</span>
                                </label>
                                <label style="display:flex; align-items:center; margin-bottom:4px;">
                                    <input type="checkbox" class="filter-option status-option" data-column="6"
                                        data-value="ไม่ครบ">
                                    <span style="margin-left:6px; font-size:14px; color:#374151;">ไม่ครบ</span>
                                </label>
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

        <!-- ตารางรายการตัวบ่งชี้ -->
        <div class="evidence-containers">
            <div class="evidence-list">
                <table class="table" id="evidenceTable">
                    <thead>
                        <tr>
                            <th>ปี</th>
                            <th>ชื่อตัวบ่งชี้</th>
                            <th>รหัส</th>
                            <th>ประเภทองค์กร</th>
                            <th>หน่วยงานที่รับผิดชอบ</th>
                            <th>คะแนนเต็ม</th>
                            <th>คะแนนรวม</th>
                            <th>สถานะตัวบ่งชี้</th>
                            <th>สถานะเอกสาร</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($indicators as $indicator)
                            @php
                                $standardName = $indicator->category->standard->name ?? '';
                                $dimensionName = $indicator->category->name ?? '';
                                $collectorName = $indicator->assignments->first()->collectorUser->name ?? '';
                                $deptName = '';
                                foreach ($indicator->assignments as $assignment) {
                                    $deptName = optional($assignment->collectorUser?->department)->name ?? '';
                                    if ($deptName) {
                                        break;
                                    }
                                }
                            @endphp
                            <tr data-max="{{ (float) $indicator->max_score }}" data-standard="{{ $standardName }}"
                                data-dimension="{{ $dimensionName }}" data-collector="{{ $collectorName }}"
                                data-dept="{{ $deptName }}" data-status="{{ $indicator->status_key }}">

                                <td class="status-cell">{{ $indicator->year }}</td>
                                <td>{{ $indicator->name }}</td>
                                <td class="status-cell">{{ $indicator->code }}</td>
                                <td class="status-cell">{{ $indicator->type }}</td>
                                <td class="status-cell">{{ $deptName ?: '-' }}</td>
                                <td class="status-cell">{{ $indicator->score_acc }}</td>
                                <td class="status-cell">{{ $indicator->max_score }}</td>
                                <td class="status-cell">
                                    @switch($indicator->status)
                                        @case(0)
                                            <i data-lucide="alert-triangle" class="status-icon text-danger"
                                                title="อยู่ระหว่างดำเนินการ"></i>
                                        @break

                                        @case(4)
                                            <i data-lucide="clock" class="status-icon text-warn"
                                                title="ผลการดำเนินงานยังไม่ครบถ้วนตามเกณฑ์"></i>
                                        @break

                                        @case(2)
                                        @case(3)
                                            <i data-lucide="check-circle" class="status-icon text-success"
                                                title="ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรการ"></i>
                                        @break

                                        @default
                                            <i data-lucide="help-circle" class="status-icon text-gray-500"
                                                title="สถานะไม่ระบุ"></i>
                                    @endswitch
                                </td>
                                <td class="status-cell">
                                    @switch($indicator->doc_status)
                                        @case('ไม่ครบ')
                                            <i data-lucide="x-circle" class="status-icon text-danger"
                                                title="ผลการดำเนินงานยังไม่ครบถ้วนตามเกณฑ์"></i>
                                        @break

                                        @case('ครบ')
                                            <i data-lucide="check-circle" class="status-icon text-success"
                                                title="ผลการดำเนินงานครบถ้วนตามเกณฑ์"></i>
                                        @break

                                        @default
                                            <i data-lucide="help-circle" class="status-icon text-gray-400" title="ไม่ทราบ"></i>
                                    @endswitch
                                </td>
                                <td>
                                    <div class="evidence-actions">
                                        <a href="{{ route('assessment.show', $indicator->id) }}" class="btn-edit"
                                            title="ทำการประเมิน">
                                            <i data-lucide="edit" style="margin-right:4px;"></i> ทำการประเมิน
                                        </a>
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
                $('#year-label').text('เลือกปี');
                $('#status-label').text('เลือกสถานะ');

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
                        (dropdownId === 'yearDropdown' ?
                            'เลือกปี' :
                            'เลือกสถานะ');
                });
            });
        }

        setupDropdownLabel('yearDropdown', 'year-label');
        setupDropdownLabel('statusDropdown', 'status-label');

        // ปิด dropdown ถ้าคลิกข้างนอก
        document.addEventListener('click', function(e) {
            const dropdowns = ['yearDropdown', 'statusDropdown'];
            dropdowns.forEach(id => {
                const dropdown = document.getElementById(id);
                if (dropdown && !dropdown.contains(e.target)) {
                    dropdown.classList.remove("open");
                }
            });
        });
    </script>
    <script>
        lucide.createIcons();
    </script>

    <!-- ========== CSS ========== -->
    <style>
        :root {
            --blue-600: #2563eb;
            --blue-700: #1d4ed8;
            --green-100: #dcfce7;
            --green-600: #16a34a;
            --green-700: #15803d;
            --yellow-100: #fef3c7;
            --yellow-600: #d97706;
            --yellow-700: #b45309;
            --red-100: #fee2e2;
            --red-600: #dc2626;
            --red-700: #b91c1c;
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
            font-size: 14px;
        }

        .table tbody td {
            padding: 12px;
            border-bottom: 1px solid var(--gray-200);
            font-size: 14px;
            vertical-align: middle;
        }

        /* Status badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-completed {
            background: var(--green-100);
            color: var(--green-700);
        }

        .status-warning {
            background: var(--yellow-100);
            color: var(--yellow-700);
        }

        .status-error {
            background: var(--red-100);
            color: var(--red-700);
        }

        /* Action buttons */
        .evidence-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-edit {
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid var(--blue-600);
            background: var(--white);
            cursor: pointer;
            font-size: 12px;
            white-space: nowrap;
            color: var(--blue-600);
        }

        .btn-edit:hover {
            background: #eff6ff;
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
