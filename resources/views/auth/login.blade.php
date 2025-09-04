@extends('layouts.app')

@section('title', 'เข้าสู่ระบบ')
@section('header', '')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-300 px-4">
    <div class="max-w-md w-full bg-white shadow-xl rounded-2xl p-8">
        <div class="flex justify-center mb-4">
            <img src="{{ asset('favicon-msu.png') }}" alt="MSU Logo" class="h-28 w-28 object-contain" />
        </div>
        <h2 class="text-2xl font-extrabold text-gray-800 mb-6 text-center">เข้าสู่ระบบ</h2>

        <form id="loginForm" class="space-y-5" method="POST" action="{{ url('/login') }}">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">อีเมล</label>
                <input type="text" id="email" name="email" autocomplete="username"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="กรอกอีเมล" required>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">รหัสผ่าน</label>
                <input type="password" id="password" name="password" autocomplete="current-password"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="กรอกรหัสผ่าน" required>
            </div>

            <div id="error" class="text-red-500 text-sm hidden"></div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition duration-200">
                เข้าสู่ระบบ
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const form = document.getElementById('loginForm');
    const errorDiv = document.getElementById('error');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    form?.addEventListener('submit', async (e) => {
        e.preventDefault();
        errorDiv.classList.add('hidden');
        errorDiv.textContent = '';

        const payload = {
            email: form.email.value,
            password: form.password.value,
            remember: true
        };

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                credentials: 'include', // needed for Sanctum cookie auth
                body: JSON.stringify(payload)
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok) {
                const msg = data.message || 'เข้าสู่ระบบไม่สำเร็จ';
                throw new Error(msg);
            }

            // If your endpoint returns a token (token flow), store it; otherwise ignore (cookie flow)
            if (data.token) {
                try { localStorage.setItem('token', data.token); } catch (_) {}
            }

            const redirectTo = data.redirect || "{{ route('dashboard') }}";
            window.location.assign(redirectTo);
        } catch (err) {
            errorDiv.textContent = err.message || 'เกิดข้อผิดพลาด';
            errorDiv.classList.remove('hidden');
        }
    });
})();
</script>
@endpush
