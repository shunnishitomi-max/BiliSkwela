<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::resource('categories', CategoryController::class)
    ->middlewareFor(['index', 'show'], 'permission:categories.view')
    ->middlewareFor(['create', 'store'], 'permission:categories.create')
    ->middlewareFor(['edit', 'update'], 'permission:categories.edit')
    ->middlewareFor('destroy', 'permission:categories.delete');
