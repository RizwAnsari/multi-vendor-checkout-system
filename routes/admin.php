<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\OrderController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});
