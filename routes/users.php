<?php

use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['role:Admin', 'permission:users.manage'])->prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserRoleController::class, 'index'])->name('index');
    Route::put('/{user}/role', [UserRoleController::class, 'update'])->name('role');
});
