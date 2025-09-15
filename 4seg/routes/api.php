<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;


Route::get('/', function () {
    return response()->json(['status' => 'API rodando com sucesso']);
});

Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:2,1');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:2,1');
Route::post('/verify-code', [AuthController::class, 'verifyCode'])->middleware('throttle:2,1');

// Rotas protegidas por JWT e Middleware
Route::middleware('auth:api')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
