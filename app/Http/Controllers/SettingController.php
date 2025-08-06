<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();
        return view('setting.app', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'day_notify' => 'required|integer|min:1',
        ]);
        $existingsetting = Setting::where('name', $request->name)->first();
        if ($existingsetting) {
            return redirect()->route('setting.index')->with('error', 'มาตรฐานนี้มีอยู่แล้ว');
        }
        Setting::create([
            'name' => $request->name,
        ]);
        return redirect()->route('setting.index')->with('success', 'สร้างมาตรฐานนี้เรียบร้อย');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // ตรวจสอบชื่อซ้ำ (ไม่รวมตัวเอง)
        $existingSetting = Setting::where('name', $request->name)
            ->where('id', '!=', $id)
            ->first();

        if ($existingSetting) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['name' => 'ชื่อมาตรฐานนี้มีอยู่ในระบบ กรุณาใช้ชื่ออื่น']);
        }

        $setting = Setting::findOrFail($id);
        $setting->update([
            'name' => $request->name,
        ]);

        return redirect()->route('setting.index')->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว');
    }
}
