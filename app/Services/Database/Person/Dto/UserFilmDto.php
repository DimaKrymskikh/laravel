<?php

namespace App\Services\Database\Person\Dto;

final readonly class UserFilmDto
{
    public function __construct(
        public int $userId,
        public int $filmId,
    ) {
    }
}
