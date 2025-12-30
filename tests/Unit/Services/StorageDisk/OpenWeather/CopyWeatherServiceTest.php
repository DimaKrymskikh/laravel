<?php

namespace Tests\Unit\Services\StorageDisk\OpenWeather;

use App\Queries\OpenWeather\OpenWeatherQueriesInterface;
use App\Services\StorageDisk\OpenWeather\CopyWeatherService;
use App\StorageDisk\CopyingDatabaseDataToFile\OpenWeather\WeatherCopyistInterface;
use PHPUnit\Framework\TestCase;

class CopyWeatherServiceTest extends TestCase
{
    private OpenWeatherQueriesInterface $queries;
    private CopyWeatherService $service;
    private WeatherCopyistInterface $copyist;
    
    public function test_success_copy(): void
    {
        $file = 'test.php';
        
        $this->copyist->expects($this->once())
                ->method('writeHeader');
        
        $this->queries->expects($this->once())
                ->method('getListInLazyById')
                ->with(fn (Weather $weather) => $this->copyist->writeData($file, $weather));
        
        $this->copyist->expects($this->once())
                ->method('writeFooter');
        
        $this->assertNull($this->service->copy());
    }
    
    protected function setUp(): void
    {
        $this->queries = $this->createMock(OpenWeatherQueriesInterface::class);
        $this->copyist = $this->createMock(WeatherCopyistInterface::class);
        
        $this->service = new CopyWeatherService($this->queries, $this->copyist);
    }
}
