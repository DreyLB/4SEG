<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Application\Services\AuthService;
use App\Http\Resources\UserResource;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Application\Services\TwoFactorService;


class AuthController extends Controller
{
    protected $authService;
    protected $twoFactorService;

    public function __construct(AuthService $authService, TwoFactorService $twoFactorService)
    {
        $this->authService = $authService;
        $this->twoFactorService = $twoFactorService;
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $result = $this->authService->login($request->only('email', 'password'));

            if (!$result) {
                return response()->json(['message' => 'Credenciais inválidas'], 401);
            }

            return response()->json([
                /* 'token' => $result['token'], */
                'user' => new UserResource($result['user']),
                'two_factor_required' => $result['2fa_required'] ?? false,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if ($user) {
                $this->twoFactorService->clearTwoFactorCode($user);
            }

            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json(['message' => 'Logout realizado com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token inválido ou ausente.'], 401);
        }
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

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
        ]); 

        $token = $this->authService->verifyTwoFactor($request->email, $request->code);

        if (!$token) {
            return response()->json(['message' => 'Código inválido ou expirado.'], 401);
        }

        return response()->json(['token' => $token, 'message' => '2FA confirmado com sucesso']);
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
