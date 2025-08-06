@extends('layouts.app')
@section('title', 'จัดการข้อมูลหน่วยงาน')
@section('content')



    <div class="department-container">
        <div class="department-containers">
            <div class="header-contatainers">
                หน่วยงาน
            </div>

            <!-- ฟอร์มเพิ่มหน่วยงาน -->
            <div class="department-form">

                <div class="add-section-title">เพิ่มหน่วยงาน</div>

                <form action="{{ route('departments.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">ชื่อหน่วยงาน <span class="required">*</span></label>
                        <input type="text" name="name" class="form-input" required>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> บันทึก
                    </button>
                </form>
            </div>

            <!-- รายการหน่วยงาน -->
            <div class="department-list">
                <div class="list-title">รายชื่อหน่วยงานที่มี</div>


                @foreach ($departments as $department)
                    <div class="department-item">
                        <span class="department-name">{{ $department->name ?? 'ชื่อหน่วยงาน' }}</span>
                        <div class="department-actions">
                            <button class="btn-edit"
                                onclick="openEditModal({{ $department->id }}, '{{ $department->name ?? 'ชื่อหน่วยงาน' }}')">
                                <i data-lucide="edit-3" style="margin-right: 1px;"></i> แก้ไข
                            </button>
                            <button class="btn-delete"
                                onclick="openDeleteModal({{ $department->id }}, '{{ $department->name ?? 'ชื่อหน่วยงาน' }}')">
                                <i data-lucide="trash-2" style="margin-right: 5px;"></i> ลบ
                            </button>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>


    <!-- Edit Modal -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-content">

            <h2 class="modal-title">แก้ไขชื่อหน่วยงาน</h2>
            <div class="modal-section-title">แก้ไขชื่อหน่วยงานที่ต้องการแล้วกดบันทึกเพื่อบันทึกผลที่ต้องการ
                <p>ชื่อหน่วยงานเดิม : <span id="currentDepartmentName"></span></p>
            </div>

            <form id="editForm" method="POST"
                action="{{ session('edit_department_id') ? url('/departments/' . session('edit_department_id')) : '' }}">
                @csrf
                @method('PUT')
                <div class="modal-form-group">
                    <label class="modal-form-label">ชื่อหน่วยงาน <span class="required">*</span></label>
                    <input type="text" id="editName" name="name" class="modal-form-input" required
                        value="{{ old('name') }}">
                </div>
                @error('name')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
                <div class="modal-buttons">
                    <button type="button" class="modal-btn modal-btn-secondary" onclick="closeModal('editModal')">
                        <i data-lucide="undo-2" style="margin-right: 6px;"></i>กลับ</button>
                    <button type="submit" class="modal-btn modal-btn-primary">
                        <i data-lucide="save" style="margin-right: 6px;"></i>บันทึก</button>
                </div>
            </form>

        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal('deleteModal')">&times;</button>
            <h2 class="modal-title">ลบชิ่อหน่วยงาน</h2>
            <div class="modal-section-title">คำเตือน : การลบชื่อหน่วยงานที่ถูกนำมาใช้แล้วจะไม่สามารถลบได้</div>

            <div class="delete-message">
                คุณต้องการลบข้อมูลหน่วยงาน "<span id="deleteName"></span>" หรือไม่?
            </div>

            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-buttons">
                    <button type="button" class="modal-btn modal-btn-secondary" onclick="closeModal('deleteModal')">
                        <i data-lucide="undo-2" style="margin-right: 6px;"></i>กลับ</button>
                    <button type="submit" class="modal-btn modal-btn-danger"><i data-lucide="x"
                            style="margin-right: 6px;"></i>ยืนยันการลบ</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name) {
            document.getElementById('editName').value = name;
            document.getElementById('editForm').action = `/departments/${id}`;
            document.getElementById('editModal').classList.add('active');
            document.getElementById('currentDepartmentName').innerText = name; //แสดงชื่อเดิม
        }

        function openDeleteModal(id, name) {
            document.getElementById('deleteName').textContent = name;
            document.getElementById('deleteForm').action = `/departments/${id}`;
            document.getElementById('deleteModal').classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        // ปิด modal เมื่อคลิกนอก modal content
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.remove('active');
            }
        });

        // ปิด modal เมื่อกด ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.active').forEach(modal => {
                    modal.classList.remove('active');
                });
            }
        });

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
        .department-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;

        }

        .department-containers {
            width: 100%;
            max-width: 900px;
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

        .department-form {

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

        .form-input:focus {
            outline: none;
            border-color: #2196f3;
        }

        .submit-btn {
            background: #2196f3;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            display: block;
            margin: 0 auto;
            transition: background-color 0.3s;
        }

        .submit-btn:hover {
            background: #1976d2;
        }

        .department-list {
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

        .department-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .department-item:last-child {
            border-bottom: none;
        }

        .department-name {
            color: #333;
            font-size: 16px;
        }

        .department-name::before {
            content: " ";
            color: #333;
            margin-right: 8px;
        }

        .department-actions {
            display: flex;
            gap: 10px;
        }

        .edit-btn {
            color: #2196f3;
            text-decoration: underline;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .delete-btn {
            color: #f44336;
            text-decoration: underline;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .edit-btn:hover {
            color: #1976d2;
        }

        .delete-btn:hover {
            color: #d32f2f;
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 10px;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .modal-close {
            position: absolute;
            right: 15px;
            top: 15px;
            background: none;
            border: none;
            font-size: 24px;
            color: #666;
            cursor: pointer;
        }

        .modal-title {
            color: #000000;
            font-size: 20px;
            font-weight: bold;
            /* text-align: center; */
            margin-bottom: 10px;
        }

        .modal-section-title {
            color: #868686;
            font-size: 16px;
            /* font-weight: bold; */
            /* margin-bottom: 10px; */
            /* text-decoration: underline; */
        }

        .modal-form-group {
            margin-bottom: 20px;
        }

        .modal-form-label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: normal;
        }

        .modal-form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }

        .modal-form-input:focus {
            outline: none;
            border-color: #2196f3;
        }

        .modal-buttons {
            display: flex;
            gap: 150px;
            justify-content: center;
            margin-top: 30px;
        }

        .modal-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .modal-btn-primary {
            background: #2196f3;
            color: white;
        }

        .modal-btn-primary:hover {
            background: #1976d2;
        }

        .modal-btn-secondary {
            background: #FFFFFF;
            color: #398ECA;
            border: 1px solid #398ECA;
        }

        .modal-btn-secondary:hover {
            background: #398ECA;
            color: white;
        }

        .modal-btn-danger {
            background: #FFFFFF;
            color: #FF0004;
            border: 1px solid #FF0004;
        }

        .modal-btn-danger:hover {

            background: #db0a0d;
            color: white;
        }

        .delete-message {
            text-align: center;
            margin: 20px 0;
            color: #333;
            font-size: 16px;
        }

        .btn-edit {
            background-color: white;
            border: 1px solid #398ECA;
            color: #398ECA;
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-edit:hover {
            background-color: #398ECA;
            color: white;
        }

        .btn-delete {
            background-color: white;
            color: #e53935;
            border:  1px solid  #e53935;
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-delete:hover {
            background-color: #c62828;
            color: white;
        }
    </style>
@endsection
