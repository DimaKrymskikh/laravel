<?php

namespace Tests\Unit\Services\Database\Thesaurus;

use App\Models\Thesaurus\City;
use App\Models\Thesaurus\Timezone;
use App\Queries\Thesaurus\Timezones\TimezoneQueriesInterface;
use App\Services\Database\Thesaurus\TimezoneService;
use App\Services\CarbonService;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;

class TimezoneServiceTest extends TestCase
{
    private TimezoneQueriesInterface $timezoneQueries;
    private TimezoneService $timezoneService;
    private string $filterName = 'TestFilter';
    
    public function test_success_get_all_timezones_list(): void
    {
        $this->timezoneQueries->expects($this->once())
                ->method('getList')
                ->with($this->filterName);
        
        $this->assertInstanceOf(Collection::class, $this->timezoneService->getAllTimezonesList($this->filterName));
    }
    
    public function test_success_get_timezone_by_city(): void
    {
        $city = new City();
        $city->timezone_id = 11;
        $city->timezone = new Timezone();
        $city->timezone->name = 'TestTimezoneName';
        
        $this->assertSame('TestTimezoneName', $this->timezoneService->getTimezoneByCity($city));
    }
    
    public function test_default_get_timezone_by_city(): void
    {
        $city = new City();
        
        $this->assertSame(CarbonService::DEFAULT_TIMEZONE_NAME, $this->timezoneService->getTimezoneByCity($city));
    }
    
    protected function setUp(): void
    {
        $this->timezoneQueries = $this->createMock(TimezoneQueriesInterface::class);
        
        $this->timezoneService = new TimezoneService($this->timezoneQueries);
    }
}
