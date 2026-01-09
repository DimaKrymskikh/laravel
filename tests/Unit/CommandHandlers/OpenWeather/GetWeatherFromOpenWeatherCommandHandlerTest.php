<?php

namespace Tests\Unit\CommandHandlers\OpenWeather;

use App\Exceptions\DatabaseException;
use App\Exceptions\OpenWeatherException;
use App\CommandHandlers\OpenWeather\Facades\OpenWeatherFacadeInterface;
use App\CommandHandlers\OpenWeather\GetWeatherFromOpenWeatherCommandHandler;
use App\Console\Commands\OpenWeather\GetWeather;
use Illuminate\Http\Client\Response;

class GetWeatherFromOpenWeatherCommandHandlerTest extends OpenWeatherTestCase
{
    private GetWeatherFromOpenWeatherCommandHandler $handler;
    private GetWeather $command;
    private OpenWeatherFacadeInterface $facade;
    private Response $response;
    private OpenWeatherException $openWeatherException;

    public function test_success_handle_one_city(): void
    {
        $city = $this->defineSuccessStart($this->facade, $this->command);
        
        $this->defineSuccessResponse($this->response);
        
        $this->facade->expects($this->never())
                ->method('getAllCitiesList');
        
        $this->facade->expects($this->once())
                ->method('getWeatherByCity')
                ->with($this->identicalTo($city))
                ->willReturn($this->response);
        
        $this->facade->expects($this->once())
                ->method('updateOrCreate');
        
        $this->assertNull($this->handler->handle($this->command));
    }

    public function test_fail_handle_not_int_argument(): void
    {
        $this->defineStringArgument($this->command);
        
        $this->facade->expects($this->never())
                ->method('findCityByOpenWeatherId');
        
        $this->defineNeverFacade($this->facade);
        
        $this->handler->handle($this->command);
    }

    public function test_fail_handle_not_open_weather_id(): void
    {
        $this->defineIntArgument($this->factoryCity(), $this->command);
        
        $this->facade->expects($this->once())
                ->method('findCityByOpenWeatherId')
                ->willThrowException(new DatabaseException(''));
        
        $this->defineNeverFacade($this->facade);
        
        $this->handler->handle($this->command);
    }

    public function test_fail_handle_request_limit_exceeded(): void
    {
        $this->defineSuccessStart($this->facade, $this->command);
        
        $this->facade->expects($this->once())
                ->method('checkNumberOfWeatherLinesForLastMinuteLessBaseValue')
                ->willThrowException($this->openWeatherException);
        
        $this->openWeatherException->expects($this->once())
                ->method('report');
        
        $this->defineNeverRequest($this->facade);
        
        $this->handler->handle($this->command);
    }

    public function test_fail_handle_little_time_has_passed(): void
    {
        $this->defineSuccessStart($this->facade, $this->command);
        
        $this->facade->expects($this->once())
                ->method('checkTooEarlyToSubmitRequestForThisCity')
                ->willThrowException($this->openWeatherException);
        
        $this->openWeatherException->expects($this->once())
                ->method('report');
        
        $this->defineNeverRequest($this->facade);
        
        $this->handler->handle($this->command);
    }

    public function test_fail_handle_fail_response(): void
    {
        $city = $this->defineSuccessStart($this->facade, $this->command);
        
        $this->defineFailResponse($this->response);
        
        $this->facade->expects($this->once())
                ->method('getWeatherByCity')
                ->with($this->identicalTo($city))
                ->willReturn($this->response);
        
        $this->facade->expects($this->never())
                ->method('updateOrCreate');
        
        $this->handler->handle($this->command);
    }

    public function test_success_handle_many_cities(): void
    {
        $nCity = 5;
        $cities = $this->factoryCities($nCity);
        
        $this->defineSuccessResponse($this->response);
        
        $this->defineNullArgument($this->command);
        
        $this->facade->expects($this->never())
                ->method('findCityByOpenWeatherId');
        
        $this->facade->expects($this->once())
                ->method('getAllCitiesList')
                ->willReturn($cities);
        
        $this->facade->expects($this->exactly($nCity))
                ->method('getWeatherByCity')
                ->willReturn($this->response);
        
        $this->facade->expects($this->exactly($nCity))
                ->method('updateOrCreate');
        
        $this->assertNull($this->handler->handle($this->command));
    }
    
    protected function setUp(): void
    {
        $this->openWeatherException = $this->createStub(OpenWeatherException::class);
        $this->response = $this->createStub(Response::class);
        $this->command = $this->createStub(GetWeather::class);
        $this->facade = $this->createMock(OpenWeatherFacadeInterface::class);
        
        $this->handler = new GetWeatherFromOpenWeatherCommandHandler($this->facade);
    }
}
