@extends('layouts.app')
@section('title', 'กราฟผลลัพธ์')
@section('content')

    <div class="resul-container">
        <!-- Header -->
        <div class="resul-header">
            <h1>
                กราฟผลลัพธ์การประเมิน
                <span class="subtitle">/ ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล</span>
            </h1>
        </div>
        {{-- ฟิลเตอร์ --}}
        <div class="filter-card card">
            <h2 class="card-title">กรองข้อมูลการประเมิน</h2>

            <div class="form-grid">
                <div class="field">
                    <label>ปีการประเมิน</label>
                    <select id="filter-year">
                        <option value="">ทั้งหมด</option>
                    </select>
                </div>

                <div class="field">
                    <label>รหัสตัวชี้วัด</label>
                    <select id="filter-code">
                        <option value="">ทั้งหมด</option>
                    </select>
                </div>

                <div class="field">
                    <label>มาตรฐานตัวชี้วัด</label>
                    <select id="filter-standard">
                        <option value="">ทั้งหมด</option>
                    </select>
                </div>

                <div class="field">
                    <label>ด้านตัวชี้วัด</label>
                    <select id="filter-dimension">
                        <option value="">ทั้งหมด</option>
                    </select>
                </div>

                <div class="field">
                    <label>ประเภทตัวชี้วัด</label>
                    <select id="filter-type">
                        <option value="">ทั้งหมด</option>
                    </select>
                </div>
            </div>

            <div class="card-actions">
                <button type="button" id="reset-filters" class="btn btn-outline">ล้างค่า</button>
                <button type="button" id="apply-filters" class="btn btn-primary">กรองข้อมูล</button>
            </div>
        </div>

        {{-- กราฟ แบ่งกลุ่มตามมาตรฐาน --}}
        <div class="charts-grid">
            @foreach ($standards as $standard)
                <div class="stat-title" style="margin:12px 0 8px;">
                    <h3>กราฟผลลัพธ์ {{ $standard->name }}</h3>
                </div>

                @php $bucket = $chartsByStandard[$standard->id] ?? null; @endphp

                @if ($bucket && !empty($bucket['indicators']))
                    <div class="charts-of-standard" data-standard-id="{{ $standard->id }}"
                        style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px;">
                        @foreach ($bucket['indicators'] as $i => $c)
                            <div class="chart-card" data-standard="{{ $standard->id }}"
                                data-dimension="{{ $c['category_name'] }}" data-type="{{ $c['indicator_type'] }}"
                                data-code="{{ $c['indicator_code'] }}" data-years='@json($c['years'])'
                                data-index="{{ $i }}"
                                style="{{ $i >= 10 ? 'display:none;' : '' }};background:#fff;border-radius:16px;padding:16px;box-shadow:0 2px 10px rgba(0,0,0,.06);">
                                <div class="subtitle" style="font-weight:600;margin-bottom:8px;">
                                    {{ $c['indicator_code'] ? '[' . $c['indicator_code'] . '] ' : '' }}
                                    {{ $c['indicator_name'] }}
                                </div>
                                <canvas id="chart-{{ $standard->id }}-{{ $c['indicator_id'] }}" height="180"></canvas>
                                <script id="data-{{ $standard->id }}-{{ $c['indicator_id'] }}" type="application/json">
                                    @json(['years' => $c['years'], 'values' => $c['values']], JSON_UNESCAPED_UNICODE)
                                </script>
                            </div>
                        @endforeach
                    </div>

                    @if (count($bucket['indicators']) > 10)
                        <button type="button" class="btn-show-more" data-standard-id="{{ $standard->id }}"
                            style="margin-top:10px;display:block;">
                            แสดงเพิ่มเติม
                        </button>
                    @endif
                @else
                    <div style="color:#6b7280;margin-bottom:16px;">ไม่มีข้อมูลตัวชี้วัดที่มีการบันทึกผลลัพธ์</div>
                @endif
            @endforeach
        </div>


    </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.469.0/dist/umd/lucide.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>




    <script>
        (function() {
            // ====== ข้อมูลตัวเลือกที่เตรียมมาจาก Controller ======
            const FILTERS = @json($filters, JSON_UNESCAPED_UNICODE);

            // เติม options ให้ select ต่าง ๆ
            const $year = document.getElementById('filter-year');
            const $code = document.getElementById('filter-code');
            const $std = document.getElementById('filter-standard');
            const $dim = document.getElementById('filter-dimension');
            const $type = document.getElementById('filter-type');

            function fillSelect(sel, items, mapper) {
                // ล้างที่ไม่ใช่ค่าเริ่มต้น
                [...sel.querySelectorAll('option')].forEach(o => {
                    if (o.value) o.remove();
                });
                (items || []).forEach(it => {
                    const opt = document.createElement('option');
                    if (mapper) {
                        const {
                            value,
                            label
                        } = mapper(it);
                        opt.value = value;
                        opt.textContent = label;
                    } else {
                        opt.value = it;
                        opt.textContent = it;
                    }
                    sel.appendChild(opt);
                });
            }

            fillSelect($year, (FILTERS.years || []).sort());

            function sortCodes(codes) {
                return codes.sort((a, b) => {
                    const ma = a.match(/^([A-Za-z]+)[- ]?(\d+)$/i);
                    const mb = b.match(/^([A-Za-z]+)[- ]?(\d+)$/i);

                    const prefixA = ma ? ma[1].toUpperCase() : a;
                    const prefixB = mb ? mb[1].toUpperCase() : b;
                    const numA = ma ? parseInt(ma[2], 10) : 0;
                    const numB = mb ? parseInt(mb[2], 10) : 0;

                    if (prefixA === prefixB) {
                        return numA - numB;
                    }
                    return prefixA.localeCompare(prefixB);
                });
            }

            fillSelect($code, sortCodes(FILTERS.codes || []));

            fillSelect($std, (FILTERS.standards || []).sort((a, b) => a.name.localeCompare(b.name, 'th')), it => ({
                value: String(it.id),
                label: it.name
            }));
            fillSelect($dim, (FILTERS.dimensions || []).sort());
            fillSelect($type, (FILTERS.types || []).sort());

            // ====== วาดกราฟทั้งหมดครั้งแรก ======
            const chartInstances = {}; // key = canvasId -> Chart instance
            const chartOriginals = {}; // key = canvasId -> { years:[], values:[] }

            function renderAllCharts() {
                document.querySelectorAll('.btn-show-more').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const sid = this.dataset.standardId;
                        const container = document.querySelector(
                            `.charts-of-standard[data-standard-id="${sid}"]`);
                        const cards = container.querySelectorAll('.chart-card[data-index]');
                        const isExpanded = this.classList.contains('expanded');

                        if (!isExpanded) {
                            // แสดงทั้งหมด
                            cards.forEach(c => c.style.display = '');
                            this.textContent = 'แสดงน้อยลง';
                            this.classList.add('expanded');
                        } else {
                            // ซ่อนเกิน 10
                            cards.forEach(c => {
                                const idx = parseInt(c.dataset.index, 10);
                                c.style.display = idx < 10 ? '' : 'none';
                            });
                            this.textContent = 'แสดงเพิ่มเติม';
                            this.classList.remove('expanded');
                        }
                    });
                });

            }
            renderAllCharts();

            // ====== ฟิลเตอร์กราฟ (แสดง/ซ่อนการ์ด + กรองปีในตัวกราฟ) ======
            function applyFilters() {
                const vYear = $year.value;
                const vCode = $code.value;
                const vStd = $std.value;
                const vDim = $dim.value;
                const vType = $type.value;

                // 1) แสดง/ซ่อนการ์ด
                document.querySelectorAll('.chart-card').forEach(card => {
                    let show = true;
                    if (vStd && card.dataset.standard !== vStd) show = false;
                    if (vDim && (card.dataset.dimension || '') !== vDim) show = false;
                    if (vType && (card.dataset.type || '') !== vType) show = false;
                    if (vCode && (card.dataset.code || '') !== vCode) show = false;

                    if (vYear) {
                        try {
                            const years = JSON.parse(card.getAttribute('data-years') || '[]');
                            if (!years.includes(vYear)) show = false;
                        } catch (e) {
                            /* ignore */
                        }
                    }

                    card.style.display = show ? '' : 'none';
                });

                // 2) ซ่อน/โชว์หัวข้อมาตรฐาน
                document.querySelectorAll('.charts-of-standard').forEach(group => {
                    const anyVisible = !![...group.querySelectorAll('.chart-card')].find(c => c.style
                        .display !== 'none');
                    const title = group.previousElementSibling; // .stat-title
                    group.style.display = anyVisible ? '' : 'none';
                    if (title && title.classList.contains('stat-title')) {
                        title.style.display = anyVisible ? '' : 'none';
                    }
                });

                // 3) อัปเดต “ภายในกราฟ” เมื่อกรองปี
                Object.entries(chartInstances).forEach(([canvasId, inst]) => {
                    const orig = chartOriginals[canvasId];
                    if (!orig) return;

                    if (vYear) {
                        const idxs = [];
                        orig.years.forEach((y, i) => {
                            if (String(y) === String(vYear)) idxs.push(i);
                        });
                        inst.data.labels = idxs.map(i => orig.years[i]);
                        inst.data.datasets[0].data = idxs.map(i => orig.values[i]);
                    } else {
                        inst.data.labels = [...orig.years];
                        inst.data.datasets[0].data = [...orig.values];
                    }
                    inst.update();
                });
            }

            document.getElementById('apply-filters').addEventListener('click', applyFilters);

            // ====== รีเซ็ต: ล้างค่า + กู้กราฟจากต้นฉบับ + แสดงทุกอย่าง ======
            // ===== RESET: ต้อง "กู้จากต้นฉบับ" แล้วค่อย apply =====
            document.getElementById('reset-filters').addEventListener('click', () => {
                // 1) เซ็ต dropdown กลับ "ทั้งหมด"
                [$year, $code, $std, $dim, $type].forEach(sel => {
                    if (sel) sel.selectedIndex = 0;
                });

                // 2) แสดงการ์ด/หัวข้อทั้งหมด
                document.querySelectorAll('.chart-card').forEach(c => c.style.display = '');
                document.querySelectorAll('.charts-of-standard, .stat-title').forEach(el => el.style.display =
                    '');

                // 3) ทำลายแล้วสร้างกราฟใหม่จาก chartOriginals
                Object.entries(chartInstances).forEach(([canvasId, inst]) => {
                    try {
                        inst.destroy();
                    } catch (e) {}

                    const orig = chartOriginals[canvasId] || {
                        years: [],
                        values: []
                    };
                    const canvas = document.getElementById(canvasId);
                    if (!canvas) return;

                    chartInstances[canvasId] = new Chart(canvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: [...orig.years],
                            datasets: [{
                                data: [...orig.values],
                                borderWidth: 1,
                                borderRadius: 12
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                });

                // 4) เรียก applyFilters อีกครั้ง (ตอนนี้ค่าทุก select ว่าง → แสดงทั้งหมด)
                applyFilters();
            });

        })();
    </script>

    <script>
        const chartsByStandard = @json($chartsByStandard, JSON_UNESCAPED_UNICODE);

        Object.values(chartsByStandard).forEach(bucket => {
            (bucket.indicators || []).forEach(c => {
                const cid = 'chart-' + bucket.standard_id + '-' + c.indicator_id;
                const el = document.getElementById(cid);
                if (!el) return;

                new Chart(el.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: c.years,
                        datasets: [{
                            data: c.values,
                            borderWidth: 1,
                            borderRadius: 12
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: (tt) =>
                                        ` ${Number(tt.raw ?? 0).toLocaleString('th-TH', { maximumFractionDigits: 2 })}`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true /*, suggestedMax: 100*/
                            }
                        }
                    }
                });
            });
        });
    </script>


    <style>
        :root {
            --blue-50: #eff6ff;
            --blue-100: #dbeafe;
            --blue-500: #3b82f6;
            --blue-600: #2563eb;
            --green-500: #22c55e;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }

        .resul-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
        }

        .resul-header {
            margin-bottom: 32px;
        }

        .resul-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0 0 8px 0;
        }

        .subtitle {
            color: var(--gray-600);
            font-size: 16px;
            margin: 0;
        }




        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            justify-content: flex-start;
        }

        /* กล่องรวมกราฟของแต่ละมาตรฐาน */
        .charts-of-standard {
            display: flex;
            flex-wrap: wrap;
            /* ให้ขึ้นบรรทัดใหม่อัตโนมัติ */
            gap: 16px;
            align-items: stretch;
        }

        /* การ์ดกราฟ: คงขนาดพอดีมือ */
        .chart-card {
            flex: 0 0 320px;
            /* ความกว้างการ์ดคงที่ 320px */
            background: #fff;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
        }

        /* แคนวาสไม่ขยาย */
        .chart-card canvas {
            width: 100% !important;
            height: 180px !important;
        }

        /* มือถือให้ยืดเต็มแถว */
        @media (max-width: 480px) {
            .chart-card {
                flex: 1 1 100%;
            }
        }


        .chart-content {
            position: relative;
            height: 420px;
        }




        /* Responsive */
        @media (max-width: 768px) {
            .resul-container {
                padding: 16px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-card {
                padding: 16px;
            }

            .satisfaction-chart {
                flex-direction: column;
                gap: 16px;
            }

            .stat-footer {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .resul-header h1 {
                font-size: 24px;
            }

            .stat-number {
                font-size: 28px;
            }

            .satisfaction-score {
                font-size: 36px;
            }
        }
    </style>

    <style>
        /* ---- Base ---- */
        :root {
            --blue: #398ECA;
            --blue-600: #2f7db2;
            --text: #1f2937;
            /* gray-800 */
            --muted: #6b7280;
            /* gray-500 */
            --border: #e5e7eb;
            /* gray-200 */
            --bg: #ffffff;
            --shadow: 0 8px 24px rgba(0, 0, 0, .08);
            --radius: 16px;
            --radius-sm: 10px;
        }

        .card {
            background: var(--bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 24px;
            /* max-width: 920px; */
            /* ปรับตามหน้า */
            margin: 16px auto;
            border: 1px solid #f3f4f6;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--blue);
            margin: 0 0 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
            padding-left: 10px;
        }

        .card-title::before {
            content: "";
            width: 4px;
            height: 20px;
            border-radius: 8px;
            background: var(--blue);
            position: absolute;
            left: 0;
            top: 2px;
            opacity: .25;
        }

        /* ---- Grid ---- */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px 16px;
        }

        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        /* ---- Fields ---- */
        .field {
            margin-bottom: 16px;
        }

        .field label {
            display: block;
            font-size: 13px;
            color: var(--text);
            margin-bottom: 6px;
            font-weight: 600;
        }

        .field select {
            width: 100%;
            height: 40px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 0 36px 0 12px;
            /* padding ขวาเยอะขึ้น กันทับลูกศร */
            font-size: 14px;
            color: var(--text);
            background: #fff;
            outline: none;
            transition: box-shadow .2s, border-color .2s;
            appearance: none;
            cursor: pointer;

            /* ลูกศร custom */
            background-image:
                linear-gradient(45deg, transparent 50%, var(--muted) 50%),
                linear-gradient(135deg, var(--muted) 50%, transparent 50%);
            background-position:
                calc(100% - 18px) 16px,
                calc(100% - 12px) 16px;
            background-size: 6px 6px, 6px 6px;
            background-repeat: no-repeat;
        }

        .field select:hover {
            border-color: var(--blue);


        }

        .field select:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(80, 162, 221, 0.15);
        }

        .field select:disabled {
            background: #f9fafb;
            color: #9ca3af;
            cursor: not-allowed;
        }



        /* ---- Actions ---- */
        .card-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 18px;
        }

        .btn {
            height: 40px;
            padding: 0 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            transition: transform .05s ease, background .2s, border-color .2s, color .2s;
        }

        .btn:active {
            transform: translateY(1px);
        }

        .btn-outline {
            background: #fff;
            color: var(--blue);
            border-color: var(--blue);
        }

        .btn-outline:hover {
            background: #f0f7fc;
        }

        .btn-primary {
            background: var(--blue);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--blue-600);
        }
    </style>


@endsection
