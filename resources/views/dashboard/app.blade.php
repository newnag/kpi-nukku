@extends('layouts.app')
@section('title', 'แดชบอร์ด')
@section('content')

    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <h1>
                แดชบอร์ด
                <span class="subtitle">/ ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล</span>
            </h1>
        </div>
        <!-- ===== Card: กรองข้อมูลการประเมิน ===== -->
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
                    <label>หน่วยงานที่รับผิดชอบ</label>
                    <select id="filter-dept">
                        <option value="">ทั้งหมด</option>
                    </select>
                </div>

                <div class="field">
                    <label>ผู้รับผิดชอบในการรวบรวมข้อมูล</label>
                    <select id="filter-collector">
                        <option value="">ทั้งหมด</option>
                    </select>
                </div>
            </div>

            <div class="card-actions">
                <button type="button" id="reset-filters" class="btn btn-outline">ล้างค่า</button>
                <button type="button" id="apply-filters" class="btn btn-primary">กรองข้อมูล</button>
            </div>
        </div>

        <!-- Score Card -->

        <div class="stat-title">
            <h3> คะแนนทั้งหมดที่ได้ในแต่ละปี</h3>

        </div>
        <div class="score-card">
            <div class="score-header">
                <span class="label">ปีการประเมิน</span>
                <span class="year">{{ $year ?? 'ทั้งหมด' }}</span>
            </div>
            <hr />
            <div class="score-body">
                <span class="label-left">คะแนนที่ได้</span>
                <div class="score-value">
                    {{ number_format($totalScore) }}
                    <span class="divider">/</span>
                    {{ number_format($maxScore) }}
                </div>
                <span class="label-right">คะแนนเต็ม</span>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stat-title">
            <h3>สถานะทั้งหมดของตัวชี้วัดต่อปี</h3>
            <span class="sub">(04/08/2025 up to date)</span>
        </div>
        <div class="stats-grid">

            <!-- Card  ความพึงพอใจ -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">

                    </div>

                    <button class="btn-export">
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
                        EXPORT TO EXCEL
                    </button>
                </div>

                <div class="stat-body">
                    <div class="chart-wrap">
                        <canvas id="satisfactionChart" width="260" height="260"></canvas>
                    </div>

                    <div class="legend-wrap">
                        <div class="legend-item">
                            <div class="legend-left">
                                <span class="dot" style="background:#22c55e"></span>
                                <div class="legend-text">
                                    <div class="label">ผลการดำเนินการครบถ้วนตามเกณฑ์มาตรฐาน</div>
                                    <div class="subtext"><strong>15</strong> indicator</div>
                                </div>
                            </div>
                            <div class="legend-right">
                                <div class="bar" style="background:#22c55e"></div>
                                <div class="pct">26.79%</div>
                            </div>
                        </div>

                        <div class="legend-item">
                            <div class="legend-left">
                                <span class="dot" style="background:#f59e0b"></span>
                                <div class="legend-text">
                                    <div class="label">อยู่ระหว่างดำเนินการ</div>
                                    <div class="subtext"><strong>35</strong> indicator</div>
                                </div>
                            </div>
                            <div class="legend-right">
                                <div class="bar" style="background:#f59e0b"></div>
                                <div class="pct">62.50%</div>
                            </div>
                        </div>

                        <div class="legend-item">
                            <div class="legend-left">
                                <span class="dot" style="background:#ef4444"></span>
                                <div class="legend-text">
                                    <div class="label">ผลการดำเนินงานยังไม่ครบถ้วนตามเกณฑ์</div>
                                    <div class="subtext"><strong>6</strong> indicator</div>
                                </div>
                            </div>
                            <div class="legend-right">
                                <div class="bar" style="background:#ef4444"></div>
                                <div class="pct">10.71%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-grid">
            <div class="stat-title">
                <h3>กราฟคะแนน 3 มาตรฐานตัวชี้วัด 5 ปีย้อนหลัง 2020-2024</h3>

            </div>
            <!-- Chart 1: การเข้าชม 5 ปีย้อนหลัง 2020-2024 -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3></h3>
                    <button class="btn-export">
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
                <div class="chart-content">
                    <canvas id="visitsChart" width="400" height="200"></canvas>
                </div>
            </div>
            <div class="stat-title">
                <h3>กราฟคะแนน 7 ด้านการประเมิน ตามปี 2020-2024</h3>

            </div>
            <!-- Chart 2: สถิติเข้าชม 7 วันย้อนหลัง 2020-2024 -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3></h3>
                    <button class="btn-export">
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

                <div class="chart-content">
                    <canvas id="score7Chart"></canvas>
                </div>
            </div>

        </div>
        <!-- ตารางเอกสารและหลักฐาน -->
        <div class="dashboard-containers">
            <div class="dashboard-list">
                <table class="table" id="dashboardTable">
                    <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>ปีการประเมิน</th>
                            <th>ชื่อตัวบ่งชี้</th>
                            <th>รหัส</th>
                            <th>ประเภทตัวชี้วัด</th>
                            <th>หน่วยงานที่รับผิดชอบ</th>
                            <th>ผลลัพธ์</th>
                            <th>คะแนนรวม</th>
                            <th>สถานะตัวชี้วัด</th>
                            <th>สถานะเอกสาร</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($indicators as $index => $indicator)
                            <tr data-standard="{{ $indicator->category->standard->name ?? '' }}"
                                data-dimension="{{ $indicator->category->name ?? '' }}"
                                data-collector="{{ $indicator->assignments->first()->collectorUser->name ?? '' }}">
                                <td class="status-cell">{{ $index + 1 }}</td>
                                <td class="status-cell">{{ $indicator->year }}</td>
                                <td class="status-cell">{{ $indicator->name }}</td>
                                <td class="status-cell">{{ $indicator->code }}</td>
                                <td class="status-cell">{{ $indicator->type }}</td>

                                <td class="status-cell">
                                    @foreach ($indicator->assignments as $as)
                                        {{ optional($as->collectorUser?->department)->name ?? '-' }}
                                    @endforeach
                                </td>

                                <td class="status-cell">{{ $indicator->score_acc }}</td>
                                <td class="status-cell">{{ $indicator->max_score }}</td>
                                <td class="status-cell">
                                    @switch($indicator->status)
                                        @case(0)
                                            <span class="tip" data-tip="อยู่ระหว่างดำเนินการ"
                                                aria-label="อยู่ระหว่างดำเนินการ" tabindex="0">
                                                <i data-lucide=" alert-triangle " class="status-icon text-danger"></i>
                                            </span>
                                        @break

                                        @case(1)
                                            <span class="tip" data-tip="ผลการดำเนินงานยังไม่ครบถ้วนตามเกณฑ์"
                                                aria-label="ผลการดำเนินงานยังไม่ครบถ้วนตามเกณฑ์" tabindex="0">
                                                <i data-lucide="clock" class="status-icon text-warn"></i>
                                            </span>
                                        @break

                                        @case(3)
                                            <span class="tip" data-tip="ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรฐาน"
                                                aria-label="ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรฐาน" tabindex="0">
                                                <i data-lucide="check-circle" class="status-icon text-success"></i>
                                            </span>
                                        @break
                                    @endswitch
                                </td>

                                <td class="status-cell">
                                    {{ $indicator->status_doc }}
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            // ปลอดภัยไว้ก่อน: ถ้าไม่ได้โหลด lucide ให้ข้าม
            if (window.lucide?.createIcons) lucide.createIcons();

            // ===== Doughnut: Satisfaction =====
            const donutCanvas = document.getElementById('satisfactionChart');
            if (donutCanvas) {
                const donutCtx = donutCanvas.getContext('2d');
                const dataValues = [15, 35, 6];
                const colors = ["#22c55e", "#f59e0b", "#ef4444"];

                new Chart(donutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ["ครบถ้วน", "ระหว่างดำเนินการ", "ยังไม่ครบถ้วน"],
                        datasets: [{
                            data: dataValues,
                            backgroundColor: colors,
                            borderColor: "#ffffff",
                            borderWidth: 4,
                            hoverOffset: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: (context) => {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const val = context.parsed;
                                        const pct = (val / total * 100).toFixed(2);
                                        return ` ${val} (${pct}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // ===== Visits (ตัวอย่าง 3 ชุดข้อมูล) =====
            const visitsCanvas = document.getElementById('visitsChart');
            if (visitsCanvas) {
                const visitsCtx = visitsCanvas.getContext('2d');
                new Chart(visitsCtx, {
                    type: 'bar',
                    data: {
                        labels: ['ปี 2020', 'ปี 2021', 'ปี 2022', 'ปี 2023', 'ปี 2024'],
                        datasets: [{
                                label: 'มาตราฐานโครงสร้าง',
                                data: [120, 150, 180, 220, 190],
                                backgroundColor: '#8979FF'
                            },
                            {
                                label: 'มาตรฐานกระบวนการ',
                                data: [80, 110, 140, 170, 160],
                                backgroundColor: '#FF928A'
                            },
                            {
                                label: 'มาตรฐานผลลัพธ์',
                                data: [40, 60, 80, 100, 85],
                                backgroundColor: '#3CC3DF'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            // ===== 7 ด้านตามปี 2020–2024 =====
            const score7Canvas = document.getElementById('score7Chart');
            if (score7Canvas) {
                const score7Ctx = score7Canvas.getContext('2d');
                const years = ['2020', '2021', '2022', '2023', '2024'];
                const palette = {
                    org: '#6366f1',
                    staff: '#06b6d4',
                    edu: '#10b981',
                    research: '#f59e0b',
                    service: '#3b82f6',
                    culture: '#22c55e',
                    student: '#a78bfa'
                };
                const datasets = [{
                        label: 'ด้านองค์กรและบริหารองค์กร',
                        data: [97.63, 63.25, 82.93, 66.10, 79.98],
                        backgroundColor: palette.service
                    },
                    {
                        label: 'ด้านบุคลากร',
                        data: [34.44, 59.93, 25.87, 78.12, 63.10],
                        backgroundColor: palette.staff
                    },
                    {
                        label: 'ด้านการจัดการศึกษา',
                        data: [16.90, 42.92, 47.00, 73.51, 61.57],
                        backgroundColor: palette.edu
                    },
                    {
                        label: 'ด้านวิจัยและนวัตกรรมและผลผลิตทางวิชาการ',
                        data: [25.26, 91.58, 19.71, 67.66, 38.00],
                        backgroundColor: palette.research
                    },
                    {
                        label: 'ด้านการบริการวิชาการ/วิชาชีพแก่สังคม',
                        data: [97.70, 59.89, 52.04, 57.75, 84.66],
                        backgroundColor: palette.service
                    },
                    {
                        label: 'ด้านการทำนุบำรุงศิลปะและวัฒนธรรม',
                        data: [68.32, 76.79, 18.89, 51.72, 80.58],
                        backgroundColor: palette.culture
                    },
                    {
                        label: 'ด้านนิสิต/นักศึกษา',
                        data: [24.00, 23.83, 37.00, 31.59, 30.06],
                        backgroundColor: palette.student
                    },
                ];

                // ปลั๊กอินแสดงค่าบนแท่ง
                const valueLabelPlugin = {
                    id: 'valueLabel',
                    afterDatasetsDraw(chart) {
                        const {
                            ctx
                        } = chart;
                        ctx.save();
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';
                        ctx.fillStyle = '#334155';
                        ctx.font = '500 11px ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto';
                        chart.data.datasets.forEach((ds, di) => {
                            const meta = chart.getDatasetMeta(di);
                            meta.data.forEach((bar, i) => {
                                const v = ds.data[i];
                                if (v == null) return;
                                const {
                                    x,
                                    y
                                } = bar.tooltipPosition();
                                ctx.fillText(Number(v).toFixed(2), x, y - 6);
                            });
                        });
                        ctx.restore();
                    }
                };

                new Chart(score7Ctx, {
                    type: 'bar',
                    data: {
                        labels: years,
                        datasets: datasets.map(d => ({
                            ...d,
                            borderRadius: 6,
                            barThickness: 'flex',
                            maxBarThickness: 26,
                        }))
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        scales: {
                            x: {
                                ticks: {
                                    color: '#64748b',
                                    font: {
                                        size: 12
                                    }
                                },
                                grid: {
                                    display: true,
                                    color: '#f1f5f9'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                suggestedMax: 100,
                                ticks: {
                                    stepSize: 20,
                                    color: '#64748b',
                                    font: {
                                        size: 12
                                    }
                                },
                                grid: {
                                    color: '#eef2f7'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'rectRounded',
                                    boxWidth: 10,
                                    font: {
                                        size: 12
                                    },
                                    color: '#334155'
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: (c) => `${c.dataset.label}: ${Number(c.parsed.y).toFixed(2)}`
                                }
                            }
                        }
                    },
                    plugins: [valueLabelPlugin]
                });
            }


        });
    </script>
    <script>
        (function($) {
            let table;

            const stripHtml = (s) => {
                const d = document.createElement('div');
                d.innerHTML = String(s ?? '');
                return (d.textContent || d.innerText || '').trim();
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
                        info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                        emptyTable: "ไม่พบข้อมูล",
                        zeroRecords: "ไม่พบข้อมูลที่ตรงกับการค้นหา"
                    }
                });

                // 2) หาคอลัมน์จริง
                const heads = $('#dashboardTable thead th').map((i, th) => $(th).text().trim()).get();
                const findCol = (cands) => {
                    for (const kw of cands) {
                        const idx = heads.findIndex(h => h.includes(kw));
                        if (idx !== -1) return idx;
                    }
                    return -1;
                };

                const idxYear = findCol(['ปีการประเมิน']);
                const idxCode = findCol(['รหัส']);
                const idxDept = findCol(['หน่วยงานที่รับผิดชอบ']);

                // 3) Select element
                const $year = $('#filter-year'),
                    $code = $('#filter-code'),
                    $std = $('#filter-standard'),
                    $dim = $('#filter-dimension'),
                    $dept = $('#filter-dept'),
                    $collector = $('#filter-collector');

                // 4) เติม option จากคอลัมน์จริง
                const populateFromColumn = ($sel, colIdx) => {
                    $sel.find('option:not([value=""])').remove();
                    if (colIdx === -1) return;
                    const vals = table.column(colIdx).data().toArray().map(stripHtml).filter(Boolean);
                    const uniq = [...new Set(vals)].sort((a, b) => a.localeCompare(b, 'th'));
                    uniq.forEach(v => $sel.append(`<option value="${v}">${v}</option>`));
                };

                populateFromColumn($year, idxYear);
                populateFromColumn($code, idxCode);
                populateFromColumn($dept, idxDept);

                // 5) เติม option จาก data-* (มาตรฐาน, ด้าน, ผู้รับผิดชอบ)
                const populateFromData = ($sel, attr) => {
                    $sel.find('option:not([value=""])').remove();
                    const vals = [];
                    $('#dashboardTable tbody tr').each(function() {
                        const v = $(this).data(attr);
                        if (v) vals.push(v);
                    });
                    const uniq = [...new Set(vals)].sort((a, b) => a.localeCompare(b, 'th'));
                    uniq.forEach(v => $sel.append(`<option value="${v}">${v}</option>`));
                };

                populateFromData($std, 'standard');
                populateFromData($dim, 'dimension');
                populateFromData($collector, 'collector');

                // 6) ฟังก์ชันกรอง
                const applyFilters = () => {
                    table.rows().every(function() {
                        let show = true;
                        const row = this.node();

                        const vYear = $year.val();
                        const vCode = $code.val();
                        const vStd = $std.val();
                        const vDim = $dim.val();
                        const vDept = $dept.val();
                        const vCollector = $collector.val();

                        if (vYear && stripHtml(this.data()[idxYear]) != vYear) show = false;
                        if (vCode && stripHtml(this.data()[idxCode]) != vCode) show = false;
                        if (vDept && stripHtml(this.data()[idxDept]) != vDept) show = false;

                        if (vStd && row.dataset.standard != vStd) show = false;
                        if (vDim && row.dataset.dimension != vDim) show = false;
                        if (vCollector && row.dataset.collector != vCollector) show = false;

                        if (show) $(row).show();
                        else $(row).hide();
                    });
                };

                // 7) bind event
                $('#apply-filters').on('click', applyFilters);
                $('.filter-card select').on('change', applyFilters);

                $('#reset-filters').on('click', function() {
                    $('.filter-card select').val('');
                    $('#dashboardTable tbody tr').show();
                });

                // 8) ช่องค้นหาอิสระ
                let timer;
                $('#custom-search')
                    .on('input', function() {
                        clearTimeout(timer);
                        const val = this.value;
                        timer = setTimeout(() => table.search(val).draw(), 150);
                    })
                    .on('search', function() {
                        if (this.value === '') table.search('').draw();
                    });
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

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
        }

        .dashboard-header {
            margin-bottom: 32px;
        }

        .dashboard-header h1 {
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

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            font-size: 12px;
            color: var(--gray-600);
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

        }

        .chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 14px;
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

        .chart-content {
            position: relative;
            height: 420px;
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
            .dashboard-container {
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

        .legend-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #e5e7eb;
        }

        .legend-item:last-child {
            border-bottom: none
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

        .legend-right {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 160px
        }

        .legend-right .bar {
            width: 70px;
            height: 6px;
            border-radius: 999px;
            opacity: .9
        }

        .legend-right .pct {
            font-weight: 700;
            color: #334155;
            min-width: 64px;
            text-align: right
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
        .dashboard-containers {
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
