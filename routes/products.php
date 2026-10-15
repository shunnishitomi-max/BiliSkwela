<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::resource('products', ProductController::class)
    ->middlewareFor(['index', 'show'], 'permission:products.view')
    ->middlewareFor(['create', 'store'], 'permission:products.create')
    ->middlewareFor(['edit', 'update'], 'permission:products.edit')
    ->middlewareFor('destroy', 'permission:products.delete');
