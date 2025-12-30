<?php

namespace Tests\Unit\Services\StorageDisk\Dvd;

use App\Models\Dvd\Actor;
use App\Queries\Dvd\Actors\ActorQueriesInterface;
use App\Services\StorageDisk\Dvd\CopyActorsService;
use App\StorageDisk\CopyingDatabaseDataToFile\Dvd\ActorsCopyistInterface;
use PHPUnit\Framework\TestCase;

class CopyActorsServiceTest extends TestCase
{
    private ActorQueriesInterface $queries;
    private CopyActorsService $service;
    private ActorsCopyistInterface $copyist;
    
    public function test_success_copy(): void
    {
        $file = 'test.php';
        
        $this->copyist->expects($this->once())
                ->method('writeHeader');
        
        $this->queries->expects($this->once())
                ->method('getListInLazyById')
                ->with(fn (Actor $actor) => $this->copyist->writeData($file, $actor));
        
        $this->copyist->expects($this->once())
                ->method('writeFooter');
        
        $this->assertNull($this->service->copy());
    }
    
    protected function setUp(): void
    {
        $this->queries = $this->createMock(ActorQueriesInterface::class);
        $this->copyist = $this->createMock(ActorsCopyistInterface::class);
        
        $this->service = new CopyActorsService($this->queries, $this->copyist);
    }
}
