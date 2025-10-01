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
        <div id="year-filters" style="margin-bottom:10px;margin-left: 20px;">
            @foreach ($filters['years'] as $y)
                <label style="margin-right:10px;">
                    <input type="checkbox" class="year-checkbox" value="{{ $y }}" checked>
                    {{ $y }}
                </label>
            @endforeach
        </div>

        <!-- เปลี่ยนจาก canvas เป็น div สำหรับ ApexCharts -->
        <div class="chart-card">
            <div id="scoreLineChart"></div>
        </div>



    </div>
    <div class="chart-card summary-card">
        <h2 class="card-title">คะแนนรวมมาตรฐานตามปี</h2>

        <!-- ✅ Checkbox ปี -->
        <div id="year-filters-standard" style="margin-bottom:10px;margin-left: 20px;">
            @foreach ($filters['years'] as $y)
                <label style="margin-right:10px;">
                    <input type="checkbox" class="year-checkbox-std" value="{{ $y }}" checked>
                    {{ $y }}
                </label>
            @endforeach
        </div>

        <!-- ✅ Grid 3 คอลัมน์ -->
        <div class="charts-of-standards">
            @foreach ($chartsStandardBars as $chart)
                <div class="chart-card standard-card">
                    <h3 style="margin-bottom:10px;">{{ $chart['name'] }}</h3>
                    <div id="stdChart-{{ $chart['id'] }}" style="height:300px;"></div>

                    <script type="application/json" id="stdData-{{ $chart['id'] }}">
                {!! json_encode([
                    'labels' => $chart['labels'],
                    'scores' => $chart['scores'],
                    'max'    => $chart['max'],
                ], JSON_UNESCAPED_UNICODE) !!}
            </script>
                </div>
            @endforeach
        </div>

    </div>

    <div class="chart-card summary-card">
        <h2 class="card-title">คะแนนรวมตามด้าน</h2>

        <!-- ✅ Checkbox ปี -->
        <div id="year-filters-dim" style="margin-bottom:10px;margin-left: 20px;">
            @foreach ($filters['years'] as $y)
                <label style="margin-right:10px;">
                    <input type="checkbox" class="year-checkbox-dim" value="{{ $y }}" checked>
                    {{ $y }}
                </label>
            @endforeach
        </div>

        <div class="charts-of-dimensions">
            @foreach ($chartDimensions as $chart)
                <div class="chart-card dim-card">
                    <h3 style="margin-bottom:10px;">{{ $chart['name'] }}</h3>
                    <div id="dimChart-{{ $chart['id'] }}" style="height:300px;"></div>

                    <script type="application/json" id="dimData-{{ $chart['id'] }}">
                    {!! json_encode([
                        'labels' => $chart['labels'],
                        'scores' => $chart['scores'],
                        'max'    => $chart['max'],
                    ], JSON_UNESCAPED_UNICODE) !!}
                </script>
                </div>
            @endforeach
        </div>
    </div>


    <!-- Toggle Switch -->
    <div style="align-items:center;gap:8px;margin-bottom:12px;text-align: right;margin-top: 20px;">
        <label class="switch">
            <input type="checkbox" id="toggle-filter">
            <span class="slider round"></span>
        </label>
        <span>กรองข้อมูล</span>
    </div>

    <!-- Filter Component -->
    <x-filter 
        :years="$filters['years'] ?? []" 
        :standards="$filters['standards'] ?? []" 
        :departments="[]" 
        :collectors="[]"
        :dimensions="$filters['dimensions'] ?? []"
        :filters="$filters"
        :action="route('dashboard.getData')"
        title="กรองข้อมูลการประเมิน"
        filterId="filter-panel"
        formId="result-filter-form"
                :showFields="[
            'year' => true,
            'codes' => true,
            'standard' => true,
            'dimension' => true,
            'department' => false,
            'collector' => false,
            'type' => true
        ]"
    >
        <script>
            // Custom JavaScript สำหรับหน้า result
            document.addEventListener('DOMContentLoaded', function() {
                // รอให้ FilterComponent โหลดเสร็จ
                setTimeout(function() {
                    if (typeof window.FilterComponent !== 'undefined') {
                        window.FilterComponent.init({
                            filterId: 'filter-panel',
                            formId: 'result-filter-form',
                            onApply: function() {
                                console.log('Filter applied in result page');
                                // เรียกใช้ฟังก์ชัน applyFilters ของหน้า result
                                if (typeof window.applyResultFilters === 'function') {
                                    window.applyResultFilters();
                                }
                            },
                            onReset: function() {
                                console.log('Filter reset in result page');
                                // เรียกใช้ฟังก์ชัน resetFilters ของหน้า result
                                if (typeof window.resetResultFilters === 'function') {
                                    window.resetResultFilters();
                                }
                            }
                        });
                    }
                }, 100);
            });
        </script>
    </x-filter>

    <div class="chart-card ">

        <div class="search-box flex-1 max-w-[420px]">

            <div class="icon">
                <!-- search icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" style="color:#9ca3af;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" id="custom-search" class="search-input" placeholder="ค้นหารายการตัวบ่งชี้">
        </div>

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
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
                                        </svg>
                                    </button>

                                </div>

                                {{-- กราฟ --}}
                                <div class="chart-wrapper" style="position:relative;height:220px;margin-bottom:10px;">
                                    <div id="chart-{{ $standard->id }}-{{ $c['indicator_id'] }}" style="height:220px;">
                                    </div>
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
                    <div style="color:#6b7280;margin-bottom:16px;">ไม่มีข้อมูลตัวบ่งชี้ที่มีการบันทึกผลลัพธ์</div>
                @endif
            @endforeach
        </div>
    </div>
    {{-- กราฟ --}}


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
        document.addEventListener("DOMContentLoaded", () => {
            const charts = {};
            const originals = {};

            document.querySelectorAll('[id^="stdData-"]').forEach(el => {
                const id = el.id.replace('stdData-', '');
                const payload = JSON.parse(el.textContent);

                originals[id] = payload;

                const options = {
                    chart: {
                        type: 'bar',
                        height: 300,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                            name: "คะแนนที่ได้",
                            data: (payload.scores || []).map(v => Number(v) || 0)

                        },
                        {
                            name: "คะแนนเต็ม",
                            data: payload.max
                        }
                    ],
                    xaxis: {
                        categories: payload.labels,
                        title: {
                            text: "ปีการประเมิน"
                        }
                    },
                    yaxis: {
                        title: {
                            text: "คะแนน"
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: val => val.toLocaleString('th-TH')
                    },
                    colors: ['#3b82f6', '#9ca3af'],
                    legend: {
                        position: 'top'
                    }
                };

                const chart = new ApexCharts(document.querySelector(`#stdChart-${id}`), options);
                chart.render();
                charts[id] = chart;
            });

            // === filter by year ===
            function applyYearFilter() {
                const years = Array.from(document.querySelectorAll('.year-checkbox-std:checked'))
                    .map(cb => String(cb.value));

                Object.entries(charts).forEach(([id, chart]) => {
                    const orig = originals[id];
                    const idxs = orig.labels.map((y, i) => years.includes(String(y)) ? i : -1).filter(i =>
                        i >= 0);

                    const newLabels = idxs.map(i => orig.labels[i]);
                    const newScores = idxs.map(i => orig.scores[i]);
                    const newMax = idxs.map(i => orig.max[i]);

                    chart.updateOptions({
                        xaxis: {
                            categories: newLabels
                        },
                        series: [{
                                name: "คะแนนที่ได้",
                                data: newScores
                            },
                            {
                                name: "คะแนนเต็ม",
                                data: newMax
                            }
                        ]
                    }, true, true);
                });
            }

            document.querySelectorAll('.year-checkbox-std').forEach(cb => {
                cb.addEventListener('change', applyYearFilter);
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const chartsDim = {};
            const originalsDim = {};

            document.querySelectorAll('[id^="dimData-"]').forEach(el => {
                const id = el.id.replace('dimData-', '');
                const payload = JSON.parse(el.textContent);

                originalsDim[id] = payload;

                const options = {
                    chart: {
                        type: 'bar',
                        height: 300,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                            name: "คะแนนที่ได้",
                            data: payload.scores
                        },
                        {
                            name: "คะแนนเต็ม",
                            data: payload.max
                        }
                    ],
                    xaxis: {
                        categories: payload.labels,
                        title: {
                            text: "ปีการประเมิน"
                        }
                    },
                    yaxis: {
                        title: {
                            text: "คะแนน"
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: val => val.toLocaleString('th-TH')
                    },
                    colors: ['#10b981', '#9ca3af'], // เขียว + เทา
                    legend: {
                        position: 'top'
                    }
                };

                const chart = new ApexCharts(document.querySelector(`#dimChart-${id}`), options);
                chart.render();
                chartsDim[id] = chart;
            });

            // === filter by year ===
            function applyDimYearFilter() {
                const years = Array.from(document.querySelectorAll('.year-checkbox-dim:checked'))
                    .map(cb => String(cb.value));

                Object.entries(chartsDim).forEach(([id, chart]) => {
                    const orig = originalsDim[id];
                    const idxs = orig.labels.map((y, i) => years.includes(String(y)) ? i : -1).filter(i =>
                        i >= 0);

                    const newLabels = idxs.map(i => orig.labels[i]);
                    const newScores = idxs.map(i => orig.scores[i]);
                    const newMax = idxs.map(i => orig.max[i]);

                    chart.updateOptions({
                        xaxis: {
                            categories: newLabels
                        },
                        series: [{
                                name: "คะแนนที่ได้",
                                data: newScores
                            },
                            {
                                name: "คะแนนเต็ม",
                                data: newMax
                            }
                        ]
                    }, true, true);
                });
            }

            document.querySelectorAll('.year-checkbox-dim').forEach(cb => {
                cb.addEventListener('change', applyDimYearFilter);
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const chartsMain = {};
            const originalsMain = {};

            // === โหลดข้อมูลจาก blade ===
            const payload = @json($yearlyTotals);
            originalsMain['scoreLine'] = payload;

            const options = {
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                series: [{
                        name: "คะแนนที่ได้",
                        data: payload.map(r => Number(r.score) || 0)
                    },
                    {
                        name: "คะแนนเต็ม",
                        data: payload.map(r => Number(r.max) || 0)
                    }
                ],
                xaxis: {
                    categories: payload.map(r => r.year),
                    title: {
                        text: "ปีการประเมิน"
                    }
                },
                yaxis: {
                    min: 0,
                    max: payload.length ? Math.max(...payload.map(r => r.max)) * 1.15 : 100,
                    title: {
                        text: "คะแนน"
                    }
                },
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
                        if (val === null || isNaN(val)) return "";
                        if (opts.seriesIndex === 0) {
                            const row = payload[opts.dataPointIndex];
                            const percent = row.max ? ((row.score / row.max) * 100).toFixed(1) : 0;
                            return `${val} (${percent}%)`;
                        }
                        return "";
                    },
                    offsetY: -10
                },
                colors: ['#4f46e5', '#94a3b8'],
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
                grid: {
                    padding: {
                        top: 40,
                        right: 30,
                        bottom: 10,
                        left: 20
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                }
            };

            const chart = new ApexCharts(document.querySelector("#scoreLineChart"), options);
            chart.render();
            chartsMain['scoreLine'] = chart;

            // === filter by year ===
            function applyYearFilter() {
                const years = Array.from(document.querySelectorAll('.year-checkbox:checked'))
                    .map(cb => String(cb.value));

                const orig = originalsMain['scoreLine'];
                const idxs = orig.map((r, i) => years.includes(String(r.year)) ? i : -1).filter(i => i >= 0);

                const newLabels = idxs.map(i => orig[i].year);
                const newScores = idxs.map(i => Number(orig[i].score) || 0);
                const newMax = idxs.map(i => Number(orig[i].max) || 0);

                chartsMain['scoreLine'].updateOptions({
                    xaxis: {
                        categories: newLabels
                    },
                    series: [{
                            name: "คะแนนที่ได้",
                            data: newScores
                        },
                        {
                            name: "คะแนนเต็ม",
                            data: newMax
                        }
                    ],
                    yaxis: {
                        min: 0,
                        max: newMax.length ? Math.max(...newMax) * 1.15 : 100,
                        title: {
                            text: "คะแนน"
                        }
                    }
                }, true, true);
            }

            document.querySelectorAll('.year-checkbox').forEach(cb => {
                cb.addEventListener('change', applyYearFilter);
            });
        });
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
        document.getElementById('custom-search').addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.enhanced-chart-card').forEach(card => {
                const title = card.querySelector('.chart-title')?.textContent.toLowerCase() || '';
                card.style.display = title.includes(q) ? '' : 'none';
            });
        });
        
        // ==== เก็บ instance ของ ApexCharts (ย้ายออกมาข้างนอก) ====
        window.chartInstances = window.chartInstances || {};
        window.chartOriginals = window.chartOriginals || {};

        // ==== init ApexCharts (ย้ายออกมาข้างนอก) ====
        window.initChartsFromInlineJSON = function() {
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
                window.chartOriginals[container.id] = {
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
                window.chartInstances[container.id] = chart;
            });
        };

        // ==== ฟังก์ชัน Show More (ย้ายออกมาข้างนอก) ====
        window.bindShowMore = function() {
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
        };
        
        (function() {
            // หยุดการทำงานถ้า FilterComponent มีอยู่แล้ว
            if (typeof window.FilterComponent !== 'undefined') {
                console.log('FilterComponent detected, skipping legacy filter initialization');
                return;
            }
            
            const FILTERS = @json($filters, JSON_UNESCAPED_UNICODE);
            const $year = document.getElementById('filter-year');
            const $code = document.getElementById('filter-codes');
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
            fillSelect($code, sortCodes(FILTERS.codes || []));
            fillSelect($std, (FILTERS.standards || []).slice().sort((a, b) => a.name.localeCompare(b.name, 'th')), it =>
                ({
                    value: String(it.id),
                    label: it.name
                }));
            fillSelect($dim, (FILTERS.dimensions || []).slice().sort());
            fillSelect($type, (FILTERS.types || []).slice().sort());

            // ==== ฟิลเตอร์ ====
            async function applyFilters() {
                const vYear = String($year.value || '');
                const vCode = $code.value,
                    vStd = $std.value,
                    vDim = $dim.value,
                    vType = $type.value;

                document.querySelectorAll('.charts-of-standard').forEach(container => {
                    const cards = container.querySelectorAll('.chart-card[data-index]');
                    let visibleCount = 0;

                    cards.forEach(card => {
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

                        if (show) {
                            // ✅ limit แค่ 5 ตัวแรก
                            if (visibleCount < 5) {
                                card.style.display = '';
                            } else {
                                card.style.display = 'none';
                            }
                            visibleCount++;
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    // ✅ reset ปุ่ม show more
                    const btn = container.parentElement.querySelector('.btn-show-more');
                    if (btn) {
                        btn.classList.remove('expanded');
                        btn.textContent = 'แสดงเพิ่มเติม ▼';
                        btn.style.display = visibleCount > 5 ? '' : 'none';
                    }
                });

                // === update charts ตาม year filter ===
                Object.entries(window.chartInstances).forEach(([id, chart]) => {
                    const orig = window.chartOriginals[id];
                    if (!orig) return;

                    let newYears = orig.years.slice();
                    let newValues = orig.values.slice();
                    let newMax = orig.maxValues.slice();
                    if (vYear) {
                        const idxs = orig.years.map((y, i) => ({
                                y,
                                i
                            }))
                            .filter(o => o.y === vYear)
                            .map(o => o.i);
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

                // รีเซ็ตการ์ดของทุก standard
                document.querySelectorAll('.charts-of-standard').forEach(container => {
                    const cards = container.querySelectorAll('.chart-card[data-index]');
                    cards.forEach(c => {
                        const idx = parseInt(c.dataset.index, 10);
                        c.style.display = idx < 5 ? '' : 'none'; // ✅ โชว์เฉพาะ 5 การ์ดแรก
                    });
                });

                // รีเซ็ต chart กลับเป็นข้อมูลต้นฉบับ
                Object.entries(window.chartInstances).forEach(([id, chart]) => {
                    const orig = window.chartOriginals[id];
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

                // รีเซ็ตปุ่ม show more ทุกตัว
                document.querySelectorAll('.btn-show-more').forEach(btn => {
                    btn.classList.remove('expanded');
                    btn.textContent = 'แสดงเพิ่มเติม ▼';
                });
            }

            // สร้าง global functions สำหรับ FilterComponent
            window.applyResultFilters = applyFilters;
            window.resetResultFilters = function() {
                resetFilters();
                applyFilters();
            };

            // Fallback event listeners
            document.getElementById('apply-filters')?.addEventListener('click', applyFilters);
            document.getElementById('reset-filters')?.addEventListener('click', () => {
                resetFilters();
                applyFilters();
            });

            // ==== init ====
            if (typeof window.FilterComponent === 'undefined') {
                // เฉพาะเมื่อไม่มี FilterComponent เท่านั้น
                window.initChartsFromInlineJSON();
                window.bindShowMore();
            }
        })();

        // ==== เริ่มต้น FilterComponent (ย้ายออกมาข้างนอก) ====
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.FilterComponent !== 'undefined') {
                window.FilterComponent.init({
                    filterId: 'filter-panel',
                    formId: 'result-filter-form',
                    onApply: function() {
                        if (typeof window.applyResultFilters === 'function') {
                            window.applyResultFilters();
                        }
                    },
                    onReset: function() {
                        if (typeof window.resetResultFilters === 'function') {
                            window.resetResultFilters();
                        }
                    }
                });
                
                // Initialize charts เมื่อใช้ FilterComponent
                setTimeout(function() {
                    if (typeof window.initChartsFromInlineJSON === 'function') {
                        window.initChartsFromInlineJSON();
                    }
                    if (typeof window.bindShowMore === 'function') {
                        window.bindShowMore();
                    }
                }, 100);
            }
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

        .search-box {
            /* margin-left: 60px; */
            margin-top: 30px;
            position: relative;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .search-box .icon {
            position: absolute;
            inset: 0 auto 0 12px;
            display: flex;
            align-items: center;
            pointer-events: none;
        }

        .search-input {
            padding: 8px 16px 8px 40px;
            width: 100%;
            outline: 0;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius);
        }

        .search-input:focus {
            border-color: var(--ring);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .2);
        }


        .charts-of-dimensions {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            /* ✅ 3 คอลัมน์ */
            gap: 20px;
        }

        .dim-card {
            background: #fff;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
        }

        .charts-of-standards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            /* ✅ 3 คอลัมน์ */
            gap: 20px;
        }

        .standard-card {
            background: #fff;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
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

        #scoreLineChart {
            min-height: 365px;
            width: 100%;
            max-width: 100%;

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
            background: #fff;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
            margin-bottom: 24px;
            /* ✅ เพิ่มระยะห่าง */
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

        .switch {
            position: relative;
            display: inline-block;
            width: 46px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background-color: #ccc;
            transition: .3s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #2196F3;
        }

        input:checked+.slider:before {
            transform: translateX(22px);
        }
    </style>

    <style>
        /* ---- Base Variables ---- */
        :root {
            --blue: #398ECA;
            --blue-600: #2f7db2;
            --text: #1f2937;
            --muted: #6b7280;
            --border: #e5e7eb;
            --bg: #ffffff;
            --shadow: 0 8px 24px rgba(0, 0, 0, .08);
            --radius: 16px;
            --radius-sm: 10px;
            
            /* CSS Variables for FilterComponent */
            --color-blue-500: #3b82f6;
            --color-blue-600: #2563eb;
            --color-blue-700: #1d4ed8;
            --color-blue-50: #eff6ff;
            --color-white: #ffffff;
            --color-gray-200: #e5e7eb;
            --color-gray-400: #9ca3af;
            --color-gray-500: #6b7280;
            --color-gray-700: #374151;
            --color-gray-50: #f9fafb;
        }

        /* Note: Form, Field, Button styles are now handled by FilterComponent */

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
            margin: 0 8px;
        }

        .btn-show-more {
            background: none;
            border: none;
            color: #3b82f6;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            padding: 0 4px;
            transition: color 0.2s;
        }

        .btn-show-more:hover {
            color: #2563eb;
        }
    </style>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize FilterComponent toggle if available
            if (typeof window.FilterComponent !== 'undefined') {
                // FilterComponent จะจัดการ toggle เอง
                const toggle = document.getElementById('toggle-filter');
                if (toggle) {
                    toggle.addEventListener('change', function() {
                        const panel = document.getElementById('filter-panel');
                        if (panel) {
                            panel.style.display = this.checked ? 'block' : 'none';
                        }
                    });
                }
            } else {
                // Fallback for legacy toggle
                const toggle = document.getElementById('toggle-filter');
                const panel = document.getElementById('filter-panel');
                if (toggle && panel) {
                    toggle.addEventListener('change', function() {
                        panel.style.display = this.checked ? '' : 'none';
                    });
                }
            }
        });
    </script>
@endsection
