<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();  // หรือ Setting::find(1) ถ้ามีแค่ 1 record
        return view('setting.app', compact('setting'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'day_notify' => 'required|integer|min:1',

        ]);

        // สมมติว่า settings มีแค่ 1 record (id=1) เก็บ config เดียว
        $data = $request->only(['title', 'day_notify',]);

        // updateOrCreate จะค้นหา id=1 ถ้ามีอัปเดต ถ้าไม่มีสร้างใหม่
        $setting = Setting::updateOrCreate(
            ['id' => 1],
            $data
        );

        return redirect()->route('settings.index')->with('success', 'บันทึกข้อมูลสำเร็จ!');
    }
}
