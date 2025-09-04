@extends('layouts.app')
@section('title', 'จัดการข้อมูลหน่วยงาน')
@section('content')

    <div class="user-container">
        <h1>รายชื่อผู้ใช้งาน</h1>

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
                <input type="text" id="custom-search" class="search-input" placeholder="ค้นหารายการชื่อผู้ใช้">
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
                    <button class="dropdown-item sort-option" data-column="1" data-order="asc" role="menuitem">ชื่อผู้ใช้งาน
                        (A-Z)</button>
                    <button class="dropdown-item sort-option" data-column="1" data-order="desc"
                        role="menuitem">ชื่อผู้ใช้งาน (Z-A)</button>
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
                        <h3 class="dropdown-title">หน่วยงาน</h3>
                        <div id="department-options" style="display:grid; gap:8px;">
                            @foreach ($departments as $dep)
                                <label style="display:inline-flex; align-items:center;">
                                    <input type="checkbox" class="filter-option" data-column="3"
                                        data-value="{{ $dep }}">
                                    <span style="margin-left:8px; font-size:14px; color:#374151;">{{ $dep }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="dropdown-divider"></div>

                        <h3 class="dropdown-title">บทบาท</h3>
                        <div id="role-options" style="display:grid; gap:8px;">
                            @foreach ($roles as $role)
                                <label style="display:inline-flex; align-items:center;">
                                    <input type="checkbox" class="filter-option" data-column="5"
                                        data-value="{{ $role }}">
                                    <span style="margin-left:8px; font-size:14px; color:#374151;">{{ $role }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="dropdown-divider"></div>

                        <div style="display:flex; justify-content:space-between; gap:12px;">
                            <button id="clear-filters" class="btn" style="padding:6px 10px;">ล้างตัวกรอง</button>
                            <button id="apply-filters" class="btn btn-primary" style="padding:6px 10px;">ใช้ตัวกรอง</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <button id="add-user-button" type="button" class="btn btn-primary"
                style="--blue-600:#398ECA; --blue-700:#2f7ab1;">
                <i data-lucide="circle-plus" style="margin-right:6px;"></i> เพิ่มผู้ใช้งาน
            </button>

        </div>

        <!-- ตารางผู้ใช้งาน -->
        <div class="user-containers">
            <div class="user-list">
                <table class="table" id="table3">
                    <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>ชื่อ-สกุล</th>
                            <th>อีเมล</th>
                            <th>หน่วยงาน</th>
                            <th>หมายเลขโทรศัพท์</th>
                            <th>บทบาท</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->department->name ?? '-' }}</td>
                                <td>{{ $item->phone }}</td>
                                <td>{{ $item->getRoleNames()->implode(', ') ?: '-' }}</td>
                                <td>
                                    <div class="categories-actions">
                                        {{-- ถ้าใช้ Route Model Binding --}}
                                        <a href="{{ route('users.edit', ['id' => $item->id]) }}" class="btn-edit"
                                            style="text-decoration:none;">
                                            <i data-lucide="edit-3" style="margin-right:4px;"></i> แก้ไข
                                        </a>

                                        <form action="{{ route('users.destroy', ['id' => $item->id]) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete"
                                                onclick="return confirm('ต้องการลบผู้ใช้งาน {{ $item->name }} หรือไม่?')">
                                                <i data-lucide="trash-2" style="margin-right:4px;"></i> ลบ
                                            </button>
                                        </form>



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

    <script>
        let table;
        document.getElementById('add-user-button').addEventListener('click', function() {
            window.location.href = "{{ route('users.create') }}";
        });
        $(function() {
            // --- DataTable init ---
            table = $('#table3').DataTable({
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
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#sort-dropdown-container, #filter-dropdown-container').length) {
                    $('#sort-dropdown, #filter-dropdown').addClass('hidden');
                }
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
            // กดตัวเลือกเรียงลำดับ
            $('#sort-dropdown').on('click', '.sort-option', function(e) {
                e.stopPropagation(); // กัน event ไปถึง document แล้วปิด dropdown ทันที
                const col = Number($(this).data('column'));
                const order = String($(this).data('order')); // 'asc' | 'desc'
                table.order([
                    [col, order]
                ]).draw(false); // <-- ต้องเป็น [[col, order]]

                $('#sort-button span').text('เรียงลำดับ: ' + $(this).text().trim());
                $('#sort-dropdown').addClass('hidden');
            });

            // ล้างการเรียงลำดับ
            $('#clear-sort').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (table.state && typeof table.state.clear === 'function') {
                    table.state.clear();
                }

                table.order([]).draw(false);
                // ถ้าต้องการ fallback ให้กลับไปเรียงคอลัมน์ "ลำดับ" จากน้อยไปมาก
                table.order([
                    [0, 'asc']
                ]).draw(false); // <-- ต้องเป็น [[0, 'asc']]

                $('#sort-button span').text('เรียงลำดับ');
                $('#sort-dropdown').addClass('hidden');
            });


            // --- Filtering (Department + Role) ---
            let activeFilters = {}; // { '3': ['แผนก A','แผนก B'], '5': ['Admin'] }

            // เก็บค่า checkbox
            $('.filter-option').on('change', function() {
                const column = String($(this).data('column')); // '3' | '5'
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
                // ล้าง search เดิมทุกคอลัมน์
                table.columns().every(function() {
                    this.search('');
                });

                let filterCount = 0;

                for (const column in activeFilters) {
                    if (activeFilters[column].length > 0) {
                        filterCount += activeFilters[column].length;

                        // สร้าง regex OR และ escape อักขระพิเศษ
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
                table.columns().search('').draw();
            });

            // ปุ่มอื่น ๆ (ถ้ามี)
            $('#add_indicator_button').on('click', function() {
                alert('add indicator functionality will be implemented here');
            });
        });
    </script>



    <script>
        lucide.createIcons();
    </script>
    <!-- ========== CSS (แทน Tailwind) ========== -->
    <style>
        :root {
            --blue-600: #2563eb;
            /* ใกล้เคียง Tailwind */
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

        /* Utilities ที่ใช้แทน hidden/sr-only ฯลฯ */
        .hidden {
            display: none !important;
        }

        .push-right {
            margin-left: auto;
        }

        .hide-sm {
            display: none;
        }

        @media (min-width:640px) {
            .hide-sm {
                display: inline;
            }
        }

        /* Layout พื้นฐาน */
        .user-container h1 {
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

        .actions {
            display: flex;
            width: 100%;
            gap: var(--gap-3);
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
            /* เพิ่ม z-index ให้สูงขึ้น */
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

        /* Checkbox ฟิลเตอร์ */
        .filter-option {
            accent-color: var(--blue-600);
        }

        /* ตาราง */
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

        .categories-actions {
            display: flex;
            gap: 8px;
        }

        .btn-edit,
        .btn-delete {
            text-decoration: none;
            /* ตัดเส้นใต้ */
            color: #398ECA;
            /* กำหนดสีข้อความ */
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid var(--gray-300);
            background: var(--white);
            cursor: pointer;
        }

        .btn-edit:hover {
            background: var(--gray-100);
        }

        .btn-delete:hover {
            background: #fee2e2;
            border-color: #fecaca;
        }

        /* กล่องหัวข้อ/รายการ */
        .user-containers {
            margin-top: 12px;
        }

        .user-list {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: var(--pad-3);
        }
    </style>

    <style>
        .user-container {
            max-width: 1500px;
            margin: 0 auto;
            padding: 20px;

        }

        .user-containers {
            width: 100%;
            max-width: 1500px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            /* ทำให้มุมมนทำงานดีขึ้น */
        }

        .header-contatainers {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px 20px;
            font-weight: 700;
            font-size: 30px;
            background: linear-gradient(90deg, #a9c6ff 0%, #fff3d4 100%);
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            color: #222;
        }

        .user-form {

            margin-bottom: 30px;
            position: relative;
            background: white;
            border-radius: 10px;
            f padding: 30px;
            /* box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); */
            border: 2px solid #C2D9EB;
            margin-top: 40px;
            margin-bottom: 40px;
            margin-left: 60px;
            margin-right: 60px;
        }

        .close-btn {
            position: absolute;
            right: 20px;
            top: 20px;
            background: none;
            border: none;
            font-size: 24px;
            color: #666;
            cursor: pointer;
        }

        .form-title {
            color: #1976d2;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
        }

        .add-section-title {
            color: #2196f3;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-decoration: underline;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: normal;
        }

        .required {
            color: red;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }

        .form-input:focus {
            outline: none;
            border-color: #2196f3;
        }

        .submit-btn {
            background: #2196f3;
            color: white;
            border: none;
            padding: 12px 16px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            /* ระยะห่างระหว่างไอคอนและข้อความ */
            transition: background-color 0.3s;
            margin: 0 auto;
        }

        .submit-btn:hover {
            background: #1976d2;
        }

        .btn-icon {
            width: 20px;
            height: 20px;
        }

        .user-list {
            background: white;
            border-radius: 10px;
            padding: 30px;
            /* box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); */
            border: 2px solid #C2D9EB;
            margin-top: 40px;
            margin-bottom: 40px;
            margin-left: 60px;
            margin-right: 60px;
        }

        .list-title {
            color: #2196f3;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-decoration: underline;
        }

        .user-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .user-item:last-child {
            border-bottom: none;
        }

        .user-name {
            color: #333;
            font-size: 16px;
        }

        .user-name::before {
            content: " ";
            color: #333;
            margin-right: 8px;
        }

        .user-actions {
            display: flex;
            gap: 10px;
        }

        .edit-btn {
            color: #2196f3;
            text-decoration: underline;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .delete-btn {
            color: #f44336;
            text-decoration: underline;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .edit-btn:hover {
            color: #1976d2;
        }

        .delete-btn:hover {
            color: #d32f2f;
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 10px;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .modal-close {
            position: absolute;
            right: 15px;
            top: 15px;
            background: none;
            border: none;
            font-size: 24px;
            color: #666;
            cursor: pointer;
        }

        .modal-title {
            color: #000000;
            font-size: 20px;
            font-weight: bold;
            /* text-align: center; */
            margin-bottom: 10px;
        }

        .modal-section-title {
            color: #868686;
            font-size: 16px;
            /* font-weight: bold; */
            /* margin-bottom: 10px; */
            /* text-decoration: underline; */
        }

        .modal-form-group {
            margin-bottom: 20px;
        }

        .modal-form-label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: normal;
        }

        .modal-form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }

        .modal-form-input:focus {
            outline: none;
            border-color: #2196f3;
        }

        .modal-buttons {
            display: flex;
            gap: 150px;
            justify-content: center;
            margin-top: 30px;
        }

        .modal-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .modal-btn-primary {
            background: #2196f3;
            color: white;
        }

        .modal-btn-primary:hover {
            background: #1976d2;
        }

        .modal-btn-secondary {
            background: #FFFFFF;
            color: #398ECA;
            border: 1px solid #398ECA;
        }

        .modal-btn-secondary:hover {
            background: #398ECA;
            color: white;
        }

        .modal-btn-danger {
            background: #FFFFFF;
            color: #FF0004;
            border: 1px solid #FF0004;
        }

        .modal-btn-danger:hover {

            background: #db0a0d;
            color: white;
        }

        .delete-message {
            text-align: center;
            margin: 20px 0;
            color: #333;
            font-size: 16px;
        }

        .categories-actions {
            display: flex;
            gap: 10px;
        }

        .btn-edit {
            background-color: white;
            border: 1px solid #398ECA;
            color: #398ECA;
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-edit:hover {
            background-color: #398ECA;
            color: white;
        }

        .btn-delete {
            background-color: white;
            color: #e53935;
            border: 1px solid #e53935;
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-delete:hover {
            background-color: #c62828;
            color: white;
        }

        .dataTables_length,
        .dataTables_filter {
            display: none;
        }

        /* เดิมมี flex-wrap: wrap; อยู่ — เก็บไว้สำหรับจอเล็ก */
        .controls {
            display: flex;
            flex-wrap: wrap;
            gap: var(--gap-2);
            align-items: center;
        }

        /* จอกว้าง: บังคับให้อยู่บรรทัดเดียว แล้วดันปุ่มไปขวา */
        @media (min-width: 768px) {
            .controls {
                flex-wrap: nowrap;
            }

            .controls .search-box {
                flex: 1 1 420px;
                /* ให้ช่องค้นหายืดได้ */
                max-width: none;
                /* เอา max-width เดิมออกเมื่อจอกว้าง */
            }

            #add-user-button {
                margin-left: auto;
            }

            /* ดันปุ่มไปชิดขวา */
        }

        /* กันองค์ประกอบโดนบีบจน wrap โดยไม่ตั้งใจ */
        .controls>* {
            flex-shrink: 0;
        }
    </style>

<<<<<<< HEAD
@endsection
=======
@endsection
>>>>>>> origin/Jui
