@extends('layouts.app')
@section('title', 'จัดการข้อมูลมาตรฐานและด้านการประเมิน')
@section('content')

    <div class="categories-container">
        <div class="categories-containers">
            <div class="header-contatainers">
                มาตรฐานและด้านการประเมิน
            </div>
            <!-- ฟอร์มเพิ่มด้านการประเมิน -->
            <div class="category-form">

                <div class="add-section-title">เพิ่มมาตรฐานการประเมิน</div>

                <form action="{{ route('standards.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">ชื่อมาตรฐานการประเมิน <span class="required">*</span></label>
                        <input type="text" name="name" class="form-input" required>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i data-lucide="save" class="btn-icon"></i> บันทึก
                    </button>
                </form>
            </div>

            <!-- รายการมาตรฐานการประเมิน -->
            <div class="categories-list">
                <div class="list-title">รายชื่อมาตรฐานการประเมินที่มี</div>
                <!-- Search & Filter Controls Group -->
                <div class="flex flex-wrap gap-2 mt-4 mb-4 items-center">
                    <div class="relative w-full sm:w-auto bg-white  rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="custom-search-standards"
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none w-full"
                            placeholder="ค้นหารายการชื่อผู้ใช้">
                    </div>
                    <!-- Sort Button with Dropdown -->
                    <div class="relative inline-block text-left  " id="sort-dropdown-container">
                        <button id="sort-button-standards"
                            class="h-fit border border-gray-300 rounded-lg  px-4 py-2 bg-white text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                            <span>เรียงลำดับ</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                            </svg>
                        </button>
                        <div id="sort-dropdown-standards"
                            class="hidden absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                            <div class="py-1" role="menu" aria-orientation="vertical">
                                <button
                                    class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    data-column="0" data-order="asc" role="menuitem">ปี (น้อยไปมาก)</button>
                                <button
                                    class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    data-column="0" data-order="desc" role="menuitem">ปี (มากไปน้อย)</button>
                                <button
                                    class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    data-column="1" data-order="asc" role="menuitem">ชื่อมาตรฐานการประเมิน (A-Z)</button>
                                <button
                                    class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    data-column="1" data-order="desc" role="menuitem">ชื่อมาตรฐานการประเมิน (Z-A)</button>

                                <button id="clear-sort-standards"type="button"
                                    class=" text-left block w-full px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">ล้างตัวเรียงลำดับ</button>
                            </div>
                        </div>
                    </div>


                </div>

                <table class="table" id="table1">
                    <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>ชื่อมาตรฐานการประเมิน</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($standards as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td>
                                    <div class="categories-actions">
                                        <button class="btn-edit"
                                            onclick="openEditModalStandards({{ $item->id }}, '{{ $item->name }}')">
                                            <i data-lucide="edit-3" style="margin-right: 1px;"></i> แก้ไข
                                        </button>

                                        <button class="btn-delete"
                                            onclick="openDeleteModalStandards({{ $item->id }}, '{{ $item->name }}')">
                                            <i data-lucide="trash-2" style="margin-right: 5px;"></i> ลบ
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

            <!-- Edit Modal Standards-->
            <div id="editModalStandards" class="modal-overlay">
                <div class="modal-content">

                    <h2 class="modal-title">แก้ไขชื่อมาตรฐานการประเมิน</h2>
                    <div class="modal-section-title">
                        แก้ไขชื่อมาตรฐานการประเมินที่ต้องการแล้วกดบันทึกเพื่อบันทึกผลที่ต้องการ
                        <p>ชื่อด้านการประเมินเดิม : <span id="currentstandardsName"></span></p>

                    </div>

                    <form id="editFormStandards" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="modal-form-group">
                            <label class="modal-form-label">ชื่อมาตรฐานการประเมิน <span class="required">*</span></label>
                            <input type="text" id="editNameStandards" name="name" class="modal-form-input" required
                                value="{{ old('name') }}">
                        </div>
                        @error('name')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror

                        <div class="modal-buttons">
                            <button type="button" class="modal-btn modal-btn-secondary"
                                onclick="closeModal('editModalStandards')">
                                <i data-lucide="undo-2" style="margin-right: 6px;"></i>กลับ</button>
                            <button type="submit" class="modal-btn modal-btn-primary">
                                <i data-lucide="save" style="margin-right: 6px;"></i>บันทึก</button>
                        </div>
                    </form>

                </div>
            </div>

            <!-- Delete Modal Standards -->
            <div id="deleteModalStandards" class="modal-overlay">
                <div class="modal-content">
                    <button class="modal-close" onclick="closeModal('deleteModalStandards')">&times;</button>
                    <h2 class="modal-title">ลบชิ่อมาตรฐานการประเมิน</h2>
                    <div class="modal-section-title">คำเตือน : การลบชื่อมาตรฐานการประเมินที่ถูกนำมาใช้แล้วจะไม่สามารถลบได้
                    </div>

                    <div class="delete-message">
                        คุณต้องการลบข้อมูลด้านการประเมิน "<span id="deleteNameStandards"></span>" <br>
                        หรือไม่?
                    </div>
                    <form id="deleteFormStandards" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-buttons">
                            <button type="button" class="modal-btn modal-btn-secondary"
                                onclick="closeModal('deleteModalStandards')">
                                <i data-lucide="undo-2" style="margin-right: 6px;"></i>กลับ</button>
                            <button type="submit" class="modal-btn modal-btn-danger"><i data-lucide="x"
                                    style="margin-right: 6px;"></i>ยืนยันการลบ</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ฟอร์มเพิ่มด้านการประเมิน -->
            <div class="category-form">

                <div class="add-section-title">เพิ่มด้าน และคะแนนเต็มการประเมิน</div>

                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">ชื่อด้านการประเมิน <span class="required">*</span></label>
                        <input type="text" name="name" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">ตะแนนเต็มของด้านการประเมิน <span class="required">*</span></label>
                        <input type="text" name="max_score" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">เลือกมาตรของด้านการประเมิน <span class="required">*</span></label>
                        <select name="standard_id" class="form-input" required>
                            <option value="">-- เลือกมาตรฐาน --</option>
                            @foreach ($standards as $standard)
                                <option value="{{ $standard->id }}">{{ $standard->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i data-lucide="save" class="btn-icon"></i> บันทึก
                    </button>
                </form>
            </div>

            <!-- รายการด้านการประเมิน -->
            <div class="categories-list">
                <div class="list-title">รายชื่อด้านการประเมินที่มี</div>
                <div class="flex flex-wrap gap-2 mt-4 mb-4 items-center">
                    <div class="relative w-full sm:w-auto bg-white  rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="custom-search-categories"
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none w-full"
                            placeholder="ค้นหารายการชื่อผู้ใช้">
                    </div>
                    <!-- Sort Button with Dropdown -->
                    <div class="relative inline-block text-left  " id="sort-dropdown-container">
                        <button id="sort-button-categories"
                            class="h-fit border border-gray-300 rounded-lg  px-4 py-2 bg-white text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                            <span>เรียงลำดับ</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                            </svg>
                        </button>
                        <div id="sort-dropdown-categories"
                            class="hidden absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                            <div class="py-1" role="menu" aria-orientation="vertical">
                                <button
                                    class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    data-column="0" data-order="asc" role="menuitem">ปี (น้อยไปมาก)</button>
                                <button
                                    class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    data-column="0" data-order="desc" role="menuitem">ปี (มากไปน้อย)</button>
                                <button
                                    class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    data-column="1" data-order="asc" role="menuitem">ชื่อด้านการประเมิน (A-Z)</button>
                                <button
                                    class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    data-column="1" data-order="desc" role="menuitem">ชื่อด้านการประเมิน (Z-A)</button>

                                <button id="clear-sort-categories"type="button"
                                    class=" text-left block w-full px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">ล้างตัวเรียงลำดับ</button>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons Group -->

                </div>

                <table class="table" id="table2">
                    <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>ชื่อด้านการประเมิน</th>
                            <th>คะแนนเต็ม</th>
                            <th>มาตรฐาน</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $index => $cat)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $cat->name }}</td>
                                <td>{{ $cat->max_score }}</td>
                                <td>{{ $cat->standard->name ?? '-' }}</td>
                                <td>
                                    <div class="categories-actions">
                                        <button class="btn-edit"
                                            onclick="openEditModalCat({{ $cat->id }}, '{{ $cat->name }}', '{{ $cat->max_score }}', '{{ $cat->standard_id }}', '{{ $cat->standard->name }}')">
                                            <i data-lucide="edit-3" style="margin-right: 1px;"></i> แก้ไข
                                        </button>

                                        <button class="btn-delete"
                                            onclick="openDeleteModalCat({{ $cat->id }}, '{{ $cat->name }}', '{{ $cat->max_score }}', '{{ $cat->standard->name }}')">
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


    <!-- Edit Modal Categories-->
    <div id="editModalCategories" class="modal-overlay">
        <div class="modal-content">

            <h2 class="modal-title">แก้ไขชื่อด้านการประเมิน</h2>
            <div class="modal-section-title">แก้ไขชื่อด้านการประเมินที่ต้องการแล้วกดบันทึกเพื่อบันทึกผลที่ต้องการ
                <p>ชื่อด้านการประเมินเดิม : <span id="currentcategoriesName"></span></p>
                <p>คะแนนเต็มเดิม : <span id="currentcategoriesMaxScore"></span></p>
                <p>มาตรฐานเดิม : <span id="currentcategoriesStandardName"></span></p>

            </div>

            <form id="editFormCategories" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-form-group">
                    <label class="modal-form-label">ชื่อด้านการประเมิน <span class="required">*</span></label>
                    <input type="text" id="editNameCategories" name="name" class="modal-form-input" required
                        value="{{ old('name') }}">
                </div>
                @error('name')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
                <div class="modal-form-group">
                    <label class="modal-form-label">คะแนนเต็ม <span class="required">*</span></label>
                    <input type="number" id="editMaxScore" name="max_score" class="modal-form-input" required
                        value="{{ old('max_score') }}">
                </div>
                @error('max_score')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
                <div class="modal-form-group">
                    <label class="modal-form-label">เลือกมาตรฐาน <span class="required">*</span></label>
                    <select id="editStandardId" name="standard_id" class="modal-form-input" required>
                        <option value="">-- เลือกมาตรฐาน --</option>
                        @foreach ($standards as $standard)
                            <option value="{{ $standard->id }}">{{ $standard->name }}</option>
                        @endforeach
                    </select>
                </div>
                @error('standard_id')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
                <div class="modal-buttons">
                    <button type="button" class="modal-btn modal-btn-secondary"
                        onclick="closeModal('editModalCategories')">
                        <i data-lucide="undo-2" style="margin-right: 6px;"></i>กลับ</button>
                    <button type="submit" class="modal-btn modal-btn-primary">
                        <i data-lucide="save" style="margin-right: 6px;"></i>บันทึก</button>
                </div>
            </form>

        </div>
    </div>

    <!-- Delete Modal Categories -->
    <div id="deleteModalCategories" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal('deleteModalCategories')">&times;</button>
            <h2 class="modal-title">ลบชิ่อด้านการประเมิน</h2>
            <div class="modal-section-title">คำเตือน : การลบชื่อด้านการประเมินที่ถูกนำมาใช้แล้วจะไม่สามารถลบได้</div>

            <div class="delete-message">
                คุณต้องการลบข้อมูลด้านการประเมิน "<span id="deleteNameCategories"></span>" <br>
                (คะแนนเต็ม: <span id="deleteMaxScoreCategories"></span>) <br>
                มาตรฐาน: <span id="deleteStandardNameCategories"></span> <br>
                หรือไม่?
            </div>
            <form id="deleteFormCategories" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-buttons">
                    <button type="button" class="modal-btn modal-btn-secondary"
                        onclick="closeModal('deleteModalCategories')">
                        <i data-lucide="undo-2" style="margin-right: 6px;"></i>กลับ</button>
                    <button type="submit" class="modal-btn modal-btn-danger"><i data-lucide="x"
                            style="margin-right: 6px;"></i>ยืนยันการลบ</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Standards Modal Functions
        function openEditModalStandards(id, name) {
            document.getElementById('editNameStandards').value = name;
            document.getElementById('editFormStandards').action = `/standards/${id}`;
            document.getElementById('editModalStandards').classList.add('active');
            document.getElementById('currentstandardsName').innerText = name;
        }

        function openDeleteModalStandards(id, name) {
            document.getElementById('deleteNameStandards').textContent = name;
            document.getElementById('deleteFormStandards').action = `/standards/${id}`;
            document.getElementById('deleteModalStandards').classList.add('active');
        }

        // Categories Modal Functions
        function openEditModalCat(id, name, max_score, standard_id, standard_name) {
            document.getElementById('editNameCategories').value = name;
            document.getElementById('editMaxScore').value = max_score;
            document.getElementById('editStandardId').value = standard_id;
            document.getElementById('editFormCategories').action = `/categories/${id}`;
            document.getElementById('editModalCategories').classList.add('active');
            document.getElementById('currentcategoriesName').innerText = name;
            document.getElementById('currentcategoriesMaxScore').innerText = max_score;
            document.getElementById('currentcategoriesStandardName').innerText = standard_name;
        }

        function openDeleteModalCat(id, name, max_score, standard_name) {
            document.getElementById('deleteNameCategories').textContent = name;
            document.getElementById('deleteMaxScoreCategories').textContent = max_score;
            document.getElementById('deleteStandardNameCategories').textContent = standard_name;
            document.getElementById('deleteFormCategories').action = `/categories/${id}`;
            document.getElementById('deleteModalCategories').classList.add('active');
        }

        // Universal Modal Close Function
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        // ปิด modal เมื่อคลิกนอก modal content
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.remove('active');
            }
        });

        // ปิด modal เมื่อกด ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.active').forEach(modal => {
                    modal.classList.remove('active');
                });
            }
        });

        lucide.createIcons();
    </script>
    <script>
        // ---------- Helper: init ตาราง + event ชุดเดียว ----------
        function initTableControls({
            tableSelector,
            searchInputSelector,
            sortButtonSelector,
            sortDropdownSelector,
            clearSortSelector
        }) {
            // 1) Init DataTable
            const dt = $(tableSelector).DataTable({
                searching: true,
                lengthChange: false,
                dom: 'rtip',
                order: [], // ไม่มี default sort
                stateSave: false, // ไม่จำสถานะ
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

            // 2) ค้นหา (debounce เล็กน้อย)
            let typingTimer;
            const doSearch = (val) => dt.search(val).draw();

            $(searchInputSelector)
                .on('input', function() {
                    clearTimeout(typingTimer);
                    const val = this.value;
                    typingTimer = setTimeout(() => doSearch(val), 150);
                })
                .on('search', function() { // รองรับกด x เคลียร์
                    if (this.value === '') doSearch('');
                });

            // 3) เปิด/ปิด sort dropdown
            $(sortButtonSelector).on('click', function(e) {
                e.stopPropagation();
                $(sortDropdownSelector).toggleClass('hidden');
            });

            // 4) คลิกตัวเลือกเรียงลำดับ
            $(sortDropdownSelector).on('click', '.sort-option', function() {
                const col = Number($(this).data('column'));
                const order = String($(this).data('order')); // 'asc' | 'desc'
                dt.order([col, order]).draw(false);

                // อัปเดตข้อความปุ่มให้ผู้ใช้รู้ว่าตอนนี้เรียงตามอะไร
                const label = $(this).text().trim();
                const $btnSpan = $(sortButtonSelector).find('span').first();
                $btnSpan.text('เรียงลำดับ: ' + label);

                // ปิด dropdown
                $(sortDropdownSelector).addClass('hidden');
            });

            // 5) ล้างการเรียงลำดับ
            $(clearSortSelector).on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // ล้าง order → แล้ว fallback ให้เรียงคอลัมน์ลำดับ (0) จากน้อยไปมาก
                dt.order([]).draw(false);
                dt.order([0, 'asc']).draw(false);

                // รีเซ็ตข้อความปุ่ม + ปิด dropdown
                const $btnSpan = $(sortButtonSelector).find('span').first();
                $btnSpan.text('เรียงลำดับ');
                $(sortDropdownSelector).addClass('hidden');
            });

            return dt;
        }

        // ---------- ป้องกัน dropdown เปิดค้าง (คลิกภายนอกแล้วปิด) ----------
        function setupGlobalDropdownCloser(dropdownSelectors = []) {
            $(document).on('click', function(e) {
                // ถ้าคลิกนอก dropdown ทั้งหมด ให้ปิดทุก dropdown
                const clickedInsideAny = dropdownSelectors.some(sel => $(e.target).closest(sel).length > 0);
                if (!clickedInsideAny) {
                    dropdownSelectors.forEach(sel => $(sel).addClass('hidden'));
                }
            });
        }

        // ---------- เริ่มทำงานเมื่อ DOM พร้อม ----------
        $(function() {
            // Init สำหรับ Standards (#table1)
            const dt1 = initTableControls({
                tableSelector: '#table1',
                searchInputSelector: '#custom-search-standards',
                sortButtonSelector: '#sort-button-standards',
                sortDropdownSelector: '#sort-dropdown-standards',
                clearSortSelector: '#clear-sort-standards'
            });

            // Init สำหรับ Categories (#table2)
            const dt2 = initTableControls({
                tableSelector: '#table2',
                searchInputSelector: '#custom-search-categories',
                sortButtonSelector: '#sort-button-categories',
                sortDropdownSelector: '#sort-dropdown-categories',
                clearSortSelector: '#clear-sort-categories'
            });

            // ปิด dropdown เมื่อคลิกพื้นที่ว่าง (ครอบคลุมทั้งสองชุด)
            setupGlobalDropdownCloser([
                '#sort-dropdown-standards',
                '#sort-dropdown-categories'
            ]);

            // (ถ้ามี filter เพิ่มภายหลังค่อย bind แยกตามตารางแบบเดียวกับ sort/search)
        });
    </script>


    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // You might need to adjust this to show the correct modal based on which form had errors
                document.getElementById('editModalCategories').classList.add('active');
            });
        </script>
    @endif
    <style>
        .categories-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;

        }

        .categories-containers {
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

        .category-form {

            margin-bottom: 30px;
            position: relative;
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

        .categories-list {
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

        .categories-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .categories-item:last-child {
            border-bottom: none;
        }

        .categories-name {
            color: #333;
            font-size: 16px;
        }

        .categories-name::before {
            content: " ";
            color: #333;
            margin-right: 8px;
        }

        .categories-actions {
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

        .modal-section-title p {
            margin: 4px 0;
            /* ลดระยะห่างบน-ล่างของ <p> */
            font-size: 16px;
            /* ปรับขนาดข้อความให้เท่ากัน */
            line-height: 1.3;
            /* ปรับระยะบรรทัดให้อ่านง่าย */
        }

        .delete-message {
            line-height: 1.6;
            font-size: 16px;
            color: #333;
            text-align: center;
        }
    </style>
@endsection
