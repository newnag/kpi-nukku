<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My App')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-[#f8fafc] w-full">
    @include('components.navbar')
    <header class="container mx-auto p-4">
        <h1 class="text-4xl font-bold ">@yield('header', 'Welcome!')</h1>
    </header>
    <main class="container mx-auto p-4">
        @yield('content')
    </main>
    {{-- <footer class="static bottom-0 w-full bg-gray-800 text-white text-center py-4">
        <p>&copy; {{ date('Y') }} My App</p>
    </footer> --}}
    @stack('scripts')
</body>

</html>
