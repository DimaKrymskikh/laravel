<?php

namespace Tests\Unit\Events;

use App\Events\AddFilmInUserList;
use PHPUnit\Framework\TestCase;

class AddFilmInUserListTest extends TestCase
{
    private AddFilmInUserList $addFilmInUserList;
    private int $userId = 2;
    private string $filmTitle = 'TestFilmTitle';
    
    public function test_film_can_be_add_in_user_list(): void
    {
        $broadcastWith = $this->addFilmInUserList->broadcastWith();
        // Проверка отправляемого сообщения
        $this->assertEquals($broadcastWith['message'], "Вы добавили в свою коллекцию фильм $this->filmTitle");
        
        // Проверка имени канала
        $channelName = $this->addFilmInUserList->broadcastOn()[0]->name;
        $this->assertEquals($channelName, "private-auth.$this->userId");
        
        $this->assertSame($this->filmTitle, $this->addFilmInUserList->getFilmTitle());
    }
    
    protected function setUp(): void
    {
        $this->addFilmInUserList = new AddFilmInUserList($this->userId, $this->filmTitle);
    }
}
