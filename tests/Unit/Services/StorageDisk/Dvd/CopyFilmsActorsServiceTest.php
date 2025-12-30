<?php

namespace Tests\Unit\Services\StorageDisk\Dvd;

use App\Models\Dvd\FilmActor;
use App\Queries\Dvd\FilmsActors\FilmActorQueriesInterface;
use App\Services\StorageDisk\Dvd\CopyFilmsActorsService;
use App\StorageDisk\CopyingDatabaseDataToFile\Dvd\FilmsActorsCopyistInterface;
use PHPUnit\Framework\TestCase;

class CopyFilmsActorsServiceTest extends TestCase
{
    private FilmActorQueriesInterface $queries;
    private CopyFilmsActorsService $service;
    private FilmsActorsCopyistInterface $copyist;
    
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
        $this->queries = $this->createMock(FilmActorQueriesInterface::class);
        $this->copyist = $this->createMock(FilmsActorsCopyistInterface::class);
        
        $this->service = new CopyFilmsActorsService($this->queries, $this->copyist);
    }
}
