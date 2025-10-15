<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule: run KPI deadline reminders every minute (the command self-throttles by time/date)
Schedule::command('kpi:send-deadline-reminders')->everyMinute();
