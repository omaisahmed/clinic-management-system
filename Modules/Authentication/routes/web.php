<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Authentication\Http\Controllers\UserController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('staff')
    ->name('authentication.')
    ->group(function (): void {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
