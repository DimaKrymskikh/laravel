<?php

namespace App\Modifiers\OpenWeather\Weather;

use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Modifiers\ModifiersInterface;

interface WeatherModifiersInterface extends ModifiersInterface
{
    /**
     * Сохраняет погоду в таблице open_weather.weather
     * 
     * @param WeatherDto $dto
     * @return void
     */
    public function updateOrCreate(WeatherDto $dto): void;
}
