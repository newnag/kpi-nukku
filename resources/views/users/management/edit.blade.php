@extends('layouts.app')
@section('title', 'แก้ไขผู้ใช้งาน')
@section('content')
    {{-- resources/views/users/edit.blade.php --}}
    @if ($errors->any())
        <div class="error-summary">
            <ul>
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="user-container">
        <div class="user-containers">
            <div class="header-contatainers">
                แก้ไขผู้ใช้งาน
            </div>

            <!-- ฟอร์มแก้ไขผู้ใช้งาน -->
            <div class="user-form">
                {{-- สรุป Error ด้านบน (ถ้ามี) --}}
                @if ($errors->any())
                    <div class="error-message" style="margin-bottom:16px;">
                        <ul style="margin-left:18px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <!-- ข้อมูลส่วนตัว -->
                    <div class="form-section">
                        <div class="section-title">ข้อมูลส่วนตัว</div>

                        <div class="form-row">
                            <div class="form-group half-width">
                                <label class="form-label">ชื่อ-สกุล <span class="required">*</span></label>
                                <input type="text" name="name" class="form-input"
                                    value="{{ old('name', $user->name) }}" placeholder="กรุณากรอกชื่อ-สกุล" required>
                                @error('name')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group half-width">
                                <label class="form-label">อีเมล <span class="required">*</span></label>
                                <input type="email" name="email" class="form-input"
                                    value="{{ old('email', $user->email) }}" placeholder="กรุณากรอกอีเมล" required>
                                @error('email')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group half-width">
                                <label class="form-label">เบอร์โทรศัพท์ <span class="required">*</span></label>
                                <input type="text" name="phone" class="form-input"
                                    value="{{ old('phone', $user->phone) }}" placeholder="กรุณากรอกเบอร์โทรศัพท์" required>
                                @error('phone')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group half-width">
                                <label class="form-label">หน่วยงาน <span class="required">*</span></label>
                                <select name="department_id" class="form-input" required>
                                    <option value="">กรุณาเลือกหน่วยงาน</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ (string) old('department_id', $user->department_id) === (string) $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- รหัสผ่าน (ไม่บังคับ) -->
                    <div class="form-section">
                        <div class="section-title">รหัสผ่าน</div>

                        <div class="form-row">
                            <!-- รหัสผ่าน -->
                            <div class="form-group half-width" style="position: relative;">
                                <label class="form-label">รหัสผ่าน (ปล่อยว่างหากไม่เปลี่ยน)</label>
                                <input type="password" id="password" name="password" class="form-input"
                                    placeholder="เว้นว่างไว้ถ้าไม่ต้องการเปลี่ยน">
                                <span class="toggle-password" onclick="togglePassword('password', this)">
                                    <i data-lucide="eye"></i>
                                </span>
                                @error('password')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ยืนยันรหัสผ่าน -->
                            <div class="form-group half-width" style="position: relative;">
                                <label class="form-label">ยืนยันรหัสผ่าน</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="form-input" placeholder="พิมพ์รหัสผ่านเดิมอีกครั้ง (ถ้ามีการเปลี่ยน)">
                                <span class="toggle-password" onclick="togglePassword('password_confirmation', this)">
                                    <i data-lucide="eye"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- สถานะ + บทบาท -->
                    <div class="form-section">
                        <div class="section-title">สถานะการใช้งาน</div>

                        <div class="form-row">
                            <div class="form-group half-width">
                                <label class="form-label">สถานะ <span class="required">*</span></label>
                                <select name="status" class="form-input" required>
                                    <option value="1"
                                        {{ (string) old('status', (string) $user->status) === '1' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="0"
                                        {{ (string) old('status', (string) $user->status) === '0' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                                @error('status')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group half-width">
                                <label class="form-label">บทบาท (Role) <span class="required">*</span></label>
                                @php
                                    $currentRole = old('role', $user->getRoleNames()->first());
                                @endphp
                                <select name="role" class="form-input" required>
                                    <option value="">กรุณาเลือกบทบาท</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role }}"
                                            {{ $currentRole === $role ? 'selected' : '' }}>
                                            {{ $role }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- ปุ่มบันทึก -->
                    <div class="form-actions">
                        <a href="{{ route('users.index') }}" class="btn-back">
                            <i data-lucide="arrow-left" class="btn-icon"></i> กลับ
                        </a>
                        <button type="submit" class="submit-btn">
                            <i data-lucide="save" class="btn-icon"></i> อัปเดต
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId, el) {
            const input = document.getElementById(fieldId);
            const icon = el.querySelector('i');
            if (!input) return;

            if (input.type === "password") {
                input.type = "text";
                icon.setAttribute("data-lucide", "eye-off");
            } else {
                input.type = "password";
                icon.setAttribute("data-lucide", "eye");
            }
            // รีเฟรชไอคอนหลังเปลี่ยน
            if (window.lucide && typeof lucide.createIcons === 'function') {
                lucide.createIcons();
            }
        }
        // สร้างไอคอนตอนโหลดหน้า
        if (window.lucide && typeof lucide.createIcons === 'function') {
            lucide.createIcons();
        }
    </script>

    <style>
        .submit-btn {
            text-decoration: none;
        }

        .btn-back {
            text-decoration: none;
        }

        .btn-edit,
        .edit-btn {
            text-decoration: none !important;
        }
    </style>
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
            background: white;
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
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            color: #222;
        }

        .user-form {
            background: white;
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

        .form-group.half-width {
            flex: 1;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: normal;
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
            transition: border-color 0.3s;
            box-sizing: border-box;
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

        .submit-btn {
            background: #2196f3;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background-color 0.3s;
            text-decoration: none;
        }

        .submit-btn:hover {
            background: #1976d2;
        }

        .btn-back {
            background: #FFFFFF;
            color: #398ECA;
            border: 1px solid #398ECA;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background-color 0.3s;
            text-decoration: none;
        }

        .btn-back:hover {
            background: #398ECA;
            color: white;
        }

        .btn-icon {
            width: 20px;
            height: 20px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .user-form {
                margin: 20px;
                padding: 20px;
            }

            .form-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .submit-btn,
            .btn-back {
                width: 100%;
                justify-content: center;
            }
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 38px;
            cursor: pointer;
            color: #666;
        }

        .toggle-password:hover {
            color: #2196f3;
        }
    </style>

@endsection

