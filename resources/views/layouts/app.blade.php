<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล')</title>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- Font Awesome -->
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> --}}
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background: #f8f9fa;
            margin: 0;
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 0.5rem 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            font-weight: 600;
            color: #000;
            text-decoration: none;
        }

        .navbar-brand img {
            height: 40px;
            width: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .navbar-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .navbar-menu a {
            color: #333;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .navbar-menu a:hover,
        .navbar-menu a.active {
            background: #eee;
        }

        /* Dropdown */
        .dropdown {
            position: relative;
        }

        .dropdown-toggle {
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        /* Navbar Dropdown */
        .navbar .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            /* ✅ ชิดขอบขวา */
            left: auto;
            /* ✅ ไม่บังคับชิดซ้าย */
            top: 100%;
            margin-top: 8px;
            background: #fff;
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
            min-width: 180px;
            z-index: 1000;
        }

        .navbar .dropdown.open .dropdown-menu {
            display: block;
        }


        .dropdown-menu a {
            padding: 8px 12px;
            color: #333;
            text-decoration: none;
        }

        .dropdown-menu a:hover {
            background: #f3f4f6;
        }

        .user-avatar {
            height: 35px;
            width: 35px;
            border-radius: 50%;
            border: 2px solid #ddd;
        }

        /* Main Content */
        .main-content {
            padding: 20px;
            min-height: calc(100vh - 120px);
        }

        /* Breadcrumb */
        .breadcrumb {
            list-style: none;
            padding: 0;
            margin: 0 0 1rem 0;
            display: flex;
            gap: 6px;
            font-size: 14px;
        }

        .breadcrumb a {
            text-decoration: none;
            color: #007bff;
        }

        .breadcrumb li::after {
            content: "/";
            margin: 0 4px;
            color: #999;
        }

        .breadcrumb li:last-child::after {
            content: "";
        }

        /* Footer */
        footer {
            background: #fff;
            padding: 1rem;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 14px;
            color: #666;
        }

        /* Alerts */
        .alert {
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .alert button {
            background: transparent;
            border: none;
            float: right;
            font-size: 16px;
            cursor: pointer;
            color: inherit;
        }

        /* Navbar Menu */
        .navbar-menu a {
            color: #333;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 6px;
            transition: background 0.2s, color 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .navbar-menu a:hover,
        .navbar-menu a.active {
            background: #f3f4f6;
            color: #111;
        }

        /* Dropdown Menu */
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            min-width: 220px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: none;
            flex-direction: column;
            z-index: 1000;
            padding: 6px 0;
        }

        .dropdown-menu a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            font-size: 14px;
            color: #333;
        }

        .dropdown-menu a:hover {
            background: #f9fafb;
        }

        /* ปุ่ม Settings + User ให้ cursor pointer */
        .dropdown-toggle {
            cursor: pointer;
            padding: 8px 14px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }

        .dropdown-toggle:hover {
            background: #f3f4f6;
        }
    </style>
    @stack('styles')
    {{-- @stark('styles') --}}
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar">
        <a href="{{ route('dashboard.index') }}" class="navbar-brand">
            <img src="/uploads/logonuthaiS-2.png" alt="Logo">
            <span>{{ $global_setting->title ?? 'ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล' }}</span>
        </a>

        <div class="navbar-menu">
            <!-- Dashboard -->
            <a href="{{ route('dashboard.index') }}"
                class="{{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge-high"></i> Dashboard
            </a>


            <!-- Graph Result -->
            <a href="{{ route('dashboard.getData') }}"
                class="{{ request()->routeIs('dashboard.getData') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-column"></i> กราฟแสดงผลลัพธ์ตัวชี้วัด
            </a>

            <!-- Indicators -->
            <a href="{{ route('indicator.dashboard') }}" class="{{ request()->is('indicator*') ? 'active' : '' }}">
                <i class="fa-solid fa-bullseye"></i> จัดการตัวชี้วัด
            </a>

            <!-- Settings -->
            <div class="dropdown" id="settingsDropdown">
                <div class="dropdown-toggle">
                    <i class="fa-solid fa-gear"></i> Settings <i class="fa-solid fa-caret-down ml-1"></i>
                </div>
                <div class="dropdown-menu">
                    <a href="{{ route('settings.index') }}">
                        <i class="fa-solid fa-building"></i> ตั้งค่าหน่วยงาน/กำหนดวันแจ้งเตือน
                    </a>
                    <a href="{{ route('departments.index') }}">
                        <i class="fa-solid fa-sitemap"></i> จัดการหน่วยงาน
                    </a>
                    <a href="{{ route('standards.index') }}">
                        <i class="fa-solid fa-layer-group"></i> จัดการข้อมูลมาตรฐาน/ด้านต่างๆ
                    </a>
                </div>
            </div>

            <!-- User -->
            <div class="dropdown" id="userDropdown">
                <div class="dropdown-toggle">
                    <img src="/uploads/avatar-type1.png" alt="User" class="user-avatar">
                </div>
                <div class="dropdown-menu">
                    <a href="#"><i class="fa-solid fa-id-badge"></i> โปรไฟล์</a>
                    <a href="#"><i class="fa-solid fa-sliders"></i> ตั้งค่าระบบ</a>
                    <hr>
                    <a href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa-solid fa-right-from-bracket"></i> ออกจากระบบ
                    </a>
                </div>
            </div>
        </div>
    </nav>


    <!-- Main -->
    <div class="main-content">
        @if (!empty($breadcrumbs))
            <ul class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i></a></li>
                @foreach ($breadcrumbs as $breadcrumb)
                    @if ($loop->last)
                        <li>{{ $breadcrumb['title'] }}</li>
                    @else
                        <li><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
                    @endif
                @endforeach
            </ul>
        @endif

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success">
                <button onclick="this.parentElement.remove()">×</button>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                <button onclick="this.parentElement.remove()">×</button>
                {{ session('error') }}
            </div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning">
                <button onclick="this.parentElement.remove()">×</button>
                {{ session('warning') }}
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Footer -->
    <footer>
        &copy; {{ date('Y') }} ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล — Version 1.0.0
    </footer>

    <!-- Logout Form -->
    {{-- <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form> --}}

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        // รีเซ็ต dropdown เมื่อโหลดหน้าใหม่
        document.addEventListener("DOMContentLoaded", () => {
            // toggle dropdown
            document.querySelectorAll(".navbar .dropdown-toggle").forEach(btn => {
                btn.addEventListener("click", e => {
                    e.preventDefault();
                    e.stopPropagation();
                    const parent = btn.closest(".dropdown");
                    parent.classList.toggle("open");
                });
            });

            // close เมื่อกดที่อื่น
            window.addEventListener("click", () => {
                document.querySelectorAll(".navbar .dropdown.open").forEach(d => d.classList.remove(
                    "open"));
            });

            // close เมื่อคลิกลิงก์ใน dropdown
            document.querySelectorAll(".navbar .dropdown-menu a").forEach(link => {
                link.addEventListener("click", () => {
                    link.closest(".dropdown").classList.remove("open");
                });
            });
        });
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>

    {{-- @yield('scripts') --}}
    @stack('scripts')

</body>

</html>
