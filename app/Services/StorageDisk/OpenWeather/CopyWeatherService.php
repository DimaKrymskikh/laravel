<?php

namespace App\Services\StorageDisk\OpenWeather;

use App\Models\OpenWeather\Weather;
use App\Queries\OpenWeather\OpenWeatherQueriesInterface;
use App\StorageDisk\CopyingDatabaseDataToFile\OpenWeather\WeatherCopyistInterface;

final class CopyWeatherService
{
    public function __construct(
            private OpenWeatherQueriesInterface $queries,
            private WeatherCopyistInterface $copyist
    ) {
    }
    
    /**
     * Извлекает данные из таблицы 'open_weather.weather' и создаёт класс \Database\Copy\OpenWeather\WeatherData, хранящий эти данные.
     * 
     * @return void
     */
    public function copy(): void
    {
        $file = 'OpenWeather/WeatherData.php';
        
        $this->copyist->writeHeader($file, 'Database\Copy\OpenWeather', 'open_weather.weather', 'WeatherData');
        
        $this->queries->getListInLazyById(fn (Weather $weather) => $this->copyist->writeData($file, $weather));

        $this->copyist->writeFooter($file);
    }
}
