<?php

namespace App\Services\Database\Person\Dto;

final readonly class UserCityDto
{
    public function __construct(
        public int $userId,
        public int $cityId,
    ) {
    }
}
