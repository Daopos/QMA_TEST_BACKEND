<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('tests:update-status')->everyMinute(); // Adjust frequency as needed
Schedule::command('classwork:update-status')->everyMinute(); // Adjust frequency as needed