<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Reports\Http\Controllers\ReportController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('reports')
    ->name('reports.')
    ->group(function (): void {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
        Route::get('/patients-report', [ReportController::class, 'patientsReport'])->name('patients-report');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
    });
