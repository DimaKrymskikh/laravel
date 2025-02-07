<?php

namespace Tests\Support\Data\Dto\Database\OpenWeather\Filters;

use App\DataTransferObjects\Database\OpenWeather\Filters\WeatherFilterDto;
use App\ValueObjects\DateString;

trait WeatherFilterDtoCase
{
    private function getBaseCaseWeatherFilterDto(): WeatherFilterDto
    {
        return new WeatherFilterDto(DateString::create('01.02.2025'), DateString::create('03.02.2025'));
    }
}
