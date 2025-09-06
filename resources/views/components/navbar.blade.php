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
    <a href="{{ auth()->check() ? route('dashboard.index') : '#' }}" class="navbar-brand">
        <img src="/uploads/logonuthaiS-2.png" alt="Logo">
        <span>{{ 'ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล' }}</span>
    </a>

    @auth
        <div class="navbar-menu">
            <!-- Dashboard - Available to all authenticated users -->
            @can('view-dashboard')
                <a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                <!-- Graph Result - Available to users with export permission -->
                <a href="{{ route('dashboard.getData') }}" class="{{ request()->routeIs('dashboard.getData') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-column"></i> กราฟแสดงผลลัพธ์ตัวชี้วัด
                </a>
            @endcan


            <!-- Indicators - Available to users with indicator permissions -->
            @can('view-indicator-dashboard')
                <a href="{{ route('indicator.index') }}" class="{{ request()->is('indicator*') ? 'active' : '' }}">
                    <i class="fa-solid fa-bullseye"></i> จัดการตัวชี้วัด
                </a>
            @endcan

            <!-- Settings - Only for super_admin and system_admin roles -->
            @hasanyrole('super_admin|system_admin')
                <div class="dropdown" id="settingsDropdown">
                    <div class="dropdown-toggle">
                        <i class="fa-solid fa-gear"></i> ตั้งค่าระบบ <i class="fa-solid fa-caret-down ml-1"></i>
                    </div>
                    <div class="dropdown-menu">
                        @can('view-settings')
                            <a href="{{ route('settings.index') }}">
                                <i class="fa-solid fa-bell"></i> กำหนดวันแจ้งเตือน
                            </a>
                        @endcan
                        @can('view-departments')    
                            <a href="{{ route('departments.index') }}">
                                <i class="fa-solid fa-sitemap"></i> จัดการหน่วยงาน
                            </a>
                        @endcan
                        @can('view-standards')
                            <a href="{{ route('standards.index') }}">
                                <i class="fa-solid fa-layer-group"></i> จัดการข้อมูลมาตรฐาน/ด้านต่างๆ
                            </a>
                        @endcan
                        @can('view-users')
                            <a href="{{ route('users.index') }}">
                                <i class="fa-solid fa-users"></i> จัดการผู้ใช้งาน
                            </a>
                        @endcan
                    </div>
                </div>
            @endhasanyrole

            <!-- Management Menu - For qa_admin and administration_admin -->
            @hasanyrole('qa_admin|administration_admin')
                <div class="dropdown" id="managementDropdown">
                    <div class="dropdown-toggle">
                        <i class="fa-solid fa-tasks"></i> จัดการ <i class="fa-solid fa-caret-down ml-1"></i>
                    </div>
                    <div class="dropdown-menu">
                        @can('view-departments')
                            <a href="{{ route('departments.index') }}">
                                <i class="fa-solid fa-sitemap"></i> จัดการหน่วยงาน
                            </a>
                        @endcan
                        @can('view-standards')
                            <a href="{{ route('standards.index') }}">
                                <i class="fa-solid fa-layer-group"></i> จัดการมาตรฐาน
                            </a>
                        @endcan
                        @can('view-evidence')
                            <a href="{{ route('evidences.index') }}">
                                <i class="fa-solid fa-file-alt"></i> จัดการหลักฐาน
                            </a>
                        @endcan
                    </div>
                </div>
            @endhasanyrole

            <!-- User Menu - Available to all authenticated users -->
            <div class="dropdown" id="userDropdown">
                <div class="dropdown-toggle">
                    <img src="/uploads/avatar-type1.png" alt="User" class="user-avatar">
                    <span class=" sm:inline">{{ auth()->user()->name ?? 'ผู้ใช้' }}</span>
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
                    {{-- @can('edit-profile')
                        <a href="#"><i class="fa-solid fa-id-badge"></i> โปรไฟล์</a>
                    @endcan --}}
                    <hr>
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
