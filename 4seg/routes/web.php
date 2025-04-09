<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->middleware('auth');
Route::resource('4seg', HomeController::class)->middleware('auth');
