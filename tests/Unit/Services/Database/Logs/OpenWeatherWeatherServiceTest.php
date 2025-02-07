<?php

namespace Tests\Unit\Services\Database\Logs;

use App\Exceptions\OpenWeatherException;
use App\Models\Thesaurus\City;
use App\Repositories\Logs\OpenWeatherWeatherRepositoryInterface;
use App\Repositories\Thesaurus\TimezoneRepositoryInterface;
use App\Services\Database\Logs\OpenWeatherWeatherService;
use App\Services\Database\Thesaurus\TimezoneService;
use Illuminate\Pagination\LengthAwarePaginator;
use PHPUnit\Framework\TestCase;
use Tests\Support\Data\Dto\Database\OpenWeather\Filters\WeatherFilterDtoCase;
use Tests\Support\Data\Dto\Pagination\PaginatorDtoCase;

class OpenWeatherWeatherServiceTest extends TestCase
{
    use WeatherFilterDtoCase, PaginatorDtoCase;
    
    private OpenWeatherWeatherRepositoryInterface $openWeatherWeatherRepository;
    private TimezoneRepositoryInterface $timezoneRepository;
    private OpenWeatherWeatherService $openWeatherWeatherService;
    private TimezoneService $timezoneService;
    private int $cityId = 18;

    public function test_check_number_of_weather_lines_for_last_minute_less_base_value(): void
    {
        $this->openWeatherWeatherRepository->expects($this->once())
                ->method('getNumberOfWeatherLinesForLastMinute')
                ->willReturn(OpenWeatherWeatherService::OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE - 1);
        
        $this->assertNull($this->openWeatherWeatherService->checkNumberOfWeatherLinesForLastMinuteLessBaseValue());
    }
    
    public function test_check_number_of_weather_lines_for_last_minute_less_base_value_with_exception(): void
    {
        $this->openWeatherWeatherRepository->expects($this->once())
                ->method('getNumberOfWeatherLinesForLastMinute')
                ->willReturn(OpenWeatherWeatherService::OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE);
        
        $this->expectException(OpenWeatherException::class);
        
        $this->openWeatherWeatherService->checkNumberOfWeatherLinesForLastMinuteLessBaseValue();
    }

    public function test_check_too_early_to_submit_request_for_this_city(): void
    {
        $this->openWeatherWeatherRepository->expects($this->once())
                ->method('isTooEarlyToSubmitRequestForThisCity')
                ->with($this->identicalTo($this->cityId))
                ->willReturn(false);
        
        $this->assertNull($this->openWeatherWeatherService->checkTooEarlyToSubmitRequestForThisCity($this->cityId));
    }

    public function test_check_too_early_to_submit_request_for_this_city_with_exception(): void
    {
        $this->openWeatherWeatherRepository->expects($this->once())
                ->method('isTooEarlyToSubmitRequestForThisCity')
                ->with($this->identicalTo($this->cityId))
                ->willReturn(true);
        
        $this->expectException(OpenWeatherException::class);
        
        $this->openWeatherWeatherService->checkTooEarlyToSubmitRequestForThisCity($this->cityId);
    }

    public function test_getWeatherListForPageByCity(): void
    {
        $city = new City();
        $city->id = $this->cityId;
        
        $weatherFilterDto = $this->getBaseCaseWeatherFilterDto();
        $paginatorDto = $this->getBaseCasePaginatorDto();
        
        $this->openWeatherWeatherRepository->expects($this->once())
                ->method('getByCityIdForPage')
                ->with($paginatorDto, $weatherFilterDto, $this->identicalTo($this->cityId));
        
        $this->assertInstanceOf(LengthAwarePaginator::class, $this->openWeatherWeatherService->getWeatherListForPageByCity($paginatorDto, $weatherFilterDto, $city));
    }
    
    protected function setUp(): void
    {
        $this->openWeatherWeatherRepository = $this->createMock(OpenWeatherWeatherRepositoryInterface::class);
        $this->timezoneRepository = $this->createMock(TimezoneRepositoryInterface::class);
        
        $this->timezoneService = new TimezoneService($this->timezoneRepository);
        
        $this->openWeatherWeatherService = new OpenWeatherWeatherService($this->openWeatherWeatherRepository, $this->timezoneService);
    }
}
