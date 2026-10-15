<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'));
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('PhaseHome', ['phase'=>2]))->name('dashboard');
    require __DIR__.'/categories.php';
    require __DIR__.'/suppliers.php';
});