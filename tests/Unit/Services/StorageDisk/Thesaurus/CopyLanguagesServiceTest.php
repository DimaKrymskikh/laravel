<?php

namespace Tests\Unit\Services\StorageDisk\Thesaurus;

use App\Models\Thesaurus\Language;
use App\Queries\Thesaurus\LanguageQueries;
use App\Services\StorageDisk\Thesaurus\CopyLanguagesService;
use App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus\LanguagesCopyist;
use Tests\Unit\Services\StorageDisk\StorageDiskTestCase;

class CopyLanguagesServiceTest extends StorageDiskTestCase
{
    protected LanguageQueries $queries;
    protected CopyLanguagesService $service;
    protected LanguagesCopyist $copyist;
    
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
        $this->queries = $this->createMock(LanguageQueries::class);
        $this->copyist = $this->createMock(LanguagesCopyist::class);
        
        parent::setUp();
        
        $this->service = new CopyLanguagesService($this->serviceManager);
    }
}
