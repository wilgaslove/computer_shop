<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Libère le stock des commandes à payer en ligne jamais réglées (après 60 min)
Schedule::command('orders:cancel-unpaid')->everyTenMinutes();
