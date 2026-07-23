<?php

namespace Tests\Unit\Services\OpenWeather;

use App\Curl\OpenWeather\OpenWeatherRequests;
use App\Exceptions\OpenWeatherException;
use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use App\Models\User;
use App\Modifiers\OpenWeather\WeatherModifiers;
use App\Queries\Logs\OpenWeatherWeatherQueries;
use App\Queries\OpenWeather\OpenWeatherQueries;
use App\Queries\Person\UserQueries;
use App\Queries\Thesaurus\CityQueries;
use App\Queries\Thesaurus\TimezoneQueries;
use App\Services\Database\Thesaurus\TimezoneService;
use App\Services\OpenWeather\WeatherService;
use App\Services\ServiceManagerInterface;
use App\ValueObjects\ResponseObjects\OpenWeatherObject;
use Database\Testsupport\OpenWeather\OpenWeatherResponse;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\Response;

class WeatherServiceTest extends WeatherTestCase
{
    use OpenWeatherResponse;
    
    private ServiceManagerInterface $serviceManager;
    private OpenWeatherRequests $openWeatherRequests;
    private WeatherModifiers $weatherModifiers;
    private OpenWeatherWeatherQueries $openWeatherWeatherQueries;
    private OpenWeatherQueries $openWeatherQueries;
    private UserQueries $userQueries;
    private CityQueries $cityQueries;
    private TimezoneService $timezoneService;
    private TimezoneQueries $timezoneQueries;
    private WeatherService $weatherService;
    private Response $response;
    private City $city;

    public function test_success_updateOrCreate(): void
    {
        $weatherDto = new WeatherDto($this->cityId, OpenWeatherObject::create((object) $this->getWeatherForOneCity()));
        
        $this->weatherModifiers->expects($this->once())
                ->method('updateOrCreate')
                ->with($weatherDto);
        
        $this->assertInstanceOf(Weather::class, $this->weatherService->updateOrCreate($weatherDto));
    }
    
    public function test_success_getWeatherInCitiesForAuthUserByUserId(): void
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
    
    public function test_success_checkNumberOfWeatherLinesForLastMinuteLessBaseValue(): void
    {
        $this->openWeatherWeatherQueries->expects($this->once())
                ->method('getNumberOfWeatherLinesForLastMinute')
                ->willReturn(WeatherService::OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE - 1);
        
        $this->assertNull($this->weatherService->checkNumberOfWeatherLinesForLastMinuteLessBaseValue());
    }
    
    public function test_fail_checkNumberOfWeatherLinesForLastMinuteLessBaseValue(): void
    {
        $this->expectException(OpenWeatherException::class);
        
        $this->openWeatherWeatherQueries->expects($this->once())
                ->method('getNumberOfWeatherLinesForLastMinute')
                ->willReturn(WeatherService::OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE);
        
        $this->weatherService->checkNumberOfWeatherLinesForLastMinuteLessBaseValue();
    }
    
    public function test_success_checkTooEarlyToSubmitRequestForThisCity(): void
    {
        $this->openWeatherWeatherQueries->expects($this->once())
                ->method('isTooEarlyToSubmitRequestForThisCity')
                ->with($this->cityId)
                ->willReturn(false);
        
        $this->assertNull($this->weatherService->checkTooEarlyToSubmitRequestForThisCity($this->cityId));
    }
    
    public function test_fail_checkTooEarlyToSubmitRequestForThisCity(): void
    {
        $this->expectException(OpenWeatherException::class);
        
        $this->openWeatherWeatherQueries->expects($this->once())
                ->method('isTooEarlyToSubmitRequestForThisCity')
                ->with($this->cityId)
                ->willReturn(true);
        
        $this->weatherService->checkTooEarlyToSubmitRequestForThisCity($this->cityId);
    }
    
    public function test_success_sendRequest(): void
    {
        $this->city = $this->factoryCity();
        
        $this->openWeatherWeatherQueries->expects($this->once())
                ->method('getNumberOfWeatherLinesForLastMinute')
                ->willReturn(WeatherService::OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE - 1);
        
        $this->openWeatherWeatherQueries->expects($this->once())
                ->method('isTooEarlyToSubmitRequestForThisCity')
                ->willReturn(false);
        
        $this->openWeatherRequests->expects($this->once())
                ->method('getWeatherByCity')
                ->with($this->identicalTo($this->city))
                ->willReturn($this->response);
        
        $this->assertInstanceOf(Response::class, $this->weatherService->sendRequest($this->city));
    }
    
    public function test_success_saveResponse(): void
    {
        $this->city = $this->factoryCity();
        $this->defineSuccessResponse($this->response);
        
        $this->assertInstanceOf(Weather::class, $this->weatherService->saveResponse($this->response, $this->city));
    }
    
    public function test_success_refreshWeatherInCity(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        
        $this->city = $this->factoryCity();
        $this->defineSuccessResponse($this->response);
        
        $this->cityQueries->expects($this->once())
                ->method('getById')
                ->with($this->cityId)
                ->willReturn($this->city);
        
        $this->openWeatherRequests->expects($this->once())
                ->method('getWeatherByCity')
                ->with($this->city)
                ->willReturn($this->response);
        
        $this->weatherModifiers->expects($this->once())
                ->method('updateOrCreate');
        
        $this->openWeatherQueries->expects($this->once())
                ->method('getByCityId')
                ->willReturn(Weather::factory()->make());
        
        $dispatcher->expects($this->once())
                ->method('dispatch');
        
        $this->assertNull($this->weatherService->refreshWeatherInCity( $this->getUserCityDto(), $dispatcher ));
    }
    
    public function test_fail_refreshWeatherInCity(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        
        $this->city = $this->factoryCity();
        $this->response->method('status')->willReturn(400); // !200
        
        $this->cityQueries->expects($this->once())
                ->method('getById')
                ->with($this->cityId)
                ->willReturn($this->city);
        
        $this->openWeatherRequests->expects($this->once())
                ->method('getWeatherByCity')
                ->with($this->city)
                ->willReturn($this->response);
        
        $this->weatherModifiers->expects($this->never())
                ->method('updateOrCreate');
        
        $dispatcher->expects($this->never())
                ->method('dispatch');
        
        $this->assertNull($this->weatherService->refreshWeatherInCity( $this->getUserCityDto(), $dispatcher ));
    }
    
    protected function setUp(): void
    {
        $this->response = $this->createStub(Response::class);
        
        $this->timezoneQueries = $this->createStub(TimezoneQueries::class);
        
        $this->openWeatherRequests = $this->createMock(OpenWeatherRequests::class);
        $this->weatherModifiers = $this->createMock(WeatherModifiers::class);
        $this->openWeatherWeatherQueries = $this->createMock(OpenWeatherWeatherQueries::class);
        $this->openWeatherQueries = $this->createMock(OpenWeatherQueries::class);
        $this->userQueries = $this->createMock(UserQueries::class);
        $this->cityQueries = $this->createMock(CityQueries::class);
        
        $this->serviceManager = $this->createStub(ServiceManagerInterface::class);
        $this->serviceManager->method('getCurlRequest')
                ->willReturn($this->openWeatherRequests);
        $this->serviceManager->method('getQueriesOrModifiers')
                ->willReturn(
                        $this->timezoneQueries,
                        $this->weatherModifiers,
                        $this->openWeatherWeatherQueries, $this->openWeatherQueries, $this->userQueries, $this->cityQueries,
                    );
        
        $this->timezoneService = new TimezoneService($this->serviceManager);
        $this->weatherService = new WeatherService($this->serviceManager, $this->timezoneService);
    }
}
