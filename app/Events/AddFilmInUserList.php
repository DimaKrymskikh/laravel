<?php

namespace App\Events;

use App\Models\Dvd\Film;
use App\Models\Person\UserFilm;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddFilmInUserList implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public string $message;

    /**
     * Create a new event instance.
     */
    public function __construct(
            public UserFilm $userFilm
    )
    {
        $titleFilm = Film::find($userFilm->film_id)->title;
        $this->message = "Вы добавили в свою коллекцию фильм $titleFilm";
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("auth.{$this->userFilm->user_id}"),
        ];
    }
    
    public function broadcastWith(): array
    {
        return ['message' => $this->message];
    }
}
