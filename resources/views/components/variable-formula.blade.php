@props([
    // Form name prefix for submitted fields:
    // <prefix>[variables]..., <prefix>[formula]
    'prefix' => 'scoring',

    // Optional initial data (for edit forms)
    // 'initial' => ['variables' => [...], 'formula' => '...']
    'initial' => [],
])

@php
    $initialVars = old($prefix . '.variables', $initial['variables'] ?? []);
    $initialFormula = old($prefix . '.formula', $initial['formula'] ?? '');
@endphp

<div x-data="{
    prefix: '{{ $prefix }}',

    // state
    vars: @js(array_values($initialVars)),
    formula: @js($initialFormula),

    // add-new controls
    newName: '',
    newType: 'defined', // defined | input | output
    newValue: '',

    add() {
        const name = (this.newName || '').trim();
        if (!name) return;
        const v = {
            id: Date.now() + Math.random(),
            name,
            type: this.newType,
            value: this.newType === 'defined' ? (this.newValue ?? '') : ''
        };
        this.vars = [...this.vars, v];
        this.newName = '';
        this.newType = 'defined';
        this.newValue = '';
        this.$nextTick(() => this.$refs.newName?.focus());
    },

    remove(i) {
        const a = [...this.vars];
        a.splice(i, 1);
        this.vars = a;
    },

    insert(text) {
        const el = this.$refs.formula;
        const start = el.selectionStart ?? el.value.length;
        const end = el.selectionEnd ?? el.value.length;
        const before = el.value.slice(0, start);
        const after = el.value.slice(end);
        this.formula = before + text + after;
        this.$nextTick(() => {
            el.focus();
            const pos = start + text.length;
            el.setSelectionRange(pos, pos);
        });
    }
}" class="space-y-5">
    {{-- Header row --}}
    <div class="hidden md:grid grid-cols-[1fr,260px,auto] gap-3 text-slate-600 text-sm">
        <div>กำหนดตัวแปร</div>
        <div>ประเภทตัวแปร</div>
        <div></div>
    </div>

    {{-- Add variable --}}
    <div class="grid grid-cols-1 md:grid-cols-[1fr,260px,auto] gap-3 items-center">
        <input x-ref="newName" x-model="newName" type="text" placeholder="กรุณาตั้งชื่อตัวแปร"
            class="rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500" />

        <div class="flex gap-3 items-center">
            <select x-model="newType"
                class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                <option value="defined">Static</option>
                <option value="output">Output</option>
                <option value="input">Input</option>
            </select>

            <template x-if="newType === 'defined'">
                <input x-model="newValue" type="number" placeholder="ค่า"
                    class="w-24 rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </template>
        </div>

        <button type="button" @click="add()"
            class="justify-self-start md:justify-self-end inline-flex items-center gap-2 rounded-xl border border-blue-500 text-blue-600 px-4 py-2 hover:bg-blue-50">
            เพิ่มตัวแปร <span class="text-xl leading-none">＋</span>
        </button>
    </div>

    {{-- Variable list --}}
    <template x-for="(v, i) in vars" :key="v.id">
        <div class="flex items-center gap-3 bg-white rounded-2xl border border-slate-200 px-4 py-3">
            <input x-model="v.name"
                class="min-w-0 flex-1 bg-transparent border-0 focus:ring-0 text-blue-700 font-medium" />

            <span class="w-24 text-sm text-slate-600 text-right md:text-left"
                x-text="v.type === 'defined' ? 'Static' : (v.type === 'input' ? 'Input' : 'Output')"></span>

            <template x-if="v.type === 'defined'">
                <input x-model.number="v.value" type="number"
                    class="w-24 rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500" />
            </template>
            <template x-if="v.type === 'input'">
                <span class="text-slate-500 text-sm">ค่าที่ผู้ใช้ต้องกรอก</span>
            </template>
            <template x-if="v.type === 'output'">
                <span class="text-slate-500 text-sm">ค่าผลลัพธ์จากสูตร</span>
            </template>

            <button type="button" @click="remove(i)" class="ml-2 text-slate-500 hover:text-red-600">✕</button>

            {{-- Hidden fields to submit --}}
            <input type="hidden" :name="`${prefix}[variables][${i}][name]`" :value="v.name">
            <input type="hidden" :name="`${prefix}[variables][${i}][type]`" :value="v.type">
            <input type="hidden" :name="`${prefix}[variables][${i}][value]`" :value="v.value ?? ''">
        </div>
    </template>

    {{-- Formula builder --}}
    <div class="space-y-3">
        <label class="block text-slate-800 font-medium">สร้างการคำนวณ</label>
        <textarea x-ref="formula" x-model="formula" rows="5"
            class="w-full rounded-2xl border border-blue-200 focus:border-blue-500 focus:ring-blue-500 p-3"></textarea>

        {{-- Operators --}}
        <div class="space-y-2">
            <div class="text-slate-700 font-medium">เครื่องหมาย</div>
            <div class="flex flex-wrap gap-2">
                <template x-for="op in ['+','-','*','/','<','>','<=','>=','==','!=','IF(']">
                    <button type="button" @click="insert(op)"
                        class="px-3 py-1 rounded-xl bg-blue-50 text-slate-800 hover:bg-blue-100 text-sm">
                        <span x-text="op"></span>
                    </button>
                </template>
            </div>

            <div class="text-slate-700 font-medium mt-2">คำสั่ง</div>
            <div class="flex flex-wrap gap-2">
                <template x-for="op in ['AND','OR','NOR','XOR','XNOR','NAND','NOT']">
                    <button type="button" @click="insert(op)"
                        class="px-3 py-1 rounded-xl bg-blue-50 text-slate-800 hover:bg-blue-100 text-sm">
                        <span x-text="op"></span>
                    </button>
                </template>
            </div>

            <div class="text-slate-700 font-medium mt-2">ตัวแปร</div>
            <div class="flex flex-wrap gap-2">
                <template x-for="(v, i) in vars" :key="'chip' + v.id">
                    <button type="button" @click="insert(v.name)"
                        class="px-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm">
                        <span x-text="v.name"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Hidden final formula --}}
        <input type="hidden" :name="`${prefix}[formula]`" :value="formula">
    </div>
</div>
