<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Medicines\Http\Controllers\MedicineController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('medicines')
    ->name('medicines.')
    ->group(function (): void {
        Route::get('/', [MedicineController::class, 'index'])->name('index');
        Route::get('/create', [MedicineController::class, 'create'])->name('create');
        Route::post('/', [MedicineController::class, 'store'])->name('store');
        Route::get('/{medicine}', [MedicineController::class, 'show'])->name('show');
        Route::get('/{medicine}/edit', [MedicineController::class, 'edit'])->name('edit');
        Route::put('/{medicine}', [MedicineController::class, 'update'])->name('update');
        Route::delete('/{medicine}', [MedicineController::class, 'destroy'])->name('destroy');
    });
