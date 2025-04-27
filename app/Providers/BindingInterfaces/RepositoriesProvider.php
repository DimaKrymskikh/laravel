<?php

namespace App\Providers\BindingInterfaces;

use App\Repositories\Dvd\ActorRepository;
use App\Repositories\Dvd\ActorRepositoryInterface;
use App\Repositories\Dvd\FilmActorRepository;
use App\Repositories\Dvd\FilmActorRepositoryInterface;
use App\Repositories\Logs\OpenWeatherWeatherRepository;
use App\Repositories\Logs\OpenWeatherWeatherRepositoryInterface;
use App\Repositories\OpenWeather\WeatherRepository;
use App\Repositories\OpenWeather\WeatherRepositoryInterface;
use App\Repositories\Person\UserCityRepository;
use App\Repositories\Person\UserCityRepositoryInterface;
use App\Repositories\Person\UserFilmRepository;
use App\Repositories\Person\UserFilmRepositoryInterface;
use App\Repositories\Person\UserRepository;
use App\Repositories\Person\UserRepositoryInterface;
use App\Repositories\Thesaurus\CityRepository;
use App\Repositories\Thesaurus\CityRepositoryInterface;
use App\Repositories\Thesaurus\LanguageRepository;
use App\Repositories\Thesaurus\LanguageRepositoryInterface;
use App\Repositories\Thesaurus\TimezoneRepository;
use App\Repositories\Thesaurus\TimezoneRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoriesProvider extends ServiceProvider
{
    const DEFAULT_LIMIT = 100;
    
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ActorRepositoryInterface::class, ActorRepository::class);
        
        $this->app->bind(FilmActorRepositoryInterface::class, FilmActorRepository::class);
        
        $this->app->bind(OpenWeatherWeatherRepositoryInterface::class, OpenWeatherWeatherRepository::class);
        
        $this->app->bind(WeatherRepositoryInterface::class, WeatherRepository::class);
        
        $this->app->bind(UserCityRepositoryInterface::class, UserCityRepository::class);
        
        $this->app->bind(UserFilmRepositoryInterface::class, UserFilmRepository::class);
        
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        
        $this->app->bind(CityRepositoryInterface::class, CityRepository::class);
        
        $this->app->bind(LanguageRepositoryInterface::class, LanguageRepository::class);
        
        $this->app->bind(TimezoneRepositoryInterface::class, TimezoneRepository::class);
    }
}
