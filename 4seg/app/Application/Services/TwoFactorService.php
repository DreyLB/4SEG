<?php

namespace App\Application\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use App\Domain\User\ValueObjects\VerificationCode;
use App\Models\User;

class TwoFactorService
{
  
  public function sendVerificationCode(User $user): string
  {
    $code = VerificationCode::generate();

    $user->two_factor_secret = $code;
    $user->two_factor_expires_at = now()->addMinutes(10);
    $user->save();


    Mail::to($user->email)->send(new VerificationCodeMail($code));

    return $code;
  }

  public function verifyCode(User $user, string $code): bool
  {
    return $user->two_factor_secret === $code
      && $user->two_factor_expires_at
      && now()->lessThanOrEqualTo($user->two_factor_expires_at);
  }

  public function clearTwoFactorCode(User $user): void
  {
    $user->two_factor_secret = null;
    $user->two_factor_expires_at = null;
    $user->save();
  }
}
