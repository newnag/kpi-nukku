@extends('layouts.app')

@section('title', 'เพิ่มตัวชี้วัด')
@section('header', 'เพิ่มตัวชี้วัด')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/ui/trumbowyg.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/plugins/table/ui/trumbowyg.table.min.css">
    <style>
        /* ---- Shell ---- */
        .trumbowyg-box {
            border-radius: 1rem;
            /* rounded-2xl */
            border: 1px solid #e5e7eb;
            /* slate-200 */
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
            overflow: hidden;
            background: #fff;
        }

        /* ---- Toolbar ---- */
        .trumbowyg-box .trumbowyg-button-pane {
            display: flex;
            flex-wrap: wrap;
            gap: .25rem .375rem;
            padding: .375rem .5rem;
            /* 6px 8px */
            background: #fff;
        }

        .trumbowyg-box .trumbowyg-button-pane::before,
        .trumbowyg-box .trumbowyg-button-pane::after {
            content: none;
            display: none;
        }

        /* hide default lines */

        .trumbowyg-box .trumbowyg-button-group {
            display: flex;
            gap: .25rem;
        }

        .trumbowyg-box .trumbowyg-button-pane button {
            border-radius: .5rem;
            /* rounded-lg */
            padding: .25rem;
            transition: background .15s ease;
        }

        .trumbowyg-box .trumbowyg-button-pane button:hover {
            background: #f1f5f9;
        }

        /* slate-100 */

        @media (max-width:640px) {
            .trumbowyg-box .trumbowyg-button-pane {
                padding: .25rem .5rem;
            }

            .trumbowyg-box .trumbowyg-button-pane button {
                transform: scale(.95);
            }
        }

        /* ---- Editor area ---- */
        .trumbowyg-box .trumbowyg-editor {
            padding: .75rem;
            /* px-3 py-3 */
            font-size: .9375rem;
            /* text-[15px] ~ text-sm/md */
            min-height: var(--rte-min-h, 260px);
        }

        /* Lists (Tailwind preflight restores) */
        .trumbowyg-box .trumbowyg-editor ul {
            list-style: disc;
            padding-left: 1.25rem;
        }

        .trumbowyg-box .trumbowyg-editor ol {
            list-style: decimal;
            padding-left: 1.5rem;
        }

        .trumbowyg-box .trumbowyg-editor li {
            list-style: inherit;
            margin: .25rem 0;
        }

        /* Tables */
        .trumbowyg-box .trumbowyg-editor table {
            width: 100%;
            border-collapse: collapse;
            font-size: .95rem;
        }

        .trumbowyg-box .trumbowyg-editor table,
        .trumbowyg-box .trumbowyg-editor th,
        .trumbowyg-box .trumbowyg-editor td {
            border: 1px solid #e5e7eb;
        }

        .trumbowyg-box .trumbowyg-editor th,
        .trumbowyg-box .trumbowyg-editor td {
            padding: .5rem .625rem;
        }

        /* Dropdowns */
        .trumbowyg-box .trumbowyg-dropdown {
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, .06);
        }
    </style>
@endpush

