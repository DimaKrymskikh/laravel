<?php

namespace Tests\Unit\Events;

use App\Events\RemoveFilmFromUserList;
use PHPUnit\Framework\TestCase;

class RemoveFilmFromUserListTest extends TestCase
{
    private RemoveFilmFromUserList $removeFilmFromUserList;
    private int $userId = 2;
    private string $filmTitle = 'TestFilmTitle';
    
    public function test_film_can_be_remove_from_user_list(): void
    {
        $broadcastWith = $this->removeFilmFromUserList->broadcastWith();
        // Проверка отправляемого сообщения
        $this->assertEquals($broadcastWith['message'], "Вы удалили из своей коллекции фильм $this->filmTitle");
        
        // Проверка имени канала
        $channelName = $this->removeFilmFromUserList->broadcastOn()[0]->name;
        $this->assertEquals($channelName, "private-auth.$this->userId");
        
        $this->assertSame($this->filmTitle, $this->removeFilmFromUserList->getFilmTitle());
    }
    
    protected function setUp(): void
    {
        $this->removeFilmFromUserList = new RemoveFilmFromUserList($this->userId, $this->filmTitle);
    }
}
