<?php

namespace Tests\Unit\Services\Database\Person;

use App\Exceptions\DatabaseException;
use App\Models\Person\UserFilm;
use App\Repositories\Dvd\FilmRepositoryInterface;
use App\Repositories\Person\UserFilmRepositoryInterface;
use App\Services\Database\Person\UserFilmService;
use PHPUnit\Framework\TestCase;

class UserFilmServiceTest extends TestCase
{
    private FilmRepositoryInterface $filmRepository;
    private UserFilmRepositoryInterface $userFilmRepository;
    private UserFilmService $userFilmService;
    private int $userId = 3;
    private int $filmId = 12;

    public function test_success_create(): void
    {
        $this->userFilmRepository->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->userId), $this->identicalTo($this->filmId))
                ->willReturn(false);
        
        $this->filmRepository->expects($this->never())
                ->method('getById');
        
        $this->userFilmRepository->expects($this->once())
                ->method('save')
                ->with(new UserFilm(), $this->identicalTo($this->userId), $this->identicalTo($this->filmId));
        
        $this->userFilmService->create($this->userId, $this->filmId);
    }

    public function test_fail_create(): void
    {
        $this->userFilmRepository->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->userId), $this->identicalTo($this->filmId))
                ->willReturn(true);
        
        $this->filmRepository->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($this->filmId));
        
        $this->userFilmRepository->expects($this->never())
                ->method('save');
        
        $this->expectException(DatabaseException::class);
        
        $this->userFilmService->create($this->userId, $this->filmId);
    }

    public function test_success_delete(): void
    {
        $this->userFilmRepository->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->userId), $this->identicalTo($this->filmId))
                ->willReturn(true);
        
        $this->filmRepository->expects($this->never())
                ->method('getById');
        
        $this->userFilmRepository->expects($this->once())
                ->method('delete')
                ->with($this->identicalTo($this->userId), $this->identicalTo($this->filmId));
        
        $this->userFilmService->delete($this->userId, $this->filmId);
    }

    public function test_fail_delete(): void
    {
        $this->userFilmRepository->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->userId), $this->identicalTo($this->filmId))
                ->willReturn(false);
        
        $this->filmRepository->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($this->filmId));
        
        $this->userFilmRepository->expects($this->never())
                ->method('delete');
        
        $this->expectException(DatabaseException::class);
        
        $this->userFilmService->delete($this->userId, $this->filmId);
    }
    
    protected function setUp(): void
    {
        $this->filmRepository = $this->createMock(FilmRepositoryInterface::class);
        $this->userFilmRepository = $this->createMock(UserFilmRepositoryInterface::class);
        
        $this->userFilmService = new UserFilmService($this->filmRepository, $this->userFilmRepository);
    }
}
