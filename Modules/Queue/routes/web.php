<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Queue\Http\Controllers\QueueEntryController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('queue')
    ->name('queue.')
    ->group(function (): void {
        Route::get('/', [QueueEntryController::class, 'index'])->name('index');
        Route::post('/', [QueueEntryController::class, 'store'])->name('store');
        Route::post('/call-next', [QueueEntryController::class, 'callNext'])->name('call-next');
        Route::patch('/{entry}/status', [QueueEntryController::class, 'updateStatus'])->name('status');
        Route::delete('/{entry}', [QueueEntryController::class, 'destroy'])->name('destroy');
    });
