<?php

namespace App\Providers\BindingInterfaces;

use App\Queries\Dvd\Actors\ActorQueries;
use App\Queries\Dvd\Actors\ActorQueriesInterface;
use App\Queries\Dvd\Films\FilmQueries;
use App\Queries\Dvd\Films\FilmQueriesInterface;
use App\Queries\Dvd\FilmsActors\FilmActorQueries;
use App\Queries\Dvd\FilmsActors\FilmActorQueriesInterface;
use App\Queries\Logs\OpenWeatherWeather\OpenWeatherWeatherQueries;
use App\Queries\Logs\OpenWeatherWeather\OpenWeatherWeatherQueriesInterface;
use App\Queries\Person\Users\UserQueries;
use App\Queries\Person\Users\UserQueriesInterface;
use App\Queries\Person\UsersCities\UserCityQueries;
use App\Queries\Person\UsersCities\UserCityQueriesInterface;
use App\Queries\Person\UsersFilms\UserFilmQueries;
use App\Queries\Person\UsersFilms\UserFilmQueriesInterface;
use App\Queries\Quiz\QuizAnswers\AdminQuizAnswerQueries;
use App\Queries\Quiz\QuizAnswers\AdminQuizAnswerQueriesInterface;
use App\Queries\Quiz\QuizItems\AdminQuizItemQueries;
use App\Queries\Quiz\QuizItems\AdminQuizItemQueriesInterface;
use App\Queries\Quiz\Quizzes\AdminQuizQueries;
use App\Queries\Quiz\Quizzes\AdminQuizQueriesInterface;
use App\Queries\Thesaurus\Cities\CityQueries;
use App\Queries\Thesaurus\Cities\CityQueriesInterface;
use App\Queries\Thesaurus\Languages\LanguageQueries;
use App\Queries\Thesaurus\Languages\LanguageQueriesInterface;
use App\Queries\Thesaurus\Timezones\TimezoneQueries;
use App\Queries\Thesaurus\Timezones\TimezoneQueriesInterface;
use Illuminate\Support\ServiceProvider;

final class QueriesProvider extends ServiceProvider
{
    const DEFAULT_LIMIT = 100;
    
    public function register(): void
    {
        $this->app->bind(ActorQueriesInterface::class, ActorQueries::class);
        
        $this->app->bind(FilmQueriesInterface::class, FilmQueries::class);
        
        $this->app->bind(FilmActorQueriesInterface::class, FilmActorQueries::class);
        
        $this->app->bind(OpenWeatherWeatherQueriesInterface::class, OpenWeatherWeatherQueries::class);
        
        $this->app->bind(UserQueriesInterface::class, UserQueries::class);
        
        $this->app->bind(UserCityQueriesInterface::class, UserCityQueries::class);
        
        $this->app->bind(UserFilmQueriesInterface::class, UserFilmQueries::class);
        
        $this->app->bind(AdminQuizAnswerQueriesInterface::class, AdminQuizAnswerQueries::class);
        
        $this->app->bind(AdminQuizItemQueriesInterface::class, AdminQuizItemQueries::class);
        
        $this->app->bind(AdminQuizQueriesInterface::class, AdminQuizQueries::class);
        
        $this->app->bind(CityQueriesInterface::class, CityQueries::class);
        
        $this->app->bind(LanguageQueriesInterface::class, LanguageQueries::class);
        
        $this->app->bind(TimezoneQueriesInterface::class, TimezoneQueries::class);
    }
}
