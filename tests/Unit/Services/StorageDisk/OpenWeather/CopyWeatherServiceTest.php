<?php

namespace Tests\Unit\Services\StorageDisk\OpenWeather;

use App\Queries\OpenWeather\OpenWeatherQueries;
use App\Services\StorageDisk\OpenWeather\CopyWeatherService;
use App\StorageDisk\CopyingDatabaseDataToFile\OpenWeather\WeatherCopyist;
use Tests\Unit\Services\StorageDisk\StorageDiskTestCase;

class CopyWeatherServiceTest extends StorageDiskTestCase
{
    protected OpenWeatherQueries $queries;
    protected CopyWeatherService $service;
    protected WeatherCopyist $copyist;
    
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
        $this->queries = $this->createMock(OpenWeatherQueries::class);
        $this->copyist = $this->createMock(WeatherCopyist::class);
        
        parent::setUp();
        
        $this->service = new CopyWeatherService($this->serviceManager);
    }
}
