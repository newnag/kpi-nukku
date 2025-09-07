@extends('layouts.app')

@section('title', 'รายการตัวบ่งชี้ที่ได้รับมอบหมาย')

@section('header', 'รายการตัวบ่งชี้ที่ได้รับมอบหมาย')
@section('subheader', 'ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล')

@section('content')
    <div class="ml-3 mr-3 md:ml-9 md:mr-9 mb-9 space-y-5">
        <div class="flex flex-col md:flex-row md:justify-between gap-4 md:gap-2">
            <!-- Search & Filter Controls Group -->
            <div class="flex flex-wrap gap-2">
                <!-- Search -->
                <div class="relative w-full sm:w-auto bg-white rounded-lg shadow-sm">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input id="custom-search" type="text"
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none w-full"
                        placeholder="ค้นหารายการตัวบ่งชี้">
                </div>

                <!-- Sort -->
                <div class="relative inline-block text-left" id="sort-dropdown-container">
                    <button id="sort-button"
                        class="h-fit border border-gray-300 rounded-lg px-4 py-2 bg-white text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                        <span>เรียงลำดับ</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                        </svg>
                    </button>
                    <div id="sort-dropdown"
                        class="hidden absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                        <div class="py-1" role="menu" aria-orientation="vertical">
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="0" data-order="asc" role="menuitem">ปี (น้อยไปมาก)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="0" data-order="desc" role="menuitem">ปี (มากไปน้อย)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="1" data-order="asc" role="menuitem">ชื่อตัวบ่งชี้ (A-Z)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="1" data-order="desc" role="menuitem">ชื่อตัวบ่งชี้ (Z-A)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="6" data-order="asc" role="menuitem">คะแนนรวม (น้อยไปมาก)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="6" data-order="desc" role="menuitem">คะแนนรวม (มากไปน้อย)</button>
                            <button id="clear-sort"
                                class="text-left block w-full px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">ล้างตัวเรียงลำดับ</button>
                        </div>
                    </div>
                </div>

                <!-- Filter -->
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
                        class="hidden absolute left-0 mt-2 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                        <div class="py-2 px-3">
                            <!-- ปี -->
                            <h3 class="text-sm font-medium text-gray-900 mb-2">ปี</h3>
                            <div class="flex flex-wrap gap-3 mb-3">
                                @php
                                    $years = collect($indicators)->pluck('year')->unique()->sort()->values();
                                @endphp
                                @foreach ($years as $y)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" class="filter-option rounded text-blue-600" data-column="0"
                                            data-value="{{ $y }}">
                                        <span class="ml-2 text-sm text-gray-700">{{ $y }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <div class="border-t border-gray-200 my-3"></div>

                            <!-- สถานะตัวชี้วัด -->
                            <h3 class="text-sm font-medium text-gray-900 mb-2">สถานะตัวชี้วัด</h3>
                            <div class="space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="filter-option rounded text-blue-600" data-column="7"
                                        data-value="0">
                                    <span class="ml-2 text-sm text-gray-700">สมบูรณ์</span>
                                </label><br>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="filter-option rounded text-blue-600" data-column="7"
                                        data-value="1">
                                    <span class="ml-2 text-sm text-gray-700">ไม่ครบถ้วน</span>
                                </label><br>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="filter-option rounded text-blue-600" data-column="7"
                                        data-value="2">
                                    <span class="ml-2 text-sm text-gray-700">อยู่ระหว่างดำเนินการ</span>
                                </label>
                            </div>

                            <div class="border-t border-gray-200 my-3"></div>

                            <!-- สถานะเอกสาร -->
                            <h3 class="text-sm font-medium text-gray-900 mb-2">สถานะเอกสาร</h3>
                            <div class="space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="filter-option rounded text-blue-600" data-column="8"
                                        data-value="รอดำเนินการ">
                                    <span class="ml-2 text-sm text-gray-700">รอดำเนินการ</span>
                                </label><br>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="filter-option rounded text-blue-600" data-column="8"
                                        data-value="ไม่ครบ">
                                    <span class="ml-2 text-sm text-gray-700">ไม่ครบถ้วน</span>
                                </label><br>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="filter-option rounded text-blue-600" data-column="8"
                                        data-value="ครบ">
                                    <span class="ml-2 text-sm text-gray-700">ครบถ้วน</span>
                                </label>
                            </div>

                            <div class="border-t border-gray-200 my-3"></div>

                            <div class="flex justify-between">
                                <button id="clear-filters"
                                    class="text-sm text-gray-600 hover:text-gray-900">ล้างตัวกรอง</button>
                                <button id="apply-filters"
                                    class="text-sm text-white bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded">
                                    ใช้ตัวกรอง
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- (Optional) Right-side actions -->
            <div class="flex flex-wrap gap-3">
                <!-- Place for future actions if needed -->
            </div>
        </div>
    </div>

    <div class="border border-gray-200 rounded-lg shadow-sm overflow-x-auto">
        <table id="assignedTable" class="w-full">
            <thead>
                <tr>
                    <th class="w-16 px-4 py-3 text-sm font-medium text-gray-900 cursor-pointer select-none">
                        <div class="flex items-center justify-between">
                            <span>ปี</span>
                            <svg class="sort-icon w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                            </svg>
                        </div>
                    </th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-900 cursor-pointer select-none">
                        <div class="flex items-center justify-between">
                            <span>ชื่อตัวบ่งชี้</span>
                            <svg class="sort-icon w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                            </svg>
                        </div>
                    </th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-900 cursor-pointer select-none">
                        <div class="flex items-center justify-between">
                            <span>รหัส</span>
                            <svg class="sort-icon w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                            </svg>
                        </div>
                    </th>
                    <th
                        class="px-4 py-3 text-sm font-medium text-gray-900 cursor-pointer select-none hidden md:table-cell">
                        <div class="flex items-center justify-between">
                            <span>ประเภทองค์กร</span>
                            <svg class="sort-icon w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                            </svg>
                        </div>
                    </th>
                    <th
                        class="px-4 py-3 text-sm font-medium text-gray-900 cursor-pointer select-none hidden md:table-cell">
                        <div class="flex items-center justify-between">
                            <span>หน่วยงานที่รับผิดชอบ</span>
                            <svg class="sort-icon w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                            </svg>
                        </div>
                    </th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-900 cursor-pointer select-none">
                        <div class="flex items-center justify-between">
                            <span>คะแนนเต็ม</span>
                            <svg class="sort-icon w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                            </svg>
                        </div>
                    </th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-900 cursor-pointer select-none">
                        <div class="flex items-center justify-between">
                            <span>คะแนนรวม</span>
                            <svg class="sort-icon w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                            </svg>
                        </div>
                    </th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-900 cursor-pointer select-none">
                        <div class="flex items-center justify-between">
                            <span>สถานะตัวบ่งชี้</span>
                            <svg class="sort-icon w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                            </svg>
                        </div>
                    </th>
                    <th
                        class="px-4 py-3 text-sm font-medium text-gray-900 cursor-pointer select-none hidden sm:table-cell">
                        <div class="flex items-center justify-between">
                            <span>สถานะเอกสาร</span>
                            <svg class="sort-icon w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                            </svg>
                        </div>
                    </th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-900">จัดการ</th>
                </tr>
            </thead>
            <tbody class="bg-white">
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

                        // Status mapping for sorting/filtering:
                        // 0 = complete, 1 = not complete, 2 = in progress
                        $statusRaw = (int) ($indicator->status ?? -1);
                        if ($statusRaw === 2 || $statusRaw === 3) {
                            $statusCode = 0;
                            $statusText = 'สมบูรณ์';
                            $badgeCls = 'bg-green-100 text-green-800';
                        } elseif ($statusRaw === 4) {
                            $statusCode = 1;
                            $statusText = 'ไม่ครบถ้วน';
                            $badgeCls = 'bg-red-100 text-red-800';
                        } else {
                            $statusCode = 2;
                            $statusText = 'อยู่ระหว่างดำเนินการ';
                            $badgeCls = 'bg-yellow-100 text-yellow-800';
                        }

                        // Document status uses backend strings ("ไม่ครบ","ครบ", or others)
                        $doc = $indicator->doc_status;
                        if ($doc === 'ครบ') {
                            $docText = 'ครบถ้วน';
                            $docOrder = 1;
                            $docCls = 'bg-green-100 text-green-800';
                        } elseif ($doc === 'ไม่ครบ') {
                            $docText = 'ไม่ครบถ้วน';
                            $docOrder = 2;
                            $docCls = 'bg-red-100 text-red-800';
                        } else {
                            $docText = 'รอดำเนินการ';
                            $docOrder = 0;
                            $docCls = 'bg-gray-100 text-gray-800';
                        }
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $indicator->year }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $indicator->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $indicator->code }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 hidden md:table-cell">
                            {{ $indicator->type }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 hidden md:table-cell">
                            {{ $deptName ?: '-' }}
                        </td>
                        <!-- คะแนนเต็ม (max_score) -->
                        <td class="px-4 py-3 text-sm text-gray-900 text-center">
                            {{ number_format((float) $indicator->max_score, 2) }}
                        </td>
                        <!-- คะแนนรวม (score_acc) -->
                        <td class="px-4 py-3 text-sm text-gray-900 text-center">
                            {{ number_format((float) $indicator->score_acc, 2) }}
                        </td>

                        <!-- สถานะตัวบ่งชี้ -->
                        <td class="px-4 py-3 place-items-center" data-search="{{ $statusCode }}"
                            data-order="{{ $statusCode }}">
                            @if ($statusCode === 0)
                                <div class=" text-green-600">
                                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                                </div>
                            @elseif ($statusCode === 1)
                                <div class=" text-red-600">
                                    <i data-lucide="x-circle" class="w-5 h-5"></i>
                                </div>
                            @else
                                <div class=" text-yellow-600">
                                    <i data-lucide="timer" class="w-5 h-5"></i>
                                </div>
                            @endif
                        </td>

                        <!-- สถานะเอกสาร -->
                        <td class="px-4 py-3 place-items-center"
                            data-search="{{ $doc === 'ครบ' ? 'ครบ' : ($doc === 'ไม่ครบ' ? 'ไม่ครบ' : 'รอดำเนินการ') }}"
                            data-order="{{ $docOrder }}">
                            @if ($doc === 'ครบ')
                                <div class=" text-green-600">
                                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                                    {{-- <span class="text-xs font-medium">ครบถ้วน</span> --}}
                                </div>
                            @elseif ($doc === 'ไม่ครบ')
                                <div class=" text-red-600">
                                    <i data-lucide="x-circle" class="w-5 h-5"></i>
                                    {{-- <span class="text-xs font-medium">ไม่ครบถ้วน</span> --}}
                                </div>
                            @else
                                <div class=" text-gray-600">
                                    <i data-lucide="clock" class="w-5 h-5"></i>
                                    {{-- <span class="text-xs font-medium">รอดำเนินการ</span> --}}
                                </div>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('dashboardKpiUser.show', $indicator->id) }}"
                                class="inline-flex items-center justify-center px-3 py-1 bg-white border border-blue-500 text-blue-500 rounded-full text-xs font-medium hover:bg-blue-500 hover:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                ทำการประเมิน
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
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
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.469.0/dist/umd/lucide.min.js"></script>
    <script>
        $(function() {
            // Initialize DataTable
            const table = new DataTable('#assignedTable', {
                searching: true,
                language: {
                    paginate: {
                        previous: 'ก่อนหน้า',
                        next: 'ถัดไป'
                    },
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    lengthMenu: "แสดง _MENU_ รายการต่อหน้า",
                    emptyTable: "ไม่พบข้อมูล",
                    zeroRecords: "ไม่พบข้อมูลที่ตรงกับการค้นหา"
                },
                dom: '<"flex flex-col md:flex-row justify-between items-center p-3"<"flex-1 hidden"f><"flex"l>>' +
                    't' +
                    '<"flex flex-col md:flex-row justify-between items-center p-3"<"flex-1"i><"flex"p>>',
            });

            // Lucide refresh on draw
            $('#assignedTable').on('draw.dt', function() {
                if (window.lucide?.createIcons) lucide.createIcons();
            });

            // Custom search
            $('#custom-search').on('keyup input', function() {
                table.search(this.value).draw();
            }).on('search', function() {
                if (this.value === '') table.search('').draw();
            });

            // Sort dropdown
            $('#sort-button').on('click', function(e) {
                e.stopPropagation();
                $('#sort-dropdown').toggleClass('hidden');
                $('#filter-dropdown').addClass('hidden');
            });

            // Filter dropdown
            $('#filter-button').on('click', function(e) {
                e.stopPropagation();
                $('#filter-dropdown').toggleClass('hidden');
                $('#sort-dropdown').addClass('hidden');
            });

            // Close dropdowns when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#sort-dropdown-container, #filter-dropdown-container').length) {
                    $('#sort-dropdown, #filter-dropdown').addClass('hidden');
                }
            });

            // Handle sort options
            $('.sort-option').on('click', function() {
                const column = Number($(this).data('column'));
                const order = String($(this).data('order'));
                table.order([column, order]).draw();
                $('#sort-button span').text('เรียงลำดับ: ' + $(this).text().trim().substring(0, 18) +
                    '...');
                $('#sort-dropdown').addClass('hidden');
            });

            // Clear sort
            $('#clear-sort').on('click', function() {
                $('#sort-button span').text('เรียงลำดับ');
                table.order([]).draw();
                $('#sort-dropdown').addClass('hidden');
            });

            // ===== Filtering =====
            const escapeRegex = s => s.toString().replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const makeExactRegex = values => `^(?:${values.map(v => escapeRegex(String(v))).join('|')})$`;

            let activeFilters = {}; // { columnIndex: [values] }

            $('.filter-option').on('change', function() {
                const col = String($(this).data('column'));
                const val = String($(this).data('value'));
                if (!activeFilters[col]) activeFilters[col] = [];
                if ($(this).is(':checked')) {
                    if (!activeFilters[col].includes(val)) activeFilters[col].push(val);
                } else {
                    activeFilters[col] = activeFilters[col].filter(v => v !== val);
                    if (!activeFilters[col].length) delete activeFilters[col];
                }
            });

            $('#apply-filters').on('click', function() {
                // Clear previous column searches
                table.columns().search('');

                let count = 0;
                for (const col in activeFilters) {
                    if (activeFilters[col].length > 0) {
                        count += activeFilters[col].length;
                        const regex = makeExactRegex(activeFilters[col]);
                        table.column(Number(col)).search(regex, true, false);
                    }
                }

                $('#filter-button span').text(count > 0 ? `กรองข้อมูล (${count})` : 'กรองข้อมูล');
                table.draw();
                $('#filter-dropdown').addClass('hidden');
            });

            $('#clear-filters').on('click', function() {
                $('.filter-option').prop('checked', false);
                activeFilters = {};
                $('#filter-button span').text('กรองข้อมูล');
                table.columns().search('').draw();
            });
        });
    </script>
@endpush
