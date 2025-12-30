<?php

namespace Tests\Unit\Services\StorageDisk\Dvd;

use App\Models\Dvd\Film;
use App\Queries\Dvd\Films\FilmQueriesInterface;
use App\Services\StorageDisk\Dvd\CopyFilmsService;
use App\StorageDisk\CopyingDatabaseDataToFile\Dvd\FilmsCopyistInterface;
use PHPUnit\Framework\TestCase;

class CopyFilmsServiceTest extends TestCase
{
    private FilmQueriesInterface $queries;
    private CopyFilmsService $service;
    private FilmsCopyistInterface $copyist;
    
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
        $this->queries = $this->createMock(FilmQueriesInterface::class);
        $this->copyist = $this->createMock(FilmsCopyistInterface::class);
        
        $this->service = new CopyFilmsService($this->queries, $this->copyist);
    }
}
