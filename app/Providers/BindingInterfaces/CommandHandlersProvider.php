<?php

namespace App\Providers\BindingInterfaces;

use App\CommandHandlers\OpenWeather\Facades\OpenWeatherFacade;
use App\CommandHandlers\OpenWeather\Facades\OpenWeatherFacadeInterface;
use Illuminate\Support\ServiceProvider;

/**
 * Провайдер для связывания фасада с его интерфейсом.
 * Фасад должен быть в папке App\CommandHandlers.
 */
class CommandHandlersProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(OpenWeatherFacadeInterface::class, OpenWeatherFacade::class);
    }
}
