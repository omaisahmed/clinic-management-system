<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Expenses\Http\Controllers\ExpenseCategoryController;
use Modules\Expenses\Http\Controllers\ExpenseController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('expenses')
    ->name('expenses.')
    ->group(function (): void {
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::get('/create', [ExpenseController::class, 'create'])->name('create');
        Route::post('/', [ExpenseController::class, 'store'])->name('store');
        Route::get('/{expense}', [ExpenseController::class, 'show'])->name('show');
        Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])->name('edit');
        Route::put('/{expense}', [ExpenseController::class, 'update'])->name('update');
        Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->name('destroy');

        Route::post('/categories', [ExpenseCategoryController::class, 'store'])->name('categories.store');
        Route::delete('/categories/{category}', [ExpenseCategoryController::class, 'destroy'])->name('categories.destroy');
    });
