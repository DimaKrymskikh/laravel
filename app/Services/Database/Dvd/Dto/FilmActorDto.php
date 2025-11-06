<?php

namespace App\Services\Database\Dvd\Dto;

final readonly class FilmActorDto
{
    public function __construct(
        public int $filmId,
        public int $actorId,
    ) {
    }
}
