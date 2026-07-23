<?php

namespace Tests\Unit\Services\StorageDisk\Thesaurus;

use App\Models\Thesaurus\Timezone;
use App\Queries\Thesaurus\TimezoneQueries;
use App\Services\StorageDisk\Thesaurus\CopyTimezonesService;
use App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus\TimezonesCopyist;
use Tests\Unit\Services\StorageDisk\StorageDiskTestCase;

class CopyTimezonesServiceTest extends StorageDiskTestCase
{
    protected TimezoneQueries $queries;
    protected CopyTimezonesService $service;
    protected TimezonesCopyist $copyist;
    
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
        $this->queries = $this->createMock(TimezoneQueries::class);
        $this->copyist = $this->createMock(TimezonesCopyist::class);
        
        parent::setUp();
        
        $this->service = new CopyTimezonesService($this->serviceManager);
    }
}
