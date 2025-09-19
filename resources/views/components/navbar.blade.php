<style>
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
        width: 100%;
        box-sizing: border-box;
        flex-wrap: wrap;
    }

    .navbar-menu {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

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

    .navbar-brand {
        display: flex;
        align-items: center;
        font-weight: 600;
        color: #000;
        text-decoration: none;
        white-space: nowrap;
        /* ✅ ไม่ให้ตัดบรรทัด */
        font-size: 15px;
        gap: 10px;
    }

    .navbar-brand img {
        height: 50px;
        /* ✅ ย่อโลโก้ */
        width: auto;
        border-radius: 50%;
    }

    .dropdown {
        position: relative;
    }

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

    .dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        min-width: 220px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        flex-direction: column;
        z-index: 1000;
        padding: 6px 0;
    }

    .dropdown.open .dropdown-menu {
        display: flex;
    }

    .dropdown-menu a {
        padding: 8px 14px;
        color: #333;
        font-size: 14px;
    }

    .dropdown-menu a:hover {
        background: #f9fafb;
    }

    .user-avatar {
        height: 35px;
        width: 35px;
        border-radius: 50%;
        border: 2px solid #ddd;
    }

    .navbar-toggle {
        display: none;
        font-size: 1.5rem;
        cursor: pointer;
        background: none;
        border: none;
        color: #333;
    }

    /* ✅ Responsive */
    @media (max-width: 768px) {
        .navbar {
            flex-direction: column;
            align-items: flex-start;
        }

        .navbar-menu {
            display: none;
            flex-direction: column;
            width: 100%;
            padding: 0.5rem 0;
            border-top: 1px solid #eee;
        }

        .navbar-menu.show {
            display: flex;
        }

        .navbar-toggle {
            display: block;
            margin-left: auto;
        }
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

<!-- Navbar Component -->
<nav class="navbar">
    <a href="{{ auth()->check() ? (auth()->user()->hasRole('user') ? route('dashboardkpi.index') : route('dashboard.index')) : route('dashboard.index') }}" class="navbar-brand">
        <img src="/uploads/logonuthaiS-2.png" alt="Logo">
        <span>ระบบบริหารจัดการข้อมูลการรับรองสถาบัน</span>
    </a>

    <!-- ปุ่ม Hamburger -->
    <button class="navbar-toggle" onclick="document.querySelector('.navbar-menu').classList.toggle('show')">
        <i class="fa-solid fa-bars"></i>
    </button>

    @auth
        <div class="navbar-menu">
            @can('view-dashboard')
                <a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                <a href="{{ route('dashboard.getData') }}"
                    class="{{ request()->routeIs('dashboard.getData') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-column"></i> กราฟแสดงผลลัพธ์ตัวชี้วัด
                </a>
            @endcan

            @hasanyrole('user')
                <a href="{{ route('dashboardkpi.index') }}"
                    class="{{ request()->is('dashboardkpi*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard ผู้ใช้งาน
                </a>
                <a href="{{ route('evidences.index') }}" class="{{ request()->is('evidences*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file"></i> หลักฐานของฉัน
                </a>
            @endhasanyrole

            @can('view-indicator-dashboard')
                <a href="{{ route('indicator.index') }}" class="{{ request()->is('indicator*') ? 'active' : '' }}">
                    <i class="fa-solid fa-sliders"></i> จัดการตัวชี้วัด
                </a>
            @endcan

            @hasanyrole('super_admin|system_admin|qa_admin')
                <a href="{{ route('dashboardkpi.index') }}"
                    class="{{ request()->routeIs('dashboardkpi.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i> ตรวจสอบตัวชี้วัด
                </a>
            @endhasanyrole

            @hasanyrole('super_admin|system_admin')
                <div class="dropdown" onclick="this.classList.toggle('open')">
                    <div class="dropdown-toggle">
                        <i class="fa-solid fa-gear"></i> ตั้งค่าระบบ <i class="fa-solid fa-caret-down ml-1"></i>
                    </div>
                    <div class="dropdown-menu">
                        @can('view-settings')
                            <a href="{{ route('settings.index') }}"><i class="fa-solid fa-bell"></i> กำหนดวันแจ้งเตือน</a>
                        @endcan
                        @can('view-departments')
                            <a href="{{ route('departments.index') }}"><i class="fa-solid fa-sitemap"></i> จัดการหน่วยงาน</a>
                        @endcan
                        @can('view-standards')
                            <a href="{{ route('standards.index') }}"><i class="fa-solid fa-layer-group"></i>
                                จัดการข้อมูลมาตรฐาน/ด้านต่างๆ</a>
                        @endcan
                        @can('view-users')
                            <a href="{{ route('users.index') }}"><i class="fa-solid fa-users"></i> จัดการผู้ใช้งาน</a>
                        @endcan
                        @can('view-evidence')
                            <a href="{{ route('evidences.index') }}"><i class="fa-solid fa-folder-open"></i> จัดการหลักฐาน</a>
                        @endcan
                    </div>
                </div>
            @endhasanyrole

            <div class="dropdown" onclick="this.classList.toggle('open')">
                <div class="dropdown-toggle">
                    <img src="/uploads/avatar-type1.png" alt="User" class="user-avatar">
                    <span class="sm:inline">{{ auth()->user()->name ?? 'ผู้ใช้' }}</span>
                </div>
                <div class="dropdown-menu">
                    <div class="px-3 py-2 text-xs text-gray-500 border-b">
                        <div class="font-medium">{{ auth()->user()->name ?? 'ผู้ใช้' }}</div>
                        <div class="text-xs">
                            @if (auth()->user()->roles->isNotEmpty())
                                บทบาท: {{ auth()->user()->roles->pluck('name')->join(', ') }}
                            @endif
                        </div>
                    </div>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa-solid fa-right-from-bracket"></i> ออกจากระบบ
                    </a>
                </div>
            </div>
        </div>
    @endauth
</nav>

<!-- Logout Form -->
@auth
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>
@endauth


<!-- Logout Form -->
@auth
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>
@endauth
