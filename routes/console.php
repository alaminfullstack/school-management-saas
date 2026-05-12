<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Schedule the SMS Cleanup Command
 * Runs every day at midnight (Dhaka Time)
 */
Schedule::command('sms:cleanup-expired')
    ->daily()
    ->at('00:00')
    ->timezone('Asia/Dhaka');