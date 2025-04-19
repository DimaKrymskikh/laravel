<?php

namespace Tests\Support\Data\Dto\Database\Dvd\Filters;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;

trait FilmFilterDtoCase
{
    private function getBaseCaseFilmFilterDto(): FilmFilterDto
    {
        return new FilmFilterDto('TestTitle', 'TestDescription', '2025', 'Русский');
    }
}
