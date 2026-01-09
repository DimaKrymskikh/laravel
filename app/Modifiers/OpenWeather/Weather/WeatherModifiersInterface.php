<?php

namespace App\Modifiers\OpenWeather\Weather;

use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\OpenWeather\Weather;
use App\Modifiers\ModifiersInterface;

interface WeatherModifiersInterface extends ModifiersInterface
{
    /**
     * Сохраняет погоду в таблице open_weather.weather
     * 
     * @param Weather $weather
     * @param WeatherDto $dto
     * @return void
     */
    public function updateOrCreate(Weather $weather, WeatherDto $dto): void;
}
