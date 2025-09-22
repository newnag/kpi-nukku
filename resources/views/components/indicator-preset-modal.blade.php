@props(['indicators', 'standards' => [], 'modalId' => 'preset-modal'])

<div id="{{ $modalId }}" class="fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg   w-full max-w-4xl p-6 relative mb-3">

        <!-- ปุ่มปิด -->
        <button type="button"
            onclick="document.getElementById('{{ $modalId }}').classList.add('hidden')"
            class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
            ✕
        </button>

        <h2 class="text-lg font-bold mb-4">จัดการ Preset ตัวชี้วัด</h2>

        <!-- ✅ Filter มาตรฐาน -->
        @php
            // Build standards from indicators when not passed in
            if (empty($standards)) {
                $standards = collect($indicators)
                    ->map(function ($i) {
                        return data_get($i, 'category.standard') ?? data_get($i, 'standard');
                    })
                    ->filter()
                    ->unique('id')
                    ->values();
            }

            // Build year options from indicators
            $years = collect($indicators)
                ->map(fn($i) => (int) data_get($i, 'year'))
                ->filter()
                ->unique()
                ->sortDesc()
                ->values();
        @endphp

     <div x-data="{ showFilters: false }" class="mb-4">
    <!-- ✅ Toggle Switch -->
    <div class="flex items-center justify-end mb-3">
        <span class="mr-3 text-sm font-medium text-gray-700">แสดงตัวกรอง</span>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" x-model="showFilters" class="sr-only peer">
            <div
                class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer dark:bg-gray-600
                       peer-checked:bg-green-500 transition-colors duration-300">
            </div>
            <div
                class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full border border-gray-300
                       transition-transform duration-300 peer-checked:translate-x-5">
            </div>
        </label>
    </div>

    <!-- ✅ ฟิลเตอร์ -->
    <div x-show="showFilters" x-transition class="space-y-3 bg-gray-50 p-4 rounded-lg shadow">
        <div>
            <label class="block text-sm font-medium text-gray-700">กรองตามปี</label>
            <select id="year-filter-{{ $modalId }}" class="w-full border rounded px-2 py-1 text-sm focus:ring focus:ring-green-200">
                <option value="">-- แสดงทั้งหมด --</option>
                @foreach ($years as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">กรองตามมาตรฐาน</label>
            <select id="standard-filter-{{ $modalId }}" class="w-full border rounded px-2 py-1 text-sm focus:ring focus:ring-green-200">
                <option value="">-- แสดงทั้งหมด --</option>
                @foreach ($standards as $std)
                    <option value="{{ data_get($std, 'id') }}">{{ data_get($std, 'name') }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>


        <!-- ✅ Search Box -->
        <div class="mb-3">
            <input type="text" id="search-{{ $modalId }}"
                   placeholder="ค้นหาตัวชี้วัด..."
                   class="w-full border rounded px-2 py-1 text-sm">
        </div>

        <!-- ✅ Select All -->
        <div class="flex items-center space-x-2 mb-3">
            <input type="checkbox" id="select-all-{{ $modalId }}" class="rounded border-gray-300">
            <label for="select-all-{{ $modalId }}" class="text-sm font-medium">เลือกทั้งหมด</label>
        </div>

        <!-- ✅ Check list Indicators -->
      <!-- ✅ Export + Import เป็น 2 คอลัมน์ -->
<div class="flex flex-col md:flex-row gap-4">
    <!-- Export Preset -->
    <form id="preset-export-form" method="GET" action="{{ route('indicator.export.bulk') }}"
          class="flex-1 flex flex-col border rounded-lg p-4">
        <div class="max-h-64 overflow-y-auto border rounded p-2 mb-4"
             id="indicator-list-{{ $modalId }}">
            @foreach ($indicators as $ind)
                <label class="flex items-center space-x-2 py-1 indicator-item"
                       data-standard="{{ data_get($ind, 'category.standard.id') ?? data_get($ind, 'standard.id') }}"
                       data-year="{{ data_get($ind, 'year') }}">
                    <input type="checkbox" name="ids[]" value="{{ data_get($ind, 'id') }}"
                           class="rounded border-gray-300">
                    <span>{{ data_get($ind, 'code') }} - {{ data_get($ind, 'name') }}</span>
                </label>
            @endforeach
        </div>
        <input type="hidden" name="year" id="hidden-year-{{ $modalId }}" value="">
        <button type="submit"
            class="mt-auto block w-full text-center bg-blue-500 text-white py-2 rounded hover:bg-blue-600">
            Export Preset
        </button>
    </form>

    <!-- Import Preset -->
    <form action="{{ route('indicator.import') }}" method="POST" enctype="multipart/form-data"
          class="flex-1 flex flex-col border rounded-lg p-4 space-y-3">
        @csrf
        <!-- เลือกไฟล์ -->
        <input type="file" name="preset_file" accept=".json" required
            class="block w-full border rounded px-2 py-1 text-sm">

        <!-- เลือกปี -->
        <label class="block text-sm font-medium text-gray-700">ปีที่ต้องการนำเข้า</label>
        <input type="number" name="year" value="{{ now()->year }}" min="2000" max="2100"
            class="block w-full border rounded px-2 py-1 text-sm" required>

        <button type="submit"
            class="mt-auto w-full bg-green-500 text-white py-2 rounded hover:bg-green-600">
            Import Preset
        </button>
    </form>
</div>

    </div>
</div>

<!-- ✅ Script -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const modalId = @json($modalId);
    const selectAll = document.getElementById(`select-all-${modalId}`);
    const indicatorList = document.getElementById(`indicator-list-${modalId}`);
    const standardFilter = document.getElementById(`standard-filter-${modalId}`);
    const yearFilter = document.getElementById(`year-filter-${modalId}`);
    const searchBox = document.getElementById(`search-${modalId}`);
    const hiddenYear = document.getElementById(`hidden-year-${modalId}`);

    // ฟังก์ชันรีเฟรช filter
    function applyFilter() {
        const selectedStd = standardFilter?.value || "";
        const selectedYear = yearFilter?.value || "";
        const searchTerm = searchBox?.value.toLowerCase() || "";

        indicatorList.querySelectorAll(".indicator-item").forEach(item => {
            const matchesStandard = !selectedStd || item.dataset.standard === selectedStd;
            const matchesYear = !selectedYear || item.dataset.year === selectedYear;
            const text = item.innerText.toLowerCase();
            const matchesSearch = !searchTerm || text.includes(searchTerm);

            if (matchesStandard && matchesYear && matchesSearch) {
                item.classList.remove("hidden");
            } else {
                item.classList.add("hidden");
            }
        });
    }

    // ✅ เลือกทั้งหมด (เลือกเฉพาะที่มองเห็น)
    selectAll?.addEventListener("change", function () {
        indicatorList.querySelectorAll(".indicator-item:not(.hidden) input[type=checkbox]").forEach(cb => {
            cb.checked = selectAll.checked;
        });
    });

    // ✅ กรองตามมาตรฐาน
    standardFilter?.addEventListener("change", applyFilter);
    yearFilter?.addEventListener("change", function(){
        if (hiddenYear) hiddenYear.value = yearFilter.value || "";
        applyFilter();
    });

    // ✅ ค้นหา
    searchBox?.addEventListener("input", applyFilter);
});
</script>
