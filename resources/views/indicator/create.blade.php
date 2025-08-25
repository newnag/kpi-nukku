@extends('layouts.app')

@section('title', 'เพิ่มตัวชี้วัด')
@section('header', 'เพิ่มตัวชี้วัด')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
@endpush

@section('content')
    <form action="{{ route('indicator.store') }}" method="POST" class="mx-3 md:mx-9 mb-16 space-y-6">
        @csrf
        <x-card number="1" title="ข้อมูลตัวชี้วัด">
            <div class="grid md:grid-cols-2 gap-4">
                <x-select name="year_id" :options="$years ?? []" label="ปีการประเมิน" placeholder="กรุณาเลือกปีการประเมิน"
                    required />
                <x-input name="name" label="ชื่อตัวชี้วัด" placeholder="กรุณากรอกชื่อตัวชี้วัด" required />

                <x-input name="code" label="รหัสตัวชี้วัด" placeholder="เช่น NCS-1" />
                <x-input name="score_acc" type="number" step="0.01" label="คะแนนตัวชี้วัด" placeholder="กรุณากรอกคะแนน"
                    required />

                <x-select name="standard_id" :options="$standards ?? []" label="มาตรฐานตัวชี้วัด" placeholder="กรุณาเลือกมาตรฐาน"
                    required />
                <x-select name="side_id" :options="$sides ?? []" label="ด้านตัวชี้วัด" placeholder="กรุณาเลือกด้าน" required />

                <x-select name="type_id" :options="$types ?? []" label="ประเภทตัวชี้วัด" placeholder="กรุณาเลือกประเภท"
                    required />
                <x-input name="due_date" type="date" label="วันสิ้นสุดการประเมิน" />
            </div>
        </x-card>

        <x-card number="2" title="ผู้รับผิดชอบ">
            <x-select name="dept_id" :options="$departments ?? []" label="หน่วยงานที่รับผิดชอบ"
                placeholder="กรุณาเลือกหน่วยงานที่รับผิดชอบ" required />
            <x-select name="user_id" :options="$user ?? []" label="ผู้รับผิดชอบในการรวบรวมข้อมูล"
                placeholder="กรุณาเลือกผู้รับผิดชอบในการรวบรวมข้อมูล" required />
        </x-card>

        <x-card number="3" title="คำอธิบายตัวชี้วัด">
            <x-richtext name="description" placeholder="กรอกคำอธิบายตัวชี้วัด" />
        </x-card>

        <x-card number="4" title="เกณฑ์การพิจารณา" class="space-y-6">
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
                
                    // collect titles from inputs and broadcast + cache globally
                    broadcast() {
                        const inputs = Array.from($el.querySelectorAll('[data-criteria-title]'));
                        this.titles = inputs.map(el => el.value || '');
                        window.__criteriaTitles = this.titles.slice(); // 🔹 global cache
                        $dispatch('criteria-updated', { titles: this.titles }); // 🔹 global event
                    }
                }" x-init="$nextTick(() => broadcast())" {{-- 🔸 LOCAL listeners: no .window --}}
                    @criteria-remove="remove($event.detail.index-1)" @criteria-move-up="up($event.detail.index-1)"
                    @criteria-move-down="down($event.detail.index-1)"
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
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 py-2 hover:bg-blue-700">
                            เพิ่มเกณฑ์ <span class="text-xl leading-none">＋</span>
                        </button>
                    </div>
                </div>
            </x-card-box>

            <x-card-box title="วิธีการคำนวณ" icon="📋">
                <x-richtext name="calc_method" placeholder="กรอกคำอธิบายตัวชี้วัด" />
            </x-card-box>
        </x-card>


        <x-card number="5" title="เกณฑ์การให้คะแนน" class="space-y-6">
            <x-card-box title="เกณฑ์ให้คะแนนและคะแนนเต็ม" icon="📋">
                <x-input name="criteria_description" type="text" label="คำอธิบายเกณฑ์ให้คะแนน" />
                <x-input name="max_score" type="number" label="คะแนนเต็มทั้งหมดของตัวชี้วัด" />
            </x-card-box>

            <x-card-box title="เกณฑ์ให้คะแนนแบบหลายตัวเลือก" icon="📋">
                <div x-data="{
                    items: [{ id: Date.now() }],
                    add() {
                        this.items = [...this.items, { id: Date.now() + this.items.length }];
                        this.$nextTick(() => {
                            // 🔹 ping all multichoice instances (including the brand-new one)
                            window.dispatchEvent(new CustomEvent('criteria-updated', {
                                detail: { titles: window.__criteriaTitles || [] }
                            }));
                        });
                    },
                    remove(i) {
                        const a = [...this.items];
                        a.splice(i, 1);
                        this.items = a
                    },
                    up(i) {
                        if (i > 0) {
                            const a = [...this.items];
                            [a[i - 1], a[i]] = [a[i], a[i - 1]];
                            this.items = a
                        }
                    },
                    down(i) {
                        if (i < this.items.length - 1) {
                            const a = [...this.items];
                            [a[i + 1], a[i]] = [a[i], a[i + 1]];
                            this.items = a
                        }
                    },
                }" {{-- LOCAL listeners here as well --}} @criteria-remove="remove($event.detail.index-1)"
                    @criteria-move-up="up($event.detail.index-1)" @criteria-move-down="down($event.detail.index-1)"
                    class="space-y-4">
                    <template x-for="(it, i) in items" :key="it.id">
                        <div x-data="{ order: i + 1, prefix: 'multiScores[' + i + ']' }" x-effect="order = i+1; prefix = 'multiScores['+i+']'">
                            <x-multichoice-score :options="$criteriaOptions ?? []" :show-controls="true" />
                        </div>
                    </template>

                    <div class="pt-2">
                        <button type="button" @click="add()"
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 py-2 hover:bg-blue-700">
                            เพิ่มเกณฑ์ <span class="text-xl leading-none">＋</span>
                        </button>
                    </div>
                </div>
            </x-card-box>

            <x-card-box title="เกณฑ์ให้คะแนนแบบปรับแต่งอิสระ" icon="📋">
                <x-variable-formula prefix="scoring" />
            </x-card-box>
        </x-card>

        <x-card number="6" title="หมายเหตุ">
            <x-richtext name="description" placeholder="กรอกคำอธิบายตัวชี้วัด" />
        </x-card>

        <div class="flex items-center justify-end gap-3">
            <a class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50">ยกเลิก</a>
            <button type="submit"
                class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700">บันทึกตัวชี้วัด</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>
@endpush
