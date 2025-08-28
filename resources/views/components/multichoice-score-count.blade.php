@props(['index' => 1,'namePrefix' => null,'showControls' => true,'options' => []])

@php $prefix = $namePrefix ?? 'multiScores[' . $index . ']'; @endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white shadow-sm p-4 sm:p-5 md:p-6']) }}
    x-data="{
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
    }">
    <input type="hidden" :name="(prefix || '{{ $prefix }}') + '[order]'" :value="order ?? {{ $index }}">
    <input type="hidden" :name="(prefix || '{{ $prefix }}') + '[mode]'" value="count">

    <div class="space-y-3">
        <div class="text-slate-900 font-semibold border-l-4 border-blue-500 pl-3">กำหนดคะแนนตามจำนวนที่เลือก</div>

        <template x-for="(r, i) in countRules" :key="r.id">
            <div class="space-y-2 sm:space-y-0 sm:grid sm:grid-cols-3 sm:items-center gap-3">
                <input type="number" min="0" step="1" x-model.number="r.count"
                       class="rounded-xl border bg-white py-1 px-3 border-slate-300 focus:border-blue-500 focus:ring-blue-500 w-full text-sm md:text-base">
                <input type="number" step="1" x-model.number="r.score"
                       class="rounded-xl border bg-white py-1 px-3 border-slate-300 focus:border-blue-500 focus:ring-blue-500 w-full text-sm md:text-base">
                <div class="flex sm:justify-end">
                    <button type="button" @click="removeRule(i)"
                            class="text-red-600 hover:underline px-3 py-2 text-sm md:text-base">ลบ</button>
                </div>

                <input type="hidden" :name="(prefix || '{{ $prefix }}') + `[countRules][${i}][count]`" :value="r.count">
                <input type="hidden" :name="(prefix || '{{ $prefix }}') + `[countRules][${i}][score]`" :value="r.score">
            </div>
        </template>

        <div>
            <button type="button" @click="addRule()"
                class="inline-flex items-center gap-2 rounded-xl border border-blue-500 text-blue-600 px-3 py-2 md:px-4 md:py-2 hover:bg-blue-50 text-sm md:text-base">
                เพิ่มเงื่อนไข <span class="text-xl leading-none">＋</span>
            </button>
        </div>
    </div>

    {{ $slot }}
</div>
