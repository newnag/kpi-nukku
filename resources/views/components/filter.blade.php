@props([
    'years' => [],
    'standards' => [],
    'departments' => [],
    'collectors' => [],
    'dimensions' => [],
    'action' => '#',
    'selectedYear' => null,
])

<div id="filter-card" class="filter-card card" style="display:none; margin-top:15px;">
    <h2 class="card-title">กรองข้อมูลการประเมิน</h2>

    <form id="filter-form" method="GET" action="{{ $action }}">
        <div class="form-grid">
            <!-- ปีการประเมิน -->
            <div class="field">
                <label>ปีการประเมิน</label>
                <select id="filter-year" name="year">
                    {{-- <option value="">ทั้งหมด</option> --}}
                    @foreach ($years as $y)
                        <option value="{{ $y }}"
                            {{ (string) $selectedYear === (string) $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- รหัสตัวชี้วัด -->
            <div class="field">
                <label>รหัสตัวชี้วัด</label>
                <select id="filter-code" name="code">
                    <option value="">ทั้งหมด</option>
                </select>
            </div>

            <!-- มาตรฐาน -->
            <!-- มาตรฐาน -->
            <div class="field">
                <label>มาตรฐานตัวชี้วัด</label>
                <select id="filter-standard" name="standard_id">
                    <option value="">ทั้งหมด</option>
                    @foreach ($standards as $std)
                        <option value="{{ $std->id }}">
                            {{ $std->name }}
                        </option>
                    @endforeach
                </select>

            </div>

            <!-- ด้าน -->
            <div class="field">
                <label>ด้านตัวชี้วัด</label>
                <select id="filter-dimension" name="category_id"> <!-- ✅ ใช้ category_id -->
                    <option value="">ทั้งหมด</option>
                    @foreach ($dimensions as $dim)
                        <option value="{{ $dim->id ?? $dim }}">
                            {{ $dim->name ?? $dim }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- หน่วยงาน -->
            <div class="field">
                <label>หน่วยงานที่รับผิดชอบ</label>
                <select id="filter-dept" name="dept_id"> <!-- ✅ ใช้ dept_id -->
                    <option value="">ทั้งหมด</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id ?? $dept }}">
                            {{ $dept->name ?? $dept }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- ผู้รับผิดชอบ -->
            <div class="field">
                <label>ผู้รับผิดชอบในการรวบรวมข้อมูล</label>
                <select id="filter-collector" name="collector">
                    <option value="">ทั้งหมด</option>
                    @foreach ($collectors as $col)
                        <option value="{{ $col }}">{{ $col }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="card-actions">
            <button type="button" id="reset-filters" class="btn btn-outline">ล้างค่า</button>
            <button type="button" id="apply-filters" class="btn btn-primary">กรองข้อมูล</button>
        </div>
    </form>
</div>
