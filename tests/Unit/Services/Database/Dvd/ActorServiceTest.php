<?php

namespace Tests\Unit\Services\Database\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\DataTransferObjects\Database\Dvd\ActorDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Exceptions\DatabaseException;
use App\Models\Dvd\Actor;
use App\Repositories\Dvd\ActorRepositoryInterface;
use App\Services\Database\Dvd\ActorService;
use App\ValueObjects\PersonName;
use App\ValueObjects\Pagination\PageValue;
use App\ValueObjects\Pagination\PerPageValue;
use PHPUnit\Framework\TestCase;

class ActorServiceTest extends TestCase
{
    private ActorRepositoryInterface $actorRepository;
    private ActorService $actorService;
    private int $actorId = 12;
    
    public function test_success_create(): void
    {
        $firstName = PersonName::create('Testfirstname', 'first_name', 'Имя актёра должно начинаться с заглавной буквы. Остальные буквы должны быть строчными.');
        $lastName = PersonName::create('Testlastname', 'last_name', 'Фамилия актёра должна начинаться с заглавной буквы. Остальные буквы должны быть строчными.');
        $actorDto = new ActorDto($firstName, $lastName);
        
        $this->actorRepository->expects($this->once())
                ->method('save');
        
        $this->actorService->create($actorDto);
    }
    
    public function test_success_update(): void
    {
        $firstName = PersonName::create('Testfirstname', 'first_name', 'Имя актёра должно начинаться с заглавной буквы. Остальные буквы должны быть строчными.');
        $lastName = PersonName::create('Testlastname', 'last_name', 'Фамилия актёра должна начинаться с заглавной буквы. Остальные буквы должны быть строчными.');
        $actorDto = new ActorDto($firstName, $lastName);
        $actor = new Actor();
        
        $this->actorRepository->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($this->actorId))
                ->willReturn($actor);
        
        $this->actorRepository->expects($this->once())
                ->method('save')
                ->with($actor, $actorDto);
        
        $this->actorService->update($actorDto, $this->actorId);
    }
    
    public function test_success_delete(): void
    {
        $this->actorRepository->expects($this->once())
                ->method('exists')
                ->with($this->actorId)
                ->willReturn(true);
        
        $this->actorRepository->expects($this->once())
                ->method('delete')
                ->with($this->identicalTo($this->actorId));
        
        $this->actorService->delete($this->actorId);
    }
    
    public function test_fail_delete(): void
    {
        $this->actorRepository->expects($this->once())
                ->method('exists')
                ->with($this->actorId)
                ->willReturn(false);
        
        $this->actorRepository->expects($this->never())
                ->method('delete')
                ->with($this->identicalTo($this->actorId));
        
        $this->expectException(DatabaseException::class);
        
        $this->actorService->delete($this->actorId);
    }
    
    public function test_success_getAllActorsList(): void
    {
        $actorFilterDto = new ActorFilterDto('test');
        
        $this->actorRepository->expects($this->once())
                ->method('getList')
                ->with($actorFilterDto);
        
        $this->actorService->getAllActorsList($actorFilterDto);
    }
    
    public function test_success_getActorsListForPage(): void
    {
        $actorFilterDto = new ActorFilterDto('test');
        $paginatorDto = new PaginatorDto(PageValue::create(3), PerPageValue::create(20));
        
        $this->actorRepository->expects($this->once())
                ->method('getListForPage')
                ->with($paginatorDto, $actorFilterDto);
        
        $this->actorService->getActorsListForPage($paginatorDto, $actorFilterDto);
    }
    
    protected function setUp(): void
    {
        $this->actorRepository = $this->createMock(ActorRepositoryInterface::class);
        
        $this->actorService = new ActorService($this->actorRepository);
    }
}
