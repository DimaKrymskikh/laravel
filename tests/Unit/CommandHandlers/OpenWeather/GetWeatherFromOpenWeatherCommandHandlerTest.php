<?php

namespace Tests\Unit\CommandHandlers\OpenWeather;

use App\Curl\OpenWeather\OpenWeatherRequests;
use App\Exceptions\DatabaseException;
use App\Exceptions\OpenWeatherException;
use App\CommandHandlers\OpenWeather\GetWeatherFromOpenWeatherCommandHandler;
use App\Console\Commands\OpenWeather\GetWeather;
use App\Modifiers\OpenWeather\WeatherModifiers;
use App\Modifiers\Thesaurus\CityModifiers;
use App\Queries\Logs\OpenWeatherWeatherQueries;
use App\Queries\OpenWeather\OpenWeatherQueries;
use App\Queries\Person\UserQueries;
use App\Queries\Thesaurus\CityQueries;
use App\Queries\Thesaurus\TimezoneQueries;
use App\Services\OpenWeather\WeatherService;
use App\Services\Database\Thesaurus\CityService;
use App\Services\Database\Thesaurus\TimezoneService;
use App\Services\ServiceManagerInterface;
use Illuminate\Http\Client\Response;

class GetWeatherFromOpenWeatherCommandHandlerTest extends OpenWeatherTestCase
{
    private GetWeatherFromOpenWeatherCommandHandler $handler;
    private GetWeather $command;
    private WeatherService $weatherService;
    private ServiceManagerInterface $serviceManager;
    private TimezoneService $timezoneService;
    private CityService $cityService;
    private Response $response;
    private OpenWeatherException $openWeatherException;
    private CityModifiers $cityModifiers;
    private CityQueries $cityQueries;
    private TimezoneQueries $timezoneQueries;
    private OpenWeatherRequests $openWeatherRequests;
    private WeatherModifiers $weatherModifiers;
    private OpenWeatherWeatherQueries $openWeatherWeatherQueries;
    private OpenWeatherQueries $openWeatherQueries;
    private UserQueries $userQueries;

    public function test_success_handle_one_city(): void
    {
        $this->defineSuccessStart($this->cityQueries, $this->command);
        
        $this->defineSuccessResponse($this->response);
        
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
        
        $this->cityQueries->method('getByOpenWeatherId')
                ->willThrowException(new DatabaseException(''));
        
        $this->assertNull($this->handler->handle($this->command));
    }

    public function test_fail_handle_request_limit_exceeded(): void
    {
        $this->defineSuccessStart($this->cityQueries, $this->command);
        
        $this->openWeatherWeatherQueries->method('getNumberOfWeatherLinesForLastMinute')
                ->willThrowException($this->openWeatherException);
        
        $this->assertNull($this->handler->handle($this->command));
    }

    public function test_fail_handle_little_time_has_passed(): void
    {
        $this->defineSuccessStart($this->cityQueries, $this->command);
        
        $this->openWeatherWeatherQueries->method('isTooEarlyToSubmitRequestForThisCity')
                ->willThrowException($this->openWeatherException);
        
        $this->assertNull($this->handler->handle($this->command));
    }

    public function test_fail_handle_fail_response(): void
    {
        $this->defineFailResponse($this->response);
        
        $this->assertNull($this->handler->handle($this->command));
    }

    public function test_success_handle_many_cities(): void
    {
        $nCity = 5;
        $cities = $this->factoryCities($nCity);
        
        $this->defineSuccessResponse($this->response);
        
        $this->defineNullArgument($this->command);
        
        $this->cityQueries->method('getList')
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
        
        $this->cityModifiers = $this->createStub(CityModifiers::class);
        $this->weatherModifiers = $this->createStub(WeatherModifiers::class);
        
        $this->openWeatherWeatherQueries = $this->createStub(OpenWeatherWeatherQueries::class);
        $this->openWeatherQueries = $this->createStub(OpenWeatherQueries::class);
        $this->userQueries = $this->createStub(UserQueries::class);
        $this->cityQueries = $this->createStub(CityQueries::class);
        $this->timezoneQueries = $this->createStub(TimezoneQueries::class);
        
        $this->serviceManager = $this->createStub(ServiceManagerInterface::class);
        $this->serviceManager->method('getCurlRequest')
                ->willReturn($this->openWeatherRequests);
        $this->serviceManager->method('getQueriesOrModifiers')
                ->willReturn(
                        $this->timezoneQueries,
                        $this->weatherModifiers,
                        $this->openWeatherWeatherQueries, $this->openWeatherQueries, $this->userQueries, $this->cityQueries,
                        $this->cityModifiers, $this->cityQueries
                    );
        
        $this->timezoneService = new TimezoneService($this->serviceManager);
        $this->weatherService = new WeatherService($this->serviceManager, $this->timezoneService);
        
        $this->cityService = new CityService($this->serviceManager);
        
        $this->handler = new GetWeatherFromOpenWeatherCommandHandler($this->weatherService, $this->cityService);
    }
}
