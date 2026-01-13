<?php

namespace Tests\Unit\CommandHandlers\Database\Logs\WeatherStatisticsByCity;

use App\CommandHandlers\Database\Logs\WeatherStatisticsByCity\SelectForAllCities;
use App\CommandHandlers\Database\Logs\WeatherStatisticsByCity\SelectForOneCity;
use App\CommandHandlers\Database\Logs\WeatherStatisticsByCity\WeatherStatisticsByCityCommandHandler;
use App\Console\Commands\Logs\WeatherStatisticsByCity;
use App\Exceptions\DatabaseException;
use App\Models\Thesaurus\City;
use App\Queries\Thesaurus\Cities\CityQueriesInterface;
use App\Queries\Thesaurus\Timezones\TimezoneQueriesInterface;
use App\Services\Database\Thesaurus\TimezoneService;
use PHPUnit\Framework\TestCase;

class WeatherStatisticsByCityCommandHandlerTest extends TestCase
{
    private WeatherStatisticsByCityCommandHandler $handler;
    private WeatherStatisticsByCity $command;
    private CityQueriesInterface $queries;
    private TimezoneQueriesInterface $timezoneQueries;
    private TimezoneService $timezoneService;
    
    public function test_success_handle_all_cities(): void
    {
        $this->command->method('argument')
                ->willReturn(null);
        
        $this->queries->expects($this->once())
                ->method('getObject')
                ->with(new SelectForAllCities())
                ->willReturn((object) [
                    // Суммарно 3 даты
                    'min_date' => '2_15:34:55 08.12.2023|2_14:12:06 09.12.2023',
                    'max_date' => '7_16:07:19 25.06.2024'
                ]);
        
        $this->queries->expects($this->exactly(3))
                ->method('getById')
                ->willReturn(new City());
        
        $this->assertNull($this->handler->handle($this->command));
    }
    
    public function test_success_handle_one_city(): void
    {
        $this->command->method('argument')
                ->willReturn('33');
        
        $this->queries->expects($this->once())
                ->method('getObject')
                ->with(new SelectForOneCity())
                ->willReturn((object) [
                    'id' => '7',
                    // Суммарно 3 даты
                    'min_temp_date' => '15:34:55 08.12.2023|14:12:06 09.12.2023',
                    'max_temp_date' => '17:47:32 28.06.2024'
                ]);
        
        $this->queries->expects($this->once())
                ->method('getById')
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
        
        $this->queries->expects($this->once())
                ->method('getByOpenWeatherId')
                ->willThrowException(new DatabaseException('Test Message'));
        
        $this->queries->expects($this->never())
                ->method('getObject');
        
        $this->assertNull($this->handler->handle($this->command));
    }
    
    protected function setUp(): void
    {
        $this->command = $this->createStub(WeatherStatisticsByCity::class);
        $this->queries = $this->createMock(CityQueriesInterface::class);
        $this->timezoneQueries = $this->createMock(TimezoneQueriesInterface::class);
        $this->timezoneService = new TimezoneService($this->timezoneQueries);
        
        $this->handler = new WeatherStatisticsByCityCommandHandler($this->queries, $this->timezoneService);
    }
}
