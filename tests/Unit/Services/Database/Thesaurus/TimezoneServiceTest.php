<?php

namespace Tests\Unit\Services\Database\Thesaurus;

use App\Queries\Thesaurus\TimezoneQueries;
use App\Services\Database\Thesaurus\TimezoneService;
use App\Services\ServiceManagerInterface;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;

class TimezoneServiceTest extends TestCase
{
    private ServiceManagerInterface $serviceManager;
    private TimezoneQueries $timezoneQueries;
    private TimezoneService $timezoneService;
    private string $filterName = 'TestFilter';
    
    public function test_success_get_all_timezones_list(): void
    {
        $this->timezoneQueries->expects($this->once())
                ->method('getList')
                ->with($this->filterName);
        
        $this->assertInstanceOf(Collection::class, $this->timezoneService->getAllTimezonesList($this->filterName));
    }
    
    protected function setUp(): void
    {
        $this->timezoneQueries = $this->createMock(TimezoneQueries::class);
        
        $this->serviceManager = $this->createStub(ServiceManagerInterface::class);
        $this->serviceManager->method('getQueriesOrModifiers')
                ->willReturn($this->timezoneQueries);
        
        $this->timezoneService = new TimezoneService($this->serviceManager);
    }
}
