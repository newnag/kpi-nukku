<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Assignment;
use App\Notifications\DeadlineReminderNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SettingNotifyController extends Controller
{
    public function sendNow(Request $request)
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

        if (array_key_exists('title', $validated) && $validated['title'] === '') {
            $validated['title'] = null;
        }
        $validated['remind_enabled'] = (bool) ($validated['remind_enabled'] ?? false);

        $setting = Setting::updateOrCreate(['id' => 1], $validated);

        // Clear fixed caches for today so sending now is not blocked by prior runs
        try {
            $today = Carbon::now('Asia/Bangkok')->toDateString();
            Cache::forget("reminder:fixed:d1:$today");
            Cache::forget("reminder:fixed:d2:$today");
        } catch (\Throwable $e) {
            // ignore
        }

        $sent = $this->sendRemindersNow($setting);

        return redirect()->route('settings.index')->with('success', "บันทึกแล้ว และส่งแจ้งเตือนทันที จำนวนผู้รับ {$sent} คน");
    }

    private function sendRemindersNow(Setting $setting): int
    {
        $title = $setting->title ?: '[KPI] แจ้งเตือนกำหนดส่ง';
        $msg = $setting->message ?: 'ใกล้ครบกำหนดส่งหลักฐาน โปรดตรวจสอบตัวชี้วัดที่รับผิดชอบ';

        $assignments = Assignment::with('collectorUser')->get();
        $uniqueUsers = $assignments->pluck('collectorUser')->filter()->unique('id');
        $url = route('dashboardkpi.index');

        $sent = 0;
        foreach ($uniqueUsers as $user) {
            try {
                $user->notify(new DeadlineReminderNotification($title, $msg, $url));
                $sent++;
            } catch (\Throwable $e) {
                Log::warning('sendRemindersNow failed for user '.$user->id.' : '.$e->getMessage());
            }
        }
        Log::info('[settings] send_now dispatched to '.$sent.' users');
        return $sent;
    }
}
