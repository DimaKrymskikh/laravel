<?php

namespace Tests\Unit\Services\Database\Thesaurus;

use App\Exceptions\DatabaseException;
use App\Models\Thesaurus\Language;
use App\Queries\Thesaurus\Languages\LanguageQueriesInterface;
use App\Repositories\Thesaurus\LanguageRepositoryInterface;
use App\Services\Database\Thesaurus\LanguageService;
use Illuminate\Database\Eloquent\Collection;
use Tests\Support\Data\Dto\Database\Thesaurus\Filters\LanguageFilterDtoCase;
use PHPUnit\Framework\TestCase;

class LanguageServiceTest extends TestCase
{
    use LanguageFilterDtoCase;
    
    private LanguageQueriesInterface $languageQueries;
    private LanguageRepositoryInterface $languageRepository;
    private LanguageService $languageService;
    private string $name = 'TestName';
    private int $languageId = 12;
    
    public function test_success_create(): void
    {
        $this->languageRepository->expects($this->once())
                ->method('save')
                ->with(new Language(), $this->name);
        
        $this->assertNull($this->languageService->create($this->name));
    }
    
    public function test_success_update(): void
    {
        $language = new Language();
        
        $this->languageRepository->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($this->languageId))
                ->willReturn($language);
        
        $this->languageRepository->expects($this->once())
                ->method('save')
                ->with($this->identicalTo($language), $this->name);
        
        $this->assertNull($this->languageService->update($this->name, $this->languageId));
    }
    
    public function test_success_delete(): void
    {
        $this->languageRepository->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->languageId))
                ->willReturn(true);
        
        $this->languageRepository->expects($this->once())
                ->method('delete')
                ->with($this->identicalTo($this->languageId));
        
        $this->assertNull($this->languageService->delete($this->languageId));
    }
    
    public function test_fail_delete(): void
    {
        $this->languageRepository->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->languageId))
                ->willReturn(false);
        
        $this->languageRepository->expects($this->never())
                ->method('delete')
                ->with($this->identicalTo($this->languageId));
        
        $this->expectException(DatabaseException::class);
        
        $this->assertNull($this->languageService->delete($this->languageId));
    }
    
    public function test_success_get_all_languages_list(): void
    {
        $dto = $this->getBaseCaseLanguageFilterDto();
        
        $this->languageQueries->expects($this->once())
                ->method('getList')
                ->with($this->identicalTo($dto));
        
        $this->assertInstanceOf(Collection::class, $this->languageService->getAllLanguagesList($dto));
    }
    
    protected function setUp(): void
    {
        $this->languageQueries = $this->createMock(LanguageQueriesInterface::class);
        $this->languageRepository = $this->createMock(LanguageRepositoryInterface::class);
        
        $this->languageService = new LanguageService($this->languageQueries, $this->languageRepository);
    }
}
