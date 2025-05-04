<?php

namespace Tests\Unit\Services\Database\Person;

use App\Exceptions\DatabaseException;
use App\Models\Person\UserFilm;
use App\Modifiers\Person\UsersFilms\UserFilmModifiersInterface;
use App\Queries\Dvd\Films\FilmQueriesInterface;
use App\Queries\Person\UsersFilms\UserFilmQueriesInterface;
use App\Services\Database\Person\UserFilmService;
use PHPUnit\Framework\TestCase;

class UserFilmServiceTest extends TestCase
{
    private UserFilmModifiersInterface $userFilmModifiers;
    private FilmQueriesInterface $filmQueries;
    private UserFilmQueriesInterface $userFilmQueries;
    private UserFilmService $userFilmService;
    private int $userId = 3;
    private int $filmId = 12;

    public function test_success_create(): void
    {
        $this->userFilmQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->userId), $this->identicalTo($this->filmId))
                ->willReturn(false);
        
        $this->filmQueries->expects($this->never())
                ->method('getById');
        
        $this->userFilmModifiers->expects($this->once())
                ->method('save')
                ->with(new UserFilm(), $this->identicalTo($this->userId), $this->identicalTo($this->filmId));
        
        $this->userFilmService->create($this->userId, $this->filmId);
    }

    public function test_fail_create(): void
    {
        $this->userFilmQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->userId), $this->identicalTo($this->filmId))
                ->willReturn(true);
        
        $this->filmQueries->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($this->filmId));
        
        $this->userFilmModifiers->expects($this->never())
                ->method('save');
        
        $this->expectException(DatabaseException::class);
        
        $this->userFilmService->create($this->userId, $this->filmId);
    }

    public function test_success_delete(): void
    {
        $this->userFilmQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->userId), $this->identicalTo($this->filmId))
                ->willReturn(true);
        
        $this->filmQueries->expects($this->never())
                ->method('getById');
        
        $this->userFilmModifiers->expects($this->once())
                ->method('delete')
                ->with($this->identicalTo($this->userId), $this->identicalTo($this->filmId));
        
        $this->userFilmService->delete($this->userId, $this->filmId);
    }

    public function test_fail_delete(): void
    {
        $this->userFilmQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->userId), $this->identicalTo($this->filmId))
                ->willReturn(false);
        
        $this->filmQueries->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($this->filmId));
        
        $this->userFilmModifiers->expects($this->never())
                ->method('delete');
        
        $this->expectException(DatabaseException::class);
        
        $this->userFilmService->delete($this->userId, $this->filmId);
    }
    
    protected function setUp(): void
    {
        $this->userFilmModifiers = $this->createMock(UserFilmModifiersInterface::class);
        $this->filmQueries = $this->createMock(FilmQueriesInterface::class);
        $this->userFilmQueries = $this->createMock(UserFilmQueriesInterface::class);
        
        $this->userFilmService = new UserFilmService($this->userFilmModifiers, $this->filmQueries, $this->userFilmQueries);
    }
}
