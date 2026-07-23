<?php

namespace Tests\Unit\Services\StorageDisk\Dvd;

use App\Models\Dvd\FilmActor;
use App\Queries\Dvd\FilmActorQueries;
use App\Services\StorageDisk\Dvd\CopyFilmsActorsService;
use App\StorageDisk\CopyingDatabaseDataToFile\Dvd\FilmsActorsCopyist;
use Tests\Unit\Services\StorageDisk\StorageDiskTestCase;

class CopyFilmsActorsServiceTest extends StorageDiskTestCase
{
    protected FilmActorQueries $queries;
    protected CopyFilmsActorsService $service;
    protected FilmsActorsCopyist $copyist;
    
    public function test_success_copy(): void
    {
        $file = 'test.php';
        
        $this->copyist->expects($this->once())
                ->method('writeHeader');
        
        $this->queries->expects($this->once())
                ->method('getListInLazy')
                ->with(fn (FilmActor $filmActor) => $this->copyist->writeData($file, $filmActor));
        
        $this->copyist->expects($this->once())
                ->method('writeFooter');
        
        $this->assertNull($this->service->copy());
    }
    
    protected function setUp(): void
    {
        $this->queries = $this->createMock(FilmActorQueries::class);
        $this->copyist = $this->createMock(FilmsActorsCopyist::class);
        
        parent::setUp();
        
        $this->service = new CopyFilmsActorsService($this->serviceManager);
    }
}
