<?php

namespace Tests\Unit\Services\Database\Thesaurus;

use App\Support\Collections\Thesaurus\TimezoneCollection;

class TimezoneServiceTest extends TimezoneServiceCase
{
    public function test_success_getAllTimezonesList(): void
    {
        $this->assertInstanceOf(TimezoneCollection::class, $this->timezoneService->getAllTimezonesList($this->filterName));
    }
    
    public function test_success_setCityTimezoneForWeatherData(): void
    {
        $city = $this->factoryCity();
        
        $weather = $this->factoryWeather();
        
        $this->assertNull($this->timezoneService->setCityTimezoneForWeatherData($city, $weather));
    }
    
    public function test_success_setCityTimezoneForCollectionOfWeatherData(): void
    {
        $city = $this->factoryCity();
        
        $weather = $this->factoryWeatherCollection();
        
        $this->assertNull($this->timezoneService->setCityTimezoneForCollectionOfWeatherData($city, $weather));
    }
    
    public function test_success_setTimezoneOfCitiesForWeatherData(): void
    {
        $cities = $this->factoryCityCollectionWithWeather();
        
        $this->assertNull($this->timezoneService->setTimezoneOfCitiesForWeatherData($cities));
    }
}
