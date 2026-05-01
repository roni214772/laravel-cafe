<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Her gece 02:00'de veritabanı yedeği al
Schedule::command('db:backup')->dailyAt('02:00');

// Her 30 saniyede platformlardan sipariş çek (Trendyol, Yemeksepeti, Getir)
Schedule::command('orders:fetch-platforms')->everyMinute();
