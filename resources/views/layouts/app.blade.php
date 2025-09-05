<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล')</title>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/css/layout.css', 'resources/js/app.js'])
    
    <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    @stack('styles')
</head>

<body>

    <!-- Navbar -->
    <x-navbar/>

    <!-- Main -->
    <main>
            @if (!empty($breadcrumbs))
                <ul class="breadcrumb">
                    <li><a href="{{ route('dashboard.index') }}"><i class="fas fa-home"></i></a></li>
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
            {{-- @if (session('success'))
                <div class="alert alert-success">
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()">×</button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    <span>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()">×</button>
                </div>
            @endif
            @if (session('warning'))
                <div class="alert alert-warning">
                    <span>{{ session('warning') }}</span>
                    <button onclick="this.parentElement.remove()">×</button>
                </div>
            @endif --}}

            @yield('content')

            <x-toasts />
    </main>

    <!-- Footer -->
    {{-- <footer class="static bottom-0 w-full bg-gray-800 text-white text-center py-4">
        &copy; {{ date('Y') }} ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล — Version 1.0.0
    </footer> --}}

    <footer>
        &copy; {{ date('Y') }} ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล — Version 1.0.0
    </footer>

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
