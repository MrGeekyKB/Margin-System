<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Http\Controllers\ProfitMarginController;

// Route::get('/', function () { 
//     return view('welcome');
// });

Route::get('/', [ProfitMarginController::class, 'show'])->name('profit.table');

Route::post('/save-margins', [ProfitMarginController::class, 'save'])->name('save.margins');
