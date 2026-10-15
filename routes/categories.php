<?php
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
Route::resource('categories', CategoryController::class)->middleware('role:Admin');
