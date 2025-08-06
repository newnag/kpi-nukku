@extends('layouts.app')
@section('title', 'ตั้งค่าการแจ้งเตือน')
@section('content')

    <div class="Setting-container">
        <div class="Setting-containers">
            <div class="header-contatainers">
                ตั้งค่าการแจ้งเตือน
            </div>

            <!-- ฟอร์มsetting-->
            <div class="Setting-form">

                <div class="add-section-title">การตั้งค่าเว็บไซต์</div>

                <form action="{{ route('settings.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">ชื่อเว็บไซต์ <span class="required">*</span></label>
                        <input type="text" name="title" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">จำนวนวันก่อนถึงกำหนดการแจ้งเตือน <span class="required">*</span></label>
                        <input type="text" name="day_notify" class="form-input2" required>
                        <i data-lucide="alarm-clock" class="lucide-icon"></i>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i data-lucide="save" class="btn-icon"></i> บันทึก
                    </button>
                </form>
            </div>
        </div>
    </div>




    <script>
        lucide.createIcons();
    </script>
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('editModal').classList.add('active');
            });
        </script>
    @endif
    <style>
        .Setting-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;

        }

        .Setting-containers {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            /* ทำให้มุมมนทำงานดีขึ้น */
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

        .Setting-form {

            margin-bottom: 30px;
            position: relative;
            background: white;
            border-radius: 10px;
            padding: 30px;
            /* box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); */
            border: 2px solid #C2D9EB;
            margin-top: 40px;
            margin-bottom: 40px;
            margin-left: 60px;
            margin-right: 60px;
        }

        .close-btn {
            position: absolute;
            right: 20px;
            top: 20px;
            background: none;
            border: none;
            font-size: 24px;
            color: #666;
            cursor: pointer;
        }

        .form-title {
            color: #1976d2;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
        }

        .add-section-title {
            color: #2196f3;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-decoration: underline;
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

        .submit-btn {
            background: #2196f3;
            color: white;
            border: none;
            padding: 12px 16px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            /* ระยะห่างระหว่างไอคอนและข้อความ */
            transition: background-color 0.3s;
            margin: 0 auto;
        }

        .submit-btn:hover {
            background: #1976d2;
        }

        .btn-icon {
            width: 20px;
            height: 20px;
        }


        .Setting-list {
            background: white;
            border-radius: 10px;
            padding: 30px;
            /* box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); */
            border: 2px solid #C2D9EB;
            margin-top: 40px;
            margin-bottom: 40px;
            margin-left: 60px;
            margin-right: 60px;
        }

        .list-title {
            color: #2196f3;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-decoration: underline;
        }

        .Setting-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .Setting-item:last-child {
            border-bottom: none;
        }

        .Setting-name {
            color: #333;
            font-size: 16px;
        }

        .Setting-name::before {
            content: " ";
            color: #333;
            margin-right: 8px;
        }

        .Setting-actions {
            display: flex;
            gap: 10px;
        }

        

      

       

        .lucide-icon {
            width: 30px;
            height: 30px;
            color: #D9D9D9;
        }
    </style>
@endsection
