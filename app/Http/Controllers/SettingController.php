<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // ดึงเรคอร์ดเดียว ถ้าไม่มีจะคืน null
        $setting = Setting::first();
        return view('setting.app', compact('setting'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => ['nullable', 'string', 'max:255'],
            'notify_date1' => ['nullable', 'date'],
            'notify_date2' => ['nullable', 'date'],
            'message'      => ['nullable', 'string', 'max:500'],
        ]);

        // อัปเดตหรือสร้างแถวเดียว (id = 1)
        $setting = Setting::updateOrCreate(
            ['id' => 1],
            $validated
        );

        return redirect()->route('settings.index')->with('success', 'บันทึกข้อมูลสำเร็จ!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title'        => ['nullable', 'string', 'max:255'],
            'notify_date1' => ['nullable', 'date'],
            'notify_date2' => ['nullable', 'date'],
            'message'      => ['nullable', 'string', 'max:500'],
        ]);

        $setting = Setting::findOrFail($id);
        $setting->update($validated);

        return redirect()->route('settings.index')->with('success', 'อัปเดตข้อมูลสำเร็จ!');
    }
}
