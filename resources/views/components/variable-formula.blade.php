@props(['prefix' => 'scoring', 'initial' => []])

@php
    // Existing props:
    // $prefix (e.g., 'scoring'), $initial (may contain resources/collections)

    // 1) Variables
    $initialVarsRaw = old($prefix . '.variables', $initial['variables'] ?? []);

    if ($initialVarsRaw instanceof \Illuminate\Http\Resources\Json\ResourceCollection) {
        $initialVarsRaw = $initialVarsRaw->jsonSerialize();
    } elseif ($initialVarsRaw instanceof \Illuminate\Support\Collection) {
        $initialVarsRaw = $initialVarsRaw->values()->toArray();
    } elseif (is_object($initialVarsRaw) && method_exists($initialVarsRaw, 'toArray')) {
        try {
            $initialVarsRaw = $initialVarsRaw->toArray(request());
        } catch (\Throwable $e) {
            $initialVarsRaw = $initialVarsRaw->toArray();
        }
    }

    // ensure plain, indexed array
    $initialVars = array_values((array) $initialVarsRaw);

    // 2) Condition / formula
    $initialCondition = old(
        $prefix . '.condition',
        old($prefix . '.formula', $initial['condition'] ?? ($initial['formula'] ?? '')),
    );

    // Check if we have existing formula data with ID (for updates)
    $hasExistingFormula = !empty($initial['condition']) && !empty($initial['formula_id'] ?? null);
    $formulaId = $initial['formula_id'] ?? null;
@endphp

