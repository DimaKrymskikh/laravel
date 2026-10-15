<?php

namespace Tests\Unit\CommandHandlers\OpenWeather;

use App\Contracts\Services\Database\Thesaurus\InfoAboutCitiesInterface;
use App\Contracts\Services\OpenWeather\OpenWeatherInterface;
use App\Curl\OpenWeather\OpenWeatherRequests;
use App\Exceptions\DatabaseException;
use App\Exceptions\OpenWeatherException;
use App\CommandHandlers\OpenWeather\GetWeatherFromOpenWeatherCommandHandler;
use App\Console\Commands\OpenWeather\GetWeather;
use Illuminate\Http\Client\Response;

class GetWeatherFromOpenWeatherCommandHandlerTest extends OpenWeatherTestCase
{
    private GetWeatherFromOpenWeatherCommandHandler $handler;
    private GetWeather $command;
    private OpenWeatherInterface $weatherService;
    private InfoAboutCitiesInterface $cityService;
    private Response $response;
    private OpenWeatherException $openWeatherException;
    private OpenWeatherRequests $openWeatherRequests;

    public function test_success_handle_one_city(): void
    {
        $this->defineSuccessStart($this->cityService, $this->command);
        
        $this->defineSuccessResponse($this->response, $this->weatherService);
        
        $this->assertNull($this->handler->handle($this->command));
    }

    public function test_fail_handle_not_int_argument(): void
    {
        $this->defineStringArgument($this->command);
        
        $this->assertNull($this->handler->handle($this->command));
    }

    public function test_fail_handle_not_open_weather_id(): void
    {
        $this->defineIntArgument($this->factoryCity(), $this->command);
        
        $this->cityService->method('getCityByOpenWeatherId')
                ->willThrowException(new DatabaseException(''));
        
        $this->assertNull($this->handler->handle($this->command));
    }

    public function test_fail_handle_bad_request(): void
    {
        $this->defineSuccessStart($this->cityService, $this->command);
        
        $this->weatherService->method('sendRequest')
                ->willThrowException($this->openWeatherException);
        
        $this->assertNull($this->handler->handle($this->command));
    }

    public function test_fail_handle_fail_response(): void
    {
        $this->defineSuccessStart($this->cityService, $this->command);
        
        $this->defineFailResponse($this->response, $this->weatherService);
        
        $this->assertNull($this->handler->handle($this->command));
    }

    public function test_success_handle_many_cities(): void
    {
        $nCity = 5;
        $cities = $this->factoryCities($nCity);
        
        $this->defineSuccessResponse($this->response, $this->weatherService);
        
        $this->defineNullArgument($this->command);
        
        $this->cityService->method('getAllCitiesList')
                ->willReturn($cities);
        
        $this->openWeatherRequests->method('getWeatherByCity')
                ->willReturn($this->response);
        
        $this->assertNull($this->handler->handle($this->command));
    }
    
    protected function setUp(): void
    {
        $this->openWeatherException = $this->createStub(OpenWeatherException::class);
        $this->response = $this->createStub(Response::class);
        $this->command = $this->createStub(GetWeather::class);
        
        $this->openWeatherRequests = $this->createStub(OpenWeatherRequests::class);
        
        $this->cityService = $this->createStub(InfoAboutCitiesInterface::class);
        $this->weatherService = $this->createStub(OpenWeatherInterface::class);
        
        $this->handler = new GetWeatherFromOpenWeatherCommandHandler($this->weatherService, $this->cityService);
    }
}
