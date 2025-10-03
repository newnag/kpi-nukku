@extends('layouts.app')
@section('title', 'แก้ไขผู้ใช้งาน')
@section('content')
    <div class="user-container">
        <div class="user-containers">
            <div class="header-contatainers">แก้ไขผู้ใช้งาน</div>
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

                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="name" name="name" value="{{ old('name', $user->name) }}">

                    <div class="form-section">
                        <div class="section-title">ข้อมูลส่วนตัว</div>
                        <div class="form-row">
                            <div class="form-group half-width">
                                <label class="form-label">ชื่อจริง <span class="required">*</span></label>
                                <input id="first_name" type="text" name="first_name" class="form-input"
                                    value="{{ old('first_name', $user->first_name) }}" placeholder="เช่น กิตติพันธ์"
                                    required>
                                @error('first_name')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group half-width">
                                <label class="form-label">นามสกุล <span class="required">*</span></label>
                                <input id="last_name" type="text" name="last_name" class="form-input"
                                    value="{{ old('last_name', $user->last_name) }}" placeholder="เช่น ศรศักดา" required>
                                @error('last_name')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group half-width">
                                <label class="form-label">อีเมล <span class="required">*</span></label>
                                <input type="email" name="email" class="form-input"
                                    value="{{ old('email', $user->email) }}" placeholder="กรุณากรอกอีเมล" required>
                                @error('email')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group half-width">
                                <label class="form-label">เบอร์โทรศัพท์ <span class="required">*</span></label>
                                <input type="text" name="phone" class="form-input"
                                    value="{{ old('phone', $user->phone) }}" placeholder="กรุณากรอกเบอร์โทรศัพท์" required>
                                @error('phone')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Password (optional) -->
                        <div class="form-row">
                            <div class="form-group half-width">
                                <label class="form-label">รหัสผ่านใหม่ (ถ้าไม่เปลี่ยน ปล่อยว่าง)</label>
                                <input id="password" type="password" name="password" class="form-input" placeholder="ใส่รหัสผ่านใหม่ (อย่างน้อย 8 ตัวอักษร)">
                                @error('password')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group half-width">
                                <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" class="form-input" placeholder="พิมพ์รหัสผ่านใหม่อีกครั้ง">
                            </div>
                        </div>


                        <div class="form-row">
                            <div class="form-group half-width">
                                <label class="form-label">หน่วยงาน <span class="required">*</span></label>
                                <select name="department_id" class="form-input" required>
                                    <option value="">กรุณาเลือกหน่วยงาน</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ (string) old('department_id', $user->department_id) === (string) $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group half-width">
                                <label class="form-label">สถานะ <span class="required">*</span></label>
                                <select name="status" class="form-input" required>
                                    <option value="1"
                                        {{ (string) old('status', (string) $user->status) === '1' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0"
                                        {{ (string) old('status', (string) $user->status) === '0' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                                @error('status')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-title">สิทธิ์การใช้งาน</div>
                        <div class="form-row">
                            <div class="form-group half-width">
                                <label class="form-label">บทบาท (Role) <span class="required">*</span></label>
                                @php $currentRole = old('role', $user->getRoleNames()->first()); @endphp
                                <select name="role" class="form-input" required>
                                    <option value="">กรุณาเลือกบทบาท</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role }}"
                                            {{ $currentRole === $role ? 'selected' : '' }}>{{ $role }}</option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('users.index') }}" class="btn-back"><i data-lucide="arrow-left"
                                class="btn-icon"></i> ยกเลิก</a>
                        <button type="submit" class="submit-btn"><i data-lucide="save" class="btn-icon"></i>
                            บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const first = document.getElementById('first_name');
            const last = document.getElementById('last_name');
            const nameField = document.getElementById('name');

            function compose() {
                const full = [first?.value?.trim(), last?.value?.trim()].filter(Boolean).join(' ');
                if (nameField) nameField.value = full;
            }
            first && first.addEventListener('input', compose);
            last && last.addEventListener('input', compose);
            compose();
        });
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

        .submit-btn {
            background: #2196f3;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back {
            background: #fff;
            color: #398ECA;
            border: 1px solid #398ECA;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
@endsection
