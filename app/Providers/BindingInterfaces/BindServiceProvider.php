<?php

namespace App\Providers\BindingInterfaces;

use App\Contracts\Services\Database\Thesaurus\InfoAboutCitiesInterface;
use App\Contracts\Services\Database\Thesaurus\TimezoneServiceInterface;
use App\Contracts\Services\OpenWeather\OpenWeatherInterface;
use App\Contracts\Services\ServiceManagerInterface;
use App\Services\Database\Thesaurus\CityService;
use App\Services\Database\Thesaurus\TimezoneService;
use App\Services\OpenWeather\WeatherService;
use App\Services\ServiceManager;
use Illuminate\Support\ServiceProvider;

class BindServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InfoAboutCitiesInterface::class, CityService::class);
        
        $this->app->bind(TimezoneServiceInterface::class, TimezoneService::class);
        
        $this->app->bind(OpenWeatherInterface::class, WeatherService::class);
        
        $this->app->bind(ServiceManagerInterface::class, ServiceManager::class);
    }
}
