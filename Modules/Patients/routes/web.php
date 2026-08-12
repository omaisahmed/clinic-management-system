<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Patients\Http\Controllers\PatientController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('patients')
    ->name('patients.')
    ->group(function (): void {
        Route::get('/', [PatientController::class, 'index'])->name('index');
        Route::get('/create', [PatientController::class, 'create'])->name('create');
        Route::post('/', [PatientController::class, 'store'])->name('store');
        Route::get('/{patient}', [PatientController::class, 'show'])->name('show');
        Route::get('/{patient}/edit', [PatientController::class, 'edit'])->name('edit');
        Route::put('/{patient}', [PatientController::class, 'update'])->name('update');
        Route::delete('/{patient}', [PatientController::class, 'destroy'])->name('destroy');
        Route::post('/{patient}/contacts', [PatientController::class, 'storeContact'])->name('contacts.store');
        Route::delete('/{patient}/contacts/{contact}', [PatientController::class, 'destroyContact'])->name('contacts.destroy');
    });
