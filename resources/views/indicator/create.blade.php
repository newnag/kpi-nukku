@extends('layouts.app')

@section('title', 'เพิ่มตัวชี้วัด')
{{-- @section('header', 'เพิ่มตัวชี้วัด') --}}

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/ui/trumbowyg.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/plugins/table/ui/trumbowyg.table.min.css">
    <style>
        .banner {
            background: linear-gradient(90deg, #e0f2fe 0%, #fef3e0 100%);
            transition: all 0.3s ease-in-out;
        }
    </style>
@endpush

@section('content')
    <form action="{{ route('indicator.store') }}" method="POST" x-data="{ score_acc: @js(old('max_score', '')) }">
        @csrf
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <div class="banner rounded-t-2xl border border-slate-200 p-5 ">
                    <h1 class="text-2xl sm:text-3xl text-center font-bold">เพิ่มตัวชี้วัด</h1>
                </div>
                <div class="mb-5 w-full px-4 sm:px-6 lg:px-8 py-6 bg-white rounded-b-2xl border border-slate-200 shadow-sm">

                    <div class="space-y-6 sm:space-y-8">
                        {{-- Card 1: Basic --}}
                        <x-card number="1" title="ข้อมูลตัวชี้วัด">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                                <x-input name="year" type="number" maxlength="4" pattern="\d{4}" label="ปีการประเมิน"
                                    placeholder="กรอกปีการประเมิน" required />
                                <x-input name="name" label="ชื่อตัวชี้วัด" placeholder="กรุณากรอกชื่อตัวชี้วัด"
                                    required />
                                <x-input name="code" label="รหัสตัวชี้วัด" placeholder="เช่น NCS-1" required />
                                <x-input name="max_score" type="number" step="1" label="คะแนนตัวชี้วัด"
                                    placeholder="กรุณากรอกคะแนน" required x-model.number="score_acc" />
                                <x-select name="standard_id" :options="$standards ?? []" label="มาตรฐานตัวชี้วัด"
                                    placeholder="กรุณาเลือกมาตรฐานตัวชี้วัด" required />
                                <x-select name="category_id" :options="$categories ?? []" label="ด้านตัวชี้วัด"
                                    placeholder="กรุณาเลือกด้าน" searchable required />
                                <x-select name="type" :options="['เชิงคุณภาพ' => 'เชิงคุณภาพ', 'เชิงปริมาณ' => 'เชิงปริมาณ']" label="ประเภทตัวชี้วัด"
                                    placeholder="กรุณาเลือกประเภท" required />
                                <x-input name="deadline" type="date" label="วันสิ้นสุดการประเมิน" required />
                            </div>
                        </x-card>

                        {{-- Card 2: Responsible --}}
                        <x-card number="2" title="ผู้รับผิดชอบ">
                            <div x-data="{
                                usersAll: @js($usersForAssign ?? []), // [{id,name,department_id}]
                                depSelected: (@js(old('department_ids', [])) || []).map(v => String(v)),
                            
                                usersEl() { return this.$refs.usersMulti },
                                deptEl() { return this.$refs.deptMulti },
                            
                                filteredUsers() {
                                    if (!this.depSelected?.length) return this.usersAll;
                                    const set = new Set(this.depSelected.map(String));
                                    return this.usersAll.filter(u => set.has(String(u.department_id)));
                                },
                            
                                refreshUsers() {
                                    const filtered = this.filteredUsers();
                                    this.usersEl()?.setOptions?.(filtered);
                            
                                    const allowed = new Set(filtered.map(u => String(u.id)));
                                    const current = (this.usersEl()?.selected || []).map(String);
                                    const keep = current.filter(v => allowed.has(v));
                                    this.usersEl()?.setSelected?.(keep);
                                }
                            }" x-init="$nextTick(() => setTimeout(() => refreshUsers(), 50))"
                                @multiselect-change.window="
                            if ($event.detail?.name === 'department_ids') {
                              depSelected = ($event.detail.values || []).map(String);
                              refreshUsers();
                            }
                          "
                                class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                                {{-- Departments (multi) --}}
                                <x-multiselect x-ref="deptMulti" name="department_ids" label="หน่วยงานที่รับผิดชอบ"
                                    :options="$departments ?? []" placeholder="กรุณาเลือกหน่วยงาน" searchable select-all />

                                {{-- Users (multi, filtered by departments) --}}
                                <x-multiselect x-ref="usersMulti" name="user_ids" label="ผู้รับผิดชอบในการรวบรวมข้อมูล"
                                    :options="$usersForAssign ?? []" optionValue="id" optionLabel="name"
                                    placeholder="กรุณาเลือกผู้รับผิดชอบ" searchable select-all required />
                            </div>
                        </x-card>

                        {{-- Card 3: Description --}}
                        <x-card number="3" title="คำอธิบายตัวชี้วัด">
                            <x-richtext name="description" placeholder="กรอกคำอธิบายตัวชี้วัด" />
                        </x-card>

                        {{-- Card 4: Criteria & Condition --}}
                        <x-card number="4" title="เกณฑ์การพิจารณา" class="space-y-6">
                            <x-card-box title="รายการเกณฑ์การพิจารณา" icon="📋">
                                <div x-data="{
                                    items: [{ id: Date.now() }],
                                    name: [],
                                    add() {
                                        this.items = [...this.items, { id: Date.now() + this.items.length }];
                                        this.$nextTick(() => this.broadcast())
                                    },
                                    remove(i) {
                                        const a = [...this.items];
                                        a.splice(i, 1);
                                        this.items = a;
                                        this.$nextTick(() => this.broadcast())
                                    },
                                    up(i) {
                                        if (i > 0) {
                                            const a = [...this.items];
                                            [a[i - 1], a[i]] = [a[i], a[i - 1]];
                                            this.items = a;
                                            this.$nextTick(() => this.broadcast())
                                        }
                                    },
                                    down(i) {
                                        if (i < this.items.length - 1) {
                                            const a = [...this.items];
                                            [a[i + 1], a[i]] = [a[i], a[i + 1]];
                                            this.items = a;
                                            this.$nextTick(() => this.broadcast())
                                        }
                                    },
                                    broadcast() {
                                        const inputs = Array.from($el.querySelectorAll('[data-criteria-name]'));
                                        this.name = inputs.map(el => el.value || '');
                                        window.__criteriaTitles = this.name.slice();
                                        $dispatch('criteria-updated', { name: this.name });
                                        window.dispatchEvent(new CustomEvent('criteria-updated', { detail: { name: this.name } }));
                                    }
                                }" x-init="$nextTick(() => broadcast())"
                                    @criteria-remove="remove($event.detail.index-1)"
                                    @criteria-move-up="up($event.detail.index-1)"
                                    @criteria-move-down="down($event.detail.index-1)"
                                    @criteria-name-change="
                                  name[$event.detail.idx] = $event.detail.name;
                                  window.__criteriaTitles = name.slice();
                                  $dispatch('criteria-updated', { name })
                                "
                                    class="space-y-4">
                                    <template x-for="(it, i) in items" :key="it.id">
                                        <div x-data="{ sequence: i + 1, prefix: 'criteria[' + i + ']' }"
                                            x-effect="sequence = i + 1; prefix = 'criteria[' + i + ']'">
                                            <x-card-criteria :show-controls="true" />
                                        </div>
                                    </template>

                                    <div class="pt-3">
                                        <button type="button" @click="add()"
                                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 py-2 md:px-5 md:py-3 hover:bg-blue-700 text-sm md:text-base transition-colors">
                                            <span>เพิ่มเกณฑ์</span>
                                            <span class="text-lg md:text-xl leading-none">＋</span>
                                        </button>
                                    </div>
                                </div>
                            </x-card-box>

                            <x-card-box title="วิธีการคำนวณ" icon="📋">
                                <x-richtext name="condition" placeholder="กรอกวิธีการคำนวณ/เงื่อนไข" />
                            </x-card-box>
                        </x-card>

                        {{-- Card 5: Scoring --}}
                        <x-card number="5" title="เกณฑ์การให้คะแนน" class="space-y-6" x-data="{
                            scoringMethod() {
                                return document.querySelector('input[name=scoring_method]')?.value || '';
                            }
                        }">
                            <x-card-box title="เกณฑ์ให้คะแนนและคะแนนเต็ม" icon="📋">
                                <div>
                                    <x-richtext name="comment" placeholder="คำอธิบายเกณฑ์ให้คะแนน" />
                                    <label class="block">
                                        <span class="text-sm font-medium text-slate-700">คะแนนเต็มทั้งหมดของตัวชี้วัด</span>
                                        <input type="text" :value="score_acc" readonly
                                            class="p-2 mt-1 w-full bg-gray-100 rounded-xl border border-slate-300 text-sm md:text-base cursor-not-allowed"
                                            placeholder="คะแนนจะปรากฏที่นี่">
                                    </label>
                                </div>
                            </x-card-box>

                            <div class="w-full">
                                <label class="block mb-3 text-sm font-medium text-slate-700">เลือกวิธีการให้คะแนน</label>

                                <select x-model="scoringMethod"
                                    class="p-2 mt-1 w-full bg-white rounded-xl border border-slate-300 
                placeholder-slate-400 text-sm md:text-base 
                hover:shadow-md hover:border-blue-400 transition
                focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                                    <option value="">-- กรุณาเลือกวิธีการให้คะแนน --</option>
                                    <option value="count">หลายตัวเลือก - ตามจำนวนข้อที่เลือก</option>
                                    <option value="selected">หลายตัวเลือก - คะแนนตามข้อที่เลือก</option>
                                    <option value="custom">ปรับแต่งอิสระ</option>
                                </select>

                            </div>

                            <div class="space-y-6">
                                <template x-if="scoringMethod === 'count'">
                                    <x-card-box title="เกณฑ์ให้คะแนนแบบหลายตัวเลือก-ตามจำนวนข้อที่เลือก" icon="📋">
                                        <x-multichoice-score-count name-prefix="multiCounts" />
                                    </x-card-box>
                                </template>

                                <template x-if="scoringMethod === 'selected'">
                                    <x-card-box title="เกณฑ์ให้คะแนนแบบหลายตัวเลือก-คะแนนตามข้อที่เลือก" icon="📋">
                                        <div x-data="{
                                            items: [{ id: Date.now() }],
                                            add() { this.items = [...this.items, { id: Date.now() + this.items.length }] },
                                            remove(i) {
                                                const a = [...this.items];
                                                a.splice(i, 1);
                                                this.items = a.length ? a : [{ id: Date.now() }]
                                            }
                                        }" @criteria-remove="remove($event.detail.index - 1)"
                                            class="space-y-4">
                                            <template x-for="(it, i) in items" :key="it.id">
                                                <div x-data="{ sequence: i + 1, prefix: 'multiSelected[' + i + ']' }"
                                                    x-effect="sequence = i + 1; prefix = 'multiSelected[' + i + ']'">
                                                    <x-multichoice-score-selected :options="$criteriaOptions ?? []" />
                                                </div>
                                            </template>

                                            <button type="button" @click="add()"
                                                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 py-2 md:px-5 md:py-3 hover:bg-blue-700 text-sm md:text-base transition-colors">
                                                <span>เพิ่มเกณฑ์</span>
                                                <span class="text-lg md:text-xl leading-none">＋</span>
                                            </button>
                                        </div>
                                    </x-card-box>
                                </template>

                                <template x-if="scoringMethod === 'custom'">
                                    <x-card-box title="เกณฑ์ให้คะแนนแบบปรับแต่งอิสระ" icon="📋">
                                        <x-variable-formula prefix="scoring" />
                                    </x-card-box>
                                </template>
                            </div>
                        </x-card>

                        {{-- Card 6: Note --}}
                        <x-card number="6" title="หมายเหตุ">
                            <x-richtext name="annotation" placeholder="...." />
                        </x-card>

                        {{-- Actions --}}
                        <div class="flex flex-col sm:flex-row justify-center gap-4 pt-6">
                            <button type="button" onclick="history.back()"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-200 text-gray-700 px-6 py-3 hover:bg-gray-300 text-sm md:text-base transition-colors order-2 sm:order-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                <span>กลับ</span>
                            </button>
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 text-white px-6 py-3 hover:bg-blue-700 text-sm md:text-base transition-colors order-1 sm:order-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>บันทึกตัวชี้วัด</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/trumbowyg.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/plugins/justify/trumbowyg.justify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/plugins/table/trumbowyg.table.min.js"></script>
@endpush
