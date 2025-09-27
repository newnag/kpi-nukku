@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto bg-white shadow rounded p-6">
        <h2 class="text-xl font-bold mb-4">รายการรายงาน SAR</h2>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-2 rounded mb-3">
                {{ session('success') }}
            </div>
        @endif

        <table class="w-full border-collapse border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-2 py-1">ปี</th>
                    <th class="border px-2 py-1">ชื่อเอกสาร</th>
                    <th class="border px-2 py-1">วันที่</th>
                    <th class="border px-2 py-1">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $r)
                    <tr>
                        <td class="border px-2 py-1">{{ $r->year }}</td>
                        <td class="border px-2 py-1">องค์ประกอบ SAR รับรองสถาบัน คณะพยาบาลศาสตร์</td>
                        <td class="border px-2 py-1">{{ $r->created_at ? $r->created_at->format('d/m/Y') : '-' }}</td>


                     <td class="border px-2 py-1 space-x-2">
    <a href="{{ route('sar_reports.edit', $r->id) }}" class="text-blue-600">แก้ไข</a>
    <a href="{{ route('sar_reports.create') }}" class="text-green-600">เพิ่ม</a>
</td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center p-3">ยังไม่มีข้อมูล</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $reports->links() }}
        </div>
    </div>
@endsection
