<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Payments\Http\Controllers\PaymentController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('payments')
    ->name('payments.')
    ->group(function (): void {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/create', [PaymentController::class, 'create'])->name('create');
        Route::post('/', [PaymentController::class, 'store'])->name('store');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
        Route::get('/{payment}/edit', [PaymentController::class, 'edit'])->name('edit');
        Route::put('/{payment}', [PaymentController::class, 'update'])->name('update');
        Route::delete('/{payment}', [PaymentController::class, 'destroy'])->name('destroy');
    });
