<?php

namespace App\Application\Services;

use App\Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class AuthService
{
    protected $userRepository;
    protected $twoFactorService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        TwoFactorService $twoFactorService
    ) {
        $this->userRepository = $userRepository;
        $this->twoFactorService = $twoFactorService;
    }

    public function login(array $credentials): ?array
    {
        $email = filter_var($credentials['email'], FILTER_SANITIZE_EMAIL);
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        $token = JWTAuth::fromUser($user);

        $this->twoFactorService->sendVerificationCode($user);

        session(['jwt_token' => $token]);

        return [
            'user' => $user,
            'token' => $token,
            '2fa_required' => true,
        ];
    }

    public function register(array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = $this->userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function verifyCode(User $user, string $code): bool
    {

        return $user->two_factor_secret === $code
            && $user->two_factor_expires_at
            && now()->lessThan($user->two_factor_expires_at);
    }

    public function verifyTwoFactor(string $email, string $code): ?string
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            print_r('Entrou aqui user');
            return null;
        }

        if (!$this->twoFactorService->verifyCode($user, $code)) {
            print_r('Entrou aqui twoFactorService ' . $this->twoFactorService->verifyCode($user, $code));
            return null;
        }

        $this->twoFactorService->clearTwoFactorCode($user);

        return JWTAuth::fromUser($user);
    }
}
