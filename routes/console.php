<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Run abandoned cart recovery daily at 10:00 AM
Schedule::command('cart:send-abandoned-emails')->dailyAt('10:00');

// Generate vendor payouts automatically every Monday for the previous week.
Schedule::command('payouts:generate --auto')->weeklyOn(1, '01:00');
