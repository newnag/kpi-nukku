@extends('layouts.app')
@section('title', 'เอกสารและหลักฐาน')
@section('header', 'เอกสารและหลักฐาน')
@section('subheader', 'ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล')
@section('content')

    <div class="evidence-container">


        <!-- Controls -->
        <div class="controls">
            <!-- Search -->
            <div class="search-box" style="width:100%; max-width:420px;">
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" style="color:#9ca3af;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" id="custom-search" class="search-input" placeholder="ค้นหาไฟล์ / ตัวชี้วัด">
            </div>

            <!-- Sort -->
            <div class="dropdown" id="sort-dropdown-container">
                <button id="sort-button" class="btns">
                    <span>เรียงลำดับ</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                    </svg>
                </button>
                <div id="sort-dropdown" class="dropdown-menus hidden" role="menu">
                    <button class="dropdown-item sort-option" data-column="8" data-order="asc">ปี (น้อย→มาก)</button>
                    <button class="dropdown-item sort-option" data-column="8" data-order="desc">ปี (มาก→น้อย)</button>
                    <button class="dropdown-item sort-option" data-column="1" data-order="asc">ชื่อไฟล์ (A-Z)</button>
                    <button class="dropdown-item sort-option" data-column="1" data-order="desc">ชื่อไฟล์ (Z-A)</button>
                    <button class="dropdown-item sort-option" data-column="3" data-order="asc">ประเภทไฟล์ (A-Z)</button>
                    <button class="dropdown-item sort-option" data-column="3" data-order="desc">ประเภทไฟล์ (Z-A)</button>
                    <div class="dropdown-divider"></div>
                    <button id="clear-sort" type="button" class="dropdown-item"
                        style="color:#4b5563;">ล้างการเรียงลำดับ</button>
                </div>
            </div>

            <!-- Filter -->
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

                        {{-- ปี --}}
                        <h3 class="dropdown-title">ปี</h3>
                        <div class="dropdown-multiselect" id="yearDropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('yearDropdown')">
                                <span id="year-label">เลือกปี</span>
                                <i style="font-size:12px;">▼</i>
                            </div>
                            <div class="dropdown-content">
                                @foreach ($years as $year)
                                    <label>
                                        <input type="checkbox" class="filter-option" data-column="8"
                                            data-value="{{ $year }}">
                                        <span style="margin-left:6px;">{{ $year }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        {{-- มาตรฐาน --}}
                        <h3 class="dropdown-title">มาตรฐาน</h3>
                        <div class="dropdown-multiselect" id="standardDropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('standardDropdown')">
                                <span id="standard-label">เลือกมาตรฐาน</span>
                                <i style="font-size:12px;">▼</i>
                            </div>
                            <div class="dropdown-content">
                                @foreach ($standards as $std)
                                    <label>
                                        <input type="checkbox" class="filter-option" data-column="9"
                                            data-value="{{ $std }}">
                                        <span style="margin-left:6px;">{{ $std }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        {{-- ด้าน --}}
                        <h3 class="dropdown-title">ด้าน</h3>
                        <div class="dropdown-multiselect" id="dimensionDropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('dimensionDropdown')">
                                <span id="dimension-label">เลือกด้าน</span>
                                <i style="font-size:12px;">▼</i>
                            </div>
                            <div class="dropdown-content">
                                @foreach ($dimensions as $dim)
                                    <label>
                                        <input type="checkbox" class="filter-option" data-column="10"
                                            data-value="{{ $dim }}">
                                        <span style="margin-left:6px;">{{ $dim }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        {{-- ผู้รับผิดชอบ --}}
                        <h3 class="dropdown-title">ผู้รับผิดชอบ</h3>
                        <div class="dropdown-multiselect" id="collectorDropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('collectorDropdown')">
                                <span id="collector-label">เลือกผู้รับผิดชอบ</span>
                                <i style="font-size:12px;">▼</i>
                            </div>
                            <div class="dropdown-content">
                                @foreach ($collectors as $collector)
                                    <label>
                                        <input type="checkbox" class="filter-option" data-column="5"
                                            data-value="{{ $collector }}">
                                        <span style="margin-left:6px;">{{ $collector }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        {{-- หน่วยงาน --}}
                        <h3 class="dropdown-title">หน่วยงาน</h3>
                        <div class="dropdown-multiselect" id="deptDropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('deptDropdown')">
                                <span id="dept-label">เลือกหน่วยงาน</span>
                                <i style="font-size:12px;">▼</i>
                            </div>
                            <div class="dropdown-content">
                                @foreach ($departments as $dept)
                                    <label>
                                        <input type="checkbox" class="filter-option" data-column="12"
                                            data-value="{{ $dept }}">
                                        <span style="margin-left:6px;">{{ $dept }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        {{-- ประเภทไฟล์ --}}
                        <h3 class="dropdown-title">ประเภทไฟล์</h3>
                        <div class="dropdown-multiselect" id="typeDropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('typeDropdown')">
                                <span id="type-label">เลือกประเภทไฟล์</span>
                                <i style="font-size:12px;">▼</i>
                            </div>
                            <div class="dropdown-content">
                                @foreach ($fileTypes as $type)
                                    <label>
                                        <input type="checkbox" class="filter-option" data-column="3"
                                            data-value="{{ $type }}">
                                        <span style="margin-left:6px;">{{ $type }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        {{-- สถานะ --}}
                        <h3 class="dropdown-title">สถานะ</h3>
                        <div class="dropdown-multiselect" id="statusDropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('statusDropdown')">
                                <span id="status-label">เลือกสถานะ</span>
                                <i style="font-size:12px;">▼</i>
                            </div>
                            <div class="dropdown-content">
                                @foreach ($statusList as $statusText)
                                    <label>
                                        <input type="checkbox" class="filter-option" data-column="11"
                                            data-value="{{ $statusText }}">
                                        <span style="margin-left:6px;">{{ $statusText }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        {{-- Buttons --}}
                        <div style="display:flex; justify-content:space-between; gap:12px;">
                            <button id="clear-filters" class="btn">ล้างตัวกรอง</button>
                            <button id="apply-filters" class="btn btn-primary">ใช้ตัวกรอง</button>
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
                            <th>ลำดับ</th> <!-- 0 -->
                            <th>ชื่อไฟล์</th> <!-- 1 -->
                            <th>ขนาดไฟล์</th> <!-- 2 -->
                            <th>ประเภทไฟล์</th> <!-- 3 -->
                            <th>วันที่อัปโหลด</th> <!-- 4 -->
                            <th>ชื่อผู้อัปโหลด</th> <!-- 5 -->
                            <th>ตัวชี้วัด</th> <!-- 6 -->
                            <th>จัดการ</th> <!-- 7 -->

                            <!-- ✅ hidden columns -->
                            <th style="display:none;">ปี</th> <!-- 8 -->
                            <th style="display:none;">มาตรฐาน</th> <!-- 9 -->
                            <th style="display:none;">ด้าน</th> <!-- 10 -->
                            <th style="display:none;">สถานะ</th> <!-- 11 -->
                            <th style="display:none;">หน่วยงาน</th> <!-- 12 -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($evidences as $index => $evidence)
                            @php
                                $indicator = $evidence->criteria->indicator ?? null;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @php $previewUrl = evidence_preview_url($evidence); @endphp
                                    @php $isOffice = in_array($evidence->type, ['doc','docx','xls','xlsx','ppt','pptx']); @endphp

                                    @if ($previewUrl && !$isOffice)
                                        <a href="{{ $previewUrl }}" target="_blank" rel="noopener noreferrer"
                                            class="block w-full text-left hover:bg-gray-50 rounded p-2">
                                            <div class="file-info flex items-start space-x-2">
                                                <div class="file-icon">
                                                    @if ($evidence->type === 'pdf')
                                                        <i data-lucide="file-text" style="color:#dc2626;"></i>
                                                    @elseif (in_array($evidence->type, ['doc', 'docx']))
                                                        <i data-lucide="file-text" style="color:#2563eb;"></i>
                                                    @elseif (in_array($evidence->type, ['ppt', 'pptx']))
                                                        <i data-lucide="file-text" style="color:#eb7e25;"></i>
                                                    @elseif (in_array($evidence->type, ['jpg', 'jpeg', 'png', 'gif', 'svg']))
                                                        <i data-lucide="image" style="color:#16a34a;"></i>
                                                    @elseif (in_array($evidence->type, ['xls', 'xlsx']))
                                                        <i data-lucide="file-spreadsheet" style="color:#059669;"></i>
                                                    @elseif ($evidence->type === 'url')
                                                        <i data-lucide="link" style="color:#9333ea;"></i>
                                                    @else
                                                        <i data-lucide="file" style="color:#6b7280;"></i>
                                                    @endif
                                                </div>
                                                <div class="file-details">
                                                    <div class="file-name text-blue-600 underline hover:text-blue-800">
                                                        {{ $evidence->name }}
                                                    </div>
                                                    @if ($evidence->detail)
                                                        <div class="file-description text-sm text-gray-500">
                                                            {{ Str::limit(strip_tags($evidence->detail), 50) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                    @elseif($isOffice)
                                        <div
                                            class="block w-full text-left rounded p-2 hover:bg-gray-50 cursor-not-allowed">
                                            <div class="file-info flex items-start space-x-2">
                                                <div class="file-icon">
                                                    @if (in_array($evidence->type, ['doc', 'docx']))
                                                        <i data-lucide="file-text" style="color:#2563eb;"></i>
                                                    @elseif (in_array($evidence->type, ['ppt', 'pptx']))
                                                        <i data-lucide="file-text" style="color:#eb7e25;"></i>
                                                    @elseif (in_array($evidence->type, ['xls', 'xlsx']))
                                                        <i data-lucide="file-spreadsheet" style="color:#059669;"></i>
                                                    @else
                                                        <i data-lucide="file" style="color:#6b7280;"></i>
                                                    @endif
                                                </div>
                                                <!-- ✅ Tooltip -->
                                                <div class="file-details relative group">
                                                    <div class="file-name text-gray-700 inline-block cursor-not-allowed">
                                                        {{ $evidence->name }}
                                                    </div>
                                                    <div
                                                        class="pointer-events-none absolute left-1/2 -translate-x-1/2 top-full mt-1 opacity-0 group-hover:opacity-100 w-max bg-gray-800 text-white text-xs rounded px-2 py-1 shadow z-50">
                                                        ดาวน์โหลดเท่านั้น
                                                    </div>

                                                    @if ($evidence->detail)
                                                        <div class="file-description text-sm text-gray-500">
                                                            {{ Str::limit(strip_tags($evidence->detail), 50) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400">ไม่มีไฟล์</span>
                                    @endif
                                </td>




                                <td>{{ $evidence->total_size_human ?? '-' }}</td>
                                <td data-search="{{ $evidence->type }}">{{ $evidence->type }}</td>
                                <td data-order="{{ optional($evidence->created_at)->timestamp }}">
                                    {{ $evidence->created_at?->format('M d, Y') ?? '-' }}
                                </td>
                                <td data-search="{{ optional($evidence->user)->name ?? '' }}">
                                    {{ $evidence->user->name ?? '-' }}
                                </td>
                                @php
                                    $is_assigned = (bool) ($indicator['is_assigned'] ?? false);
                                    $rowUrl =
                                        auth()->user() && auth()->user()->hasRole('user')
                                            ? route('dashboardkpi.user.show', [
                                                'id' => $indicator['id'],
                                                'is_assigned' => $is_assigned,
                                            ])
                                            : route('dashboardkpi.admin.show', [
                                                'id' => $indicator['id'],
                                                'is_assigned' => $is_assigned,
                                            ]);

                                    $rowClass = $is_assigned ? 'assigned-row' : 'unassigned-row';
                                @endphp

                                <td data-search="{{ optional($indicator)->code ?? '' }}"
                                    onclick="window.location='{{ $rowUrl }}';"
                                    class="relative group cursor-pointer hover:bg-gray-50">

                                    <span>{{ $indicator->name ?? '-' }}</span>

                                    <!-- Tooltip -->
                                    <div
                                        class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2
         bg-gray-800 text-white text-xs rounded px-2 py-1 shadow-lg whitespace-nowrap z-50
         invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-opacity duration-150">
                                        กำลังไปหน้าการกรอกคะแนนตัวชี้วัด {{ $indicator->name ?? '-' }}
                                    </div>


                                </td>


                                <td>
                                    <div class="evidence-actions">
                                        @if ($evidence->type === 'url' && !empty($evidence->path['urls'][0]))
                                            <button type="button" class="btn-link"
                                                onclick="window.open('{{ $evidence->path['urls'][0] }}', '_blank')">
                                                <i data-lucide="external-link"></i> เปิดลิงก์
                                            </button>
                                        @else
                                            <button type="button" class="btn-download"
                                                onclick="window.location.href='{{ route('evidences.download', $evidence->id) }}'">
                                                <i data-lucide="download"></i> ดาวน์โหลด
                                            </button>
                                        @endif
                                    </div>
                                </td>

                                <!-- ✅ hidden values for filtering -->
                                <td style="display:none;">{{ $indicator->year ?? '' }}</td>
                                <td style="display:none;">{{ $indicator->category->standard->name ?? '' }}</td>
                                <td style="display:none;">{{ $indicator->category->name ?? '' }}</td>
                                <td style="display:none;">{{ $statusMap[$indicator->status ?? 0] ?? 'ไม่ทราบ' }}</td>
                                <td style="display:none;">
                                    {{ $indicator->assignments->first()?->collectorUser?->department->name ?? '-' }}</td>
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

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.469.0/dist/umd/lucide.min.js"></script>
    @push('scripts')
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
    @endpush
    <script>
        function openPreview(url, title) {
            document.getElementById('previewTitle').textContent = title;
            document.getElementById('previewContent').innerHTML =
                `<iframe src="${url}" class="w-full h-full"></iframe>`;
            document.getElementById('previewModal').classList.remove('hidden');
        }

        function closePreview() {
            document.getElementById('previewModal').classList.add('hidden');
        }
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

        .evidence-container {
            max-width: 1500px;
            margin: 0 auto;

        }

        .evidence-container h1 {
            margin: 0 0 8px;
            font-size: 24px;
            color: #111827;
        }

        .table-container {
            overflow: visible !important;
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
            background: white;
            border-radius: 10px;
            padding: 30px;
            border: 2px solid #C2D9EB;
            margin-top: 40px;
            margin-bottom: 40px;
            margin-left: 40px;
            margin-right: 40px;
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
