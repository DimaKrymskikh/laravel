<?php

namespace App\Providers\BindingInterfaces;

use App\StorageDisk\CopyingDatabaseDataToFile\Dvd\ActorsCopyist;
use App\StorageDisk\CopyingDatabaseDataToFile\Dvd\ActorsCopyistInterface;
use App\StorageDisk\CopyingDatabaseDataToFile\Dvd\FilmsActorsCopyist;
use App\StorageDisk\CopyingDatabaseDataToFile\Dvd\FilmsActorsCopyistInterface;
use App\StorageDisk\CopyingDatabaseDataToFile\Dvd\FilmsCopyist;
use App\StorageDisk\CopyingDatabaseDataToFile\Dvd\FilmsCopyistInterface;
use App\StorageDisk\CopyingDatabaseDataToFile\OpenWeather\WeatherCopyist;
use App\StorageDisk\CopyingDatabaseDataToFile\OpenWeather\WeatherCopyistInterface;
use App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus\CitiesCopyist;
use App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus\CitiesCopyistInterface;
use App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus\LanguagesCopyist;
use App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus\LanguagesCopyistInterface;
use App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus\TimezonesCopyist;
use App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus\TimezonesCopyistInterface;
use Illuminate\Support\ServiceProvider;

final class CopyistProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ActorsCopyistInterface::class, ActorsCopyist::class);
        
        $this->app->bind(FilmsActorsCopyistInterface::class, FilmsActorsCopyist::class);
        
        $this->app->bind(FilmsCopyistInterface::class, FilmsCopyist::class);
        
        $this->app->bind(WeatherCopyistInterface::class, WeatherCopyist::class);
        
        $this->app->bind(CitiesCopyistInterface::class, CitiesCopyist::class);
        
        $this->app->bind(LanguagesCopyistInterface::class, LanguagesCopyist::class);
        
        $this->app->bind(TimezonesCopyistInterface::class, TimezonesCopyist::class);
    }
}
