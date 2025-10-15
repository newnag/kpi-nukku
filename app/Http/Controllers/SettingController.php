<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

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
            'notify_time1' => ['nullable', 'date_format:H:i'],
            'notify_date2' => ['nullable', 'date'],
            'notify_time2' => ['nullable', 'date_format:H:i'],
            'message'      => ['nullable', 'string', 'max:500'],
            'remind_days'  => ['nullable', 'string', 'max:50'],
            'remind_time'  => ['nullable', 'date_format:H:i'],
            'remind_enabled' => ['nullable', 'boolean'],
        ]);

        // Normalize empty string to null for nullable fields
        if (array_key_exists('title', $validated) && $validated['title'] === '') {
            $validated['title'] = null;
        }

        // อัปเดตหรือสร้างแถวเดียว (id = 1)
        $validated['remind_enabled'] = (bool) ($validated['remind_enabled'] ?? false);

        $setting = Setting::updateOrCreate(
            ['id' => 1],
            $validated
        );

        // Auto-clear fixed reminder caches for today so new times take effect immediately
        try {
            $today = Carbon::now('Asia/Bangkok')->toDateString();
            Cache::forget("reminder:fixed:d1:$today");
            Cache::forget("reminder:fixed:d2:$today");
        } catch (\Throwable $e) {
            // ignore cache clear failures
        }

        return redirect()->route('settings.index')->with('success', 'บันทึกข้อมูลสำเร็จ!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title'        => ['nullable', 'string', 'max:255'],
            'notify_date1' => ['nullable', 'date'],
            'notify_time1' => ['nullable', 'date_format:H:i'],
            'notify_date2' => ['nullable', 'date'],
            'notify_time2' => ['nullable', 'date_format:H:i'],
            'message'      => ['nullable', 'string', 'max:500'],
            'remind_days'  => ['nullable', 'string', 'max:50'],
            'remind_time'  => ['nullable', 'date_format:H:i'],
            'remind_enabled' => ['nullable', 'boolean'],
        ]);

        // Normalize empty string to null for nullable fields
        if (array_key_exists('title', $validated) && $validated['title'] === '') {
            $validated['title'] = null;
        }

        $validated['remind_enabled'] = (bool) ($validated['remind_enabled'] ?? false);

        $setting = Setting::findOrFail($id);
        $setting->update($validated);

        // Auto-clear fixed reminder caches for today so new times take effect immediately
        try {
            $today = Carbon::now('Asia/Bangkok')->toDateString();
            Cache::forget("reminder:fixed:d1:$today");
            Cache::forget("reminder:fixed:d2:$today");
        } catch (\Throwable $e) {
            // ignore cache clear failures
        }

        return redirect()->route('settings.index')->with('success', 'อัปเดตข้อมูลสำเร็จ!');
    }
}
