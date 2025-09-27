@props([
    'years' => [],
    'context' => 'year-export',
    'title' => 'เลือกปีที่ต้องการ Export',
    // Absolute URL to navigate on confirm; defaults to route('sar_reports.create')
    'url' => null,
])

@php
    $targetUrl = $url ?? route('sar_reports.create');
    $yearsList = collect($years)->filter()->unique()->sortDesc()->values();
@endphp

<x-modal :title="$title" size="sm" :context="$context">
    <div class="space-y-4">
        <div>
            <label for="yearSelectGlobal" class="block text-sm font-medium text-gray-700 mb-1">ปีที่ต้องการ Export</label>
            <select id="yearSelectGlobal" x-ref="year"
                class="w-full border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-green-200">
                <option value="">-- กรุณาเลือกปี --</option>
                @foreach ($yearsList as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <x-slot:footer>
        <div class="flex justify-end gap-2">
            <button type="button" class="px-3 py-1 bg-gray-300 rounded" @click="open = false">ยกเลิก</button>
            <button type="button" class="px-3 py-1 bg-green-500 text-white rounded"
                @click="const y = $refs.year.value; if (!y) { alert('กรุณาเลือกปี'); return; } open = false; window.location.href = '{{ $targetUrl }}' + ({{ Str::contains($targetUrl, '?') ? 'true' : 'false' }} ? '&' : '?') + 'year=' + y;">
                ยืนยัน
            </button>
        </div>
    </x-slot:footer>
</x-modal>

