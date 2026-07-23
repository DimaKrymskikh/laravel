<?php

namespace Tests\Unit\Services\Database\Person;

use App\Events\AddFilmInUserList;
use App\Events\RemoveFilmFromUserList;
use App\Exceptions\DatabaseException;
use App\Models\Dvd\Film;
use App\Modifiers\Person\UserFilmModifiers;
use App\Queries\Dvd\FilmQueries;
use App\Queries\Person\UserFilmQueries;
use App\Services\Database\Person\Dto\UserFilmDto;
use App\Services\Database\Person\UserFilmService;
use App\Services\ServiceManagerInterface;
use Illuminate\Contracts\Events\Dispatcher;

class UserFilmServiceTest extends UserTestCase
{
    private ServiceManagerInterface $serviceManager;
    private UserFilmModifiers $userFilmModifiers;
    private FilmQueries $filmQueries;
    private UserFilmQueries $userFilmQueries;
    private UserFilmService $userFilmService;
    private Dispatcher $dispatcher;
    private UserFilmDto $dto;
    private Film $film;

    public function test_success_create(): void
    {
        $this->userFilmQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->dto))
                ->willReturn(false);
        
        $this->userFilmModifiers->expects($this->once())
                ->method('save')
                ->with($this->identicalTo($this->dto));
        
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with(new AddFilmInUserList($this->dto->userId, $this->film->title));
        
        $this->userFilmService->create($this->dto);
    }

    public function test_fail_create(): void
    {
        $this->userFilmQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->dto))
                ->willReturn(true);
        
        $this->userFilmModifiers->expects($this->never())
                ->method('save');
        
        $this->dispatcher->expects($this->never())
                ->method('dispatch');
        
        $this->expectException(DatabaseException::class);
        
        $this->userFilmService->create($this->dto);
    }

    public function test_success_delete(): void
    {
        $this->userFilmQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->dto))
                ->willReturn(true);
        
        $this->userFilmModifiers->expects($this->once())
                ->method('remove')
                ->with($this->identicalTo($this->dto));
        
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with(new RemoveFilmFromUserList($this->dto->userId, $this->film->title));
        
        $this->userFilmService->delete($this->dto);
    }

    public function test_fail_delete(): void
    {
        $this->userFilmQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->dto))
                ->willReturn(false);
        
        $this->userFilmModifiers->expects($this->never())
                ->method('remove');
        
        $this->dispatcher->expects($this->never())
                ->method('dispatch');
        
        $this->expectException(DatabaseException::class);
        
        $this->userFilmService->delete($this->dto);
    }
    
    protected function setUp(): void
    {
        $this->dto = $this->getUserFilmDto();
        $this->film = $this->factoryFilm();
        
        $this->userFilmModifiers = $this->createMock(UserFilmModifiers::class);
        $this->filmQueries = $this->createMock(FilmQueries::class);
        $this->userFilmQueries = $this->createMock(UserFilmQueries::class);
        $this->dispatcher = $this->createMock(Dispatcher::class);
        
        $this->serviceManager = $this->createStub(ServiceManagerInterface::class);
        $this->serviceManager->method('getQueriesOrModifiers')
                ->willReturn($this->userFilmModifiers, $this->filmQueries, $this->userFilmQueries);
        
        $this->userFilmService = new UserFilmService($this->serviceManager, $this->dispatcher);
        
        $this->filmQueries->expects($this->once())
                ->method('getById')
                ->with($this->dto->filmId)
                ->willReturn($this->film);
    }
}
