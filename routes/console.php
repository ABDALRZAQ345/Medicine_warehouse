<?php

use App\Jobs\SendExpiryNotifications;
use App\Jobs\SendQuantityNotification;

//Artisan::command('inspire', function () {
//    $this->comment(Inspiring::quote());
//})->purpose('Display an inspiring quote')->hourly();
//
//Schedule::command('auth:clear-resets')->everyFifteenMinutes();

Schedule::command('delete_expired_medicines')->everyMinute();
Schedule::call(function () {
    dispatch(new SendExpiryNotifications);
    dispatch(new SendQuantityNotification);

})->everyMinute();
