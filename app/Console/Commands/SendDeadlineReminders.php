<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\Indicator;
use App\Models\Setting;
use App\Notifications\DeadlineReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SendDeadlineReminders extends Command
{
    protected $signature = 'kpi:send-deadline-reminders';
    protected $description = 'Send reminder emails to assigned users near deadlines and on configured notify dates';

    public function handle(): int
    {
        Log::info('[reminder] command start');
        $setting = Setting::first();
        if (!$setting) {
            Log::info('[reminder] no settings found -> skip');
            $this->info('No settings found. Skipping.');
            return self::SUCCESS;
        }

        $now = Carbon::now('Asia/Bangkok');
        $today = $now->toDateString();

        // 1) Fixed notify dates (round 1 and 2)
        $this->sendByFixedDate($setting, $now);

        // 2) Automatic days-before reminders
        if ($setting->remind_enabled) {
            $this->sendByDaysBefore($setting, $now);
        } else {
            Log::info('[reminder] remind_enabled = false');
        }

        Log::info('[reminder] command end');
        return self::SUCCESS;
    }

    private function sendByFixedDate(Setting $setting, Carbon $now): void
    {
        $allowDup = (bool) env('REMINDER_ALLOW_DUPLICATES', false);
        $pairs = [
            ['date' => $setting->notify_date1, 'time' => $setting->notify_time1, 'key' => 'd1'],
            ['date' => $setting->notify_date2, 'time' => $setting->notify_time2, 'key' => 'd2'],
        ];

        foreach ($pairs as $p) {
            if (empty($p['date'])) { Log::info("[reminder] fixed: empty date for {$p['key']}"); continue; }
            $dateStr = Carbon::parse($p['date'])->toDateString();
            if ($now->toDateString() !== $dateStr) { Log::info("[reminder] fixed: today != {$dateStr}"); continue; }

            $time = $p['time'] ?: '09:00';
            [$hh,$mm] = array_pad(explode(':', $time), 2, '00');
            $trigger = $now->copy()->setTime((int)$hh, (int)$mm, 0);
            if ($now->lt($trigger)) { Log::info("[reminder] fixed: now < trigger {$time}"); continue; }

            $cacheKey = sprintf('reminder:fixed:%s:%s', $p['key'], $dateStr);
            if (!$allowDup && Cache::get($cacheKey)) { Log::info("[reminder] fixed: cached {$cacheKey}"); continue; }

            $count = $this->sendToAssignees($setting);
            Log::info("[reminder] fixed: sent to {$count} users");
            if (!$allowDup) {
                Cache::put($cacheKey, true, $now->copy()->endOfDay());
            } else {
                Log::info('[reminder] fixed: dedupe disabled, not caching');
            }
        }
    }

    private function sendByDaysBefore(Setting $setting, Carbon $now): void
    {
        $allowDup = (bool) env('REMINDER_ALLOW_DUPLICATES', false);
        $time = $setting->remind_time ?: '09:00';
        [$hh,$mm] = array_pad(explode(':', $time), 2, '00');
        $trigger = $now->copy()->setTime((int)$hh, (int)$mm, 0);
        if ($now->lt($trigger)) { Log::info('[reminder] before: now < remind_time'); return; }

        $days = collect(preg_split('/\s*,\s*/', (string) ($setting->remind_days ?? '')))
            ->filter(fn($v) => $v !== '')
            ->map(fn($v) => (int) $v)
            ->filter(fn($v) => $v >= 0)
            ->unique()->values();
        if ($days->isEmpty()) { Log::info('[reminder] before: days empty'); return; }

        // Find indicators whose deadline is today + N days and not finalized (status not in [3,4])
        $targets = Indicator::query()
            ->whereNotNull('deadline')
            ->whereNotIn('status', [3,4])
            ->get(['id','deadline']);

        foreach ($targets as $ind) {
            try {
                $deadlineDate = Carbon::parse($ind->deadline)->startOfDay();
            } catch (\Throwable $e) {
                continue;
            }
            $diff = $now->copy()->startOfDay()->diffInDays($deadlineDate, false);
            if (!$days->contains($diff)) continue;

            $cacheKey = sprintf('reminder:before:%d:%s', $ind->id, $now->toDateString());
            if (!$allowDup && Cache::get($cacheKey)) { Log::info("[reminder] before: cached {$cacheKey}"); continue; }

            $count = $this->sendToAssignees($setting, $ind->id);
            Log::info("[reminder] before: ind {$ind->id} -> sent to {$count} users");
            if (!$allowDup) {
                Cache::put($cacheKey, true, $now->copy()->endOfDay());
            } else {
                Log::info('[reminder] before: dedupe disabled, not caching');
            }
        }
    }

    private function sendToAssignees(Setting $setting, ?int $onlyIndicatorId = null): void
    {
        $title = $setting->title ?: '[KPI] แจ้งเตือนกำหนดส่ง';
        $msg = $setting->message ?: 'ใกล้ครบกำหนดส่งหลักฐาน โปรดตรวจสอบตัวชี้วัดที่รับผิดชอบ';

        $assignments = Assignment::query()
            ->when($onlyIndicatorId, fn($q) => $q->where('indicator_id', $onlyIndicatorId))
            ->with('collectorUser')
            ->get();

        $uniqueUsers = $assignments->pluck('collectorUser')->filter()->unique('id');
        Log::info('[reminder] send: users=' . $uniqueUsers->count() . ', indicator=' . ($onlyIndicatorId ?? 'all'));
        $url = route('dashboardkpi.index');

        foreach ($uniqueUsers as $user) {
            try {
                $user->notify(new DeadlineReminderNotification($title, $msg, $url));
            } catch (\Throwable $e) {
                // ignore individual failures
            }
        }
    }
}
