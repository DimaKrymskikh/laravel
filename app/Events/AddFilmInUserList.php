<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddFilmInUserList implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Создаёт событие после добавления фильма в коллекцию пользователя.
     * 
     * @param int $userId
     * @param string $filmTitle
     */
    public function __construct(
            private int $userId,
            private string $filmTitle,
    ) {
    }
    
    public function getFilmTitle(): string
    {
        return $this->filmTitle;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("auth.{$this->userId}"),
        ];
    }
    
    public function broadcastWith(): array
    {
        return ['message' => "Вы добавили в свою коллекцию фильм $this->filmTitle"];
    }
}
