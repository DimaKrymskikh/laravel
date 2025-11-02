<?php

namespace Tests\Unit\Services\Database\Thesaurus;

use App\Exceptions\DatabaseException;
use App\Models\Thesaurus\Language;
use App\Modifiers\Thesaurus\Languages\LanguageModifiersInterface;
use App\Queries\Thesaurus\Languages\LanguageQueriesInterface;
use App\Services\Database\Thesaurus\LanguageService;
use Illuminate\Database\Eloquent\Collection;
use Tests\Unit\TestCase\ThesaurusTestCase;

class LanguageServiceTest extends ThesaurusTestCase
{
    private LanguageModifiersInterface $languageModifiers;
    private LanguageQueriesInterface $languageQueries;
    private LanguageService $languageService;
    private string $name = 'TestName';
    private int $languageId = 12;
    
    public function test_success_create(): void
    {
        $this->languageModifiers->expects($this->once())
                ->method('save')
                ->with(new Language(), $this->name);
        
        $this->assertNull($this->languageService->create($this->name));
    }
    
    public function test_success_update(): void
    {
        $language = new Language();
        
        $this->languageQueries->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($this->languageId))
                ->willReturn($language);
        
        $this->languageModifiers->expects($this->once())
                ->method('save')
                ->with($this->identicalTo($language), $this->name);
        
        $this->assertNull($this->languageService->update($this->name, $this->languageId));
    }
    
    public function test_success_delete(): void
    {
        $this->languageQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->languageId))
                ->willReturn(true);
        
        $this->languageModifiers->expects($this->once())
                ->method('delete')
                ->with($this->identicalTo($this->languageId));
        
        $this->assertNull($this->languageService->delete($this->languageId));
    }
    
    public function test_fail_delete(): void
    {
        $this->languageQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->languageId))
                ->willReturn(false);
        
        $this->languageModifiers->expects($this->never())
                ->method('delete')
                ->with($this->identicalTo($this->languageId));
        
        $this->expectException(DatabaseException::class);
        
        $this->assertNull($this->languageService->delete($this->languageId));
    }
    
    public function test_success_get_all_languages_list(): void
    {
        $dto = $this->getLanguageFilterDto();
        
        $this->languageQueries->expects($this->once())
                ->method('getListWithFilter')
                ->with($this->identicalTo($dto));
        
        $this->assertInstanceOf(Collection::class, $this->languageService->getAllLanguagesList($dto));
    }
    
    protected function setUp(): void
    {
        $this->languageModifiers = $this->createMock(LanguageModifiersInterface::class);
        $this->languageQueries = $this->createMock(LanguageQueriesInterface::class);
        
        $this->languageService = new LanguageService($this->languageModifiers, $this->languageQueries);
    }
}
