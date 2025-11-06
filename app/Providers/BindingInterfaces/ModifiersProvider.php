<?php

namespace App\Providers\BindingInterfaces;

use App\Modifiers\Dvd\Actors\ActorModifiers;
use App\Modifiers\Dvd\Actors\ActorModifiersInterface;
use App\Modifiers\Dvd\Films\FilmModifiers;
use App\Modifiers\Dvd\Films\FilmModifiersInterface;
use App\Modifiers\Dvd\FilmsActors\FilmActorModifiers;
use App\Modifiers\Dvd\FilmsActors\FilmActorModifiersInterface;
use App\Modifiers\OpenWeather\Weather\WeatherModifiers;
use App\Modifiers\OpenWeather\Weather\WeatherModifiersInterface;
use App\Modifiers\Person\Users\UserModifiers;
use App\Modifiers\Person\Users\UserModifiersInterface;
use App\Modifiers\Person\UsersCities\UserCityModifiers;
use App\Modifiers\Person\UsersCities\UserCityModifiersInterface;
use App\Modifiers\Person\UsersFilms\UserFilmModifiers;
use App\Modifiers\Person\UsersFilms\UserFilmModifiersInterface;
use App\Modifiers\Quiz\QuizAnswerModifiers;
use App\Modifiers\Quiz\QuizAnswerModifiersInterface;
use App\Modifiers\Quiz\QuizItemModifiers;
use App\Modifiers\Quiz\QuizItemModifiersInterface;
use App\Modifiers\Quiz\QuizModifiers;
use App\Modifiers\Quiz\QuizModifiersInterface;
use App\Modifiers\Thesaurus\Cities\CityModifiers;
use App\Modifiers\Thesaurus\Cities\CityModifiersInterface;
use App\Modifiers\Thesaurus\Languages\LanguageModifiers;
use App\Modifiers\Thesaurus\Languages\LanguageModifiersInterface;
use Illuminate\Support\ServiceProvider;

final class ModifiersProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ActorModifiersInterface::class, ActorModifiers::class);

        $this->app->bind(FilmModifiersInterface::class, FilmModifiers::class);

        $this->app->bind(FilmActorModifiersInterface::class, FilmActorModifiers::class);
        
        $this->app->bind(WeatherModifiersInterface::class, WeatherModifiers::class);
        
        $this->app->bind(UserModifiersInterface::class, UserModifiers::class);
        
        $this->app->bind(UserCityModifiersInterface::class, UserCityModifiers::class);
        
        $this->app->bind(UserFilmModifiersInterface::class, UserFilmModifiers::class);
        
        $this->app->bind(CityModifiersInterface::class, CityModifiers::class);
        
        $this->app->bind(LanguageModifiersInterface::class, LanguageModifiers::class);
        
        $this->app->bind(QuizAnswerModifiersInterface::class, QuizAnswerModifiers::class);
        $this->app->bind(QuizItemModifiersInterface::class, QuizItemModifiers::class);
        $this->app->bind(QuizModifiersInterface::class, QuizModifiers::class);
    }
}
