@props(['index' => 1, 'namePrefix' => null, 'showControls' => true])

@php
    // Fallback names when Alpine is missing
    $prefix = $namePrefix ?? 'criteria[' . $index . ']';
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white shadow-sm p-5 md:p-6']) }}>
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-slate-900">
            ลำดับ <span x-text="order ?? {{ $index }}">{{ $index }}</span>
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

    <!-- hidden order -->
    <input type="hidden" :name="(prefix || '{{ $prefix }}') + '[order]'" :value="order ?? {{ $index }}">

    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
                ชื่อเกณฑ์ <span class="text-red-500">*</span>
            </label>
            <input type="text" data-criteria-title :name="(prefix || '{{ $prefix }}') + '[title]'" required
                placeholder="กรุณากรอกชื่อเกณฑ์"
                class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                @input.debounce.200ms="$dispatch('criteria-title-change', { idx: (order ?? {{ $index }}) - 1, title: $event.target.value })">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">หลักฐานอ้างอิง</label>
            <textarea rows="3" :name="(prefix || '{{ $prefix }}') + '[evidence]'"
                placeholder="กรุณากรอกรายละเอียดหลักฐานอ้างอิง เช่น เอกสารแสดงรายชื่ออาจารย์"
                class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 resize-y"></textarea>
        </div>
    </div>

    {{ $slot }}
</div>
