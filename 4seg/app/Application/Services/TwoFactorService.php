<?php

namespace App\Application\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use App\Domain\User\ValueObjects\VerificationCode; 

class TwoFactorService
{
  public function sendVerificationCode(string $email): string
  {
    $code = VerificationCode::generate();

    session([
      '2fa_email' => $email,
      '2fa_code' => $code,
      '2fa_expires_at' => now()->addMinutes(10),
    ]);


    Mail::to($email)->send(new VerificationCodeMail($code));

    return $code;
  }

  public function verifyCode(string $email, string $code): bool
  {
    return session('2fa_email') === $email
      && session('2fa_code') === $code
      && now()->lessThan(session('2fa_expires_at'));
  }
}
