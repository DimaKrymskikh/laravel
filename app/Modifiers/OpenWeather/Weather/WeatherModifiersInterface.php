<?php

namespace App\Modifiers\OpenWeather\Weather;

use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\OpenWeather\Weather;
use App\Modifiers\ModifiersInterface;

interface WeatherModifiersInterface extends ModifiersInterface
{
    public function updateOrCreate(Weather $weather, WeatherDto $dto): void;
}
