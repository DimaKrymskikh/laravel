<?php

namespace Tests\Unit\Services\StorageDisk\Thesaurus;

use App\Models\Thesaurus\Language;
use App\Queries\Thesaurus\Languages\LanguageQueriesInterface;
use App\Services\StorageDisk\Thesaurus\CopyLanguagesService;
use App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus\LanguagesCopyistInterface;
use PHPUnit\Framework\TestCase;

class CopyLanguagesServiceTest extends TestCase
{
    private LanguageQueriesInterface $queries;
    private CopyLanguagesService $service;
    private LanguagesCopyistInterface $copyist;
    
    public function test_success_copy(): void
    {
        $file = 'test.php';
        
        $this->copyist->expects($this->once())
                ->method('writeHeader');
        
        $this->queries->expects($this->once())
                ->method('getListInLazyById')
                ->with(fn (Language $language) => $this->copyist->writeData($file, $language));
        
        $this->copyist->expects($this->once())
                ->method('writeFooter');
        
        $this->assertNull($this->service->copy());
    }
    
    protected function setUp(): void
    {
        $this->queries = $this->createMock(LanguageQueriesInterface::class);
        $this->copyist = $this->createMock(LanguagesCopyistInterface::class);
        
        $this->service = new CopyLanguagesService($this->queries, $this->copyist);
    }
}
