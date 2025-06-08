<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;


Route::get('/', function () {
  return view('welcome');
});



Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');  // mostra o formulário
Route::post('/login', [AuthController::class, 'login']);                       // processa o formulário

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);