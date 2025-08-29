@extends('layouts.app')

@section('title', 'เพิ่มตัวชี้วัด')
{{-- @section('header', 'เพิ่มตัวชี้วัด') --}}

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/ui/trumbowyg.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/plugins/table/ui/trumbowyg.table.min.css">
@endpush

@section('content')

    <form action="{{ route('indicator.store') }}" method="POST" class="" x-data="{ score_acc: @js(old('max_score', '')) }">
        @csrf
        <div class="w-full disabled:grid place-items-center">
            <div class="mb-5 w-fit px-5 pb-5 bg-white rounded-2xl border border-slate-200 shadow-sm ">
                <p class="text-3xl text-center font-bold py-7">เพิ่มตัวชี้วัด</p>
                <div class="max-w-5xl mx-auto space-y-5 sm:space-y-6 md:space-y-8">
                    <x-card number="1" title="ข้อมูลตัวชี้วัด">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                            <x-input name="year" type="number" maxlength="4" pattern="\d{4}" label="ปีการประเมิน"
                                placeholder="กรอกปีการประเมิน" required />

                            <x-input name="name" label="ชื่อตัวชี้วัด" placeholder="กรุณากรอกชื่อตัวชี้วัด" required />

                            <x-input name="code" label="รหัสตัวชี้วัด" placeholder="เช่น NCS-1" required />

                            <x-input name="max_score" type="number" step="1" label="คะแนนตัวชี้วัด"
                                placeholder="กรุณากรอกคะแนน" required x-model.number="score_acc" />

                            <x-select name="standard_id" :options="$standards ?? []" label="มาตรฐานตัวชี้วัด"
                                placeholder="กรุณาเลือกมาตรฐานตัวชี้วัด" searchable required />

                            <x-select name="category_id" :options="$categories ?? []" label="ด้านตัวชี้วัด"
                                placeholder="กรุณาเลือกด้าน" searchable required />

                            <x-select name="type" :options="['เชิงคุณภาพ', 'เชิงปริมาณ']" label="ประเภทตัวชี้วัด"
                                placeholder="กรุณาเลือกประเภท" required />
                            <x-input name="deadline" type="date" label="วันสิ้นสุดการประเมิน" required />
                        </div>
                    </x-card>

                    <x-card number="2" title="ผู้รับผิดชอบ">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                            <x-select name="department_id" :options="$departments ?? []" label="หน่วยงานที่รับผิดชอบ"
                                placeholder="กรุณาเลือกหน่วยงาน" searchable required />

                            <x-select name="user_id" :options="$users ?? []" label="ผู้รับผิดชอบในการรวบรวมข้อมูล"
                                placeholder="กรุณาเลือกผู้รับผิดชอบ" searchable required />
                        </div>
                    </x-card>

                    <x-card number="3" title="คำอธิบายตัวชี้วัด">
                        <x-richtext name="description" placeholder="กรอกคำอธิบายตัวชี้วัด" />
                    </x-card>

                    <x-card number="4" title="เกณฑ์การพิจารณา" class="space-y-5 sm:space-y-6">
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
                                    <div x-data="{ sequence: i + 1, prefix: 'criteria[' + i + ']' }" x-effect="sequence = i+1; prefix = 'criteria['+i+']'">
                                        <x-card-criteria :show-controls="true" />
                                    </div>
                                </template>

                                <div class="pt-2">
                                    <button type="button" @click="add()"
                                        class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-3 py-2 md:px-4 md:py-2 hover:bg-blue-700 text-sm md:text-base">
                                        เพิ่มเกณฑ์ <span class="text-xl leading-none">＋</span>
                                    </button>
                                </div>
                            </div>
                        </x-card-box>

                        <x-card-box title="วิธีการคำนวณ" icon="📋">
                            <x-richtext name="condition" placeholder="กรอกคำอธิบายตัวชี้วัด" />
                        </x-card-box>
                    </x-card>

                    <x-card number="5" title="เกณฑ์การให้คะแนน" class="space-y-5 sm:space-y-6" x-data="{ scoringMethod: '' }">

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

                        {{-- Dropdown to select scoring method --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium text-slate-700">เลือกวิธีการให้คะแนน</label>
                            <select x-model="scoringMethod"
                                class="w-full rounded-xl border border-slate-300 p-2 text-sm md:text-base">
                                <option value="">-- กรุณาเลือกวิธีการให้คะแนน --</option>
                                <option value="count">หลายตัวเลือก - ตามจำนวนข้อที่เลือก</option>
                                <option value="selected">หลายตัวเลือก - คะแนนตามข้อที่เลือก</option>
                                <option value="custom">ปรับแต่งอิสระ</option>
                            </select>
                        </div>

                        {{-- Conditionally render based on dropdown --}}
                        <div class="space-y-5 sm:space-y-6">
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
                                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-3 py-2 md:px-4 md:py-2 hover:bg-blue-700 text-sm md:text-base">
                                            เพิ่มเกณฑ์ <span class="text-xl leading-none">＋</span>
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
                    <x-card number="6" title="หมายเหตุ">
                        <x-richtext name="annotation" placeholder="...." />
                    </x-card>

                    <div class="flex justify-center gap-3">
                        <button type="button" onclick="history.back()"
                            class="inline-flex items-center gap-2 rounded-xl bg-gray-200 text-gray-700 px-4 py-2 hover:bg-gray-300 text-sm md:text-base">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            กลับ
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 text-sm md:text-base">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            บันทึกตัวชี้วัด
                        </button>
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
