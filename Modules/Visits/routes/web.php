<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Visits\Http\Controllers\VisitController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('visits')
    ->name('visits.')
    ->group(function (): void {
        Route::get('/', [VisitController::class, 'index'])->name('index');
        Route::get('/create', [VisitController::class, 'create'])->name('create');
        Route::post('/', [VisitController::class, 'store'])->name('store');
        Route::get('/{visit}', [VisitController::class, 'show'])->name('show');
        Route::get('/{visit}/edit', [VisitController::class, 'edit'])->name('edit');
        Route::put('/{visit}', [VisitController::class, 'update'])->name('update');
        Route::put('/{visit}/complete', [VisitController::class, 'complete'])->name('complete');
        Route::delete('/{visit}', [VisitController::class, 'destroy'])->name('destroy');
    });
