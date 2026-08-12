<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecords\Http\Controllers\PatientMedicalHistoryController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('patients/{patient}/medical-records')
    ->name('medical-records.')
    ->group(function (): void {
        Route::post('/{type}', [PatientMedicalHistoryController::class, 'store'])->name('store');
        Route::delete('/{type}/{id}', [PatientMedicalHistoryController::class, 'destroy'])->name('destroy');
    });
