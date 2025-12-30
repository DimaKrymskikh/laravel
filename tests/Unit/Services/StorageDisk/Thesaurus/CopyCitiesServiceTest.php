<?php

namespace Tests\Unit\Services\StorageDisk\Thesaurus;

use App\Models\Thesaurus\City;
use App\Queries\Thesaurus\Cities\CityQueriesInterface;
use App\Services\StorageDisk\Thesaurus\CopyCitiesService;
use App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus\CitiesCopyistInterface;
use PHPUnit\Framework\TestCase;

class CopyCitiesServiceTest extends TestCase
{
    private CityQueriesInterface $queries;
    private CopyCitiesService $service;
    private CitiesCopyistInterface $copyist;
    
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
        $this->queries = $this->createMock(CityQueriesInterface::class);
        $this->copyist = $this->createMock(CitiesCopyistInterface::class);
        
        $this->service = new CopyCitiesService($this->queries, $this->copyist);
    }
}
