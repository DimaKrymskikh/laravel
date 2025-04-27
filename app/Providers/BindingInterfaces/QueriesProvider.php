<?php

namespace App\Providers\BindingInterfaces;

use App\Queries\Dvd\Films\FilmQueries;
use App\Queries\Dvd\Films\FilmQueriesInterface;
use App\Queries\Thesaurus\Languages\LanguageQueries;
use App\Queries\Thesaurus\Languages\LanguageQueriesInterface;
use Illuminate\Support\ServiceProvider;

class QueriesProvider extends ServiceProvider
{
    const DEFAULT_LIMIT = 100;
    
    public function register(): void
    {
        $this->app->bind(FilmQueriesInterface::class, FilmQueries::class);
        
        $this->app->bind(LanguageQueriesInterface::class, LanguageQueries::class);
    }
}
