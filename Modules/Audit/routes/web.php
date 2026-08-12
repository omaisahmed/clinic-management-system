<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Audit\Http\Controllers\AuditLogController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('audit-logs')
    ->name('audit.')
    ->group(function (): void {
        Route::get('/', [AuditLogController::class, 'index'])->name('logs.index');
    });
