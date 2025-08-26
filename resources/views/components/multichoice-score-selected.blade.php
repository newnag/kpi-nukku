@props([
    'index' => 1,
    'namePrefix' => null,
    'showControls' => true,
    'options' => [], // ชื่อเกณฑ์ fallback จาก server
])

@php
    $prefix = $namePrefix ?? 'multiScores[' . $index . ']';
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white shadow-sm p-5 md:p-6']) }}
    x-data="{
        labels: @js(array_values($options)),
        get allList() { return Array.from({ length: this.labels.length }, (_, i) => i + 1) },
        selected: [],
    }" x-init="labels = (window.__criteriaTitles && window.__criteriaTitles.length) ?
        window.__criteriaTitles.slice() :
        labels"
    @criteria-updated.window="
        labels = ($event.detail?.titles ?? []);
        selected = selected.filter(v => v <= labels.length);
     ">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-slate-900">
            เกณฑ์ที่ <span x-text="order ?? {{ $index }}">{{ $index }}</span>
        </h3>

        @if ($showControls)
            <div class="flex items-center gap-3">
                <button type="button" class="p-1 text-red-500 hover:text-red-600"
                    @click="$dispatch('criteria-remove',    { index: order ?? {{ $index }} })"
                    title="ลบรายการนี้">✖</button>
            </div>
        @endif
    </div>

    <!-- ส่งค่า order + mode -->
    <input type="hidden" :name="(prefix || '{{ $prefix }}') + '[order]'" :value="order ?? {{ $index }}">
    <input type="hidden" :name="(prefix || '{{ $prefix }}') + '[mode]'" value="selected">

    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div class="text-slate-900 font-semibold border-l-4 border-blue-500 pl-3">รายการเกณฑ์</div>
            <div class="flex items-center gap-2">
                <button type="button"
                    class="px-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs"
                    @click="selected = allList.slice()">เลือกทั้งหมด</button>
            </div>
        </div>

        <!-- Checkboxes -->
        <div class="space-y-2">
            <template x-for="(label, i) in labels" :key="i">
                <label class="flex items-start gap-3 rounded-2xl border border-slate-200 px-4 py-3 bg-white">
                    <input type="checkbox" :name="(prefix || '{{ $prefix }}') + '[criteria][]'"
                        :value="i + 1" x-model="selected"
                        class="mt-1 h-4 w-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                    <span class="text-slate-800" x-text="label"></span>
                </label>
            </template>

            <template x-if="!labels.length">
                <div class="text-slate-500 text-sm">ยังไม่มีรายการเกณฑ์ — เพิ่มที่ “รายการเกณฑ์การพิจารณา”</div>
            </template>
        </div>

        <hr class="my-2 border-slate-200">

        <!-- คะแนนรวมของชุดที่เลือก -->
        <div class="space-y-2">
            <label class="block text-sm font-medium text-slate-700">
                คะแนน <span class="text-red-500">*</span>
            </label>
            <input type="number" step="0.01" required :name="(prefix || '{{ $prefix }}') + '[score]'"
                placeholder="กรุณากรอกคะแนนเป็นตัวเลข เช่น 0"
                class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>

    {{ $slot }}
</div>
