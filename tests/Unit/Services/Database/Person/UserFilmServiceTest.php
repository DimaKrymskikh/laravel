<?php

namespace Tests\Unit\Services\Database\Person;

use App\Events\AddFilmInUserList;
use App\Events\RemoveFilmFromUserList;
use App\Exceptions\DatabaseException;
use App\Models\Dvd\Film;
use App\Modifiers\Person\UsersFilms\UserFilmModifiersInterface;
use App\Queries\Dvd\Films\FilmQueriesInterface;
use App\Queries\Person\UsersFilms\UserFilmQueriesInterface;
use App\Services\Database\Person\Dto\UserFilmDto;
use App\Services\Database\Person\UserFilmService;
use Illuminate\Contracts\Events\Dispatcher;

class UserFilmServiceTest extends UserTestCase
{
    private UserFilmModifiersInterface $userFilmModifiers;
    private FilmQueriesInterface $filmQueries;
    private UserFilmQueriesInterface $userFilmQueries;
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
        
        $this->userFilmModifiers = $this->createMock(UserFilmModifiersInterface::class);
        $this->filmQueries = $this->createMock(FilmQueriesInterface::class);
        $this->userFilmQueries = $this->createMock(UserFilmQueriesInterface::class);
        $this->dispatcher = $this->createMock(Dispatcher::class);
        
        $this->userFilmService = new UserFilmService($this->userFilmModifiers, $this->filmQueries, $this->userFilmQueries, $this->dispatcher);
        
        $this->filmQueries->expects($this->once())
                ->method('getById')
                ->with($this->dto->filmId)
                ->willReturn($this->film);
    }
}
