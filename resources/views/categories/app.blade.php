@extends('layouts.app')
@section('title', 'จัดการข้อมูลมาตรฐานและด้านการประเมิน')
@section('content')

    <div class="categories-container">
        <div class="categories-containers">
            <div class="header-contatainers">
                มาตรฐานและด้านการประเมิน
            </div>
            <!-- ฟอร์มเพิ่มด้านการประเมิน -->
            <div class="category-form">

                <div class="add-section-title">เพิ่มมาตรฐานการประเมิน</div>

                <form action="{{ route('standards.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">ชื่อมาตรฐานการประเมิน <span class="required">*</span></label>
                        <input type="text" name="name" class="form-input" required>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> บันทึก
                    </button>
                </form>
            </div>
            <!-- รายการมาตรฐานการประเมิน -->
            <div class="categories-list">
                <div class="list-title">รายชื่อมาตรฐานการประเมินที่มี</div>

                <table class="datatable" id="table1">
                    <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>ชื่อมาตรฐานการประเมิน</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($standards as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td>
                                    <div class="categories-actions">
                                        <button class="btn-edit"
                                            onclick="openEditModalStandards({{ $item->id }}, '{{ $item->name }}')">
                                            <i data-lucide="edit-3" style="margin-right: 1px;"></i> แก้ไข
                                        </button>

                                        <button class="btn-delete"
                                            onclick="openDeleteModalStandards({{ $item->id }}, '{{ $item->name }}')">
                                            <i data-lucide="trash-2" style="margin-right: 5px;"></i> ลบ
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
            <!-- Edit Modal Standards-->
            <div id="editModal" class="modal-overlay">
                <div class="modal-content">

                    <h2 class="modal-title">แก้ไขชื่อมาตรฐานการประเมิน</h2>
                    <div class="modal-section-title">แก้ไขชื่อมาตรฐานการประเมินที่ต้องการแล้วกดบันทึกเพื่อบันทึกผลที่ต้องการ
                        <p>ชื่อด้านการประเมินเดิม : <span id="currentstandardsName"></span></p>


                    </div>

                    <form id="editForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="modal-form-group">
                            <label class="modal-form-label">ชื่อมาตรฐานการประเมิน <span class="required">*</span></label>
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
            <!-- Delete Modal Standards -->
            <div id="deleteModal" class="modal-overlay">
                <div class="modal-content">
                    <button class="modal-close" onclick="closeModal('deleteModal')">&times;</button>
                    <h2 class="modal-title">ลบชิ่อมาตรฐานการประเมิน</h2>
                    <div class="modal-section-title">คำเตือน : การลบชื่อมาตรฐานการประเมินที่ถูกนำมาใช้แล้วจะไม่สามารถลบได้
                    </div>

                    <div class="delete-message">
                        คุณต้องการลบข้อมูลด้านการประเมิน "<span id="deleteName"></span>" <br>
                        หรือไม่?
                    </div>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-buttons">
                            <button type="button" class="modal-btn modal-btn-secondary"
                                onclick="closeModal('deleteModal')">
                                <i data-lucide="undo-2" style="margin-right: 6px;"></i>กลับ</button>
                            <button type="submit" class="modal-btn modal-btn-danger"><i data-lucide="x"
                                    style="margin-right: 6px;"></i>ยืนยันการลบ</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ฟอร์มเพิ่มด้านการประเมิน -->
            <div class="category-form">

                <div class="add-section-title">เพิ่มด้าน และคะแนนเต็มการประเมิน</div>

                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">ชื่อด้านการประเมิน <span class="required">*</span></label>
                        <input type="text" name="name" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">ตะแนนเต็มของด้านการประเมิน <span class="required">*</span></label>
                        <input type="text" name="max_score" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">เลือกมาตรของด้านการประเมิน <span class="required">*</span></label>
                        <select name="standard_id" class="form-input" required>
                            <option value="">-- เลือกมาตรฐาน --</option>
                            @foreach ($standards as $standard)
                                <option value="{{ $standard->id }}">{{ $standard->name }}</option>
                            @endforeach
                        </select>
                    </div>


                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> บันทึก
                    </button>
                </form>
            </div>

            <!-- รายการด้านการประเมิน -->
            <div class="categories-list">
                <div class="list-title">รายชื่อด้านการประเมินที่มี</div>

                <table class="datatable" id="table2">
                    <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>ชื่อด้านการประเมิน</th>
                            <th>คะแนนเต็ม</th>
                            <th>มาตรฐาน</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $index => $cat)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $cat->name }}</td>
                                <td>{{ $cat->max_score }}</td>
                                <td>{{ $cat->standard->name ?? '-' }}</td>
                                <td>
                                    <div class="categories-actions">
                                        <button class="btn-edit"
                                            onclick="openEditModalCat({{ $cat->id }}, '{{ $cat->name }}', '{{ $cat->max_score }}', '{{ $cat->standard_id }}', '{{ $cat->standard->name }}')">
                                            <i data-lucide="edit-3" style="margin-right: 1px;"></i> แก้ไข
                                        </button>

                                        <button class="btn-delete"
                                            onclick="openDeleteModalCat({{ $cat->id }}, '{{ $cat->name }}', '{{ $cat->max_score }}', '{{ $cat->standard->name }}')">
                                            <i data-lucide="trash-2" style="margin-right: 5px;"></i> ลบ
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>


    <!-- Edit Modal Categories-->
    <div id="editModal" class="modal-overlay">
        <div class="modal-content">

            <h2 class="modal-title">แก้ไขชื่อด้านการประเมิน</h2>
            <div class="modal-section-title">แก้ไขชื่อด้านการประเมินที่ต้องการแล้วกดบันทึกเพื่อบันทึกผลที่ต้องการ
                <p>ชื่อด้านการประเมินเดิม : <span id="currentcategoriesName"></span></p>
                <p>คะแนนเต็มเดิม : <span id="currentcategoriesMaxScore"></span></p>
                <p>มาตรฐานเดิม : <span id="currentcategoriesStandardName"></span></p>

            </div>

            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-form-group">
                    <label class="modal-form-label">ชื่อด้านการประเมิน <span class="required">*</span></label>
                    <input type="text" id="editName" name="name" class="modal-form-input" required
                        value="{{ old('name') }}">
                </div>
                @error('name')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
                <div class="modal-form-group">
                    <label class="modal-form-label">คะแนนเต็ม <span class="required">*</span></label>
                    <input type="number" id="editMaxScore" name="max_score" class="modal-form-input" required
                        value="{{ old('max_score') }}">
                </div>
                @error('max_score')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
                <div class="modal-form-group">
                    <label class="modal-form-label">เลือกมาตรฐาน <span class="required">*</span></label>
                    <select id="editStandardId" name="standard_id" class="modal-form-input" required>
                        <option value="">-- เลือกมาตรฐาน --</option>
                        @foreach ($standards as $standard)
                            <option value="{{ $standard->id }}">{{ $standard->name }}</option>
                        @endforeach
                    </select>
                </div>
                @error('standard_id')
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

    <!-- Delete Modal Categories -->
    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal('deleteModal')">&times;</button>
            <h2 class="modal-title">ลบชิ่อด้านการประเมิน</h2>
            <div class="modal-section-title">คำเตือน : การลบชื่อด้านการประเมินที่ถูกนำมาใช้แล้วจะไม่สามารถลบได้</div>

            <div class="delete-message">
                คุณต้องการลบข้อมูลด้านการประเมิน "<span id="deleteName"></span>" <br>
                (คะแนนเต็ม: <span id="deleteMaxScore"></span>) <br>
                มาตรฐาน: <span id="deleteStandardName"></span> <br>
                หรือไม่?
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
        function openEditModalStandards(id, name) {
            document.getElementById('editName').value = name;
            document.getElementById('editForm').action = `/standards/${id}`; // ต้องตรงกับ route PUT /standards/{id}
            document.getElementById('editModal').classList.add('active');
            document.getElementById('currentstandardsName').innerText = name;

        }

        function openDeleteModalStandards(id, name) {
            document.getElementById('deleteName').textContent = name;
            document.getElementById('deleteForm').action = `/standards/${id}`;
            document.getElementById('deleteModal').classList.add('active');
        }

        function openEditModalCat(id, name, max_score, standard_id, standard_name) {
            document.getElementById('editName').value = name;
            document.getElementById('editMaxScore').value = max_score;
            document.getElementById('editStandardId').value = standard_id;
            document.getElementById('editForm').action = `/categories/${id}`; // ต้องตรงกับ route PUT /categories/{id}
            document.getElementById('editModal').classList.add('active');
            document.getElementById('currentcategoriesName').innerText = name;
            document.getElementById('currentcategoriesMaxScore').innerText = max_score;
            document.getElementById('currentcategoriesStandardName').innerText = standard_name;
        }

        function openDeleteModalCat(id, name, max_score, standard_name) {
            document.getElementById('deleteName').textContent = name;
            document.getElementById('deleteMaxScore').textContent = max_score;
            document.getElementById('deleteStandardName').textContent = standard_name;
            document.getElementById('deleteForm').action = `/categories/${id}`;
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
        .categories-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;

        }

        .categories-containers {
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

        .category-form {

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

        .categories-list {
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

        .categories-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .categories-item:last-child {
            border-bottom: none;
        }

        .categories-name {
            color: #333;
            font-size: 16px;
        }

        .categories-name::before {
            content: " ";
            color: #333;
            margin-right: 8px;
        }

        .categories-actions {
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
            border: 1px solid #e53935;
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

        .modal-section-title p {
            margin: 4px 0;
            /* ลดระยะห่างบน-ล่างของ <p> */
            font-size: 16px;
            /* ปรับขนาดข้อความให้เท่ากัน */
            line-height: 1.3;
            /* ปรับระยะบรรทัดให้อ่านง่าย */
        }

        .delete-message {
            line-height: 1.6;
            font-size: 16px;
            color: #333;
            text-align: center;
        }
    </style>
@endsection
