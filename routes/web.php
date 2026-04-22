<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/', [AdminController::class, 'index'])->name('houses.index');
Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
