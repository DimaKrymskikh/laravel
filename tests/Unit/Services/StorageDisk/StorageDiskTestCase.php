<?php

namespace Tests\Unit\Services\StorageDisk;

use App\Services\ServiceManagerInterface;
use PHPUnit\Framework\TestCase;

abstract class StorageDiskTestCase extends TestCase
{
    protected ServiceManagerInterface $serviceManager;
    
    protected function setUp(): void
    {
        $this->serviceManager = $this->createStub(ServiceManagerInterface::class);
        $this->serviceManager->method('getQueriesOrModifiers')->willReturn($this->queries);
        $this->serviceManager->method('getCopyist')->willReturn($this->copyist);
    }
}
