<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\ProductController;

// Product / Shop Routes
Route::get('/', [ProductController::class, 'index'])->name('products.index');
