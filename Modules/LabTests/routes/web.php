<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\LabTests\Http\Controllers\LabTestController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('lab-tests')
    ->name('lab_tests.')
    ->group(function (): void {
        Route::get('/', [LabTestController::class, 'index'])->name('index');
        Route::get('/create', [LabTestController::class, 'create'])->name('create');
        Route::post('/', [LabTestController::class, 'store'])->name('store');
        Route::get('/{test}/result', [LabTestController::class, 'result'])->name('result');
        Route::put('/{test}/result', [LabTestController::class, 'updateResult'])->name('result.update');
        Route::get('/{test}', [LabTestController::class, 'show'])->name('show');
        Route::get('/{test}/edit', [LabTestController::class, 'edit'])->name('edit');
        Route::put('/{test}', [LabTestController::class, 'update'])->name('update');
        Route::delete('/{test}', [LabTestController::class, 'destroy'])->name('destroy');
    });
