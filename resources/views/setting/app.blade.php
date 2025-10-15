@extends('layouts.app')
@section('title', 'ตั้งค่าการแจ้งเตือน')

@section('header', 'ตั้งค่าการแจ้งเตือน')
@section('subheader', 'ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล')

@section('content')
    <div class="Setting-card">
        <x-card>
            <div class="card-title">การตั้งค่าการแจ้งเตือน</div>
            <!-- Title -->
            <div class="form-group">
                <label class="form-label">หัวข้อการแจ้งเตือน <span class="required">*</span></label>
                <input form="notification-settings-form" type="text" name="title" class="form-input"
                    value="{{ old('title', $setting->title ?? '') }}">
            </div>

            <div class="form-group">
                <label class="form-label">วันที่แจ้งเตือนรอบที่ 1</label>
                <input form="notification-settings-form" type="date" name="notify_date1" class="form-input2"
                    value="{{ old('notify_date1', $setting?->notify_date1?->format('Y-m-d')) }}">
            </div>

            <div class="form-group">
                <label class="form-label">วันที่แจ้งเตือนรอบที่ 2</label>
                <input form="notification-settings-form" type="date" name="notify_date2" class="form-input2"
                    value="{{ old('notify_date2', $setting?->notify_date2?->format('Y-m-d')) }}">
            </div>

            <!-- Message -->
            <div class="form-group">
                <label class="form-label">ข้อความแจ้งเตือน</label>
                <textarea name="message" class="form-input" rows="3" placeholder="เช่น กรุณากรอกข้อมูลภายในสิ้นเดือน">{{ old('message', $setting->message ?? '') }}</textarea>
            </div>

            <!-- เพิ่มเวลาในการแจ้งเตือน และตัวเลือกเตือนอัตโนมัติ -->
            <div class="form-group">
                <label class="form-label">เวลาแจ้งเตือน (รอบที่ 1)</label>
                <input form="notification-settings-form" type="time" name="notify_time1" class="form-input2"
                    style="width:140px;" value="{{ old('notify_time1', $setting->notify_time1 ?? '09:00') }}">
            </div>

            <div class="form-group">
                <label class="form-label">เวลาแจ้งเตือน (รอบที่ 2)</label>
                <input form="notification-settings-form" type="time" name="notify_time2" class="form-input2"
                    style="width:140px;" value="{{ old('notify_time2', $setting->notify_time2 ?? '09:00') }}">
            </div>

        </x-card>

        <x-card>
            <div class="card-title">ตัวเลือกแจ้งเตือนอัตโนมัติ (ก่อนวันกำหนด)</div>
            <div class="form-group">
                <label class="form-label">เปิดใช้งาน</label>
                <input form="notification-settings-form" type="checkbox" name="remind_enabled" value="1"
                    {{ old('remind_enabled', $setting->remind_enabled ?? false) ? 'checked' : '' }} />
            </div>
            <div class="form-group">
                <label class="form-label">จำนวนวันก่อนกำหนด (เช่น 7,3,1)</label>
                <input form="notification-settings-form" type="text" name="remind_days" class="form-input"
                    value="{{ old('remind_days', $setting->remind_days ?? '7,3,1') }}" placeholder="7,3,1">
            </div>
            <div class="form-group">
                <label class="form-label">เวลาแจ้งเตือนอัตโนมัติ</label>
                <input form="notification-settings-form" type="time" name="remind_time" class="form-input2"
                    style="width:140px;" value="{{ old('remind_time', $setting->remind_time ?? '09:00') }}">
            </div>
        </x-card>

        <form id="notification-settings-form" action="{{ route('settings.store') }}" method="POST">
            @csrf
            <div class="action-bts">
                <!-- Submit -->
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save" style="width: 20px;"></i> บันทึก
                </button>
                <button type="submit" name="send_now" value="1" formaction="{{ route('settings.sendNow') }}"
                    class="btn btn-secondary">
                    <i data-lucide="send" style="width: 20px;"></i> บันทึกและส่งแจ้งเตือนทันที
                </button>
            </div>
        </form>
    </div>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('editModal').classList.add('active');
            });
        </script>
    @endif
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/trumbowyg.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.jQuery && typeof $.fn.trumbowyg === 'function') {
                $('textarea[name="message"]').trumbowyg({
                    svgPath: 'https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/ui/icons.svg',
                });
            }
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/ui/trumbowyg.min.css" />
    <style>
        .container {
            max-width: 960px !important;
        }

        .Setting-card {
            display: flex;
            flex-direction: column;
            background: var(--color-white);
            border-radius: var(--radius-default);
            box-shadow: var(--shadow-default);
            border: 1px solid var(--color-gray-100);
            padding: 24px;
            gap: 24px;
        }

        .card-title {
            font-size: 18px;
            color: var(--blue-default);
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
            background: var(--blue-default);
            position: absolute;
            left: 0;
            top: 2px;
            opacity: .25;
        }

        .action-bts {
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: normal;
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

        .form-input2 {
            /* width: 100%; */
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

        .form-input2:focus {
            outline: none;
            border-color: #2196f3;
        }
    </style>
@endpush
