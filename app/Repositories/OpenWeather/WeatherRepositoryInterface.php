<?php

namespace App\Repositories\OpenWeather;

use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\OpenWeather\Weather;

interface WeatherRepositoryInterface
{
    public function save(Weather $weather, WeatherDto $dto): void;
}
