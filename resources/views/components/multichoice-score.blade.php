@props([
    'index' => 1,
    'namePrefix' => null,
    'showControls' => true,
    // optional initial option labels from server (fallback)
    'options' => [],
])

@php
    $prefix = $namePrefix ?? 'multiScores[' . $index . ']';
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white shadow-sm p-5 md:p-6']) }}
    x-data="{
        // MODE
        mode: 'selected', // 'selected' | 'count'
        // SHARED
        labels: @js(array_values($options)),
        get allList() { return Array.from({ length: this.labels.length }, (_, i) => i + 1) },
    
        // SELECTED mode state
        selected: [],
    
        // COUNT mode state
        countRules: [{ id: Date.now(), count: 1, score: 0 }],
        addRule() {
            const nextCount = (this.countRules.at(-1)?.count ?? 0) + 1;
            this.countRules = [...this.countRules, { id: Date.now() + Math.random(), count: nextCount, score: 0 }];
        },
        removeRule(i) {
            const a = [...this.countRules];
            a.splice(i, 1);
            this.countRules = a.length ? a : [{ id: Date.now() + Math.random(), count: 1, score: 0 }];
        },
    }" x-init="// seed on mount from global cache
    labels = (window.__criteriaTitles && window.__criteriaTitles.length) ?
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
                <button type="button" class="p-1 text-slate-500 hover:text-slate-700"
                    @click="$dispatch('criteria-move-up',   { index: order ?? {{ $index }} })"
                    title="ย้ายขึ้น">▲</button>
                <button type="button" class="p-1 text-slate-500 hover:text-slate-700"
                    @click="$dispatch('criteria-move-down', { index: order ?? {{ $index }} })"
                    title="ย้ายลง">▼</button>
                <button type="button" class="p-1 text-red-500 hover:text-red-600"
                    @click="$dispatch('criteria-remove',    { index: order ?? {{ $index }} })"
                    title="ลบรายการนี้">✖</button>
            </div>
        @endif
    </div>

    <!-- keep order + mode -->
    <input type="hidden" :name="(prefix || '{{ $prefix }}') + '[order]'" :value="order ?? {{ $index }}">
    <input type="hidden" :name="(prefix || '{{ $prefix }}') + '[mode]'" :value="mode">

    <!-- Mode toggles -->
    <div class="flex gap-2 pt-2 mb-4">
        <button type="button" @click="mode = 'selected'"
            :class="mode === 'selected' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'"
            class="px-4 py-2 rounded-xl">
            คะแนนตามข้อที่เลือก
        </button>
        <button type="button" @click="mode = 'count'"
            :class="mode === 'count' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'"
            class="px-4 py-2 rounded-xl">
            คะแนนตามจำนวนข้อที่เลือก
        </button>
    </div>

    <!-- SELECTED MODE -->
    <div x-show="mode==='selected'" class="space-y-4">
        <div class="flex items-center justify-between">
            <div class="text-slate-900 font-semibold border-l-4 border-blue-500 pl-3">รายการเกณฑ์</div>
            <div class="flex items-center gap-2">
                <button type="button"
                    class="px-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs"
                    @click="selected = allList.slice()" :disabled="mode !== 'selected'">เลือกทั้งหมด</button>
                <button type="button"
                    class="px-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs"
                    @click="selected = [1]" x-show="labels.length" :disabled="mode !== 'selected'">เลือกตัวเลือกข้อที่
                    1</button>
            </div>
        </div>

        <!-- Dynamic checkboxes -->
        <div class="space-y-2">
            <template x-for="(label, i) in labels" :key="i">
                <label class="flex items-start gap-3 rounded-2xl border border-slate-200 px-4 py-3 bg-white">
                    <input type="checkbox" :name="(prefix || '{{ $prefix }}') + '[criteria][]'"
                        :value="i + 1" x-model="selected" :disabled="mode !== 'selected'"
                        class="mt-1 h-4 w-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                    <span class="text-slate-800" x-text="label"></span>
                </label>
            </template>

            <template x-if="!labels.length">
                <div class="text-slate-500 text-sm">ยังไม่มีรายการเกณฑ์ — เพิ่มที่ “รายการเกณฑ์การพิจารณา”</div>
            </template>
        </div>

        <hr class="my-2 border-slate-200">

        <!-- Single score for this selection -->
        <div class="space-y-2">
            <label class="block text-sm font-medium text-slate-700">คะแนน <span class="text-red-500">*</span></label>
            <input type="number" step="0.01" required :name="(prefix || '{{ $prefix }}') + '[score]'"
                :disabled="mode !== 'selected'" placeholder="กรุณากรอกคะแนนเป็นตัวเลข เช่น 0"
                class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>

    <!-- COUNT MODE -->
    <div x-show="mode==='count'" class="space-y-3">
        <div class="text-slate-900 font-semibold border-l-4 border-blue-500 pl-3">กำหนดคะแนนตามจำนวนที่เลือก</div>

        <!-- Head row -->
        <div class="grid grid-cols-[140px,1fr,auto] gap-3 items-center text-sm text-slate-600">
        </div>

        <!-- Rule rows -->
        <template x-for="(r, i) in countRules" :key="r.id">
            <div class="grid grid-cols-[140px,1fr,auto] gap-3 items-center">
                <input type="number" min="0" step="1" x-model.number="r.count" :disabled="mode !== 'count'"
                    class="rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                <input type="number" step="0.01" x-model.number="r.score" :disabled="mode !== 'count'"
                    class="rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                <button type="button" @click="removeRule(i)" :disabled="mode !== 'count'"
                    class="text-red-600 hover:underline px-3 py-1">ลบ</button>

                <!-- Hidden inputs for submission -->
                <input type="hidden" :name="(prefix || '{{ $prefix }}') + `[countRules][${i}][count]`"
                    :value="r.count" :disabled="mode !== 'count'">
                <input type="hidden" :name="(prefix || '{{ $prefix }}') + `[countRules][${i}][score]`"
                    :value="r.score" :disabled="mode !== 'count'">
            </div>
        </template>

        <div>
            <button type="button" @click="addRule()" :disabled="mode !== 'count'"
                class="inline-flex items-center gap-2 rounded-xl border border-blue-500 text-blue-600 px-4 py-2 hover:bg-blue-50">
                เพิ่มเงื่อนไข
                <span class="text-xl leading-none">＋</span>
            </button>
        </div>
    </div>

    {{ $slot }}
</div>
