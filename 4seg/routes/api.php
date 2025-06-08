<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Controller;

// routes/api.php
Route::get('/', function () {
    return response()->json(['status' => 'API rodando com sucesso']);
});

// Rota para registrar um novo usuário
Route::post('/register', [AuthController::class, 'register']);

// Rota para login de usuário
Route::post('/login', [AuthController::class, 'login']);



// Rotas protegidas por JWT
Route::middleware('auth:api')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
