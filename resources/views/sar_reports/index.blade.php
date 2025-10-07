@extends('layouts.app')
@section('title', 'รายการรายงาน SAR')
@section('header', 'รายการรายงาน SAR')
@section('subheader', 'ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล')

@section('content')
    <div class="flex flex-col sm:flex-row justify-between gap-4 mb-4">
        <div class="flex flex-col sm:flex-row flex-wrap gap-2 w-full sm:w-auto">
            <div class="relative w-full sm:w-auto bg-white rounded-lg shadow-sm min-w-64">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" id="custom-search" placeholder="ค้นหารายการตัวบ่งชี้"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40" />
            </div>

            <!-- Sort Button with Dropdown -->
            <div class="relative inline-block text-left" id="sort-dropdown-container">
                <button id="sort-button" class="btns">
                    <span>เรียงลำดับ</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                    </svg>
                </button>
                <div id="sort-dropdown"
                    class="hidden absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                    <div class="py-1" role="menu" aria-orientation="vertical">
                        <button class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            data-column="0" data-order="asc" role="menuitem">ปี (น้อยไปมาก)</button>
                        <button class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            data-column="0" data-order="desc" role="menuitem">ปี (มากไปน้อย)</button>
                        <button class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            data-column="1" data-order="asc" role="menuitem">ชื่อรายงาน (A-Z)</button>
                        <button class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            data-column="1" data-order="desc" role="menuitem">ชื่อรายงาน (Z-A)</button>
                        <button class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            data-column="2" data-order="asc" role="menuitem">วันที่สร้าง (เก่า→ใหม่)</button>
                        <button class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            data-column="2" data-order="desc" role="menuitem">วันที่สร้าง (ใหม่→เก่า)</button>
                        <button id="clear-sort"
                            class=" text-left block w-full px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">ล้างตัวเรียงลำดับ</button>
                    </div>
                </div>
            </div>


        </div>
        <div>
            <x-year-export-modal :years="$years" context="year-export"
                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md inline-flex items-center space-x-2 shadow">
                <i data-lucide="upload" class="w-4 h-4"></i>
                <span>EXPORT</span>
            </x-year-export-modal>
        </div>
    </div>
    <div class=" mx-auto bg-white shadow rounded ">


        <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
            <table id="myTable" class="w-full min-w-full">
                <thead>
                    <tr>
                        <th class="text-xs sm:text-sm font-medium text-gray-900 text-center">ปี</th>
                        <th class="text-xs sm:text-sm font-medium text-gray-900 text-left">ชื่อเอกสาร</th>
                        <th class="text-xs sm:text-sm font-medium text-gray-900 text-center">วันที่สร้างเอกสาร</th>
                        <th class="text-xs sm:text-sm font-medium text-gray-900 text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($reports as $r)
                        <tr>
                            <td class="text-xs sm:text-sm text-gray-700">{{ $r->year }}</td>
                            <td class="text-xs sm:text-sm text-gray-700">{{ $r->title }}</td>
                            <td class="text-xs sm:text-sm text-gray-700 ">
                                {{ $r->created_at ? $r->created_at->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-xs sm:text-sm text-gray-700">
                                <div x-data="{
                                    open: false,
                                    pos: { top: 0, left: 0 },
                                    menuWidth: 180,
                                    position() {
                                        const r = this.$refs.trigger.getBoundingClientRect();
                                        let left = r.right - this.menuWidth;
                                        let top = r.bottom + 8;
                                        const spaceBelow = window.innerHeight - r.bottom;
                                        const spaceAbove = r.top;
                                        const mh = this.$refs.menu ? this.$refs.menu.offsetHeight : 160;
                                        if (spaceBelow < mh + 8 && spaceAbove > spaceBelow) {
                                            top = r.top - mh - 8;
                                        }
                                        this.pos = { top, left };
                                    }
                                }" class="relative inline-block text-left"
                                    @scroll.window="open && position()" @resize.window="open && position()">

                                    <!-- ปุ่มหลัก (3-dot menu) -->
                                    <button type="button" x-ref="trigger" @click="position(); open = !open"
                                        @keydown.escape.window="open=false"
                                        class="inline-flex items-center p-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm 
                   hover:bg-gray-200 focus:outline-none transition">
                                        <i data-lucide="more-vertical" class="w-5 h-5 text-gray-700"></i>
                                    </button>

                                    <!-- เมนูหลัก -->
                                    <template x-teleport="body">
                                        <div x-show="open" x-ref="menu" @click.away="open = false"
                                            @keydown.escape.window="open=false" x-transition
                                            :style="`position: fixed; top: ${pos.top}px; left: ${pos.left}px; width: ${menuWidth}px;`"
                                            class="mt-2 bg-white rounded-md shadow-lg border border-gray-300 z-[9999]">

                                            <div class="py-1 text-gray-800 text-sm font-medium">
                                                <!-- ปุ่มแก้ไข -->
                                                <a href="{{ route('sar_reports.edit', $r->id) }}"
                                                    class="flex items-center px-4 py-2 hover:bg-gray-100 transition">
                                                    <i data-lucide="edit-3" class="w-4 h-4 mr-2 text-gray-600"></i>
                                                    แก้ไขข้อมูล
                                                </a>

                                                <!-- เส้นคั่น -->
                                                <div class="border-t border-gray-200 my-1"></div>

                                                <!-- ปุ่มลบ -->
                                                <form id="del-sar-{{ $r->id }}" action="{{ route('sar_reports.destroy', $r->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-modal title="ยืนยันการลบข้อมูล" size="sm">
                                                        <x-slot:trigger>
                                                            <button type="button"
                                                                class="flex items-center w-full px-4 py-2 text-red-700 hover:bg-red-50 transition">
                                                                <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i> ลบข้อมูล
                                                            </button>
                                                        </x-slot:trigger>

                                                        <div class="space-y-2">
                                                            <p class="text-slate-700">คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลนี้?</p>
                                                        </div>

                                                        <x-slot:footer>
                                                            <div class="flex justify-between gap-5">
                                                                <button type="button" class="btn btn-outline" @click="$dispatch('modal:close')">ยกเลิก</button>
                                                                <button type="button" class="btn btn-danger" onclick="document.getElementById('del-sar-{{ $r->id }}').submit()">ยืนยันการลบ</button>
                                                            </div>
                                                        </x-slot:footer>
                                                    </x-modal>
                                                </form>

                                                <!-- เส้นคั่น -->
                                                <div class="border-t border-gray-200 my-1"></div>

                                                <!-- ปุ่ม Export (Dropdown ซ้อน) -->
                                                <div x-data="{ open: false }" class="relative">
                                                    <button type="button" @click="open = !open"
                                                        class="flex items-center w-full px-4 py-2 hover:bg-gray-100 transition">
                                                        <i data-lucide="download" class="w-4 h-4 mr-2 text-gray-600"></i>
                                                        ส่งออกเอกสาร
                                                        <i data-lucide="chevron-right"
                                                            class="ml-auto w-4 h-4 text-gray-500"></i>
                                                    </button>

                                                    <!-- Submenu Export -->
                                                    <div x-show="open" @click.away="open = false" x-cloak
                                                        class="absolute left-full top-0 ml-1 w-48 bg-white border border-gray-300 rounded-md shadow-lg z-50">

                                                        <!-- DOCX -->
                                                        <a href="{{ route('sar_reports.export.docx', $r->id) }}"
                                                            class="flex items-center px-4 py-2 hover:bg-gray-100 transition">
                                                            <i data-lucide="file-text"
                                                                class="w-4 h-4 mr-2 text-blue-700"></i> Word (DOCX)
                                                        </a>

                                                        <!-- Excel -->
                                                        <a href="{{ route('sar_reports.export.xlsx', $r->id) }}"
                                                            class="flex items-center px-4 py-2 hover:bg-gray-100 transition">
                                                            <i data-lucide="file-spreadsheet"
                                                                class="w-4 h-4 mr-2 text-green-700"></i> Excel
                                                        </a>

                                                        <!-- PDF Preview -->
                                                        <a href="{{ route('sar_reports.export.pdf', $r->id) }}"
                                                            target="_blank"
                                                            class="flex items-center px-4 py-2 hover:bg-gray-100 transition">
                                                            <i data-lucide="file" class="w-4 h-4 mr-2 text-red-700"></i>
                                                            PDF Preview
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                </div>
                            </td>

                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.dataTables.min.css">
    <style>
        .btns {
            width: 100%;
            justify-content: center;
        }

        .btns {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #374151;
            padding: 8px 12px;
            border-radius: 8px;
        }

        .btns:hover {
            background: #f9fafb;
        }

        table.dataTable thead th {
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
        }

        table.dataTable thead th:hover {
            background-color: #f3f4f6;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.5rem 1rem;
            margin-left: 0.25rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            background-color: white;
            color: #4b5563 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #3b82f6 !important;
            color: white !important;
        }

        table.dataTable tbody tr {
            background-color: inherit !important;
        }

        #myTable_wrapper {
            max-width: 100%;
            overflow-x: auto;
        }

        #myTable,
        table.dataTable {
            width: 100% !important;
        }

        #myTable tbody tr {
            transition: background-color 0.15s ease, transform 0.05s ease;
            cursor: pointer;
            /* reinforce clickable rows */
        }

        #myTable tbody tr:hover {
            /* Default hover for rows without specific class */
            background-color: #dbeafe !important;
            /* slate-50 */
        }

        table.dataTable tbody tr {
            background-color: inherit !important;
        }

        #myTable tbody tr:hover td {
            background-color: inherit !important;
        }

        /* Visual accent stripe on left edge for quick scanning */
        #myTable tbody tr.assigned-row {
            box-shadow: inset 4px 0 0 0 #3b82f6;
            /* blue-500 */
        }

        #myTable tbody tr.unassigned-row {
            box-shadow: inset 4px 0 0 0 #cbd5e1;
            /* slate-300 */
        }

        /* Subtle base background to distinguish unassigned rows even without hover */
        #myTable tbody tr.unassigned-row {
            background-color: #f9fafb !important;
            /* gray-50 */
        }

        #myTable tbody tr.unassigned-row:hover {
            background-color: #e5e7eb !important;
            /* gray-200 */
        }

        /* Hover variants for assignment state */
        #myTable tbody tr.assigned-row:hover {
            background-color: #dbeafe !important;
            /* blue-100 */
        }

        /* Slightly dim text for unassigned rows to reduce visual weight */
        #myTable tbody tr.unassigned-row td {
            /* color: #6b7280; */
            /* gray-600 */
        }

        #myTable tbody tr.unassigned-row:hover td {
            /* background-color: #e5e7eb !important; */
            /* gray-200 */
        }

        #myTable tbody tr.assigned-row:hover td {
            /* background-color: #dbeafe !important; */
            /* blue-100 */
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Store default label text for each section to support reset
            const sectionDefaults = {};
            $('.dropdown-multiselect').each(function() {
                const id = this.id;
                const $label = $(this).find('.dropdown-btn span').first();
                sectionDefaults[id] = $label.text().trim();
            });

            // Toggle inner section dropdown (scoped; avoid clobbering global navbar toggle)
            window.toggleFilterDropdown = function(sectionId) {
                if (!sectionId || typeof sectionId !== 'string') return;
                $('.dropdown-multiselect').not('#' + sectionId).removeClass('open');
                $('#' + sectionId).toggleClass('open');
            };
            // Initialize DataTable
            let table = new DataTable('#myTable', {
                // Remove default search box since we have a custom one
                searching: true,
                responsive: true,
                autoWidth: false,
                // Customize pagination and info text
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
                // Adjust the DOM structure for Tailwind CSS compatibility
                dom: '<"flex flex-col md:flex-row justify-between items-center p-3"<"flex-1 hidden"f><"flex"l>>' +
                    't' +
                    '<"flex flex-col md:flex-row justify-between items-center p-3"<"flex-1"i><"flex"p>>',

            });

            // Adjust columns to fill available width
            setTimeout(() => {
                table.columns.adjust().draw(false);
            }, 0);
            $(window).on('resize', function() {
                table.columns.adjust();
            });

            // Connect custom search box to DataTable with real-time search
            $('#custom-search').on('keyup input', function() {
                table.search(this.value).draw();
            });

            // Clear search when input is empty
            $('#custom-search').on('search', function() {
                if (this.value === '') {
                    table.search('').draw();
                }
            });

            $('#export_button').on('click', function() {
                alert('Export to Excel functionality will be implemented here');
            });

            // $('#add_indicator_button').on('click', function() {
            //     window.location.href = "{{ route('indicator.create') }}";
            // });

            // Sorting dropdown functionality
            $('#sort-button').on('click', function(e) {
                e.stopPropagation();
                $('#sort-dropdown').toggleClass('hidden');
                $('#filter-dropdown').addClass('hidden'); // Close other dropdown
            });

            // Filter dropdown functionality
            $('#filter-button').on('click', function(e) {
                e.stopPropagation();
                $('#filter-dropdown').toggleClass('hidden');
                $('#sort-dropdown').addClass('hidden'); // Close other dropdown
                $('.dropdown-multiselect').removeClass('open'); // Close all filter section dropdowns
            });

            // Close dropdowns when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest(
                        '#sort-dropdown-container, #filter-dropdown-container, .dropdown-multiselect')
                    .length) {
                    $('#sort-dropdown, #filter-dropdown').addClass('hidden');
                    $('.dropdown-multiselect').removeClass('open');
                }
            });

            // Handle sort options
            $('.sort-option').on('click', function() {
                const column = $(this).data('column');
                const order = $(this).data('order');

                // Apply sorting
                table.order([column, order]).draw();

                // Update button text to show active sort
                $('#sort-button span').text('เรียงลำดับ: ' + $(this).text().trim().substring(0, 15) +
                    '...');

                // Close dropdown
                $('#sort-dropdown').addClass('hidden');
            });

            // Track active filters
            let activeFilters = {};

            // Helper: update a section label based on selected options
            function updateSectionLabel(sectionId) {
                const $section = $('#' + sectionId);
                const $checked = $section.find('.filter-option:checked');
                const labelId = '#' + sectionId.replace('Dropdown', '-label');
                if ($checked.length === 0) {
                    $(labelId).text(sectionDefaults[sectionId] || $(labelId).text());
                    return;
                }
                const names = $checked.map(function() {
                    const txt = $(this).next('span').text().trim();
                    return txt || String($(this).data('value'));
                }).get();
                if (names.length <= 2) {
                    $(labelId).text(names.join(', '));
                } else {
                    $(labelId).text(`${names[0]}, ${names[1]} +${names.length - 2}`);
                }
            }

            // Helper: rebuild activeFilters for a given column
            function rebuildColumnFilter(column) {
                const values = $(`.filter-option[data-column="${column}"]:checked`).map(function() {
                    return $(this).data('value');
                }).get();
                if (values.length) {
                    activeFilters[column] = values;
                } else {
                    delete activeFilters[column];
                }
            }

            // Handle filter checkboxes
            $('.filter-option').on('change', function() {
                const column = String($(this).data('column'));
                rebuildColumnFilter(column);
                const sectionId = $(this).closest('.dropdown-multiselect').attr('id');
                if (sectionId) updateSectionLabel(sectionId);
            });

            // In-dropdown search per section
            $('.filter-search').on('input', function() {
                const q = $(this).val().toString().toLowerCase();
                const sectionId = $(this).closest('.dropdown-tools').data('section');
                if (!sectionId) return;
                const $content = $('#' + sectionId + ' .dropdown-content');
                $content.find('label').each(function() {
                    const txt = $(this).text().toLowerCase();
                    $(this).toggle(txt.indexOf(q) !== -1);
                });
            });

            // Select-all / Clear-all buttons within a section
            $('.dropdown-tools .tool-btn').on('click', function() {
                const action = $(this).data('action');
                const sectionId = $(this).closest('.dropdown-tools').data('section');
                if (!sectionId) return;
                const $section = $('#' + sectionId);
                const $checks = $section.find('.filter-option');
                if (action === 'select-all') {
                    $checks.prop('checked', true).trigger('change');
                } else if (action === 'clear-all') {
                    $checks.prop('checked', false).trigger('change');
                }
                updateSectionLabel(sectionId);
            });

            // helper: escape regex
            const escapeRegex = s => s.toString().replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            // helper: multiple exact choices
            const makeExactRegex = (values) => `^(?:${values.map(escapeRegex).join('|')})$`;

            $('#apply-filters').on('click', function() {
                // clear previous
                table.columns().search('');

                let filterCount = 0;

                for (const column in activeFilters) {
                    if (activeFilters[column].length > 0) {
                        filterCount += activeFilters[column].length;

                        const colIdx = Number(column);

                        // Column 6 (Departments): cell contains multiple dept tags; match any selected
                        if (colIdx === 6) {
                            const regex = activeFilters[column]
                                .map(v => escapeRegex(String(v)))
                                .join('|');
                            table.column(colIdx).search(regex, true, false);
                            continue;
                        }

                        // Column 9 (Status): use exact Thai labels present in sr-only text
                        if (colIdx === 9) {
                            const regex = makeExactRegex(activeFilters[column].map(String));
                            table.column(colIdx).search(regex, true, false);
                            continue;
                        }

                        // Column 11 (is_assigned hidden): values are '1' or '0'
                        if (colIdx === 11) {
                            const regex = makeExactRegex(activeFilters[column].map(String));
                            table.column(colIdx).search(regex, true, false);
                            continue;
                        }

                        // Other columns: exact matching (0: year, 1: category, 2: standard, 5: type)
                        const regex = makeExactRegex(activeFilters[column].map(String));
                        table.column(colIdx).search(regex, true, false);
                    }
                }

                $('#filter-button span').text(filterCount > 0 ? `กรองข้อมูล (${filterCount})` :
                    'กรองข้อมูล');

                // Override filter button text with count while preserving base label
                (function() {
                    const $filterText = $('#filter-button span');
                    const baseText = $filterText.data('base') || $filterText.text().replace(
                        /\s*\(.*\)$/, '');
                    $filterText.data('base', baseText);
                    $filterText.text(filterCount > 0 ? `${baseText} (${filterCount})` : baseText);
                })();

                table.draw();
                $('#filter-dropdown').addClass('hidden');
            });



            // Clear filters button
            $('#clear-filters').on('click', function() {
                // Uncheck all filter checkboxes
                $('.filter-option').prop('checked', false);

                // Clear filter tracking
                activeFilters = {};

                // Reset button text
                $('#filter-button span').text('กรองข้อมูล');

                // Clear all column searches
                table.columns().search('').draw();

                // Reset labels
                $('#year-label').text('เลือกปี');
                $('#standard-label').text('เลือกมาตรฐาน');
                $('#dimension-label').text('เลือกด้าน');
                $('#dept-label').text('เลือกหน่วยงาน');
                $('#type-label').text('เลือกประเภท');
                $('#status-label').text('เลือกสถานะ');
                $('#assigned-label').text('เลือกการมอบหมาย');
            });

            // Clear sort button
            $('#clear-sort').on('click', function() {
                // Reset all column orders
                $('#sort-button span').text('เรียงลำดับ');
                table.order([]).draw();
                // Close dropdown
                $('#sort-dropdown').addClass('hidden');
                // // Reset sort icon states
                // $('.sort-icon').removeClass('sorting_asc sorting_desc').addClass('sorting');
                // $('.sort-icon').css('opacity', '0.3');
                // $('.sort-icon').css('transform', 'rotate(0deg)');


            });

            // Override clear-filters with improved reset logic
            $('#clear-filters').off('click').on('click', function() {
                // Uncheck all filter checkboxes
                $('.filter-option').prop('checked', false);

                // Clear filter tracking
                activeFilters = {};

                // Clear all column searches
                table.columns().search('').draw();

                // Reset each section label to its default
                Object.keys(sectionDefaults).forEach(function(sectionId) {
                    const labelId = '#' + sectionId.replace('Dropdown', '-label');
                    $(labelId).text(sectionDefaults[sectionId]);
                });

                // Reset main filter button text to base label
                const $filterText = $('#filter-button span');
                const baseText = $filterText.data('base') || $filterText.text().replace(/\s*\(.*\)$/, '');
                $filterText.data('base', baseText);
                $filterText.text(baseText);
            });


        });
    </script>
@endpush
