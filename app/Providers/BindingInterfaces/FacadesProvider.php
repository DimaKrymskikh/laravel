<?php

namespace App\Providers\BindingInterfaces;

use App\Support\Facades\Queries\Quiz\TrialQueriesFacade;
use App\Support\Facades\Queries\Quiz\TrialQueriesFacadeInterface;
use App\Support\Facades\Services\OpenWeather\OpenWeatherFacade;
use App\Support\Facades\Services\OpenWeather\OpenWeatherFacadeInterface;
use Illuminate\Support\ServiceProvider;

class FacadesProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TrialQueriesFacadeInterface::class, TrialQueriesFacade::class);
        
        $this->app->bind(OpenWeatherFacadeInterface::class, OpenWeatherFacade::class);
    }
}
