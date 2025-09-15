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
                                data-column="1" data-order="asc" role="menuitem">ชื่อตัวบ่งชี้ (A-Z)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="1" data-order="desc" role="menuitem">ชื่อตัวบ่งชี้ (Z-A)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="5" data-order="asc" role="menuitem">ผลลัพธ์ (น้อยไปมาก)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="5" data-order="desc" role="menuitem">ผลลัพธ์ (มากไปน้อย)</button>
                            <button id="clear-sort"
                                class=" text-left block w-full px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">ล้างตัวเรียงลำดับ</button>
                        </div>
                    </div>
                </div>

                <!-- Filter Button with Dropdown -->
                {{-- <div class="relative inline-block text-left" id="filter-dropdown-container">
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
                            <h3 class="text-sm font-medium text-gray-900 mb-2">สถานะตัวชี้วัด</h3>
                            <div class="space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="filter-option rounded text-blue-600" data-column="7"
                                        data-value="3">
                                    <span class="ml-2 text-sm text-gray-700">สมบูรณ์</span>
                                </label>
                                <br>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="filter-option rounded text-blue-600" data-column="7"
                                        data-value="4">
                                    <span class="ml-2 text-sm text-gray-700">ไม่สมบูรณ์</span>
                                </label>
                                <br>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="filter-option rounded text-blue-600" data-column="7"
                                        data-value="0">
                                    <span class="ml-2 text-sm text-gray-700">อยู่ระหว่างดำเนินการ</span>
                                </label>
                                <br>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="filter-option rounded text-blue-600" data-column="7"
                                        data-value="1">
                                    <span class="ml-2 text-sm text-gray-700">บันทึกฉบับร่าง</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="filter-option rounded text-blue-600" data-column="7"
                                        data-value="2">
                                    <span class="ml-2 text-sm text-gray-700">บันทึกฉบับจริง</span>
                                </label>
                            </div>

                            <div class="border-t border-gray-200 my-3"></div>

                            <h3 class="text-sm font-medium text-gray-900 mb-2">สถานะเอกสาร</h3>
                            <div class="space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="filter-option rounded text-blue-600" data-column="8"
                                        data-value="รอดำเนินการ">
                                    <span class="ml-2 text-sm text-gray-700">รอดำเนินการ</span>
                                </label>
                                <br>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="filter-option rounded text-blue-600" data-column="8"
                                        data-value="ไม่ครบถ้วน">
                                    <span class="ml-2 text-sm text-gray-700">ไม่ครบถ้วน</span>
                                </label>
                                <br>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="filter-option rounded text-blue-600" data-column="8"
                                        data-value="ครบถ้วน">
                                    <span class="ml-2 text-sm text-gray-700">ครบถ้วน</span>
                                </label>
                            </div>

                            <div class="border-t border-gray-200 my-3"></div>

                            <h3 class="text-sm font-medium text-gray-900 mb-2">ประเภทตัวชี้วัด</h3>
                            <div class="space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="filter-option rounded text-blue-600" data-column="3"
                                        data-value="คุณภาพ">
                                    <span class="ml-2 text-sm text-gray-700">คุณภาพ</span>
                                </label>
                                <br>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="filter-option rounded text-blue-600" data-column="3"
                                        data-value="ปริมาณ">
                                    <span class="ml-2 text-sm text-gray-700">ปริมาณ</span>
                                </label>
                                <br>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="filter-option rounded text-blue-600" data-column="3"
                                        data-value="คุณภาพ/ปริมาณ">
                                    <span class="ml-2 text-sm text-gray-700">คุณภาพ/ปริมาณ</span>
                                </label>
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
                </div> --}}
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
        <div class="border border-gray-200 rounded-lg shadow-sm overflow-x-auto">
            <table id="myTable" class="w-full">
                <thead>
                    <tr>
                        <th class="w-fit  text-sm font-medium text-gray-900 cursor-pointer select-none"
                            title="ปีของตัวชี้วัด">
                            <div class="flex items-center justify-center truncate w-6">
                                ปี
                            </div>
                        </th>
                        <th class="w-fit  text-sm font-medium text-gray-900 cursor-pointer select-none"
                            title="มาตรฐานตัวชี้วัด 3 หมวด">
                            <div class="flex items-center justify-center truncate w-15">
                                มาตรฐาน
                            </div>
                        </th>
                        <th class="w-fit  text-sm font-medium text-gray-900 cursor-pointer select-none"
                            title="ด้านตัวชี้วัดใน 3 หมวด">
                            <div class="flex items-center justify-center truncate w-15">
                                ด้าน
                            </div>
                        </th>
                        <th class="w-fit  text-sm font-medium text-gray-900 text-left cursor-pointer select-none"
                            title="ชื่อตัวบ่งชี้">
                            <div class="flex items-center justify-center w-60 truncate">
                                ชื่อตัวบ่งชี้
                            </div>
                        </th>
                        <th class="w-fit  text-sm font-medium text-gray-900 cursor-pointer select-none"
                            title="รหัสตัวบ่งชี้">
                            <div class="flex items-center justify-center w-9 truncate">
                                รหัส
                            </div>
                        </th>
                        <th class="w-fit  text-sm font-medium text-gray-900 cursor-pointer select-none hidden md:table-cell"
                            title="ประเภทตัวบ่งชี้ (คุณภาพ, ปริมาณ, คุณภาพ/ปริมาณ)">
                            <div class="flex items-center justify-center w-11 truncate">
                                ประเภท
                            </div>
                        </th>
                        <th class="w-fit text-sm font-medium text-gray-900 cursor-pointer select-none hidden md:table-cell"
                            title="หน่วยงานที่รับผิดชอบในตัวบ่งชี้">
                            <div class="flex items-center justify-center w-[185px]">
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
                        <th class="w-fit  text-sm font-medium text-gray-900 cursor-pointer select-none"
                            title="สถานะของตัวบ่งชี้">
                            <div class="flex items-center justify-center">
                                สถานะ
                            </div>
                        </th>
                        <th class="w-fit  text-sm font-medium text-gray-900 cursor-pointer select-none hidden sm:table-cell"
                            title="สถานะเอกสาร">
                            <div class="flex items-center justify-center">
                                เอกสาร
                            </div>
                        </th>
                        {{-- <th class="w-fit  text-sm font-medium text-gray-900">
                            <div class="flex items-center justify-center">
                                จัดการ
                            </div>
                        </th> --}}
                    </tr>
                </thead>
                <tbody class="bg-white ">
                    @forelse($indicators as $indicator)
                        <tr class="hover:bg-gray-50 active:bg-gray-100 divide-y divide-gray-200 cursor-pointer"
                            data-href="{{ route('indicator.show', $indicator['id']) }}" tabindex="0" role="link"
                            aria-label="เปิด {{ $indicator['name'] }}" title="คลิกเพื่อดูรายละเอียด">
                            <td class="max-w-6 text-sm text-gray-700 text-center align-top">{{ $indicator['year'] }}</td>
                            <td class="max-w-15 text-sm text-balance text-gray-700 align-top">
                                {{ $indicator['category']['name'] }}</td>
                            <td class="max-w-15 text-sm text-balance text-gray-700 align-top">
                                {{ $indicator['standard']['name'] }}</td>
                            <td class="max-w-60 text-sm text-gray-700 text-balance align-top">
                                {{-- <span class="block truncate" title="{{ $indicator['name'] }}"> --}}
                                {{ $indicator['name'] }}
                                {{-- </span> --}}
                            </td>
                            <td class="max-w-9 text-center text-sm text-gray-700 truncate  align-top">
                                {{ $indicator['code'] }}</td>
                            <td class="max-w-11 text-sm text-gray-700 text-center align-top">
                                {{ $indicator['type'] ?? 'ไม่ระบุ' }}
                            </td>
                            <td class="text-sm text-gray-700 align-top" data-rowlink-ignore>
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
                                    <div x-data="{ open: false }" class="flex flex-wrap gap-1 max-w-full">
                                        @foreach ($departments as $name)
                                            <span x-show="@json($loop->iteration <= 3) || open" x-cloak
                                                class="inline-flex items-center rounded-full bg-slate-50 text-slate-700 ring-1 ring-inset ring-slate-200 px-2 py-0.5 text-xs md:text-xs max-w-[200px] truncate"
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
                            {{-- <td class=" text-sm">
                                <a href="{{ route('indicator.show', $indicator['id']) }}"
                                    class="inline-flex items-center justify-center px-3 py-1 bg-white border border-blue-500 text-blue-500 rounded-full text-xs font-medium hover:bg-blue-500 hover:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    MORE
                                </a>
                            </td> --}}
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
            .dataTables_wrapper {
                background-color: ;
            }

            /* Length and Filter Controls */
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {}

            /* Dropdown Select Styling */
            .dataTables_wrapper .dataTables_length select {
                border: 1px solid #a0aec0;
                border-radius: 0.5rem;
                padding: 0.5rem;
                background-color: white;
            }

            .dataTables_wrapper .dataTables_info {
                /* padding-top: 1rem; */
                color: #4b5563;
            }

            .dataTables_wrapper .dataTables_paginate {
                /* padding-top: 1rem; */
            }

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
                color: #1f2937 !important;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                background-color: #2563eb !important;
                border-color: #2563eb;
                color: white !important;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
                background-color: #1d4ed8 !important;
                border-color: #1d4ed8;
            }

            table.dataTable thead th,
            table.dataTable tbody td {
                padding: 10px 4px;

            }

            @media (max-width: 768px) {

                .dataTables_wrapper .dataTables_length,
                .dataTables_wrapper .dataTables_filter,
                .dataTables_wrapper .dataTables_info,
                .dataTables_wrapper .dataTables_paginate {
                    text-align: left;
                    float: none;
                    width: 100%;
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
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
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

                            if (column == 7) {
                                // activeFilters['7'] already contains ["0","1","2"] from checkboxes
                                const regex = makeExactRegex(activeFilters[column].map(String));
                                table.column(7).search(regex, true, false); // regex = true, smart = false
                                continue;
                            }

                            if (column == 8) {
                                // you kept text values ("รอดำเนินการ","ไม่ครบ") for column 8
                                const regex = makeExactRegex(activeFilters[column].map(String));
                                table.column(8).search(regex, true, false);
                                continue;
                            }

                            // (future other columns)
                            const regex = makeExactRegex(activeFilters[column].map(String));
                            table.column(Number(column)).search(regex, true, false);
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
