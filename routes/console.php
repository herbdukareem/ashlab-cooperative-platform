<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('about-platform', function (): void {
    $this->info('Ashlab Cooperative Platform');
    $this->line('Multi-tenant cooperative operations API.');
})->purpose('Display platform information');

