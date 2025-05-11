<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

// Rota para registrar um novo usuário
Route::post('/register', [AuthController::class, 'register']);

// Rota para login de usuário
Route::post('/login', [AuthController::class, 'login']);

// Rota protegida para obter os dados do usuário autenticado
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rota para logout (protegida, só pode ser acessada por usuários autenticados)
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
