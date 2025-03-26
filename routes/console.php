<?php

use App\Console\Commands\CheckPlanExpiry;
use App\Console\Commands\SendPlanExpirationReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command(CheckPlanExpiry::class)->daily();
Schedule::command(SendPlanExpirationReminders::class)->daily();
