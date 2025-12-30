<?php

namespace Tests\Unit\Services\StorageDisk\Thesaurus;

use App\Models\Thesaurus\Timezone;
use App\Queries\Thesaurus\Timezones\TimezoneQueriesInterface;
use App\Services\StorageDisk\Thesaurus\CopyTimezonesService;
use App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus\TimezonesCopyistInterface;
use PHPUnit\Framework\TestCase;

class CopyTimezonesServiceTest extends TestCase
{
    private TimezoneQueriesInterface $queries;
    private CopyTimezonesService $service;
    private TimezonesCopyistInterface $copyist;
    
    public function test_success_copy(): void
    {
        $file = 'test.php';
        
        $this->copyist->expects($this->once())
                ->method('writeHeader');
        
        $this->queries->expects($this->once())
                ->method('getListInLazyById')
                ->with(fn (Timezone $tz) => $this->copyist->writeData($file, $tz));
        
        $this->copyist->expects($this->once())
                ->method('writeFooter');
        
        $this->assertNull($this->service->copy());
    }
    
    protected function setUp(): void
    {
        $this->queries = $this->createMock(TimezoneQueriesInterface::class);
        $this->copyist = $this->createMock(TimezonesCopyistInterface::class);
        
        $this->service = new CopyTimezonesService($this->queries, $this->copyist);
    }
}
