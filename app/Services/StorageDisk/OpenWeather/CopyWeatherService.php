<?php

namespace App\Services\StorageDisk\OpenWeather;

use App\Models\OpenWeather\Weather;
use App\Queries\OpenWeather\OpenWeatherQueries;
use App\Services\ServiceManagerInterface;
use App\StorageDisk\CopyingDatabaseDataToFile\OpenWeather\WeatherCopyist;

final class CopyWeatherService
{
    private OpenWeatherQueries $queries;
    private WeatherCopyist $copyist;

    public function __construct(
            private ServiceManagerInterface $serviceManager
    ) {
        $this->queries = $this->serviceManager->getQueriesOrModifiers(OpenWeatherQueries::class);
        $this->copyist = $this->serviceManager->getCopyist(WeatherCopyist::class);
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
