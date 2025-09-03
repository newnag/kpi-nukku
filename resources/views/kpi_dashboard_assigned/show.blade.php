@extends('layouts.app')
@section('title', 'แดชบอร์ด')
@section('content')
    <div class="dashboard-container">
        <div class="card indicator-card">
            <!-- ชื่อหัวข้อ -->
            <h1 class="indicator-title">
                {{ $indicator->name }} ({{ $indicator->code }})
            </h1>

            <!-- Tabs -->
            <div class="indicator-tabs">
                <span class="tab ">{{ $indicator->category->standard->name ?? '-' }}</span>
                <span class="tab-divider">|</span>
                <span class="tab">{{ $indicator->category->name ?? '-' }}</span>
            </div>
            <hr class="tab-divider">



            <!-- ข้อมูล -->
            <div class="info-block">
                <div class="info-row">
                    <span class="label">หน่วยงานที่รับผิดชอบ:</span>
                    <span class="value">
                        @forelse($indicator->assignments as $assignment)
                            @if ($assignment->collectorUser)
                                <span class="chip">
                                    {{ $assignment->collectorUser->department->name }}

                                </span>
                            @endif
                        @empty
                            <span class="value">-</span>
                        @endforelse
                    </span>
                </div>

                <div class="info-row">
                    <span class="label">ผู้รับผิดชอบในการรวบรวม:</span>
                    @forelse($indicator->assignments as $assignment)
                        @if ($assignment->collectorUser)
                            <span class="chip">
                                {{ $assignment->collectorUser->name }}

                            </span>
                        @endif
                    @empty
                        <span class="value">-</span>
                    @endforelse
                </div>

                <div class="info-row">
                    <span class="label">สถานะตัวชี้วัด:</span>
                    @if ($indicator->status == 0)
                        <span class="status-chip orange">รอดำเนินการ</span>
                    @elseif ($indicator->status == 1)
                        <span class="status-chip gray">บันทึกร่าง</span>
                    @elseif ($indicator->status == 2)
                        <span class="status-chip blue">บันทึกจริง</span>
                    @elseif ($indicator->status == 3)
                        <span class="status-chip green">ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรฐาน</span>
                    @elseif ($indicator->status == 4)
                        <span class="status-chip red">ผลการดำเนินงานไม่ครบถ้วนตามเกณฑ์มาตรฐาน</span>
                    @else
                        <span class="status-chip">ไม่ทราบสถานะ</span>
                    @endif
                </div>
            </div>
            <hr class="section-divider">

            <div class="card ">
                <h2 class="card-title">คำอธิบายตัวชี้วัด</h2>
                <div class="description-box">
                    {!! $indicator->description ?? '-' !!}
                </div>
            </div>
            <div class="card">
                <h2 class="card-title">เกณฑ์การพิจารณา</h2>

                @forelse($indicator->criterias as $criteria)
                    <div class="criteria-box">
                        <!-- ชื่อเกณฑ์ -->
                        <div class="criteria-header">
                            <div class="criteria-title">
                                {{ $criteria->sequence }}. {!! $criteria->name !!}
                            </div>
                            <div class="criteria-actions">
                                <a href="{{ route('evidences.create', $criteria->id) }}" class="btn-add">
                                    เพิ่มหลักฐาน <i class="fa fa-upload"></i>
                                </a>

                                <div class="dropdown">
                                    <button @readonly(true) class="dropdown-toggle" onclick="toggleDropdown(this)">
                                        <span class="selected-text">รอดำเนินการ</span>

                                    </button>
                                    <ul class="dropdown-menu">
                                        <li onclick="selectOption(this, 'รอดำเนินการ')">รอดำเนินการ</li>
                                        <li onclick="selectOption(this, 'ครบ')">ครบ</li>
                                        <li onclick="selectOption(this, 'ไม่ครบ')">ไม่ครบ</li>
                                    </ul>
                                </div>



                            </div>


                        </div>

                        <!-- คำอธิบายเกณฑ์ -->
                        @if ($criteria->description)
                            <div class="criteria-description">
                                {!! $criteria->description !!}
                            </div>
                        @endif


                    </div>
                @empty
                    <p class="text-gray-500">ยังไม่มีเกณฑ์การพิจารณา</p>
                @endforelse

                <!-- หลักฐาน -->
                <div class="evidence-list">
                    @forelse($criteria->evidences as $evidence)
                        @php
                            $type = strtolower($evidence->type ?? '');
                            $name = strtolower($evidence->name ?? '');
                        @endphp

                        <div class="evidence-item">
                            <span class="evidence-icon">
                                @if (Str::endsWith($type, 'pdf'))
                                    <i data-lucide="file-text" style="color:#dc2626;"></i>
                                @elseif (Str::endsWith($type, 'doc') || Str::endsWith($type, 'docx') || Str::endsWith($name, '.docx'))
                                    <i data-lucide="file-text" style="color:#2563eb;"></i>
                                @elseif (Str::endsWith($type, 'ppt') || Str::endsWith($type, 'pptx') || Str::endsWith($name, '.pptx'))
                                    <i data-lucide="presentation" style="color:#eb7e25;"></i>
                                @elseif (in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'image']))
                                    <i data-lucide="image" style="color:#16a34a;"></i>
                                @elseif (Str::endsWith($type, 'xls') || Str::endsWith($type, 'xlsx') || Str::contains($name, '.xls'))
                                    <i data-lucide="file-spreadsheet" style="color:#059669;"></i>
                                @elseif ($type === 'url')
                                    <i data-lucide="link" style="color:#9333ea;"></i>
                                @elseif ($type === 'note')
                                    <i data-lucide="sticky-note" style="color:#f59e0b;"></i>
                                @else
                                    <i data-lucide="file" style="color:#6b7280;"></i>
                                @endif
                            </span>

                            <span class="evidence-name">
                                {{ $evidence->name }}
                            </span>

                            <button class="btn-delete" data-id="{{ $evidence->id }}" title="ลบหลักฐาน">
                                x
                            </button>

                        </div>
                    @empty
                        <div class="evidence-empty">ยังไม่มีหลักฐานแนบ</div>
                    @endforelse
                </div>

            </div>

        </div>
    </div>
    </div>
    <script>
        lucide.createIcons();

        function toggleDropdown(button) {
            const dropdown = button.parentElement;
            dropdown.querySelector(".dropdown-menu").classList.toggle("show");
        }

        function selectOption(el, value) {
            const dropdown = el.closest(".dropdown");
            dropdown.querySelector(".selected-text").innerText = value;
            dropdown.querySelector(".dropdown-menu").classList.remove("show");
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute("content") : "";

            document.querySelectorAll(".btn-delete").forEach(btn => {
                btn.addEventListener("click", function() {
                    const id = this.getAttribute("data-id");
                    if (!confirm("คุณแน่ใจหรือไม่ที่จะลบหลักฐานนี้?")) return;

                    fetch(`/evidences/${id}`, {
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": csrfToken,
                                "Accept": "application/json"
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                const element = document.getElementById(`evidence-${id}`);
                                if (element) element.remove();

                                // ถ้าไม่เหลือ evidence แล้ว → โชว์ข้อความว่าง
                                if (document.querySelectorAll('.evidence-item').length === 0) {
                                    const container = document.querySelector('.evidence-list');
                                    container.innerHTML =
                                        '<div class="evidence-empty">ยังไม่มีหลักฐานแนบ</div>';
                                }
                            } else {
                                alert("เกิดข้อผิดพลาด: " + (data.message || "ไม่สามารถลบได้"));
                            }
                        })
                        .catch(err => console.error(err));
                });
            });
        });
    </script>

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
            margin: 16px;
            border: 1px solid #f3f4f6;
        }

        .card-title {
            font-size: 18px;
            /* font-weight: 700; */
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

        .section-divider {
            position: relative;
            left: -29px;
            width: calc(100% + 57px);
            /* right: 30px; */
            border: none;
            border-bottom: 3px solid #C3D8E8;
            /* เทาอ่อน */
            margin: 24px 0;
        }

        .description-box {
            background: #f9fafb;
            /* gray-50 */
            border: 1px solid #e5e7eb;
            /* gray-200 */
            border-radius: 12px;
            padding: 16px 20px;
            font-size: 14px;
            line-height: 1.7;
            color: #374151;
            /* gray-800 */
        }

        .description-box p {
            margin-bottom: 12px;
        }

        .description-box ul {
            margin: 8px 0 8px 20px;
            list-style: disc;
        }

        .criteria-box {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .criteria-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .criteria-title {
            font-weight: 600;
            font-size: 14px;
            color: #1f2937;
        }

        .criteria-description {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 12px;
        }

        .criteria-actions {
            display: flex;
            align-items: stretch;
            gap: 8px;
            flex-direction: column;
        }

        .btn-add {
            background: #EBF7FF;
            border: 1px solid #398ECA;
            border-radius: 20px;
            font-size: 12px;
            padding: 6px 12px;
            cursor: pointer;
            color: #398ECA;
            text-decoration: none;
            /* กันไม่ให้มีขีดเส้นใต้ */
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }

        .btn-add:hover {
            background: #dbeafe;
        }

        .btn-delete {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #dc2626;
            /* แดงอ่อน */
            transition: color 0.2s, transform 0.1s;
        }

        .btn-delete:hover {
            color: #b91c1c;
            /* แดงเข้ม */
            transform: scale(1.1);
        }

        .evidence-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .evidence-item {
            background: #f0f9ff;
            border-radius: 8px;
            padding: 6px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            color: #374151;
        }

        .evidence-icon {
            margin-right: 6px;
        }

        .dropdown {
            position: relative;
            display: inline-block;

        }

        .dropdown-toggle {
            background: #ffffff;
            border: 1.5px solid #398ECA;
            border-radius: 20px;
            padding: 6px 28px 6px 12px;
            font-size: 13px;
            color: #398ECA;
            cursor: pointer;
            outline: none;
            text-align: left;
            position: relative;
        }

        .dropdown-toggle .arrow {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 6px solid #398ECA;
            /* ลูกศรลง */
            pointer-events: none;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            list-style: none;
            padding: 6px 0;
            margin-top: 4px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            z-index: 9999;
            /* ป้องกันถูกบัง */
        }

        .dropdown-menu li {
            padding: 8px 12px;
            cursor: pointer;
            font-size: 13px;
            color: #374151;
            transition: background 0.2s;
        }

        .dropdown-menu li:hover {
            background: #EBF7FF;
            color: #398ECA;
        }

        .dropdown-menu.show {
            display: block;
        }

        .file-icon {
            flex-shrink: 0;
        }
    </style>
    <style>
        :root {
            --blue: #398ECA;
            --green: #22c55e;
            --orange: #fbbf24;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-600: #4b5563;
            --gray-800: #1f2937;
            --radius: 14px;
            --shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .dashboard-container {
            max-width: 960px;
            margin: 0 auto;
            padding: 24px;
        }

        /* Card */
        .card.indicator-card {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 28px;
            border: 1px solid var(--gray-100);
        }

        /* Title */
        .indicator-title {
            font-size: 26px;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 16px;
        }

        /* Tabs */
        .indicator-tabs {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .tab {
            color: #6b7280;
            /* gray-500 */
            cursor: default;
        }



        .tab-divider {
            color: #d1d5db;
            /* gray-300 */
        }

        hr.tab-divider {
            border: none;
            border-bottom: 2px solid #bbc8e0;
            margin: 0 0 16px 0;
        }


        /* Info block */
        .info-block {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .info-row {
            font-size: 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .info-row .label {
            font-weight: 600;
            color: #717d83;
            margin-right: 6px;
        }

        .info-row .value {
            color: var(--gray-600);
            display: inline-block;
            background: #EBF7FF;
            border-radius: 16px;
            font-size: 13px;
        }

        /* Chips */
        .chip {
            display: inline-block;
            background: #EBF7FF;

            border-radius: 16px;
            padding: 4px 12px;
            font-size: 13px;
            color: #858e95;
        }

        /* Status Chips */
        .status-chip {
            display: inline-block;
            border-radius: 16px;
            padding: 4px 12px;
            font-size: 13px;
            color: #fff;
        }

        /* โทนส้มพาสเทล */
        .status-chip.orange {
            background: #FFD1A8;
            color: #1f2937;
        }

        /* โทนเขียวพาสเทล */
        .status-chip.green {
            background: #A8FFBD;
            color: #1f2937;
        }

        /* โทนน้ำเงินพาสเทล */
        .status-chip.blue {
            background: #A8D4FF;
            color: #1f2937;
        }

        /* โทนเทาพาสเทล */
        .status-chip.gray {
            background: #E5E7EB;
            /* gray-200 */
            color: #1f2937;
        }

        .status-chip.red {
            background: #FFA8A8;
            /* red-500 */
            color: #858e95;
        }
    </style>
@endsection
