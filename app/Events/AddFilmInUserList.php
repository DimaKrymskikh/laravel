<?php

namespace App\Events;

use App\Services\Database\Dvd\FilmService;
use App\Services\Database\Person\Dto\UserFilmDto;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddFilmInUserList implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    private int $userId;
    private string $filmTitle;

    /**
     * Create a new event instance.
     */
    public function __construct(UserFilmDto $dto, FilmService $filmService)
    {
        $this->userId = $dto->userId;
        $this->filmTitle = $filmService->getFilmById($dto->filmId)->title;
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
