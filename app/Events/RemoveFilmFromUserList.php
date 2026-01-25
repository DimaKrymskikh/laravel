<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RemoveFilmFromUserList implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Создаёт событие после удаления фильма из коллекции пользователя.
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
        return ['message' => "Вы удалили из своей коллекции фильм $this->filmTitle"];
    }
}