@section('content')
    <form action="{{ route('indicator.store') }}" method="POST" class="mx-3 md:mx-6" x-data="{ score_acc: @js(old('score_acc', '')) }">
        @csrf

        <div class="max-w-5xl mx-auto space-y-5 sm:space-y-6 md:space-y-8 mb-16">
            <x-card number="1" title="ข้อมูลตัวชี้วัด">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                    <x-input name="year_id" type="numeric" maxlength="4" pattern="\d{4}" label="ปีการประเมิน"
                        placeholder="กรอกปีการประเมิน" required />

                    <x-input name="name" label="ชื่อตัวชี้วัด" placeholder="กรุณากรอกชื่อตัวชี้วัด" required />

                    <x-input name="code" label="รหัสตัวชี้วัด" placeholder="เช่น NCS-1" />

                    <x-input name="score_acc" type="number" step="1" label="คะแนนตัวชี้วัด"
                        placeholder="กรุณากรอกคะแนน" required x-model.number="score_acc" />

                    <x-select name="standard" :options="$standards ?? []" label="มาตรฐานตัวชี้วัด"
                        placeholder="กรุณาเลือกมาตรฐานตัวชี้วัด" searchable required />

                    <x-select name="category" :options="$categories ?? []" label="ด้านตัวชี้วัด" placeholder="กรุณาเลือกด้าน"
                        searchable required />

                    <x-select name="type_id" :options="$types ?? []" label="ประเภทตัวชี้วัด" placeholder="กรุณาเลือกประเภท"
                        required />
                    <x-input name="due_date" type="date" label="วันสิ้นสุดการประเมิน" />
                </div>
            </x-card>

            <x-card number="2" title="ผู้รับผิดชอบ">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                    <x-select name="dept_id" :options="$departments ?? []" label="หน่วยงานที่รับผิดชอบ"
                        placeholder="กรุณาเลือกหน่วยงาน" searchable required />

                    <x-select name="user_id" :options="$user ?? []" label="ผู้รับผิดชอบในการรวบรวมข้อมูล"
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
                        titles: [],
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
                            const inputs = Array.from($el.querySelectorAll('[data-criteria-title]'));
                            this.titles = inputs.map(el => el.value || '');
                            window.__criteriaTitles = this.titles.slice();
                            $dispatch('criteria-updated', { titles: this.titles });
                            window.dispatchEvent(new CustomEvent('criteria-updated', { detail: { titles: this.titles } }));
                        }
                    }" x-init="$nextTick(() => broadcast())" @criteria-remove="remove($event.detail.index-1)"
                        @criteria-move-up="up($event.detail.index-1)" @criteria-move-down="down($event.detail.index-1)"
                        @criteria-title-change="
              titles[$event.detail.idx] = $event.detail.title;
              window.__criteriaTitles = titles.slice();
              $dispatch('criteria-updated', { titles })
            "
                        class="space-y-4">
                        <template x-for="(it, i) in items" :key="it.id">
                            <div x-data="{ order: i + 1, prefix: 'criteria[' + i + ']' }" x-effect="order = i+1; prefix = 'criteria['+i+']'">
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
                    <x-richtext name="calc_method" placeholder="กรอกคำอธิบายตัวชี้วัด" />
                </x-card-box>
            </x-card>

            <x-card number="5" title="เกณฑ์การให้คะแนน" class="space-y-5 sm:space-y-6">
                <x-card-box title="เกณฑ์ให้คะแนนและคะแนนเต็ม" icon="📋">
                    <div>
                        <x-richtext name="note" placeholder="คำอธิบายเกณฑ์ให้คะแนน" />
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">คะแนนเต็มทั้งหมดของตัวชี้วัด</span>
                            <input type="text" :value="score_acc" readonly
                                class="p-2 mt-1 w-full bg-gray-100 rounded-xl border border-slate-300 text-sm md:text-base cursor-not-allowed"
                                placeholder="คะแนนจะปรากฏที่นี่">
                        </label>
                    </div>
                </x-card-box>

                <x-card-box title="เกณฑ์ให้คะแนนแบบหลายตัวเลือก-ตามจำนวนข้อที่เลือก" icon="📋">
                    <x-multichoice-score-count name-prefix="multiCounts[0]" :index="1" />
                </x-card-box>

                <x-card-box title="เกณฑ์ให้คะแนนแบบหลายตัวเลือก-คะแนนตามข้อที่เลือก" icon="📋">
                    <div x-data="{
                        items: [{ id: Date.now() }],
                        add() { this.items = [...this.items, { id: Date.now() + this.items.length }] },
                        remove(i) {
                            const a = [...this.items];
                            a.splice(i, 1);
                            this.items = a.length ? a : [{ id: Date.now() }]
                        }
                    }" @criteria-remove="remove($event.detail.index - 1)" class="space-y-4">
                        <template x-for="(it, i) in items" :key="it.id">
                            <div x-data="{ order: i + 1, prefix: 'multiSelected[' + i + ']' }" x-effect="order = i + 1; prefix = 'multiSelected[' + i + ']'">
                                <x-multichoice-score-selected :options="$criteriaOptions ?? []" />
                            </div>
                        </template>

                        <button type="button" @click="add()"
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-3 py-2 md:px-4 md:py-2 hover:bg-blue-700 text-sm md:text-base">
                            เพิ่มเกณฑ์ <span class="text-xl leading-none">＋</span>
                        </button>
                    </div>
                </x-card-box>

                <x-card-box title="เกณฑ์ให้คะแนนแบบปรับแต่งอิสระ" icon="📋">
                    <x-variable-formula prefix="scoring" />
                </x-card-box>
            </x-card>

            <x-card number="6" title="หมายเหตุ">
                <x-richtext name="notation" placeholder="...." />
            </x-card>
        </div>
    </form>
@endsection

@push('scripts')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/trumbowyg.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/plugins/table/trumbowyg.table.min.js"></script>
@endpush
