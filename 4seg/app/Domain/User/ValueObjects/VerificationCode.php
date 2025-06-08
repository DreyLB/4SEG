<?php

namespace App\Domain\User\ValueObjects;

class VerificationCode
{
  public static function generate(): string
  {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
  }
}
