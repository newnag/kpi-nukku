@extends('layouts.app')

@section('title', 'รายการตัวบ่งชี้')

@section('header', 'รายการตัวบ่งชี้')
@section('subheader', 'ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล')

@section('content')
    <div class="space-y-5">
        <div class="flex justify-between">
            <div class="flex flex-wrap gap-2 ">
                <div class="relative w-screen sm:w-auto bg-white rounded-lg shadow-sm ">
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
                        class="hidden absolute left-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                        <div class="py-1" role="menu" aria-orientation="vertical">
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="0" data-order="asc" role="menuitem">ปี (น้อยไปมาก)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="0" data-order="desc" role="menuitem">ปี (มากไปน้อย)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="3" data-order="asc" role="menuitem">ชื่อตัวบ่งชี้ (A-Z)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="3" data-order="desc" role="menuitem">ชื่อตัวบ่งชี้ (Z-A)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="7" data-order="asc" role="menuitem">ผลลัพธ์ (น้อยไปมาก)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="7" data-order="desc" role="menuitem">ผลลัพธ์ (มากไปน้อย)</button>
                            <button id="clear-sort"
                                class=" text-left block w-full px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">ล้างตัวเรียงลำดับ</button>
                        </div>
                    </div>
                </div>

                <!-- Filter Button with Dropdown (styled like assigned dashboard) -->
                <div class="dropdown" id="filter-dropdown-container">
                    <button id="filter-button" class="btns">
                        <span>กรองข้อมูล</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </button>

                    <div id="filter-dropdown" class="dropdown-menus hidden">
                        <div class="filters-grid">
                            <div class="filter-section">
                                {{-- Section: ปี --}}
                                <h3 class="dropdown-title">ปี</h3>
                                <div class="dropdown-multiselect" id="yearDropdown">
                                    <div class="dropdown-btn" onclick="toggleDropdown('yearDropdown')">
                                        <span id="year-label">เลือกปี</span>
                                        <i style="font-size:12px;">▼</i>
                                    </div>
                                    <div class="dropdown-content">
                                        <div class="dropdown-tools" data-section="yearDropdown">
                                            <input type="text" class="filter-search" placeholder="ค้นหา..."
                                                aria-label="ค้นหาปี">
                                            <div class="tools-actions">
                                                <button type="button" class="tool-btn"
                                                    data-action="select-all">เลือกทั้งหมด</button>
                                                <button type="button" class="tool-btn"
                                                    data-action="clear-all">ล้างทั้งหมด</button>
                                            </div>
                                        </div>
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
                            </div>

                            <div class="filter-section">
                                {{-- Section: มาตรฐาน --}}
                                <h3 class="dropdown-title">มาตรฐาน</h3>
                                <div class="dropdown-multiselect" id="standardDropdown">
                                    <div class="dropdown-btn" onclick="toggleDropdown('standardDropdown')">
                                        <span id="standard-label">เลือกมาตรฐาน</span>
                                        <i style="font-size:12px;">▼</i>
                                    </div>

                                    <div class="dropdown-content">
                                        <div class="dropdown-tools" data-section="standardDropdown">
                                            <input type="text" class="filter-search" placeholder="ค้นหา..."
                                                aria-label="ค้นหามาตรฐาน">
                                            <div class="tools-actions">
                                                <button type="button" class="tool-btn"
                                                    data-action="select-all">เลือกทั้งหมด</button>
                                                <button type="button" class="tool-btn"
                                                    data-action="clear-all">ล้างทั้งหมด</button>
                                            </div>
                                        </div>
                                        @foreach ($indicators->pluck('standard.name')->unique() as $std)
                                            <label>
                                                <input type="checkbox" class="filter-option standard-option"
                                                    data-column="2" data-value="{{ $std }}">
                                                <span style="margin-left:6px;">{{ $std }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="filter-section">
                                {{-- Section: ด้าน --}}
                                <h3 class="dropdown-title">ด้าน</h3>
                                <div class="dropdown-multiselect" id="dimensionDropdown">
                                    <div class="dropdown-btn" onclick="toggleDropdown('dimensionDropdown')">
                                        <span id="dimension-label">เลือกด้าน</span>
                                        <i style="font-size:12px;">▼</i>
                                    </div>
                                    <div class="dropdown-content">
                                        <div class="dropdown-tools" data-section="dimensionDropdown">
                                            <input type="text" class="filter-search" placeholder="ค้นหา..."
                                                aria-label="ค้นหาด้าน">
                                            <div class="tools-actions">
                                                <button type="button" class="tool-btn"
                                                    data-action="select-all">เลือกทั้งหมด</button>
                                                <button type="button" class="tool-btn"
                                                    data-action="clear-all">ล้างทั้งหมด</button>
                                            </div>
                                        </div>
                                        @foreach ($indicators->pluck('category.name')->unique() as $dim)
                                            <label>
                                                <input type="checkbox" class="filter-option dimension-option"
                                                    data-column="1" data-value="{{ $dim }}">
                                                <span style="margin-left:6px;">{{ $dim }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="filter-section">
                                {{-- Section: หน่วยงานที่รับผิดชอบ --}}
                                <h3 class="dropdown-title">หน่วยงานที่รับผิดชอบ</h3>
                                <div class="dropdown-multiselect" id="deptDropdown">
                                    <div class="dropdown-btn" onclick="toggleDropdown('deptDropdown')">
                                        <span id="dept-label">เลือกหน่วยงาน</span>
                                        <i style="font-size:12px;">▼</i>
                                    </div>
                                    <div class="dropdown-content">
                                        <div class="dropdown-tools" data-section="deptDropdown">
                                            <input type="text" class="filter-search" placeholder="ค้นหา..."
                                                aria-label="ค้นหาหน่วยงาน">
                                            <div class="tools-actions">
                                                <button type="button" class="tool-btn"
                                                    data-action="select-all">เลือกทั้งหมด</button>
                                                <button type="button" class="tool-btn"
                                                    data-action="clear-all">ล้างทั้งหมด</button>
                                            </div>
                                        </div>
                                        @php
                                            $deptOptions = $indicators
                                                ->flatMap(function ($i) {
                                                    return collect($i['assignments'] ?? [])
                                                        ->pluck('user.department_name')
                                                        ->filter();
                                                })
                                                ->unique()
                                                ->values();
                                        @endphp
                                        @foreach ($deptOptions as $dept)
                                            <label>
                                                <input type="checkbox" class="filter-option dept-option" data-column="6"
                                                    data-value="{{ $dept }}">
                                                <span style="margin-left:6px;">{{ $dept }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="filter-section">
                                {{-- Section: ประเภทตัวชี้วัด --}}
                                <h3 class="dropdown-title">ประเภทตัวชี้วัด</h3>
                                <div class="dropdown-multiselect" id="typeDropdown">
                                    <div class="dropdown-btn" onclick="toggleDropdown('typeDropdown')">
                                        <span id="type-label">เลือกประเภท</span>
                                        <i style="font-size:12px;">▼</i>
                                    </div>
                                    <div class="dropdown-content">
                                        <div class="dropdown-tools" data-section="typeDropdown">
                                            <input type="text" class="filter-search" placeholder="ค้นหา..."
                                                aria-label="ค้นหาประเภท">
                                            <div class="tools-actions">
                                                <button type="button" class="tool-btn"
                                                    data-action="select-all">เลือกทั้งหมด</button>
                                                <button type="button" class="tool-btn"
                                                    data-action="clear-all">ล้างทั้งหมด</button>
                                            </div>
                                        </div>
                                        @foreach ($indicators->pluck('type')->unique() as $type)
                                            <label style="display:flex; align-items:center; margin-bottom:4px;">
                                                <input type="checkbox" class="filter-option type-option" data-column="5"
                                                    data-value="{{ $type ?? 'ไม่ระบุ' }}">
                                                <span style="margin-left:6px; font-size:14px; color:#374151;">
                                                    {{ $type ?? 'ไม่ระบุ' }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="filter-section">
                                {{-- Section: สถานะตัวชี้วัด --}}
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
                                        <div class="dropdown-tools" data-section="statusDropdown">
                                            <input type="text" class="filter-search" placeholder="ค้นหา..."
                                                aria-label="ค้นหาสถานะ">
                                            <div class="tools-actions">
                                                <button type="button" class="tool-btn"
                                                    data-action="select-all">เลือกทั้งหมด</button>
                                                <button type="button" class="tool-btn"
                                                    data-action="clear-all">ล้างทั้งหมด</button>
                                            </div>
                                        </div>
                                        @foreach ($statusList as $statusText)
                                            <label>
                                                <input type="checkbox" class="filter-option status-option"
                                                    data-column="9" data-value="{{ $statusText }}">
                                                <span style="margin-left:6px;">{{ $statusText }}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                </div>
                            </div>

                            <div class="filters-actions">
                                {{-- Buttons --}}
                                <div style="display:flex; justify-content:space-between; gap:12px; width:100%">
                                    <button id="clear-filters" class="btn"
                                        style="padding:6px 10px;">ล้างตัวกรอง</button>
                                    <button id="apply-filters" class="btn btn-primary"
                                        style="padding:6px 10px;">ใช้ตัวกรอง</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Group -->
            <div class="flex flex-wrap gap-3">
                <button id="export_button"
                    class="h-fit bg-green-500 hover:bg-green-600 text-white rounded-lg px-4 py-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12" />
                    </svg>
                    <span class="hidden sm:inline">EXPORT TO EXCEL</span>
                </button>
                <button id="add_indicator_button"
                    class="h-fit bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="hidden sm:inline">เพิ่มตัวชี้วัด</span>
                </button>
            </div>
        </div>

        <!-- ตารางรายการตัวบ่งชี้ -->
        <div class="border border-gray-200 rounded-lg shadow-sm xl:overflow-auto 2xl:overflow-visible">
            <table id="myTable" class="w-full">
                <thead>
                    <tr>
                        <th class="w-fit  text-sm font-medium text-gray-900 cursor-pointer select-none"
                            title="ปีของตัวชี้วัด">
                            <div class="flex items-center justify-center  min-w-6">
                                ปี
                            </div>
                        </th>
                        <th class="w-fit  text-sm font-medium text-gray-900 cursor-pointer select-none"
                            title="มาตรฐานตัวชี้วัด 3 หมวด">
                            <div class="flex items-center justify-center  min-w-15">
                                มาตรฐาน
                            </div>
                        </th>
                        <th class="w-fit  text-sm font-medium text-gray-900 cursor-pointer select-none"
                            title="ด้านตัวชี้วัดใน 3 หมวด">
                            <div class="flex items-center justify-center  min-w-20">
                                ด้าน
                            </div>
                        </th>
                        <th class="w-full  text-sm font-medium text-gray-900 text-left cursor-pointer select-none"
                            title="ชื่อตัวบ่งชี้">
                            <div class="flex items-center justify-center min-w-56 ">
                                ชื่อตัวบ่งชี้
                            </div>
                        </th>
                        <th class="w-fit  text-sm font-medium text-gray-900 cursor-pointer select-none"
                            title="รหัสตัวบ่งชี้">
                            <div class="flex items-center justify-center min-w-9 ">
                                รหัส
                            </div>
                        </th>
                        <th class="w-fit  text-sm font-medium text-gray-900 cursor-pointer select-none hidden md:table-cell"
                            title="ประเภทตัวบ่งชี้ (คุณภาพ, ปริมาณ, คุณภาพ/ปริมาณ)">
                            <div class="flex items-center justify-center min-w-11 ">
                                ประเภท
                            </div>
                        </th>
                        <th class="w-fit text-sm font-medium text-gray-900 cursor-pointer select-none hidden md:table-cell"
                            title="หน่วยงานที่รับผิดชอบในตัวบ่งชี้">
                            <div class="flex items-center justify-center min-w-48">
                                หน่วยงาน
                            </div>
                        </th>
                        <th class="w-fit  text-sm font-medium text-gray-900 cursor-pointer select-none"
                            title="ผลลัพธ์จากการกรอกข้อมูลของตัวบ่งชี้">
                            <div class="flex items-center justify-center min-w-9">
                                ผลลัพธ์
                            </div>
                        </th>
                        <th class="w-fit  text-sm font-medium text-gray-900 cursor-pointer select-none hidden sm:table-cell"
                            title="คะแนนเต็มของตัวบ่งชี้">
                            <div class="flex items-center justify-center min-w-9 text-nowrap">
                                คะแนนเต็ม
                            </div>
                        </th>
                        <th class="w-fit text-sm font-medium text-gray-900 cursor-pointer select-none"
                            title="สถานะของตัวบ่งชี้">
                            <div class="flex items-center justify-center w-fit">
                                สถานะ
                            </div>
                        </th>
                        <th class="w-fit  text-sm font-medium text-gray-900 cursor-pointer select-none hidden sm:table-cell"
                            title="สถานะเอกสาร">
                            <div class="flex items-center justify-center">
                                เอกสาร
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white ">
                    @forelse($indicators as $indicator)
                        <tr data-href="{{ route('indicator.show', $indicator['id']) }}" tabindex="0" role="link"
                            aria-label="เปิด {{ $indicator['name'] }}" title="คลิกเพื่อดูรายละเอียด">
                            <td class="max-w-6 text-sm text-gray-700 text-center align-top">{{ $indicator['year'] }}</td>
                            <td class="max-w-15 text-sm text-balance text-gray-700 align-top">
                                {{ $indicator['category']['name'] }}</td>
                            <td class="max-w-15 text-sm text-balance text-gray-700 align-top">
                                {{ $indicator['standard']['name'] }}</td>
                            <td class="max-w-full text-pretty text-sm text-gray-700 align-top truncate">
                                {{-- <span class="block truncate" title="{{ $indicator['name'] }}"> --}}
                                {{ $indicator['name'] }}
                                {{-- </span> --}}
                            </td>
                            <td class="max-w-9 text-center text-sm text-gray-700 truncate  align-top">
                                {{ $indicator['code'] }}</td>
                            <td class="max-w-11 text-sm text-gray-700 text-center align-top">
                                {{ $indicator['type'] ?? 'ไม่ระบุ' }}
                            </td>
                            <td class="text-sm text-gray-700 align-top max-w-full" data-rowlink-ignore>
                                @php
                                    // Unique, non-empty department names
                                    $departments = collect($indicator['assignments'] ?? [])
                                        ->pluck('user.department_name', 'user.department_id') // value, key
                                        ->filter(fn($name) => filled($name))
                                        ->unique() // de-dup by name
                                        ->values();

                                    $total = $departments->count();
                                @endphp

                                @if ($total === 0)
                                    <span class="text-gray-400">ไม่มีการมอบหมาย</span>
                                @else
                                    <div x-data="{ open: false }" class="flex flex-wrap gap-1 ">
                                        @foreach ($departments as $name)
                                            <span x-show="@json($loop->iteration <= 3) || open" x-cloak
                                                class="inline-flex items-center rounded-full bg-slate-50 text-slate-700 ring-1 ring-inset ring-slate-200 px-2 py-0.5 text-xs md:text-xs max-w-52 truncate"
                                                title="{{ $name }}">
                                                {{ $name }}
                                            </span>
                                        @endforeach

                                        @if ($total > 3)
                                            <button type="button"
                                                class=" inline-flex items-center rounded-full bg-slate-300 text-slate-700 ring-1 ring-inset ring-slate-200 px-2 py-0.5 text-xs md:text-xs hover:bg-slate-200"
                                                @click="open = !open" :aria-expanded="open.toString()"
                                                x-text="open ? 'แสดงน้อยลง' : '+{{ $total - 3 }}'"></button>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <td class=" text-sm text-gray-700 text-center align-top">
                                {{ number_format($indicator['score_acc'], 2) ?? '0.00' }}
                            </td>
                            <td class=" text-sm text-gray-700 text-center align-top">
                                {{ number_format($indicator['max_score'], 2) ?? '0.00' }}
                            </td>
                            @php
                                $statusCode = (int) ($indicator['status'] ?? -1);
                            @endphp

                            <td class=" text-sm text-center align-top" data-search="{{ $statusCode }}"
                                data-order="{{ $statusCode }}">
                                @switch($statusCode)
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

                            @php
                                // Derive document status from criteria statuses (0/1/2)
                                $criteria = collect($indicator['criteria'] ?? []);

                                // Normalize to string to handle 0 vs "0"
                                $has0 = $criteria->contains(fn($c) => (string) ($c['status'] ?? '') === '0');
                                $has2 = $criteria->contains(fn($c) => (string) ($c['status'] ?? '') === '2');

                                if ($criteria->isEmpty() || $has0) {
                                    $docText = 'รอดำเนินการ';
                                    $docOrder = 0;
                                    $badgeCls = 'bg-gray-100 text-gray-800';
                                } elseif ($has2) {
                                    $docText = 'ครบ';
                                    $docOrder = 1;
                                    $badgeCls = 'bg-green-100 text-green-800';
                                } else {
                                    $docText = 'ไม่ครบ';
                                    $docOrder = 2;
                                    $badgeCls = 'bg-red-100 text-red-800';
                                }
                            @endphp

                            <td class="align-top" data-search="{{ $docText }}" data-order="{{ $docOrder }}">
                                <div class="w-full items-center justify-center flex">
                                    <span
                                        class="flex w-fit justify-center text-center px-2 py-1 font-medium rounded-full truncate text-xs {{ $badgeCls }}">
                                        {{ $docText }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="10" class=" px-4 py-8 text-center text-gray-500">
                                    ไม่พบข้อมูลตัวบ่งชี้
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    @endsection

    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.dataTables.min.css">
        <style>
            .container {
                max-width: 1400px !important;
            }

            #myTable,
            table.dataTable {
                width: 100% !important;
            }

            /* .dataTables_wrapper {
                                background-color: ;
                            } */

            /* Length and Filter Controls */
            /* .dataTables_wrapper .dataTables_length,
                            .dataTables_wrapper .dataTables_filter {} */

            /* Dropdown Select Styling */
            .dataTables_wrapper .dataTables_length select {
                border: 1px solid #a0aec0;
                border-radius: 0.5rem;
                padding: 0.5rem;
                background-color: white;
            }

            .dataTables_wrapper .dataTables_info {
                color: #4b5563;
            }

            /* .dataTables_wrapper .dataTables_paginate {
                                padding-top: 1rem;
                            } */

            .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: 0.5rem 1rem;
                margin-left: 0.25rem;
                border: 1px solid #e2e8f0;
                border-radius: 0.5rem;
                background-color: white;
                color: #4b5563 !important;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                background-color: #f3f4f6 !important;
                border-color: #e2e8f0;
                color: #ffffff !important;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                background-color: #aaaaaa !important;
                border-color: #ffffff;
                color: white !important;
            }

            /* .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
                                background-color: #1d4ed8 !important;
                                border-color: #1d4ed8;
                            } */

            table.dataTable thead th,
            table.dataTable tbody td {
                padding: 10px 4px;

            }

            @media (max-width: 640px) {
                /* Mobile-specific styles */
                .container {
                    padding: 10px;
                }

                #myTable {
                    font-size: 0.8rem;
                }

                .btn-view {
                    font-size: 12px;
                    padding: 4px 10px;
                }
            }

            @media (max-width: 768px) {
                /* Tablet-specific styles */
                .container {
                    padding: 20px;
                }

                .dataTables_wrapper .dataTables_length,
                .dataTables_wrapper .dataTables_filter {
                    text-align: center;
                }

                .btn-view {
                    font-size: 13px;
                    padding: 6px 12px;
                }
            }

            @media (max-width: 1024px) {
                /* Small Desktop-specific styles */
                .container {
                    max-width: 900px;
                }

                .dropdown-menus {
                    min-width: 200px;
                }
            }

            @media (max-width: 1280px) {
                /* Medium Desktop-specific styles */
                .container {
                    max-width: 1100px;
                }

                .dropdown-menus {
                    min-width: 250px;
                }
            }

            @media (max-width: 1536px) {
                /* Large Desktop-specific styles */
                .container {
                    max-width: 1300px;
                }

                .dropdown-menus {
                    min-width: 300px;
                }
            }

            /* Table header styling for better sorting UX */
            table.dataTable thead th {
                position: relative;
                background-color: #f9fafb;
                border-bottom: 1px solid #e5e7eb;
                font-weight: 600;
            }

            /* Style for sort indicators */
            table.dataTable thead .sorting:after,
            table.dataTable thead .sorting_asc:after,
            table.dataTable thead .sorting_desc:after {
                position: absolute;
                right: 8px;
                display: none;
            }

            /* Custom sort icons */
            table.dataTable thead th .sort-icon {
                opacity: 0.3;
                transition: transform 0.2s ease, opacity 0.2s ease;
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

            /* Hover effect on sortable headers */
            table.dataTable thead th:hover {
                background-color: #f3f4f6;
            }

            table.dataTable thead th:hover .sort-icon {
                opacity: 0.8;
            }

            /* Row hover/focus highlight */
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

            /* #myTable tbody tr:active {
                        background-color: #e5e7eb !important;
                    } */

            table.dataTable tbody tr {
                background-color: inherit !important;
            }

            /* Keyboard accessibility: show focus clearly on focused row */
            #myTable tbody tr:focus,
            #myTable tbody tr:focus-visible,
            #myTable tbody tr:focus-within {
                /* outline: 2px solid #93c5fd; */
                /* outline-offset: -2px; */
                background-color: #f8fafc !important;
                /* slate-50 */
            }

            /* Better mobile display for DataTables */
            @media (max-width: 640px) {
                table.dataTable {
                    font-size: 0.875rem;
                }

                table.dataTable thead th,
                table.dataTable tbody td {
                    padding: 8px 4px;

                }

                /* Hide sort icons on very small screens */
                table.dataTable thead th .sort-icon {
                    display: none;
                }

                /* Make the whole header clickable on mobile */
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

            /* Custom tooltip to show sorting capability */
            .sort-tooltip {
                position: relative;
            }

            .sort-tooltip:hover:after {
                content: "คลิกเพื่อเรียงลำดับ";
                position: absolute;
                top: -30px;
                left: 50%;
                transform: translateX(-50%);
                background-color: rgba(0, 0, 0, 0.8);
                color: white;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 12px;
                white-space: nowrap;
                z-index: 10;

            }

            .btn-view {
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 14px;
                border-radius: 6px;
                border: 1px solid #3b82f6;
                background: transparent;
                /* ✅ โปร่งใส */
                cursor: pointer;
                font-size: 13px;
                font-weight: 500;
                white-space: nowrap;
                color: #3b82f6;
                transition: all 0.2s ease-in-out;
            }

            .btn-view:hover {
                background: #dbeafe;
            }

            .status-badge {

                align-items: center;
                justify-content: center;
                min-width: 64px;
                /* กำหนดความกว้างขั้นต่ำ ให้ badge กว้างเท่ากัน */
                padding: 4px 10px;

                /* pill shape */
                font-size: 13px;
                font-weight: 500;
                line-height: 1.4;
                text-align: center;
                white-space: nowrap;
            }

            /* Minimal styles for custom multiselect dropdown */
            .dropdown {
                position: relative;
                display: inline-block;
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

            .dropdown-menus {
                position: absolute;
                left: -50px;
                margin-top: 8px;
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
                z-index: 50;
                min-width: 280px;
            }

            .dropdown-menus .filters-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 12px;
                padding: 12px 12px;
            }

            .dropdown-menus .filter-section {
                display: flex;
                flex-direction: column;
                gap: 6px;
                width: 360px;
                font-size: 14px;
            }

            .dropdown-menus .filters-actions {
                grid-column: 1 / -1;
            }

            @media (min-width: 768px) {
                .dropdown-menus {
                    min-width: 720px;
                }

                .dropdown-menus .filters-grid {
                    grid-template-columns: 1fr 1fr;
                }
            }

            .dropdown-title {
                font-weight: 600;
                color: #111827;
                margin: 8px 0;
            }

            .dropdown-multiselect {
                border: 1px solid #e5e7eb;
                border-radius: 8px;
            }

            .dropdown-multiselect .dropdown-btn {
                display: flex;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                padding: 8px 10px;
                cursor: pointer;
            }

            .dropdown-multiselect .dropdown-content {
                display: none;
                max-height: 220px;
                overflow-y: auto;
                border-top: 1px solid #e5e7eb;
                padding: 8px 10px;
            }

            .dropdown-multiselect.open .dropdown-content {
                display: flex;
                flex-direction: column;
                gap: 3px;
                max-height: 150px;
                overflow-y: scroll;
            }

            .dropdown-multiselect.open .dropdown-content label,
            .dropdown-multiselect.open .dropdown-content input {
                cursor: pointer;
            }


            .dropdown-divider {
                height: 1px;
                background: #e5e7eb;
                margin: 10px 0;
            }

            .dropdown-tools {
                display: flex;
                align-items: center;
                gap: 8px;
                /* padding: 8px 0px; */
                border-top: 1px dashed #e5e7eb;
                border-bottom: 1px dashed #e5e7eb;
                background: #fafafa;
            }

            .dropdown-tools .filter-search {
                flex: 1;
                border: 1px solid #e5e7eb;
                border-radius: 6px;
                padding: 6px 8px;
                font-size: 13px;
            }

            .dropdown-tools .tools-actions {
                display: flex;
                gap: 6px;
            }

            .dropdown-tools .tool-btn {
                border: 1px solid #e5e7eb;
                background: #fff;
                color: #374151;
                border-radius: 6px;
                padding: 6px 8px;
                font-size: 12px;
            }

            .dropdown-tools .tool-btn:hover {
                background: #f3f4f6;
            }
        </style>
        <style>
            .dashboard-list {
                background: white;
                border-radius: 10px;
                padding: 30px;
                border: 2px solid #C2D9EB;
                margin-top: 40px;
                margin-bottom: 40px;
                margin-left: 60px;
                margin-right: 60px;

            }

            .dashboard-list {
                margin-left: 20px;
                margin-right: 20px;
                padding: 20px;
            }

            .status-icon {
                width: 20px;
                height: 20px;
                display: block;
                /* block จะทำให้จัดตรงกลางได้ง่าย */
            }

            .tip {
                position: relative;
                display: inline-block;
                cursor: pointer;
            }

            .tip[data-tip]:hover::after {
                content: attr(data-tip);
                position: absolute;
                bottom: 125%;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(0, 0, 0, .75);
                color: #fff;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 12px;
                white-space: nowrap;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script>
            // Helpers for dropdown widgets (like in assigned dashboard)
            function toggleDropdown(id) {
                const el = document.getElementById(id);
                if (!el) return;
                el.classList.toggle('open');
            }

            function setupDropdownLabel(dropdownId, labelId, defaultText) {
                const root = document.getElementById(dropdownId);
                const label = document.getElementById(labelId);
                if (!root || !label) return;
                root.addEventListener('change', () => {
                    const checked = root.querySelectorAll('input.filter-option:checked');
                    if (checked.length === 0) {
                        label.textContent = defaultText;
                    } else {
                        label.textContent = `${defaultText} (${checked.length})`;
                    }
                });
            }
        </script>
        <script>
            // ไปหน้ารายละเอียดเมื่อคลิกแถว (ยกเว้นคลิก element ที่ควรคลิกเองอยู่แล้ว)
            $(document).on('click', '#myTable tbody tr[data-href]', function(e) {
                if ($(e.target).closest('a, button, input, select, textarea, label, [data-rowlink-ignore]').length) {
                    return; // อย่าพาไป link ถ้าคลิกสิ่งที่ interactive อยู่แล้ว
                }
                const url = this.dataset.href;
                if (url) window.location.href = url;
            });

            // รองรับ Enter/Space เพื่อเข้าหน้าใหม่ (accessibility)
            $(document).on('keydown', '#myTable tbody tr[data-href]', function(e) {
                const isEnter = e.key === 'Enter' || e.keyCode === 13;
                const isSpace = e.key === ' ' || e.keyCode === 32;
                if (isEnter || isSpace) {
                    e.preventDefault();
                    const url = this.dataset.href;
                    if (url) window.location.href = url;
                }
            });
        </script>

        <script>
            $(document).ready(function() {
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

                $('#add_indicator_button').on('click', function() {
                    window.location.href = "{{ route('indicator.create') }}";
                });

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
                });

                // Close dropdowns when clicking outside
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('#sort-dropdown-container, #filter-dropdown-container').length) {
                        $('#sort-dropdown, #filter-dropdown').addClass('hidden');
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

                // Handle filter checkboxes
                $('.filter-option').on('change', function() {
                    const column = $(this).data('column');
                    const value = $(this).data('value');

                    // Initialize filter array for this column if needed
                    if (!activeFilters[column]) {
                        activeFilters[column] = [];
                    }

                    // Add or remove value from filter
                    if ($(this).is(':checked')) {
                        activeFilters[column].push(value);
                    } else {
                        activeFilters[column] = activeFilters[column].filter(v => v !== value);
                        if (activeFilters[column].length === 0) {
                            delete activeFilters[column];
                        }
                    }
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

                            // Other columns: exact matching (0: year, 1: category, 2: standard, 5: type)
                            const regex = makeExactRegex(activeFilters[column].map(String));
                            table.column(colIdx).search(regex, true, false);
                        }
                    }

                    $('#filter-button span').text(filterCount > 0 ? `กรองข้อมูล (${filterCount})` :
                        'กรองข้อมูล');

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


            });
        </script>
        <script>
            // Initialize dropdown label counters on load
            document.addEventListener('DOMContentLoaded', function() {
                setupDropdownLabel('yearDropdown', 'year-label', 'เลือกปี');
                setupDropdownLabel('standardDropdown', 'standard-label', 'เลือกมาตรฐาน');
                setupDropdownLabel('dimensionDropdown', 'dimension-label', 'เลือกด้าน');
                setupDropdownLabel('deptDropdown', 'dept-label', 'เลือกหน่วยงาน');
                setupDropdownLabel('typeDropdown', 'type-label', 'เลือกประเภท');
                setupDropdownLabel('statusDropdown', 'status-label', 'เลือกสถานะ');

                // Keyboard accessibility for dropdown buttons
                document.querySelectorAll('.dropdown-multiselect .dropdown-btn').forEach(btn => {
                    btn.setAttribute('role', 'button');
                    btn.setAttribute('tabindex', '0');
                    btn.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            const id = btn.parentElement.id;
                            toggleDropdown(id);
                        }
                    });
                });

                // Per-section search: live filter labels in the section
                $(document).on('input', '.dropdown-tools .filter-search', function() {
                    const tools = $(this).closest('.dropdown-tools');
                    const sectionId = tools.data('section');
                    const q = $(this).val().toString().toLowerCase();
                    const content = $('#' + sectionId + ' .dropdown-content');
                    content.find('label').each(function() {
                        const text = $(this).text().toLowerCase();
                        $(this).toggle(text.indexOf(q) !== -1);
                    });
                });

                // Select all / Clear all in a section
                $(document).on('click', '.dropdown-tools [data-action] ', function() {
                    const action = $(this).data('action');
                    const tools = $(this).closest('.dropdown-tools');
                    const sectionId = tools.data('section');
                    const content = $('#' + sectionId + ' .dropdown-content');
                    const checkboxes = content.find('input.filter-option');

                    if (action === 'select-all') {
                        checkboxes.each(function() {
                            if (!$(this).is(':checked')) {
                                $(this).prop('checked', true).trigger('change');
                            }
                        });
                    } else if (action === 'clear-all') {
                        checkboxes.each(function() {
                            if ($(this).is(':checked')) {
                                $(this).prop('checked', false).trigger('change');
                            }
                        });
                    }

                    // Update label counters after bulk action
                    const btn = document.querySelector('#' + sectionId + ' .dropdown-btn span');
                    if (btn) {
                        const defaultText = btn.id === 'year-label' ? 'เลือกปี' :
                            btn.id === 'standard-label' ? 'เลือกมาตรฐาน' :
                            btn.id === 'dimension-label' ? 'เลือกด้าน' :
                            btn.id === 'dept-label' ? 'เลือกหน่วยงาน' :
                            btn.id === 'type-label' ? 'เลือกประเภท' :
                            btn.id === 'status-label' ? 'เลือกสถานะ' : '';
                        const count = content.find('input.filter-option:checked').length;
                        btn.textContent = count > 0 ? `${defaultText} (${count})` : defaultText;
                    }
                });
            });
        </script>
    @endpush
