<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'));
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/settings', fn () => Inertia::render('Settings'))->name('settings');
    require __DIR__.'/categories.php';
    require __DIR__.'/suppliers.php';
    require __DIR__.'/products.php';
    require __DIR__.'/stock.php';
    require __DIR__.'/reports.php';
    require __DIR__.'/users.php';
});
