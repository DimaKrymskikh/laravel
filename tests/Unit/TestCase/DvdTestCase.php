<?php

namespace Tests\Unit\TestCase;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\DataTransferObjects\Database\Dvd\FilmDto;
use App\ValueObjects\IntValue;
use Tests\Unit\UnitTestCase;

abstract class DvdTestCase extends UnitTestCase
{
    protected function getFilmDto(): FilmDto
    {
        return new FilmDto('TestTitle', 'TestDescription', IntValue::create('2025'));
    }
    
    protected function getFilmFilterDto(): FilmFilterDto
    {
        return new FilmFilterDto('TestTitle', 'TestDescription', '2025', 'Русский');
    }
}
