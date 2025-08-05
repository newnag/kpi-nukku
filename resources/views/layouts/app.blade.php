<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts: Prompt -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f8f9fa;
        }

        .navbar-custom {
            background: linear-gradient(135deg, #fefeff 0%, #ffffff 100%);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 600;
            color: rgb(0, 0, 0) !important;
        }

        .navbar-collapse {
            flex-grow: 0;
        }

        .nav-link {
            color: rgba(0, 0, 0, 0.9) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0 5px;
            padding: 8px 16px !important;
        }

        .nav-link:hover,
        .nav-link.active {
            color: rgb(0, 0, 0) !important;
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }

        .main-content {
            min-height: calc(100vh - 80px);
            padding: 20px 0;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .breadcrumb-custom {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 14px;
            }

            .nav-link {
                font-size: 14px;
                padding: 6px 12px !important;
            }
        }
    </style>

    @yield('styles')
</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <!-- Logo and Brand -->
            <a class="navbar-brand d-flex align-items-center" href="">
                <div class="logo-icon">
                    <i class="fas fa-hospital-alt text-primary"></i>
                </div>
                <span class="d-none d-md-inline">ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล</span>
                <span class="d-md-none">ระบบจัดการข้อมูล</span>
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="">
                            <i class="fas fa-tachometer-alt me-1"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('standards*') ? 'active' : '' }}" href="">
                            <i class="fas fa-clipboard-list me-1"></i>
                            Standards
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('indicators*') ? 'active' : '' }}" href="">
                            <i class="fas fa-chart-line me-1"></i>
                            Indicators
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports*') ? 'active' : '' }}" href="">
                            <i class="fas fa-file-alt me-1"></i>
                            Reports
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                            data-bs-toggle="dropdown">
                            {{-- <img src="https://via.placeholder.com/35x35/6c757d/ffffff?text=U" alt="User" class="user-avatar me-2"> --}}
                            <span class="d-none d-md-inline">Settings</span>
                            <i class="fas fa-cog ms-1"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="">
                                    <i class="fas fa-user me-2"></i>ตั้งค่าหน่วยงาน/กำหนดวันแจ้งเตือน
                                </a>
                            </li>
                            <li><a class="dropdown-item" href="">
                                    <i class="fas fa-cog me-2"></i>ตั้งค่าประเภทตัวชี้วัด
                                </a>
                            </li>
                            <li><a class="dropdown-item" href="">
                                    <i class="fas fa-cog me-2"></i>จัดการข้อมูลมาตรฐาน/ด้านต่างๆ
                                </a>
                            </li>


                        </ul>
                    </li>
                </ul>

                <!-- User Menu -->
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                            data-bs-toggle="dropdown">
                            <img src="/uploads/avatar-type1.png" alt="User" class="user-avatar me-2 rounded-circle">

                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="">
                                    <i class="fas fa-user me-2"></i>โปรไฟล์
                                </a></li>
                            <li><a class="dropdown-item" href="">
                                    <i class="fas fa-cog me-2"></i>ตั้งค่าระบบ
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href=""
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>ออกจากระบบ
                                </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid main-content">
        <!-- Breadcrumb -->
        @if (!empty($breadcrumbs))
            <div class="breadcrumb-custom">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">
                                <i class="fas fa-home"></i>
                            </a>
                        </li>
                        @foreach ($breadcrumbs as $breadcrumb)
                            @if ($loop->last)
                                <li class="breadcrumb-item active">{{ $breadcrumb['title'] }}</li>
                            @else
                                <li class="breadcrumb-item">
                                    <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </nav>
            </div>
        @endif

        <!-- Page Header -->
        @hasSection('page-header')
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @yield('page-header')
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Alert Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Main Content Area -->
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="bg-white border-top mt-5 py-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        &copy; {{ date('Y') }} ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">
                        Version 1.0.0
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Logout Form -->
    <form id="logout-form" action="" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @yield('scripts')

    <script>
        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Active menu highlighting
        $(document).ready(function() {
            var currentPath = window.location.pathname;
            $('.nav-link').each(function() {
                if ($(this).attr('href') === currentPath) {
                    $(this).addClass('active');
                }
            });
        });
    </script>
</body>

</html>
