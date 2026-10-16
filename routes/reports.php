<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/reports', ReportController::class)->middleware('permission:reports.view')->name('reports.index');
