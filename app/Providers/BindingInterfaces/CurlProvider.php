<?php

namespace App\Providers\BindingInterfaces;

use App\Curl\OpenWeather\OpenWeatherRequests;
use App\Curl\OpenWeather\OpenWeatherRequestsInterface;
use Illuminate\Support\ServiceProvider;

/**
 * Провайдер для связывания классов и интерфейсов, которые реализуют curl-запросы.
 */
class CurlProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(OpenWeatherRequestsInterface::class, OpenWeatherRequests::class);
    }
}
