@extends('layouts.app')

@section('title', 'รายการตัวบ่งชี้ที่ได้รับมอบหมาย')

@section('header', 'รายการตัวบ่งชี้ที่ได้รับมอบหมาย')
@section('subheader', 'ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล')

@section('content')
    <div class="space-y-5">
        <div class="flex gap-2 flex-wrap">
            <div class="relative w-full sm:w-auto bg-white rounded-lg shadow-sm">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" style="color:#9ca3af;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" id="custom-search" placeholder="ค้นหารายการตัวบ่งชี้"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40" />
            </div>
            <div class="dropdown" id="sort-dropdown-container">
                <button id="sort-button" class="btns">
                    <span>เรียงลำดับ</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                    </svg>
                </button>
                <div id="sort-dropdown" class="dropdown-menus hidden" role="menu" aria-orientation="vertical">
                    <button class="dropdown-item sort-option" data-column="0" data-order="asc" role="menuitem">ปี(น้อยไปมาก)</button>
                    <button class="dropdown-item sort-option" data-column="0" data-order="desc" role="menuitem">ปี(มากไปน้อย)</button>
                    <button class="dropdown-item sort-option" data-column="1" data-order="asc" role="menuitem">ชื่อตัวบ่งชี้(A-Z)</button>
                    <button class="dropdown-item sort-option" data-column="1" data-order="desc"
                        role="menuitem">ชื่อตัวบ่งชี้(Z-A)</button>
                    <button class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        data-column="7" data-order="asc" role="menuitem">ผลลัพธ์ (น้อยไปมาก)</button>
                    <button class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        data-column="7" data-order="desc" role="menuitem">ผลลัพธ์ (มากไปน้อย)</button>
                    <div class="dropdown-divider"></div>
                    <button id="clear-sort" type="button" class="dropdown-item"
                        style="color:#4b5563;">ล้างการเรียงลำดับ</button>
                </div>
            </div>


            {{-- Filter --}}
            <div class="dropdown" id="filter-dropdown-container">
                <button id="filter-button" class="btns">
                    <span>กรองข้อมูล</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                </button>

                <div id="filter-dropdown" class="dropdown-menus hidden">
                    <div style="padding:12px 12px;">

                        {{-- Section: ปี --}}
                        <h3 class="dropdown-title">ปี</h3>
                        <div class="dropdown-multiselect" id="yearDropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('yearDropdown')">
                                <span id="year-label">เลือกปี</span>
                                <i style="font-size:12px;">▼</i>
                            </div>
                            <div class="dropdown-content">
                                @foreach ($indicators->pluck('year')->unique()->sortDesc() as $year)
                                    <label style="display:flex; align-items:center; margin-bottom:4px;">
                                        <input type="checkbox" class="filter-option year-option" data-column="0"
                                            data-value="{{ $year }}">
                                        <span style="margin-left:6px; font-size:14px; color:#374151;">
                                            {{ $year }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>


                        {{-- Section: มาตรฐาน --}}
                        <h3 class="dropdown-title">มาตรฐาน</h3>
                        <div class="dropdown-multiselect" id="standardDropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('standardDropdown')">
                                <span id="standard-label">เลือกมาตรฐาน</span>
                                <i style="font-size:12px;">▼</i>
                            </div>
                            <div class="dropdown-content">
                                @foreach ($indicators->pluck('category.standard.name')->unique() as $std)
                                    <label>
                                        <input type="checkbox" class="filter-option standard-option" data-column="1"
                                            data-value="{{ $std }}">
                                        <span style="margin-left:6px;">{{ $std }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        {{-- Section: ด้าน --}}
                        <h3 class="dropdown-title">ด้าน</h3>
                        <div class="dropdown-multiselect" id="dimensionDropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('dimensionDropdown')">
                                <span id="dimension-label">เลือกด้าน</span>
                                <i style="font-size:12px;">▼</i>
                            </div>
                            <div class="dropdown-content">
                                @foreach ($indicators->pluck('category.name')->unique() as $dim)
                                    <label>
                                        <input type="checkbox" class="filter-option dimension-option" data-column="2"
                                            data-value="{{ $dim }}">
                                        <span style="margin-left:6px;">{{ $dim }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        {{-- Section: ผู้รับผิดชอบ --}}
                        <h3 class="dropdown-title">ผู้รับผิดชอบ</h3>
                        <div class="dropdown-multiselect" id="collectorDropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('collectorDropdown')">
                                <span id="collector-label">เลือกผู้รับผิดชอบ</span>
                                <i style="font-size:12px;">▼</i>
                            </div>
                            <div class="dropdown-content">
                                @foreach ($indicators->map(fn($i) => $i->assignments->first()->collectorUser->name ?? '-')->unique() as $collector)
                                    <label>
                                        <input type="checkbox" class="filter-option collector-option" data-column="??"
                                            data-value="{{ $collector }}">
                                        <span style="margin-left:6px;">{{ $collector }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        {{-- Section: หน่วยงานที่รับผิดชอบ --}}
                        <h3 class="dropdown-title">หน่วยงานที่รับผิดชอบ</h3>
                        <div class="dropdown-multiselect" id="deptDropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('deptDropdown')">
                                <span id="dept-label">เลือกหน่วยงาน</span>
                                <i style="font-size:12px;">▼</i>
                            </div>
                            <div class="dropdown-content">
                                @foreach ($indicators->map(fn($i) => $i->assignments->first()?->collectorUser?->department->name ?? '-')->unique() as $dept)
                                    <label>
                                        <input type="checkbox" class="filter-option dept-option" data-column="6"
                                            data-value="{{ $dept }}">
                                        <span style="margin-left:6px;">{{ $dept }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        {{-- Section: ประเภทองค์กร --}}
                        <h3 class="dropdown-title">ประเภทตัวชี้วัด</h3>
                        <div class="dropdown-multiselect" id="typeDropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('typeDropdown')">
                                <span id="type-label">เลือกประเภท</span>
                                <i style="font-size:12px;">▼</i>
                            </div>
                            <div class="dropdown-content">
                                @foreach ($indicators->pluck('type')->unique() as $type)
                                    <label style="display:flex; align-items:center; margin-bottom:4px;">
                                        <input type="checkbox" class="filter-option type-option" data-column="3"
                                            data-value="{{ $type }}">
                                        <span style="margin-left:6px; font-size:14px; color:#374151;">
                                            {{ $type }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        {{-- Section: สถานะ --}}
                        <h3 class="dropdown-title">สถานะตัวชี้วัด</h3>
                        <div class="dropdown-multiselect" id="statusDropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('statusDropdown')">
                                <span id="status-label">เลือกสถานะ</span>
                                <i style="font-size:12px;">▼</i>
                            </div>
                            @php
                                $statusMap = [
                                    0 => 'รอดำเนินการ',
                                    1 => 'รอดำเนินการ / บันทึกร่าง',
                                    2 => 'รอดำเนินการ / บันทึกจริง',
                                    3 => 'ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรการ',
                                    4 => 'ผลการดำเนินงานยังไม่ครบถ้วนตามเกณฑ์',
                                ];

                                $statusList = $indicators
                                    ->pluck('status')
                                    ->unique()
                                    ->map(fn($s) => $statusMap[$s] ?? 'ไม่ทราบ');
                            @endphp

                            <div class="dropdown-content">
                                @foreach ($statusList as $statusText)
                                    <label>
                                        <input type="checkbox" class="filter-option status-option" data-column="9"
                                            data-value="{{ $statusText }}">
                                        <span style="margin-left:6px;">{{ $statusText }}</span>
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


        <!-- ตารางรายการตัวบ่งชี้ -->
        <div class="evidence-containers">
            <div class="evidence-list">
                <table class="table" id="evidenceTable">
                    <thead>
                        <tr>
                            <th>ปี</th>
                            <th>มาตรฐานตัวชี้วัด</th>
                            <th>ด้านตัวชี้วัด</th>
                            <th>ชื่อตัวบ่งชี้</th>
                            <th>รหัส</th>
                            <th>ประเภทตัวชี้วัด</th>
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
                                <td class="status-cell">{{ $standardName ?: '-' }}</td>
                                <td class="status-cell">{{ $dimensionName ?: '-' }}</td>
                                <td>{{ $indicator->name }}</td>
                                <td class="status-cell">{{ $indicator->code }}</td>
                                <td class="status-cell">{{ $indicator->type }}</td>
                                <td class="status-cell">{{ $deptName ?: '-' }}</td>
                                <td class="status-cell">{{ $indicator->score_acc }}</td>
                                <td class="status-cell">{{ $indicator->max_score }}</td>

                                <td class="status-cell">
                                    @switch($indicator->status)
                                        @case(0)
                                            <span class="tooltip" data-tooltip="รอดำเนินการ">
                                                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i>
                                                <span class="sr-only">รอดำเนินการ</span>
                                            </span>
                                        @break

                                        @case(1)
                                            <span class="tooltip" data-tooltip="รอดำเนินการ / บันทึกร่าง">
                                                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i>
                                                <span class="sr-only">รอดำเนินการ / บันทึกร่าง</span>
                                            </span>
                                        @break

                                        @case(2)
                                            <span class="tooltip" data-tooltip="รอดำเนินการ / บันทึกจริง">
                                                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i>
                                                <span class="sr-only">รอดำเนินการ / บันทึกจริง</span>
                                            </span>
                                        @break

                                        @case(3)
                                            <span class="tooltip" data-tooltip="ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรการ">
                                                <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
                                                <span class="sr-only">ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรการ</span>
                                            </span>
                                        @break

                                        @case(4)
                                            <span class="tooltip" data-tooltip="ผลการดำเนินงานยังไม่ครบถ้วนตามเกณฑ์">
                                                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i>
                                                <span class="sr-only">ผลการดำเนินงานยังไม่ครบถ้วนตามเกณฑ์</span>
                                            </span>
                                        @break

                                        @default
                                            <span class="tooltip" data-tooltip="สถานะไม่ระบุ">
                                                <i data-lucide="help-circle" class="w-5 h-5 text-gray-400"></i>
                                                <span class="sr-only">สถานะไม่ระบุ</span>
                                            </span>
                                    @endswitch
                                </td>

                                <td class="status-cell">
                                    @switch($indicator->doc_status)
                                        @case('ไม่ครบ')
                                            <span
                                                class=" status-badge px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-700">
                                                ไม่ครบ
                                            </span>
                                        @break

                                        @case('ครบ')
                                            <span
                                                class="status-badge px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700">
                                                ครบ
                                            </span>
                                        @break

                                        @default
                                            <span
                                                class="status-badge px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700">
                                                รอดำเนินการ
                                            </span>
                                    @endswitch
                                </td>

                                <td>
                                    <div class="evidence-actions">
                                        @role('user')
                                            <a href="{{ route('dashboardkpi.user.show', $indicator->id) }}"
                                                class="btn-edit flex items-center gap-1" title="ทำการประเมิน">
                                                <i data-lucide="edit"></i>
                                                <span>ทำการประเมิน</span>
                                            </a>
                                            @elserole('super_admin|system_admin|qa_admin')
                                            <a href="{{ route('dashboardkpi.admin.show', $indicator->id) }}"
                                                class="btn-view flex items-center gap-1" title="ตรวจสอบ">
                                                <i data-lucide="eye"></i>
                                                <span>ตรวจสอบ</span>
                                            </a>
                                        @endrole
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.dataTables.min.css">
    <style>
        /* DataTables + Tailwind polish (minimal) */
        table.dataTable thead th {
            position: relative;
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
        }

        table.dataTable thead .sorting:after,
        table.dataTable thead .sorting_asc:after,
        table.dataTable thead .sorting_desc:after {
            display: none;
        }

        table.dataTable thead th:hover {
            background-color: #f3f4f6;
        }

        table.dataTable thead th .sort-icon {
            opacity: .3;
            transition: transform .2s ease, opacity .2s ease;
        }

        table.dataTable thead th.sorting_asc .sort-icon {
            opacity: 1;
            color: #2563eb;
            transform: rotate(180deg);
        }

        table.dataTable thead th.sorting_desc .sort-icon {
            opacity: 1;
            color: #2563eb;
        }

        @media (max-width: 640px) {
            table.dataTable {
                font-size: .875rem;
            }

            table.dataTable thead th,
            table.dataTable tbody td {
                padding: 8px 4px;
            }

            table.dataTable thead th .sort-icon {
                display: none;
            }

            table.dataTable thead th.sorting,
            table.dataTable thead th.sorting_asc,
            table.dataTable thead th.sorting_desc {
                padding-right: 8px;
            }
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            /* อย่างน้อย 250px ถ้ามีที่ว่างจะแบ่ง 1fr */
            gap: 16px 24px;
            max-height: 70vh;
            overflow-y: auto;
            padding: 16px;
            box-sizing: border-box;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.469.0/dist/umd/lucide.min.js"></script>
    @push('scripts')
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
                    $('#type-label').text('เลือกประเภท');

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
            // อัปเดต label ของ multiselect
            function setupDropdownLabel(dropdownId, labelId, defaultText) {
                const checkboxes = document.querySelectorAll(`#${dropdownId} .filter-option`);
                const label = document.getElementById(labelId);

                checkboxes.forEach(cb => {
                    cb.addEventListener('change', () => {
                        const selected = Array.from(checkboxes)
                            .filter(x => x.checked)
                            .map(x => x.getAttribute('data-value'));

                        label.textContent = selected.length ? selected.join(', ') : defaultText;
                    });
                });
            }

            setupDropdownLabel('yearDropdown', 'year-label', 'เลือกปี');
            setupDropdownLabel('standardDropdown', 'standard-label', 'เลือกมาตรฐาน');
            setupDropdownLabel('dimensionDropdown', 'dimension-label', 'เลือกด้าน');
            setupDropdownLabel('collectorDropdown', 'collector-label', 'เลือกผู้รับผิดชอบ');
            setupDropdownLabel('deptDropdown', 'dept-label', 'เลือกหน่วยงาน');

            setupDropdownLabel('typeDropdown', 'type-label', 'เลือกประเภท');
            setupDropdownLabel('statusDropdown', 'status-label', 'เลือกสถานะ');


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
    @endpush
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

        #filter-dropdown {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px 24px;
            max-height: 70vh;
            overflow-y: auto;
            padding: 16px;
            box-sizing: border-box;
        }

        #filter-dropdown .dropdown-title {
            grid-column: span 2;
            /* ✅ ให้หัวข้อใหญ่กินเต็มแถว */
            margin-top: 8px;
        }


        .tooltip {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        .tooltip::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 125%;
            /* tooltip อยู่ด้านบน */
            /* left: 50%; */
            /* transform: translateX(-50%);
                                                                                                background: #333; */
            color: #fff;
            font-size: 12px;
            /* padding: 5px 8px; */
            border-radius: 6px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease-in-out;
            z-index: 999;
        }

        .tooltip:hover::after {
            opacity: 1;
        }

        .hidden {
            display: none !important;
        }

        /*
                    .evidence-container {
                        max-width: 1500px;
                        margin: 0 auto;

                    } */

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
        .btns {
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

        .btns:hover {
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


        .dropdown-menus {
            position: absolute;
            left: 0;
            top: 100%;
            margin-top: 8px;
            width: 100%;
            /* ✅ ใช้ 100% ของ container (เท่าปุ่ม) */
            background: var(--white);
            border-radius: 6px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, .1), 0 4px 6px -2px rgba(0, 0, 0, .05);
            border: 1px solid rgba(0, 0, 0, .05);
            z-index: 9999;
            padding: 4px 0;
            display: block;
            box-sizing: border-box;
            /* ✅ กัน padding บวกเกิน */
            min-width: max-content;
            /* ✅ กัน dropdown เล็กเกินถ้ามีข้อความยาว */
        }

        .dropdown-menus.hidden {
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

        .dropdown-multiselect .dropdown-btn span,
        .dropdown-multiselect .dropdown-content span {
            white-space: normal;
            /* ✅ ข้อความยาวจะตัดบรรทัด */
            word-break: break-word;
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
            /* background: white;
                            border-radius: 10px;
                            padding: 30px;
                            border: 2px solid #C2D9EB;
                            margin-top: 40px;
                            margin-bottom: 40px;
                            margin-left: 40px;
                            margin-right: 40px; */
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
            justify-content: center;
            min-width: 64px;
            /* กำหนดความกว้างขั้นต่ำ ให้ badge กว้างเท่ากัน */
            padding: 4px 10px;
            border-radius: 9999px;
            /* pill shape */
            font-size: 13px;
            font-weight: 500;
            line-height: 1.4;
            text-align: center;
            white-space: nowrap;
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

        .btn-view {
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid var(--green-600);
            background: var(--white);
            cursor: pointer;
            font-size: 12px;
            white-space: nowrap;
            color: var(--green-600);
        }

        .btn-view :hover {
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
                /* margin-left: 20px;
                                margin-right: 20px;
                                padding: 20px; */
            }
        }

        i[data-lucide] {
            display: inline-block;
            vertical-align: middle;
        }

        .dropdown-multiselect {
            position: relative;
            display: inline-block;
            width: 100%;
            /* ✅ ให้กว้างเต็ม cell ของ grid */
            min-width: 200px;
            /* ✅ แต่ไม่ต่ำกว่า 200px */
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

        .dropdown-content label {
            display: flex;
            /* ✅ จัด checkbox + text เป็น flex */
            align-items: center;
            /* ✅ ให้ text อยู่กึ่งกลาง checkbox */
            gap: 6px;
            /* ✅ ระยะห่างระหว่างกล่องกับข้อความ */
            margin-bottom: 6px;
            /* ✅ ระยะห่างระหว่างแถว */
            font-size: 14px;
            color: #374151;
            /* เทาเข้ม */
            cursor: pointer;
        }

        .dropdown-content input[type="checkbox"] {
            flex-shrink: 0;
            /* ✅ กัน checkbox หด */
        }
    </style>
