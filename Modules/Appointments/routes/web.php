<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Appointments\Http\Controllers\AppointmentController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('appointments')
    ->name('appointments.')
    ->group(function (): void {
        Route::get('/', [AppointmentController::class, 'index'])->name('index');
        Route::get('/create', [AppointmentController::class, 'create'])->name('create');
        Route::post('/', [AppointmentController::class, 'store'])->name('store');
        Route::get('/{appointment}', [AppointmentController::class, 'show'])->name('show');
        Route::get('/{appointment}/edit', [AppointmentController::class, 'edit'])->name('edit');
        Route::put('/{appointment}', [AppointmentController::class, 'update'])->name('update');
        Route::delete('/{appointment}', [AppointmentController::class, 'destroy'])->name('destroy');
    });
