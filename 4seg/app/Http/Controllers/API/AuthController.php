<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Application\Services\AuthService;
use App\Http\Resources\UserResource;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $result = $this->authService->login($request->only('email', 'password'));

            if (!$result) {
                return response()->json(['message' => 'Credenciais inválidas'], 401);
            }

            return response()->json([
                'token' => $result['token'],
                'user' => new UserResource($result['user']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(), // Mensagem do erro
                'line' => $e->getLine(),       // Linha do erro
                'file' => $e->getFile(),       // Arquivo do erro
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    public function user(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        return new UserResource($user);
    }

    public function register(Request $request)
    {
        try {
            $result = $this->authService->register($request->all());

            print_r($result);

            return response()->json([
                'message' => 'Usuário criado com sucesso!',
                'user' => new UserResource($result['user']),
                'token' => $result['token']
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro: ' . $e->getMessage()], 500);
        }
    }

    // Criados para viasualizar o frontend rapidamente
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }
}
