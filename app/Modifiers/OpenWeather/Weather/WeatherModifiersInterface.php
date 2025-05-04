<?php

namespace App\Modifiers\OpenWeather\Weather;

use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\OpenWeather\Weather;

interface WeatherModifiersInterface
{
    public function save(Weather $weather, WeatherDto $dto): void;
}
