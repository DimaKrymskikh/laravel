<?php

namespace Tests\Unit\CommandHandlers\Database\Logs\WeatherStatisticsByCity;

use App\Contracts\Database\DBqueriesInterface;
use App\Contracts\Services\Database\Thesaurus\InfoAboutCitiesInterface;
use App\Exceptions\DatabaseException;
use App\CommandHandlers\Database\Logs\WeatherStatisticsByCity\SelectForAllCities;
use App\CommandHandlers\Database\Logs\WeatherStatisticsByCity\SelectForOneCity;
use App\CommandHandlers\Database\Logs\WeatherStatisticsByCity\WeatherStatisticsByCityCommandHandler;
use App\Console\Commands\Logs\WeatherStatisticsByCity;
use App\Models\Thesaurus\City;
use PHPUnit\Framework\TestCase;

class WeatherStatisticsByCityCommandHandlerTest extends TestCase
{
    private InfoAboutCitiesInterface $citiesService;
    private DBqueriesInterface $queries;
    private WeatherStatisticsByCityCommandHandler $handler;
    private WeatherStatisticsByCity $command;

    public function test_success_handle_all_cities(): void
    {
        $this->command->method('argument')
                ->willReturn(null);
        
        $this->queries->method('getObject')
                ->with(new SelectForAllCities())
                ->willReturn((object) [
                    // Суммарно 3 даты
                    'min_date' => '2_15:34:55 08.12.2023|2_14:12:06 09.12.2023',
                    'max_date' => '7_16:07:19 25.06.2024'
                ]);
        
        $this->citiesService->expects($this->exactly(3))
                ->method('getCityById')
                ->willReturn(new City());
        
        $this->assertNull($this->handler->handle($this->command));
    }
    
    public function test_success_handle_one_city(): void
    {
        $this->command->method('argument')
                ->willReturn('33');
        
        $this->citiesService->method('getCityByOpenWeatherId')
                ->willReturn(new City());
        
        $this->queries->expects($this->once())
                ->method('getObject')
                ->with(new SelectForOneCity())
                ->willReturn((object) [
                    'id' => '7',
                    // Суммарно 3 даты
                    'min_temp_date' => '15:34:55 08.12.2023|14:12:06 09.12.2023',
                    'max_temp_date' => '17:47:32 28.06.2024'
                ]);
        
        $this->citiesService->expects($this->once())
                ->method('getCityById')
                ->willReturn(new City());
        
        $this->assertNull($this->handler->handle($this->command));
    }
    
    public function test_fail_handle_one_city_argument_not_int(): void
    {
        $this->command->method('argument')
                ->willReturn('test');
        
        $this->queries->expects($this->never())
                ->method('getObject');
        
        $this->assertNull($this->handler->handle($this->command));
    }
    
    public function test_fail_handle_one_city_argument_not_city_in_table(): void
    {
        $this->command->method('argument')
                ->willReturn('33');
        
        $this->citiesService->method('getCityByOpenWeatherId')
                ->willThrowException(new DatabaseException(''));
        
        $this->queries->expects($this->never())
                ->method('getObject');
        
        $this->assertNull($this->handler->handle($this->command));
    }
    
    protected function setUp(): void
    {
        $this->command = $this->createStub(WeatherStatisticsByCity::class);
        
        $this->citiesService = $this->createMock(InfoAboutCitiesInterface::class);
        $this->queries = $this->createMock(DBqueriesInterface::class);
        
        $this->handler = new WeatherStatisticsByCityCommandHandler($this->queries, $this->citiesService);
    }
}
