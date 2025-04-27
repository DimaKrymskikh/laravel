<?php

namespace Tests\Unit\Events;

use App\Events\AddFilmInUserList;
use App\Models\Dvd\Film;
use App\Modifiers\Dvd\Films\FilmModifiersInterface;
use App\Queries\Dvd\Films\FilmQueriesInterface;
use App\Services\Database\Dvd\FilmService;
use PHPUnit\Framework\TestCase;

class AddFilmInUserListTest extends TestCase
{
    private AddFilmInUserList $addFilmInUserList;
    private FilmModifiersInterface $filmModifiers;
    private FilmService $filmService;
    private FilmQueriesInterface $filmQueries;
    private int $userId = 2;
    private int $filmId = 17;
    
    public function test_film_can_be_add_in_user_list(): void
    {
        $film = new Film();
        $film->title = 'TestTitle';
        
        $this->filmQueries->expects($this->once())
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
        $this->filmQueries = $this->createMock(FilmQueriesInterface::class);
        $this->filmModifiers = $this->createMock(FilmModifiersInterface::class);
        
        $this->filmService = new FilmService($this->filmQueries, $this->filmModifiers);
    }
}
