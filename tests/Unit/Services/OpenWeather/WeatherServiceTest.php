<?php

namespace Tests\Unit\Services\OpenWeather;

use App\Contracts\Services\Database\Thesaurus\InfoAboutCitiesInterface;
use App\Contracts\Services\Database\Thesaurus\TimezoneServiceInterface;
use App\Contracts\Services\ServiceManagerInterface;
use App\Curl\OpenWeather\OpenWeatherRequests;
use App\Exceptions\OpenWeatherException;
use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use App\Models\User;
use App\Modifiers\OpenWeather\WeatherModifiers;
use App\Queries\Logs\OpenWeatherWeatherQueries;
use App\Queries\OpenWeather\OpenWeatherQueries;
use App\Services\OpenWeather\WeatherService;
use App\Support\Collections\Thesaurus\CityCollection;
use App\ValueObjects\ResponseObjects\OpenWeatherObject;
use Database\Testsupport\OpenWeather\OpenWeatherResponse;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Client\Response;

class WeatherServiceTest extends WeatherTestCase
{
    use OpenWeatherResponse;
    
    private ServiceManagerInterface $serviceManager;
    private OpenWeatherRequests $openWeatherRequests;
    private WeatherModifiers $weatherModifiers;
    private OpenWeatherWeatherQueries $openWeatherWeatherQueries;
    private OpenWeatherQueries $openWeatherQueries;
    private TimezoneServiceInterface $timezoneService;
    private InfoAboutCitiesInterface $cityService;
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
    
    public function test_success_getWeatherInCitiesForAuthUser(): void
    {
        $this->cityService->method('getCitiesWithWeatherByUser')
                ->willReturn(new CityCollection());
        
        $this->assertInstanceOf(CityCollection::class, $this->weatherService->getWeatherInCitiesForAuthUser(new User()));
    }
    
    public function test_fail_getWeatherInCitiesForAuthUser(): void
    {
        $this->expectException(\LogicException::class);
        
        $this->weatherService->getWeatherInCitiesForAuthUser(null);
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
        
        $this->cityService->method('getCityById')
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
        
        $this->cityService->method('getCityById')
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
        
        $this->openWeatherRequests = $this->createMock(OpenWeatherRequests::class);
        $this->weatherModifiers = $this->createMock(WeatherModifiers::class);
        $this->openWeatherWeatherQueries = $this->createMock(OpenWeatherWeatherQueries::class);
        $this->openWeatherQueries = $this->createMock(OpenWeatherQueries::class);
        
        $this->serviceManager = $this->createStub(ServiceManagerInterface::class);
        $this->serviceManager->method('getCurlRequest')
                ->willReturn($this->openWeatherRequests);
        $this->serviceManager->method('getQueriesOrModifiers')
                ->willReturn($this->weatherModifiers, $this->openWeatherWeatherQueries, $this->openWeatherQueries);
        
        $this->cityService = $this->createStub(InfoAboutCitiesInterface::class);
        $this->timezoneService = $this->createStub(TimezoneServiceInterface::class);
        
        $this->weatherService = new WeatherService($this->serviceManager, $this->cityService, $this->timezoneService);
    }
}
