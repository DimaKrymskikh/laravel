<?php

namespace Tests\Unit\Services\StorageDisk\Thesaurus;

use App\Models\Thesaurus\City;
use App\Queries\Thesaurus\CityQueries;
use App\Services\StorageDisk\Thesaurus\CopyCitiesService;
use App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus\CitiesCopyist;
use Tests\Unit\Services\StorageDisk\StorageDiskTestCase;

class CopyCitiesServiceTest extends StorageDiskTestCase
{
    protected CityQueries $queries;
    protected CopyCitiesService $service;
    protected CitiesCopyist $copyist;
    
    public function test_success_copy(): void
    {
        $file = 'test.php';
        
        $this->copyist->expects($this->once())
                ->method('writeHeader');
        
        $this->queries->expects($this->once())
                ->method('getListInLazyById')
                ->with(fn (City $city) => $this->copyist->writeData($file, $city));
        
        $this->copyist->expects($this->once())
                ->method('writeFooter');
        
        $this->assertNull($this->service->copy());
    }
    
    protected function setUp(): void
    {
        $this->queries = $this->createMock(CityQueries::class);
        $this->copyist = $this->createMock(CitiesCopyist::class);
        
        parent::setUp();
        
        $this->service = new CopyCitiesService($this->serviceManager);
    }
}
