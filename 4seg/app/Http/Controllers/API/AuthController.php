<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Application\Services\AuthService;
use App\Http\Resources\UserResource;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Application\Services\TwoFactorService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\ThrottleRequestsException;


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

            Log::info('IP do usuário: ' . $request->ip());

            if (!$result) {
                Log::warning('Falha no login', ['email' => $request->email, 'ip' => $request->ip()]);
                return response()->json(['message' => 'Email, Senha ou IP inválidos'], 401);
            }

            Log::info('Login realizado com sucesso', ['user_id' => $result['user']->id ?? null]);

            return response()->json([
                'user' => new UserResource($result['user']),
                'two_factor_required' => $result['2fa_required'] ?? false,
            ]);
        } catch (ThrottleRequestsException $e) {
            Log::warning('Limite de login excedido', ['email' => $request->email, 'ip' => $request->ip()]);
            return response()->json([
                'message' => 'Você realizou muitas tentativas. Aguarde e tente novamente em instantes.'
            ], 429);
        } catch (\Exception $e) {
            Log::error('Erro no login', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'message' => $e->getMessage()
            ]);
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            Log::info('Usuário realizando logout', ['user_id' => $user->id ?? null]);
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

            Log::info('Tentativa de registro', ['email' => $request->email, 'ip' => $request->ip()]);
            Log::info('Usuário registrado com sucesso', ['user_id' => $result['user']->id]);

            return response()->json([
                'message' => 'Usuário criado com sucesso!',
                'user' => new UserResource($result['user']),
                'token' => $result['token']
            ], 201);
        } catch (ThrottleRequestsException $e) {
            Log::warning('Limite de registro excedido', ['email' => $request->email, 'ip' => $request->ip()]);
            return response()->json([
                'message' => 'Muitas tentativas de registro. Aguarde alguns minutos e tente novamente.'
            ], 429);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erro no registro', [
                'email' => $request->email ?? 'desconhecido',
                'ip' => $request->ip(),
                'message' => $e->getMessage()
            ]);
            return response()->json(['message' => 'Erro: ' . $e->getMessage()], 500);
        }
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
        ]);
        Log::info('Verificando código 2FA', ['email' => $request->email]);

        $token = $this->authService->verifyTwoFactor($request->email, $request->code);

        if (!$token) {
            Log::warning('Falha na verificação 2FA', ['email' => $request->email]);
            return response()->json(['message' => 'Código inválido ou expirado.'], 401);
        }

        Log::info('2FA confirmado com sucesso', ['email' => $request->email]);
        return response()->json(['token' => $token, 'message' => '2FA confirmado com sucesso']);
    }
}
