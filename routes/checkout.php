<?php

use App\Models\Customer\Cart;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\CheckoutController;

// Checkout is only possible if user can manage their cart
Route::middleware(['auth', 'can:manage,' . Cart::class])
    ->prefix('checkout')
    ->name('checkout.')
    ->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/', [CheckoutController::class, 'store'])->name('store');
        Route::get('/success', [CheckoutController::class, 'success'])->name('success');
    });
