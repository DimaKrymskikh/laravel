<?php

namespace Tests\Unit\Events;

use App\Events\AddFilmInUserList;
use App\Models\Dvd\Film;
use App\Modifiers\Dvd\Films\FilmModifiersInterface;
use App\Repositories\Dvd\FilmRepositoryInterface;
use App\Services\Database\Dvd\FilmService;
use PHPUnit\Framework\TestCase;

class AddFilmInUserListTest extends TestCase
{
    private AddFilmInUserList $addFilmInUserList;
    private FilmModifiersInterface $filmModifiers;
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
        
        $this->addFilmInUserList = new AddFilmInUserList($this->userId, $this->filmId, $this->filmService);
        
        $broadcastWith = $this->addFilmInUserList->broadcastWith();
        // Проверка отправляемого сообщения
        $this->assertEquals($broadcastWith['message'], "Вы добавили в свою коллекцию фильм $film->title");
        
        // Проверка имени канала
        $channelName = $this->addFilmInUserList->broadcastOn()[0]->name;
        $this->assertEquals($channelName, "private-auth.$this->userId");
        
        $this->assertSame($film->title, $this->addFilmInUserList->getFilmTitle());
    }
    
    protected function setUp(): void
    {
        $this->filmModifiers = $this->createMock(FilmModifiersInterface::class);
        $this->filmRepository = $this->createMock(FilmRepositoryInterface::class);
        
        $this->filmService = new FilmService($this->filmModifiers, $this->filmRepository);
    }
}
