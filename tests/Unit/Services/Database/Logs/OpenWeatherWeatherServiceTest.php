<?php

namespace Tests\Unit\Services\Database\Logs;

use App\Exceptions\OpenWeatherException;
use App\Queries\Logs\OpenWeatherWeather\OpenWeatherWeatherQueriesInterface;
use App\Services\Database\Logs\OpenWeatherWeatherService;
use PHPUnit\Framework\TestCase;
use Tests\Support\Data\Dto\Database\OpenWeather\Filters\WeatherFilterDtoCase;
use Tests\Support\Data\Dto\Pagination\PaginatorDtoCase;

class OpenWeatherWeatherServiceTest extends TestCase
{
    use WeatherFilterDtoCase, PaginatorDtoCase;
    
    private OpenWeatherWeatherQueriesInterface $openWeatherWeatherQueries;
    private OpenWeatherWeatherService $openWeatherWeatherService;
    private int $cityId = 18;

    public function test_check_number_of_weather_lines_for_last_minute_less_base_value(): void
    {
        $this->openWeatherWeatherQueries->expects($this->once())
                ->method('getNumberOfWeatherLinesForLastMinute')
                ->willReturn(OpenWeatherWeatherService::OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE - 1);
        
        $this->assertNull($this->openWeatherWeatherService->checkNumberOfWeatherLinesForLastMinuteLessBaseValue());
    }
    
    public function test_check_number_of_weather_lines_for_last_minute_less_base_value_with_exception(): void
    {
        $this->openWeatherWeatherQueries->expects($this->once())
                ->method('getNumberOfWeatherLinesForLastMinute')
                ->willReturn(OpenWeatherWeatherService::OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE);
        
        $this->expectException(OpenWeatherException::class);
        
        $this->openWeatherWeatherService->checkNumberOfWeatherLinesForLastMinuteLessBaseValue();
    }

    public function test_check_too_early_to_submit_request_for_this_city(): void
    {
        $this->openWeatherWeatherQueries->expects($this->once())
                ->method('isTooEarlyToSubmitRequestForThisCity')
                ->with($this->identicalTo($this->cityId))
                ->willReturn(false);
        
        $this->assertNull($this->openWeatherWeatherService->checkTooEarlyToSubmitRequestForThisCity($this->cityId));
    }

    public function test_check_too_early_to_submit_request_for_this_city_with_exception(): void
    {
        $this->openWeatherWeatherQueries->expects($this->once())
                ->method('isTooEarlyToSubmitRequestForThisCity')
                ->with($this->identicalTo($this->cityId))
                ->willReturn(true);
        
        $this->expectException(OpenWeatherException::class);
        
        $this->openWeatherWeatherService->checkTooEarlyToSubmitRequestForThisCity($this->cityId);
    }
    
    protected function setUp(): void
    {
        $this->openWeatherWeatherQueries = $this->createMock(OpenWeatherWeatherQueriesInterface::class);
        
        $this->openWeatherWeatherService = new OpenWeatherWeatherService($this->openWeatherWeatherQueries);
    }
}
