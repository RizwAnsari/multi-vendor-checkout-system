<?php

use App\Models\Customer\Cart;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\ProductController;

// Product Routes
Route::get('/', [ProductController::class, 'index'])->name('products.index');

// Cart Routes
Route::prefix('cart')
    ->name('cart.')
    ->can('manage', Cart::class)
    ->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/', [CartController::class, 'store'])->name('store');
        Route::patch('/{product}', [CartController::class, 'update'])->name('update');
        Route::delete('/{product}', [CartController::class, 'destroy'])->name('destroy');
    });
