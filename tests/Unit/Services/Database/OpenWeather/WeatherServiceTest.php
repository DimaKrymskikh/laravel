<?php

namespace Tests\Unit\Services\Database\OpenWeather;

use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use App\Models\User;
use App\Modifiers\OpenWeather\Weather\WeatherModifiersInterface;
use App\Queries\Person\Users\UserQueriesInterface;
use App\Queries\Thesaurus\Cities\CityQueriesInterface;
use App\Queries\Thesaurus\Timezones\TimezoneQueriesInterface;
use App\Services\Database\OpenWeather\WeatherService;
use App\Services\Database\Thesaurus\TimezoneService;
use App\ValueObjects\ResponseObjects\OpenWeatherObject;
use Carbon\CarbonImmutable;
use Database\Testsupport\OpenWeather\OpenWeatherResponse;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;

class WeatherServiceTest extends TestCase
{
    use OpenWeatherResponse;
    
    private WeatherModifiersInterface $weatherModifiers;
    private UserQueriesInterface $userQueries;
    private CityQueriesInterface $cityQueries;
    private TimezoneQueriesInterface $timezoneQueries;
    private WeatherService $weatherService;
    private TimezoneService $timezoneService;
    
    public function test_success_update_or_create(): void
    {
        $cityId = 11;
        $weatherDto = new WeatherDto($cityId, OpenWeatherObject::create((object) $this->getWeatherForOneCity()));
        $weather = new Weather();
        
        $this->weatherModifiers->expects($this->once())
                ->method('updateOrCreate')
                ->with($weather, $weatherDto);
        
        $this->assertInstanceOf(Weather::class, $this->weatherService->updateOrCreate($weatherDto));
    }
    
    public function test_success_get_weather_in_cities_for_auth_user_by_user_id(): void
    {
        $userId = 7;
        $user = new User();
        $user->id = $userId;
        $cities = new Collection();
        
        $this->userQueries->expects($this->once())
                ->method('getById')
                ->with($userId)
                ->willReturn($user);
        
        $this->cityQueries->expects($this->once())
                ->method('getByUserWithWeather')
                ->with($this->identicalTo($user))
                ->willReturn($cities);
        
        $this->assertSame($cities, $this->weatherService->getWeatherInCitiesForAuthUserByUserId($userId));
    }
    
    public function test_success_get_latest_weather_for_one_city_by_city(): void
    {
        $cityId = 16;
        $city = new City();
        $city->weather = new Weather();
        $city->weather->created_at = new CarbonImmutable();
        
        $this->cityQueries->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($cityId))
                ->willReturn($city);
        
        $this->assertSame($city->weather, $this->weatherService->getLatestWeatherForOneCityByCityId($cityId));
    }
    
    protected function setUp(): void
    {
        $this->weatherModifiers = $this->createMock(WeatherModifiersInterface::class);
        $this->userQueries = $this->createMock(UserQueriesInterface::class);
        $this->cityQueries = $this->createMock(CityQueriesInterface::class);
        
        $this->timezoneQueries = $this->createMock(TimezoneQueriesInterface::class);
        $this->timezoneService = new TimezoneService($this->timezoneQueries);
        
        $this->weatherService = new WeatherService($this->weatherModifiers, $this->userQueries, $this->cityQueries, $this->timezoneService);
    }
}
