<?php

use App\Http\Controllers\StockMovementController;
use Illuminate\Support\Facades\Route;

Route::prefix('stock')->name('stock.')->group(function () {
    Route::get('/', [StockMovementController::class, 'index'])->middleware('permission:stock.view')->name('index');
    Route::post('/', [StockMovementController::class, 'store'])->middleware('permission:stock.in|stock.out')->name('store');
});
