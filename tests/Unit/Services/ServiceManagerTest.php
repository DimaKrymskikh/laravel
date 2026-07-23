<?php

namespace Tests\Unit\Services;

use LogicException;
use App\Curl\OpenWeather\OpenWeatherRequests;
use App\Modifiers\Dvd\ActorModifiers;
use App\Modifiers\Dvd\FilmModifiers;
use App\Modifiers\Dvd\FilmActorModifiers;
use App\Modifiers\OpenWeather\WeatherModifiers;
use App\Modifiers\Person\UserCityModifiers;
use App\Modifiers\Person\UserFilmModifiers;
use App\Modifiers\Person\UserModifiers;
use App\Modifiers\Quiz\QuizAnswerModifiers;
use App\Modifiers\Quiz\QuizItemModifiers;
use App\Modifiers\Quiz\QuizModifiers;
use App\Modifiers\Quiz\TrialAnswerModifiers;
use App\Modifiers\Quiz\TrialModifiers;
use App\Modifiers\Thesaurus\CityModifiers;
use App\Modifiers\Thesaurus\LanguageModifiers;
use App\Queries\Dvd\ActorQueries;
use App\Queries\Dvd\FilmQueries;
use App\Queries\Dvd\FilmActorQueries;
use App\Queries\Logs\OpenWeatherWeatherQueries;
use App\Queries\OpenWeather\OpenWeatherQueries;
use App\Queries\Person\UserQueries;
use App\Queries\Person\UserCityQueries;
use App\Queries\Person\UserFilmQueries;
use App\Queries\Quiz\QuizAnswerQueries;
use App\Queries\Quiz\QuizItemQueries;
use App\Queries\Quiz\QuizQueries;
use App\Queries\Quiz\TrialAnswerQueries;
use App\Queries\Quiz\TrialQueries;
use App\Queries\Thesaurus\CityQueries;
use App\Queries\Thesaurus\LanguageQueries;
use App\Queries\Thesaurus\TimezoneQueries;
use App\Services\ServiceManager;
use App\StorageDisk\CopyingDatabaseDataToFile\Dvd\ActorsCopyist;
use App\StorageDisk\CopyingDatabaseDataToFile\Dvd\FilmsActorsCopyist;
use App\StorageDisk\CopyingDatabaseDataToFile\Dvd\FilmsCopyist;
use App\StorageDisk\CopyingDatabaseDataToFile\OpenWeather\WeatherCopyist;
use App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus\CitiesCopyist;
use App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus\LanguagesCopyist;
use App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus\TimezonesCopyist;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ServiceManagerTest extends TestCase
{
    public static function successGetQueriesOrModifiersProvider(): array
    {
        return [
            [ActorModifiers::class],
            [FilmModifiers::class],
            [FilmActorModifiers::class],
            [WeatherModifiers::class],
            [UserModifiers::class],
            [UserCityModifiers::class],
            [UserFilmModifiers::class],
            [QuizAnswerModifiers::class],
            [QuizItemModifiers::class],
            [QuizModifiers::class],
            [TrialAnswerModifiers::class],
            [TrialModifiers::class],
            [CityModifiers::class],
            [LanguageModifiers::class],
            [ActorQueries::class],
            [FilmQueries::class],
            [FilmActorQueries::class],
            [OpenWeatherWeatherQueries::class],
            [OpenWeatherQueries::class],
            [UserQueries::class],
            [UserCityQueries::class],
            [UserFilmQueries::class],
            [QuizAnswerQueries::class],
            [QuizItemQueries::class],
            [QuizQueries::class],
            [TrialAnswerQueries::class],
            [TrialQueries::class],
            [CityQueries::class],
            [LanguageQueries::class],
            [TimezoneQueries::class],
        ];
    }
    
    public static function successGetCopyistProvider(): array
    {
        return [
            [ActorsCopyist::class],
            [FilmsActorsCopyist::class],
            [FilmsCopyist::class],
            [WeatherCopyist::class],
            [CitiesCopyist::class],
            [LanguagesCopyist::class],
            [TimezonesCopyist::class],
        ];
    }
    
    public static function successGetCurlRequestProvider(): array
    {
        return [
            [OpenWeatherRequests::class],
        ];
    }
    
    #[DataProvider('successGetQueriesOrModifiersProvider')]
    public function test_success_getQueriesOrModifiers(string $className): void
    {
        $serviceManager = new ServiceManager();
        $this->assertInstanceOf($className, $serviceManager->getQueriesOrModifiers($className));
    }
    
    public function test_fail_getQueriesOrModifiers(): void
    {
        $this->expectException(LogicException::class);
        
        $serviceManager = new ServiceManager();
        $serviceManager->getQueriesOrModifiers(ActorsCopyist::class);
    }
    
    #[DataProvider('successGetCopyistProvider')]
    public function test_success_getCopyist(string $className): void
    {
        $serviceManager = new ServiceManager();
        $this->assertInstanceOf($className, $serviceManager->getCopyist($className));
    }
    
    public function test_fail_getCopyist(): void
    {
        $this->expectException(LogicException::class);
        
        $serviceManager = new ServiceManager();
        $serviceManager->getCopyist(ActorModifiers::class);
    }
    
    #[DataProvider('successGetCurlRequestProvider')]
    public function test_success_getCurlRequest(string $className): void
    {
        $serviceManager = new ServiceManager();
        $this->assertInstanceOf($className, $serviceManager->getCurlRequest($className));
    }
    
    public function test_fail_getCurlRequest(): void
    {
        $this->expectException(LogicException::class);
        
        $serviceManager = new ServiceManager();
        $serviceManager->getCurlRequest(ActorModifiers::class);
    }
}
