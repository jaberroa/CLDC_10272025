<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programación: marcar cuotas vencidas diariamente a la 01:00 AM
Schedule::command('cuotas:mark-overdue')->dailyAt('01:00');
