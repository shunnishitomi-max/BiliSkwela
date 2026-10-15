<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'));
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('PhaseHome', ['phase'=>1]))->name('dashboard');
});