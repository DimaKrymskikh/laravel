<?php

namespace Tests\Support\Data\Dto\Database\Dvd;

use App\DataTransferObjects\Database\Dvd\FilmDto;
use App\ValueObjects\IntValue;

trait FilmDtoCase
{
    private function getBaseCaseFilmDto(): FilmDto
    {
        return new FilmDto('TestTitle', 'TestDescription', IntValue::create('2025'));
    }
}
