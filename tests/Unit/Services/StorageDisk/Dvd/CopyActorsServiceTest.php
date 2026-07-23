<?php

namespace Tests\Unit\Services\StorageDisk\Dvd;

use App\Models\Dvd\Actor;
use App\Queries\Dvd\ActorQueries;
use App\Services\StorageDisk\Dvd\CopyActorsService;
use App\StorageDisk\CopyingDatabaseDataToFile\Dvd\ActorsCopyist;
use Tests\Unit\Services\StorageDisk\StorageDiskTestCase;

class CopyActorsServiceTest extends StorageDiskTestCase
{
    protected ActorQueries $queries;
    protected CopyActorsService $service;
    protected ActorsCopyist $copyist;
    
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
        $this->queries = $this->createMock(ActorQueries::class);
        $this->copyist = $this->createMock(ActorsCopyist::class);
        
        parent::setUp();
        
        $this->service = new CopyActorsService($this->serviceManager);
    }
}
