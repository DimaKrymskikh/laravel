<?php

namespace Tests\Unit\Services\Database\Thesaurus;

use App\Contracts\Services\ServiceManagerInterface;
use App\Exceptions\DatabaseException;
use App\Models\Thesaurus\Language;
use App\Modifiers\Thesaurus\LanguageModifiers;
use App\Queries\Thesaurus\LanguageQueries;
use App\Services\Database\Thesaurus\LanguageService;
use Illuminate\Database\Eloquent\Collection;
use Tests\Unit\TestCase\ThesaurusTestCase;

class LanguageServiceTest extends ThesaurusTestCase
{
    private ServiceManagerInterface $serviceManager;
    private LanguageModifiers $languageModifiers;
    private LanguageQueries $languageQueries;
    private LanguageService $languageService;
    private string $name = 'TestName';
    private int $languageId = 12;
    
    public function test_success_getLanguageById(): void
    {
        $this->languageQueries->method('find')
                ->willReturn(new Language());
        
        $this->assertInstanceOf(Language::class, $this->languageService->getLanguageById($this->languageId));
    }
    
    public function test_fail_getLanguageById(): void
    {
        $this->expectException(DatabaseException::class);
        
        $this->languageQueries->method('find')
                ->willReturn(null);
        
        $this->languageService->getLanguageById($this->languageId);
    }
    
    public function test_success_create(): void
    {
        $this->assertInstanceOf(Language::class, $this->languageService->create($this->name));
    }
    
    public function test_success_update(): void
    {
        $this->languageQueries->method('find')
                ->willReturn(new Language());
        
        $this->assertInstanceOf(Language::class, $this->languageService->update($this->name, $this->languageId));
    }
    
    public function test_success_delete(): void
    {
        $this->languageQueries->method('find')
                ->willReturn(new Language());
        
        $this->assertNull($this->languageService->delete($this->languageId));
    }
    
    public function test_success_get_all_languages_list(): void
    {
        $dto = $this->getLanguageFilterDto();
        
        $this->assertInstanceOf(Collection::class, $this->languageService->getAllLanguagesList($dto));
    }
    
    protected function setUp(): void
    {
        $this->languageModifiers = $this->createStub(LanguageModifiers::class);
        $this->languageQueries = $this->createStub(LanguageQueries::class);
        
        $this->serviceManager = $this->createStub(ServiceManagerInterface::class);
        $this->serviceManager->method('getQueriesOrModifiers')
                ->willReturn($this->languageModifiers, $this->languageQueries);
        
        $this->languageService = new LanguageService($this->serviceManager);
    }
}
