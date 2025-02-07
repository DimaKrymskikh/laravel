<?php

namespace Tests\Unit\Services\Database\OpenWeather;

use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use App\Models\User;
use App\Repositories\OpenWeather\WeatherRepositoryInterface;
use App\Repositories\Person\UserRepositoryInterface;
use App\Repositories\Thesaurus\CityRepositoryInterface;
use App\Repositories\Thesaurus\TimezoneRepositoryInterface;
use App\Services\Database\OpenWeather\WeatherService;
use App\Services\Database\Thesaurus\TimezoneService;
use App\ValueObjects\ResponseObjects\OpenWeatherObject;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;
use Tests\Support\Data\OpenWeather\OpenWeatherResponse;

class WeatherServiceTest extends TestCase
{
    use OpenWeatherResponse;
    
    private WeatherRepositoryInterface $weatherRepository;
    private UserRepositoryInterface $userRepository;
    private CityRepositoryInterface $cityRepository;
    private TimezoneRepositoryInterface $timezoneRepository;
    private WeatherService $weatherService;
    private TimezoneService $timezoneService;
    
    public function test_success_update_or_create(): void
    {
        $cityId = 11;
        $weatherDto = new WeatherDto($cityId, OpenWeatherObject::create((object) $this->getWeatherForOneCity()));
        $weather = new Weather();
        
        $this->weatherRepository->expects($this->once())
                ->method('save')
                ->with($weather, $weatherDto);
        
        $this->assertEquals($weather, $this->weatherService->updateOrCreate($weatherDto));
    }
    
    public function test_success_get_weather_in_cities_for_auth_user_by_user_id(): void
    {
        $userId = 7;
        $user = new User();
        $user->id = $userId;
        $cities = new Collection();
        
        $this->userRepository->expects($this->once())
                ->method('getById')
                ->with($userId)
                ->willReturn($user);
        
        $this->cityRepository->expects($this->once())
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
        
        $this->cityRepository->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($cityId))
                ->willReturn($city);
        
        $this->assertSame($city->weather, $this->weatherService->getLatestWeatherForOneCityByCityId($cityId));
    }
    
    protected function setUp(): void
    {
        $this->weatherRepository = $this->createMock(WeatherRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->cityRepository = $this->createMock(CityRepositoryInterface::class);
        
        $this->timezoneRepository = $this->createMock(TimezoneRepositoryInterface::class);
        $this->timezoneService = new TimezoneService($this->timezoneRepository);
        
        $this->weatherService = new WeatherService($this->weatherRepository, $this->userRepository, $this->cityRepository, $this->timezoneService);
    }
}
