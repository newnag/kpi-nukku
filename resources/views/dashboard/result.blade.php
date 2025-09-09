@extends('layouts.app')
@section('title', 'กราฟผลลัพธ์')

@section('header')
    กราฟผลลัพธ์การประเมิน
@endsection

@section('subheader')
    ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล
@endsection

@section('content')

    <div class="chart-card summary-card">
        <h2 class="card-title">คะแนนรวมตามปี</h2>

        <!-- ✅ Checkbox เลือกปี -->
        <div id="year-filters" style="margin-bottom:10px;">
            @foreach ($filters['years'] as $y)
                <label style="margin-right:10px;">
                    <input type="checkbox" class="year-checkbox" value="{{ $y }}" checked>
                    {{ $y }}
                </label>
            @endforeach
        </div>

        <!-- เปลี่ยนจาก canvas เป็น div สำหรับ ApexCharts -->
        <div id="scoreLineChart"></div>
    </div>


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
                        <div class="chart-card enhanced-chart-card" id="card-{{ $c['indicator_id'] }}"
                            data-standard="{{ $standard->id }}" data-dimension="{{ $c['category_name'] }}"
                            data-type="{{ $c['indicator_type'] }}" data-code="{{ $c['indicator_code'] }}"
                            data-years='@json($c['years'])' data-index="{{ $i }}"
                            style="{{ $i >= 5 ? 'display:none;' : '' }};background:#fff;border-radius:16px;
                                padding:20px;box-shadow:0 4px 20px rgba(0,0,0,.08);position:relative;
                                border: 1px solid rgba(0,0,0,0.05);">

                            {{-- หัวข้อ --}}
                            <div class="chart-header"
                                style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                                <div class="chart-title" style="font-weight:700;color:#1f2937;font-size:14px;">
                                    {{ $c['indicator_code'] ? '[' . $c['indicator_code'] . '] ' : '' }}
                                    {{ $c['indicator_name'] }}
                                </div>

                                <button data-html2canvas-ignore="true" type="button" class="btn-download"
                                    data-target="card-{{ $c['indicator_id'] }}"
                                    style="background:#fff;border:1px solid #ddd;
           padding:6px;border-radius:8px;cursor:pointer;
           line-height:1;display:flex;align-items:center;
           justify-content:center;transition:all .2s ease;">
                                    <!-- SVG icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
                                    </svg>
                                </button>

                            </div>

                            {{-- กราฟ --}}
                            <div class="chart-wrapper" style="position:relative;height:220px;margin-bottom:10px;">
                                <div id="chart-{{ $standard->id }}-{{ $c['indicator_id'] }}" style="height:220px;"></div>
                            </div>
                            <script id="data-{{ $standard->id }}-{{ $c['indicator_id'] }}" type="application/json">
                                {!! json_encode(
                                    [
                                        'years' => $c['years'],
                                        'values' => $c['values'],
                                        'max_values' => $c['max_values'],
                                    ],
                                    JSON_UNESCAPED_UNICODE,
                                ) !!}
                            </script>

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

    <!-- Chart.js -->
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.469.0/dist/umd/lucide.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- ✅ โหลด html2canvas ที่นี่ -->
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <!-- โหลด ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        (function() {
            const yearlyTotals = @json($yearlyTotals);
            // yearlyTotals = [{year:2025, score:735, max:740}, ...]

            // ✅ สร้าง options ของ chart
            function makeOptions(data) {
                const maxVal = data.length ? Math.max(...data.map(r => r.max)) : 0;

                return {
                    series: [{
                            name: "คะแนนที่ได้",
                            data: data.map(r => r.score)
                        },
                        {
                            name: "คะแนนเต็ม",
                            data: data.map(r => r.max)
                        }
                    ],
                    chart: {
                        height: 350,
                        type: 'area',
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: false
                        }
                    },
                    colors: ['#4f46e5', '#94a3b8'],
                    dataLabels: {
                        enabled: true,
                        background: {
                            enabled: true,
                            foreColor: '#fff',
                            borderRadius: 4,
                            padding: 4,
                            opacity: 0.9
                        },
                        formatter: function(val, opts) {
                            if (opts.seriesIndex === 0) {
                                const row = data[opts.dataPointIndex];
                                const percent = row.max ? ((row.score / row.max) * 100).toFixed(1) : 0;
                                return `${val} (${percent}%)`;
                            }
                            return "";
                        },
                        offsetY: function(val, opts) {
                            const isMax = val === maxVal;
                            return isMax ? -25 : -10; // ✅ auto shift ถ้าชนขอบ
                        }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    markers: {
                        size: 5,
                        colors: ['#fff'],
                        strokeColors: ['#4f46e5', '#94a3b8'],
                        strokeWidth: 2,
                        hover: {
                            size: 7
                        }
                    },
                    xaxis: {
                        categories: data.map(r => r.year),
                        title: {
                            text: 'ปีการประเมิน',
                            style: {
                                fontSize: '13px',
                                fontWeight: '600',
                                color: '#374151'
                            }
                        }
                    },
                    yaxis: {
                        min: 0,
                        max: maxVal ? maxVal * 1.15 : 100, // ✅ space เผื่อ datalabel
                        title: {
                            text: 'คะแนน',
                            style: {
                                fontSize: '13px',
                                fontWeight: '600',
                                color: '#374151'
                            }
                        }
                    },
                    grid: {
                        padding: {
                            top: 40,
                            right: 30,
                            bottom: 10,
                            left: 20
                        } // ✅ กันไม่ให้ชนขอบ
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right'
                    }
                };
            }

            // ✅ ฟังก์ชันดึงข้อมูลตามปีที่เลือก
            function getSelectedData() {
                const selectedYears = Array.from(document.querySelectorAll('.year-checkbox:checked'))
                    .map(c => parseInt(c.value));
                return yearlyTotals.filter(r => selectedYears.includes(r.year));
            }

            // ✅ Render chart ครั้งแรก
            let initData = getSelectedData();
            let chart = new ApexCharts(document.querySelector("#scoreLineChart"), makeOptions(initData));
            chart.render();

            // ✅ Update chart เมื่อเปลี่ยน checkbox
            document.querySelectorAll('.year-checkbox').forEach(chk => {
                chk.addEventListener('change', () => {
                    const filtered = getSelectedData();
                    chart.updateOptions(makeOptions(filtered), true, true);
                });
            });
        })();
    </script>


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
            const FILTERS = @json($filters, JSON_UNESCAPED_UNICODE);
            const $year = document.getElementById('filter-year');
            const $code = document.getElementById('filter-code');
            const $std = document.getElementById('filter-standard');
            const $dim = document.getElementById('filter-dimension');
            const $type = document.getElementById('filter-type');

            // ==== Helper สำหรับ select ====
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
            fillSelect($std, (FILTERS.standards || []).slice().sort((a, b) => a.name.localeCompare(b.name, 'th')), it =>
                ({
                    value: String(it.id),
                    label: it.name
                }));
            fillSelect($dim, (FILTERS.dimensions || []).slice().sort());
            fillSelect($type, (FILTERS.types || []).slice().sort());

            // ==== เก็บ instance ของ ApexCharts ====
            const chartInstances = {};
            const chartOriginals = {};

            // ==== init ApexCharts ====
            function initChartsFromInlineJSON() {
                document.querySelectorAll('div[id^="chart-"]').forEach(container => {
                    const key = container.id.replace(/^chart-/, '');
                    const dataEl = document.getElementById('data-' + key);
                    if (!dataEl) return;

                    let payload = {
                        years: [],
                        values: [],
                        max_values: []
                    };
                    try {
                        payload = JSON.parse(dataEl.textContent || '{}');
                    } catch {}

                    const years = (payload.years || []).map(y => String(y));
                    const values = (payload.values || []).map(v => Number(v));
                    const maxValues = (payload.max_values || []).map(v => Number(v));

                    // 🔥 เก็บข้อมูลต้นฉบับ
                    chartOriginals[container.id] = {
                        years,
                        values,
                        maxValues
                    };

                    const options = {
                        series: [{
                                name: "คะแนนที่ได้",
                                data: years.map((x, i) => ({
                                    x,
                                    y: values[i] ?? null
                                }))
                            },
                            {
                                name: "คะแนนเต็ม",
                                data: years.map((x, i) => ({
                                    x,
                                    y: maxValues[i] ?? null
                                }))
                            }
                        ],
                        chart: {
                            type: 'area',
                            height: 220,
                            toolbar: {
                                show: false
                            }
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 6,
                                columnWidth: '40%'
                            }
                        },
                        dataLabels: {
                            enabled: true
                        },
                        xaxis: {
                            categories: years
                        },
                        yaxis: {
                            title: {
                                text: 'คะแนน'
                            }
                        }
                    };

                    const chart = new ApexCharts(container, options);
                    chart.render();

                    // 🔥 เก็บ instance ของ chart
                    chartInstances[container.id] = chart;
                });
            }


            // ==== ฟังก์ชัน Show More ====
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
                                const idx = parseInt(c.dataset.index, 10);
                                c.style.display = idx < 5 ? '' : 'none';
                            });
                            this.textContent = 'แสดงเพิ่มเติม';
                            this.classList.remove('expanded');
                        }
                    });
                });
            }

            // ==== ฟิลเตอร์ ====
            async function applyFilters() {
                const vYear = String($year.value || '');
                const vCode = $code.value,
                    vStd = $std.value,
                    vDim = $dim.value,
                    vType = $type.value;

                document.querySelectorAll('.chart-card').forEach(card => {
                    if (card.classList.contains('summary-card')) return;
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

                Object.entries(chartInstances).forEach(([id, chart]) => {
                    const orig = chartOriginals[id];
                    if (!orig) return;

                    let newYears = orig.years.slice();
                    let newValues = orig.values.slice();
                    let newMax = orig.maxValues.slice();
                    if (vYear) {
                        const idxs = orig.years.map((y, i) => ({
                            y,
                            i
                        })).filter(o => o.y === vYear).map(o => o.i);
                        newYears = idxs.map(i => orig.years[i]);
                        newValues = idxs.map(i => orig.values[i]);
                        newMax = idxs.map(i => orig.maxValues[i]);
                    }

                    chart.updateOptions({
                        xaxis: {
                            categories: newYears
                        },
                        series: [{
                                name: 'คะแนนที่ได้',
                                data: newYears.map((x, i) => ({
                                    x,
                                    y: newValues[i]
                                }))
                            },
                            {
                                name: 'คะแนนเต็ม',
                                data: newYears.map((x, i) => ({
                                    x,
                                    y: newMax[i]
                                }))
                            }
                        ]
                    }, false, true);
                });
            }

            function resetFilters() {
                [$year, $code, $std, $dim, $type].forEach(sel => {
                    if (sel) sel.selectedIndex = 0;
                });
                document.querySelectorAll('.chart-card').forEach(c => c.style.display = '');
                Object.entries(chartInstances).forEach(([id, chart]) => {
                    const orig = chartOriginals[id];
                    chart.updateOptions({
                        xaxis: {
                            categories: orig.years
                        },
                        series: [{
                                name: 'คะแนนที่ได้',
                                data: orig.years.map((x, i) => ({
                                    x,
                                    y: orig.values[i]
                                }))
                            },
                            {
                                name: 'คะแนนเต็ม',
                                data: orig.years.map((x, i) => ({
                                    x,
                                    y: orig.maxValues[i]
                                }))
                            }
                        ]
                    }, false, true);
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

            // ==== init ====
            initChartsFromInlineJSON();
            bindShowMore();
        })();
    </script>

    {{-- <script>
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
                    const color = opts?.color || '#374151';
                    const fontSize = opts?.fontSize || 12;
                    const yOffset = opts?.yOffset ?? 6;

                    ctx.save();
                    ctx.font =
                        `700 ${fontSize}px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif`;
                    ctx.fillStyle = color;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'bottom';

                    ds.data.forEach((elem, i) => {
                        const value = data.datasets[0].data[i];
                        if (!value) return;
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
                const COLOR_PALETTE = [
                    '#667eea', '#764ba2', '#f093fb', '#f5576c',
                    '#4facfe', '#00f2fe', '#43e97b', '#38f9d7',
                    '#ffecd2', '#fcb69f', '#a8edea', '#fed6e3'
                ];

                const YEAR_GRADIENTS = {
                    '2020': ['#667eea', '#764ba2'],
                    '2021': ['#f093fb', '#f5576c'],
                    '2022': ['#4facfe', '#00f2fe'],
                    '2023': ['#43e97b', '#38f9d7'],
                    '2024': ['#ffecd2', '#fcb69f'],
                    '2025': ['#a8edea', '#fed6e3'],
                };

                const getGradient = (ctx, year) => {
                    const colors = YEAR_GRADIENTS[String(year)] || ['#6b7280', '#9ca3af'];
                    const gradient = ctx.createLinearGradient(0, 0, 0, 200);
                    gradient.addColorStop(0, colors[0]);
                    gradient.addColorStop(1, colors[1]);
                    return gradient;
                };

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
                    const maxValues = (payload.max_values || []).map(v => Number(v));
                    chartOriginals[canvasId] = {
                        years,
                        values,
                        maxValues 
                    };

                    const ctx = cv.getContext('2d');
                    const backgroundColors = years.map(year => getGradient(ctx, year));

                    chartInstances[canvasId] = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: years.slice(),
                            datasets: [{
                                    label: 'คะแนนที่ได้',
                                    data: values.slice(),
                                    backgroundColor: backgroundColors,
                                    borderRadius: {
                                        topLeft: 12,
                                        topRight: 12
                                    },
                                    borderSkipped: false,
                                },
                                {
                                    label: 'คะแนนเต็ม',
                                    data: maxValues.slice(),
                                    backgroundColor: 'rgba(156,163,175,0.3)', // สีเทาโปร่ง ๆ
                                    borderRadius: {
                                        topLeft: 12,
                                        topRight: 12
                                    },
                                    borderSkipped: false,
                                }
                            ]
                        },
                        options: {
                            plugins: {
                                legend: {
                                    display: true
                                },
                                tooltip: {
                                    callbacks: {
                                        label: (context) => {
                                            if (context.dataset.label === 'คะแนนที่ได้') {
                                                return `คะแนน: ${context.raw}`;
                                            } else {
                                                return `เต็ม: ${context.raw}`;
                                            }
                                        }
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
                    // ✅ ข้าม summary card
                    if (card.classList.contains('summary-card')) return;

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
                    const anyVisible = !![...group.querySelectorAll('.chart-card')].find(
                        c => c.style.display !== 'none'
                    );
                    const title = group.previousElementSibling;
                    group.style.display = anyVisible ? '' : 'none';
                    if (title && title.classList.contains('stat-title'))
                        title.style.display = anyVisible ? '' : 'none';
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
    </script> --}}
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
