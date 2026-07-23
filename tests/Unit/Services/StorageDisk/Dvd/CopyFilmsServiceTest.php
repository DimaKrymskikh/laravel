<?php

namespace Tests\Unit\Services\StorageDisk\Dvd;

use App\Models\Dvd\Film;
use App\Queries\Dvd\FilmQueries;
use App\Services\StorageDisk\Dvd\CopyFilmsService;
use App\StorageDisk\CopyingDatabaseDataToFile\Dvd\FilmsCopyist;
use Tests\Unit\Services\StorageDisk\StorageDiskTestCase;

class CopyFilmsServiceTest extends StorageDiskTestCase
{
    protected FilmQueries $queries;
    protected CopyFilmsService $service;
    protected FilmsCopyist $copyist;
    
    public function test_success_copy(): void
    {
        $file = 'test.php';
        
        $this->copyist->expects($this->once())
                ->method('writeHeader');
        
        $this->queries->expects($this->once())
                ->method('getListInLazyById')
                ->with(fn (Film $film) => $this->copyist->writeData($file, $film));
        
        $this->copyist->expects($this->once())
                ->method('writeFooter');
        
        $this->assertNull($this->service->copy());
    }
    
    protected function setUp(): void
    {
        $this->queries = $this->createMock(FilmQueries::class);
        $this->copyist = $this->createMock(FilmsCopyist::class);
        
        parent::setUp();
        
        $this->service = new CopyFilmsService($this->serviceManager);
    }
}
