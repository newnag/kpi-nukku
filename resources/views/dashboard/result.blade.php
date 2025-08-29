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

        {{-- กราฟ --}}
        <div class="charts-grid">
            @foreach ($standards as $standard)
                <div class="stat-title" style="margin:12px 0 8px;">
                    <h3>กราฟผลลัพธ์ {{ $standard->name }}</h3>
                </div>

                @php $bucket = $chartsByStandard[$standard->id] ?? null; @endphp

                @if ($bucket && !empty($bucket['indicators']))
                    <div class="charts-of-standard" data-standard-id="{{ $standard->id }}"
                        style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,2fr));gap:20px;">

                        @foreach ($bucket['indicators'] as $i => $c)
                            <div class="chart-card" id="card-{{ $c['indicator_id'] }}" data-standard="{{ $standard->id }}"
                                data-dimension="{{ $c['category_name'] }}" data-type="{{ $c['indicator_type'] }}"
                                data-code="{{ $c['indicator_code'] }}" data-years='@json($c['years'])'
                                data-index="{{ $i }}"
                                style="{{ $i >= 5 ? 'display:none;' : '' }};background:#fff;border-radius:16px;
                                padding:16px;box-shadow:0 2px 10px rgba(0,0,0,.06);position:relative;">

                                {{-- หัวข้อ --}}
                                <div class="subtitle" style="font-weight:600;margin-bottom:8px;">
                                    {{ $c['indicator_code'] ? '[' . $c['indicator_code'] . '] ' : '' }}
                                    {{ $c['indicator_name'] }}
                                </div>

                                {{-- กราฟ --}}
                                <canvas id="chart-{{ $standard->id }}-{{ $c['indicator_id'] }}" width="400"
                                    height="200"></canvas>
                                <script id="data-{{ $standard->id }}-{{ $c['indicator_id'] }}" type="application/json">
                                    @json(['years' => $c['years'], 'values' => $c['values']], JSON_UNESCAPED_UNICODE)
                                </script>

                                {{-- ปุ่มดาวน์โหลด --}}
                                <button data-html2canvas-ignore="true"  type="button" class="btn-download" data-target="card-{{ $c['indicator_id'] }}"
                                    style="position:absolute;top:8px;right:8px;
                                       background:#10B981;color:#fff;border:none;
                                       padding:4px 8px;border-radius:6px;cursor:pointer;
                                       font-size:12px;">
                                    ดาวน์โหลด
                                </button>
                            </div>
                        @endforeach
                    </div>

                    @if (count($bucket['indicators']) > 10)
                        <div class="divider-btn" data-standard-id="{{ $standard->id }}">
                            <span class="divider-line"></span>
                            <button type="button" class="btn-show-more" data-standard-id="{{ $standard->id }}">
                                แสดงเพิ่มเติม ▼
                            </button>
                            <span class="divider-line"></span>
                        </div>
                    @endif
                @else
                    <div style="color:#6b7280;margin-bottom:16px;">ไม่มีข้อมูลตัวชี้วัดที่มีการบันทึกผลลัพธ์</div>
                @endif
            @endforeach
        </div>

    </div>

    <!-- Chart.js -->
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.469.0/dist/umd/lucide.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- ✅ โหลด html2canvas ที่นี่ -->
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

    <!-- แล้วค่อยตามด้วยสคริปต์ของคุณ -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll('.btn-download').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const targetId = this.dataset.target;
                    const cardEl = document.getElementById(targetId);
                    if (!cardEl) return;

                    try {
                        const canvas = await html2canvas(cardEl, {
                            scale: 3,
                            backgroundColor: "#ffffff"
                        });
                        const dataUrl = canvas.toDataURL('image/png');

                        const link = document.createElement('a');
                        link.href = dataUrl;
                        link.download = targetId + '.png';
                        link.click();
                    } catch (err) {
                        console.error("html2canvas error:", err);
                    }
                });
            });
        });
    </script>

    <script>
        (function() {
            // ===== ปลั๊กอินพื้นหลังสีขาว =====
            const whiteBgPlugin = {
                id: 'whiteBackground',
                beforeDraw(chart, args, opts) {
                    const {
                        ctx,
                        width,
                        height
                    } = chart;
                    ctx.save();
                    ctx.globalCompositeOperation = 'destination-over';
                    ctx.fillStyle = (opts && opts.color) || '#ffffff';
                    ctx.fillRect(0, 0, width, height);
                    ctx.restore();
                }
            };
            if (window.Chart && !Chart.registry.plugins.get('whiteBackground')) {
                Chart.register(whiteBgPlugin);
            }

            // ===== ปลั๊กอินแสดงคะแนนบนแท่ง =====
            const valueLabelsPlugin = {
                id: 'valueLabels',
                afterDatasetsDraw(chart, args, opts) {
                    const {
                        ctx,
                        data
                    } = chart;
                    const ds = chart.getDatasetMeta(0);
                    if (!ds || !ds.data) return;

                    const fmt = (n) => Number(n ?? 0).toLocaleString('th-TH', {
                        maximumFractionDigits: 2
                    });
                    const color = opts?.color || '#111';
                    const fontSize = opts?.fontSize || 12;
                    const yOffset = opts?.yOffset ?? 4;

                    ctx.save();
                    ctx.font = `600 ${fontSize}px sans-serif`;
                    ctx.fillStyle = color;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'bottom';

                    ds.data.forEach((elem, i) => {
                        const value = data.datasets[0].data[i];
                        if (!value) return; // ข้ามถ้าไม่มีค่า
                        const {
                            x,
                            y
                        } = elem.tooltipPosition();
                        const ty = y - yOffset;
                        ctx.fillText(fmt(value), x, ty);
                    });

                    ctx.restore();
                }
            };
            if (!Chart.registry.plugins.get('valueLabels')) {
                Chart.register(valueLabelsPlugin);
            }

            const FILTERS = @json($filters, JSON_UNESCAPED_UNICODE);
            const $year = document.getElementById('filter-year');
            const $code = document.getElementById('filter-code');
            const $std = document.getElementById('filter-standard');
            const $dim = document.getElementById('filter-dimension');
            const $type = document.getElementById('filter-type');

            function fillSelect(sel, items, mapper) {
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

            function sortCodes(codes) {
                return (codes || []).slice().sort((a, b) => {
                    const ma = String(a).match(/^([A-Za-z]+)[- ]?(\d+)$/i);
                    const mb = String(b).match(/^([A-Za-z]+)[- ]?(\d+)$/i);
                    const prefixA = ma ? ma[1].toUpperCase() : String(a);
                    const prefixB = mb ? mb[1].toUpperCase() : String(b);
                    const numA = ma ? parseInt(ma[2], 10) : 0;
                    const numB = mb ? parseInt(mb[2], 10) : 0;
                    if (prefixA === prefixB) return numA - numB;
                    return prefixA.localeCompare(prefixB, 'th');
                });
            }
            fillSelect($year, (FILTERS.years || []).slice().sort());
            fillSelect($code, sortCodes(FILTERS.codes));
            fillSelect(
                $std,
                (FILTERS.standards || []).slice().sort((a, b) => a.name.localeCompare(b.name, 'th')),
                it => ({
                    value: String(it.id),
                    label: it.name
                })
            );
            fillSelect($dim, (FILTERS.dimensions || []).slice().sort());
            fillSelect($type, (FILTERS.types || []).slice().sort());

            const chartInstances = {};
            const chartOriginals = {};

            function initChartsFromInlineJSON() {
                const COLOR_BY_YEAR = {
                    '2020': '#8B5CF6',
                    '2021': '#FCA5A5',
                    '2022': '#60A5FA',
                    '2023': '#34D399',
                    '2024': '#10B981',
                    '2025': '#EF4444',
                };
                const getColorByYear = (y) => COLOR_BY_YEAR[String(y)] || '#9CA3AF';

                document.querySelectorAll('canvas[id^="chart-"]').forEach(cv => {
                    const canvasId = cv.id;
                    const key = canvasId.replace(/^chart-/, '');
                    const dataEl = document.getElementById('data-' + key);
                    if (!dataEl) return;

                    let payload = {
                        years: [],
                        values: []
                    };
                    try {
                        payload = JSON.parse(dataEl.textContent || '{}');
                    } catch {}

                    const years = (payload.years || []).map(y => String(y));
                    const values = (payload.values || []).map(v => Number(v));
                    chartOriginals[canvasId] = {
                        years,
                        values
                    };

                    chartInstances[canvasId] = new Chart(cv.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: years.slice(),
                            datasets: [{
                                data: values.slice(),
                                borderWidth: 1,
                                borderRadius: 12,
                                backgroundColor: years.map(y => getColorByYear(y))
                            }]
                        },
                        options: {
                            maintainAspectRatio: false, // ✅ ยืดตาม container
                            devicePixelRatio: window.devicePixelRatio || 1, // ✅ คมชัดตามจอ
                            aspectRatio: 2, // ถ้าอยาก fix อัตราส่วน
                            animation: false, // ✅ render ทันที
                            elements: {
                                bar: {
                                    borderSkipped: false
                                } // ✅ ทำให้ขอบแท่งคมขึ้น
                            },
                            layout: {
                                padding: {
                                    top: 40, // ขยายด้านบน (ค่าเดิม 0–20 ลองเพิ่มเป็น 40)
                                    // right: 9,
                                    // bottom: 9,
                                    // left: 9
                                }
                            },
                            plugins: {
                                whiteBackground: {
                                    color: '#ffffff'
                                },
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: (tt) =>
                                            ` ${Number(tt.raw ?? 0).toLocaleString('th-TH', { maximumFractionDigits: 2 })}`
                                    }
                                },
                                valueLabels: { // ✅ เปิด plugin แสดงคะแนน
                                    color: '#111',
                                    fontSize: 12,
                                    yOffset: 4
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        color: '#111'
                                    },
                                    grid: {
                                        color: 'rgba(0,0,0,0.06)'
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: '#111'
                                    },
                                    grid: {
                                        color: 'rgba(0,0,0,0.06)'
                                    }
                                }
                            }
                        }
                    });

                });
            }

            function bindShowMore() {
                document.querySelectorAll('.btn-show-more').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const sid = this.dataset.standardId;
                        const container = document.querySelector(
                            `.charts-of-standard[data-standard-id="${sid}"]`);
                        if (!container) return;
                        const cards = container.querySelectorAll('.chart-card[data-index]');
                        const isExpanded = this.classList.contains('expanded');
                        if (!isExpanded) {
                            cards.forEach(c => c.style.display = '');
                            this.textContent = 'แสดงน้อยลง';
                            this.classList.add('expanded');
                        } else {
                            cards.forEach(c => {
                                const idx = parseInt(c.dataset.index, 5);
                                c.style.display = idx < 5 ? '' : 'none';
                            });
                            this.textContent = 'แสดงเพิ่มเติม';
                            this.classList.remove('expanded');
                        }
                    });
                });
            }

            function applyFilters() {
                const vYear = String($year.value || '');
                const vCode = $code.value,
                    vStd = $std.value,
                    vDim = $dim.value,
                    vType = $type.value;

                document.querySelectorAll('.chart-card').forEach(card => {
                    let show = true;
                    if (vStd && card.dataset.standard !== vStd) show = false;
                    if (vDim && (card.dataset.dimension || '') !== vDim) show = false;
                    if (vType && (card.dataset.type || '') !== vType) show = false;
                    if (vCode && (card.dataset.code || '') !== vCode) show = false;
                    if (vYear) {
                        try {
                            const raw = JSON.parse(card.getAttribute('data-years') || '[]');
                            const years = (raw || []).map(y => String(y));
                            if (!years.includes(vYear)) show = false;
                        } catch {}
                    }
                    card.style.display = show ? '' : 'none';
                });
                document.querySelectorAll('.charts-of-standard').forEach(group => {
                    const anyVisible = !![...group.querySelectorAll('.chart-card')].find(c => c.style
                        .display !== 'none');
                    const title = group.previousElementSibling;
                    group.style.display = anyVisible ? '' : 'none';
                    if (title && title.classList.contains('stat-title')) title.style.display = anyVisible ? '' :
                        'none';
                });

                Object.entries(chartInstances).forEach(([canvasId, inst]) => {
                    const orig = chartOriginals[canvasId];
                    if (!orig) return;
                    if (vYear) {
                        const idxs = [];
                        orig.years.forEach((y, i) => {
                            if (String(y) === vYear) idxs.push(i);
                        });
                        inst.data.labels = idxs.map(i => orig.years[i]);
                        inst.data.datasets[0].data = idxs.map(i => orig.values[i]);
                    } else {
                        inst.data.labels = orig.years.slice();
                        inst.data.datasets[0].data = orig.values.slice();
                    }
                    inst.update();
                });
            }

            function resetFilters() {
                [$year, $code, $std, $dim, $type].forEach(sel => {
                    if (sel) sel.selectedIndex = 0;
                });
                document.querySelectorAll('.chart-card').forEach(c => c.style.display = '');
                document.querySelectorAll('.charts-of-standard,.stat-title').forEach(el => el.style.display = '');
                Object.entries(chartInstances).forEach(([canvasId, inst]) => {
                    const orig = chartOriginals[canvasId] || {
                        years: [],
                        values: []
                    };
                    inst.data.labels = orig.years.slice();
                    inst.data.datasets[0].data = orig.values.slice();
                    inst.update();
                });
                document.querySelectorAll('.btn-show-more.expanded').forEach(btn => {
                    btn.classList.remove('expanded');
                    btn.textContent = 'แสดงเพิ่มเติม';
                });
            }

            document.getElementById('apply-filters').addEventListener('click', applyFilters);
            document.getElementById('reset-filters').addEventListener('click', () => {
                resetFilters();
                applyFilters();
            });

            // init
            initChartsFromInlineJSON();
            bindShowMore();
        })();
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

        .chart-card canvas {
            width: 100% !important;
            /* ยืดเต็มการ์ด */
            height: auto !important;
            min-height: 200px;
            /* กันไม่ให้ collapse */
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

        .divider-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 16px 0;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background-color: #3b82f6;
            /* สีฟ้า */
            margin: 0 8px;
        }

        .btn-show-more {
            background: none;
            border: none;
            color: #3b82f6;
            /* ฟ้า */
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            padding: 0 4px;
            transition: color 0.2s;
        }

        .btn-show-more:hover {
            color: #2563eb;
            /* ฟ้าเข้ม */
        }
    </style>


@endsection
