<?php

namespace App\Services\Database\Person\Dto;

final readonly class RegisterDto {
    public function __construct(
            public string $login,
            public string $email,
            public string $password,
    ) {
    }
}
