@extends('layouts.app')
@section('title', 'เพิ่มผู้ใช้งาน')
@section('content')
    <div class="user-container">
        <div class="user-containers">
            <div class="header-contatainers">เพิ่มผู้ใช้งาน</div>
            <div class="user-form">
                @if ($errors->any())
                    <div class="error-message" style="margin-bottom:16px;">
                        <ul style="margin-left:18px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <div class="form-section">
                        <div class="section-title">ข้อมูลส่วนตัว</div>
                        <div class="form-row">
                            <div class="form-group third-width">
                                <label class="form-label">คำนำหน้าชื่อ</label>
                                <select name="title" class="form-input">
                                    <option value="">-- เลือกคำนำหน้า --</option>
                                    <option value="นาย" {{ old('title') == 'นาย' ? 'selected' : '' }}>นาย</option>
                                    <option value="นาง" {{ old('title') == 'นาง' ? 'selected' : '' }}>นาง</option>
                                    <option value="นางสาว" {{ old('title') == 'นางสาว' ? 'selected' : '' }}>นางสาว</option>
                                    <option value="ผศ." {{ old('title') == 'ผศ.' ? 'selected' : '' }}>ผศ.</option>
                                    <option value="รศ." {{ old('title') == 'รศ.' ? 'selected' : '' }}>รศ.</option>
                                    <option value="ศ." {{ old('title') == 'ศ.' ? 'selected' : '' }}>ศ.</option>
                                </select>
                                @error('title')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group third-width">
                                <label class="form-label">ชื่อจริง <span class="required">*</span></label>
                                <input id="first_name" type="text" name="first_name" class="form-input"
                                    value="{{ old('first_name') }}" placeholder="กรุณากรอกชื่อจริง" required>
                                @error('first_name')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group third-width">
                                <label class="form-label">นามสกุล <span class="required">*</span></label>
                                <input id="last_name" type="text" name="last_name" class="form-input"
                                    value="{{ old('last_name') }}" placeholder="กรุณากรอกนามสกุล" required>
                                @error('last_name')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group half-width">
                                <label class="form-label">อีเมล <span class="required">*</span></label>
                                <input type="email" name="email" class="form-input" value="{{ old('email') }}"
                                    placeholder="กรุณากรอกอีเมล" required>
                                @error('email')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group half-width">
                                <label class="form-label">เบอร์โทรศัพท์</label>
                                <input type="text" name="phone" class="form-input" value="{{ old('phone') }}"
                                    placeholder="กรุณากรอกเบอร์โทรศัพท์">
                                @error('phone')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-title">ข้อมูลการทำงาน</div>
                        <div class="form-row">
                            <div class="form-group half-width">
                                <label class="form-label">หน่วยงาน <span class="required">*</span></label>
                                <select name="department_id" class="form-input" required>
                                    <option value="">กรุณาเลือกหน่วยงาน</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group half-width">
                                <label class="form-label">ประเภทบุคลากร</label>
                                <input type="text" name="positype" class="form-input" value="{{ old('positype') }}"
                                    placeholder="เช่น พนักงานมหาวิทยาลัย, ข้าราชการ">
                                @error('positype')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group half-width">
                                <label class="form-label">สายงาน</label>
                                <input type="text" name="workline" class="form-input" value="{{ old('workline') }}"
                                    placeholder="เช่น สนับสนุน, วิชาการ">
                                @error('workline')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group half-width">
                                <label class="form-label">ตำแหน่ง</label>
                                <input type="text" name="posi" class="form-input" value="{{ old('posi') }}"
                                    placeholder="เช่น นักวิชาการคอมพิวเตอร์">
                                @error('posi')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group half-width">
                                <label class="form-label">ระดับ</label>
                                <input type="text" name="level" class="form-input" value="{{ old('level') }}"
                                    placeholder="เช่น ปฏิบัติการ, ชำนาญการ, เชี่ยวชาญ">
                                @error('level')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-title">ความปลอดภัย</div>
                        <div class="form-row">
                            <div class="form-group half-width">
                                <label class="form-label">รหัสผ่าน <span class="required">*</span></label>
                                <input type="password" id="password" name="password" class="form-input"
                                    placeholder="กรุณากรอกรหัสผ่าน (อย่างน้อย 6 ตัวอักษร)" required>
                                @error('password')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group half-width">
                                <label class="form-label">ยืนยันรหัสผ่าน <span class="required">*</span></label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="form-input" placeholder="กรุณายืนยันรหัสผ่าน" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group half-width">
                                <label class="form-label">บทบาท (Role) <span class="required">*</span></label>
                                <select name="role" class="form-input" required>
                                    <option value="">กรุณาเลือกบทบาท</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role }}"
                                            {{ old('role') === $role ? 'selected' : '' }}>{{ $role }}</option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group half-width">
                                <label class="form-label">สถานะระบบ <span class="required">*</span></label>
                                <select name="status" class="form-input" required>
                                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('users.index') }}" class="btn btn-outline"> ยกเลิก</a>
                        <button type="submit" class="btn btn-primary"><i data-lucide="save" class="btn-icon"></i>
                            บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // ไม่จำเป็นต้องมี script สร้าง name field แล้ว
        // เพราะ display_name จะถูกสร้างใน Model ผ่าน Accessor
    </script>

    <style>
        .user-container {
            max-width: 1500px;
            margin: 0 auto;
            padding: 20px;
        }

        .user-containers {
            width: 100%;
            max-width: 1500px;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header-contatainers {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px 20px;
            font-weight: 700;
            font-size: 30px;
            background: linear-gradient(90deg, #a9c6ff 0%, #fff3d4 100%);
            color: #222;
        }

        .user-form {
            background: #fff;
            border-radius: 10px;
            padding: 40px;
            margin: 40px 60px;
            border: 2px solid #C2D9EB;
        }

        .form-section {
            margin-bottom: 40px;
        }

        .section-title {
            color: #2196f3;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-decoration: underline;
        }

        .form-row {
            display: flex;
            gap: 30px;
            margin-bottom: 20px;
        }

        .form-group {
            flex: 1;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        .required {
            color: red;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-input:focus {
            outline: none;
            border-color: #2196f3;
        }

        .error-message {
            color: #f44336;
            font-size: 14px;
            margin-top: 5px;
        }

        .form-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
@endsection
