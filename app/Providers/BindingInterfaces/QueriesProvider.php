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
use App\Queries\Quiz\Facades\TrialQueriesFacade;
use App\Queries\Quiz\Facades\TrialQueriesFacadeInterface;
use App\Queries\Quiz\QuizAnswers\QuizAnswerQueries;
use App\Queries\Quiz\QuizAnswers\QuizAnswerQueriesInterface;
use App\Queries\Quiz\QuizItems\QuizItemQueries;
use App\Queries\Quiz\QuizItems\QuizItemQueriesInterface;
use App\Queries\Quiz\Quizzes\QuizQueries;
use App\Queries\Quiz\Quizzes\QuizQueriesInterface;
use App\Queries\Quiz\TrialAnswers\TrialAnswerQueries;
use App\Queries\Quiz\TrialAnswers\TrialAnswerQueriesInterface;
use App\Queries\Quiz\Trials\TrialQueries;
use App\Queries\Quiz\Trials\TrialQueriesInterface;
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
        
        $this->app->bind(TrialQueriesFacadeInterface::class, TrialQueriesFacade::class);
        
        $this->app->bind(QuizAnswerQueriesInterface::class, QuizAnswerQueries::class);
        
        $this->app->bind(QuizItemQueriesInterface::class, QuizItemQueries::class);
        
        $this->app->bind(QuizQueriesInterface::class, QuizQueries::class);
        
        $this->app->bind(TrialAnswerQueriesInterface::class, TrialAnswerQueries::class);
        
        $this->app->bind(TrialQueriesInterface::class, TrialQueries::class);
        
        $this->app->bind(CityQueriesInterface::class, CityQueries::class);
        
        $this->app->bind(LanguageQueriesInterface::class, LanguageQueries::class);
        
        $this->app->bind(TimezoneQueriesInterface::class, TimezoneQueries::class);
    }
}
