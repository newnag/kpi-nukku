@extends('layouts.app')
@section('title', 'แดชบอร์ด')

@section('header', 'แดชบอร์ด')
@section('subheader', 'ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล')

@section('content')

    <!-- Toggle Switch -->
    <div style="text-align: right; margin-bottom:10px;">
        <label class="switch">
            <input type="checkbox" id="toggle-filter">
            <span class="slider round"></span>
        </label>
        <span style="margin-left:8px;">กรองข้อมูล</span>
    </div>
    <!-- Filter Card -->
    <!-- ✅ เรียก Component filter-form -->
    <x-filter :years="$yearsForFilter" :standards="$allStandards->pluck('name')" :departments="$departments" :collectors="$collectors" :dimensions="$dimensionNames" :action="route('dashboard.index')"
        :selectedYear="$displayYear" />

    <!-- Score Card -->

    {{-- <div class="stat-title">
        <h3> คะแนนทั้งหมดที่ได้ในแต่ละปี</h3>

    </div>
    <div class="score-card">
        <div class="score-header">
            <span class="label">ปีการประเมิน</span>
            <span class="year" id="display-year">{{ $displayYearText }}</span>
        </div>
        <hr />
        <div class="score-body">
            <span class="label-left">คะแนนที่ได้</span>
            <div class="score-value">
                <span id="display-total">{{ number_format($totalScore) }}</span>
                <span class="divider">/</span>
                <span id="display-max">{{ number_format($maxScore) }}</span>
            </div>
            <span class="label-right">คะแนนเต็ม</span>
        </div>
    </div> --}}


    <!-- Stats Cards -->
    <div class="stat-title">
        <h3>สถานะทั้งหมดของตัวชี้วัดต่อปี</h3>
        <span class="year"id="display-years">{{ $displayYearText }}</span>
    </div>
    <div class="stats-grid">

        <!-- Card  ความพึงพอใจ -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title">

                </div>

                {{-- <button class="btn-export"id="downloadCard">
                    <!-- icon -->
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M9 12l3 3 3-3" stroke="#16a34a" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M12 3v12" stroke="#16a34a" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M5 21h14a2 2 0 0 0 2-2v-4" stroke="#16a34a" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M3 15v4a2 2 0 0 0 2 2" stroke="#16a34a" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    EXPORT CHART (PNG)
                </button> --}}
            </div>

            <div class="stat-body">
                <div class="chart-wrap">
                    <canvas id="satisfactionChart" width="310px" height="260"></canvas>
                </div>

                <div class="legend-wrap">

                    @php $totalStatus = array_sum($statusCounts); @endphp

                    @foreach ($legendConfig as $item)
                        @php
                            $count = $statusCounts[$item['key']] ?? 0;
                            // $pct = $totalStatus > 0 ? number_format(($count / $totalStatus) * 100, 2) : '0.00';
                        @endphp
                        {{-- <div class="legend-item" data-key="{{ $item['key'] }}">
                            <div class="legend-left">
                                <span class="dot" style="background:{{ $item['color'] }}"></span>
                                <div class="legend-text">
                                    <div class="label">{{ $item['label'] }}</div>
                                    <div class="subtext">
                                        <strong class="legend-count">{{ $count }}</strong> จำนวนตัวชี้
                                    </div>
                                </div>
                            </div>

                        </div> --}}
                    @endforeach

                    <div class="stats-card" id="card-total">
                        <div class="stats-icon">
                            <i class="fa fa-list"></i>
                        </div>
                        <div class="stats-info">
                            <div class="stats-value" id="indicator-total">0</div>
                            <div class="stats-label">จำนวนตัวชี้วัดทั้งหมด</div>
                        </div>
                    </div>

                    <div class="stats-card legend-item" data-key="complete">
                        <div class="stats-icon success">
                            <i class="fa fa-check-double"></i>
                        </div>
                        <div class="stats-info">
                            <div class="stats-value legend-count">{{ $statusCounts['complete'] ?? 0 }}</div>
                            <div class="stats-label">ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรการ</div>

                        </div>
                    </div>

                    <div class="stats-card legend-item" data-key="incomplete">
                        <div class="stats-icon warn">
                            <i class="fa fa-bell"></i>
                        </div>
                        <div class="stats-info">
                            <div class="stats-value legend-count">{{ $statusCounts['incomplete'] ?? 0 }}</div>
                            <div class="stats-label">ผลการดำเนินงานยังไม่ครบถ้วนตามเกณฑ์</div>

                        </div>
                    </div>

                    <div class="stats-card legend-item" data-key="pending">
                        <div class="stats-icon danger">
                            <i class="fa fa-times"></i>
                        </div>
                        <div class="stats-info">
                            <div class="stats-value legend-count">{{ $statusCounts['pending'] ?? 0 }}</div>
                            <div class="stats-label">อยู่ระหว่างดำเนินการ</div>

                        </div>
                    </div>


                </div>
            </div>
        </div>
        <div class="stat-title">
            <h3>รายการตัวบงชี้</h3>

        </div>

        <div class="containers">

            <div class="chart-header">

                <div class="search-box flex-1 max-w-[420px]">

                    <div class="icon">
                        <!-- search icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" style="color:#9ca3af;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" id="custom-search" class="search-input" placeholder="ค้นหารายการตัวบ่งชี้">
                </div>

                <button id="exportExell" class="btn-export-excel">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M9 12l3 3 3-3" stroke="#16a34a" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M12 3v12" stroke="#16a34a" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M5 21h14a2 2 0 0 0 2-2v-4" stroke="#16a34a" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M3 15v4a2 2 0 0 0 2 2" stroke="#16a34a" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    EXPORT TO EXCEL
                </button>
            </div>

            <div class="dashboard-list">
                <table class="table" id="dashboardTable">

                    <thead>
                        <tr>
                            {{-- <th>ลำดับ</th> --}}
                            <th>ปีการประเมิน</th>
                            <th>มาตรฐานตัวชี้วัด</th>
                            <th>ชื่อตัวบ่งชี้</th>
                            <th>รหัส</th>
                            <th>ประเภทตัวชี้วัด</th>
                            <th>หน่วยงานที่รับผิดชอบ</th>
                            <th>ผลลัพธ์</th>
                            <th>คะแนนรวม</th>
                            <th>สถานะตัวชี้วัด</th>
                            {{-- <th>สถานะเอกสาร</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($indicators as $index => $indicator)
                            @php
                                $statusKey = match ((int) $indicator->status) {
                                    2, 3 => 'complete',
                                    4 => 'incomplete',
                                    0 => 'pending',
                                    default => 'pending',
                                };

                                $standardName = $indicator->category->standard->name ?? '';
                                $dimensionName = $indicator->category->name ?? '';
                                $collectorName = $indicator->assignments->first()->collectorUser->name ?? '';
                                $deptName = '';
                                foreach ($indicator->assignments as $assignment) {
                                    $deptName = optional($assignment->collectorUser?->department)->name ?? '';
                                    if ($deptName) {
                                        break;
                                    }
                                }
                            @endphp
                            <tr data-max="{{ (float) $indicator->max_score }}" data-standard="{{ $standardName }}"
                                data-dimension="{{ $dimensionName }}" data-collector="{{ $collectorName }}"
                                data-dept="{{ $deptName }}" data-status="{{ $statusKey }}">

                                <td class="status-cell">{{ $indicator->year }}</td>
                                <td class="status-cell">{{ $standardName ?: '-' }}</td> <!-- ✅ ใช้ค่าจาก relation -->
                                <td>{{ $indicator->name }}</td>
                                <td class="status-cell">{{ $indicator->code }}</td>
                                <td class="status-cell">{{ $indicator->type }}</td>
                                <td class="status-cell">{{ $deptName ?: '-' }}</td>
                                <td class="status-cell">{{ $indicator->score_acc }}</td>
                                <td class="status-cell">{{ $indicator->max_score }}</td>
                                <td class="status-cell">
                                    {{-- status icon switch --}}
                                    @switch($indicator->status)
                                        @case(0)
                                            <span class="tip" data-tip="อยู่ระหว่างดำเนินการ">
                                                <i data-lucide="alert-triangle" class="status-icon text-danger"></i>
                                            </span>
                                        @break

                                        @case(4)
                                            <span class="tip" data-tip="ผลการดำเนินงานยังไม่ครบถ้วนตามเกณฑ์">
                                                <i data-lucide="clock" class="status-icon text-warn"></i>
                                            </span>
                                        @break

                                        @case(2)
                                        @case(3)
                                            <span class="tip" data-tip="ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรการ">
                                                <i data-lucide="check-circle" class="status-icon text-success"></i>
                                            </span>
                                        @break

                                        @default
                                            <span class="tip" data-tip="สถานะไม่ระบุ ({{ $indicator->status }})">
                                                <i data-lucide="help-circle" class="status-icon text-gray-500"></i>
                                            </span>
                                    @endswitch
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        {{-- <div class="stat-title">
            <h3>กราฟคะแนน 3 มาตรฐานตัวชี้วัด 5 ปีย้อนหลัง 2020-2024</h3>

        </div>
        <!-- Chart 1: การเข้าชม 5 ปีย้อนหลัง 2020-2024 -->
        <div class="chart-card">
            <div class="chart-header">
                <h3></h3>
                <button id="exportChart1" class="btn-export btn-export-in-card">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M9 12l3 3 3-3" stroke="#16a34a" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M12 3v12" stroke="#16a34a" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M5 21h14a2 2 0 0 0 2-2v-4" stroke="#16a34a" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M3 15v4a2 2 0 0 0 2 2" stroke="#16a34a" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    EXPORT CHART (PNG)
                </button>
            </div>
            <div class="chart-content">
                <canvas id="visitsChart"></canvas>
            </div>
        </div>
        <div class="stat-title">
            <h3>กราฟคะแนน 7 ด้านการประเมิน ตามปี 2020-2024</h3>

        </div>
        <!-- Chart 2: สถิติเข้าชม 7 วันย้อนหลัง 2020-2024 -->
        <div class="chart-card">
            <div class="chart-header">
                <h3></h3>
                <button id="exportChart2" class="btn-export">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M9 12l3 3 3-3" stroke="#16a34a" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M12 3v12" stroke="#16a34a" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M5 21h14a2 2 0 0 0 2-2v-4" stroke="#16a34a" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M3 15v4a2 2 0 0 0 2 2" stroke="#16a34a" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    EXPORT CHART (PNG)
                </button>
            </div>


            <div class="chart-content">
                <canvas id="yearAsXChart"></canvas>
            </div>
        </div> --}}

    </div>

    <!-- ตารางเอกสารและหลักฐาน -->
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.469.0/dist/umd/lucide.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>


    <script>
        document.getElementById('exportExell').addEventListener('click', function() {
            const params = new URLSearchParams();

            const year = document.getElementById('filter-year')?.value || '';
            if (year) params.set('year', year);

            const standard = document.getElementById('filter-standard')?.value || '';
            if (standard) params.set('standard', standard);

            const dimension = document.getElementById('filter-dimension')?.value || '';
            if (dimension) params.set('dimension', dimension);

            const status = document.getElementById('filter-status')?.value || '';
            if (status !== '') params.set('status', status);

            const dept = document.getElementById('filter-dept')?.value || '';
            if (dept) params.set('dept_id', dept);

            const code = document.getElementById('filter-code')?.value || '';
            if (code) params.set('code', code);

            const url = "{{ route('dashboard.export') }}" + (params.toString() ? `?${params}` : '');
            window.location.href = url;
        });
        document.getElementById('toggle-filter').addEventListener('change', function() {
            const card = document.getElementById('filter-card');
            card.style.display = this.checked ? 'block' : 'none';
        });
    </script>

    {{-- <script>
        // ============== Utility Functions ==============
        function cloneDeep(obj) {
            return JSON.parse(JSON.stringify(obj ?? {}));
        }

        function downloadBlob(blob, filename) {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(url);
        }

        // ============== Background Plugin ==============
        const bgPlugin = {
            id: 'bg',
            beforeDraw(c) {
                const {
                    ctx,
                    width,
                    height
                } = c;
                ctx.save();
                ctx.globalCompositeOperation = 'destination-over';
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, width, height);
                ctx.restore();
            }
        };

        // ============== Export Chart.js เป็น PNG แบบ offscreen ==============
        async function exportChartPNG(chart, filename, {
            widthScale = 2, // ขยายความกว้างจากต้นฉบับกี่เท่า
            heightScale = 2, // ขยายความสูงจากต้นฉบับกี่เท่า (เพิ่มค่านี้ให้รูป "สูง" ขึ้น)
            extraBottom = 60, // เผื่อพื้นที่ legend/label ด้านล่าง (px ของขนาดเดิม)
            bg = '#ffffff' // สีพื้นหลังภาพ
        } = {}) {

            // 1) คำนวณขนาดเอาต์พุตจากขนาด "จริง" ของ canvas เดิม (หน่วย px ภายใน)
            const srcW = chart.canvas.width;
            const srcH = chart.canvas.height;

            const outW = Math.round(srcW * widthScale);
            const outH = Math.round((srcH + extraBottom) * heightScale);

            // 2) เตรียม offscreen canvas
            const off = document.createElement('canvas');
            off.width = outW;
            off.height = outH;
            const offCtx = off.getContext('2d');

            // 3) clone data/options แล้วปรับให้เหมาะกับ export
            const data = cloneDeep(chart.config.data);
            const options = cloneDeep(chart.config.options);
            options.responsive = false; // เรากำหนดขนาดเอง
            options.maintainAspectRatio = false;
            options.animation = false;
            options.layout = options.layout || {};
            // เพิ่ม padding ล่างเพื่อกัน legend ชนขอบ
            const pad = options.layout.padding || {};
            options.layout.padding = {
                top: pad.top || 0,
                right: pad.right || 0,
                bottom: (pad.bottom || 0) + extraBottom,
                left: pad.left || 0
            };

            // ขยายขนาดฟอนต์ให้เหมาะกับความสูงใหม่ (optional)
            options.plugins = options.plugins || {};
            options.plugins.legend = options.plugins.legend || {};
            options.plugins.legend.labels = options.plugins.legend.labels || {};
            const baseFont = (options.plugins.legend.labels.font && options.plugins.legend.labels.font.size) || 12;
            options.plugins.legend.labels.font = {
                size: Math.round(baseFont * heightScale)
            };

            // 4) ปลั๊กอินพื้นหลังสำหรับ export
            const exportBgPlugin = {
                id: 'exportBg',
                beforeDraw(c) {
                    const {
                        ctx,
                        width,
                        height
                    } = c;
                    ctx.save();
                    ctx.globalCompositeOperation = 'destination-over';
                    ctx.fillStyle = bg;
                    ctx.fillRect(0, 0, width, height);
                    ctx.restore();
                }
            };

            // 5) เรนเดอร์กราฟใหม่บน offscreen (ชนิดเดียวกับต้นฉบับ)
            const plugins = [exportBgPlugin];
            if (window.ChartDataLabels) plugins.push(ChartDataLabels);

            const offChart = new Chart(offCtx, {
                type: chart.config.type,
                data,
                options,
                plugins
            });

            // ให้กราฟคำนวณเลย์เอาต์ตามขนาดเป้าหมาย
            offChart.resize(outW, outH);
            offChart.update('none');

            // 6) บันทึกไฟล์ (PNG)
            await new Promise(r => setTimeout(r, 0)); // ปล่อยให้วาดเสร็จเฟรมนี้
            off.toBlob((blob) => {
                downloadBlob(blob, filename);
                offChart.destroy();
            }, 'image/png', 1);
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Lucide
            if (typeof lucide !== 'undefined') lucide.createIcons();

            // ---------- Chart 1 ----------
            const el1 = document.getElementById('visitsChart');
            const data1 = @json($chart);
            // 👉 ใส่คำว่า "ปี " ให้ labels ของ chart1
            if (Array.isArray(data1?.labels)) {
                data1.labels = data1.labels.map(l => {
                    const t = String(l ?? '');
                    return /^ปี\s*/.test(t) ? t : `ปี ${t}`;
                });
            }

            let chart1 = null;

            if (el1 && Array.isArray(data1?.labels) && data1.labels.length) {
                chart1 = new Chart(el1.getContext('2d'), {
                    type: 'bar',
                    data: data1,
                    plugins: [bgPlugin],
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    font: {
                                        size: 12
                                    },
                                    usePointStyle: true,
                                    padding: 15
                                }
                            }
                        }
                    }
                });
            }

            // export ปุ่ม 1
            const btn1 = document.getElementById('exportChart1');
            if (btn1 && chart1) {
                btn1.addEventListener('click', async () => {
                    chart1.options.animation = false;
                    chart1.update('none');

                    // คมชัดขึ้น: scale 2x ชั่วคราว
                    const {
                        width,
                        height
                    } = chart1;
                    chart1.resize(width * 2, height * 2);
                    chart1.update('none');

                    el1.toBlob((blob) => {
                        downloadBlob(blob, `chart-1-${Date.now()}.png`);
                        // คืนขนาดเดิม
                        chart1.resize(width, height);
                        chart1.update('none');
                    }, 'image/png', 1);
                });
            }

            // ---------- Chart 2 ----------
            const el2 = document.getElementById('yearAsXChart');
            const data2 = @json($chartYearAsX);
            // 👉 ใส่คำว่า "ปี " ให้ labels ของ chart2
            if (Array.isArray(data2?.labels)) {
                data2.labels = data2.labels.map(l => {
                    const t = String(l ?? '');
                    return /^ปี\s*/.test(t) ? t : `ปี ${t}`;
                });
            }
            let chart2 = null;

            if (el2 && Array.isArray(data2?.labels) && data2.labels.length) {
                const plugins = window.ChartDataLabels ? [ChartDataLabels, bgPlugin] : [bgPlugin];
                chart2 = new Chart(el2.getContext('2d'), {
                    type: 'bar',
                    data: data2,
                    plugins,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                stacked: false,
                                ticks: {
                                    autoSkip: false,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    font: {
                                        size: 12
                                    },
                                    usePointStyle: true,
                                    padding: 15
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            },
                            datalabels: window.ChartDataLabels ? {
                                anchor: 'end',
                                align: 'end',
                                clamp: true,
                                font: {
                                    size: 11
                                },
                                formatter: (v) => (v == null ? '' : Number(v).toFixed(2))
                            } : undefined
                        }
                    }
                });
            }

            // export ปุ่ม 2
            const btn2 = document.getElementById('exportChart2');
            if (btn2 && chart2) {
                btn2.addEventListener('click', () => {
                    chart2.options.animation = false;
                    chart2.update('none');

                    const {
                        width,
                        height
                    } = chart2;
                    chart2.resize(width * 2, height * 2);
                    chart2.update('none');

                    el2.toBlob((blob) => {
                        downloadBlob(blob, `chart-2-${Date.now()}.png`);
                        chart2.resize(width, height);
                        chart2.update('none');
                    }, 'image/png', 1);
                });
            }
        });
    </script> --}}

    <script>
        (function($) {
            let table, donutChart;

            const stripHtml = (s) => {
                const d = document.createElement('div');
                d.innerHTML = String(s ?? '');
                return (d.textContent || d.innerText || '').trim();
            };
            const numberFormat = (n) => (isNaN(n) ? 0 : Number(n)).toLocaleString('th-TH');
            // แผนที่คะแนนรวม/คะแนนเต็มต่อปีจากเซิร์ฟเวอร์ เพื่อใช้คำนวณสรุปแบบไม่ปนปี
            const YEARLY_TOTALS_ARRAY = @json($yearlyTotals);
            const YEARLY_TOTALS_MAP = Array.isArray(YEARLY_TOTALS_ARRAY) ?
                YEARLY_TOTALS_ARRAY.reduce((acc, item) => {
                    const y = String(item.year ?? '');
                    acc[y] = {
                        total: Number(item.total_score ?? 0),
                        max: Number(item.max_score ?? 0),
                    };
                    return acc;
                }, {}) : {};
            const getLatestYear = () => {
                const years = Array.isArray(window.ALL_YEARS) ? window.ALL_YEARS : [];
                const nums = years
                    .map((y) => Number(String(y).replace(/[^0-9-]/g, '')))
                    .filter((n) => !isNaN(n));
                return nums.length ? Math.max(...nums).toString() : '';
            };

            $(function() {
                // 1) Init DataTable
                table = $('#dashboardTable').DataTable({
                    searching: true,
                    lengthChange: false,
                    dom: 'rtip',
                    order: [],
                    stateSave: false,
                    language: {
                        paginate: {
                            previous: 'ก่อนหน้า',
                            next: 'ถัดไป'
                        },
                        info: 'แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ',
                        emptyTable: 'ไม่พบข้อมูล',
                        zeroRecords: 'ไม่พบข้อมูลที่ตรงกับการค้นหา',
                    },
                });

                // 2) หาคอลัมน์จริง
                const heads = $('#dashboardTable thead th').map((i, th) => $(th).text().trim()).get();
                const findCol = (cands) => {
                    for (const kw of cands) {
                        const idx = heads.findIndex((h) => h.includes(kw));
                        if (idx !== -1) return idx;
                    }
                    return -1;
                };

                const idxYear = findCol(['ปีการประเมิน']);
                const idxCode = findCol(['รหัส']);
                const idxDept = findCol(['หน่วยงานที่รับผิดชอบ']);
                const idxScore = findCol(['คะแนนรวม']); // ใช้รวมเป็น total

                // 3) Select element
                const $year = $('#filter-year'),
                    $code = $('#filter-code'),
                    $std = $('#filter-standard'),
                    $dim = $('#filter-dimension'),
                    $dept = $('#filter-dept'),
                    $collector = $('#filter-collector');

                // 4) เติม option …
                const populateFromColumn = ($sel, colIdx) => {
                    $sel.find('option:not([value=""])').remove();
                    if (colIdx === -1) return;
                    const vals = table.column(colIdx).data().toArray().map(stripHtml).filter(Boolean);
                    const uniq = [...new Set(vals)].sort((a, b) => a.localeCompare(b, 'th'));
                    uniq.forEach((v) => $sel.append(`<option value="${v}">${v}</option>`));
                };
                populateFromColumn($year, idxYear);
                populateFromColumn($code, idxCode);
                populateFromColumn($dept, idxDept);

                window.ALL_YEARS = @json($yearsForFilter);
                window.ALL_DEPARTMENTS = @json($departments);
                window.ALL_COLLECTORS = @json($collectors);
                window.ALL_STANDARDS = @json($allStandards->pluck('name'));
                window.ALL_DIMENSIONS = @json($dimensionNames);

                const populateFromData = ($sel, attr) => {
                    $sel.find('option:not([value=""])').remove();
                    const vals = [];
                    $('#dashboardTable tbody tr').each(function() {
                        const v = $(this).data(attr);
                        if (v) vals.push(v);
                    });
                    const uniq = [...new Set(vals)].sort((a, b) => a.localeCompare(b, 'th'));
                    uniq.forEach((v) => $sel.append(`<option value="${v}">${v}</option>`));
                };

                function fillSelect($sel, items) {
                    $sel.find('option:not([value=""])').remove();
                    (items || []).forEach((v) => $sel.append(`<option value="${v}">${v}</option>`));
                }
                fillSelect($('#filter-year'), window.ALL_YEARS);
                fillSelect($('#filter-dept'), window.ALL_DEPARTMENTS);
                fillSelect($('#filter-collector'), window.ALL_COLLECTORS);
                fillSelect($('#filter-standard'), window.ALL_STANDARDS);
                fillSelect($('#filter-dimension'), window.ALL_DIMENSIONS);

                populateFromData($collector, 'collector');

                // 6) การ์ดสรุป
                function parseNumberCell(s) {
                    const t = stripHtml(String(s)).replace(/[^0-9.,-]/g, '').replace(/,/g, '');
                    const n = Number(t);
                    return isNaN(n) ? 0 : n;
                }


                // ตรวจว่ามีตัวกรองอื่นนอกจาก "ปี" หรือมีค้นหาข้อความ/คอลัมน์ไหม
                function anyExtraFilterActive() {
                    const hasNonYearSelect =
                        ($code.val() || $dept.val() || $std.val() || $dim.val() || $collector.val());
                    const hasGlobalSearch = !!table.search();
                    // ถ้าใช้ column search ด้วย (กรณีคุณมี), ให้เช็คด้วย
                    let hasColumnSearch = false;
                    table.columns().every(function() {
                        if (this.search()) {
                            hasColumnSearch = true;
                        }
                    });
                    return !!(hasNonYearSelect || hasGlobalSearch || hasColumnSearch);
                }

                // รวมจากผลกรองปัจจุบัน (ใช้ data-total / data-max ถ้ามี; เผื่อ fallback ไปอ่านคอลัมน์)
                function computeFilteredTotalsForYear(targetYear) {
                    let total = 0,
                        max = 0;

                    table.rows({
                        search: 'applied',
                        page: 'all'
                    }).every(function() {
                        const node = this.node();
                        const rowData = this.data();

                        // ปีของแถว
                        const rowYear = (idxYear !== -1 ? stripHtml(rowData[idxYear]) : '').toString();
                        if (targetYear && rowYear && rowYear !== targetYear) return;

                        // อ่านจาก data-attribute ก่อน (แม่นสุด ไม่ติดฟอร์แมต ,)
                        const rowTotal = Number(node?.dataset?.total ?? NaN);
                        const rowMax = Number(node?.dataset?.max ?? NaN);

                        if (!Number.isNaN(rowTotal)) total += rowTotal;
                        else if (idxScore !== -1) total += parseNumberCell(rowData[idxScore]);

                        if (!Number.isNaN(rowMax)) max += rowMax;
                        // ถ้ามีคอลัมน์ "คะแนนเต็ม" ให้ fallback ตรงนี้ได้ ถ้าคุณมี idxMax
                        // else if (idxMax !== -1) max += parseNumberCell(rowData[idxMax]);
                    });

                    return {
                        total,
                        max
                    };
                }
                // นับจำนวนตัวชี้วัดจากผลกรองจริง
                function updateIndicatorTotal() {
                    const selectedYear = ($('#filter-year').val() || '').toString();
                    const fallbackYear = getLatestYear();
                    const effectiveYear = selectedYear || fallbackYear || '';

                    let count = 0;

                    table.rows({
                        search: 'applied',
                        page: 'all'
                    }).every(function() {
                        const node = this.node();
                        const rowData = this.data();

                        let rowYear = '';
                        try {
                            rowYear = idxYear !== -1 ? stripHtml(rowData[idxYear]) : '';
                        } catch (e) {
                            /* noop */
                        }

                        if (effectiveYear && rowYear && rowYear !== effectiveYear) return;

                        count++;
                    });

                    $("#indicator-total").text(count.toLocaleString('th-TH'));
                }

                // แทนที่ updateSummary เดิมด้วยเวอร์ชันนี้
                function updateSummary() {
                    const selectedYear = ($year.val() || '').toString();

                    // หา "ปีล่าสุด" จากฝั่งเซิร์ฟเวอร์ก่อน แล้วค่อย fallback ไปปีล่าสุดในตาราง
                    const latestFromMap = Object.keys(YEARLY_TOTALS_MAP)
                        .map(Number).filter(n => !isNaN(n)).sort((a, b) => b - a)[0];
                    const latestYearFromMap = latestFromMap ? String(latestFromMap) : '';
                    const fallbackYearFromTable = getLatestYear() || '';
                    const effectiveYear = selectedYear || latestYearFromMap || fallbackYearFromTable;

                    if (!effectiveYear) {
                        $('#display-year, #display-years').text('ไม่มีข้อมูล');
                        $('#display-total').text('0');
                        $('#display-max').text('0');
                        return;
                    }

                    $('#display-year, #display-years').text(effectiveYear);

                    // ❗ กฎสำคัญ:
                    // - ถ้า "ไม่มีตัวกรองอื่น" (นอกจากปี) => ใช้ YEARLY_TOTALS_MAP เพื่อให้ค่าตรง 735/740
                    // - ถ้า "มีตัวกรองอื่น" หรือค้นหา => รวมจากแถวที่กรองจริง
                    if (!anyExtraFilterActive()) {
                        // ใช้ยอดจากเซิร์ฟเวอร์ (ถูกต้อง 735/740 ตามปี)
                        const y = YEARLY_TOTALS_MAP[effectiveYear] || {
                            total: 0,
                            max: 0
                        };
                        $('#display-total').text(numberFormat(y.total));
                        $('#display-max').text(numberFormat(y.max));
                        return;
                    }

                    // มีตัวกรองอื่นแล้ว => รวมจากผลกรองจริง
                    const {
                        total,
                        max
                    } = computeFilteredTotalsForYear(effectiveYear);
                    $('#display-total').text(numberFormat(total));
                    $('#display-max').text(numberFormat(max));
                }



                function applyFilters() {
                    // กรองทั้งหมดแบบ client-side
                    table.draw();
                }
                // >>>>>>>>>>>>> เพิ่ม Custom Filter ของ DataTables <<<<<<<<<<<<<<
                // $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                //     if (settings.nTable !== document.getElementById('dashboardTable')) return true;

                //     const vYear = $year.val();
                //     const vCode = $code.val();
                //     const vDept = $dept.val();
                //     const vStd = $std.val();
                //     const vDim = $dim.val();
                //     const vCollector = $collector.val();

                //     const yearVal = idxYear !== -1 ? stripHtml(data[idxYear]) : '';
                //     const codeVal = idxCode !== -1 ? stripHtml(data[idxCode]) : '';
                //     const deptVal = idxDept !== -1 ? stripHtml(data[idxDept]) : '';

                //     // อ่านค่า data-* จาก DOM ของแถว
                //     const node = table.row(dataIndex).node();
                //     const stdVal = node?.dataset?.standard || '';
                //     const dimVal = node?.dataset?.dimension || '';
                //     const colVal = node?.dataset?.collector || '';

                //     if (vYear && yearVal !== vYear) return false;
                //     if (vCode && codeVal !== vCode) return false;
                //     if (vDept && deptVal !== vDept) return false;
                //     if (vStd && stdVal !== vStd) return false;
                //     if (vDim && dimVal !== vDim) return false;
                //     if (vCollector && colVal !== vCollector) return false;

                //     return true;
                // });
                // ✅ Custom Filter ของ DataTables (รวมปี, code, dept, standard, dimension, collector + pie chart)
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    if (settings.nTable !== document.getElementById('dashboardTable')) return true;

                    const vYear = $year.val();
                    const vCode = $code.val();
                    const vDept = $dept.val();
                    const vStd = $std.val();
                    const vDim = $dim.val();
                    const vCollector = $collector.val();

                    const yearVal = idxYear !== -1 ? stripHtml(data[idxYear]) : '';
                    const codeVal = idxCode !== -1 ? stripHtml(data[idxCode]) : '';
                    const deptVal = idxDept !== -1 ? stripHtml(data[idxDept]) : '';

                    // อ่านค่า data-* จาก DOM ของแถว
                    const node = table.row(dataIndex).node();
                    const stdVal = node?.dataset?.standard || '';
                    const dimVal = node?.dataset?.dimension || '';
                    const colVal = node?.dataset?.collector || '';
                    const rowStatus = node?.dataset?.status || '';

                    // ===== กรองตาม filter ที่เลือกใน form =====
                    if (vYear && yearVal !== vYear) return false;
                    if (vCode && codeVal !== vCode) return false;
                    if (vDept && deptVal !== vDept) return false;
                    if (vStd && stdVal !== vStd) return false;
                    if (vDim && dimVal !== vDim) return false;
                    if (vCollector && colVal !== vCollector) return false;

                    // ===== กรองตามสถานะจาก pie chart (window.selectedStatusFilter) =====
                    if (window.selectedStatusFilter && rowStatus !== window.selectedStatusFilter)
                        return false;

                    return true;
                });

                // 7) ฟิลเตอร์แถว (ให้ DataTables เป็นคนกรองเอง)
                function applyFilters() {
                    // กรองทั้งหมดแบบ client-side
                    table.draw();
                }

                // 8) Chart.js (คงเดิม)
                let donutChart = null;
                const donutCanvas = document.getElementById('satisfactionChart');
                let chartKeys = [],
                    chartLabels = [],
                    chartColors = [];
                if (donutCanvas) {
                    const donutCtx = donutCanvas.getContext('2d');
                    chartLabels = @json(array_column($legendConfig, 'label'));
                    chartColors = @json(array_column($legendConfig, 'color'));
                    chartKeys = @json(array_column($legendConfig, 'key'));

                    const countsMap = @json($statusCounts);
                    const dataValues = chartKeys.map((k) => Number(countsMap[k] ?? 0));

                    donutChart = new Chart(donutCtx, {
                        type: 'pie',
                        data: {
                            labels: chartLabels,
                            datasets: [{
                                data: dataValues,
                                backgroundColor: chartColors,
                                borderColor: '#fff',
                                borderWidth: 2,
                                hoverOffset: 6,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        font: {
                                            size: 12
                                        },
                                        padding: 15
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: (ctx) => {
                                            const total = ctx.dataset.data.reduce((a, b) => a + b,
                                                0);
                                            const val = ctx.parsed;
                                            const pct = total > 0 ? ((val / total) * 100).toFixed(
                                                1) : '0.0';
                                            return ` ${ctx.label}: ${val} (${pct}%)`;
                                        },
                                    },
                                },
                                // ⭐ ใส่เปอร์เซ็นต์ในวงกลม
                                datalabels: {
                                    color: '#fff',
                                    font: {
                                        weight: 'bold',
                                        size: 14
                                    },
                                    formatter: (value, ctx) => {
                                        const total = ctx.chart.data.datasets[0].data.reduce((a,
                                            b) => a + b, 0);
                                        const pct = total > 0 ? (value / total * 100).toFixed(1) :
                                        0;
                                        return pct + '%';
                                    }
                                }
                            }
                        },
                        plugins: [ChartDataLabels] // ⭐ เปิดใช้งาน plugin
                    });


                }

                // ====== ฟังก์ชัน Export ทั้งการ์ดเป็น PNG ======
                // function exportCardToImage() {
                //     const card = document.querySelector('.stat-card'); // เลือกเฉพาะการ์ด
                //     if (!card) return;

                //     html2canvas(card, {
                //         backgroundColor: '#ffffff',
                //         scale: 2,
                //         useCORS: true,

                //         // มองข้าม element ที่ติด flag
                //         ignoreElements: (el) => el.closest('[data-html2canvas-ignore]') !== null,

                //         // กันกรณีปุ่มอยู่ใน card และไม่มี flag
                //         onclone: (doc) => {
                //             // ซ่อนปุ่มใน shadow DOM ที่ถูกโคลนไปเรนเดอร์
                //             doc.querySelectorAll('.btn-export, #downloadCard').forEach(btn => {
                //                 btn.style.display = 'none';
                //             });
                //         }
                //     }).then(canvas => {
                //         const a = document.createElement('a');
                //         a.href = canvas.toDataURL('image/png');
                //         a.download = 'satisfaction_card.png';
                //         a.click();
                //     });
                // }

                // document.getElementById('downloadCard')
                //     .addEventListener('click', exportCardToImage);


                // // ====== ผูกปุ่มดาวน์โหลด ======
                // document.getElementById('downloadCard')
                //     .addEventListener('click', exportCardToImage);
                // 9) นับสถานะจาก "ผลกรองแล้ว"
                function computeStatusCountsFiltered() {
                    const counts = Object.fromEntries(chartKeys.map((k) => [k, 0]));
                    const selectedYear = ($('#filter-year').val() || '').toString();
                    const fallbackYear = getLatestYear();
                    const effectiveYear = selectedYear || fallbackYear || '';

                    table.rows({
                        search: 'applied',
                        page: 'all'
                    }).every(function() {
                        const key = this.node().dataset.status;

                        let rowYear = '';
                        try {
                            const rowData = this.data();
                            rowYear = idxYear !== -1 ? stripHtml(rowData[idxYear]) : '';
                        } catch (e) {
                            /* noop */
                        }

                        if (effectiveYear && rowYear && rowYear !== effectiveYear) return;

                        if (key && counts.hasOwnProperty(key)) counts[key] += 1;
                    });
                    return counts;
                }

                // 10-11) อัปเดต legend + chart จากข้อมูลที่กรองแล้ว
                function updateLegend(counts) {
                    const total = Object.values(counts).reduce((a, b) => a + b, 0);
                    chartKeys.forEach((k) => {
                        const c = counts[k] ?? 0;

                        const $item = $(`.legend-item[data-key="${k}"]`);
                        $item.find('.legend-count').text(c);

                        $item.find('.bar').css('width');
                    });
                }

                function updateDonutAndLegend() {
                    if (!donutChart) return;
                    const counts = computeStatusCountsFiltered();
                    const newData = chartKeys.map((k) => counts[k] ?? 0);
                    donutChart.data.datasets[0].data = newData;
                    donutChart.update();
                    updateLegend(counts);
                }

                // 12) Bind events
                $('#filter-form').on('submit', function(e) {
                    e.preventDefault();
                });
                $('#apply-filters').on('click', function(e) {
                    e.preventDefault();
                    applyFilters();
                });

                // $(document).off('click.reset', '#reset-filters').on('click.reset', '#reset-filters', function(
                //     e) {
                //     e.preventDefault();
                //     e.stopPropagation();

                //     // ล้างค่า select ทั้งหมดให้เป็น "ทั้งหมด"
                //     $('.filter-card select').each(function() {
                //         $(this).prop('selectedIndex', 0).val('').trigger('change');
                //     });

                //     // ล้างกล่องค้นหา + วาดใหม่
                //     $('#custom-search').val('');
                //     table.search('');
                //     table.columns().every(function() {
                //         this.search('');
                //     });
                //     table.page('first').draw(
                //         'page'); // จะเรียก updateSummary()/updateDonutAndLegend() ต่อเอง
                // });
                $(document).off('click.reset', '#reset-filters').on('click.reset', '#reset-filters', function(
                    e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // ล้างค่า select ทั้งหมด
                    $('.filter-card select').each(function() {
                        $(this).prop('selectedIndex', 0).val('').trigger('change');
                    });

                    // ล้างกล่องค้นหา
                    $('#custom-search').val('');
                    table.search('');
                    table.columns().every(function() {
                        this.search('');
                    });

                    // ✅ ล้าง filter ของ pie chart
                    window.selectedStatusFilter = null;

                    // ✅ ให้ DataTables redraw แล้วค่อยอัปเดต chart
                    table.page('first').draw(false);

                    // ใช้ one-time listener รอให้ draw เสร็จ
                    table.one('draw', function() {
                        updateSummary();
                        updateDonutAndLegend(); // ตอนนี้ค่าจะตรงแล้ว
                    });
                });

                let timer;
                $('#custom-search')
                    .on('input', function() {
                        clearTimeout(timer);
                        const val = this.value;
                        timer = setTimeout(() => {
                            table.search(val).draw();
                        }, 150);
                    })
                    .on('search', function() {
                        if (this.value === '') {
                            table.search('').draw();
                        }
                    });

                // ให้สรุป/กราฟอัปเดตทุกครั้งที่ DataTables คำนวณใหม่
                table.on('draw', function() {
                    updateSummary();
                    updateDonutAndLegend();
                    updateIndicatorTotal();
                });

                // 13) อัปเดตครั้งแรก
                updateSummary();
                updateDonutAndLegend();
                updateIndicatorTotal();
            });
        })(jQuery);
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
            margin-left: 60px;
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

        /* Toggle Switch Style */
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

        .chart-wrap {
            width: 480px;
            height: 320px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--gray-200);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .stat-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--gray-800);
            margin: 0;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-icon {
            background: var(--blue-100);
            color: var(--blue-600);
        }

        .stat-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .stat-number {
            font-size: 36px;
            font-weight: 700;
            color: var(--gray-900);
        }

        .stat-change {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 14px;
            font-weight: 500;
        }

        .stat-change.positive {
            color: var(--green-500);
        }

        .stat-details {
            margin-bottom: 16px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
            color: var(--gray-600);
        }

        .detail-item:not(:last-child) {
            border-bottom: 1px solid var(--gray-100);
        }

        .stat-footer {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        /* Satisfaction Card */
        .satisfaction-content {
            text-align: center;
        }

        .satisfaction-year {
            font-size: 18px;
            color: var(--gray-600);
            margin-bottom: 8px;
        }

        .satisfaction-score {
            font-size: 48px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 24px;
        }

        .satisfaction-score .divider {
            color: var(--gray-400);
            margin: 0 8px;
        }

        .satisfaction-chart {
            display: flex;
            align-items: center;
            gap: 24px;
            justify-content: center;
            margin-bottom: 16px;
        }

        .pie-chart {
            position: relative;
            width: 120px;
            height: 120px;
        }

        .chart-legend {
            text-align: left;
        }



        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 2px;
        }

        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .chart-card {
            background: #fff;
            border: 1px solid #eef2f7;
            border-radius: 18px;
            box-shadow: 0 10px 28px rgba(0, 0, 0, .08);
            padding: 18px 18px 20px;
            min-height: 500px;
            display: flex;
            flex-direction: column;
        }

        .chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            /* ดันปุ่มไปขวา */
            gap: 12px;
            margin-bottom: 16px;
        }

        .chart-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            color: #111827
        }

        .btn-export {
            background: #f8fff9;
            color: #16a34a;
            border: 2px solid #86efac;
            padding: 8px 14px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 1px 0 rgba(0, 0, 0, .02);
        }

        .btn-export:hover {
            background: #ecffef
        }

        .btn-export-excel {
            margin-top: 20px;
            margin-right: 20px;
            background: #f8fff9;
            color: #16a34a;
            border: 2px solid #86efac;
            padding: 8px 14px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 1px 0 rgba(0, 0, 0, .02);
        }

        .btn-export-excel:hover {
            background: #ecffef
        }

        .chart-content {
            position: relative;
            height: 400px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
        }

        .chart-content canvas {
            max-width: 100%;
            max-height: 100%;
            width: auto !important;

            border-radius: 8px;
        }

        /* ปรับปรุงการแสดงผลของ chart */
        .charts-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 32px;
            margin-top: 24px;
        }

        /* เพิ่ม animation สำหรับ chart card */
        .chart-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .chart-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }

        /* Buttons */
        .btn-link {
            background: none;
            border: none;
            color: var(--blue-600);
            font-size: 14px;
            cursor: pointer;
            padding: 8px 16px;
            border-radius: 6px;
            transition: background-color 0.2s;
        }

        .btn-link:hover {
            background: var(--blue-50);
        }

        .btn-primary-small {
            background: var(--blue-600);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-primary-small:hover {
            background: var(--blue-700);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 16px;
            }

            .chart-card {
                min-height: 400px;
                padding: 16px;
            }

            .chart-content {
                height: 300px;
            }

            .chart-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .btn-export {
                align-self: flex-end;
                font-size: 11px;
                padding: 6px 12px;
            }

            .stat-title h3 {
                font-size: 18px;
            }
        }

        @media (max-width: 480px) {
            .chart-card {
                min-height: 350px;
                padding: 12px;
            }

            .chart-content {
                height: 250px;
            }

            .btn-export {
                font-size: 10px;
                padding: 5px 10px;
            }

            .stat-title h3 {
                font-size: 16px;
            }
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
            .dashboard-header h1 {
                font-size: 24px;
            }

            .stat-number {
                font-size: 28px;
            }

            .satisfaction-score {
                font-size: 36px;
            }
        }

        /* ขนาด/สีไอคอน */
        .status-icon {
            width: 20px;
            height: 20px;
            vertical-align: middle;
        }

        .text-success {
            color: #22c55e;
        }

        .text-danger {
            color: #ef4444;
        }

        .text-warn {
            color: #facc15;
        }

        /* แถวสถานะให้จัดกลางนิด ๆ */
        .status-cell {
            text-align: center;
            /* จัดกึ่งกลางเหมือน cell ปกติ */
            vertical-align: middle;
            /* ให้ icon อยู่ตรงกลางแนวตั้ง */
            padding: 0.5rem;
            /* ระยะห่างเท่า cell อื่น */
        }

        .status-icon {
            width: 18px;
            height: 18px;
            display: inline-block;
            /* ให้เป็น inline-block ไม่ขยาย cell */
            vertical-align: middle;
        }

        /* Tooltip (CSS only) */
        .tip {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        /* กล่องข้อความ */
        .tip[data-tip]::after {
            content: attr(data-tip);
            position: absolute;
            left: 50%;
            bottom: calc(100% + 10px);
            /* วางเหนือไอคอน */
            transform: translateX(-50%) translateY(4px);
            white-space: nowrap;

            /* โทนสบายตา */
            background: #fff;
            color: #334155;
            /* slate-700 */
            border: 1px solid #e5e7eb;
            /* gray-200 */
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 12px;
            line-height: 1.2;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .08);

            opacity: 0;
            pointer-events: none;
            transition: opacity .16s ease, transform .16s ease;
            z-index: 50;
        }

        /* ลูกศร */
        .tip[data-tip]::before {
            content: "";
            position: absolute;
            left: 50%;
            bottom: calc(100% + 6px);
            transform: translateX(-50%);
            width: 8px;
            height: 8px;
            background: #fff;
            border-left: 1px solid #e5e7eb;
            border-top: 1px solid #e5e7eb;
            transform: translateX(-50%) rotate(45deg);
            box-shadow: 0 2px 4px rgba(0, 0, 0, .06);
            opacity: 0;
            transition: opacity .16s ease;
            z-index: 49;
        }

        /* แสดงเมื่อ hover หรือโฟกัส (รองรับคีย์บอร์ด) */
        .tip:hover::after,
        .tip:hover::before,
        .tip:focus-visible::after,
        .tip:focus-visible::before {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* ตัวเลือก: วาง tooltip ด้านล่าง (ถ้าพื้นที่ด้านบนไม่พอ)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           <span class="tip" data-tip="..." data-pos="bottom"> */
        .tip[data-pos="bottom"]::after {
            top: calc(100% + 10px);
            bottom: auto;
        }

        .tip[data-pos="bottom"]::before {
            top: calc(100% + 6px);
            bottom: auto;
            border-left: 1px solid #e5e7eb;
            border-top: 1px solid #e5e7eb;
        }
    </style>
    <style>
        :root {
            --card-radius: 18px;
            --shadow: 0 10px 28px rgba(0, 0, 0, .08);
            --border: #eef2f7;
            --title: #111827;
            --muted: #6b7280;
            --excel: #16a34a;
            --gray-100: #f3f4f6;
            --gray-600: #4b5563;
            --gray-800: #1f2937;
            --white: #ffffff;
            --shadow: 0 2px 6px rgba(0, 0, 0, .08);
        }

        .score-card {
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 20px 24px;
            /* max-width: 400px; */
            margin: 16px auto;
            border: 1px solid var(--gray-100);
        }

        .score-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            font-size: 16px;
        }

        .score-header .label {
            color: var(--gray-600);
            font-weight: 500;
        }

        .score-header .year {
            font-weight: 700;
            color: var(--gray-800);
            font-size: 18px;
        }

        .score-body {
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            text-align: center;
            margin-top: 12px;
        }

        .score-body .label-left,
        .score-body .label-right {
            font-size: 14px;
            color: var(--gray-600);
        }

        .score-value {
            font-size: 40px;
            font-weight: 800;
            color: var(--gray-800);
        }

        .score-value .divider {
            margin: 0 8px;
            color: var(--gray-600);
            font-weight: 400;
        }

        .stat-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--card-radius);
            box-shadow: var(--shadow);
            padding: 18px 18px 20px;

            margin-top: 15px;

        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 14px;
        }

        .stat-title {
            display: flex;
            align-items: baseline;
            gap: 10px;
            flex-wrap: wrap
        }

        .stat-title h3 {
            margin-top: 20px;
            font-size: 20px;
            font-weight: 800;
            color: var(--title)
        }

        .stat-title .sub {
            color: #94a3b8;
            font-size: 13px
        }

        .btn-export {
            background: #f8fff9;
            color: var(--excel);
            border: 2px solid #86efac;
            padding: 8px 14px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 1px 0 rgba(0, 0, 0, .02);
        }

        .btn-export:hover {
            background: #ecffef
        }

        .stat-body {
            display: flex;
            gap: 28px;
            align-items: center;
        }

        .chart-wrap {
            padding: 6px 10px
        }

        .legend-wrap {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 12px
        }


        .legend-left {
            display: flex;
            gap: 10px;
            align-items: center
        }

        .dot {
            width: 14px;
            height: 14px;
            border-radius: 50%
        }

        .legend-text .label {
            font-size: 14px;
            color: #1f2937;
            font-weight: 600
        }

        .legend-text .subtext {
            font-size: 12px;
            color: var(--muted);
            margin-top: 2px
        }



        @media (max-width: 820px) {
            .stat-body {
                flex-direction: column;
                align-items: stretch
            }

            .legend-right {
                min-width: unset
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

        .stats-card {
            display: flex;
            align-items: center;
            gap: 16px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 14px;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .stats-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            border-radius: 8px;
            background: #f9fafb;
            color: #374151;
            /* default */
        }

        .stats-icon.success {
            color: #16a34a;
        }

        /* เขียว */
        .stats-icon.warn {
            color: #f59e0b;
        }

        /* เหลือง/ส้ม */
        .stats-icon.danger {
            color: #ef4444;
        }

        /* แดง */

        .stats-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .stats-value {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
        }

        .stats-label {
            font-size: 13px;
            color: #6b7280;
        }
    </style>
    <style>
        :root {
            --blue-600: #2563eb;
            --blue-700: #1d4ed8;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-700: #374151;
            --ring: #3b82f6;
            --white: #fff;
            --shadow: 0 1px 2px rgba(0, 0, 0, .06), 0 1px 3px rgba(0, 0, 0, .1);
            --radius: 8px;
            --gap-2: 8px;
            --gap-3: 12px;
            --pad-2: 8px;
            --pad-3: 12px;
            --pad-4: 16px;
        }


        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600
        }

        .badge-success {
            background: #dcfce7;
            color: #166534
        }

        .badge-warn {
            background: #fef3c7;
            color: #92400e
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b
        }

        .badge-muted {
            background: #e5e7eb;
            color: #374151
        }


        /* ตาราง */
        .containers {
            width: 100%;
            max-width: 1500px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-top: 20px;
        }

        .dashboard-list {
            background: white;
            border-radius: 10px;
            padding: 30px;
            border: 2px solid #C2D9EB;
            margin-top: 40px;
            margin-bottom: 40px;
            margin-left: 60px;
            margin-right: 60px;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            text-align: left;
            font-weight: 600;
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
            padding: 12px;
        }

        .table tbody td {
            padding: 12px;
            border-bottom: 1px solid var(--gray-200);
        }





        /* Action buttons */
        .dashboard-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-download,
        .btn-edit,
        .btn-delete {
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid var(--gray-300);
            background: var(--white);
            cursor: pointer;
            font-size: 12px;
            white-space: nowrap;
        }

        .btn-download {
            color: #059669;
            border-color: #059669;
        }

        .btn-download:hover {
            background: #ecfdf5;
        }



        /* Responsive */
        @media (min-width: 768px) {
            .controls {
                flex-wrap: nowrap;
            }

            .controls .search-box {
                flex: 1 1 420px;
                max-width: none;
            }

            #add-dashboard-button {
                margin-left: auto;
            }
        }

        .controls>* {
            flex-shrink: 0;
        }

        /* Table responsive */
        @media (max-width: 768px) {
            .dashboard-actions {
                flex-direction: column;
            }

            .table {
                font-size: 12px;
            }

            .dashboard-list {
                margin-left: 20px;
                margin-right: 20px;
                padding: 20px;
            }
        }

        i[data-lucide] {
            display: inline-block;
            vertical-align: middle;
        }
    </style>

@endsection
