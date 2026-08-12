<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Prescriptions\Http\Controllers\PrescriptionController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('prescriptions')
    ->name('prescriptions.')
    ->group(function (): void {
        Route::get('/', [PrescriptionController::class, 'index'])->name('index');
        Route::get('/create', [PrescriptionController::class, 'create'])->name('create');
        Route::post('/', [PrescriptionController::class, 'store'])->name('store');
        Route::get('/{prescription}', [PrescriptionController::class, 'show'])->name('show');
        Route::get('/{prescription}/edit', [PrescriptionController::class, 'edit'])->name('edit');
        Route::put('/{prescription}', [PrescriptionController::class, 'update'])->name('update');
        Route::delete('/{prescription}', [PrescriptionController::class, 'destroy'])->name('destroy');
        Route::get('/{prescription}/print', [PrescriptionController::class, 'print'])->name('print');
    });
