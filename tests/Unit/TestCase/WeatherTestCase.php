<?php

namespace Tests\Unit\TestCase;

use App\DataTransferObjects\Database\OpenWeather\Filters\WeatherFilterDto;
use App\ValueObjects\DateString;
use Tests\Unit\UnitTestCase;

abstract class WeatherTestCase extends UnitTestCase
{
    protected function getWeatherFilterDto(): WeatherFilterDto
    {
        return new WeatherFilterDto(DateString::create('01.02.2025'), DateString::create('03.02.2025'));
    }
}
