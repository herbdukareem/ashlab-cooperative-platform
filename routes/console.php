<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('about-platform', function (): void {
    $this->info('Ashlab Cooperative Platform');
    $this->line('Multi-tenant cooperative operations API.');
})->purpose('Display platform information');

Schedule::command('contributions:generate')->dailyAt('00:10')->withoutOverlapping();
Schedule::command('contributions:refresh-statuses')->dailyAt('00:25')->withoutOverlapping();
Schedule::command('loans:service-arrears')->dailyAt('01:00')->withoutOverlapping();
