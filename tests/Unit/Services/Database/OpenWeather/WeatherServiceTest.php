<?php

namespace Tests\Unit\Services\Database\OpenWeather;

use App\Exceptions\OpenWeatherException;
use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use App\Models\User;
use App\Support\Facades\Services\OpenWeather\OpenWeatherFacadeInterface;
use App\Services\OpenWeather\WeatherService;
use App\ValueObjects\ResponseObjects\OpenWeatherObject;
use Database\Testsupport\OpenWeather\OpenWeatherResponse;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\Response;

class WeatherServiceTest extends WeatherTestCase
{
    use OpenWeatherResponse;
    
    private OpenWeatherFacadeInterface $facade;
    private Dispatcher $dispatcher;
    private WeatherService $weatherService;
    private Response $response;
    private City $city;

    public function test_success_updateOrCreate(): void
    {
        $weatherDto = new WeatherDto($this->cityId, OpenWeatherObject::create((object) $this->getWeatherForOneCity()));
        
        $this->facade->expects($this->once())
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
        
        $this->facade->expects($this->once())
                ->method('getUserById')
                ->with($userId)
                ->willReturn($user);
        
        $this->facade->expects($this->once())
                ->method('getWeatherInCitiesForUser')
                ->with($this->identicalTo($user))
                ->willReturn($cities);
        
        $this->assertSame($cities, $this->weatherService->getWeatherInCitiesForAuthUserByUserId($userId));
    }
    
    public function test_success_checkNumberOfWeatherLinesForLastMinuteLessBaseValue(): void
    {
        $this->facade->expects($this->once())
                ->method('getNumberOfWeatherLinesForLastMinute')
                ->willReturn(WeatherService::OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE - 1);
        
        $this->assertNull($this->weatherService->checkNumberOfWeatherLinesForLastMinuteLessBaseValue());
    }
    
    public function test_fail_checkNumberOfWeatherLinesForLastMinuteLessBaseValue(): void
    {
        $this->expectException(OpenWeatherException::class);
        
        $this->facade->expects($this->once())
                ->method('getNumberOfWeatherLinesForLastMinute')
                ->willReturn(WeatherService::OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE);
        
        $this->weatherService->checkNumberOfWeatherLinesForLastMinuteLessBaseValue();
    }
    
    public function test_success_checkTooEarlyToSubmitRequestForThisCity(): void
    {
        $this->facade->expects($this->once())
                ->method('isTooEarlyToSubmitRequestForThisCity')
                ->with($this->cityId)
                ->willReturn(false);
        
        $this->assertNull($this->weatherService->checkTooEarlyToSubmitRequestForThisCity($this->cityId));
    }
    
    public function test_fail_checkTooEarlyToSubmitRequestForThisCity(): void
    {
        $this->expectException(OpenWeatherException::class);
        
        $this->facade->expects($this->once())
                ->method('isTooEarlyToSubmitRequestForThisCity')
                ->with($this->cityId)
                ->willReturn(true);
        
        $this->weatherService->checkTooEarlyToSubmitRequestForThisCity($this->cityId);
    }
    
    public function test_success_sendRequest(): void
    {
        $this->city = $this->factoryCity();
        
        $this->facade->expects($this->once())
                ->method('getNumberOfWeatherLinesForLastMinute')
                ->willReturn(WeatherService::OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE - 1);
        
        $this->facade->expects($this->once())
                ->method('isTooEarlyToSubmitRequestForThisCity')
                ->willReturn(false);
        
        $this->facade->expects($this->once())
                ->method('getWeatherFromOpenWeatherByCity')
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
        $this->city = $this->factoryCity();
        $this->defineSuccessResponse($this->response);
        
        $this->facade->expects($this->once())
                ->method('getCityById')
                ->with($this->cityId)
                ->willReturn($this->city);
        
        $this->facade->expects($this->once())
                ->method('getWeatherFromOpenWeatherByCity')
                ->with($this->city)
                ->willReturn($this->response);
        
        $this->facade->expects($this->once())
                ->method('updateOrCreate');
        
        $this->facade->expects($this->once())
                ->method('getWeatherByCityId')
                ->willReturn(Weather::factory()->make());
        
        $this->dispatcher->expects($this->once())
                ->method('dispatch');
        
        $this->assertNull($this->weatherService->refreshWeatherInCity( $this->getUserCityDto() ));
    }
    
    public function test_fail_refreshWeatherInCity(): void
    {
        $this->city = $this->factoryCity();
        $this->response->method('status')->willReturn(400); // !200
        
        $this->facade->expects($this->once())
                ->method('getCityById')
                ->with($this->cityId)
                ->willReturn($this->city);
        
        $this->facade->expects($this->once())
                ->method('getWeatherFromOpenWeatherByCity')
                ->with($this->city)
                ->willReturn($this->response);
        
        $this->facade->expects($this->never())
                ->method('updateOrCreate');
        
        $this->dispatcher->expects($this->never())
                ->method('dispatch');
        
        $this->assertNull($this->weatherService->refreshWeatherInCity( $this->getUserCityDto() ));
    }
    
    protected function setUp(): void
    {
        $this->response = $this->createStub(Response::class);
        $this->facade = $this->createMock(OpenWeatherFacadeInterface::class);
        $this->dispatcher = $this->createMock(Dispatcher::class);
        
        $this->weatherService = new WeatherService($this->facade, $this->dispatcher);
    }
}
