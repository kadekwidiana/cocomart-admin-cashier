<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule::command('oxy:refresh-token')
//     ->cron('*/50 * * * *'); // production
// // ->everyTenSeconds(); // test local

Schedule::command('oxy:login')
    ->cron('*/50 * * * *'); // production
// ->everyTenSeconds(); // test local