<div x-data="{
    prefix: '{{ $prefix }}',
    vars: (@js($initialVars)).map(v => {
        const variable = {
            id: Date.now() + Math.random(),
            dbId: v.id ?? null,
            variable_name: v.variable_name ?? '',
            label_name: v.label_name ?? '',
            type: (v.type ?? 'defined'),
            value: ((v.type ?? 'defined') === 'defined') ? (v.value ?? '') : ''
        };

        const typePattern = new RegExp(`^${variable.type}_\\d+$`);
        if (!typePattern.test(variable.variable_name)) {
            variable.needsRename = true; // mark for post-init regeneration
        }
        return variable;
    }),
    condition: @js($initialCondition),

    // inputs for adding a new var
    newName: '',
    newLabel: '',
    newType: 'defined',
    newValue: '',

    get hasOutputVariable() {
        return this.vars.some(v => v.type === 'output');
    },

    generateVariableName(type) {
        const typePrefix = type || 'defined';
        const sameTypeVars = this.vars.filter(v => v.type === typePrefix);
        const nextNumber = sameTypeVars.length + 1;
        return `${typePrefix}_${nextNumber}`;
    },

    add() {
        const label = (this.newLabel || '').trim();
        if (!label) { 
            alert('กรุณากรอกป้ายชื่อ (label_name)'); 
            return; 
        }
        if (this.newType === 'output' && this.hasOutputVariable) {
            alert('สามารถมีตัวแปรประเภท Output ได้เพียง 1 ตัวเท่านั้น');
            return;
        }

        const generatedName = this.generateVariableName(this.newType);
        this.vars = [...this.vars, {
            id: Date.now() + Math.random(),
            dbId: null,
            variable_name: generatedName,
            label_name: label,
            type: this.newType,
            value: this.newType === 'defined' ? (this.newValue ?? '') : ''
        }];

        this.newName = '';
        this.newLabel = '';
        this.newType = 'defined';
        this.newValue = '';
        this.$nextTick(() => this.$refs.newLabel?.focus());
    },

    remove(i) {
        const a = [...this.vars];
        a.splice(i, 1);
        this.vars = a;
        this.regenerateVariableNames();
    },

    regenerateVariableNames() {
        const typeCounters = {};
        this.vars.forEach(v => {
            const type = v.type || 'defined';
            typeCounters[type] = (typeCounters[type] || 0) + 1;
            v.variable_name = `${type}_${typeCounters[type]}`;
        });
    },

    initializeVariableNames() {
        let hasChanged = false;
        this.vars.forEach(v => {
            if (v.needsRename) { hasChanged = true;
                delete v.needsRename; }
        });
        if (hasChanged) this.regenerateVariableNames();
    },

    insert(text) {
        const el = this.$refs.condition;
        const s = el.selectionStart ?? el.value.length;
        const e = el.selectionEnd ?? el.value.length;
        const before = el.value.slice(0, s),
            after = el.value.slice(e);
        this.condition = before + text + after;
        this.$nextTick(() => { el.focus(); const pos = s + text.length;
            el.setSelectionRange(pos, pos); });
    },

    reserved() { return new Set(['IF', 'AND', 'OR', 'NOR', 'XOR', 'XNOR', 'NAND', 'NOT', 'TRUE', 'FALSE', 'ELSE', 'THEN']); },
    extractVars(text) {
        const tokens = (text || '').match(/[A-Za-z_][A-Za-z0-9_]*/g) || [];
        const set = new Set(),
            r = this.reserved();
        tokens.forEach(t => { if (!r.has(t) && isNaN(Number(t))) set.add(t); });
        return Array.from(set);
    },
    get declaredVars() { return this.vars.map(v => (v.variable_name || '').trim()).filter(Boolean); },
    get referencedVars() { return this.extractVars(this.condition); },
    get unknownVars() { const d = new Set(this.declaredVars); return this.referencedVars.filter(n => !d.has(n)); },

    initSubmitGuard() {
        const form = this.$el.closest('form');
        if (!form) return;
        form.addEventListener('submit', (e) => {
            if (this.unknownVars.length) { e.preventDefault();
                alert('พบตัวแปรที่ยังไม่ได้ประกาศ: ' + this.unknownVars.join(', ')); }
        });
    }
}" x-init="initSubmitGuard();
initializeVariableNames();" class="space-y-5">

    {{-- ROW: Add Variable (mobile-first, stacks; spreads at md+) --}}
    <div class="grid grid-cols-1 gap-3 md:grid-cols-[1fr,minmax(200px,380px),auto] md:items-center">
        <label class="block text-slate-800 font-medium">สร้างตัวแปร</label>

        <div class="flex flex-col gap-2 min-w-0">
            <input x-ref="newLabel" x-model="newLabel" type="text" placeholder="ป้ายชื่อ (เช่น statA, input1)"
                class="p-2 w-full bg-white rounded-xl border border-slate-300 placeholder-slate-400 text-sm md:text-base hover:border-blue-400 transition" />

            {{-- Type + (Value when defined) — inline on md+, stacked on mobile --}}
            <div class="flex flex-col sm:flex-row gap-2">
                <select x-model="newType"
                    class="p-2 w-full sm:w-48 bg-white rounded-xl border border-slate-300 text-sm md:text-base hover:border-blue-400 transition">
                    <option value="defined">Defined</option>
                    <option value="output" x-show="!hasOutputVariable || newType === 'output'">Output</option>
                    <option value="input">Input</option>
                </select>

                <template x-if="newType === 'defined'">
                    <input x-model="newValue" type="number" inputmode="decimal" placeholder="ค่า"
                        class="p-2 w-full bg-white rounded-xl border border-slate-300 text-sm md:text-base hover:border-blue-400 transition">
                </template>
            </div>
        </div>

        <button type="button" @click="add()"
            class="justify-self-start md:justify-self-end inline-flex items-center gap-2 rounded-xl border border-blue-500 text-blue-600 px-3 py-2 md:px-4 md:py-2 hover:bg-blue-50 text-sm md:text-base">
            เพิ่มตัวแปร <span class="text-xl leading-none">＋</span>
        </button>
    </div>

    {{-- LIST: Variables (each row wraps gracefully) --}}
    <template x-for="(v, i) in vars" :key="v.id">
        <div
            class="flex flex-col md:flex-row md:items-center gap-3 bg-white rounded-2xl border border-slate-200 px-4 py-3">
            <div class="flex flex-col gap-1 flex-1 min-w-0">
                <div class="text-xs text-slate-500">Variable Name (Auto-generated)</div>
                <div class="text-blue-700 font-medium text-sm md:text-base truncate" x-text="v.variable_name"></div>
            </div>

            <div class="flex flex-col gap-1 flex-1 min-w-0">
                <div class="text-xs text-slate-500">Label Name</div>
                <input x-model="v.label_name"
                    class="bg-white rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-slate-700 text-sm md:text-base p-2"
                    placeholder="label_name" />
            </div>

            <span class="md:w-28 text-sm text-slate-600 md:text-left">
                <span x-text="v.type === 'defined' ? 'defined' : (v.type === 'input' ? 'input' : 'output')"></span>
            </span>

            <div class="flex items-center gap-2">
                <template x-if="v.type === 'defined'">
                    <input x-model.number="v.value" type="number" inputmode="decimal"
                        class="w-28 rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm md:text-base" />
                </template>
                <template x-if="v.type === 'input'">
                    <span class="text-slate-500 text-sm">ผู้ใช้กรอก</span>
                </template>
                <template x-if="v.type === 'output'">
                    <span class="text-slate-500 text-sm">คำนวณจากสูตร</span>
                </template>

                <button type="button" @click="remove(i)" class="text-slate-500 hover:text-red-600 text-sm md:text-base"
                    aria-label="ลบตัวแปร" title="ลบตัวแปร">✕</button>
            </div>

            {{-- Hidden fields for POST --}}
            <input type="hidden" :name="`${prefix}[variables][${i}][variable_name]`" :value="v.variable_name">
            <input type="hidden" :name="`${prefix}[variables][${i}][label_name]`" :value="v.label_name">
            <input type="hidden" :name="`${prefix}[variables][${i}][type]`" :value="v.type">
            <input type="hidden" :name="`${prefix}[variables][${i}][value]`" :value="v.value ?? ''">
            <template x-if="v.dbId">
                <input type="hidden" :name="`${prefix}[variables][${i}][id]`" :value="v.dbId">
            </template>
        </div>
    </template>

    {{-- CONDITION: Editor + helpers --}}
    <div class="space-y-3">
        <label class="block text-slate-800 font-medium">สร้างเงื่อนไขการคำนวณ</label>

        <textarea x-ref="condition" x-model="condition" rows="5"
            class="w-full bg-white rounded-2xl border border-blue-200 focus:border-blue-500 focus:ring-blue-500 p-3 text-sm md:text-base"
            placeholder="ตัวอย่าง: defined_1 * input_1"></textarea>

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

        {{-- Toolbars become horizontally scrollable on small screens --}}
        <div class="space-y-2">
            <div class="text-slate-700 font-medium">เครื่องหมาย</div>
            <div class="flex gap-2 overflow-x-auto no-scrollbar py-1 -mx-1 px-1">
                <template x-for="op in ['+','-','*','/','<','>','<=','>=','==','!=','IF(']">
                    <button type="button" @click="insert(op)"
                        class="px-3 py-1 rounded-xl bg-white text-slate-800 hover:bg-blue-100 text-sm shrink-0">
                        <span x-text="op"></span>
                    </button>
                </template>
            </div>

            <div class="text-slate-700 font-medium mt-2">คำสั่ง</div>
            <div class="flex gap-2 overflow-x-auto no-scrollbar py-1 -mx-1 px-1">
                <template x-for="op in ['AND','OR','NOR','XOR','XNOR','NAND','NOT']">
                    <button type="button" @click="insert(op)"
                        class="px-3 py-1 rounded-xl bg-white text-slate-800 hover:bg-blue-100 text-sm shrink-0">
                        <span x-text="op"></span>
                    </button>
                </template>
            </div>

            <div class="text-slate-700 font-medium mt-2">ตัวแปร</div>
            <div class="flex gap-2 flex-wrap sm:flex-nowrap overflow-x-auto no-scrollbar py-1 -mx-1 px-1">
                <template x-for="(v, i) in vars" :key="'chip' + v.id">
                    <button type="button" @click="insert(v.variable_name)"
                        class="px-3 py-1 rounded-full bg-slate-300 hover:bg-slate-200 text-slate-800 text-sm shrink-0">
                        <span x-text="v.variable_name || ('var' + (i+1))"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Hidden field for POST --}}
        <input type="hidden" :name="`${prefix}[condition]`" :value="condition">

        @if ($formulaId)
            <input type="hidden" name="{{ $prefix }}[formula_id]" value="{{ $formulaId }}">
        @endif
    </div>
</div>

{{-- Optional: hide scrollbar utility (add once in a global CSS or layout) --}}
<style>
    /* Hide scrollbars on helper toolbars (keeps touch scroll) */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
