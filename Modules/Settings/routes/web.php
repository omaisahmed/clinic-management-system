<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Settings\Http\Controllers\SettingsController;

Route::middleware(['web', 'auth', 'verified'])
    ->name('settings.')
    ->group(function (): void {
        Route::get('/settings/{group?}', [SettingsController::class, 'index'])->name('index');
        Route::put('/settings/{group}', [SettingsController::class, 'update'])->name('update');
    });
