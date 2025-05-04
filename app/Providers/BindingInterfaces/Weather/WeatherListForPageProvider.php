<?php

namespace App\Providers\BindingInterfaces\Weather;

use Illuminate\Support\ServiceProvider;

use App\CommandHandlers\Database\Logs\Weather\WeatherListForPageCommandHandler;
use App\Http\Controllers\Project\Auth\Account\UserLogsWeatherController;
use App\Queries\Logs\OpenWeatherWeather\WeatherListForPage\UserWeatherListForPageQueries;
use App\Queries\Thesaurus\Timezones\TimezoneQueries;
use App\Services\Database\Thesaurus\TimezoneService;
use App\Support\Pagination\Logs\WeatherPagination;

class WeatherListForPageProvider extends ServiceProvider
{
    public function register(): void
    {
        $weatherPagination = new WeatherPagination();
        $timezoneService = new TimezoneService(new TimezoneQueries());
        
        $this->app->when(UserLogsWeatherController::class)
                ->needs(WeatherListForPageCommandHandler::class)
                ->give(function () use ($weatherPagination, $timezoneService) {
                    $paginator = new UserWeatherListForPageQueries($weatherPagination);
                    return new WeatherListForPageCommandHandler($paginator, $timezoneService);
                });
    }
}
