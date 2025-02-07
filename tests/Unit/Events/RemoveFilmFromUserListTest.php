<?php

namespace Tests\Unit\Events;

use App\Events\RemoveFilmFromUserList;
use App\Models\Dvd\Film;
use App\Repositories\Dvd\FilmRepositoryInterface;
use App\Services\Database\Dvd\FilmService;
use PHPUnit\Framework\TestCase;

class RemoveFilmFromUserListTest extends TestCase
{
    private RemoveFilmFromUserList $removeFilmFromUserList;
    private FilmRepositoryInterface $filmRepository;
    private FilmService $filmService;
    private int $userId = 2;
    private int $filmId = 17;
    
    public function test_film_can_be_add_in_user_list(): void
    {
        $film = new Film();
        $film->title = 'TestTitle';
        
        $this->filmRepository->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($this->filmId))
                ->willReturn($film);
        
        $broadcastWith = $this->removeFilmFromUserList->broadcastWith();
        // Проверка отправляемого сообщения
        $this->assertEquals($broadcastWith['message'], "Вы удалили из своей коллекции фильм $film->title");
        
        // Проверка имени канала
        $channelName = $this->removeFilmFromUserList->broadcastOn()[0]->name;
        $this->assertEquals($channelName, "private-auth.$this->userId");
    }
    
    protected function setUp(): void
    {
        $this->filmRepository = $this->createMock(FilmRepositoryInterface::class);
        $this->filmService = new FilmService($this->filmRepository);
        
        $this->removeFilmFromUserList = new RemoveFilmFromUserList($this->userId, $this->filmId, $this->filmService);
    }
}
