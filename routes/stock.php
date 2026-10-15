<?php

use App\Http\Controllers\StockMovementController;
use Illuminate\Support\Facades\Route;

Route::prefix('stock')->name('stock.')->group(function () {
    Route::get('/', [StockMovementController::class, 'index'])->middleware('role:Admin')->name('index');
    Route::post('/', [StockMovementController::class, 'store'])->middleware('role:Admin')->name('store');
});
