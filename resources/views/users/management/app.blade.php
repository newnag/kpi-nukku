@extends('layouts.app')
@section('title', 'จัดการข้อมูลหน่วยงาน')
@section('content')

    <div class="user-container">
        <h1>

            รายชื่อผู้ใช้งาน

        </h1>

        <!-- Search & Filter Controls Group -->
        <div class="flex flex-wrap gap-2 mt-4 mb-4 items-center">
            <div class="relative w-full sm:w-auto bg-white  rounded-lg shadow-sm">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" id="custom-search"
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none w-full"
                    placeholder="ค้นหารายการชื่อผู้ใช้">
            </div>
            <!-- Sort Button with Dropdown -->
            <div class="relative inline-block text-left  " id="sort-dropdown-container">
                <button id="sort-button"
                  class="h-fit border border-gray-300 rounded-lg  px-4 py-2 bg-white text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                    <span>เรียงลำดับ</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                    </svg>
                </button>
                <div id="sort-dropdown"
                    class="hidden absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                    <div class="py-1" role="menu" aria-orientation="vertical">
                        <button class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            data-column="0" data-order="asc" role="menuitem">ปี (น้อยไปมาก)</button>
                        <button class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            data-column="0" data-order="desc" role="menuitem">ปี (มากไปน้อย)</button>
                        <button class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            data-column="1" data-order="asc" role="menuitem">ชื่อผู้ใช้งาน (A-Z)</button>
                        <button class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            data-column="1" data-order="desc" role="menuitem">ชื่อผู้ใช้งาน (Z-A)</button>

                        <button id="clear-sort"type="button"
                            class=" text-left block w-full px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">ล้างตัวเรียงลำดับ</button>
                    </div>
                </div>
            </div>
            <!-- Filter Button with Dropdown -->
            <div class="relative inline-block text-left" id="filter-dropdown-container">
                <button id="filter-button"
                 class="h-fit border border-gray-300 rounded-lg px-4 py-2 bg-white text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                    <span>กรองข้อมูล</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                </button>
                <div id="filter-dropdown"
                    class="hidden absolute left-0 mt-2 w-64 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                    <div class="py-2 px-3">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">หน่วยงาน</h3>
                        <div id="department-options" class="space-y-2">
                            @foreach ($departments as $dep)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="filter-option rounded text-blue-600" data-column="3"
                                        data-value="{{ $dep }}">
                                    <span class="ml-2 text-sm text-gray-700">{{ $dep }}</span>
                                </label><br>
                            @endforeach
                        </div>

                        <div class="border-t border-gray-200 my-3"></div>

                        <h3 class="text-sm font-medium text-gray-900 mb-2">บทบาท</h3>
                        <div id="role-options" class="space-y-2">
                            @foreach ($roles as $role)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="filter-option rounded text-blue-600" data-column="5"
                                        data-value="{{ $role }}">
                                    <span class="ml-2 text-sm text-gray-700">{{ $role }}</span>
                                </label><br>
                            @endforeach
                        </div>


                        <div class="border-t border-gray-200 my-3"></div>

                        <div class="flex justify-between">
                            <button id="clear-filters"
                                class="text-sm text-gray-600 hover:text-gray-900">ล้างตัวกรอง</button>
                            <button id="apply-filters"
                                class="text-sm text-white bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded">ใช้ตัวกรอง</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Action Buttons Group -->

        </div>


        <div class="user-containers">


            <!-- รายการหน่วยงาน -->
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
                                        <button class="btn-edit"
                                            onclick="openEditModal({{ $item->id }}, '{{ $item->name }}')">
                                            <i data-lucide="edit-3" style="margin-right: 1px;"></i> แก้ไข
                                        </button>
                                        <button class="btn-delete"
                                            onclick="openDeleteModal({{ $item->id }}, '{{ $item->name }}')">
                                            <i data-lucide="trash-2" style="margin-right: 5px;"></i> ลบ
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>


            </div>
        </div>
    </div>

    <script>
        let table;

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
            // ใช้ delegated handler แค่ตัวเดียวพอ
            // 3) คลิกตัวเลือกเรียงลำดับ (ตัวเดียวพอ)
            $('#sort-dropdown').on('click', '.sort-option', function() {
                const col = Number($(this).data('column'));
                const order = String($(this).data('order')); // 'asc' | 'desc'
                table.order([col, order]).draw(false);

                $('#sort-button span').text('เรียงลำดับ: ' + $(this).text().trim());
                $('#sort-dropdown').addClass('hidden');
            });

            // 4) ล้างการเรียงลำดับ — ใช้ fallback ให้กลับไปที่คอลัมน์ "ลำดับ" (คอลัมน์ 0)
            $('#clear-sort').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // เคลียร์ state เผื่อเคยเปิด stateSave ที่อื่น
                if (table.state && typeof table.state.clear === 'function') {
                    table.state.clear();
                }

                // วิธีที่ควรพอ: ล้าง order
                table.order([]).draw(false);

                // Fallback (ให้ได้ผลตรงใจแน่ ๆ): บังคับเรียงตามคอลัมน์ลำดับ (0) จากน้อยไปมาก
                // ถ้าคุณอยาก "ไม่เรียง" จริง ๆ ให้คอมเมนต์สองบรรทัดนี้ออก
                table.order([0, 'asc']).draw(false);

                // รีเซ็ตข้อความปุ่ม + ปิด dropdown
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

    <style>
        .user-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;

        }

        .user-containers {
            width: 100%;
            max-width: 1200px;
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
    </style>

@endsection
