<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Billing\Http\Controllers\InvoiceController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('billing')
    ->name('billing.')
    ->group(function (): void {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
        Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
    });
