<?php

namespace App\Events;

use App\Services\Database\Dvd\FilmService;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddFilmInUserList implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
            private int $userId,
            // Используется в App\Listeners\SendEmailAddFilmNotification
            readonly public int $filmId,
            private FilmService $filmService,
    ) {
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
        $filmTitle = $this->filmService->getFilmById($this->filmId)->title;
        
        return ['message' => "Вы добавили в свою коллекцию фильм $filmTitle"];
    }
}
