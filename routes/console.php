<?php

use App\Jobs\BillRunJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\SendPaymentReminderEmails;
use \Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::job(new SendPaymentReminderEmails)->twiceMonthly(5, 26, '13:00');
Schedule::job(new BillRunJob)->monthly();


//Schedule::job(new SendPaymentReminderEmails)->everyMinute();
// php artisan schedule:work - runs the scheduler which will queue the job every ...
//php artisan queue:work - runs the jobs on the queue
