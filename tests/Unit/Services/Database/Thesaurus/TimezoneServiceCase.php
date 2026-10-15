<?php

namespace Tests\Unit\Services\Database\Thesaurus;

use App\Contracts\Services\ServiceManagerInterface;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use App\Queries\Thesaurus\TimezoneQueries;
use App\Services\Database\Thesaurus\TimezoneService;
use App\Support\Collections\OpenWeather\WeatherCollection;
use App\Support\Collections\Thesaurus\CityCollection;
use PHPUnit\Framework\TestCase;

class TimezoneServiceCase extends TestCase
{
    protected ServiceManagerInterface $serviceManager;
    protected TimezoneQueries $timezoneQueries;
    protected TimezoneService $timezoneService;
    protected string $filterName = 'TestFilter';
    
    protected function factoryCity(): City
    {
        return City::factory()
                ->state([
                    'timezone_id' => null,
                ])
                ->make();
    }
    
    protected function factoryCityCollectionWithWeather(): CityCollection
    {
        $weather = $this->factoryWeather();
        
        $cities = City::factory()->count(2)->make();
        
        $i = 0;
        
        // Метод коллекции map вернёт Illuminate\Support\Collection.
        foreach ($cities as $city) {
            if (++$i % 2) {
                $city->weather = $weather;
            } else {
                $city->weather = null;
            }
        }
        
        return $cities;
    }
    
    protected function factoryWeather(): Weather
    {
        return Weather::factory()->make();
    }
    
    protected function factoryWeatherCollection(): WeatherCollection
    {
        return Weather::factory()->count(2)->make();
    }
    
    protected function setUp(): void
    {
        $this->timezoneQueries = $this->createStub(TimezoneQueries::class);
        
        $this->serviceManager = $this->createStub(ServiceManagerInterface::class);
        $this->serviceManager->method('getQueriesOrModifiers')
                ->willReturn($this->timezoneQueries);
        
        $this->timezoneService = new TimezoneService($this->serviceManager);
    }
}
