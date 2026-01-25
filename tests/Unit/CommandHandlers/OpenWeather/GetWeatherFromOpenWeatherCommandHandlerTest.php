<?php

namespace Tests\Unit\CommandHandlers\OpenWeather;

use App\Exceptions\DatabaseException;
use App\Exceptions\OpenWeatherException;
use App\CommandHandlers\OpenWeather\GetWeatherFromOpenWeatherCommandHandler;
use App\Console\Commands\OpenWeather\GetWeather;
use App\Modifiers\Thesaurus\Cities\CityModifiersInterface;
use App\Queries\Thesaurus\Cities\CityQueriesInterface;
use App\Support\Facades\Services\OpenWeather\OpenWeatherFacadeInterface;
use App\Services\OpenWeather\WeatherService;
use App\Services\Database\Thesaurus\CityService;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Client\Response;

class GetWeatherFromOpenWeatherCommandHandlerTest extends OpenWeatherTestCase
{
    private GetWeatherFromOpenWeatherCommandHandler $handler;
    private GetWeather $command;
    private WeatherService $weatherService;
    private CityService $cityService;
    private Response $response;
    private OpenWeatherException $openWeatherException;
    private OpenWeatherFacadeInterface $openWeatherFacade;
    private CityModifiersInterface $cityModifiers;
    private CityQueriesInterface $cityQueries;
    private Dispatcher $dispatcher;

    public function test_success_handle_one_city(): void
    {
        $city = $this->defineSuccessStart($this->cityQueries, $this->command);
        
        $this->defineSuccessResponse($this->response);
        
        $this->cityQueries->expects($this->never())
                ->method('getList');
        
        $this->openWeatherFacade->expects($this->once())
                ->method('getWeatherFromOpenWeatherByCity')
                ->with($this->identicalTo($city))
                ->willReturn($this->response);
        
        $this->openWeatherFacade->expects($this->once())
                ->method('updateOrCreate');
        
        $this->assertNull($this->handler->handle($this->command));
    }

    public function test_fail_handle_not_int_argument(): void
    {
        $this->defineStringArgument($this->command);
        
        $this->cityQueries->expects($this->never())
                ->method('getByOpenWeatherId');
        
        $this->defineNeverFacade($this->openWeatherFacade, $this->cityQueries);
        
        $this->handler->handle($this->command);
    }

    public function test_fail_handle_not_open_weather_id(): void
    {
        $this->defineIntArgument($this->factoryCity(), $this->command);
        
        $this->cityQueries->expects($this->once())
                ->method('getByOpenWeatherId')
                ->willThrowException(new DatabaseException(''));
        
        $this->defineNeverFacade($this->openWeatherFacade, $this->cityQueries);
        
        $this->handler->handle($this->command);
    }

    public function test_fail_handle_request_limit_exceeded(): void
    {
        $this->defineSuccessStart($this->cityQueries, $this->command);
        
        $this->openWeatherFacade->expects($this->once())
                ->method('getNumberOfWeatherLinesForLastMinute')
                ->willThrowException($this->openWeatherException);
        
        $this->openWeatherException->expects($this->once())
                ->method('report');
        
        $this->defineNeverRequest($this->openWeatherFacade);
        
        $this->handler->handle($this->command);
    }

    public function test_fail_handle_little_time_has_passed(): void
    {
        $this->defineSuccessStart($this->cityQueries, $this->command);
        
        $this->openWeatherFacade->expects($this->once())
                ->method('isTooEarlyToSubmitRequestForThisCity')
                ->willThrowException($this->openWeatherException);
        
        $this->openWeatherException->expects($this->once())
                ->method('report');
        
        $this->defineNeverRequest($this->openWeatherFacade);
        
        $this->handler->handle($this->command);
    }

    public function test_fail_handle_fail_response(): void
    {
        $city = $this->defineSuccessStart($this->cityQueries, $this->command);
        
        $this->defineFailResponse($this->response);
        
        $this->openWeatherFacade->expects($this->once())
                ->method('getWeatherFromOpenWeatherByCity')
                ->with($this->identicalTo($city))
                ->willReturn($this->response);
        
        $this->openWeatherFacade->expects($this->never())
                ->method('updateOrCreate');
        
        $this->handler->handle($this->command);
    }

    public function test_success_handle_many_cities(): void
    {
        $nCity = 5;
        $cities = $this->factoryCities($nCity);
        
        $this->defineSuccessResponse($this->response);
        
        $this->defineNullArgument($this->command);
        
        $this->cityQueries->expects($this->never())
                ->method('getByOpenWeatherId');
        
        $this->cityQueries->expects($this->once())
                ->method('getList')
                ->willReturn($cities);
        
        $this->openWeatherFacade->expects($this->exactly($nCity))
                ->method('getWeatherFromOpenWeatherByCity')
                ->willReturn($this->response);
        
        $this->openWeatherFacade->expects($this->exactly($nCity))
                ->method('updateOrCreate');
        
        $this->assertNull($this->handler->handle($this->command));
    }
    
    protected function setUp(): void
    {
        $this->openWeatherException = $this->createStub(OpenWeatherException::class);
        $this->response = $this->createStub(Response::class);
        $this->command = $this->createStub(GetWeather::class);
        
        $this->openWeatherFacade = $this->createMock(OpenWeatherFacadeInterface::class);
        $this->dispatcher = $this->createMock(Dispatcher::class);
        $this->weatherService = new WeatherService($this->openWeatherFacade, $this->dispatcher);
        
        $this->cityModifiers = $this->createMock(CityModifiersInterface::class);
        $this->cityQueries = $this->createMock(CityQueriesInterface::class);
        $this->cityService = new CityService($this->cityModifiers, $this->cityQueries);
        
        $this->handler = new GetWeatherFromOpenWeatherCommandHandler($this->weatherService, $this->cityService);
    }
}
