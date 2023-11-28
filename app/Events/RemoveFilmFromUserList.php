<?php

namespace App\Events;

use App\Models\ModelsFields;
use App\Models\Person\UserFilm;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RemoveFilmFromUserList implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels, ModelsFields;
    
    public string $message;

    /**
     * Create a new event instance.
     */
    public function __construct(
            public UserFilm $userFilm
    )
    {
        $this->message = 'Вы удалили из своей коллекции фильм '.$this->getFilmTitle($userFilm->film_id);
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