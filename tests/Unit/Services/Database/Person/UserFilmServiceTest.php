<?php

namespace Tests\Unit\Services\Database\Person;

use App\Exceptions\DatabaseException;
use App\Modifiers\Person\UsersFilms\UserFilmModifiersInterface;
use App\Queries\Dvd\Films\FilmQueriesInterface;
use App\Queries\Person\UsersFilms\UserFilmQueriesInterface;
use App\Services\Database\Person\Dto\UserFilmDto;
use App\Services\Database\Person\UserFilmService;
use PHPUnit\Framework\TestCase;

class UserFilmServiceTest extends TestCase
{
    private UserFilmModifiersInterface $userFilmModifiers;
    private FilmQueriesInterface $filmQueries;
    private UserFilmQueriesInterface $userFilmQueries;
    private UserFilmService $userFilmService;
    private UserFilmDto $dto;
    private int $userId = 3;
    private int $filmId = 12;

    public function test_success_create(): void
    {
        $this->userFilmQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->dto))
                ->willReturn(false);
        
        $this->filmQueries->expects($this->never())
                ->method('getById');
        
        $this->userFilmModifiers->expects($this->once())
                ->method('save')
                ->with($this->identicalTo($this->dto));
        
        $this->userFilmService->create($this->dto);
    }

    public function test_fail_create(): void
    {
        $this->userFilmQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->dto))
                ->willReturn(true);
        
        $this->filmQueries->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($this->filmId));
        
        $this->userFilmModifiers->expects($this->never())
                ->method('save');
        
        $this->expectException(DatabaseException::class);
        
        $this->userFilmService->create($this->dto);
    }

    public function test_success_delete(): void
    {
        $this->userFilmQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->dto))
                ->willReturn(true);
        
        $this->filmQueries->expects($this->never())
                ->method('getById');
        
        $this->userFilmModifiers->expects($this->once())
                ->method('remove')
                ->with($this->identicalTo($this->dto));
        
        $this->userFilmService->delete($this->dto);
    }

    public function test_fail_delete(): void
    {
        $this->userFilmQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->dto))
                ->willReturn(false);
        
        $this->filmQueries->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($this->filmId));
        
        $this->userFilmModifiers->expects($this->never())
                ->method('remove');
        
        $this->expectException(DatabaseException::class);
        
        $this->userFilmService->delete($this->dto);
    }
    
    protected function setUp(): void
    {
        $this->dto = new UserFilmDto($this->userId, $this->filmId);
        $this->userFilmModifiers = $this->createMock(UserFilmModifiersInterface::class);
        $this->filmQueries = $this->createMock(FilmQueriesInterface::class);
        $this->userFilmQueries = $this->createMock(UserFilmQueriesInterface::class);
        
        $this->userFilmService = new UserFilmService($this->userFilmModifiers, $this->filmQueries, $this->userFilmQueries);
    }
}
