@props(['prefix' => 'scoring', 'initial' => []])

@php
    // Backward-compat for old payloads that used `name` and `formula`
    $initialVars = old($prefix . '.variables', $initial['variables'] ?? []);
    $initialCondition = old(
        $prefix . '.condition',
        old($prefix . '.formula', $initial['condition'] ?? ($initial['formula'] ?? '')),
    );
@endphp

<div x-data="{
    prefix: '{{ $prefix }}',

    // Normalize initial vars to { variable_name, type, value }
    vars: (@js(array_values($initialVars))).map(v => ({
        id: Date.now() + Math.random(),
        variable_name: v.variable_name ?? v.name ?? '',
        type: v.type ?? 'defined',
        value: (v.type ?? 'defined') === 'defined' ? (v.value ?? '') : ''
    })),

    condition: @js($initialCondition),

    // Inputs for adding a new var
    newName: '',
    newType: 'defined',
    newValue: '',

    add() {
        const name = (this.newName || '').trim();
        if (!name) return;
        const v = {
            id: Date.now() + Math.random(),
            variable_name: name,
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
        const el = this.$refs.condition;
        const s = el.selectionStart ?? el.value.length;
        const e = el.selectionEnd ?? el.value.length;
        const before = el.value.slice(0, s),
            after = el.value.slice(e);
        this.condition = before + text + after;
        this.$nextTick(() => {
            el.focus();
            const pos = s + text.length;
            el.setSelectionRange(pos, pos);
        });
    },

    // --- Validation helpers ---
    reserved() {
        return new Set(['IF', 'AND', 'OR', 'NOR', 'XOR', 'XNOR', 'NAND', 'NOT', 'TRUE', 'FALSE', 'ELSE', 'THEN']);
    },
    extractVars(text) {
        // Grab identifiers like var1, result_2; ignore numbers
        const tokens = (text || '').match(/[A-Za-z_][A-Za-z0-9_]*/g) || [];
        const set = new Set();
        const reserved = this.reserved();
        tokens.forEach(t => {
            if (!reserved.has(t) && isNaN(Number(t))) set.add(t);
        });
        return Array.from(set);
    },
    get declaredVars() {
        return this.vars.map(v => (v.variable_name || '').trim()).filter(Boolean);
    },
    get referencedVars() {
        return this.extractVars(this.condition);
    },
    get unknownVars() {
        const declared = new Set(this.declaredVars);
        return this.referencedVars.filter(n => !declared.has(n));
    },
    initSubmitGuard() {
        const form = this.$el.closest('form');
        if (!form) return;
        form.addEventListener('submit', (e) => {
            // Recompute before submit
            if (this.unknownVars.length) {
                e.preventDefault();
                alert('พบตัวแปรที่ยังไม่ได้ประกาศ: ' + this.unknownVars.join(', '));
            }
        });
    }
}" x-init="initSubmitGuard()" class="space-y-5">
    <div class="grid grid-cols-1 md:grid-cols-[1fr,260px,auto] gap-3 items-center">
        <label class="block text-slate-800 font-medium">สร้างตัวแปร</label>

        <input x-ref="newName" x-model="newName" type="text" placeholder="กรุณาตั้งชื่อตัวแปร"
            class="p-2 w-full bg-white rounded-xl border border-slate-300 placeholder-slate-400 text-sm md:text-base hover:border-blue-400 transition" />

        <div class="flex gap-3 items-center">
            <select x-model="newType"
                class="p-2 w-full bg-white rounded-xl border border-slate-300 placeholder-slate-400 text-sm md:text-base hover:border-blue-400 transition">
                <option value="defined">Static</option>
                <option value="output">Output</option>
                <option value="input">Input</option>
            </select>

            <template x-if="newType === 'defined'">
                <input x-model="newValue" type="number" placeholder="ค่า"
                    class="p-2 w-full bg-white rounded-xl border border-slate-300 placeholder-slate-400 text-sm md:text-base hover:border-blue-400 transition">
            </template>
        </div>

        <button type="button" @click="add()"
            class="justify-self-start md:justify-self-end inline-flex items-center gap-2 rounded-xl border border-blue-500 text-blue-600 px-3 py-2 md:px-4 md:py-2 hover:bg-blue-50 text-sm md:text-base">
            เพิ่มตัวแปร <span class="text-xl leading-none">＋</span>
        </button>
    </div>

    <template x-for="(v, i) in vars" :key="v.id">
        <div
            class="flex flex-col md:flex-row md:items-center gap-2 md:gap-3 bg-white rounded-2xl border border-slate-200 px-4 py-3">
            <input x-model="v.variable_name"
                class="min-w-0 flex-1 bg-transparent border-0 focus:ring-0 text-blue-700 font-medium text-sm md:text-base"
                placeholder="variable_name" />

            <span class="md:w-24 text-sm text-slate-600 md:text-left"
                x-text="v.type === 'defined' ? 'Static' : (v.type === 'input' ? 'Input' : 'Output')"></span>

            <template x-if="v.type === 'defined'">
                <input x-model.number="v.value" type="number"
                    class="w-24 rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm md:text-base" />
            </template>
            <template x-if="v.type === 'input'">
                <span class="text-slate-500 text-sm">ค่าที่ผู้ใช้ต้องกรอก</span>
            </template>
            <template x-if="v.type === 'output'">
                <span class="text-slate-500 text-sm">ค่าผลลัพธ์จากสูตร</span>
            </template>

            <button type="button" @click="remove(i)"
                class="md:ml-2 text-slate-500 hover:text-red-600 text-sm md:text-base">✕</button>

            {{-- Hidden fields for POST --}}
            <input type="hidden" :name="`${prefix}[variables][${i}][variable_name]`" :value="v.variable_name">
            <input type="hidden" :name="`${prefix}[variables][${i}][type]`" :value="v.type">
            <input type="hidden" :name="`${prefix}[variables][${i}][value]`" :value="v.value ?? ''">
        </div>
    </template>

    <div class="space-y-3">
        <label class="block text-slate-800 font-medium">สร้างเงื่อนไขการคำนวณ</label>
        <textarea x-ref="condition" x-model="condition" rows="5"
            class="w-full bg-white rounded-2xl border border-blue-200 focus:border-blue-500 focus:ring-blue-500 p-3 text-sm md:text-base"
            placeholder="ตัวอย่าง: result = var1 * var2"></textarea>

        {{-- Validation status --}}
        <template x-if="unknownVars.length">
            <div class="rounded-xl border border-red-200 bg-red-50 text-red-700 p-3 text-sm">
                พบตัวแปรที่ยังไม่ได้ประกาศ: <span x-text="unknownVars.join(', ')"></span>
            </div>
        </template>
        <template x-if="!unknownVars.length && condition && condition.trim().length">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 p-3 text-sm">
                ✓ ตัวแปรทั้งหมดที่ใช้งานในเงื่อนไขถูกประกาศแล้ว
            </div>
        </template>

        <div class="space-y-2">
            <div class="text-slate-700 font-medium">เครื่องหมาย</div>
            <div class="flex flex-wrap gap-2">
                <template x-for="op in ['+','-','*','/','<','>','<=','>=','==','!=','IF(']">
                    <button type="button" @click="insert(op)"
                        class="px-3 py-1 rounded-xl bg-white text-slate-800 hover:bg-blue-100 text-sm">
                        <span x-text="op"></span>
                    </button>
                </template>
            </div>

            <div class="text-slate-700 font-medium mt-2">คำสั่ง</div>
            <div class="flex flex-wrap gap-2">
                <template x-for="op in ['AND','OR','NOR','XOR','XNOR','NAND','NOT']">
                    <button type="button" @click="insert(op)"
                        class="px-3 py-1 rounded-xl bg-white text-slate-800 hover:bg-blue-100 text-sm">
                        <span x-text="op"></span>
                    </button>
                </template>
            </div>

            <div class="text-slate-700 font-medium mt-2">ตัวแปร</div>
            <div class="flex flex-wrap gap-2">
                <template x-for="(v, i) in vars" :key="'chip' + v.id">
                    <button type="button" @click="insert(v.variable_name)"
                        class="px-3 py-1 rounded-full bg-slate-300 hover:bg-slate-200 text-slate-800 text-sm">
                        <span x-text="v.variable_name || 'var' + (i+1)"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Hidden field for POST --}}
        <input type="hidden" :name="`${prefix}[condition]`" :value="condition">
    </div>
</div>
